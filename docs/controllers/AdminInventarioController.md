# 📦 AdminInventarioController

## 📍 Ubicación
`app/Http/Controllers/AdminInventarioController.php`

---

## 🎯 Propósito General
Controla la gestión de existencias en almacén, kardex de entradas y salidas de mercancía, cálculo del valor total del inventario (a costo y precio de venta estimado), margen bruto potencial y auditoría de movimientos.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\proveedor;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index(Request $request)` - Panel de Almacén, Filtros y Métricas

#### 💻 Código Clave:
```php
public function index(Request $request)
{
    $query = Producto::with(['categoria', 'proveedor', 'imagenes', 'ofertaActiva']);

    // 1. Búsqueda por múltiples campos
    if ($request->filled('q')) {
        $q = trim($request->q);
        $query->where(function ($sub) use ($q) {
            $sub->where('id', 'like', "%{$q}%")
                ->orWhere('nombre', 'like', "%{$q}%")
                ->orWhere('descripcion', 'like', "%{$q}%")
                ->orWhereHas('categoria', fn($c) => $c->where('nombre_categoria', 'like', "%{$q}%"))
                ->orWhereHas('proveedor', fn($p) => $p->where('nombre_proveedor', 'like', "%{$q}%"));
        });
    }

    // 2. Filtro por Nivel de Stock
    if ($request->filled('nivel')) {
        switch ($request->nivel) {
            case 'agotado': $query->where('stock', '<=', 0); break;
            case 'bajo':    $query->where('stock', '>', 0)->where('stock', '<=', 5); break;
            case 'optimo':  $query->where('stock', '>', 5); break;
        }
    }

    $productos = $query->paginate(10)->withQueryString();

    // ========== MÉTRICAS GLOBALES DEL ALMACÉN ==========
    $totalReferencias = Producto::count();
    $unidadesTotales = Producto::sum('stock');
    
    // Inversión en almacén a costo de compra (Stock * Precio Compra)
    $valorInventarioCosto = Producto::selectRaw('SUM(stock * precio_compra) as total')->value('total') ?? 0;
    
    // Valoración a precio de venta estimado (Stock * Precio Venta)
    $valorInventarioVenta = Producto::selectRaw('SUM(stock * precio) as total')->value('total') ?? 0;
    
    // Margen Bruto Potencial porcentual
    $margenPotencial = $valorInventarioVenta > 0 
        ? round((($valorInventarioVenta - $valorInventarioCosto) / $valorInventarioVenta) * 100, 1) 
        : 0;

    return view('admin.inventario.index', compact(
        'productos', 'totalReferencias', 'unidadesTotales',
        'valorInventarioCosto', 'valorInventarioVenta', 'margenPotencial'
    ));
}
```

---

### 2. `ajustarStock(Request $request)` - Registro de Kardex y Ajuste Manual

#### 💻 Código Clave:
```php
public function ajustarStock(Request $request)
{
    $request->validate([
        'producto_id' => 'required|exists:productos,id',
        'tipo' => 'required|in:entrada,salida',
        'cantidad' => 'required|integer|min:1',
        'motivo' => 'required|string|max:100',
    ]);

    $producto = Producto::findOrFail($request->producto_id);
    $stockAnterior = (int)$producto->stock;
    $cantidad = (int)$request->cantidad;

    if ($request->tipo === 'entrada') {
        $stockNuevo = $stockAnterior + $cantidad;
    } else {
        // Validación de sobregiro de stock
        if ($cantidad > $stockAnterior) {
            return redirect()->back()->withErrors([
                'cantidad' => "No puedes retirar {$cantidad} unidades. El stock actual es de sólo {$stockAnterior}."
            ])->withInput();
        }
        $stockNuevo = $stockAnterior - $cantidad;
    }

    // 1. Actualizar el stock del producto
    $producto->stock = $stockNuevo;
    $producto->save();

    // 2. Guardar trazabilidad en el Kardex (tabla inventarios)
    Inventario::create([
        'producto_id' => $producto->id,
        'tipo' => $request->tipo,
        'cantidad' => $cantidad,
        'stock_anterior' => $stockAnterior,
        'stock_nuevo' => $stockNuevo,
        'motivo' => $request->motivo,
        'pedido_id' => null,
    ]);

    return redirect()->route('admin.inventario.index')
        ->with('Mensaje', "Movimiento registrado con éxito. Nuevo stock: {$stockNuevo} unidades.");
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este controlador en una sustentación?
> *"El `AdminInventarioController` maneja el control de existencias de PowerNet mediante un sistema de Kardex. Cuando se realiza un ajuste con `ajustarStock`, el controlador no solo actualiza la columna `stock` en la tabla `productos`, sino que genera una entrada inmutable en la tabla `inventarios` guardando el stock anterior, el nuevo stock, la cantidad movida y el motivo para auditoría contable."*

### 2. Tablas y campos afectados en MySQL:
- **`productos`**: `stock`, `precio`, `precio_compra`.
- **`inventarios`**: `producto_id`, `tipo` (`entrada`/`salida`), `cantidad`, `stock_anterior`, `stock_nuevo`, `motivo`, `pedido_id`.

### 3. Posibles errores y soluciones:
- **Error al restar stock ("No puedes retirar X unidades...")**: Ocurre si la cantidad a sacar supera el stock disponible en bodega.
- **El valor total de inventario da cero**: Verifica que los productos tengan cargados tanto `stock` como `precio_compra` en la base de datos.
