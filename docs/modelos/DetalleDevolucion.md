# 🔄 Modelo: DetalleDevolucion

## 📍 Ubicación
`app/Models/DetalleDevolucion.php`

---

## 🎯 Propósito General
Representa cada producto y cantidad que forma parte de una solicitud de devolución o garantía.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'detalle_devolucions';

protected $fillable = [
    'devolucione_id',
    'producto_id',
    'cantidad',
    'motivo',
];
```

---

## 🔗 Relaciones Eloquent

```php
// Devolución padre a la que pertenece (N:1)
public function devolucion()
{
    return $this->belongsTo(Devolucion::class, 'devolucione_id');
}

// Producto sujeto a garantía (N:1)
public function producto()
{
    return $this->belongsTo(Producto::class, 'producto_id');
}
```
