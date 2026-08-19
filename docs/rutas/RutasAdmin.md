# 👑 Rutas del Panel de Administración

## 📍 Ubicación en Código
`routes/web.php` (Líneas 78 a 153)

---

## 🎯 Propósito General
Conjunto de rutas operativas de PowerNet para gestión de catálogo, almacén, finanzas, ventas y logística. Protegidas por `auth` y verificadas por rol de administrador (`role_id == 1`).

---

## 💻 Definición de Código en `routes/web.php`

```php
Route::middleware('auth')->group(function () {

    // 1. Dashboard Principal & Gestión de Usuarios
    Route::get('/dashboard', [dashboardController::class, 'index'])->name('dashboard');
    Route::patch('/admin/usuarios/{id}/cambiar-rol', [dashboardController::class, 'cambiarRol'])->name('admin.usuarios.cambiarRol');

    // 2. Gestión de Categorías
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::put('/categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.actualizar');
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categorias.eliminar');
    Route::patch('/categorias/{id}/estado', [CategoriaController::class, 'cambiarEstado'])->name('categorias.estado');

    // 3. Gestión de Productos e Imágenes
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.actualizar');
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.eliminar');
    Route::patch('/productos/{id}/estado', [ProductoController::class, 'cambiarEstado'])->name('productos.estado');
    Route::delete('/productos/imagen/{id}', [ProductoController::class, 'eliminarImagen'])->name('productos.imagen.eliminar');

    // 4. Gestión de Ofertas y Promociones
    Route::get('/ofertas', [OfertaController::class, 'index'])->name('ofertas.index');
    Route::post('/ofertas', [OfertaController::class, 'store'])->name('ofertas.store');
    Route::put('/ofertas/{id}', [OfertaController::class, 'update'])->name('ofertas.actualizar');
    Route::delete('/ofertas/{id}', [OfertaController::class, 'destroy'])->name('ofertas.eliminar');
    Route::patch('/ofertas/{id}/estado', [OfertaController::class, 'cambiarEstado'])->name('ofertas.estado');

    // 5. Gestión de Proveedores
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');    
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.actualizar');
    Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.eliminar');
    Route::patch('/proveedores/{id}/estado', [ProveedorController::class, 'cambiarEstado'])->name('proveedores.estado');

    // 6. Configuración de Métodos de Pago
    Route::get('/admin/metodos-pago', [MetodoPagoController::class, 'index'])->name('metodospago.index');
    Route::post('/admin/metodos-pago', [MetodoPagoController::class, 'store'])->name('metodospago.store');
    Route::put('/admin/metodos-pago/{id}', [MetodoPagoController::class, 'update'])->name('metodospago.actualizar');
    Route::delete('/admin/metodos-pago/{id}', [MetodoPagoController::class, 'destroy'])->name('metodospago.eliminar');
    Route::patch('/admin/metodos-pago/{id}/estado', [MetodoPagoController::class, 'cambiarEstado'])->name('metodospago.estado');

    // 7. Gestión de Pedidos
    Route::get('/admin/pedidos', [AdminPedidoController::class, 'index'])->name('admin.pedidos.index');
    Route::patch('/admin/pedidos/{id}/estado', [AdminPedidoController::class, 'updateEstado'])->name('admin.pedidos.estado');
    Route::delete('/admin/pedidos/{id}', [AdminPedidoController::class, 'destroy'])->name('admin.pedidos.eliminar');

    // 8. Gestión de Envíos y Logística
    Route::get('/admin/envios', [AdminEnvioController::class, 'index'])->name('admin.envios.index');
    Route::put('/admin/envios/{id}', [AdminEnvioController::class, 'update'])->name('admin.envios.update');
    Route::patch('/admin/envios/{id}/estado', [AdminEnvioController::class, 'cambiarEstado'])->name('admin.envios.estado');
    Route::delete('/admin/envios/{id}', [AdminEnvioController::class, 'destroy'])->name('admin.envios.destroy');

    // 9. Control de Pagos y Finanzas
    Route::get('/admin/pagos', [AdminPagoController::class, 'index'])->name('admin.pagos.index');
    Route::put('/admin/pagos/{id}', [AdminPagoController::class, 'update'])->name('admin.pagos.update');
    Route::patch('/admin/pagos/{id}/estado', [AdminPagoController::class, 'cambiarEstado'])->name('admin.pagos.estado');
    Route::delete('/admin/pagos/{id}', [AdminPagoController::class, 'destroy'])->name('admin.pagos.destroy');

    // 10. Ventas y Reportes
    Route::get('/admin/ventas', [AdminVentaController::class, 'index'])->name('admin.ventas.index');

    // 11. Gestión de Devoluciones y Garantías
    Route::get('/admin/devoluciones', [AdminDevolucionController::class, 'index'])->name('admin.devoluciones.index');
    Route::post('/admin/devoluciones', [AdminDevolucionController::class, 'store'])->name('admin.devoluciones.store');
    Route::patch('/admin/devoluciones/{id}/estado', [AdminDevolucionController::class, 'updateEstado'])->name('admin.devoluciones.estado');
    Route::delete('/admin/devoluciones/{id}', [AdminDevolucionController::class, 'destroy'])->name('admin.devoluciones.destroy');

    // 12. Control de Inventario y Kardex
    Route::get('/admin/inventario', [AdminInventarioController::class, 'index'])->name('admin.inventario.index');
    Route::post('/admin/inventario/ajuste', [AdminInventarioController::class, 'ajustarStock'])->name('admin.inventario.ajuste');
    Route::put('/admin/inventario/{id}/precios', [AdminInventarioController::class, 'actualizarPrecios'])->name('admin.inventario.precios');
    Route::get('/admin/inventario/movimientos', [AdminInventarioController::class, 'movimientos'])->name('admin.inventario.movimientos');

});
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar estas rutas en una sustentación?
> *"Las rutas administrativas estructuran las operaciones internas del negocio siguiendo estándares RESTful: `GET` para consultar listados, `POST` para registrar nuevos recursos, `PUT`/`PATCH` para actualizaciones de estado o precios, y `DELETE` para bajas lógicas o físicas. Cada ruta está nombrada con prefijos claros (`admin.*`, `productos.*`, `categorias.*`) para facilitar la navegación desde las vistas Blade con la función `route('nombre.ruta')`."*

### 2. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **Un cliente accede indebidamente al Dashboard**: El controlador `dashboardController` valida que `auth()->user()->role_id == 1`. Si no lo es, lo expulsa automáticamente a la tienda (`route('tienda.inicio')`).
- **Error 419 `Page Expired` en formularios POST/PUT**: Falta incluir el token de seguridad `@csrf` dentro de la etiqueta `<form>` en el archivo Blade.
