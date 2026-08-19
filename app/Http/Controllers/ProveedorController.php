<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\proveedor;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proveedores = proveedor::with(['productos.imagenes', 'productos.categoria'])
            ->withCount('productos')
            ->latest('id')
            ->paginate(10);

        return view('admin.proveedor.Proveedor', compact('proveedores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_proveedor' => 'required|string|max:100',
            'correo' => 'required|email|max:150',
            'telefono' => 'required|string|max:30',
            'estado' => 'required|in:0,1',
        ]);

        $proveedor = new proveedor();
        $proveedor->nombre_proveedor = $request->nombre_proveedor;
        $proveedor->correo = $request->correo;
        $proveedor->telefono = $request->telefono;
        $proveedor->estado = $request->estado;
        $proveedor->save();

        return redirect()
            ->route('proveedores.index')
            ->with('Mensaje', '¡Proveedor registrado correctamente!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre_proveedor' => 'required|string|max:100',
            'correo' => 'required|email|max:150',
            'telefono' => 'required|string|max:30',
            'estado' => 'required|in:0,1',
        ]);

        $proveedor = proveedor::findOrFail($id);
        $proveedor->nombre_proveedor = $request->nombre_proveedor;
        $proveedor->correo = $request->correo;
        $proveedor->telefono = $request->telefono;
        $proveedor->estado = $request->estado;
        $proveedor->save();

        return redirect()
            ->route('proveedores.index')
            ->with('Mensaje', 'Proveedor actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $proveedor = proveedor::withCount('productos')->findOrFail($id);
        
        if ($proveedor->productos_count > 0) {
            return redirect()
                ->route('proveedores.index')
                ->with('Error', 'No se puede eliminar el proveedor porque tiene productos asociados.');
        }

        $proveedor->delete();

        return redirect()
            ->route('proveedores.index')
            ->with('Mensaje', 'Proveedor eliminado correctamente.');
    }

    /**
     * Cambiar estado activo/inactivo (Toggle Switch).
     */
    public function cambiarEstado($id)
    {
        $proveedor = proveedor::findOrFail($id);
        $proveedor->estado = !$proveedor->estado;
        $proveedor->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'estado' => (bool)$proveedor->estado,
                'mensaje' => 'Estado del proveedor actualizado correctamente.'
            ]);
        }

        return redirect()
            ->route('proveedores.index')
            ->with('Mensaje', 'Estado del proveedor actualizado correctamente.');
    }
}
