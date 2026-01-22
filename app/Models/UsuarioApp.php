<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioApp extends Model
{
    protected $connection = 'oracle';
    protected $table = 'USUARIO_APP';
    protected $primaryKey = 'USR_NOMBRE';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    // ✅ Columnas reales en MAYÚSCULAS
    protected $fillable = [
        'USR_NOMBRE',
        'CLI_CEDULA',
        'USR_CONTRASENA',
    ];

    protected $hidden = [
        'USR_CONTRASENA',
    ];

    /**
     * USUARIO_APP.CLI_CEDULA -> CLIENTE.CLI_CEDULA
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_CEDULA', 'CLI_CEDULA');
    }

    /*
    |--------------------------------------------------------------------------
     | Accessors / Mutators compat
     |--------------------------------------------------------------------------
     */

    public function getUsrNombreAttribute()
    {
        return $this->attributes['USR_NOMBRE'] ?? $this->attributes['usr_nombre'] ?? null;
    }

    public function setUsrNombreAttribute($value)
    {
        $this->attributes['USR_NOMBRE'] = trim((string) $value);
    }

    public function getCliCedulaAttribute()
    {
        return $this->attributes['CLI_CEDULA'] ?? $this->attributes['cli_cedula'] ?? null;
    }

    public function setCliCedulaAttribute($value)
    {
        $this->attributes['CLI_CEDULA'] = strtoupper(trim((string) $value));
    }

    public function getUsrContrasenaAttribute()
    {
        return $this->attributes['USR_CONTRASENA'] ?? $this->attributes['usr_contrasena'] ?? null;
    }

    public function setUsrContrasenaAttribute($value)
    {
        // Guarda hash SHA1 como estás usando en tu app
        $this->attributes['USR_CONTRASENA'] = sha1((string) $value);
    }

    public function verificarContrasena($clave): bool
    {
        $guardada = trim((string) ($this->usr_contrasena ?? $this->USR_CONTRASENA ?? ''));
        return hash_equals($guardada, sha1((string) $clave));
    }
}
