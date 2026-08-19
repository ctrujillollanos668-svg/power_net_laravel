# 📋 AdminPedidoController

## 📍 Ubicación
`app/Http/Controllers/AdminPedidoController.php`

---

## 🎯 Propósito General
Administra el ciclo de vida y la trazabilidad de los pedidos recibidos en PowerNet. Permite a los administradores monitorear compras, filtrar por estados de preparación o pago, y ejecutar la actualización coordinada entre **Pedido**, **Pago** y **Envío**.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Envio;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código con Trazabilidad

### 1. `index(Request $request)` - Listado y Trazabilidad de Búsqueda

```php
public function index(Request $request)
{
    // TRAZABILIDAD: Conecta el Pedido con Cliente -> Persona, Detalles -> Producto, Envío y Pago
    $query = Pedido::with([
        'cliente.persona',
        'detalles.producto.imagenes',
        'envio',
        'pago'
    ])->latest();

    // Filtro por Estado del Pedido
    if ($request->filled('estado') && $request->estado !== 'todos') {
        $query->where('estado_pedido', $request->estado);
    }

    // Filtro por Estado del Pago
    if ($request->filled('pago') && $request->pago !== 'todos') {
        $query->whereHas('pago', function ($q) use ($request) {
            $q->where('estado_pago', $request->pago);
        });
    }

    // Búsqueda multi-tabla (Cruza Pedido con Persona y Factura)
    if ($request->filled('q')) {
        $q = $request->q;
        $query->where(function ($sub) use ($q) {
            $sub->where('id', 'like', "%{$q}%")
                ->orWhereHas('cliente.persona', function ($p) use ($q) {
                    $p->where('nombre_persona', 'like', "%{$q}%")
                      ->orWhere('telefono', 'like', "%{$q}%")
                      ->orWhere('documento', 'like', "%{$q}%");
                })
                ->orWhereHas('pago', function ($pg) use ($q) {
                    $pg->where('factura', 'like', "%{$q}%");
                });
        });
    }

    $pedidos = $query->paginate(10)->withQueryString();

    // Métricas en tiempo real de la cabecera
    $totalPedidos = Pedido::count();
    $pedidosPendientes = Pedido::where('estado_pedido', 'En preparación')->count();
    $pedidosEnviados = Pedido::where('estado_pedido', 'Enviado')->count();
    $pedidosEntregados = Pedido::where('estado_pedido', 'Entregado')->count();
    $totalVentas = Pedido::where('estado_pedido', '!=', 'Cancelado')->sum('total_pedido');

    return view('admin.pedidos.index', compact(
        'pedidos', 'totalPedidos', 'pedidosPendientes',
        'pedidosEnviados', 'pedidosEntregados', 'totalVentas'
    ));
}
```

---

### 2. `updateEstado(Request $request, $id)` - Sincronización en Cascada

```php
public function updateEstado(Request $request, $id)
{
    // 1. Carga el pedido junto a sus modelos hijos (envío y pago)
    $pedido = Pedido::with(['envio', 'pago'])->findOrFail($id);

    $validated = $request->validate([
        'estado_pedido' => 'required|string|max:50',
        'estado_pago' => 'nullable|string|max:50',
        'empresa_envios' => 'nullable|string|max:100',
        'direccion_envio' => 'nullable|string|max:255',
    ]);

    // 2. TRAZABILIDAD PASO 1: Actualizar el estado en la tabla 'pedidos'
    $pedido->estado_pedido = $validated['estado_pedido'];
    $pedido->save();

    // 3. TRAZABILIDAD PASO 2: Sincronizar el estado en la tabla 'pagos'
    if ($pedido->pago && !empty($validated['estado_pago'])) {
        $pedido->pago->estado_pago = $validated['estado_pago'];
        $pedido->pago->save();
    }

    // 4. TRAZABILIDAD PASO 3: Sincronizar transportadora, dirección y estado en la tabla 'envios'
    if ($pedido->envio) {
        if (!empty($validated['empresa_envios'])) {
            $pedido->envio->empresa_envios = $validated['empresa_envios'];
        }
        if (!empty($validated['direccion_envio'])) {
            $pedido->envio->direccion_envio = $validated['direccion_envio'];
        }
        // El estado del envío adopta el estado del pedido (ej. 'Enviado', 'Entregado')
        $pedido->envio->estado = $validated['estado_pedido'];
        $pedido->envio->save();
    }

    return redirect()->route('admin.pedidos.index')
        ->with('success', "Pedido #{$pedido->id} actualizado correctamente.");
}
```

---

## 🔄 Trazabilidad del Flujo de Datos (Data Flow)

```
[Usuario / Checkout] 
       ↓ 
1. Registra 'pedidos' (ID Pedido, Total, Estado inicial 'En preparación')
       ↓
2. Desglosa 'detalle_pedidos' (Items, Cantidad, Precio Unitario)
       ↓
3. Genera 'pagos' (Folio Factura, Monto, Estado Pago 'Pendiente' / 'Aprobado')
       ↓
4. Genera 'envios' (Dirección, Transportadora, Estado Envío)
       ↓
[AdminPedidoController::updateEstado]
       ↓ (Actualiza coordinadamente los 3 registros para que no haya inconsistencias)
   [pedidos.estado_pedido] <---> [pagos.estado_pago] <---> [envios.estado]
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este controlador en una sustentación?
> *"El `AdminPedidoController` garantiza la consistencia del negocio mediante actualización en cascada: cuando un administrador cambia el estado de una orden a 'Enviado', el método `updateEstado` no solo muta la tabla `pedidos`, sino que propaga simultáneamente los datos hacia la tabla `envios` (asignando transportadora y estado) y la tabla `pagos` (confirmando el recaudo). Esto evita que un pedido aparezca como entregado mientras su envío sigue en preparación."*

### 2. Tablas y campos afectados en MySQL:
- **`pedidos`**: Campo `estado_pedido` (`'En preparación'`, `'Enviado'`, `'Entregado'`, `'Cancelado'`).
- **`pagos`**: Campo `estado_pago` (`'Pendiente'`, `'Aprobado'`, `'Rechazado'`).
- **`envios`**: Campos `empresa_envios`, `direccion_envio`, `estado`.

### 3. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **Error: "Attempt to read property 'estado_pago' on null"**: Ocurre si un pedido antiguo en la base de datos no tiene creado un registro en la tabla `pagos`. 
  - *Solución:* El código ya incluye la validación `if ($pedido->pago)`, protegiendo la ejecución. Para reparar en BD, crea el registro en `pagos` con `pedido_id = $id`.
- **El cliente no ve el cambio de estado en 'Mis Pedidos'**: Verifica que en la tabla `pedidos` el campo `estado_pedido` coincida con los textos esperados (`En preparación`, `Enviado`, `Entregado`).
- **El envío no actualiza la transportadora**: Asegúrate de que el campo `empresa_envios` no venga vacío y esté permitido en la regla de validación `'nullable|string|max:100'`.
