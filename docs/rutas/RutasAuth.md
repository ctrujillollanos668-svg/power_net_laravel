# 🔐 Rutas de Autenticación y Seguridad (Auth)

## 📍 Ubicación en Código
`routes/auth.php`

---

## 🎯 Propósito General
Define las rutas para el ciclo de seguridad de usuarios (Laravel Breeze): login, registro, recuperación de contraseñas olvidadas por correo, verificación de email y cierre de sesión.

---

## 💻 Definición de Código en `routes/auth.php`

```php
// --- Rutas para Invitados (No Autenticados) ---
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// --- Rutas para Usuarios Autenticados ---
Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
```

---

## 📊 Matriz de Rutas de Seguridad

| Verbo HTTP | URI | Nombre de Ruta | Middleware | Controlador & Método |
|---|---|---|---|---|
| `GET` | `/login` | `login` | `guest` | `AuthenticatedSessionController@create` |
| `POST` | `/login` | - | `guest` | `AuthenticatedSessionController@store` |
| `POST` | `/logout` | `logout` | `auth` | `AuthenticatedSessionController@destroy` |
| `GET` | `/register` | `register` | `guest` | `RegisteredUserController@create` |
| `POST` | `/register` | - | `guest` | `RegisteredUserController@store` |
| `GET` | `/forgot-password` | `password.request` | `guest` | `PasswordResetLinkController@create` |
| `POST` | `/forgot-password` | `password.email` | `guest` | `PasswordResetLinkController@store` |
| `GET` | `/reset-password/{token}` | `password.reset` | `guest` | `NewPasswordController@create` |
| `POST` | `/reset-password` | `password.store` | `guest` | `NewPasswordController@store` |
| `PUT` | `/password` | `password.update` | `auth` | `PasswordController@update` |

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar estas rutas en una sustentación?
> *"El archivo `routes/auth.php` divide la seguridad en dos grupos de middlewares: `guest`, que impide que un usuario ya conectado vuelva a entrar al login o registro (redirigiéndolo automáticamente si ya está logueado), y `auth`, que protege las rutas sensibles de cambio de contraseña y logout."*

### 2. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **Un usuario logueado intenta abrir `/login`**: El middleware `guest` lo detecta y lo redirige automáticamente al inicio (`/`).
- **El enlace de verificación de correo da error 403 `Invalid signature`**: Ocurre si la URL firmada (`signed`) fue manipulada o expiró el token temporal.
