<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $connection = 'oracle';
    protected $table = 'PRODUCTO';

    protected $primaryKey = 'PRO_CODIGO';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'PRO_CODIGO',
        'CAT_CODIGO',
        'PRO_NOMBRE',
        'PRO_DESCRIPCION',
        'PRO_PRECIO_VENTA',
        'PRO_IMAGEN',
        'PRO_ALT_IMAGEN',
        'PRO_ESTADO',
    ];

    public function getPROCODIGOAttribute()
    {
        return $this->attributes['PRO_CODIGO'] ?? $this->attributes['pro_codigo'] ?? null;
    }

    public function getCATCODIGOAttribute()
    {
        return $this->attributes['CAT_CODIGO'] ?? $this->attributes['cat_codigo'] ?? null;
    }

    public function getPRONOMBREAttribute()
    {
        return $this->attributes['PRO_NOMBRE'] ?? $this->attributes['pro_nombre'] ?? null;
    }

    public function getPRODESCRIPCIONAttribute()
    {
        return $this->attributes['PRO_DESCRIPCION'] ?? $this->attributes['pro_descripcion'] ?? null;
    }

    public function getPROPRECIOVENTAAttribute()
    {
        return $this->attributes['PRO_PRECIO_VENTA'] ?? $this->attributes['pro_precio_venta'] ?? null;
    }

    public function getPROIMAGENAttribute()
    {
        return $this->attributes['PRO_IMAGEN'] ?? $this->attributes['pro_imagen'] ?? null;
    }

    public function getPROALTIMAGENAttribute()
    {
        return $this->attributes['PRO_ALT_IMAGEN'] ?? $this->attributes['pro_alt_imagen'] ?? null;
    }


    public function getIdAttribute() { return $this->PRO_CODIGO; }
    public function getNombreAttribute() { return $this->PRO_NOMBRE; }
    public function getDescripcionAttribute() { return $this->PRO_DESCRIPCION; }
    public function getPrecioAttribute() { return $this->PRO_PRECIO_VENTA; }
    public function getImagenAttribute() { return $this->PRO_IMAGEN; }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'CAT_CODIGO', 'CAT_CODIGO');
    }

    public function scopeActivos($q)
    {
        return $q;
    }
    // para ver imagenes
    // Devuelve el URL correcto para <img src="{{ $producto->imagen_url }}">
    public function getImagenUrlAttribute(): string
    {
        $img = $this->attributes['PRO_IMAGEN'] ?? $this->attributes['pro_imagen'] ?? null;

        if (!$img) return '';

        $img = trim((string)$img);

        // Si ya es URL absoluta (Cloudinary, http/https), úsalo tal cual
        if (preg_match('/^https?:\/\//i', $img)) {
            return $img;
        }

        // Si no es absoluta, asume ruta local en public/
        return asset(ltrim($img, '/'));
    }

    // Para que el alt funcione igual (tu blade usa $producto->alt_imagen ?? $producto->nombre)
    public function getAltImagenAttribute(): ?string
    {
        return $this->attributes['PRO_ALT_IMAGEN'] ?? $this->attributes['pro_alt_imagen'] ?? null;
    }
    // STOCK desde Oracle: PRO_EXISTENCIA / pro_existencia
    public function getStockAttribute(): int
    {
        $val = $this->attributes['PRO_EXISTENCIA'] ?? $this->attributes['pro_existencia'] ?? null;
        if ($val === null || $val === '') return 0;
        return (int)$val;
    }

    // Para que el blade pueda decidir agotado si usa $producto->agotado
    public function getAgotadoAttribute(): bool
    {
        return $this->stock <= 0;
    }


}
