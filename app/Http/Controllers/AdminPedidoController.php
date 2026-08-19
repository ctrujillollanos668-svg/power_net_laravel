<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;

class AdminPedidoController extends Controller
{
    /**
     * Muestra la lista de todos los pedidos recibidos en la tienda para el administrador.
     */
    public function index(Request $request)
    {
        $query = Pedido::with([
            'cliente.persona',
            'detalles.producto.imagenes',
            'envio',
            'pago'
        ])->latest();

        // Filtro por Estado de Pedido
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado_pedido', $request->estado);
        }

        // Filtro por Estado de Pago
        if ($request->filled('pago') && $request->pago !== 'todos') {
            $query->whereHas('pago', function ($q) use ($request) {
                $q->where('estado_pago', $request->pago);
            });
        }

        // Búsqueda por ID, nombre de cliente o factura
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%{$q}%")
                    ->orWhereHas('cliente.persona', function ($p) use ($q) {
                        $p->where('nombre_persona', 'like', "%{$q}%")
                          ->orWhere('telefono', 'like', "%{$q}%")
                          ->orWhere('documento', 'like', "%{$q}%");
                    })
                    ->orWhereHas('pago', function ($pg) use ($q) {
                        $pg->where('factura', 'like', "%{$q}%");
                    });
            });
        }

        $pedidos = $query->paginate(10)->withQueryString();

        // Métricas rápidas para las tarjetas superiores
        $totalPedidos = Pedido::count();
        $pedidosPendientes = Pedido::where('estado_pedido', 'En preparación')->count();
        $pedidosEnviados = Pedido::where('estado_pedido', 'Enviado')->count();
        $pedidosEntregados = Pedido::where('estado_pedido', 'Entregado')->count();
        $totalVentas = Pedido::where('estado_pedido', '!=', 'Cancelado')->sum('total_pedido');

        return view('admin.pedidos.index', compact(
            'pedidos',
            'totalPedidos',
            'pedidosPendientes',
            'pedidosEnviados',
            'pedidosEntregados',
            'totalVentas'
        ));
    }

    /**
     * Actualiza el estado del pedido, del pago y los datos de envío.
     */
    public function updateEstado(Request $request, $id)
    {
        $pedido = Pedido::with(['envio', 'pago'])->findOrFail($id);

        $validated = $request->validate([
            'estado_pedido' => 'required|string|max:50',
            'estado_pago' => 'nullable|string|max:50',
            'empresa_envios' => 'nullable|string|max:100',
            'direccion_envio' => 'nullable|string|max:255',
        ]);

        $pedido->estado_pedido = $validated['estado_pedido'];
        $pedido->save();

        if ($pedido->pago && !empty($validated['estado_pago'])) {
            $pedido->pago->estado_pago = $validated['estado_pago'];
            $pedido->pago->save();
        }

        if ($pedido->envio) {
            if (!empty($validated['empresa_envios'])) {
                $pedido->envio->empresa_envios = $validated['empresa_envios'];
            }
            if (!empty($validated['direccion_envio'])) {
                $pedido->envio->direccion_envio = $validated['direccion_envio'];
            }
            $pedido->envio->estado = $validated['estado_pedido'];
            $pedido->envio->save();
        }

        return redirect()->route('admin.pedidos.index')->with('success', "Pedido #{$pedido->id} actualizado exitosamente.");
    }

    /**
     * Muestra el detalle de un pedido específico.
     */
    public function show($id)
    {
        $pedido = Pedido::with([
            'cliente.persona',
            'detalles.producto.imagenes',
            'envio',
            'pago'
        ])->findOrFail($id);

        return view('cliente.checkout.Confirmacion', compact('pedido'));
    }

    /**
     * Elimina o cancela un pedido.
     */
    public function destroy($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->estado_pedido = 'Cancelado';
        $pedido->save();

        return redirect()->route('admin.pedidos.index')->with('success', "Pedido #{$pedido->id} marcado como Cancelado.");
    }
}
