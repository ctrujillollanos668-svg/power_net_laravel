# 👤 Rutas del Cliente Autenticado

## 📍 Ubicación en Código
`routes/web.php` (Líneas 52 a 76)

---

## 🎯 Propósito General
Rutas protegidas por el middleware `auth`. Solo son accesibles por usuarios que hayan iniciado sesión. Permiten consultar compras pasadas (`Mis Pedidos`), radicar garantías (`Mis Devoluciones`), gestionar datos de perfil y la lista de deseos (`Favoritos`).

---

## 💻 Definición de Código en `routes/web.php`

```php
Route::middleware('auth')->group(function () {

    // [USUARIO GENERAL] Perfil de Cuenta
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // [CLIENTE] Mis Pedidos y Solicitud de Devolución
    Route::get('/mis-pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/mis-pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::post('/mis-pedidos/{id}/devolucion', [PedidoController::class, 'solicitarDevolucion'])->name('pedidos.devolucion');

    // [CLIENTE] Mis Devoluciones y Garantías
    Route::get('/mis-devoluciones', [ClienteDevolucionController::class, 'index'])->name('cliente.devoluciones.index');
    Route::post('/mis-devoluciones', [ClienteDevolucionController::class, 'store'])->name('cliente.devoluciones.store');

    // [CLIENTE] Métodos de Pago Informativos
    Route::get('/metodos-pago', [ClienteMetodoPagoController::class, 'index'])->name('cliente.metodospago.index');

    // [CLIENTE] Favoritos / Wishlist
    Route::get('/mis-favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
    Route::post('/favoritos/toggle/{producto_id}', [FavoritoController::class, 'toggle'])->name('favoritos.toggle');
    Route::delete('/favoritos/{producto_id}', [FavoritoController::class, 'destroy'])->name('favoritos.destroy');

});
```

---

## 📊 Matriz de Rutas y Métodos Despachados

| Verbo HTTP | URI | Nombre de Ruta (`name`) | Middleware | Controlador & Método |
|---|---|---|---|---|
| `GET` | `/profile` | `profile.edit` | `auth` | `ProfileController@edit` |
| `PATCH` | `/profile` | `profile.update` | `auth` | `ProfileController@update` |
| `DELETE` | `/profile` | `profile.destroy` | `auth` | `ProfileController@destroy` |
| `GET` | `/mis-pedidos` | `pedidos.index` | `auth` | `PedidoController@index` |
| `GET` | `/mis-pedidos/{id}` | `pedidos.show` | `auth` | `PedidoController@show` |
| `POST` | `/mis-pedidos/{id}/devolucion` | `pedidos.devolucion` | `auth` | `PedidoController@solicitarDevolucion` |
| `GET` | `/mis-devoluciones` | `cliente.devoluciones.index` | `auth` | `ClienteDevolucionController@index` |
| `POST` | `/mis-devoluciones` | `cliente.devoluciones.store` | `auth` | `ClienteDevolucionController@store` |
| `GET` | `/mis-favoritos` | `favoritos.index` | `auth` | `FavoritoController@index` |
| `POST` | `/favoritos/toggle/{producto_id}` | `favoritos.toggle` | `auth` | `FavoritoController@toggle` |

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar estas rutas en una sustentación?
> *"Estas rutas están encapsuladas dentro del grupo `Route::middleware('auth')`. Si un usuario invitado intenta abrir `/mis-pedidos` o `/mis-favoritos`, el middleware intercepta la petición y redirige automáticamente al login (`route('login')`), protegiendo la privacidad de las compras."*

### 2. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **Error 401 `Unauthenticated` en peticiones AJAX de Favoritos**: Ocurre si la petición Fetch intenta agregar a favoritos sin sesión activa. El controlador responde con JSON y el frontend muestra alerta para invitar al usuario a iniciar sesión.
