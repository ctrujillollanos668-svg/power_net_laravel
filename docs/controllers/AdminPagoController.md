# 💳 AdminPagoController

## 📍 Ubicación
`app/Http/Controllers/AdminPagoController.php`

---

## 🎯 Propósito General
Centraliza el control financiero, conciliación de pagos y auditoría de transacciones recibidas. Permite validar los métodos de pago (transferencias bancarias, tarjetas, contra entrega), actualizar estados de pago y reactivar pedidos asociados.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código con Trazabilidad

### 1. `index(Request $request)` - Conciliación Financiera y Filtros

```php
public function index(Request $request)
{
    // TRAZABILIDAD: Carga el Pago junto a Pedido -> Cliente -> Persona, Detalles y Envío
    $query = Pago::with([
        'pedido.cliente.persona',
        'pedido.detalles.producto.imagenes',
        'pedido.envio'
    ])->latest('created_at');

    // Filtro por Estado de Pago
    if ($request->filled('estado') && $request->estado !== 'todos') {
        $query->where('estado_pago', $request->estado);
    }

    // Búsqueda multi-tabla por Factura, Pedido o Cliente
    if ($request->filled('q')) {
        $q = $request->q;
        $query->where(function ($sub) use ($q) {
            $sub->where('factura', 'like', "%{$q}%")
                ->orWhere('metodo_pago', 'like', "%{$q}%")
                ->orWhere('pedido_id', 'like', "%{$q}%")
                ->orWhereHas('pedido.cliente.persona', function ($p) use ($q) {
                    $p->where('nombre_persona', 'like', "%{$q}%")
                      ->orWhere('telefono', 'like', "%{$q}%")
                      ->orWhere('documento', 'like', "%{$q}%");
                });
        });
    }

    $pagos = $query->paginate(10)->withQueryString();

    // Métricas Financieras en tiempo real
    $totalRecaudado = Pago::where('estado_pago', 'Aprobado')->sum('monto');
    $totalPendiente = Pago::whereIn('estado_pago', ['Pendiente', 'Pendiente al entregar'])->sum('monto');
    $transaccionesAprobadas = Pago::where('estado_pago', 'Aprobado')->count();

    return view('admin.pagos.index', compact(
        'pagos', 'totalRecaudado', 'totalPendiente', 'transaccionesAprobadas'
    ));
}
```

---

### 2. `update(Request $request, $id)` - Aprobación y Reactivación de Pedidos

```php
public function update(Request $request, $id)
{
    // 1. Carga el pago con su pedido padre relacionado
    $pago = Pago::with('pedido')->findOrFail($id);

    $validated = $request->validate([
        'estado_pago' => 'required|string|max:50',
        'metodo_pago' => 'required|string|max:50',
        'factura' => 'required|string|max:100',
        'monto' => 'required|numeric|min:0',
    ]);

    // 2. TRAZABILIDAD: Actualizar datos propios del pago
    $pago->estado_pago = $validated['estado_pago'];
    $pago->metodo_pago = $validated['metodo_pago'];
    $pago->factura = $validated['factura'];
    $pago->monto = $validated['monto'];

    // Si el pago es Aprobado, guarda la fecha/hora exacta de confirmación
    if ($validated['estado_pago'] === 'Aprobado' && empty($pago->fecha_pago)) {
        $pago->fecha_pago = now();
    }

    $pago->save();

    // 3. TRAZABILIDAD EN CASCADA: Si el pedido estaba cancelado o retenido, lo reactiva
    if ($pago->pedido && $validated['estado_pago'] === 'Aprobado') {
        if ($pago->pedido->estado_pedido === 'Cancelado') {
            $pago->pedido->estado_pedido = 'En preparación';
            $pago->pedido->save();
        }
    }

    return redirect()->route('admin.pagos.index')
        ->with('success', "Pago de la factura {$pago->factura} actualizado exitosamente.");
}
```

---

## 🔄 Trazabilidad del Flujo de Datos (Data Flow)

```
[Cliente reporta comprobante / pago] 
       ↓ 
[Tabla 'pagos'] Guarda: monto, metodo_pago, factura, estado_pago ('Pendiente')
       ↓
[AdminPagoController::update] 
       ↓ (Valida dinero recibido en cuenta bancaria)
Cambia 'estado_pago' a 'Aprobado' y asigna 'fecha_pago = now()'
       ↓
[Sincronización hacia 'pedidos']
Si el pedido estaba 'Cancelado', lo pasa a 'En preparación' para que bodega despache
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este controlador en una sustentación?
> *"El `AdminPagoController` gestiona la conciliación contable de PowerNet. Separa el dinero recaudado de las cuentas por cobrar, y al momento de aprobar un pago, no solo estampa la marca de tiempo `fecha_pago = now()`, sino que reactiva el estado del pedido a 'En preparación', desbloqueando la orden para el área de despacho."*

### 2. Tablas y campos afectados en MySQL:
- **`pagos`**: `estado_pago`, `metodo_pago`, `factura`, `monto`, `fecha_pago`.
- **`pedidos`**: `estado_pedido`.

### 3. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **Error: "Monto total no coincide con la orden"**: Revisa si el cliente tenía descuento de oferta o costo de flete sumado en la columna `total_pedido` de la tabla `pedidos`.
- **La fecha de pago no se guarda**: Verifica que el campo `fecha_pago` esté definido en el `$fillable` del modelo `Pago` o en la migración como `timestamp/dateTime` nullable.
