# 🛡️ Modelo: Role

## 📍 Ubicación
`app/Models/Role.php`

---

## 🎯 Propósito General
Define los roles o niveles de autorización de los usuarios en PowerNet (1 = Administrador, 2 = Cliente).

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'roles';

protected $fillable = [
    'nombre_rol',
];
```

---

## 🔗 Relaciones Eloquent

```php
// Usuarios que poseen este rol (1:N)
public function users()
{
    return $this->hasMany(User::class, 'role_id');
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `Role` implementa el control de acceso basado en roles (RBAC - Role-Based Access Control) en PowerNet, permitiendo segmentar vistas y permisos entre el panel administrativo y la tienda pública."*
