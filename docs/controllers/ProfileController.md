# 👤 ProfileController

## 📍 Ubicación
`app/Http/Controllers/ProfileController.php`

---

## 🎯 Propósito General
Gestiona la cuenta del usuario autenticado (nombre, correo electrónico, actualización de datos) y el proceso de eliminación o baja de cuenta con revalidación de contraseña.

---

## 🧩 Modelos y Dependencias
```php
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `update(ProfileUpdateRequest $request)` - Actualización de Datos Personales

#### 💻 Código Clave:
```php
public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $request->user()->fill($request->validated());

    // Si cambió de correo, invalida la verificación previa
    if ($request->user()->isDirty('email')) {
        $request->user()->email_verified_at = null;
    }

    $request->user()->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}
```

#### 🔍 ¿Qué hace este código?
- **`isDirty('email')`**: Detecta si el campo de correo electrónico fue modificado. De ser así, reinicia `email_verified_at = null` para forzar al usuario a verificar nuevamente su identidad en el nuevo correo.

---

### 2. `destroy(Request $request)` - Eliminación Segura de Cuenta

#### 💻 Código Clave:
```php
public function destroy(Request $request): RedirectResponse
{
    // Exige la contraseña actual para confirmar identidad
    $request->validateWithBag('userDeletion', [
        'password' => ['required', 'current_password'],
    ]);

    $user = $request->user();

    Auth::logout();
    $user->delete();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Redirect::to('/');
}
```

#### 🔍 ¿Qué hace este código?
- **`current_password`**: Valida que quien solicita la baja de la cuenta conozca la contraseña actual. Cierra la sesión activa, destruye la sesión y regenera el token CSRF para evitar ataques de reutilización de sesión.
