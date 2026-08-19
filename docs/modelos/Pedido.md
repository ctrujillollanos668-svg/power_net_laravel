# 📋 Modelo: Pedido

## 📍 Ubicación
`app/Models/Pedido.php`

---

## 🎯 Propósito General
Representa las órdenes de compra efectuadas en la plataforma. Mapea la tabla `pedidos` y centraliza toda la trazabilidad transaccional conectando al cliente comprador con los ítems comprados (`DetallePedido`), el pago recibido (`Pago`), la transportadora asignada (`Envio`) y las garantías radicadas (`Devolucion`).

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'pedidos';

protected $fillable = [
    'fecha_pedido',
    'total_pedido',
    'estado_pedido',
    'cliente_id',
];

protected $casts = [
    'fecha_pedido' => 'datetime',
    'total_pedido' => 'decimal:2',
];
```

---

## 🔗 Relaciones Eloquent y Trazabilidad

```php
// 1. Un pedido pertenece a un Cliente (N:1)
public function cliente()
{
    return $this->belongsTo(Cliente::class, 'cliente_id');
}

// 2. Un pedido tiene múltiples líneas o productos comprados (1:N)
public function detalles()
{
    return $this->hasMany(DetallePedido::class, 'pedido_id');
}

// 3. Un pedido tiene un único despacho/envío asociado (1:1)
public function envio()
{
    return $this->hasOne(Envio::class, 'pedido_id');
}

// 4. Un pedido tiene un único comprobante de pago asociado (1:1)
public function pago()
{
    return $this->hasOne(Pago::class, 'pedido_id');
}

// 5. Un pedido puede tener reclamaciones o devoluciones asociadas (1:N)
public function devoluciones()
{
    return $this->hasMany(Devolucion::class, 'pedido_id');
}
```

---

## 🔄 Diagrama de Trazabilidad del Modelo

```
                 [Cliente]
                     ↑ (cliente_id)
                     |
              +-- [Pedido] --+
              |              |
     +--------+--------+-----+--------+
     ↓ (1:N)           ↓ (1:1)        ↓ (1:1)        ↓ (1:N)
[DetallePedido]     [Envio]        [Pago]        [Devolucion]
     ↓ (producto_id)
 [Producto]
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `Pedido` es el pivote relacional de ventas en PowerNet. Mediante las relaciones `hasOne` conecta directamente con `Pago` y `Envio`, y mediante `hasMany` con `DetallePedido`. Cuenta con `$casts` para transformar automáticamente `fecha_pedido` en una instancia de Carbon para operaciones con fechas y `total_pedido` a formato decimal exacto."*

### 2. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **Error: "Call to a member function format() on string" al mostrar la fecha**: Ocurre si `$casts` no está definido en el modelo. Asegúrate de incluir `'fecha_pedido' => 'datetime'` para que Laravel lo convierta en objeto Carbon.
- **Un pedido se muestra sin cliente**: Ocurre si el `cliente_id` fue borrado de la tabla `clientes`. Verifica la integridad referencial en MySQL.
