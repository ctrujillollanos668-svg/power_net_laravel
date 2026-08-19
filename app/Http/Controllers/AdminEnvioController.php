<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Models\Pedido;
use Illuminate\Http\Request;

class AdminEnvioController extends Controller
{
    /**
     * Muestra la lista y control general de todos los envíos de la tienda.
     */
    public function index(Request $request)
    {
        $query = Envio::with([
            'pedido.cliente.persona',
            'pedido.detalles.producto.imagenes',
            'pedido.pago'
        ])->latest('fecha_hora');

        // Filtro por Estado de Envío
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        // Filtro por Transportadora
        if ($request->filled('empresa') && $request->empresa !== 'todos') {
            $query->where('empresa_envios', 'like', "%{$request->empresa}%");
        }

        // Búsqueda por ID, # Pedido, Cliente, Dirección o Teléfono
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%{$q}%")
                    ->orWhere('direccion_envio', 'like', "%{$q}%")
                    ->orWhere('empresa_envios', 'like', "%{$q}%")
                    ->orWhere('pedido_id', 'like', "%{$q}%")
                    ->orWhereHas('pedido.cliente.persona', function ($p) use ($q) {
                        $p->where('nombre_persona', 'like', "%{$q}%")
                          ->orWhere('telefono', 'like', "%{$q}%")
                          ->orWhere('documento', 'like', "%{$q}%");
                    });
            });
        }

        $envios = $query->paginate(10)->withQueryString();

        // Métricas de Envíos
        $totalEnvios = Envio::count();
        $enviosPendientes = Envio::whereIn('estado', ['Pendiente', 'En preparación'])->count();
        $enviosEnCamino = Envio::whereIn('estado', ['Enviado', 'En camino', 'En tránsito'])->count();
        $enviosEntregados = Envio::where('estado', 'Entregado')->count();

        return view('admin.envios.index', compact(
            'envios',
            'totalEnvios',
            'enviosPendientes',
            'enviosEnCamino',
            'enviosEntregados'
        ));
    }

    /**
     * Actualiza los datos de la transportadora, dirección, estado y sincroniza el pedido.
     */
    public function update(Request $request, $id)
    {
        $envio = Envio::with('pedido')->findOrFail($id);

        $validated = $request->validate([
            'empresa_envios' => 'required|string|max:100',
            'estado' => 'required|string|max:50',
            'costo' => 'nullable|numeric|min:0',
            'direccion_envio' => 'required|string|max:255',
        ]);

        $envio->empresa_envios = $validated['empresa_envios'];
        $envio->estado = $validated['estado'];
        if (isset($validated['costo'])) {
            $envio->costo = $validated['costo'];
        }
        $envio->direccion_envio = $validated['direccion_envio'];
        $envio->save();

        // Sincronizar estado en el Pedido asociado
        if ($envio->pedido) {
            $envio->pedido->estado_pedido = $validated['estado'];
            $envio->pedido->save();
        }

        return redirect()->route('admin.envios.index')->with('success', "Envío #{$envio->id} actualizado exitosamente.");
    }

    /**
     * Cambio rápido de estado desde la tabla.
     */
    public function cambiarEstado(Request $request, $id)
    {
        $envio = Envio::with('pedido')->findOrFail($id);

        $request->validate([
            'estado' => 'required|string|max:50'
        ]);

        $envio->estado = $request->estado;
        $envio->save();

        if ($envio->pedido) {
            $envio->pedido->estado_pedido = $request->estado;
            $envio->pedido->save();
        }

        return redirect()->route('admin.envios.index')->with('success', "Estado del envío #{$envio->id} cambiado a {$request->estado}.");
    }

    /**
     * Cancela o elimina un envío.
     */
    public function destroy($id)
    {
        $envio = Envio::with('pedido')->findOrFail($id);
        $envio->estado = 'Cancelado';
        $envio->save();

        if ($envio->pedido) {
            $envio->pedido->estado_pedido = 'Cancelado';
            $envio->pedido->save();
        }

        return redirect()->route('admin.envios.index')->with('success', "Envío #{$envio->id} cancelado exitosamente.");
    }
}
