<?php

namespace App\Http\Controllers;

use App\Models\Oferta;
use App\Models\Producto;
use Illuminate\Http\Request;

class OfertaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ofertas = Oferta::with('producto')->latest('id')->paginate(10);
        $productos = Producto::where('disponibilidad', 1)->get();

        return view('admin.oferta.Oferta', compact('ofertas', 'productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'precio_oferta' => 'required|numeric|min:0',
            'descuento' => 'nullable|integer|min:0|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'estado' => 'required|string|in:activa,inactiva,vencida',
        ]);

        $producto = Producto::findOrFail($request->producto_id);

        $descuento = $request->descuento;
        if (!$descuento && $producto->precio > 0) {
            $descuento = round((($producto->precio - $request->precio_oferta) / $producto->precio) * 100);
        }

        $oferta = new Oferta();
        $oferta->producto_id = $request->producto_id;
        $oferta->precio_oferta = $request->precio_oferta;
        $oferta->descuento = max(0, min(100, (int)$descuento));
        $oferta->fecha_inicio = $request->fecha_inicio;
        $oferta->fecha_fin = $request->fecha_fin;
        $oferta->estado = $request->estado;
        $oferta->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'mensaje' => '¡Oferta creada exitosamente!',
                'oferta' => $oferta
            ]);
        }

        return redirect()->back()->with('Mensaje', '¡Oferta creada exitosamente!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'precio_oferta' => 'required|numeric|min:0',
            'descuento' => 'nullable|integer|min:0|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'estado' => 'required|string|in:activa,inactiva,vencida',
        ]);

        $oferta = Oferta::findOrFail($id);
        $producto = $oferta->producto;

        $descuento = $request->descuento;
        if (!$descuento && $producto && $producto->precio > 0) {
            $descuento = round((($producto->precio - $request->precio_oferta) / $producto->precio) * 100);
        }

        $oferta->precio_oferta = $request->precio_oferta;
        $oferta->descuento = max(0, min(100, (int)$descuento));
        $oferta->fecha_inicio = $request->fecha_inicio;
        $oferta->fecha_fin = $request->fecha_fin;
        $oferta->estado = $request->estado;
        $oferta->save();

        return redirect()->back()->with('Mensaje', 'Oferta actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $oferta = Oferta::findOrFail($id);
        $oferta->delete();

        return redirect()->back()->with('Mensaje', 'Oferta eliminada correctamente.');
    }

    /**
     * Cambiar estado de la oferta.
     */
    public function cambiarEstado(string $id)
    {
        $oferta = Oferta::findOrFail($id);
        $oferta->estado = ($oferta->estado === 'activa') ? 'inactiva' : 'activa';
        $oferta->save();

        return redirect()->back()->with('Mensaje', 'Estado de la oferta actualizado.');
    }
}
