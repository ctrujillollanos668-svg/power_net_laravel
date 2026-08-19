# 💳 CheckoutController

## 📍 Ubicación
`app/Http/Controllers/CheckoutController.php`

---

## 🎯 Propósito General
Orquesta la finalización de compra. Captura los datos del comprador y la dirección de entrega, valida la disponibilidad final de inventario y ejecuta una **transacción atómica (`DB::transaction`)** que crea el pedido, descuenta el stock de almacén, registra el pago, programa el despacho y vacía el carrito.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Envio;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index()` - Pantalla de Checkout y Validación Previa

#### 💻 Código Clave:
```php
public function index()
{
    $cart = session()->get('cart', []);

    if (empty($cart)) {
        return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
    }

    // Valida y sincroniza stock antes de mostrar el formulario de pago
    $cartModificado = false;
    foreach ($cart as $id => &$item) {
        $producto = Producto::find($item['id'] ?? $id);
        if (!$producto || (int) $producto->stock <= 0) {
            unset($cart[$id]);
            $cartModificado = true;
            continue;
        }

        $stockActual = (int) $producto->stock;
        $item['stock'] = $stockActual;

        if ($item['cantidad'] > $stockActual) {
            $item['cantidad'] = $stockActual;
            $cartModificado = true;
        }
    }
    unset($item);

    if ($cartModificado) {
        session()->put('cart', $cart);
        if (empty($cart)) {
            return redirect()->route('carrito.index')->with('error', 'Los productos en tu carrito ya no tienen stock disponible.');
        }
    }

    $subtotal = 0;
    $descuentoTotal = 0;
    $totalItems = 0;

    foreach ($cart as $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
        if (isset($item['precio_oferta']) && $item['precio_oferta'] < $item['precio']) {
            $descuentoTotal += ($item['precio'] - $item['precio_oferta']) * $item['cantidad'];
        }
        $totalItems += $item['cantidad'];
    }

    $costoEnvio = ($subtotal - $descuentoTotal > 150000) ? 0 : 12000;
    $total = ($subtotal - $descuentoTotal) + $costoEnvio;

    $user = Auth::user();
    $metodosPago = MetodoPago::where('estado', 1)->get();

    return view('cliente.checkout.Checkout', compact(
        'cart', 'subtotal', 'descuentoTotal', 'costoEnvio', 'total', 'totalItems', 'user', 'metodosPago'
    ));
}
```

---

### 2. `procesar(Request $request)` - Creación Transaccional de la Orden

#### 💻 Código Clave:
```php
public function procesar(Request $request)
{
    $cart = session()->get('cart', []);
    if (empty($cart)) {
        return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
    }

    $validated = $request->validate([
        'nombre_persona' => 'required|string|max:100',
        'documento' => 'required|string|max:30',
        'email' => 'required|email|max:100',
        'telefono' => 'required|string|max:30',
        'direccion' => 'required|string|max:200',
        'ciudad' => 'required|string|max:100',
        'metodo_pago' => 'required|string|max:50',
    ]);

    // Transacción atómica en Base de Datos
    return DB::transaction(function () use ($validated, $cart, $request) {
        // 1. Crear o reutilizar la Persona por su documento (Cédula/NIT)
        $persona = Persona::firstOrCreate(
            ['documento' => $validated['documento']],
            [
                'nombre_persona' => $validated['nombre_persona'],
                'correo' => $validated['email'],
                'telefono' => $validated['telefono'],
                'direccion' => $validated['direccion'],
            ]
        );

        // 2. Crear o reutilizar el registro del Cliente
        $cliente = Cliente::firstOrCreate(['persona_id' => $persona->id]);

        // 3. Calcular totales del carrito
        $subtotal = 0;
        foreach ($cart as $item) {
            $precio = isset($item['precio_oferta']) && $item['precio_oferta'] < $item['precio']
                ? $item['precio_oferta'] : $item['precio'];
            $subtotal += $precio * $item['cantidad'];
        }
        $costoEnvio = $subtotal > 150000 ? 0 : 12000;
        $totalPedido = $subtotal + $costoEnvio;

        // 4. Crear el Pedido principal
        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'fecha_pedido' => now(),
            'total_pedido' => $totalPedido,
            'estado_pedido' => 'En preparación',
        ]);

        // 5. Guardar cada ítem del detalle y descontar stock con bloqueo de concurrencia
        foreach ($cart as $item) {
            $producto = Producto::lockForUpdate()->findOrFail($item['id']);
            
            // Verificación de stock bajo concurrencia
            if ($producto->stock < $item['cantidad']) {
                throw new \Exception("Stock insuficiente para \"{$producto->nombre}\".");
            }

            // Descuenta inventario
            $producto->decrement('stock', $item['cantidad']);

            $precioUnitario = isset($item['precio_oferta']) && $item['precio_oferta'] < $item['precio']
                ? $item['precio_oferta'] : $item['precio'];

            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $producto->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $precioUnitario,
                'subtotal' => $precioUnitario * $item['cantidad'],
            ]);
        }

        // 6. Generar Pago con folio de factura único
        Pago::create([
            'pedido_id' => $pedido->id,
            'metodo_pago' => $validated['metodo_pago'],
            'monto' => $totalPedido,
            'estado_pago' => 'Pendiente',
            'factura' => 'FAC-' . strtoupper(uniqid()),
        ]);

        // 7. Generar Envío
        Envio::create([
            'pedido_id' => $pedido->id,
            'direccion_envio' => "{$validated['direccion']}, {$validated['ciudad']}",
            'empresa_envios' => 'Coordinadora / Interrapidísimo',
            'estado' => 'Pendiente',
            'costo' => $costoEnvio,
            'fecha_hora' => now(),
        ]);

        // 8. Vaciar el carrito de la sesión
        session()->forget('cart');

        return redirect()->route('checkout.confirmacion', $pedido->id);
    });
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este controlador en una sustentación?
> *"El `CheckoutController` es el corazón transaccional del e-commerce. Utiliza `DB::transaction` para garantizar que la creación de la persona, cliente, pedido, detalles de compra, pago y envío se realicen como una sola unidad indivisible. Si ocurre un fallo (por ejemplo, si se agota el stock mientras el usuario paga con `lockForUpdate`), la base de datos realiza un Rollback automático para evitar órdenes incompletas o cobros sin productos."*

