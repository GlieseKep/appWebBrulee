<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCarrito extends Model
{
    protected $connection = 'oracle';
    protected $table = 'DETALLE_CARRITO';

    // PK compuesta (PRO_CODIGO, CAR_CODIGO) => Eloquent no maneja PK compuesta directo
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'PRO_CODIGO',
        'CAR_CODIGO',
        'DCT_CANTIDAD',
        'DCT_MONTO',
    ];

    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'CAR_CODIGO', 'CAR_CODIGO');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_CODIGO', 'PRO_CODIGO');
    }
}
