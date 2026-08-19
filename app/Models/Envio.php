<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    protected $table = 'envios';

    protected $fillable = [
        'empresa_envios',
        'estado',
        'costo',
        'fecha_hora',
        'direccion_envio',
        'pedido_id',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'fecha_hora' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
