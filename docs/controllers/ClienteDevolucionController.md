# 🔄 ClienteDevolucionController

## 📍 Ubicación
`app/Http/Controllers/ClienteDevolucionController.php`

---

## 🎯 Propósito General
Módulo de autoservicio de garantías para el comprador. Permite al cliente autenticado consultar el estado de sus solicitudes previas y radicar nuevos reclamos vinculados a sus compras.

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

### 1. `index()` - Bandeja de Garantías del Cliente

#### 💻 Código Clave:
```php
public function index()
{
    $user = Auth::user();
    $devoluciones = collect();
    $pedidos = collect();

    if ($user && $user->persona_id) {
        $cliente = Cliente::where('persona_id', $user->persona_id)->first();
        if ($cliente) {
            // 1. Obtener historial de devoluciones del cliente
            $devoluciones = Devolucion::whereHas('pedido', fn($q) => $q->where('cliente_id', $cliente->id))
                ->with(['pedido.pago', 'detalles.producto.imagenes'])
                ->latest('fecha_devolucion')
                ->paginate(10);

            // 2. Obtener pedidos aptos para solicitar nueva devolución
            $pedidos = Pedido::where('cliente_id', $cliente->id)
                ->where('estado_pedido', '!=', 'Cancelado')
                ->with('detalles.producto')
                ->latest()
                ->get();
        }
    }

    $totalDevoluciones = $devoluciones->total();
    $pendientes = $devoluciones->where('estado', 'Pendiente')->count();
    $aprobadas = $devoluciones->whereIn('estado', ['Aprobada', 'Completada'])->count();

    return view('cliente.devoluciones.index', compact(
        'devoluciones', 'pedidos', 'totalDevoluciones', 'pendientes', 'aprobadas'
    ));
}
```

#### 🔍 ¿Qué hace este código?
- Carga las devoluciones anteriores y la lista de compras del cliente para alimentar el formulario modal donde el usuario elige el pedido a devolver.

---

### 2. `store(Request $request)` - Radicación de Reclamo

#### 💻 Código Clave:
```php
public function store(Request $request)
{
    $user = Auth::user();
    $cliente = $user && $user->persona_id ? Cliente::where('persona_id', $user->persona_id)->first() : null;

    $validated = $request->validate([
        'pedido_id' => 'required|exists:pedidos,id',
        'motivo_categoria' => 'required|string|max:100',
        'descripcion' => 'required|string|max:1000',
    ]);

    $pedido = Pedido::with('detalles.producto')->findOrFail($validated['pedido_id']);

    // Verifica que el pedido pertenezca al usuario en sesión
    if ($cliente && $pedido->cliente_id !== $cliente->id) {
        return back()->with('error', 'No tienes permiso para solicitar una devolución en este pedido.');
    }

    $motivoCompleto = "[{$validated['motivo_categoria']}] {$validated['descripcion']}";

    $devolucion = Devolucion::create([
        'pedido_id' => $pedido->id,
        'fecha_devolucion' => now(),
        'motivo' => $motivoCompleto,
        'monto_devolucion' => $pedido->total_pedido,
        'estado' => 'Pendiente',
    ]);

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

    return redirect()->route('cliente.devoluciones.index')->with('success', "Tu solicitud para el pedido #{$pedido->id} fue radicada con éxito.");
}
```

#### 🔍 ¿Qué hace este código?
- Valida la pertenencia de la orden y crea los registros correspondientes para que los administradores los atiendan desde el panel administrativo.
