# 🔐 Modelo: User

## 📍 Ubicación
`app/Models/User.php`

---

## 🎯 Propósito General
Representa las credenciales de autenticación y seguridad en el sistema (Laravel Breeze). Gestiona el acceso al panel administrativo o a la tienda según el rol asignado (`role_id`), el hashing de contraseñas y la relación con la lista de favoritos.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role_id',
    'persona_id',
];

protected $hidden = [
    'password',
    'remember_token',
];

protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

---

## 🔗 Relaciones Eloquent y Trazabilidad

```php
// 1. Rol de seguridad asignado (Admin = 1, Cliente = 2) (N:1)
public function role()
{
    return $this->belongsTo(Role::class, 'role_id');
}

// 2. Datos personales vinculados (N:1)
public function persona()
{
    return $this->belongsTo(Persona::class, 'persona_id');
}

// 3. Registros de favoritos / Wishlist del usuario (1:N)
public function favoritos()
{
    return $this->hasMany(Favorito::class, 'user_id');
}

// 4. Productos favoritos a través de la relación de muchos a muchos (N:M)
public function productosFavoritos()
{
    return $this->belongsToMany(Producto::class, 'favoritos', 'user_id', 'producto_id')
        ->withTimestamps();
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `User` extiende `Authenticatable` y centraliza la seguridad del sistema. Utiliza `$hidden` para proteger la contraseña y token en respuestas JSON, y define la relación `belongsToMany` con `Producto` a través de la tabla pivote `favoritos` para cargar eficientemente la lista de deseos del usuario con `$user->productosFavoritos`."*

### 2. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **El usuario no puede acceder al Dashboard**: Revisa que su campo `role_id` en la tabla `users` sea igual a `1` (Administrador). Si es `2`, el sistema lo redirigirá siempre a la tienda.
