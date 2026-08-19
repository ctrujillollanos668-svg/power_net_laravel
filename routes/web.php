<?php

use App\Http\Controllers\dashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\Imagen_ProductoController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\AdminPedidoController;
use App\Http\Controllers\AdminEnvioController;
use App\Http\Controllers\AdminPagoController;
use App\Http\Controllers\AdminVentaController;
use App\Http\Controllers\AdminDevolucionController;
use App\Http\Controllers\AdminInventarioController;
use App\Http\Controllers\ClienteDevolucionController;
use App\Http\Controllers\ClienteMetodoPagoController;

//1. RUTAS DEL CLIENTE / TIENDA

// --- Tienda y Catálogo (Públicas) ---
Route::get('/', [TiendaController::class, 'index'])->name('tienda.inicio');
Route::get('/catalogo', [TiendaController::class, 'catalogo'])->name('tienda.catalogo');
Route::get('/catalogo/categoria/{id}', [TiendaController::class, 'porCategoria'])->name('tienda.categoria');
Route::get('/producto/{id}', [TiendaController::class, 'show'])->name('tienda.detalle');
Route::get('/promociones', [TiendaController::class, 'ofertas'])->name('tienda.ofertas');
Route::get('/buscar', [TiendaController::class, 'buscar'])->name('tienda.buscar');

