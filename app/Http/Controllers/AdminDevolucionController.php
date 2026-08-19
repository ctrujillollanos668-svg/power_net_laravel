<?php

namespace App\Http\Controllers;

use App\Models\DetalleDevolucion;
use App\Models\Devolucion;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;

class AdminDevolucionController extends Controller
{
    /**
     * Muestra la lista, estadísticas y control de devoluciones / garantías.
     */
    public function index(Request $request)
    {
        $query = Devolucion::with([
            'pedido.cliente.persona',
            'pedido.detalles.producto.imagenes',
            'detalles.producto.imagenes'
        ])->latest('fecha_devolucion');

        // Filtro por Estado
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        // Búsqueda por ID, # Pedido, Cliente o Motivo
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%{$q}%")
                    ->orWhere('pedido_id', 'like', "%{$q}%")
                    ->orWhere('motivo', 'like', "%{$q}%")
                    ->orWhereHas('pedido.cliente.persona', function ($p) use ($q) {
                        $p->where('nombre_persona', 'like', "%{$q}%")
                          ->orWhere('documento', 'like', "%{$q}%")
                          ->orWhere('telefono', 'like', "%{$q}%");
                    });
            });
        }

        $devoluciones = $query->paginate(10)->withQueryString();

        // Métricas
        $totalDevoluciones = Devolucion::count();
        $pendientesRevision = Devolucion::where('estado', 'Pendiente')->count();
        $aprobadas = Devolucion::whereIn('estado', ['Aprobada', 'Completada'])->count();
        $totalReembolsado = Devolucion::whereIn('estado', ['Aprobada', 'Completada'])->sum('monto_devolucion');

        // Pedidos disponibles para nueva devolución
        $pedidosRecientes = Pedido::with('cliente.persona')->latest()->take(20)->get();

        return view('admin.devoluciones.index', compact(
            'devoluciones',
            'totalDevoluciones',
            'pendientesRevision',
            'aprobadas',
            'totalReembolsado',
            'pedidosRecientes'
        ));
    }

    /**
     * Registra una nueva devolución administrativa.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'motivo' => 'required|string|max:500',
            'monto_devolucion' => 'required|numeric|min:0',
            'estado' => 'required|string|max:50',
        ]);

        $devolucion = Devolucion::create([
            'pedido_id' => $validated['pedido_id'],
            'fecha_devolucion' => now(),
            'motivo' => $validated['motivo'],
            'monto_devolucion' => $validated['monto_devolucion'],
            'estado' => $validated['estado'],
            'motivo_rechazo' => null,
        ]);

        // Registrar detalles si vienen productos
        $pedido = Pedido::with('detalles.producto')->find($validated['pedido_id']);
        if ($pedido && $pedido->detalles) {
            foreach ($pedido->detalles as $detalle) {
                DetalleDevolucion::create([
                    'devolucione_id' => $devolucion->id,
                    'producto_id' => $detalle->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'motivo' => $validated['motivo'],
                ]);

                // Si se aprueba de inmediato, reponer stock
                if ($validated['estado'] === 'Aprobada' || $validated['estado'] === 'Completada') {
                    if ($detalle->producto) {
                        $detalle->producto->increment('stock', $detalle->cantidad);
                    }
                }
            }
        }

        return redirect()->route('admin.devoluciones.index')->with('success', "Devolución #{$devolucion->id} registrada exitosamente.");
    }

    /**
     * Actualiza el estado de la devolución (Aprobar, Rechazar, Completar).
     */
    public function updateEstado(Request $request, $id)
    {
        $devolucion = Devolucion::with('detalles.producto')->findOrFail($id);

        $validated = $request->validate([
            'estado' => 'required|string|max:50',
            'motivo_rechazo' => 'nullable|string|max:500',
            'reponer_stock' => 'nullable|boolean',
        ]);

        $estadoAnterior = $devolucion->estado;
        $devolucion->estado = $validated['estado'];

        if (!empty($validated['motivo_rechazo'])) {
            $devolucion->motivo_rechazo = $validated['motivo_rechazo'];
        }

        $devolucion->save();

        // Reponer stock si se solicita y no estaba aprobada antes
        if (!empty($validated['reponer_stock']) && $validated['estado'] === 'Aprobada' && $estadoAnterior !== 'Aprobada') {
            foreach ($devolucion->detalles as $det) {
                if ($det->producto) {
                    $det->producto->increment('stock', $det->cantidad ?? 1);
                }
            }
        }

        return redirect()->route('admin.devoluciones.index')->with('success', "Estado de la devolución #{$devolucion->id} actualizado a {$validated['estado']}.");
    }

    /**
     * Elimina un registro de devolución.
     */
    public function destroy($id)
    {
        $devolucion = Devolucion::findOrFail($id);
        $devolucion->detalles()->delete();
        $devolucion->delete();

        return redirect()->route('admin.devoluciones.index')->with('success', "Devolución eliminada exitosamente.");
    }
}
