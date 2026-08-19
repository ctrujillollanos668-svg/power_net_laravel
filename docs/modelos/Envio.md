# 🚚 Modelo: Envio

## 📍 Ubicación
`app/Models/Envio.php`

---

## 🎯 Propósito General
Mapea la tabla `envios` para la gestión logística de entrega: transportadora asignada, dirección física de entrega, estado de despacho y costo de flete.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'envios';

protected $fillable = [
    'pedido_id',
    'direccion_envio',
    'empresa_envios',
    'estado',
    'costo',
    'fecha_hora',
];
```

---

## 🔗 Relaciones Eloquent

```php
// Pedido al que pertenece la guía de despacho (1:1 inverso)
public function pedido()
{
    return $this->belongsTo(Pedido::class, 'pedido_id');
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `Envio` desacopla la información logística del pedido principal, permitiendo almacenar datos específicos de transporte (empresa de encomiendas, costo de envío y dirección detallada) sin sobrecargar la tabla de pedidos."*
