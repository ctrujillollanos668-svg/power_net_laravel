<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetalleDevolucion;
use App\Models\Devolucion;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    /**
     * Muestra la lista de pedidos del cliente autenticado.
     */
    public function index()
    {
        $user = Auth::user();
        $pedidos = collect();

        if ($user && $user->persona_id) {
            $cliente = Cliente::where('persona_id', $user->persona_id)->first();
            if ($cliente) {
                $pedidos = Pedido::where('cliente_id', $cliente->id)
                    ->with(['detalles.producto.imagenes', 'envio', 'pago', 'devoluciones'])
                    ->latest()
                    ->paginate(10);
            }
        }

        return view('cliente.pedidos.MisPedidos', compact('pedidos'));
    }

    /**
     * Muestra el detalle completo de un pedido específico.
     */
    public function show($id)
    {
        $user = Auth::user();
        $pedido = Pedido::with(['cliente.persona', 'detalles.producto.imagenes', 'envio', 'pago', 'devoluciones'])
            ->findOrFail($id);

        return view('cliente.checkout.Confirmacion', compact('pedido'));
    }

    /**
     * Permite al cliente solicitar una devolución o reclamo de garantía sobre su pedido.
     */
    public function solicitarDevolucion(Request $request, $id)
    {
        $user = Auth::user();
        $cliente = $user && $user->persona_id ? Cliente::where('persona_id', $user->persona_id)->first() : null;

        $pedido = Pedido::with('detalles.producto')->findOrFail($id);

        // Verificar que el pedido pertenece al cliente si está autenticado
        if ($cliente && $pedido->cliente_id !== $cliente->id) {
            return back()->with('error', 'No tienes permiso para solicitar devoluciones en este pedido.');
        }

        $validated = $request->validate([
            'motivo_categoria' => 'required|string|max:100',
            'descripcion' => 'required|string|max:1000',
        ]);

        $motivoCompleto = "[{$validated['motivo_categoria']}] {$validated['descripcion']}";

        $devolucion = Devolucion::create([
            'pedido_id' => $pedido->id,
            'fecha_devolucion' => now(),
            'motivo' => $motivoCompleto,
            'monto_devolucion' => $pedido->total_pedido,
            'estado' => 'Pendiente',
            'motivo_rechazo' => null,
        ]);

        // Registrar productos en detalle devolución
        if ($pedido->detalles) {
            foreach ($pedido->detalles as $detalle) {
                DetalleDevolucion::create([
                    'devolucione_id' => $devolucion->id,
                    'producto_id' => $detalle->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'motivo' => $motivoCompleto,
                ]);
            }
        }

        return back()->with('success', "Tu solicitud de devolución para el pedido #{$pedido->id} ha sido radicada correctamente. Nuestro equipo de soporte la revisará a la brevedad.");
    }
}
