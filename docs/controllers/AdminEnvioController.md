# 🚚 AdminEnvioController

## 📍 Ubicación
`app/Http/Controllers/AdminEnvioController.php`

---

## 🎯 Propósito General
Monitorea y gestiona la logística de distribución y despacho de pedidos en PowerNet. Permite a los administradores asignar transportadoras (Servientrega, Interrapidísimo, Envia, etc.), actualizar costos de flete, modificar direcciones de destino y cambiar estados logísticos (`Pendiente`, `En preparación`, `Enviado`, `Entregado`, `Cancelado`), manteniendo siempre sincronizado el estado del pedido asociado.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Envio;
use App\Models\Pedido;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index(Request $request)` - Listado, Filtros y Métricas de Despacho

Este método obtiene los envíos con sus relaciones, aplica filtros dinámicos y calcula las tarjetas de métricas en la cabecera.

#### 💻 Código Clave:
```php
public function index(Request $request)
{
    // Carga impaciente (Eager Loading) de relaciones anidadas
    $query = Envio::with([
        'pedido.cliente.persona',
        'pedido.detalles.producto.imagenes',
        'pedido.pago'
    ])->latest('fecha_hora');

    // 1. Filtro por Estado de Envío
    if ($request->filled('estado') && $request->estado !== 'todos') {
        $query->where('estado', $request->estado);
    }

    // 2. Filtro por Transportadora
    if ($request->filled('empresa') && $request->empresa !== 'todos') {
        $query->where('empresa_envios', 'like', "%{$request->empresa}%");
    }

    // 3. Búsqueda por ID, # Pedido, Cliente, Dirección o Teléfono
    if ($request->filled('q')) {
        $q = $request->q;
        $query->where(function ($sub) use ($q) {
            $sub->where('id', 'like', "%{$q}%")
                ->orWhere('direccion_envio', 'like', "%{$q}%")
                ->orWhere('empresa_envios', 'like', "%{$q}%")
                ->orWhere('pedido_id', 'like', "%{$q}%")
                ->orWhereHas('pedido.cliente.persona', function ($p) use ($q) {
                    $p->where('nombre_persona', 'like', "%{$q}%")
                      ->orWhere('telefono', 'like', "%{$q}%")
                      ->orWhere('documento', 'like', "%{$q}%");
                });
        });
    }

    $envios = $query->paginate(10)->withQueryString();

    // Métricas en tiempo real
    $totalEnvios = Envio::count();
    $enviosPendientes = Envio::whereIn('estado', ['Pendiente', 'En preparación'])->count();
    $enviosEnCamino = Envio::whereIn('estado', ['Enviado', 'En camino', 'En tránsito'])->count();
    $enviosEntregados = Envio::where('estado', 'Entregado')->count();

    return view('admin.envios.index', compact(
        'envios', 'totalEnvios', 'enviosPendientes', 'enviosEnCamino', 'enviosEntregados'
    ));
}
```

#### 🔍 ¿Qué hace este código?
- **`Envio::with([...])`**: Realiza *Eager Loading* para traer en una sola consulta SQL optimizada el pedido, cliente, persona, productos, imágenes y pago, evitando el problema de rendimiento $N+1$.
- **`whereHas('pedido.cliente.persona', ...)`**: Permite buscar envíos por datos del cliente final (nombre, teléfono o cédula) cruzando 3 tablas relacionadas.
- **`paginate(10)->withQueryString()`**: Divide los resultados en páginas de 10 registros y conserva los filtros activos en la URL al cambiar de página.
- **Cálculo de Contadores**: Utiliza `whereIn()` y `count()` para mostrar las tarjetas superiores (Total, Pendientes, En Camino, Entregados).

---

### 2. `update(Request $request, $id)` - Actualización y Sincronización con Pedido

Actualiza los datos de la transportadora, dirección de entrega y sincroniza el estado con el pedido principal.

#### 💻 Código Clave:
```php
public function update(Request $request, $id)
{
    $envio = Envio::with('pedido')->findOrFail($id);

    $validated = $request->validate([
        'empresa_envios' => 'required|string|max:100',
        'estado' => 'required|string|max:50',
        'costo' => 'nullable|numeric|min:0',
        'direccion_envio' => 'required|string|max:255',
    ]);

    $envio->empresa_envios = $validated['empresa_envios'];
    $envio->estado = $validated['estado'];
    if (isset($validated['costo'])) {
        $envio->costo = $validated['costo'];
    }
    $envio->direccion_envio = $validated['direccion_envio'];
    $envio->save();

    // Sincronización bidireccional con el Pedido
    if ($envio->pedido) {
        $envio->pedido->estado_pedido = $validated['estado'];
        $envio->pedido->save();
    }

    return redirect()->route('admin.envios.index')->with('success', "Envío #{$envio->id} actualizado exitosamente.");
}
```

#### 🔍 ¿Qué hace este código?
- **`findOrFail($id)`**: Busca el envío por su ID; si no existe, lanza un error 404 de manera segura.
- **`$request->validate([...])`**: Asegura que los campos cumplan con tipos y tamaños requeridos antes de tocar la base de datos.
- **Sincronización en Cascada (`$envio->pedido->estado_pedido = ...`)**: Al cambiar el estado del envío (por ejemplo a `"Enviado"` o `"Entregado"`), actualiza inmediatamente el `estado_pedido` del modelo `Pedido` asociado para que el cliente lo vea reflejado en su historial.

---

### 3. `cambiarEstado(Request $request, $id)` - Cambio Rápido de Estado

Permite cambiar el estado del envío directamente desde botones rápidos o selectores de la tabla.

#### 💻 Código Clave:
```php
public function cambiarEstado(Request $request, $id)
{
    $envio = Envio::with('pedido')->findOrFail($id);

    $request->validate([
        'estado' => 'required|string|max:50'
    ]);

    $envio->estado = $request->estado;
    $envio->save();

    if ($envio->pedido) {
        $envio->pedido->estado_pedido = $request->estado;
        $envio->pedido->save();
    }

    return redirect()->route('admin.envios.index')->with('success', "Estado del envío #{$envio->id} cambiado a {$request->estado}.");
}
```

#### 🔍 ¿Qué hace este código?
- Ejecuta una actualización ágil del estado de entrega sin necesidad de revalidar toda la información de dirección o transportadora.

---

### 4. `destroy($id)` - Cancelación Logística

En lugar de borrar físicamente el registro (lo cual rompería la integridad referencial del pedido), aplica una cancelación lógica.

#### 💻 Código Clave:
```php
public function destroy($id)
{
    $envio = Envio::with('pedido')->findOrFail($id);
    $envio->estado = 'Cancelado';
    $envio->save();

    if ($envio->pedido) {
        $envio->pedido->estado_pedido = 'Cancelado';
        $envio->pedido->save();
    }

    return redirect()->route('admin.envios.index')->with('success', "Envío #{$envio->id} cancelado exitosamente.");
}
```

#### 🔍 ¿Qué hace este código?
- Marca tanto el envío como el pedido con estado `'Cancelado'`, preservando el historial para auditoría contable.
