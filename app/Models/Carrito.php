<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $connection = 'oracle';
    protected $table = 'CARRITO';
    protected $primaryKey = 'CAR_CODIGO';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'CAR_CODIGO',
        'CLI_CEDULA',
        'CAR_COMENTARIO',
        'CAR_UBICACION',
        'CAR_MONTO_TOTAL',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleCarrito::class, 'CAR_CODIGO', 'CAR_CODIGO');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_CEDULA', 'CLI_CEDULA');
    }
}
