<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    /**
     * Muestra la lista de métodos de pago configurados en la tienda.
     */
    public function index()
    {
        $metodos = MetodoPago::latest()->paginate(10);
        return view('admin.metodopago.index', compact('metodos'));
    }

    /**
     * Registra un nuevo método de pago.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|string|max:50',
            'numero' => 'nullable|string|max:100',
            'titular' => 'nullable|string|max:100',
            'instrucciones' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
        ]);

        $validated['estado'] = $request->has('estado') ? 1 : 0;

        MetodoPago::create($validated);

        return redirect()->route('metodospago.index')->with('success', 'Método de pago creado exitosamente.');
    }

    /**
     * Actualiza un método de pago existente.
     */
    public function update(Request $request, $id)
    {
        $metodo = MetodoPago::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|string|max:50',
            'numero' => 'nullable|string|max:100',
            'titular' => 'nullable|string|max:100',
            'instrucciones' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
        ]);

        $validated['estado'] = $request->has('estado') ? 1 : 0;

        $metodo->update($validated);

        return redirect()->route('metodospago.index')->with('success', 'Método de pago actualizado exitosamente.');
    }

    /**
     * Elimina un método de pago.
     */
    public function destroy($id)
    {
        $metodo = MetodoPago::findOrFail($id);
        $metodo->delete();

        return redirect()->route('metodospago.index')->with('success', 'Método de pago eliminado.');
    }

    /**
     * Alterna el estado activo/inactivo de un método de pago.
     */
    public function cambiarEstado($id)
    {
        $metodo = MetodoPago::findOrFail($id);
        $metodo->estado = !$metodo->estado;
        $metodo->save();

        return redirect()->route('metodospago.index')->with('success', 'Estado del método de pago actualizado.');
    }
}
