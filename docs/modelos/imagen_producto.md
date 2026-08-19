# 🖼️ Modelo: imagen_producto

## 📍 Ubicación
`app/Models/imagen_producto.php`

---

## 🎯 Propósito General
Representa cada fotografía asociada a la galería de un producto en la tabla `imagen_productos`.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'imagen_productos';

protected $fillable = [
    'producto_id',
    'imagen', // Nombre del archivo físico en public/imagenes_productos/
];
```

---

## 🔗 Relaciones Eloquent

```php
// Producto dueño de la fotografía (N:1)
public function producto()
{
    return $this->belongsTo(Producto::class, 'producto_id');
}
```
