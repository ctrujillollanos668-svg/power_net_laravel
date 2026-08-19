<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'nombre_persona',
        'telefono',
        'documento',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'persona_id');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'persona_id');
    }
}
