# 🏷️ Modelo: Oferta

## 📍 Ubicación
`app/Models/Oferta.php`

---

## 🎯 Propósito General
Representa los descuentos y promociones programadas sobre productos específicos con fechas de vigencia y porcentajes de descuento.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'ofertas';

protected $fillable = [
    'producto_id',
    'precio_oferta',
    'descuento',
    'fecha_inicio',
    'fecha_fin',
    'estado',          // 'activa', 'inactiva', 'vencida'
];
```

---

## 🔗 Relaciones Eloquent

```php
// Producto que tiene la promoción aplicada (N:1)
public function producto()
{
    return $this->belongsTo(Producto::class, 'producto_id');
}
```
