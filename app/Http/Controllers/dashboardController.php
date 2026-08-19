<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $role = auth()->user()->role_id;

        if ($role == 1) {
            // Métricas fila 1
            $totalProductos = Producto::count();
            $productosActivos = Producto::where('disponibilidad', 1)->count();
            $productosInactivos = Producto::where('disponibilidad', 0)->count();

            $valorInventario = Producto::selectRaw('SUM(stock * precio) as total')->value('total') ?? 0;
            $productosStockBajo = Producto::where('stock', '<=', 5)->count();

            $totalVentas = Pedido::where('estado_pedido', '!=', 'Cancelado')->sum('total_pedido');
            $totalVentasCount = Pedido::where('estado_pedido', '!=', 'Cancelado')->count();

            $totalPedidos = Pedido::count();
            $pedidosPendientes = Pedido::whereIn('estado_pedido', ['En preparación', 'Pendiente'])->count();

            // Usuarios registrados con búsqueda
            $query = User::with('role');
            if ($request->filled('buscar_usuario')) {
                $term = trim($request->buscar_usuario);
                $query->where(function($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%")
                      ->orWhere('id', 'like', "%{$term}%");
                });
            }
            $usuarios = $query->latest('id')->paginate(10)->withQueryString();
            $totalUsuariosCount = User::count();

            return view('admin.dashboard', compact(
                'totalProductos',
                'productosActivos',
                'productosInactivos',
                'valorInventario',
                'productosStockBajo',
                'totalVentas',
                'totalVentasCount',
                'totalPedidos',
                'pedidosPendientes',
                'usuarios',
                'totalUsuariosCount'
            ));
        } else {
            return redirect()->route('tienda.inicio');
        }
    }

    /**
     * Alternar rol de usuario (Admin <-> Cliente)
     */
    public function cambiarRol($id)
    {
        $usuario = User::findOrFail($id);

        if (auth()->id() == $usuario->id) {
            return back()->with('error', 'No puedes cambiar tu propio rol de administrador mientras estás en sesión activa.');
        }

        $nuevoRol = ($usuario->role_id == 1) ? 2 : 1;
        $usuario->role_id = $nuevoRol;
        $usuario->save();

        $nombreRol = ($nuevoRol == 1) ? 'Administrador' : 'Cliente';
        return back()->with('success', "El rol de {$usuario->name} ({$usuario->email}) fue cambiado a {$nombreRol} correctamente.");
    }
}

