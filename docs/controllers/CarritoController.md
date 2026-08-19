# 🛒 CarritoController

## 📍 Ubicación
`app/Http/Controllers/CarritoController.php`

---

## 🎯 Propósito General
Gestiona la cesta de compras almacenada en la sesión del usuario (`session('cart')`). Valida el stock en tiempo real, calcula descuentos por promociones, aplica reglas de envío gratuito y atiende peticiones síncronas y asíncronas (AJAX / JSON).

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Producto;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index()` - Visualización y Sincronización Automática de Stock

#### 💻 Código Clave:
```php
public function index()
{
    $cart = session()->get('cart', []);
    $cartModificado = false;

    // 1. Sincronizar el carrito contra el stock real en Base de Datos
    foreach ($cart as $id => &$item) {
        $producto = Producto::find($item['id'] ?? $id);
        if (!$producto || (int) $producto->stock <= 0) {
            unset($cart[$id]); // Si se agotó o borró, lo saca del carrito
            $cartModificado = true;
            continue;
        }

        $stockActual = (int) $producto->stock;
        $item['stock'] = $stockActual;

        // Si el cliente pide más unidades de las disponibles, ajusta al stock máximo
        if ($item['cantidad'] > $stockActual) {
            $item['cantidad'] = $stockActual;
            $cartModificado = true;
        }
    }
    unset($item);

    if ($cartModificado) {
        session()->put('cart', $cart);
    }
    
    // 2. Cálculo de Totales y Descuentos
    $subtotal = 0;
    $descuentoTotal = 0;
    $totalItems = 0;

    foreach ($cart as $id => $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
        
        if (isset($item['precio_oferta']) && $item['precio_oferta'] < $item['precio']) {
            $descuentoTotal += ($item['precio'] - $item['precio_oferta']) * $item['cantidad'];
        }
        
        $totalItems += $item['cantidad'];
    }

    // Envío gratis para compras superiores a $150.000 COP
    $costoEnvio = ($subtotal - $descuentoTotal > 150000 || empty($cart)) ? 0 : 12000;
    $total = ($subtotal - $descuentoTotal) + $costoEnvio;

    return view('cliente.carrito.Carrito', compact(
        'cart', 'subtotal', 'descuentoTotal', 'costoEnvio', 'total', 'totalItems'
    ));
}
```

#### 🔍 ¿Qué hace este código?
- **Protección contra Compras Fantasma**: Antes de mostrar el carrito, verifica el stock real en la base de datos para evitar que un cliente compre un producto que otro usuario acaba de agotar.
- **Regla de Negocio de Envío**: Aplica flete de $12.000 COP si el pedido es menor o igual a $150.000 COP, o $0 (envío gratis) si supera dicho monto.

---

### 2. `agregar(Request $request, $id)` - Añadir Producto con Control de Stock

#### 💻 Código Clave:
```php
public function agregar(Request $request, $id)
{
    $producto = Producto::with(['imagenes', 'categoria', 'ofertaActiva'])->findOrFail($id);
    $cantidad = max(1, (int) $request->input('cantidad', 1));
    $stockDisponible = (int) ($producto->stock ?? 0);

    if ($stockDisponible <= 0) {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Producto agotado.'], 422);
        }
        return back()->with('error', 'El producto está agotado.');
    }

    $cart = session()->get('cart', []);
    $cantidadActual = isset($cart[$id]) ? (int) $cart[$id]['cantidad'] : 0;
    $nuevaCantidad = min($cantidadActual + $cantidad, $stockDisponible);

    $cart[$id] = [
        'id' => $producto->id,
        'nombre' => $producto->nombre,
        'categoria' => $producto->categoria->nombre_categoria ?? 'Tecnología',
        'precio' => (float) $producto->precio,
        'precio_oferta' => $producto->ofertaActiva ? (float)$producto->ofertaActiva->precio_oferta : null,
        'imagen' => $producto->imagenes->first() ? $producto->imagenes->first()->imagen : null,
        'cantidad' => $nuevaCantidad,
        'stock' => $stockDisponible,
    ];

    session()->put('cart', $cart);

    if ($request->wantsJson()) {
        return response()->json(['success' => true, 'cartCount' => array_sum(array_column($cart, 'cantidad'))]);
    }
    return back()->with('success', '¡Producto añadido al carrito!');
}
```

#### 🔍 ¿Qué hace este código?
- Soporta tanto formularios clásicos como peticiones JavaScript (`fetch` / `axios`), respondiendo con JSON y actualizando el badge del carrito sin recargar la página.
