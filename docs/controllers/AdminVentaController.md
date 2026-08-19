# 📈 AdminVentaController

## 📍 Ubicación
`app/Http/Controllers/AdminVentaController.php`

---

## 🎯 Propósito General
Genera reportes financieros, estadísticas de rendimiento comercial y análisis del volumen de ventas. Permite filtrar ingresos por rangos de fecha predefinidos o personalizados y extrae el Top 5 de productos más vendidos.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index(Request $request)` - Informes Financieros y Ranking de Ventas

#### 💻 Código Clave:
```php
public function index(Request $request)
{
    $query = Pedido::with([
        'cliente.persona',
        'detalles.producto.imagenes',
        'pago',
        'envio'
    ])->where('estado_pedido', '!=', 'Cancelado')
      ->latest('fecha_pedido');

    // 1. Filtro por Períodos Rápidos con Carbon
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

    // 2. Filtro Personalizado por Rango de Fechas
    if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
        $query->whereBetween('fecha_pedido', [
            Carbon::parse($request->fecha_desde)->startOfDay(),
            Carbon::parse($request->fecha_hasta)->endOfDay()
        ]);
    }

    $ventas = $query->paginate(10)->withQueryString();

    // 3. Métricas Financieras
    $totalIngresos = Pedido::where('estado_pedido', '!=', 'Cancelado')->sum('total_pedido');
    $totalOrdenesVenta = Pedido::where('estado_pedido', '!=', 'Cancelado')->count();
    $ticketPromedio = $totalOrdenesVenta > 0 ? round($totalIngresos / $totalOrdenesVenta) : 0;
    
    $totalUnidadesVendidas = DetallePedido::whereHas('pedido', fn($q) => $q->where('estado_pedido', '!=', 'Cancelado'))->sum('cantidad');

    // 4. Ranking Top 5 Productos Más Vendidos
    $topProductos = DetallePedido::select('producto_id', DB::raw('SUM(cantidad) as total_vendido'), DB::raw('SUM(subtotal) as total_ingresos'))
        ->whereHas('pedido', fn($q) => $q->where('estado_pedido', '!=', 'Cancelado'))
        ->groupBy('producto_id')
        ->orderByDesc('total_vendido')
        ->take(5)
        ->with(['producto.imagenes'])
        ->get();

    return view('admin.ventas.index', compact(
        'ventas', 'totalIngresos', 'totalOrdenesVenta',
        'ticketPromedio', 'totalUnidadesVendidas', 'topProductos'
    ));
}
```

#### 🔍 ¿Qué hace este código?
- **`Carbon::startOfWeek()` / `Carbon::today()`**: Manipula fechas de forma limpia para filtrar transacciones de hoy, esta semana, este mes o este año.
- **`ticketPromedio`**: Calcula el valor monetario medio gastado por cada cliente en cada orden ($\text{Total Ingresos} / \text{Total Órdenes}$).
- **Agrupamiento SQL (`groupBy('producto_id')` y `DB::raw(...)`)**: Suma las cantidades y subtotales agrupados por cada producto para generar el ranking de los 5 artículos con mayor demanda.
