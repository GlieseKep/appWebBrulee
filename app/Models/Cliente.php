<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    protected $connection = 'oracle';
    protected $table = 'CLIENTE';
    protected $primaryKey = 'CLI_CEDULA';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    // ✅ TODO en MAYÚSCULAS (columnas reales)
    protected $fillable = [
        'CLI_CEDULA',
        'EMP_CEDULA_RUC',
        'CLI_CORREO',
        'CLI_TELEFONO',
    ];

    /**
     * Relación correcta (claves reales)
     * CLIENTE.CLI_CEDULA <-> USUARIO_APP.CLI_CEDULA
     */
    public function usuarioApp()
    {
        return $this->hasOne(UsuarioApp::class, 'CLI_CEDULA', 'CLI_CEDULA');
    }

    public static function obtenerEmpresaCedulaRuc(): ?string
    {
        $row = DB::connection('oracle')
            ->table('EMPRESA')
            ->selectRaw('EMP_CEDULA_RUC AS EMP_CEDULA_RUC')
            ->first();

        if (!$row)
            return null;

        // Fallback por si el driver lo devuelve en minúsculas
        $val = $row->EMP_CEDULA_RUC ?? $row->emp_cedula_ruc ?? null;

        return $val !== null ? (string) $val : null;
    }


    /*
    |--------------------------------------------------------------------------
     | Accessors / Mutators (compatibles con tu código actual)
     | - Permiten leer/escribir usando minúsculas, pero guardan en MAYÚSCULAS reales.
     |--------------------------------------------------------------------------
     */

    // GET $cliente->cli_cedula
    public function getCliCedulaAttribute()
    {
        return $this->attributes['CLI_CEDULA'] ?? $this->attributes['cli_cedula'] ?? null;
    }

    // SET $cliente->cli_cedula = ...
    public function setCliCedulaAttribute($value)
    {
        $this->attributes['CLI_CEDULA'] = strtoupper(trim((string) $value));
    }

    public function getEmpCedulaRucAttribute()
    {
        return $this->attributes['EMP_CEDULA_RUC'] ?? $this->attributes['emp_cedula_ruc'] ?? null;
    }

    public function setEmpCedulaRucAttribute($value)
    {
        $this->attributes['EMP_CEDULA_RUC'] = strtoupper(trim((string) $value));
    }

    public function getCliCorreoAttribute()
    {
        return $this->attributes['CLI_CORREO'] ?? $this->attributes['cli_correo'] ?? null;
    }

    public function setCliCorreoAttribute($value)
    {
        $this->attributes['CLI_CORREO'] = $value !== null ? trim((string) $value) : null;
    }

    public function getCliTelefonoAttribute()
    {
        return $this->attributes['CLI_TELEFONO'] ?? $this->attributes['cli_telefono'] ?? null;
    }

    public function setCliTelefonoAttribute($value)
    {
        $this->attributes['CLI_TELEFONO'] = $value !== null ? trim((string) $value) : null;
    }
}
