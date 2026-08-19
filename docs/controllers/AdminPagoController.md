# 💳 AdminPagoController

## 📍 Ubicación
`app/Http/Controllers/AdminPagoController.php`

---

## 🎯 Propósito General
Centraliza el control financiero y la conciliación de pagos de PowerNet. Permite a los administradores revisar los comprobantes de pago subidos por los clientes, validar transacciones (Nequi, Bancolombia, Tarjeta, etc.), aprobar/rechazar pagos y sincronizar el estado del pedido.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index(Request $request)` - Conciliación Financiera y Filtros

#### 💻 Código Clave:
```php
public function index(Request $request)
{
    $query = Pago::with([
        'pedido.cliente.persona',
        'pedido.detalles.producto.imagenes',
        'pedido.envio'
    ])->latest('created_at');

    // Filtro por Estado de Pago
    if ($request->filled('estado') && $request->estado !== 'todos') {
        $query->where('estado_pago', $request->estado);
    }

    // Búsqueda por Factura, Pedido o Cliente
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

    // Métricas Financieras
    $totalRecaudado = Pago::where('estado_pago', 'Aprobado')->sum('monto');
    $totalPendiente = Pago::whereIn('estado_pago', ['Pendiente', 'Pendiente al entregar'])->sum('monto');
    $transaccionesAprobadas = Pago::where('estado_pago', 'Aprobado')->count();

    return view('admin.pagos.index', compact(
        'pagos', 'totalRecaudado', 'totalPendiente', 'transaccionesAprobadas'
    ));
}
```

#### 🔍 ¿Qué hace este código?
- Separa claramente el dinero efectivamente recaudado (`estado_pago = 'Aprobado'`) del dinero pendiente de pago o confirmación (`'Pendiente al entregar'`), permitiendo un arqueo de caja exacto.

---

### 2. `update(Request $request, $id)` - Aprobación y Reactivación de Pedidos

#### 💻 Código Clave:
```php
public function update(Request $request, $id)
{
    $pago = Pago::with('pedido')->findOrFail($id);

    $validated = $request->validate([
        'estado_pago' => 'required|string|max:50',
        'metodo_pago' => 'required|string|max:50',
        'factura' => 'required|string|max:100',
        'monto' => 'required|numeric|min:0',
    ]);

    $pago->estado_pago = $validated['estado_pago'];
    $pago->metodo_pago = $validated['metodo_pago'];
    $pago->factura = $validated['factura'];
    $pago->monto = $validated['monto'];

    // Si el pago es Aprobado, guarda la fecha/hora de confirmación
    if ($validated['estado_pago'] === 'Aprobado' && empty($pago->fecha_pago)) {
        $pago->fecha_pago = now();
    }

    $pago->save();

    // Si el pedido estaba cancelado o retenido, lo reactiva automáticamente
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

#### 🔍 ¿Qué hace este código?
- **`$pago->fecha_pago = now()`**: Guarda automáticamente el instante exacto en que el administrador confirmó la recepción del dinero.
- **Reactivación Automática**: Si el cliente completó el pago tras una cancelación previa, cambia el estado del pedido a `'En preparación'` para que bodega comience a alistarlo.
