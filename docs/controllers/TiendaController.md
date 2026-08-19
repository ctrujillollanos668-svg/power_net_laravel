# 🛍️ TiendaController

## 📍 Ubicación
`app/Http/Controllers/TiendaController.php`

---

## 🎯 Propósito General
Atiende la experiencia pública de compra. Controla la página de bienvenida (`welcome`), el catálogo interactivo con filtros multidimensionales (búsqueda de texto, categoría, rango de precios y ordenamiento) y la ficha técnica de producto.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Oferta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index(Request $request)` - Página Principal (Welcome)

#### 💻 Código Clave:
```php
public function index(Request $request)
{
    // 1. Categorías activas con conteo de productos en stock
    $categorias = Categoria::where('estado', 1)
        ->withCount(['productos' => fn($q) => $q->where('disponibilidad', 1)])
        ->get();

    // 2. Consulta de productos disponibles
    $query = Producto::where('disponibilidad', 1)
        ->with(['imagenes', 'categoria', 'ofertaActiva']);

    // Filtros de categoría, búsqueda por texto y rango de precio
    if ($request->filled('categoria')) {
        $query->where('categoria_id', $request->get('categoria'));
    }
    if ($request->filled('q')) {
        $busqueda = $request->get('q');
        $query->where(function ($q) use ($busqueda) {
            $q->where('nombre', 'like', "%{$busqueda}%")
              ->orWhere('descripcion', 'like', "%{$busqueda}%");
        });
    }
    if ($request->filled('precio_min')) {
        $query->where('precio', '>=', (float)$request->get('precio_min'));
    }
    if ($request->filled('precio_max')) {
        $query->where('precio', '<=', (float)$request->get('precio_max'));
    }

    // 3. Ordenamiento dinámico
    switch ($request->get('orden', 'recientes')) {
        case 'precio_asc':  $query->orderBy('precio', 'asc'); break;
        case 'precio_desc': $query->orderBy('precio', 'desc'); break;
        case 'nombre_asc':  $query->orderBy('nombre', 'asc'); break;
        default:            $query->latest(); break;
    }

    $products = $query->paginate(15)->withQueryString();

    // 4. Ofertas activas destacadas (Top 4)
    $ofertas = Oferta::where('estado', 'activa')
        ->whereHas('producto', fn($q) => $q->where('disponibilidad', 1))
        ->with(['producto.imagenes', 'producto.categoria'])
        ->latest()->take(4)->get();

    // IDs de favoritos del usuario para pintar el corazón activo
    $favoritosIds = Auth::check() ? Auth::user()->favoritos()->pluck('producto_id')->toArray() : [];

    return view('welcome', compact('products', 'categorias', 'ofertas', 'favoritosIds'));
}
```

#### 🔍 ¿Qué hace este código?
- **`withCount(['productos' => ...])`**: Cuenta únicamente los productos disponibles dentro de cada categoría para no mostrar categorías vacías.
- **`ofertaActiva`**: Carga automáticamente el precio promocional si el producto tiene una oferta vigente.
- **`Auth::user()->favoritos()->pluck('producto_id')`**: Extrae un arreglo simple de IDs (ej. `[1, 5, 12]`) que permite a la vista Blade saber al instante si debe pintar el corazón de favorito en rojo o en gris sin hacer consultas adicionales en cada iteración del bucle.
