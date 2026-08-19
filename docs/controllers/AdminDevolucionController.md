# 🔄 AdminDevolucionController

## 📍 Ubicación
`app/Http/Controllers/AdminDevolucionController.php`

---

## 🎯 Propósito General
Administra el proceso de garantías y devoluciones de PowerNet. Permite a los administradores evaluar los reclamos de los clientes, aprobar o rechazar devoluciones y, en caso de aprobación, reincorporar automáticamente las unidades devueltas al inventario.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\DetalleDevolucion;
use App\Models\Devolucion;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `store(Request $request)` - Creación y Reposición Automática de Stock

#### 💻 Código Clave:
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'pedido_id' => 'required|exists:pedidos,id',
        'motivo' => 'required|string|max:500',
        'monto_devolucion' => 'required|numeric|min:0',
        'estado' => 'required|string|max:50',
    ]);

    // 1. Crear cabecera de la devolución
    $devolucion = Devolucion::create([
        'pedido_id' => $validated['pedido_id'],
        'fecha_devolucion' => now(),
        'motivo' => $validated['motivo'],
        'monto_devolucion' => $validated['monto_devolucion'],
        'estado' => $validated['estado'],
        'motivo_rechazo' => null,
    ]);

    // 2. Registrar detalle de productos y reintegrar stock si fue aprobada
    $pedido = Pedido::with('detalles.producto')->find($validated['pedido_id']);
    if ($pedido && $pedido->detalles) {
        foreach ($pedido->detalles as $detalle) {
            DetalleDevolucion::create([
                'devolucione_id' => $devolucion->id,
                'producto_id' => $detalle->producto_id,
                'cantidad' => $detalle->cantidad,
                'motivo' => $validated['motivo'],
            ]);

            // Reincorporar stock automáticamente a bodega
            if ($validated['estado'] === 'Aprobada' || $validated['estado'] === 'Completada') {
                if ($detalle->producto) {
                    $detalle->producto->increment('stock', $detalle->cantidad);
                }
            }
        }
    }

    return redirect()->route('admin.devoluciones.index')->with('success', "Devolución registrada correctamente.");
}
```

#### 🔍 ¿Qué hace este código?
- **`$detalle->producto->increment('stock', $detalle->cantidad)`**: Suma de forma atómica y segura las unidades físicas devueltas al inventario disponible para que puedan ser vendidas nuevamente.
- **Relación `DetalleDevolucion`**: Guarda el registro específico de cada producto, la cantidad y el motivo de garantía.

---

### 2. `updateEstado(Request $request, $id)` - Aprobación / Rechazo con Motivo

#### 💻 Código Clave:
```php
public function updateEstado(Request $request, $id)
{
    $devolucion = Devolucion::with('detalles.producto')->findOrFail($id);

    $validated = $request->validate([
        'estado' => 'required|string|max:50',
        'motivo_rechazo' => 'nullable|string|max:500',
    ]);

    $estadoAnterior = $devolucion->estado;
    $devolucion->estado = $validated['estado'];

    if ($validated['estado'] === 'Rechazada') {
        $devolucion->motivo_rechazo = $validated['motivo_rechazo'] ?? 'No cumple con las políticas de garantía.';
    }

    // Si pasa a Aprobada/Completada desde Pendiente, suma el stock
    if (in_array($validated['estado'], ['Aprobada', 'Completada']) && !in_array($estadoAnterior, ['Aprobada', 'Completada'])) {
        foreach ($devolucion->detalles as $detalle) {
            if ($detalle->producto) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }
        }
    }

    $devolucion->save();
    return redirect()->route('admin.devoluciones.index')->with('success', "Devolución actualizada.");
}
```

#### 🔍 ¿Qué hace este código?
- Evita duplicar el stock si el administrador vuelve a guardar una devolución ya aprobada previamente, comprobando `$estadoAnterior`.
- Registra el motivo formal de rechazo para que el cliente conozca la razón en su panel.
