<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pagos';

    protected $fillable = [
        'nombre',
        'tipo',
        'numero',
        'titular',
        'instrucciones',
        'estado',
        'cliente_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
