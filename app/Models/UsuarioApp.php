<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioApp extends Model
{
    use HasFactory;

    protected $table = 'usuarios_app';

    protected $fillable = [
        'nombre',
        'cedula_cliente',
        'contrasena',
        'activo'
    ];

    protected $hidden = [
        'contrasena'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    /**
     * Relación: Un usuario_app pertenece a un cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cedula_cliente', 'cedula');
    }

    /**
     * Hashear contraseña automáticamente al asignarla
     */
    public function setContrasenaAttribute($value)
    {
        $this->attributes['contrasena'] = \Hash::make($value);
    }

    /**
     * Verificar contraseña
     */
    public function verificarContrasena($clave)
    {
        return \Hash::check($clave, $this->contrasena);
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
