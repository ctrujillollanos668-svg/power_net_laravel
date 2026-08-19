# ❤️ Modelo: Favorito

## 📍 Ubicación
`app/Models/Favorito.php`

---

## 🎯 Propósito General
Mapea la tabla `favoritos` y actúa como pivote relacional entre los usuarios registrados y los productos marcados en su lista de deseos (*Wishlist*).

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'favoritos';

protected $fillable = [
    'user_id',
    'producto_id',
];
```

---

## 🔗 Relaciones Eloquent

```php
// Usuario dueño del favorito (N:1)
public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

// Producto marcado como favorito (N:1)
public function producto()
{
    return $this->belongsTo(Producto::class, 'producto_id');
}
```
