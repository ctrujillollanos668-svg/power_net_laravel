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

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este controlador en una sustentación?
> *"El `CarritoController` almacena temporalmente los artículos seleccionados en la sesión del servidor (`session('cart')`). Antes de renderizar la vista o procesar el pago, el método `index` auto-corrige el carrito contra la base de datos: si un producto fue agotado o se redujo su stock por otra venta, el controlador ajusta la cantidad en sesión automáticamente para evitar vender unidades inexistentes."*

### 2. Estructura de la sesión `cart`:
```php
session('cart') => [
    14 => [
        'id' => 14,
        'nombre' => 'Router Gigabit Wi-Fi 6',
        'precio' => 180000.0,
        'precio_oferta' => 155000.0,
        'cantidad' => 2,
        'stock' => 10,
        'imagen' => 'router_65a7.webp'
    ]
]
```

### 3. Posibles errores y soluciones:
- **El carrito aparece vacío tras agregar un producto**: Verifica que en el archivo `.env` la variable `SESSION_DRIVER=file` o `SESSION_DRIVER=database` esté configurada correctamente.
- **El botón de agregar no actualiza el contador**: Si se usa AJAX, verifica que el archivo JavaScript lea la respuesta `response.data.cartCount` y actualice el elemento `<span id="cart-count">`.
