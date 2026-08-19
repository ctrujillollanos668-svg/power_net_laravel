# 🏦 ClienteMetodoPagoController

## 📍 Ubicación
`app/Http/Controllers/ClienteMetodoPagoController.php`

---

## 🎯 Propósito General
Muestra la página informativa pública para los clientes con las cuentas bancarias oficiales, billeteras virtuales (Nequi, Daviplata) e instructivos de consignación autorizados por PowerNet.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\MetodoPago;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index()` - Canales Oficiales de Pago

#### 💻 Código Clave:
```php
public function index()
{
    // Obtiene únicamente los métodos de pago habilitados por el administrador
    $metodos = MetodoPago::where('estado', 1)->get();

    return view('cliente.metodospago.index', compact('metodos'));
}
```

#### 🔍 ¿Qué hace este código?
- Consulta en la base de datos los métodos con `estado = 1` y los envía a la vista informativa del cliente con números de cuenta, titulares y pasos para reportar el pago.
