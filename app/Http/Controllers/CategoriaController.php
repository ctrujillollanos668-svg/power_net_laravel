<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = Categoria::with(['productos.imagenes', 'productos.proveedor'])
            ->withCount('productos')
            ->latest('id')
            ->paginate(10);

        return view('admin.categoria.Categoria', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_categoria' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:0,1',
        ]);

        $categoria = new Categoria();
        $categoria->nombre_categoria = $request->post('nombre_categoria');
        $categoria->descripcion = $request->post('descripcion');
        $categoria->estado = $request->post('estado');
        $categoria->save();

        return redirect()
            ->route('categorias.index')
            ->with('Mensaje', '¡Categoría creada correctamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_categoria' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:0,1',
        ]);

        $categoria = Categoria::findOrFail($id);
        $categoria->nombre_categoria = $request->post('nombre_categoria');
        $categoria->descripcion = $request->post('descripcion');
        $categoria->estado = $request->post('estado');
        $categoria->save();

        return redirect()
            ->route('categorias.index')
            ->with('Mensaje', 'Categoría actualizada correctamente.');
    }

    public function destroy($id)
    {
        $categoria = Categoria::withCount('productos')->findOrFail($id);

        if ($categoria->productos_count > 0) {
            return redirect()
                ->route('categorias.index')
                ->with('Error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $categoria->delete();

        return redirect()
            ->route('categorias.index')
            ->with('Mensaje', 'Categoría eliminada correctamente.');
    }

    /**
     * Cambiar estado activo/inactivo (Toggle Switch).
     */
    public function cambiarEstado($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->estado = !$categoria->estado;
        $categoria->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'estado' => (bool)$categoria->estado,
                'mensaje' => 'Estado de la categoría actualizado correctamente.'
            ]);
        }

        return redirect()
            ->route('categorias.index')
            ->with('Mensaje', 'Estado de la categoría actualizado correctamente.');
    }
}
