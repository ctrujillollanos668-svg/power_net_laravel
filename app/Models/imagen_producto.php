<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class imagen_producto extends Model
{
    protected $table = 'imagenes_productos';

    protected $fillable = [
        'producto_id',
        'imagen',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
