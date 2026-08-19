# ❤️ FavoritoController

## 📍 Ubicación
`app/Http/Controllers/FavoritoController.php`

---

## 🎯 Propósito General
Gestiona la lista de deseos (*Wishlist*) de los usuarios autenticados con soporte para peticiones asíncronas vía AJAX/Fetch para dar o quitar "like" a productos al instante.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Favorito;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `toggle(Request $request, $producto_id)` - Conmutador de Favorito (AJAX)

#### 💻 Código Clave:
```php
public function toggle(Request $request, $producto_id)
{
    // 1. Control de autenticación para peticiones asíncronas
    if (!Auth::check()) {
        return response()->json([
            'status' => 'unauthenticated',
            'message' => 'Debes iniciar sesión para guardar favoritos.',
        ], 401);
    }

    $userId = Auth::id();
    $producto = Producto::findOrFail($producto_id);

    // 2. Verificar si ya existe en favoritos
    $favorito = Favorito::where('user_id', $userId)
        ->where('producto_id', $producto_id)
        ->first();

    if ($favorito) {
        // Si existe, lo remueve (Unlike)
        $favorito->delete();
        return response()->json([
            'status' => 'removed',
            'is_favorite' => false,
            'message' => 'Producto eliminado de tus favoritos.',
            'total' => Favorito::where('user_id', $userId)->count(),
        ]);
    } else {
        // Si no existe, lo agrega (Like)
        Favorito::create([
            'user_id' => $userId,
            'producto_id' => $producto_id,
        ]);
        return response()->json([
            'status' => 'added',
            'is_favorite' => true,
            'message' => '¡Producto agregado a tus favoritos!',
            'total' => Favorito::where('user_id', $userId)->count(),
        ]);
    }
}
```

#### 🔍 ¿Qué hace este código?
- **Respuesta JSON Reactiva**: Devuelve `is_favorite: true/false` y el número total actualizado de favoritos para que la interfaz en JavaScript cambie el color del icono en la barra de navegación y en la tarjeta de producto sin recargar la página.
