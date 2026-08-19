<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDevolucion extends Model
{
    protected $table = 'detalles_devoluciones';

    protected $fillable = [
        'cantidad',
        'motivo',
        'devolucione_id',
        'producto_id',
    ];

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'devolucione_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
