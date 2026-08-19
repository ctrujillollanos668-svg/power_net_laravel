# 📋 AdminPedidoController

## 📍 Ubicación
`app/Http/Controllers/AdminPedidoController.php`

---

## 🎯 Propósito General
Administra el ciclo de vida de los pedidos recibidos en PowerNet. Permite a los administradores filtrar órdenes por estado de preparación, verificar el pago, gestionar los despachos y actualizar los estados en cascada.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Envio;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index(Request $request)` - Listado de Pedidos y Filtros

#### 💻 Código Clave:
```php
public function index(Request $request)
{
    $query = Pedido::with([
        'cliente.persona',
        'detalles.producto.imagenes',
        'envio',
        'pago'
    ])->latest();

    // 1. Filtro por Estado de Pedido
    if ($request->filled('estado') && $request->estado !== 'todos') {
        $query->where('estado_pedido', $request->estado);
    }

    // 2. Filtro por Estado de Pago
    if ($request->filled('pago') && $request->pago !== 'todos') {
        $query->whereHas('pago', function ($q) use ($request) {
            $q->where('estado_pago', $request->pago);
        });
    }

    // 3. Búsqueda por ID de pedido, cliente o folio de factura
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

    // Métricas en cabecera
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

#### 🔍 ¿Qué hace este código?
- **`whereHas('pago', ...)` y `whereHas('cliente.persona', ...)`**: Permite buscar pedidos utilizando datos de tablas relacionadas (como el número de factura o nombre del comprador).
- **`latest()`**: Muestra siempre primero los pedidos más recientes para dar atención inmediata a las nuevas compras.

---

### 2. `updateEstado(Request $request, $id)` - Actualización Integral del Pedido

#### 💻 Código Clave:
```php
public function updateEstado(Request $request, $id)
{
    $pedido = Pedido::with(['envio', 'pago'])->findOrFail($id);

    $validated = $request->validate([
        'estado_pedido' => 'required|string|max:50',
        'estado_pago' => 'nullable|string|max:50',
        'empresa_envios' => 'nullable|string|max:100',
        'direccion_envio' => 'nullable|string|max:255',
    ]);

    // 1. Actualizar estado del pedido
    $pedido->estado_pedido = $validated['estado_pedido'];
    $pedido->save();

    // 2. Actualizar estado del pago si se modificó
    if ($pedido->pago && !empty($validated['estado_pago'])) {
        $pedido->pago->estado_pago = $validated['estado_pago'];
        $pedido->pago->save();
    }

    // 3. Sincronizar transportadora y dirección en el envío
    if ($pedido->envio) {
        if (!empty($validated['empresa_envios'])) {
            $pedido->envio->empresa_envios = $validated['empresa_envios'];
        }
        if (!empty($validated['direccion_envio'])) {
            $pedido->envio->direccion_envio = $validated['direccion_envio'];
        }
        $pedido->envio->estado = $validated['estado_pedido'];
        $pedido->envio->save();
    }

    return redirect()->route('admin.pedidos.index')
        ->with('success', "Pedido #{$pedido->id} actualizado correctamente.");
}
```

#### 🔍 ¿Qué hace este código?
- Centraliza en un solo formulario la actualización del estado general (`$pedido`), del pago (`$pedido->pago`) y de la logística (`$pedido->envio`), evitando inconsistencias de datos entre módulos.
