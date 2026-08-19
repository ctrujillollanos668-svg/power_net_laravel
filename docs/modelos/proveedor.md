# 🏭 Modelo: proveedor

## 📍 Ubicación
`app/Models/proveedor.php`

---

## 🎯 Propósito General
Representa los proveedores comerciales que suministran los artículos a PowerNet.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'proveedores';

protected $fillable = [
    'nombre_proveedor',
    'correo',
    'telefono',
    'estado',
];
```

---

## 🔗 Relaciones Eloquent

```php
// Productos suministrados por el proveedor (1:N)
public function productos()
{
    return $this->hasMany(Producto::class, 'proveedor_id');
}
```
