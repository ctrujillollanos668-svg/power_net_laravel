# 💵 Modelo: MetodoPago

## 📍 Ubicación
`app/Models/MetodoPago.php`

---

## 🎯 Propósito General
Representa los canales de pago y cuentas bancarias configuradas en PowerNet (Nequi, Daviplata, Bancolombia, Tarjeta de Crédito, Contra entrega).

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'metodos_pago';

protected $fillable = [
    'nombre',
    'tipo',
    'numero',
    'titular',
    'instrucciones',
    'estado',
];
```
