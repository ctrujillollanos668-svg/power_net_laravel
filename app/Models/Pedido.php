<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'fecha_pedido',
        'total_pedido',
        'estado_pedido',
        'cliente_id',
    ];

    protected $casts = [
        'fecha_pedido' => 'datetime',
        'total_pedido' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function envio()
    {
        return $this->hasOne(Envio::class, 'pedido_id');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'pedido_id');
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'pedido_id');
    }
}
