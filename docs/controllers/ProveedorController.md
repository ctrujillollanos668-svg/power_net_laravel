# 🏭 ProveedorController

## 📍 Ubicación
`app/Http/Controllers/ProveedorController.php`

---

## 🎯 Propósito General
Gestiona la información de contacto y estado operativo de los proveedores de mercancía tecnológica para PowerNet.

---

## 🧩 Modelos y Dependencias
```php
use App\Models\proveedor;
use Illuminate\Http\Request;
```

---

## 🛠️ Explicación Detallada del Código por Método

### 1. `store(Request $request)` - Registro de Proveedor

#### 💻 Código Clave:
```php
public function store(Request $request)
{
    $request->validate([
        'nombre_proveedor' => 'required|string|max:100',
        'correo' => 'required|email|max:150',
        'telefono' => 'required|string|max:30',
        'estado' => 'required|in:0,1',
    ]);

    $proveedor = new proveedor();
    $proveedor->nombre_proveedor = $request->nombre_proveedor;
    $proveedor->correo = $request->correo;
    $proveedor->telefono = $request->telefono;
    $proveedor->estado = $request->estado;
    $proveedor->save();

    return redirect()->route('proveedores.index')->with('Mensaje', '¡Proveedor registrado correctamente!');
}
```

#### 🔍 ¿Qué hace este código?
- Valida los datos requeridos e inserta el nuevo registro en la tabla `proveedores` con estado activo o inactivo.

---

### 2. `destroy($id)` - Eliminación con Control Referencial

#### 💻 Código Clave:
```php
public function destroy($id)
{
    $proveedor = proveedor::withCount('productos')->findOrFail($id);
    
    // Bloquea el borrado si tiene productos registrados a su nombre
    if ($proveedor->productos_count > 0) {
        return redirect()->route('proveedores.index')
            ->with('Error', 'No se puede eliminar el proveedor porque tiene productos asociados.');
    }

    $proveedor->delete();
    return redirect()->route('proveedores.index')->with('Mensaje', 'Proveedor eliminado correctamente.');
}
```

#### 🔍 ¿Qué hace este código?
- Protege la integridad de la base de datos evitando eliminar un proveedor si todavía existen productos en bodega suministrados por él.
