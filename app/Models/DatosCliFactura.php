<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosCliFactura extends Model
{
    use HasFactory;

    protected $table = 'DATOS_CLI_FACTURA';
    protected $primaryKey = 'DCF_CODIGO';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'DCF_CODIGO',
        'CLI_CEDULA',
        'DCF_NOMBRE',
        'DCF_DIRECCION',
        'DCF_METODO_PAGO',
        'DCF_NUMERO_TARJETA',
        'DCF_MES_CADUCIDAD',
        'DCF_YEAR_CADUCIDAD',
        'DCF_CODIGO_SEGURIDAD',
        'DCF_ACEPTA_TERMINOS'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_CEDULA', 'cedula');
    }
}
