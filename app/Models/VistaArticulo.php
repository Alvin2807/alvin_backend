<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaArticulo extends Model
{
    use HasFactory;

    public     $table        = "vw_articulos";
    protected  $primaryKey   = "id_articulo";
    protected  $fillable     = ['id_articulo','codigo','descripcion','marca','modelo','cantidad_pedida','cantidad_almacen',
    'cantidad_stock','fk_unidad_medida','unidad_de_medida','cantidad_minima','ultimo_precio','periodo_grantia', 'garantia','fk_marca',
    'fk_modelo','fk_grupo','fk_sub_grupo','fk_tipo_uso','fk_categoria','requiere_activo','categoria','grupo','sub_grupo'];
    public     $incrementing = true;
    public     $timestamps   = false;

    protected $casts = [
        
        'id_articulo'      => 'integer',
        'codigo'           => 'integer',
        'descripcion'      => 'string',
        'marca'            => 'string',
        'modelo'           => 'string',
        'cantidad_pedida'  => 'integer',
        'cantidad_almacen' => 'integer',
        'cantidad_stock'   => 'integer',
        'cantidad_minima'  => 'integer',
        'fk_unidad_medida' => 'integer',
        'unidad_de_medida' => 'string',
        'garantia'         => 'integer',
        'periodo_gantia'   => 'string',
        'ultimo_recio'     => 'float',
        'fk_marca'         => 'integer',
        'fk_modelo'        => 'integer',
        'fk_grupo'         => 'integer',
        'fk_sub_grupo'     => 'integer',
        'fk_categoria'     => 'integer',
        'fk_tipo_uso'      => 'integer',
        'categoria'        => 'string',
        'grupo'            => 'string',
        'sub_grupo'        => 'string',
        'requiere_activo'  => 'string'
        
    ];
}
