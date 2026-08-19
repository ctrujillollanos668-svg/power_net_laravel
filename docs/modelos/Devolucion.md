# 🔄 Modelo: Devolucion

## 📍 Ubicación
`app/Models/Devolucion.php`

---

## 🎯 Propósito General
Representa la solicitud de garantía o reclamo de devolución radicada por un cliente o creada por el administrador para un pedido específico.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'devoluciones';

protected $fillable = [
    'pedido_id',
    'fecha_devolucion',
    'motivo',
    'monto_devolucion',
    'estado',          // 'Pendiente', 'Aprobada', 'Rechazada', 'Completada'
    'motivo_rechazo',
];
```

---

## 🔗 Relaciones Eloquent

```php
// Pedido original sujeto a reclamación (N:1)
public function pedido()
{
    return $this->belongsTo(Pedido::class, 'pedido_id');
}

// Productos específicos devueltos (1:N)
public function detalles()
{
    return $this->hasMany(DetalleDevolucion::class, 'devolucione_id');
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `Devolucion` centraliza las solicitudes postventa y de garantía. Maneja una relación $1:N$ con `DetalleDevolucion` para registrar los productos defectuosos y un ciclo de estados (`Pendiente` $\rightarrow$ `Aprobada`/`Rechazada`) que dispara la reincorporación de stock al inventario."*
