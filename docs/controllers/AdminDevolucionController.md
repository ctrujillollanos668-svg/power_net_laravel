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

## 🛠️ Explicación Detallada del Código con Trazabilidad

### 1. `store(Request $request)` - Registro y Reposición Automática de Stock

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'pedido_id' => 'required|exists:pedidos,id',
        'motivo' => 'required|string|max:500',
        'monto_devolucion' => 'required|numeric|min:0',
        'estado' => 'required|string|max:50',
    ]);

    // 1. TRAZABILIDAD: Crea la cabecera del reclamo en la tabla 'devoluciones'
    $devolucion = Devolucion::create([
        'pedido_id' => $validated['pedido_id'],
        'fecha_devolucion' => now(),
        'motivo' => $validated['motivo'],
        'monto_devolucion' => $validated['monto_devolucion'],
        'estado' => $validated['estado'],
        'motivo_rechazo' => null,
    ]);

    // 2. TRAZABILIDAD EN CASCADA: Copia los ítems del pedido a 'detalle_devolucions'
    $pedido = Pedido::with('detalles.producto')->find($validated['pedido_id']);
    if ($pedido && $pedido->detalles) {
        foreach ($pedido->detalles as $detalle) {
            DetalleDevolucion::create([
                'devolucione_id' => $devolucion->id,
                'producto_id' => $detalle->producto_id,
                'cantidad' => $detalle->cantidad,
                'motivo' => $validated['motivo'],
            ]);

            // 3. TRAZABILIDAD DE STOCK: Si la devolución es aprobada, suma el stock en 'productos'
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

---

### 2. `updateEstado(Request $request, $id)` - Aprobación / Rechazo con Motivo

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

    // TRAZABILIDAD DE STOCK: Solo incrementa si pasa por primera vez a Aprobada/Completada
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

---

## 🔄 Trazabilidad del Flujo de Datos (Data Flow)

```
[Cliente o Admin radica Devolución]
       ↓
[Tabla 'devoluciones'] Genera ID Devolución, Pedido ID, Monto y Estado 'Pendiente'
       ↓
[Tabla 'detalle_devolucions'] Registra cada producto y cantidad reclamada
       ↓
[Evaluación Admin: updateEstado -> 'Aprobada']
       ↓
[Tabla 'productos'] Ejecuta '$producto->increment('stock', cantidad)' reponiendo existencias
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este controlador en una sustentación?
> *"El `AdminDevolucionController` automatiza la reversión logística. Cuando una devolución pasa a estado 'Aprobada', el controlador itera sobre `detalle_devolucions` y ejecuta un incremento atómico `$producto->increment('stock', $cantidad)` en la tabla `productos`, devolviendo la mercancía al inventario disponible sin requerir intervención manual en almacén."*

### 2. Tablas y campos afectados en MySQL:
- **`devoluciones`**: `pedido_id`, `fecha_devolucion`, `motivo`, `monto_devolucion`, `estado`, `motivo_rechazo`.
- **`detalle_devolucions`**: `devolucione_id`, `producto_id`, `cantidad`, `motivo`.
- **`productos`**: Campo `stock` (incrementado).

### 3. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **El stock se sumó dos veces por error**: Esto ocurre si no se valida `$estadoAnterior`. El código actual ya previene la duplicidad validando `!in_array($estadoAnterior, ['Aprobada', 'Completada'])`.
- **Error: "SQLSTATE[23000]: Integrity constraint violation: Column 'pedido_id' cannot be null"**: Ocurre si se intenta registrar una devolución sin seleccionar un pedido válido existente en la base de datos.
