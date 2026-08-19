<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorito;
use App\Models\Producto;

class FavoritoController extends Controller
{
    /**
     * Muestra la lista de productos favoritos del cliente autenticado.
     */
    public function index()
    {
        $favoritos = Auth::user()->productosFavoritos()
            ->where('disponibilidad', 1)
            ->with(['imagenes', 'categoria', 'ofertaActiva'])
            ->latest('favoritos.created_at')
            ->paginate(10);

        $totalFavoritos = Auth::user()->favoritos()->count();

        return view('cliente.favorito.Favoritos', compact('favoritos', 'totalFavoritos'));
    }

    /**
     * Alterna un producto como favorito (Agregar o Quitar).
     */
    public function toggle(Request $request, $producto_id)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Debes iniciar sesión para guardar favoritos.',
            ], 401);
        }

        $userId = Auth::id();
        $producto = Producto::findOrFail($producto_id);

        $favorito = Favorito::where('user_id', $userId)
            ->where('producto_id', $producto_id)
            ->first();

        if ($favorito) {
            $favorito->delete();
            return response()->json([
                'status' => 'removed',
                'is_favorite' => false,
                'message' => 'Producto eliminado de tus favoritos.',
                'total' => Favorito::where('user_id', $userId)->count(),
            ]);
        } else {
            Favorito::create([
                'user_id' => $userId,
                'producto_id' => $producto_id,
            ]);
            return response()->json([
                'status' => 'added',
                'is_favorite' => true,
                'message' => '¡Producto agregado a tus favoritos!',
                'total' => Favorito::where('user_id', $userId)->count(),
            ]);
        }
    }

    /**
     * Elimina un producto de favoritos.
     */
    public function destroy($producto_id)
    {
        Favorito::where('user_id', Auth::id())
            ->where('producto_id', $producto_id)
            ->delete();

        return back()->with('success', 'Producto eliminado de tus favoritos.');
    }
}
