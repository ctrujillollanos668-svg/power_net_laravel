# 👤 Modelo: Persona

## 📍 Ubicación
`app/Models/Persona.php`

---

## 🎯 Propósito General
Almacena los datos personales, de identificación y contacto básicos (nombre, documento/cédula, teléfono) de los individuos registrados en el sistema, vinculándose tanto con la cuenta de acceso (`User`) como con el perfil de comprador (`Cliente`).

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'personas';

protected $fillable = [
    'nombre_persona',
    'telefono',
    'documento',
];
```

---

## 🔗 Relaciones Eloquent y Trazabilidad

```php
// 1. Cuenta de usuario web vinculada para login (1:1)
public function user()
{
    return $this->hasOne(User::class, 'persona_id');
}

// 2. Perfil de comprador asociado para registrar compras (1:1)
public function cliente()
{
    return $this->hasOne(Cliente::class, 'persona_id');
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `Persona` implementa el patrón de diseño de Normalización de Tercera Forma Normal (3NF). Centraliza la identidad legal de los usuarios (identificación y nombre) evitando la duplicación de datos entre los roles de sistema (`users`) y el historial comercial (`clientes`)."*

### 2. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **Duplicidad de Cédula/NIT**: En el checkout se utiliza `Persona::firstOrCreate(['documento' => $doc], [...])` para evitar duplicar registros si la misma persona vuelve a comprar.
