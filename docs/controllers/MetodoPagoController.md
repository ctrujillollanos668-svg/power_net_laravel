# 💵 MetodoPagoController

## 📍 Ubicación
`app/Http/Controllers/MetodoPagoController.php`

---

## 🎯 Propósito General
Permite a los administradores registrar, actualizar y activar/desactivar las cuentas y pasarelas de pago disponibles en PowerNet.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\MetodoPago;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `store(Request $request)` - Registro de Canal de Recaudo

#### 💻 Código Clave:
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:100',
        'tipo' => 'required|string|max:50',
        'numero' => 'nullable|string|max:100',
        'titular' => 'nullable|string|max:100',
        'instrucciones' => 'nullable|string|max:500',
        'estado' => 'nullable|boolean',
    ]);

    // Asigna 1 si el checkbox 'estado' viene marcado, o 0 si no
    $validated['estado'] = $request->has('estado') ? 1 : 0;

    MetodoPago::create($validated);

    return redirect()->route('metodospago.index')->with('success', 'Método de pago creado exitosamente.');
}
```

#### 🔍 ¿Qué hace este código?
- Valida los datos bancarios o instrucciones del canal e inserta el método en la base de datos para que quede disponible en el checkout.

---

### 2. `cambiarEstado($id)` - Conmutador Activo/Inactivo

#### 💻 Código Clave:
```php
public function cambiarEstado($id)
{
    $metodo = MetodoPago::findOrFail($id);
    $metodo->estado = !$metodo->estado;
    $metodo->save();

    return redirect()->route('metodospago.index')->with('success', 'Estado del método de pago actualizado.');
}
```

#### 🔍 ¿Qué hace este código?
- Alterna el booleano `estado` para mostrar u ocultar el método de pago inmediatamente a los clientes en el proceso de compra.
