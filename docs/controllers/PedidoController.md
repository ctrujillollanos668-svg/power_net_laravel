# 📦 PedidoController

## 📍 Ubicación
`app/Http/Controllers/PedidoController.php`

---

## 🎯 Propósito General
Permite a los clientes registrados ver sus compras realizadas, comprobar el estado de entrega y radicar garantías o solicitudes de devolución.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Cliente;
use App\Models\DetalleDevolucion;
use App\Models\Devolucion;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index()` - Historial de Pedidos del Cliente Autenticado

#### 💻 Código Clave:
```php
public function index()
{
    $user = Auth::user();
    $pedidos = collect();

    // Obtiene el cliente vinculado a la persona del usuario
    if ($user && $user->persona_id) {
        $cliente = Cliente::where('persona_id', $user->persona_id)->first();
        if ($cliente) {
            $pedidos = Pedido::where('cliente_id', $cliente->id)
                ->with(['detalles.producto.imagenes', 'envio', 'pago', 'devoluciones'])
                ->latest()
                ->paginate(10);
        }
    }

    return view('cliente.pedidos.MisPedidos', compact('pedidos'));
}
```

#### 🔍 ¿Qué hace este código?
- Identifica al usuario autenticado, localiza su registro de cliente mediante `$user->persona_id` y carga sus pedidos paginados con los productos, fotos, transportadora y estado del pago.

---

### 2. `solicitarDevolucion(Request $request, $id)` - Radicación de Garantía

#### 💻 Código Clave:
```php
public function solicitarDevolucion(Request $request, $id)
{
    $user = Auth::user();
    $cliente = $user && $user->persona_id ? Cliente::where('persona_id', $user->persona_id)->first() : null;

    $pedido = Pedido::with('detalles.producto')->findOrFail($id);

    // Validación estricta de propiedad
    if ($cliente && $pedido->cliente_id !== $cliente->id) {
        return back()->with('error', 'No tienes permiso para solicitar devoluciones en este pedido.');
    }

    $validated = $request->validate([
        'motivo_categoria' => 'required|string|max:100',
        'descripcion' => 'required|string|max:1000',
    ]);

    $motivoCompleto = "[{$validated['motivo_categoria']}] {$validated['descripcion']}";

    // Crea el reclamo en estado Pendiente
    $devolucion = Devolucion::create([
        'pedido_id' => $pedido->id,
        'fecha_devolucion' => now(),
        'motivo' => $motivoCompleto,
        'monto_devolucion' => $pedido->total_pedido,
        'estado' => 'Pendiente',
        'motivo_rechazo' => null,
    ]);

    // Asocia los productos reclamados
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

    return back()->with('success', "Tu solicitud de devolución para el pedido #{$pedido->id} ha sido radicada correctamente.");
}
```

#### 🔍 ¿Qué hace este código?
- **Seguridad**: Comprueba que el usuario sólo pueda radicar reclamos sobre sus propias órdenes de compra.
- Inserta los productos reclamados en `detalle_devolucions` con estado `'Pendiente'` para que el equipo administrativo lo revise en el módulo de devoluciones.
