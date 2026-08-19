<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;

class AdminPagoController extends Controller
{
    /**
     * Muestra la lista, control financiero y conciliación de todos los pagos de la tienda.
     */
    public function index(Request $request)
    {
        $query = Pago::with([
            'pedido.cliente.persona',
            'pedido.detalles.producto.imagenes',
            'pedido.envio'
        ])->latest('created_at');

        // Filtro por Estado de Pago
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado_pago', $request->estado);
        }

        // Filtro por Método de Pago
        if ($request->filled('metodo') && $request->metodo !== 'todos') {
            $query->where('metodo_pago', 'like', "%{$request->metodo}%");
        }

        // Búsqueda por Factura, ID Pedido, Cliente o Documento
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('factura', 'like', "%{$q}%")
                    ->orWhere('metodo_pago', 'like', "%{$q}%")
                    ->orWhere('pedido_id', 'like', "%{$q}%")
                    ->orWhere('id', 'like', "%{$q}%")
                    ->orWhereHas('pedido.cliente.persona', function ($p) use ($q) {
                        $p->where('nombre_persona', 'like', "%{$q}%")
                          ->orWhere('telefono', 'like', "%{$q}%")
                          ->orWhere('documento', 'like', "%{$q}%");
                    });
            });
        }

        $pagos = $query->paginate(10)->withQueryString();

        // Métricas Financieras
        $totalRecaudado = Pago::where('estado_pago', 'Aprobado')->sum('monto');
        $totalPendiente = Pago::whereIn('estado_pago', ['Pendiente', 'Pendiente al entregar'])->sum('monto');
        $transaccionesAprobadas = Pago::where('estado_pago', 'Aprobado')->count();
        $transaccionesPendientes = Pago::whereIn('estado_pago', ['Pendiente', 'Pendiente al entregar'])->count();
        $totalTransacciones = Pago::count();

        return view('admin.pagos.index', compact(
            'pagos',
            'totalRecaudado',
            'totalPendiente',
            'transaccionesAprobadas',
            'transaccionesPendientes',
            'totalTransacciones'
        ));
    }

    /**
     * Actualiza el estado de un pago, método de pago o folio de factura.
     */
    public function update(Request $request, $id)
    {
        $pago = Pago::with('pedido')->findOrFail($id);

        $validated = $request->validate([
            'estado_pago' => 'required|string|max:50',
            'metodo_pago' => 'required|string|max:50',
            'factura' => 'required|string|max:100',
            'monto' => 'required|numeric|min:0',
        ]);

        $pago->estado_pago = $validated['estado_pago'];
        $pago->metodo_pago = $validated['metodo_pago'];
        $pago->factura = $validated['factura'];
        $pago->monto = $validated['monto'];

        if ($validated['estado_pago'] === 'Aprobado' && empty($pago->fecha_pago)) {
            $pago->fecha_pago = now();
        }

        $pago->save();

        // Sincronizar con el pedido si se aprueba el pago
        if ($pago->pedido && $validated['estado_pago'] === 'Aprobado') {
            if ($pago->pedido->estado_pedido === 'Cancelado') {
                $pago->pedido->estado_pedido = 'En preparación';
                $pago->pedido->save();
            }
        }

        return redirect()->route('admin.pagos.index')->with('success', "Pago de la factura {$pago->factura} actualizado exitosamente.");
    }

    /**
     * Cambio rápido de estado de pago (Aprobar / Rechazar).
     */
    public function cambiarEstado(Request $request, $id)
    {
        $pago = Pago::with('pedido')->findOrFail($id);

        $request->validate([
            'estado_pago' => 'required|string|max:50'
        ]);

        $pago->estado_pago = $request->estado_pago;
        if ($request->estado_pago === 'Aprobado') {
            $pago->fecha_pago = now();
        }
        $pago->save();

        return redirect()->route('admin.pagos.index')->with('success', "Estado del pago de la factura {$pago->factura} cambiado a {$request->estado_pago}.");
    }

    /**
     * Cancela o rechaza un pago.
     */
    public function destroy($id)
    {
        $pago = Pago::with('pedido')->findOrFail($id);
        $pago->estado_pago = 'Rechazado';
        $pago->save();

        return redirect()->route('admin.pagos.index')->with('success', "Pago {$pago->factura} marcado como Rechazado.");
    }
}
