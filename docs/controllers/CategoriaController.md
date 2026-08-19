# 🏷️ CategoriaController

## 📍 Ubicación
`app/Http/Controllers/CategoriaController.php`

---

## 🎯 Propósito General
Administra las familias o categorías de productos tecnológicos (redes, conectores, routers, fibra óptica). Controla la creación, edición, borrado seguro con protección de integridad referencial y alternado de estado activo/inactivo.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\Categoria;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `destroy($id)` - Eliminación Segura con Protección de Integridad

#### 💻 Código Clave:
```php
public function destroy($id)
{
    $categoria = Categoria::withCount('productos')->findOrFail($id);

    // Evita borrar categorías que tienen productos vinculados
    if ($categoria->productos_count > 0) {
        return redirect()
            ->route('categorias.index')
            ->with('Error', 'No se puede eliminar la categoría porque tiene productos asociados.');
    }

    $categoria->delete();

    return redirect()
        ->route('categorias.index')
        ->with('Mensaje', 'Categoría eliminada correctamente.');
}
```

#### 🔍 ¿Qué hace este código?
- **`withCount('productos')`**: Cuenta de forma eficiente los productos de la categoría; si `productos_count > 0`, rechaza la acción para evitar violaciones de clave foránea en la base de datos o dejar artículos huérfanos.

---

### 2. `cambiarEstado($id)` - Toggle Switch Activo/Inactivo

#### 💻 Código Clave:
```php
public function cambiarEstado($id)
{
    $categoria = Categoria::findOrFail($id);
    $categoria->estado = !$categoria->estado;
    $categoria->save();

    if (request()->wantsJson() || request()->ajax()) {
        return response()->json([
            'success' => true,
            'estado' => (bool)$categoria->estado,
            'mensaje' => 'Estado de la categoría actualizado correctamente.'
        ]);
    }

    return redirect()->route('categorias.index')
        ->with('Mensaje', 'Estado de la categoría actualizado correctamente.');
}
```

#### 🔍 ¿Qué hace este código?
- **`!$categoria->estado`**: Invierte el valor booleano (`1` pasa a `0` y viceversa) para habilitar o deshabilitar una categoría completa del catálogo en un solo clic.