### 2. ¿Qué tablas y campos se tocan si algo se borra o se daña?
Si necesitas reconstruir o revisar una compra en la base de datos:
1. **`personas`**: Guarda `documento`, `nombre_persona`, `correo`, `telefono`, `direccion`.
2. **`clientes`**: Conecta `persona_id` con el sistema de pedidos.
3. **`pedidos`**: Contiene `cliente_id`, `fecha_pedido`, `total_pedido`, `estado_pedido`.
4. **`detalle_pedidos`**: Guarda `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`.
5. **`pagos`**: Guarda `pedido_id`, `monto`, `metodo_pago`, `estado_pago`, `factura`.
6. **`envios`**: Guarda `pedido_id`, `direccion_envio`, `empresa_envios`, `estado`, `costo`.
7. **`productos`**: Se actualiza el campo `stock` con `$producto->decrement(...)`.

### 3. Posibles errores y cómo solucionarlos:
- **Error: "Stock insuficiente..."**: Ocurre si dos personas intentan comprar la última unidad al mismo tiempo. El `lockForUpdate()` protege la integridad y cancela la transacción limpia antes de generar una venta en negativo.
- **Error con campos nulos en Persona**: Si el formulario no envía `documento`, `Persona::firstOrCreate` fallará. Revisa que el campo `documento` tenga la regla `'required'`.
- **El carrito no se vacía tras pagar**: Verifica que `session()->forget('cart')` se ejecute antes del `return redirect()`.
