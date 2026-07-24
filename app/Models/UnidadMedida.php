<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    use HasFactory;

    public      $table          = "inv_unidad_medidas";
    protected   $primaryKey     = "id_unidad_medida";
    protected   $fillable       = ['id_unidad_medida','descripcion', 'factor_unidad'];
    public      $incrementing   = true;
    public      $timestamps     = false;

    protected $casts = [
        
        'id_unidad_medida' => 'integer',
        'descripcion'      => 'string',
        'factor_unidad'    => 'integer'
    ];

    public function articulos()
    {
        return $this->hasMany(Articulos::class, 'fk_unidad_medida', 'id_unidad_medida');
    }

}
