# 👤 Modelo: Cliente

## 📍 Ubicación
`app/Models/Cliente.php`

---

## 🎯 Propósito General
Representa el rol comercial del comprador en PowerNet. Conecta la información humana/demográfica (`Persona`) con el historial de compras (`Pedido`) y métodos de pago guardados.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'clientes';

protected $fillable = [
    'direccion',
    'persona_id',
];
```

---

## 🔗 Relaciones Eloquent y Trazabilidad

```php
// 1. Datos personales y de contacto del cliente (N:1)
public function persona()
{
    return $this->belongsTo(Persona::class, 'persona_id');
}

// 2. Historial de órdenes de compra realizadas por el cliente (1:N)
public function pedidos()
{
    return $this->hasMany(Pedido::class, 'cliente_id');
}

// 3. Canales o métodos de pago vinculados (1:N)
public function metodosPago()
{
    return $this->hasMany(MetodoPago::class, 'cliente_id');
}
```

---

## 🔄 Diagrama de Trazabilidad del Modelo

```
[Persona] (nombre, teléfono, documento)
    ↑ (persona_id)
    |
[Cliente] (dirección de residencia)
    ↓ (1:N)
 [Pedido] (órdenes de compra)
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `Cliente` separa la lógica de negocio de la información personal mediante una relación `belongsTo` hacia `Persona`. Esto permite que una misma persona pueda mantener sus datos unificados mientras se auditan sus compras a través de la relación `hasMany` con `Pedido`."*

### 2. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **Error: "Attempt to read property 'nombre_persona' on null"**: Ocurre si el registro en `clientes` tiene un `persona_id` que fue eliminado en `personas`.
  - *Solución:* Verifica que la clave foránea `persona_id` apunte a un registro válido en `personas`.
