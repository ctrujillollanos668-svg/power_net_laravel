# 🔐 Controladores del Módulo de Autenticación (Auth)

## 📍 Ubicación
`app/Http/Controllers/Auth/`

---

## 🎯 Propósito General
Implementa el flujo seguro de autenticación de usuarios mediante **Laravel Breeze**: inicio de sesión con redirección por roles, registro automático de clientes, reseteo de contraseñas olvidadas por correo y verificación de cuenta.

---

## 🛠️ Explicación Detallada del Código por Controlador

### 1. `AuthenticatedSessionController.php` - Inicio y Cierre de Sesión

#### 💻 Código Clave:
```php
public function store(LoginRequest $request): RedirectResponse
{
    // 1. Autenticar credenciales con Rate Limiting (anti fuerza bruta)
    $request->authenticate();

    // 2. Regenerar ID de sesión para prevenir Session Fixation
    $request->session()->regenerate();

    // 3. Redirección basada en Rol
    $role = auth()->user()->role_id;
    if ($role == 1) {
        return redirect()->intended(route('dashboard', absolute: false));
    }

    return redirect()->intended(route('tienda.inicio', absolute: false));
}

public function destroy(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
```

#### 🔍 ¿Qué hace este código?
- **`$request->authenticate()`**: Valida correo y contraseña aplicando control de intentos fallidos.
- **Redirección por Rol**: Si el usuario es Administrador (`role_id == 1`), lo envía a su panel (`/dashboard`); si es Cliente (`role_id == 2`), lo envía a la tienda (`/`).
- **`destroy()`**: Cierra la sesión web de forma segura, destruye las variables de sesión y regenera el token CSRF.

---

### 2. `RegisteredUserController.php` - Registro de Nuevos Clientes

#### 💻 Código Clave:
```php
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // Asignación por defecto de role_id = 2 (Cliente)
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => 2,
    ]);

    event(new Registered($user));
    Auth::login($user);

    return redirect(route('tienda.inicio', absolute: false));
}
```

#### 🔍 ¿Qué hace este código?
- **`Hash::make($request->password)`**: Cifra la contraseña con el algoritmo seguro Bcrypt / Argon2 antes de guardarla.
- **`'role_id' => 2`**: Asegura que todo nuevo usuario registrado desde el formulario público reciba únicamente permisos de Cliente.
- **`Auth::login($user)`**: Inicia sesión automáticamente tras el registro exitoso.
