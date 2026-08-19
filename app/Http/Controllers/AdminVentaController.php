<?php

namespace App\Http\Controllers;

use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminVentaController extends Controller
{
    /**
     * Muestra el módulo de ventas, reportes financieros y productos más vendidos.
     */
    public function index(Request $request)
    {
        $query = Pedido::with([
            'cliente.persona',
            'detalles.producto.imagenes',
            'pago',
            'envio'
        ])->where('estado_pedido', '!=', 'Cancelado')
          ->latest('fecha_pedido');

        // Filtro por Período / Fechas
        $periodo = $request->get('periodo', 'todos');
        if ($periodo === 'hoy') {
            $query->whereDate('fecha_pedido', Carbon::today());
        } elseif ($periodo === 'semana') {
            $query->whereBetween('fecha_pedido', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($periodo === 'mes') {
            $query->whereMonth('fecha_pedido', Carbon::now()->month)
                  ->whereYear('fecha_pedido', Carbon::now()->year);
        } elseif ($periodo === 'anio') {
            $query->whereYear('fecha_pedido', Carbon::now()->year);
        }

        // Filtro personalizado por rango de fechas
        if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
            $query->whereBetween('fecha_pedido', [
                Carbon::parse($request->fecha_desde)->startOfDay(),
                Carbon::parse($request->fecha_hasta)->endOfDay()
            ]);
        }

        // Filtro por Método de Pago
        if ($request->filled('metodo') && $request->metodo !== 'todos') {
            $query->whereHas('pago', function ($q) use ($request) {
                $q->where('metodo_pago', 'like', "%{$request->metodo}%");
            });
        }

        // Búsqueda por Factura, Pedido, Cliente o Documento
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%{$q}%")
                    ->orWhereHas('pago', function ($p) use ($q) {
                        $p->where('factura', 'like', "%{$q}%");
                    })
                    ->orWhereHas('cliente.persona', function ($c) use ($q) {
                        $c->where('nombre_persona', 'like', "%{$q}%")
                          ->orWhere('documento', 'like', "%{$q}%")
                          ->orWhere('telefono', 'like', "%{$q}%");
                    });
            });
        }

        $ventas = $query->paginate(10)->withQueryString();

        // Métricas Comerciales Globales
        $totalIngresos = Pedido::where('estado_pedido', '!=', 'Cancelado')->sum('total_pedido');
        $totalOrdenesVenta = Pedido::where('estado_pedido', '!=', 'Cancelado')->count();
        $ticketPromedio = $totalOrdenesVenta > 0 ? round($totalIngresos / $totalOrdenesVenta) : 0;
        $totalUnidadesVendidas = DetallePedido::whereHas('pedido', function ($q) {
            $q->where('estado_pedido', '!=', 'Cancelado');
        })->sum('cantidad');

        // Ventas de Hoy y del Mes
        $ventasHoy = Pedido::where('estado_pedido', '!=', 'Cancelado')
            ->whereDate('fecha_pedido', Carbon::today())
            ->sum('total_pedido');

        $ventasMes = Pedido::where('estado_pedido', '!=', 'Cancelado')
            ->whereMonth('fecha_pedido', Carbon::now()->month)
            ->whereYear('fecha_pedido', Carbon::now()->year)
            ->sum('total_pedido');

        // Top 5 Productos Más Vendidos
        $topProductos = DetallePedido::select('producto_id', DB::raw('SUM(cantidad) as total_vendido'), DB::raw('SUM(subtotal) as total_ingresos'))
            ->whereHas('pedido', function ($q) {
                $q->where('estado_pedido', '!=', 'Cancelado');
            })
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->take(5)
            ->with(['producto.imagenes'])
            ->get();

        return view('admin.ventas.index', compact(
            'ventas',
            'totalIngresos',
            'totalOrdenesVenta',
            'ticketPromedio',
            'totalUnidadesVendidas',
            'ventasHoy',
            'ventasMes',
            'topProductos'
        ));
    }
}
