# 📦 ProductoController

## 📍 Ubicación
`app/Http/Controllers/ProductoController.php`

---

## 🎯 Propósito General
Gestiona el catálogo de productos de PowerNet. Administra la creación, edición, eliminación, control de existencias, carga y guardado físico de múltiples imágenes por producto, y asignación de proveedores y categorías.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\imagen_producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `index()` - Listado de Productos en Panel Admin

#### 💻 Código Clave:
```php
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

    return view('admin.producto.Productos', compact('productos', 'categorias', 'proveedores'));
}
```

#### 🔍 ¿Qué hace este código?
- **`Producto::with([...])`**: Carga de forma eficiente todas las fotos, la categoría, el proveedor y las ofertas de cada producto en una sola consulta.
- **`paginate(10)`**: Limita a 10 productos por página para mantener alta velocidad de carga.
- Carga las listas completas de `$categorias` y `$proveedores` para poblar los selectores (`<select>`) de los formularios modales de creación y edición.

---

### 2. `store(Request $request)` - Registro y Carga Múltiple de Imágenes

#### 💻 Código Clave:
```php
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
    ]);

    // 1. Guardar datos principales del producto
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

    // 2. Procesar y guardar múltiples imágenes en disco y base de datos
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

    return redirect()->route('productos.index')->with('Mensaje', '¡Producto creado correctamente!');
}
```

#### 🔍 ¿Qué hace este código?
- **Validaciones**: Verifica que la categoría y proveedor existan en sus respectivas tablas (`exists:categorias,id`), que el stock sea $\ge 0$ y que los archivos sean imágenes válidas de máx 10MB (`10240 KB`).
- **Manejo Seguro de Archivos**: Crea automáticamente el directorio `public/imagenes_productos/` si no existe. Genera nombres únicos con `uniqid()` para evitar sobreescritura.
- **Relación 1:N**: Por cada imagen subida, crea un registro en `imagen_productos` vinculado al `$producto->id`.

---

### 3. `destroy($id)` - Eliminación y Limpieza en Disco

#### 💻 Código Clave:
```php
public function destroy(string $id)
{
    $producto = Producto::with('imagenes')->findOrFail($id);

    // Borrado físico de imágenes en el servidor
    foreach ($producto->imagenes as $imagen) {
        $rutaArchivo = public_path('imagenes_productos/' . $imagen->imagen);
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }
        $imagen->delete();
    }

    $producto->delete();
    return redirect()->route('productos.index')->with('Mensaje', '¡Producto e imágenes eliminados correctamente!');
}
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este controlador en una sustentación?
> *"El `ProductoController` gestiona el catálogo de PowerNet implementando el patrón CRUD. En el método `store`, además de validar tipos y llaves foráneas (`categoria_id`, `proveedor_id`), procesa múltiples archivos binarios con `move()` guardándolos en `public/imagenes_productos/` y asociando sus nombres a la tabla `imagen_productos`. Al eliminar un producto con `destroy`, ejecuta un `unlink()` para liberar espacio en disco del servidor."*

### 2. Tablas y campos afectados en MySQL:
- **`productos`**: `nombre`, `descripcion`, `categoria_id`, `proveedor_id`, `stock`, `disponibilidad`, `precio`, `precio_compra`.
- **`imagen_productos`**: `producto_id`, `imagen` (nombre del archivo).
- **`categorias`** y **`proveedores`**: Tablas padre referenciadas por ID.

### 3. Posibles errores y soluciones:
- **Error: "The imagenes.* failed to upload" o pantalla blanca al subir imagen**: La imagen supera el límite de PHP. Revisa `upload_max_filesize` en `php.ini` o valida que el formulario en Blade tenga `enctype="multipart/form-data"`.
- **Las imágenes no cargan en el navegador**: Verifica que la carpeta física exista en `public/imagenes_productos/` y que en Blade se use `asset('imagenes_productos/' . $foto)`.
