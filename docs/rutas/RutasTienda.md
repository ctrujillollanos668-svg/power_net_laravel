# 🛍️ Rutas de Tienda, Catálogo, Carrito y Checkout (Públicas / Cliente)

## 📍 Ubicación en Código
`routes/web.php` (Líneas 26 a 49)

---

## 🎯 Propósito General
Define los endpoints de navegación pública y del proceso de compra que no requieren autenticación previa obligatoria para explorar productos o armar el carrito de compras.

---

## 💻 Definición de Código en `routes/web.php`

```php
// --- 1. Tienda y Catálogo (Públicas) ---
Route::get('/', [TiendaController::class, 'index'])->name('tienda.inicio');
Route::get('/catalogo', [TiendaController::class, 'catalogo'])->name('tienda.catalogo');
Route::get('/catalogo/categoria/{id}', [TiendaController::class, 'porCategoria'])->name('tienda.categoria');
Route::get('/producto/{id}', [TiendaController::class, 'show'])->name('tienda.detalle');
Route::get('/promociones', [TiendaController::class, 'ofertas'])->name('tienda.ofertas');
Route::get('/buscar', [TiendaController::class, 'buscar'])->name('tienda.buscar');

// --- 2. Carrito de Compras (Público / Sesión) ---
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::post('/carrito/actualizar/{id}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
Route::delete('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
Route::post('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');
Route::get('/carrito/resumen', [CarritoController::class, 'resumen'])->name('carrito.resumen');

// --- 3. Checkout y Procesamiento de Pagos (Cliente) ---
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/procesar', [CheckoutController::class, 'procesar'])->name('checkout.procesar');
Route::get('/pedido/confirmacion/{id}', [CheckoutController::class, 'confirmacion'])->name('checkout.confirmacion');
Route::get('/pedido/{id}/factura-pos', [CheckoutController::class, 'facturaPos'])->name('pedido.factura.pos');
```

---

## 📊 Matriz de Rutas y Métodos Despachados

| Verbo HTTP | URI | Nombre de Ruta (`name`) | Controlador & Método | Descripción |
|---|---|---|---|---|
| `GET` | `/` | `tienda.inicio` | `TiendaController@index` | Página principal de bienvenida con productos destacados. |
| `GET` | `/catalogo` | `tienda.catalogo` | `TiendaController@catalogo` | Catálogo interactivo con filtros por precio y categoría. |
| `GET` | `/producto/{id}` | `tienda.detalle` | `TiendaController@show` | Ficha técnica y fotos del producto. |
| `GET` | `/carrito` | `carrito.index` | `CarritoController@index` | Vista de artículos en la cesta de compras. |
| `POST` | `/carrito/agregar/{id}` | `carrito.agregar` | `CarritoController@agregar` | Añade $N$ unidades de un producto a la sesión. |
| `DELETE` | `/carrito/eliminar/{id}` | `carrito.eliminar` | `CarritoController@eliminar` | Remueve un producto del carrito. |
| `GET` | `/checkout` | `checkout.index` | `CheckoutController@index` | Formulario de pago y dirección de envío. |
| `POST` | `/checkout/procesar` | `checkout.procesar` | `CheckoutController@procesar` | Ejecuta la transacción atómica de compra. |
| `GET` | `/pedido/confirmacion/{id}` | `checkout.confirmacion` | `CheckoutController@confirmacion` | Pantalla de agradecimiento y orden creada. |

---

## 🔄 Trazabilidad de Petición HTTP a Controlador

```
1. Cliente da clic en "Comprar" en /producto/5
   ↓
2. Petición POST a /carrito/agregar/5
   ↓
3. Router web.php resuelve -> CarritoController::class, 'agregar'
   ↓
4. Controlador valida stock y escribe en session('cart')
   ↓
5. Redirige a ruta con nombre: route('carrito.index') -> URL /carrito
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar estas rutas en una sustentación?
> *"Las rutas públicas de tienda y carrito no requieren sesión activa obligatoria, permitiendo a los visitantes navegar y agregar productos a `session('cart')`. Al momento de comprar (`POST /checkout/procesar`), la ruta conecta con el controlador transaccional que valida inventario y genera el pedido."*

### 2. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **Error: `Route [carrito.agregar] not defined`**: Ocurre si en un formulario Blade escribes mal el nombre de la ruta. Verifica que en `web.php` tenga `->name('carrito.agregar')`.
- **Error 405 `MethodNotAllowedHttpException`**: Ocurre si envías un formulario por `GET` a una ruta que espera `POST` o `DELETE`. Agrega `@csrf` y `@method('DELETE')` según corresponda.
