<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre_proveedor',
        'correo',
        'telefono',
        'estado'
    ];
    public function productos()
    {
        return $this->hasMany(Producto::class, 'proveedor_id');
    }
}
