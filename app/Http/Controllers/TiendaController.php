<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Oferta;

class TiendaController extends Controller
{
    /**
     * Retorna los IDs de productos favoritos del usuario autenticado
     */
    private function getFavoritosIds()
    {
        return Auth::check() ? Auth::user()->favoritos()->pluck('producto_id')->toArray() : [];
    }

    /**
     * Página de Inicio de la Tienda (Welcome) con Filtro Directo
     */
    public function index(Request $request)
    {
        // Categorías activas con conteo de productos disponibles
        $categorias = Categoria::where('estado', 1)
            ->withCount(['productos' => function ($query) {
                $query->where('disponibilidad', 1);
            }])
            ->get();

        // Query principal de productos disponibles
        $query = Producto::where('disponibilidad', 1)
            ->with(['imagenes', 'categoria', 'ofertaActiva']);

        // Filtro por categoría seleccionada
        $categoriaSeleccionada = null;
        if ($request->filled('categoria')) {
            $categoriaId = $request->get('categoria');
            $query->where('categoria_id', $categoriaId);
            $categoriaSeleccionada = Categoria::find($categoriaId);
        }

        // Filtro por término de búsqueda
        if ($request->filled('q')) {
            $busqueda = $request->get('q');
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('descripcion', 'like', "%{$busqueda}%");
            });
        }

        // Filtros de precio
        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', (float)$request->get('precio_min'));
        }
        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', (float)$request->get('precio_max'));
        }

        // Ordenamiento
        $orden = $request->get('orden', 'recientes');
        switch ($orden) {
            case 'precio_asc':
                $query->orderBy('precio', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('precio', 'desc');
                break;
            case 'nombre_asc':
                $query->orderBy('nombre', 'asc');
                break;
            case 'recientes':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(15)->withQueryString();
        $productCount = $products->total();

        // Ofertas activas destacadas
        $ofertas = Oferta::where('estado', 'activa')
            ->whereHas('producto', function ($query) {
                $query->where('disponibilidad', 1);
            })
            ->with(['producto.imagenes', 'producto.categoria'])
            ->latest()
            ->take(4)
            ->get();

        $favoritosIds = $this->getFavoritosIds();

        return view('welcome', compact('products', 'productCount', 'categorias', 'ofertas', 'favoritosIds', 'categoriaSeleccionada'));
    }

    /**
     * Catálogo General de Productos con Filtros
     */
    public function catalogo(Request $request)
    {
        $query = Producto::where('disponibilidad', 1)
            ->with(['imagenes', 'categoria', 'ofertaActiva']);

        // Filtro por término de búsqueda
        if ($request->filled('q')) {
            $busqueda = $request->get('q');
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('descripcion', 'like', "%{$busqueda}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->get('categoria'));
        }

        // Filtro por precio mínimo
        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', (float)$request->get('precio_min'));
        }

        // Filtro por precio máximo
        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', (float)$request->get('precio_max'));
        }

        // Ordenamiento
        $orden = $request->get('orden', 'recientes');
        switch ($orden) {
            case 'precio_asc':
                $query->orderBy('precio', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('precio', 'desc');
                break;
            case 'nombre_asc':
                $query->orderBy('nombre', 'asc');
                break;
            case 'recientes':
            default:
                $query->latest();
                break;
        }

        $productos = $query->paginate(15)->withQueryString();

        $categorias = Categoria::where('estado', 1)
            ->withCount(['productos' => function ($q) {
                $q->where('disponibilidad', 1);
            }])
            ->get();

        $totalResultados = $productos->total();
        $favoritosIds = $this->getFavoritosIds();

        return view('cliente.catalogo.Catalogo', compact('productos', 'categorias', 'totalResultados', 'favoritosIds'));
    }

    /**
     * Catálogo filtrado directamente por Categoría
     */
    public function porCategoria($id)
    {
        $categoriaActual = Categoria::findOrFail($id);

        $productos = Producto::where('disponibilidad', 1)
            ->where('categoria_id', $id)
            ->with(['imagenes', 'categoria', 'ofertaActiva'])
            ->latest()
            ->paginate(15);

        $categorias = Categoria::where('estado', 1)
            ->withCount(['productos' => function ($q) {
                $q->where('disponibilidad', 1);
            }])
            ->get();

        $totalResultados = $productos->total();
        $favoritosIds = $this->getFavoritosIds();

        return view('cliente.catalogo.Catalogo', compact('productos', 'categorias', 'categoriaActual', 'totalResultados', 'favoritosIds'));
    }

    /**
     * Detalle de un Producto individual (show)
     */
    public function show($id)
    {
        $producto = Producto::where('disponibilidad', 1)
            ->with(['imagenes', 'categoria', 'proveedor', 'ofertaActiva'])
            ->findOrFail($id);

        // Productos relacionados en la misma categoría
        $relacionados = Producto::where('disponibilidad', 1)
            ->where('categoria_id', $producto->categoria_id)
            ->where('id', '!=', $producto->id)
            ->with(['imagenes', 'categoria', 'ofertaActiva'])
            ->take(4)
            ->get();

        $esFavorito = Auth::check() ? Auth::user()->favoritos()->where('producto_id', $producto->id)->exists() : false;
        $favoritosIds = $this->getFavoritosIds();

        return view('cliente.producto.Detalle', compact('producto', 'relacionados', 'esFavorito', 'favoritosIds'));
    }

    /**
     * Zona de Ofertas y Promociones
     */
    public function ofertas()
    {
        $ofertas = Oferta::where('estado', 'activa')
            ->whereHas('producto', function ($query) {
                $query->where('disponibilidad', 1);
            })
            ->with(['producto.imagenes', 'producto.categoria'])
            ->latest()
            ->paginate(12);

        $categorias = Categoria::where('estado', 1)->get();
        $favoritosIds = $this->getFavoritosIds();

        return view('cliente.oferta.Oferta', compact('ofertas', 'categorias', 'favoritosIds'));
    }

    /**
     * Búsqueda rápida desde la barra superior
     */
    public function buscar(Request $request)
    {
        return $this->catalogo($request);
    }
}
