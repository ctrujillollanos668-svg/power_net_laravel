# 🏷️ OfertaController

## 📍 Ubicación
`app/Http/Controllers/OfertaController.php`

---

## 🎯 Propósito General
Permite a los administradores programar descuentos y promociones en productos con cálculo automático de porcentajes de rebaja y vigencia por fechas.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Oferta;
use App\Models\Producto;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `store(Request $request)` - Creación y Cálculo Automático de Descuento

#### 💻 Código Clave:
```php
public function store(Request $request)
{
    $request->validate([
        'producto_id' => 'required|exists:productos,id',
        'precio_oferta' => 'required|numeric|min:0',
        'descuento' => 'nullable|integer|min:0|max:100',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        'estado' => 'required|string|in:activa,inactiva,vencida',
    ]);

    $producto = Producto::findOrFail($request->producto_id);

    // Cálculo automático del porcentaje de descuento si no fue provisto
    $descuento = $request->descuento;
    if (!$descuento && $producto->precio > 0) {
        $descuento = round((($producto->precio - $request->precio_oferta) / $producto->precio) * 100);
    }

    $oferta = new Oferta();
    $oferta->producto_id = $request->producto_id;
    $oferta->precio_oferta = $request->precio_oferta;
    $oferta->descuento = max(0, min(100, (int)$descuento));
    $oferta->fecha_inicio = $request->fecha_inicio;
    $oferta->fecha_fin = $request->fecha_fin;
    $oferta->estado = $request->estado;
    $oferta->save();

    if ($request->wantsJson()) {
        return response()->json(['success' => true, 'mensaje' => '¡Oferta creada exitosamente!']);
    }

    return redirect()->back()->with('Mensaje', '¡Oferta creada exitosamente!');
}
```

#### 🔍 ¿Qué hace este código?
- **`after_or_equal:fecha_inicio`**: Valida que la fecha de finalización de la promoción no sea anterior a la fecha de inicio.
- **Fórmula de Descuento**: Si el administrador sólo escribe el precio final rebajado, el código calcula automáticamente el porcentaje para la etiqueta de descuento (ej. "20% OFF").
