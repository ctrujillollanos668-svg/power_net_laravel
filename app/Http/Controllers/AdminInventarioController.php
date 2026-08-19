<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminInventarioController extends Controller
{
    /**
     * Muestra la vista principal del Módulo de Inventario con métricas, filtros y tabla paginada.
     */
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'proveedor', 'imagenes', 'ofertaActiva']);

        // 1. Filtro por Búsqueda (Nombre, ID, Categoría, Proveedor)
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%{$q}%")
                    ->orWhere('nombre', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%")
                    ->orWhereHas('categoria', function ($c) use ($q) {
                        $c->where('nombre_categoria', 'like', "%{$q}%");
                    })
                    ->orWhereHas('proveedor', function ($p) use ($q) {
                        $p->where('nombre_proveedor', 'like', "%{$q}%");
                    });
            });
        }

        // 2. Filtro por Nivel de Stock
        if ($request->filled('nivel')) {
            switch ($request->nivel) {
                case 'agotado':
                    $query->where('stock', '<=', 0);
                    break;
                case 'bajo':
                    $query->where('stock', '>', 0)->where('stock', '<=', 5);
                    break;
                case 'optimo':
                    $query->where('stock', '>', 5);
                    break;
            }
        }

        // 3. Filtro por Categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // 4. Filtro por Proveedor
        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        // 5. Filtro por Disponibilidad / Estado
        if ($request->filled('disponibilidad') && $request->disponibilidad !== 'todos') {
            $query->where('disponibilidad', $request->disponibilidad);
        }

        // 6. Ordenamiento
        $sort = $request->get('sort', 'id_desc');
        switch ($sort) {
            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            case 'precio_desc':
                $query->orderBy('precio', 'desc');
                break;
            case 'precio_asc':
                $query->orderBy('precio', 'asc');
                break;
            case 'nombre_asc':
                $query->orderBy('nombre', 'asc');
                break;
            default:
                $query->latest('id');
                break;
        }

        // Paginación obligatoria de 10 en 10
        $productos = $query->paginate(10)->withQueryString();

        // ========== MÉTRICAS GLOBALES DEL ALMACÉN ==========
        $totalReferencias = Producto::count();
        $unidadesTotales = Producto::sum('stock');
        
        // Inversión en almacén (Precio de Compra * Stock)
        $valorInventarioCosto = Producto::selectRaw('SUM(stock * precio_compra) as total')->value('total') ?? 0;
        // Valoración a precio de venta estimado
        $valorInventarioVenta = Producto::selectRaw('SUM(stock * precio) as total')->value('total') ?? 0;
        
        $stockAgotado = Producto::where('stock', '<=', 0)->count();
        $stockCritico = Producto::where('stock', '>', 0)->where('stock', '<=', 5)->count();
        $stockOptimo = Producto::where('stock', '>', 5)->count();

        // Margen Bruto Potencial del inventario
        $margenPotencial = $valorInventarioVenta > 0 
            ? round((($valorInventarioVenta - $valorInventarioCosto) / $valorInventarioVenta) * 100, 1) 
            : 0;

        // Listas auxiliares para selectores y modales
        $categorias = Categoria::orderBy('nombre_categoria')->get();
        $proveedores = proveedor::orderBy('nombre_proveedor')->get();
        $todosProductos = Producto::select('id', 'nombre', 'stock', 'precio', 'precio_compra')
            ->orderBy('nombre')
            ->get();

        // Movimientos recientes (Kardex)
        $movimientosRecientes = Inventario::with(['producto.imagenes', 'pedido'])
            ->latest()
            ->take(15)
            ->get();

        return view('admin.inventario.index', compact(
            'productos',
            'totalReferencias',
            'unidadesTotales',
            'valorInventarioCosto',
            'valorInventarioVenta',
            'stockAgotado',
            'stockCritico',
            'stockOptimo',
            'margenPotencial',
            'categorias',
            'proveedores',
            'todosProductos',
            'movimientosRecientes'
        ));
    }

    /**
     * Registra un movimiento o ajuste manual de stock (Entrada / Salida).
     */
    public function ajustarStock(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:100',
        ], [
            'producto_id.required' => 'Debes seleccionar un producto.',
            'tipo.required' => 'Selecciona el tipo de movimiento (Entrada o Salida).',
            'cantidad.min' => 'La cantidad debe ser al menos 1 unidad.',
            'motivo.required' => 'Indica el motivo del ajuste o movimiento.',
        ]);

        $producto = Producto::findOrFail($request->producto_id);
        $stockAnterior = (int)$producto->stock;
        $cantidad = (int)$request->cantidad;

        if ($request->tipo === 'entrada') {
            $stockNuevo = $stockAnterior + $cantidad;
        } else {
            if ($cantidad > $stockAnterior) {
                return redirect()->back()->withErrors([
                    'cantidad' => "No puedes retirar {$cantidad} unidades. El stock actual es de sólo {$stockAnterior} unidades."
                ])->withInput();
            }
            $stockNuevo = $stockAnterior - $cantidad;
        }

        // Actualizar el stock del producto
        $producto->stock = $stockNuevo;
        $producto->save();

        // Registrar en la tabla inventarios
        Inventario::create([
            'producto_id' => $producto->id,
            'tipo' => $request->tipo,
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
            'motivo' => $request->motivo,
            'pedido_id' => null,
        ]);

        $tipoTexto = $request->tipo === 'entrada' ? 'Entrada (+)' : 'Salida (-)';

        return redirect()->route('admin.inventario.index')
            ->with('Mensaje', "¡{$tipoTexto} de {$cantidad} unid. para \"{$producto->nombre}\" registrada con éxito! Nuevo stock: {$stockNuevo} unid.");
    }

    /**
     * Actualiza rápidamente costos y precios de un producto desde el inventario.
     */
    public function actualizarPrecios(Request $request, $id)
    {
        $request->validate([
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
        ]);

        $producto = Producto::findOrFail($id);
        $producto->precio = $request->precio;
        $producto->precio_compra = $request->precio_compra;
        $producto->save();

        return redirect()->route('admin.inventario.index')
            ->with('Mensaje', "¡Precios de \"{$producto->nombre}\" actualizados correctamente!");
    }

    /**
     * Vista completa de Kardex / Historial de Movimientos de Inventario.
     */
    public function movimientos(Request $request)
    {
        $query = Inventario::with(['producto.imagenes', 'producto.categoria', 'pedido']);

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('motivo', 'like', "%{$q}%")
                    ->orWhere('id', 'like', "%{$q}%")
                    ->orWhereHas('producto', function ($p) use ($q) {
                        $p->where('nombre', 'like', "%{$q}%")
                          ->orWhere('id', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $query->where('tipo', $request->tipo);
        }

        $movimientos = $query->latest()->paginate(10)->withQueryString();

        $totalEntradas = Inventario::where('tipo', 'entrada')->sum('cantidad');
        $totalSalidas = Inventario::where('tipo', 'salida')->sum('cantidad');

        return view('admin.inventario.movimientos', compact('movimientos', 'totalEntradas', 'totalSalidas'));
    }
}
