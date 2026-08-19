# 🔐 AuthenticatedSessionController

## 📍 Ubicación
`app/Http/Controllers/Auth/AuthenticatedSessionController.php`

---

## 🎯 Propósito General
Maneja el ciclo de inicio (`login`) y cierre (`logout`) de sesión de usuarios en PowerNet, implementando protección contra ataques de fuerza bruta (Rate Limiting) y redirección inteligente según el rol del usuario (Admin $\rightarrow$ Dashboard, Cliente $\rightarrow$ Tienda).

---

## 🧩 Dependencias
```php
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `store(LoginRequest $request)` - Autenticación y Redirección por Rol

#### 💻 Código Clave:
```php
public function store(LoginRequest $request): RedirectResponse
{
    // 1. Validar credenciales con limitador de intentos
    $request->authenticate();

    // 2. Regenerar ID de sesión contra ataques de Session Fixation
    $request->session()->regenerate();

    // 3. Redirección condicional según rol
    $role = auth()->user()->role_id;
    if ($role == 1) {
        return redirect()->intended(route('dashboard', absolute: false));
    }

    return redirect()->intended(route('tienda.inicio', absolute: false));
}
```

---

### 2. `destroy(Request $request)` - Cierre Seguro de Sesión

#### 💻 Código Clave:
```php
public function destroy(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este controlador en una sustentación?
> *"El `AuthenticatedSessionController` gestiona el inicio y fin de sesión. En el método `store`, valida las credenciales a través de un Form Request (`LoginRequest`) que previene ataques de fuerza bruta, regenera el ID de sesión para evitar vulnerabilidades de fijación de sesión y redirige dinámicamente: si el `role_id` es 1 lo envía al panel administrativo `/dashboard`, y si es 2 lo dirige a la tienda `/`."*

### 2. Tablas y campos afectados en MySQL:
- **`users`**: Consulta `email`, `password` (hash cifrado) y `role_id`.

### 3. Posibles errores y soluciones:
- **Error: "These credentials do not match our records"**: Correo o contraseña incorrectos.
- **Error: "Too many login attempts"**: Se activó el Rate Limiting por demasiados intentos fallidos. Esperar 60 segundos o reiniciar caché con `php artisan cache:clear`.