// --- Carrito de Compras (Público / Sesión) ---
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::post('/carrito/actualizar/{id}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
Route::delete('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
Route::post('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');
Route::get('/carrito/resumen', [CarritoController::class, 'resumen'])->name('carrito.resumen');

// --- Checkout y Procesamiento de Pagos (Cliente) ---
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/procesar', [CheckoutController::class, 'procesar'])->name('checkout.procesar');
Route::get('/pedido/confirmacion/{id}', [CheckoutController::class, 'confirmacion'])->name('checkout.confirmacion');
Route::get('/pedido/{id}/factura-pos', [CheckoutController::class, 'facturaPos'])->name('pedido.factura.pos');

//2. RUTAS AUTENTICADAS (CLIENTE & ADMINISTRADOR)

Route::middleware('auth')->group(function () {

//[USUARIO GENERAL] Perfil de Cuenta
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //[CLIENTE] Panel del Cliente / Mis Compras
    
    // Mis Pedidos y Solicitud de Devolución
    Route::get('/mis-pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/mis-pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::post('/mis-pedidos/{id}/devolucion', [PedidoController::class, 'solicitarDevolucion'])->name('pedidos.devolucion');

    // Mis Devoluciones y Garantías
    Route::get('/mis-devoluciones', [ClienteDevolucionController::class, 'index'])->name('cliente.devoluciones.index');
    Route::post('/mis-devoluciones', [ClienteDevolucionController::class, 'store'])->name('cliente.devoluciones.store');

    // Métodos de Pago disponibles para compras
    Route::get('/metodos-pago', [ClienteMetodoPagoController::class, 'index'])->name('cliente.metodospago.index');

    // Favoritos / Lista de Deseos
    Route::get('/mis-favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
    Route::post('/favoritos/toggle/{producto_id}', [FavoritoController::class, 'toggle'])->name('favoritos.toggle');
    Route::delete('/favoritos/{producto_id}', [FavoritoController::class, 'destroy'])->name('favoritos.destroy');

//[ADMINISTRADOR] Panel de Administración & Gestión

    // Dashboard Principal & Gestión de Usuarios
    Route::get('/dashboard', [dashboardController::class, 'index'])->name('dashboard');
    Route::patch('/admin/usuarios/{id}/cambiar-rol', [dashboardController::class, 'cambiarRol'])->name('admin.usuarios.cambiarRol');

    // [ADMIN] Gestión de Categorías
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::put('/categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.actualizar');
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categorias.eliminar');
    Route::patch('/categorias/{id}/estado', [CategoriaController::class, 'cambiarEstado'])->name('categorias.estado');

    // [ADMIN] Gestión de Productos e Imágenes
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.actualizar');
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.eliminar');
    Route::patch('/productos/{id}/estado', [ProductoController::class, 'cambiarEstado'])->name('productos.estado');
    Route::delete('/productos/imagen/{id}', [ProductoController::class, 'eliminarImagen'])->name('productos.imagen.eliminar');

    // [ADMIN] Gestión de Ofertas y Promociones
    Route::get('/ofertas', [OfertaController::class, 'index'])->name('ofertas.index');
    Route::post('/ofertas', [OfertaController::class, 'store'])->name('ofertas.store');
    Route::put('/ofertas/{id}', [OfertaController::class, 'update'])->name('ofertas.actualizar');
    Route::delete('/ofertas/{id}', [OfertaController::class, 'destroy'])->name('ofertas.eliminar');
    Route::patch('/ofertas/{id}/estado', [OfertaController::class, 'cambiarEstado'])->name('ofertas.estado');

    // [ADMIN] Gestión de Proveedores
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');    
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.actualizar');
    Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.eliminar');
    Route::patch('/proveedores/{id}/estado', [ProveedorController::class, 'cambiarEstado'])->name('proveedores.estado');

    // [ADMIN] Configuración de Métodos de Pago del Negocio
    Route::get('/admin/metodos-pago', [MetodoPagoController::class, 'index'])->name('metodospago.index');
    Route::post('/admin/metodos-pago', [MetodoPagoController::class, 'store'])->name('metodospago.store');
    Route::put('/admin/metodos-pago/{id}', [MetodoPagoController::class, 'update'])->name('metodospago.actualizar');
    Route::delete('/admin/metodos-pago/{id}', [MetodoPagoController::class, 'destroy'])->name('metodospago.eliminar');
    Route::patch('/admin/metodos-pago/{id}/estado', [MetodoPagoController::class, 'cambiarEstado'])->name('metodospago.estado');

    // [ADMIN] Gestión de Pedidos de Clientes
    Route::get('/admin/pedidos', [AdminPedidoController::class, 'index'])->name('admin.pedidos.index');
    Route::patch('/admin/pedidos/{id}/estado', [AdminPedidoController::class, 'updateEstado'])->name('admin.pedidos.estado');
    Route::delete('/admin/pedidos/{id}', [AdminPedidoController::class, 'destroy'])->name('admin.pedidos.eliminar');

    // [ADMIN] Gestión y Despacho de Envíos
    Route::get('/admin/envios', [AdminEnvioController::class, 'index'])->name('admin.envios.index');
    Route::put('/admin/envios/{id}', [AdminEnvioController::class, 'update'])->name('admin.envios.update');
    Route::patch('/admin/envios/{id}/estado', [AdminEnvioController::class, 'cambiarEstado'])->name('admin.envios.estado');
    Route::delete('/admin/envios/{id}', [AdminEnvioController::class, 'destroy'])->name('admin.envios.destroy');

    // [ADMIN] Control de Pagos y Finanzas
    Route::get('/admin/pagos', [AdminPagoController::class, 'index'])->name('admin.pagos.index');
    Route::put('/admin/pagos/{id}', [AdminPagoController::class, 'update'])->name('admin.pagos.update');
    Route::patch('/admin/pagos/{id}/estado', [AdminPagoController::class, 'cambiarEstado'])->name('admin.pagos.estado');
    Route::delete('/admin/pagos/{id}', [AdminPagoController::class, 'destroy'])->name('admin.pagos.destroy');

    // [ADMIN] Ventas, Estadísticas y Reportes
    Route::get('/admin/ventas', [AdminVentaController::class, 'index'])->name('admin.ventas.index');

    // [ADMIN] Gestión de Devoluciones y Garantías
    Route::get('/admin/devoluciones', [AdminDevolucionController::class, 'index'])->name('admin.devoluciones.index');
    Route::post('/admin/devoluciones', [AdminDevolucionController::class, 'store'])->name('admin.devoluciones.store');
    Route::patch('/admin/devoluciones/{id}/estado', [AdminDevolucionController::class, 'updateEstado'])->name('admin.devoluciones.estado');
    Route::delete('/admin/devoluciones/{id}', [AdminDevolucionController::class, 'destroy'])->name('admin.devoluciones.destroy');

    // [ADMIN] Control de Inventario, Ajustes y Almacén
    Route::get('/admin/inventario', [AdminInventarioController::class, 'index'])->name('admin.inventario.index');
    Route::get('/inventario', fn() => redirect()->route('admin.inventario.index'));
    Route::post('/admin/inventario/ajuste', [AdminInventarioController::class, 'ajustarStock'])->name('admin.inventario.ajuste');
    Route::put('/admin/inventario/{id}/precios', [AdminInventarioController::class, 'actualizarPrecios'])->name('admin.inventario.precios');
    Route::get('/admin/inventario/movimientos', [AdminInventarioController::class, 'movimientos'])->name('admin.inventario.movimientos');

});

require __DIR__ . '/auth.php';
