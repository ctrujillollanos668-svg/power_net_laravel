# 💳 CheckoutController

## 📍 Ubicación
`app/Http/Controllers/CheckoutController.php`

---

## 🎯 Propósito General
Orquesta la finalización de compra. Captura los datos del comprador y la dirección de envío, valida la disponibilidad final de inventario y ejecuta una **transacción atómica (`DB::transaction`)** que crea el pedido, descuenta el stock de almacén, registra el pago, programa el despacho y vacía el carrito.

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

### 1. `procesar(Request $request)` - Creación Transaccional de la Orden

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
        // 1. Crear o actualizar la Persona
        $persona = Persona::firstOrCreate(
            ['documento' => $validated['documento']],
            [
                'nombre_persona' => $validated['nombre_persona'],
                'correo' => $validated['email'],
                'telefono' => $validated['telefono'],
                'direccion' => $validated['direccion'],
            ]
        );

        // 2. Crear o actualizar el Cliente
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

        // 5. Guardar cada ítem del detalle y descontar stock
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

        // 6. Generar Pago con factura única
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

#### 🔍 ¿Qué hace este código?
- **`DB::transaction(...)`**: Garantiza integridad total: si ocurre un error en cualquier punto (por ejemplo si un producto se queda sin stock a mitad de camino), toda la operación se revierte (`rollback`) y no se cobra ni descuenta nada a medias.
- **`lockForUpdate()`**: Bloquea la fila del producto durante la transacción para evitar condiciones de carrera (*Race Conditions*) cuando dos clientes intentan comprar la última unidad al mismo segundo.
- **`$producto->decrement('stock', $item['cantidad'])`**: Descuenta las unidades vendidas directamente del stock.
- **`session()->forget('cart')`**: Limpia el carrito tras completar exitosamente la compra.
