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
                // Genera nombre único y seguro
                $nombreImagen = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                $archivo->move($destPath, $nombreImagen);

                // Guarda la relación en la tabla imagen_productos
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
- **Validaciones Rigurosas**: Verifica que la categoría y proveedor existan en sus respectivas tablas (`exists:categorias,id`), que el stock sea $\ge 0$ y que los archivos sean imágenes válidas de máximo 10MB (`10240 KB`).
- **Manejo Seguro de Archivos**: Crea automáticamente el directorio `public/imagenes_productos/` si no existe. Genera nombres de archivo únicos con `uniqid()` para evitar sobreescrituras si dos usuarios suben fotos con el mismo nombre.
- **Relación 1 a Muchos (`1:N`)**: Por cada imagen subida, crea un registro en `imagen_productos` vinculado al `$producto->id`.

---

### 3. `show($id)` - Consulta JSON para Modales

#### 💻 Código Clave:
```php
public function show(string $id)
{
    $producto = Producto::with(['categoria', 'imagenes', 'proveedor', 'ofertas'])->findOrFail($id);
    return response()->json($producto);
}
```

#### 🔍 ¿Qué hace este código?
- Retorna el objeto del producto con todas sus relaciones en formato JSON. Se utiliza desde JavaScript para abrir modales de edición o vista previa instantánea sin tener que recargar toda la página web.

---

### 4. `destroy($id)` - Eliminación y Limpieza en Disco

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

#### 🔍 ¿Qué hace este código?
- **`unlink($rutaArchivo)`**: Elimina los archivos de imagen físicos del servidor para no dejar imágenes "huérfanas" que consuman espacio en disco antes de borrar el producto de la base de datos.
