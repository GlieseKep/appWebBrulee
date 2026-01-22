<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $connection = 'oracle';
    protected $table = 'CATEGORIA';

    
    protected $primaryKey = 'CAT_CODIGO';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'CAT_CODIGO',
        'CAT_DESCRIPCION',
    ];

    // === Bridge: si el driver hidrata en minúsculas, lo exponemos en MAYÚSCULAS ===
    public function getCATCODIGOAttribute()
    {
        return $this->attributes['CAT_CODIGO'] ?? $this->attributes['cat_codigo'] ?? null;
    }

    public function getCATDESCRIPCIONAttribute()
    {
        return $this->attributes['CAT_DESCRIPCION'] ?? $this->attributes['cat_descripcion'] ?? null;
    }

    // Mantener blades sin tocar
    public function getIdAttribute()
    {
        return $this->CAT_CODIGO;
    }

    public function getNombreAttribute()
    {
        return $this->CAT_DESCRIPCION;
    }

    public function getDescripcionAttribute()
    {
        return null;
    }

    public function productos()
    {
        // Columnas en MAYÚSCULAS
        return $this->hasMany(Producto::class, 'CAT_CODIGO', 'CAT_CODIGO');
    }
}
