<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;
use App\Models\imagen_producto;
use App\Models\proveedor;
class Producto extends Model
{
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

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function imagenes()
    {
        return $this->hasMany(imagen_producto::class, 'producto_id');
    }
    public function proveedor()
    {
        return $this->belongsTo(proveedor::class, 'proveedor_id');
    }

    public function ofertas()
    {
        return $this->hasMany(Oferta::class, 'producto_id');
    }

    public function ofertaActiva()
    {
        return $this->hasOne(Oferta::class, 'producto_id')
            ->where('estado', 'activa')
            ->latest();
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'producto_id');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(Inventario::class, 'producto_id')->latest();
    }
}
