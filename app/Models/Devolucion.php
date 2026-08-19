<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'fecha_devolucion',
        'monto_devolucion',
        'motivo',
        'estado',
        'motivo_rechazo',
        'pedido_id',
    ];

    protected $casts = [
        'fecha_devolucion' => 'datetime',
        'monto_devolucion' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleDevolucion::class, 'devolucione_id');
    }
}
