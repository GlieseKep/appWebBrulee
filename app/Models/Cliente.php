<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    // Definir la tabla
    protected $table = 'clientes';

    // La clave primaria no es 'id', es 'cedula'
    protected $primaryKey = 'cedula';

    // La clave primaria no es autoincremental
    public $incrementing = false;

    // Tipo de la clave primaria
    protected $keyType = 'string';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'cedula',
        'correo',
        'telefono'
    ];

    // Campos ocultos (no se incluyen en JSON)
    protected $hidden = [];

    /**
     * Relación: Un cliente puede tener un usuario_app
     */
    public function usuarioApp()
    {
        return $this->hasOne(UsuarioApp::class, 'cedula_cliente', 'cedula');
    }
}
