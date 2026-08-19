# 📊 dashboardController

## 📍 Ubicación
`app/Http/Controllers/dashboardController.php`

---

## 🎯 Propósito General
Controla la vista principal del panel de administración. Calcula en tiempo real las métricas financieras y logísticas más importantes (inventario valorizado, pedidos pendientes, ventas totales, productos con stock bajo) y administra los roles de los usuarios del sistema.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index(Request $request)` - Panel de Control y Métricas Globales

Valida el rol de administrador, ejecuta cálculos agregados sobre la base de datos y lista los usuarios registrados.

#### 💻 Código Clave:
```php
public function index(Request $request)
{
    $role = auth()->user()->role_id;

    // 1. Verificación de Seguridad por Rol
    if ($role == 1) {
        // Métricas de Productos e Inventario
        $totalProductos = Producto::count();
        $productosActivos = Producto::where('disponibilidad', 1)->count();
        $productosInactivos = Producto::where('disponibilidad', 0)->count();

        // Cálculo de valorización total del stock en bodega
        $valorInventario = Producto::selectRaw('SUM(stock * precio) as total')->value('total') ?? 0;
        // Alerta de stock crítico
        $productosStockBajo = Producto::where('stock', '<=', 5)->count();

        // Métricas de Ventas y Pedidos
        $totalVentas = Pedido::where('estado_pedido', '!=', 'Cancelado')->sum('total_pedido');
        $totalVentasCount = Pedido::where('estado_pedido', '!=', 'Cancelado')->count();

        $totalPedidos = Pedido::count();
        $pedidosPendientes = Pedido::whereIn('estado_pedido', ['En preparación', 'Pendiente'])->count();

        // Consulta de usuarios con filtro de búsqueda
        $query = User::with('role');
        if ($request->filled('buscar_usuario')) {
            $term = trim($request->buscar_usuario);
            $query->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('id', 'like', "%{$term}%");
            });
        }
        $usuarios = $query->latest('id')->paginate(10)->withQueryString();
        $totalUsuariosCount = User::count();

        return view('admin.dashboard', compact(
            'totalProductos', 'productosActivos', 'productosInactivos',
            'valorInventario', 'productosStockBajo', 'totalVentas',
            'totalVentasCount', 'totalPedidos', 'pedidosPendientes',
            'usuarios', 'totalUsuariosCount'
        ));
    } else {
        // Si no es administrador, redirige a la tienda pública
        return redirect()->route('tienda.inicio');
    }
}
```

#### 🔍 ¿Qué hace este código?
- **`$role == 1`**: Valida que el usuario sea Administrador. Si un Cliente (`role_id == 2`) intenta entrar a `/dashboard`, es redirigido automáticamente a la tienda (`route('tienda.inicio')`).
- **`Producto::selectRaw('SUM(stock * precio) as total')`**: Ejecuta una consulta SQL nativa agregada que multiplica el stock por el precio unitario de cada producto y suma todo para obtener el capital total invertido en almacén.
- **`Pedido::where('estado_pedido', '!=', 'Cancelado')->sum('total_pedido')`**: Suma los ingresos monetarios reales, ignorando las órdenes canceladas.
- **`User::with('role')` y `$query->where(...)`**: Carga los usuarios junto con su rol y permite buscar por nombre, correo o ID con paginación de 10 en 10.

---

### 2. `cambiarRol($id)` - Alternar Rol de Usuario (Admin <-> Cliente)

Permite conceder o revocar permisos de administrador a cualquier usuario registrado.

#### 💻 Código Clave:
```php
public function cambiarRol($id)
{
    $usuario = User::findOrFail($id);

    // Validación de auto-bloqueo
    if (auth()->id() == $usuario->id) {
        return back()->with('error', 'No puedes cambiar tu propio rol de administrador mientras estás en sesión activa.');
    }

    // Alternar: Si es 1 (Admin) pasa a 2 (Cliente), y viceversa
    $nuevoRol = ($usuario->role_id == 1) ? 2 : 1;
    $usuario->role_id = $nuevoRol;
    $usuario->save();

    $nombreRol = ($nuevoRol == 1) ? 'Administrador' : 'Cliente';
    return back()->with('success', "El rol de {$usuario->name} ({$usuario->email}) fue cambiado a {$nombreRol} correctamente.");
}
```

#### 🔍 ¿Qué hace este código?
- **`auth()->id() == $usuario->id`**: Medida de protección esencial que impide que el administrador en sesión se quite su propio rol por error, evitando quedar bloqueado fuera del sistema.
- **Operador Ternario (`$usuario->role_id == 1 ? 2 : 1`)**: Cambia de rol de forma instantánea en un solo clic y guarda los cambios en la base de datos.
