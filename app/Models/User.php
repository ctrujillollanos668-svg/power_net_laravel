<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'role_id',
    'name',
    'apellido',
    'email',
    'password',
    'telefono',
    'direccion',
    'estado',
    'created_by'
])]

#[Hidden([
    'password',
    'remember_token'
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Casts
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Rol del usuario
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Usuario creador
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Usuarios creados por este usuario
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    // Registros de favoritos del usuario
    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'user_id');
    }

    // Productos favoritos del usuario (Many-to-Many)
    public function productosFavoritos()
    {
        return $this->belongsToMany(Producto::class, 'favoritos', 'user_id', 'producto_id')
                    ->withTimestamps();
    }
}