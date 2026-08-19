# 📦 Modelo: Producto

## 📍 Ubicación
`app/Models/Producto.php`

---

## 🎯 Propósito General
Representa la entidad central del catálogo de PowerNet en la base de datos. Mapea la tabla `productos` y gestiona sus relaciones con categorías, fotos, proveedores, promociones, existencias de inventario (Kardex) y lista de favoritos.

---

## 🗄️ Estructura de Base de Datos y Atributos

```php
protected $table = 'productos';

protected $fillable = [
    'nombre',
    'descripcion',
    'categoria_id',
    'stock',
    'disponibilidad',
    'precio',
    'precio_compra',
    'proveedor_id',
];
```

---

## 🔗 Relaciones Eloquent y Trazabilidad

```php
// 1. Un producto pertenece a una sola Categoría (N:1)
public function categoria()
{
    return $this->belongsTo(Categoria::class, 'categoria_id');
}

// 2. Un producto tiene múltiples fotos en la galería (1:N)
public function imagenes()
{
    return $this->hasMany(imagen_producto::class, 'producto_id');
}

// 3. Un producto es suministrado por un Proveedor (N:1)
public function proveedor()
{
    return $this->belongsTo(proveedor::class, 'proveedor_id');
}

// 4. Un producto puede tener historial de ofertas (1:N)
public function ofertas()
{
    return $this->hasMany(Oferta::class, 'producto_id');
}

// 5. Oferta activa vigente en el momento actual (1:1)
public function ofertaActiva()
{
    return $this->hasOne(Oferta::class, 'producto_id')
        ->where('estado', 'activa')
        ->latest();
}

// 6. Lista de favoritos / Wishlist (1:N)
public function favoritos()
{
    return $this->hasMany(Favorito::class, 'producto_id');
}

// 7. Kardex / Historial de entradas y salidas de almacén (1:N)
public function movimientosInventario()
{
    return $this->hasMany(Inventario::class, 'producto_id')->latest();
}
```

---

## 🔄 Diagrama de Trazabilidad del Modelo

```
     [Categoria]                  [Proveedor]
          ↑ (categoria_id)             ↑ (proveedor_id)
          |                            |
          +---------- [Producto] ------+
                         |
       +-----------------+-----------------+-----------------+
       ↓ (1:N)           ↓ (1:N)           ↓ (1:1 activa)    ↓ (1:N)
[imagen_producto]   [DetallePedido]     [Oferta]         [Inventario]
```

---

## 🛠️ Guía de Diagnóstico, Sustentación y Reparación

### 1. ¿Cómo explicar este Modelo en una sustentación?
> *"El modelo `Producto` es la entidad principal del e-commerce. Implementa relaciones de cardinalidad $1:N$ con `imagen_producto` para soportar galerías fotográficas múltiples y con `Inventario` para el registro de Kardex. Además, incluye la relación especializada `ofertaActiva()`, que filtra mediante un `hasOne` con condición `where('estado', 'activa')` para resolver precios en promoción de forma instantánea sin lógica repetitiva en las vistas."*

### 2. ¿Qué pasa si algo se daña y cómo solucionarlo?
- **Error: "MassAssignmentException" al guardar un producto**: Ocurre si intentas hacer `Producto::create($request->all())` con un campo que no esté listado en el arreglo `$fillable`.
  - *Solución:* Agrega el nombre del campo a la propiedad `protected $fillable`.
- **Error: "Call to a member function first() on null" en las fotos**: Ocurre si llamas a `$producto->imagenes->first()->imagen` en un producto que no tiene ninguna foto asociada.
  - *Solución:* En Blade, valida siempre con `optional($producto->imagenes->first())->imagen ?? 'placeholder.png'`.
- **Error de Clave Foránea (`foreign key constraint fails`)**: Si intentas guardar un producto con un `categoria_id` o `proveedor_id` que no exista en la base de datos.
