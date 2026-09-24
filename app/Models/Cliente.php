<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'direccion',
        'alias',
        'persona_id',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    public function metodosPago()
    {
        return $this->hasMany(MetodoPago::class, 'cliente_id');
    }
}
