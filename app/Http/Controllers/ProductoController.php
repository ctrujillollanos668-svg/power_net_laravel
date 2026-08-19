<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\imagen_producto;
use App\Models\Proveedor;

class ProductoController extends Controller
{
    /**
     * Mostrar productos.
     */
    public function index()
    {
        $productos = Producto::with([
            'categoria',
            'imagenes',
            'proveedor',
            'ofertas'
        ])->latest('id')->paginate(10);

        $categorias = Categoria::all();
        $proveedores = Proveedor::all();

        return view(
            'admin.producto.Productos',
            compact(
                'productos',
                'categorias',
                'proveedores'
            )
        );
    }

    /**
     * Guardar producto.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'stock' => 'required|integer|min:0',
            'disponibilidad' => 'required|in:0,1',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg,gif,avif,bmp|max:10240',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 100 caracteres.',
            'categoria_id.required' => 'Debes seleccionar una categoría válida.',
            'proveedor_id.required' => 'Debes seleccionar un proveedor.',
            'stock.required' => 'El stock es obligatorio y debe ser un número mayor o igual a 0.',
            'precio.required' => 'El precio de venta es obligatorio.',
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'imagenes.*.image' => 'Los archivos seleccionados deben ser imágenes válidas.',
            'imagenes.*.max' => 'Cada imagen no debe superar los 10MB.',
        ]);

        // Crear producto
        $producto = new Producto();
        $producto->nombre = $validated['nombre'];
        $producto->descripcion = $validated['descripcion'] ?? null;
        $producto->categoria_id = $validated['categoria_id'];
        $producto->proveedor_id = $validated['proveedor_id'];
        $producto->stock = $validated['stock'];
        $producto->disponibilidad = (bool)$validated['disponibilidad'];
        $producto->precio = $validated['precio'];
        $producto->precio_compra = $validated['precio_compra'];
        $producto->save();

        // Guardar imágenes
        if ($request->hasFile('imagenes')) {
            $destPath = public_path('imagenes_productos');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0777, true);
            }

            foreach ($request->file('imagenes') as $archivo) {
                if ($archivo->isValid()) {
                    $nombreImagen = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                    $archivo->move($destPath, $nombreImagen);

                    $imagen = new imagen_producto();
                    $imagen->producto_id = $producto->id;
                    $imagen->imagen = $nombreImagen;
                    $imagen->save();
                }
            }
        }

        return redirect()
            ->route('productos.index')
            ->with(
                'Mensaje',
                '¡Producto creado correctamente!'
            );
    }

    /**
     * Mostrar producto.
     */
    public function show(string $id)
    {
        $producto = Producto::with(['categoria', 'imagenes', 'proveedor', 'ofertas'])->findOrFail($id);
        return response()->json($producto);
    }

    /**
     * Actualizar producto.
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'stock' => 'required|integer|min:0',
            'disponibilidad' => 'required|in:0,1',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg,gif,avif,bmp|max:10240',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 100 caracteres.',
            'categoria_id.required' => 'Debes seleccionar una categoría válida.',
            'proveedor_id.required' => 'Debes seleccionar un proveedor.',
            'stock.required' => 'El stock es obligatorio.',
            'precio.required' => 'El precio de venta es obligatorio.',
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'imagenes.*.image' => 'Los archivos seleccionados deben ser imágenes válidas.',
            'imagenes.*.max' => 'Cada imagen no debe superar los 10MB.',
        ]);

        $producto->nombre = $validated['nombre'];
        $producto->descripcion = $validated['descripcion'] ?? null;
        $producto->categoria_id = $validated['categoria_id'];
        $producto->proveedor_id = $validated['proveedor_id'];
        $producto->stock = $validated['stock'];
        $producto->disponibilidad = (bool)$validated['disponibilidad'];
        $producto->precio = $validated['precio'];
        $producto->precio_compra = $validated['precio_compra'];
        $producto->save();

        // Agregar nuevas imágenes
        if ($request->hasFile('imagenes')) {
            $destPath = public_path('imagenes_productos');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0777, true);
            }

            foreach ($request->file('imagenes') as $archivo) {
                if ($archivo->isValid()) {
                    $nombreImagen = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                    $archivo->move($destPath, $nombreImagen);

                    $imagen = new imagen_producto();
                    $imagen->producto_id = $producto->id;
                    $imagen->imagen = $nombreImagen;
                    $imagen->save();
                }
            }
        }

        return redirect()
            ->route('productos.index')
            ->with(
                'Mensaje',
                '¡Producto actualizado correctamente!'
            );
    }

    /**
     * Eliminar producto.
     */
    public function destroy($id)
    {
        $producto = Producto::with('imagenes')->findOrFail($id);

        // Eliminar archivos físicos
        foreach ($producto->imagenes as $imagen) {
            $ruta = public_path('imagenes_productos/' . $imagen->imagen);
            if (file_exists($ruta)) {
                @unlink($ruta);
            }
        }

        // Eliminar registros de imágenes
        $producto->imagenes()->delete();

        // Eliminar ofertas asociadas si existen
        $producto->ofertas()->delete();

        // Eliminar producto
        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with(
                'Mensaje',
                'Producto eliminado correctamente.'
            );
    }

    /**
     * Eliminar una sola imagen de un producto.
     */
    public function eliminarImagen($id)
    {
        $imagen = imagen_producto::findOrFail($id);

        $ruta = public_path('imagenes_productos/' . $imagen->imagen);
        if (file_exists($ruta)) {
            @unlink($ruta);
        }

        $imagen->delete();

        return back()->with('Mensaje', 'Imagen eliminada correctamente.');
    }

    /**
     * Cambiar disponibilidad (activo/inactivo).
     */
    public function cambiarEstado($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->disponibilidad = !$producto->disponibilidad;
        $producto->save();

        $estadoTexto = $producto->disponibilidad ? 'activado' : 'desactivado';

        return redirect()
            ->route('productos.index')
            ->with('Mensaje', "Producto {$producto->nombre} {$estadoTexto} correctamente.");
    }
}
