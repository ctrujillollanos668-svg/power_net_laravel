# 👤 RegisteredUserController

## 📍 Ubicación
`app/Http/Controllers/Auth/RegisteredUserController.php`

---

## 🎯 Propósito General
Controla el registro de nuevos usuarios desde el formulario público, asegurando el cifrado de contraseñas con `Hash::make`, la asignación por defecto del rol de cliente (`role_id = 2`) y el inicio automático de sesión tras registrarse.

---

## 🧩 Dependencias
```php
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `store(Request $request)` - Registro y Asignación de Rol

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

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este controlador en una sustentación?
> *"El `RegisteredUserController` registra nuevos clientes. Valida que el correo sea único en la tabla `users` (`unique:users`), cifra la contraseña usando el algoritmo seguro de hashing con `Hash::make` y le asigna obligatoriamente el `role_id = 2` para garantizar que ningún usuario externo se registre como Administrador."*

### 2. Tablas y campos afectados en MySQL:
- **`users`**: Inserta `name`, `email`, `password` (cifrado), `role_id = 2`.

### 3. Posibles errores y soluciones:
- **Error: "The email has already been taken"**: El correo ya está registrado por otro usuario.
- **Error: "The password confirmation does not match"**: El campo `password_confirmation` no coincide con `password`.
