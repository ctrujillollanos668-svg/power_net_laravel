# 🏷️ Modelo: Categoria

## 📍 Ubicación
`app/Models/Categoria.php`

---

## 🎯 Propósito General
Mapea la tabla `categorias` y representa las agrupaciones de productos del catálogo de PowerNet.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'categorias';

protected $fillable = [
    'nombre_categoria',
    'descripcion',
    'estado',
];
```

---

## 🔗 Relaciones Eloquent

```php
// Productos que pertenecen a esta categoría (1:N)
public function productos()
{
    return $this->hasMany(Producto::class, 'categoria_id');
}
```
