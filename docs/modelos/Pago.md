# 💳 Modelo: Pago

## 📍 Ubicación
`app/Models/Pago.php`

---

## 🎯 Propósito General
Mapea la tabla `pagos` y registra la transacción financiera de cada pedido: método de recaudo, número de factura único, monto cobrado, estado (`Pendiente`, `Aprobado`, `Rechazado`) y fecha de confirmación.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'pagos';

protected $fillable = [
    'pedido_id',
    'metodo_pago',
    'monto',
    'estado_pago',
    'factura',
    'fecha_pago',
];
```

---

## 🔗 Relaciones Eloquent

```php
// Pedido al que corresponde el comprobante de pago (1:1 inverso)
public function pedido()
{
    return $this->belongsTo(Pedido::class, 'pedido_id');
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `Pago` registra la evidencia del recaudo monetario. Asigna un folio de factura único (ej. `FAC-65AF43`) y mantiene el estado de conciliación (`estado_pago`), permitiendo a contabilidad auditar qué dinero ha ingresado y qué pagos siguen pendientes."*
