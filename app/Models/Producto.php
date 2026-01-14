<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'PRODUCTOS';
    protected $fillable = [
        'nombre',
        'descripcion',
        'precioVenta',
        'imagen',
        'imagenAlt'
    ];
    static public function getProductos()
    {
        return Producto::all();
    }
}
