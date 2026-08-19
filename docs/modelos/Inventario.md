# 📦 Modelo: Inventario (Kardex)

## 📍 Ubicación
`app/Models/Inventario.php`

---

## 🎯 Propósito General
Representa el Kardex o libro contable de movimientos de bodega. Registra de forma inmutable cada entrada, salida o merma de productos, almacenando el stock previo, la cantidad movida, el nuevo stock resultante y la justificación.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'inventarios';

protected $fillable = [
    'producto_id',
    'tipo',          // 'entrada' o 'salida'
    'cantidad',
    'stock_anterior',
    'stock_nuevo',
    'motivo',
    'pedido_id',
];
```

---

## 🔗 Relaciones Eloquent

```php
// Producto afectado en el inventario (N:1)
public function producto()
{
    return $this->belongsTo(Producto::class, 'producto_id');
}

// Pedido que originó la salida si fue por venta (N:1 opcional)
public function pedido()
{
    return $this->belongsTo(Pedido::class, 'pedido_id');
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `Inventario` garantiza la trazabilidad física y contable del almacén. Cada vez que se realiza una venta o un ajuste manual, este modelo crea una fila de auditoría que guarda el historial exacto (`stock_anterior` y `stock_nuevo`), respondiendo a normas de control interno."*
