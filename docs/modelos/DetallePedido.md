# 🧾 Modelo: DetallePedido

## 📍 Ubicación
`app/Models/DetallePedido.php`

---

## 🎯 Propósito General
Representa cada línea o artículo individual dentro de una orden de compra. Guarda el registro histórico inmutable del precio unitario al momento de la venta, cantidad y subtotal.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'detalles_pedidos';

protected $fillable = [
    'precio_unitario',
    'cantidad',
    'subtotal',
    'pedido_id',
    'producto_id',
];

protected $casts = [
    'precio_unitario' => 'decimal:2',
    'subtotal' => 'decimal:2',
    'cantidad' => 'integer',
];
```

---

## 🔗 Relaciones Eloquent y Trazabilidad

```php
// 1. Pedido padre al que pertenece la línea (N:1)
public function pedido()
{
    return $this->belongsTo(Pedido::class, 'pedido_id');
}

// 2. Producto tecnológico vendido (N:1)
public function producto()
{
    return $this->belongsTo(Producto::class, 'producto_id');
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `DetallePedido` almacena una instantánea histórica de los precios (`precio_unitario` y `subtotal`). Aunque el administrador cambie el precio del producto en el catálogo en el futuro, el detalle del pedido preserva el valor exacto al que fue vendido originalmente, garantizando la fidelidad contable."*

### 2. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **El total del pedido no coincide con la suma de los detalles**: Verifica que el `subtotal` sea exactamente `precio_unitario * cantidad` antes de insertar.
