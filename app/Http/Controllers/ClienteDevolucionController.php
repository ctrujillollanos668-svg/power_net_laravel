<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetalleDevolucion;
use App\Models\Devolucion;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteDevolucionController extends Controller
{
    /**
     * Muestra la lista de devoluciones y garantías del cliente autenticado.
     */
    public function index()
    {
        $user = Auth::user();
        $devoluciones = collect();
        $pedidos = collect();

        if ($user && $user->persona_id) {
            $cliente = Cliente::where('persona_id', $user->persona_id)->first();
            if ($cliente) {
                // Obtener devoluciones del cliente
                $devoluciones = Devolucion::whereHas('pedido', function ($q) use ($cliente) {
                    $q->where('cliente_id', $cliente->id);
                })
                ->with(['pedido.pago', 'detalles.producto.imagenes'])
                ->latest('fecha_devolucion')
                ->paginate(10);

                // Obtener pedidos del cliente para el modal de nueva devolución
                $pedidos = Pedido::where('cliente_id', $cliente->id)
                    ->where('estado_pedido', '!=', 'Cancelado')
                    ->with('detalles.producto')
                    ->latest()
                    ->get();
            }
        }

        // Métricas del cliente
        $totalDevoluciones = $devoluciones->total();
        $pendientes = $devoluciones->where('estado', 'Pendiente')->count();
        $aprobadas = $devoluciones->whereIn('estado', ['Aprobada', 'Completada'])->count();

        return view('cliente.devoluciones.index', compact(
            'devoluciones',
            'pedidos',
            'totalDevoluciones',
            'pendientes',
            'aprobadas'
        ));
    }

    /**
     * Radica una nueva solicitud de devolución / garantía.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $cliente = $user && $user->persona_id ? Cliente::where('persona_id', $user->persona_id)->first() : null;

        $validated = $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'motivo_categoria' => 'required|string|max:100',
            'descripcion' => 'required|string|max:1000',
        ]);

        $pedido = Pedido::with('detalles.producto')->findOrFail($validated['pedido_id']);

        if ($cliente && $pedido->cliente_id !== $cliente->id) {
            return back()->with('error', 'No tienes permiso para solicitar una devolución en este pedido.');
        }

        $motivoCompleto = "[{$validated['motivo_categoria']}] {$validated['descripcion']}";

        $devolucion = Devolucion::create([
            'pedido_id' => $pedido->id,
            'fecha_devolucion' => now(),
            'motivo' => $motivoCompleto,
            'monto_devolucion' => $pedido->total_pedido,
            'estado' => 'Pendiente',
            'motivo_rechazo' => null,
        ]);

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

        return redirect()->route('cliente.devoluciones.index')->with('success', "Tu solicitud de devolución para el pedido #{$pedido->id} ha sido radicada con éxito. Nuestro equipo la revisará.");
    }
}
