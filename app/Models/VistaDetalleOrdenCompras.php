<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaDetalleOrdenCompras extends Model
{
    use HasFactory;

    public    $table    =   "VW_DETALLE_ORDEN_COMPRAS";
    protected $fillable =   ['id_detalle','item','fk_articulo','codigo','descripcion','marca','modelo','refe_proveedor',
    'cantidad_pedida','cantidad_recibida','cantidad_almacen','estado','itbms','sub_total','total','precio','porcentaje','fk_solicitud','fk_localizacion'];

    protected $casts    =
    [
        'id_detalle'        => 'integer',
        'fk_solicitud'      => 'integer',
        'item'              => 'integer',
        'fk_articulo'       => 'integer',
        'codigo'            => 'integer',
        'descripcion'       => 'string',
        'marca'             => 'string',
        'modelo'            => 'string',
        'refe_proveedor'    => 'string',
        'cantidad_pedida'   => 'integer',
        'cantidad_recibida' => 'integer',
        'cantidad_almacen'  => 'integer',
        'estado'            => 'string',
        'itbms'             => 'float',
        'sub_total'         => 'float',
        'total'             => 'float',
        'precio'            => 'float',
        'porcentaje'        => 'integer',
        'fk_localizacion'   => 'integer',
        'fk_unidad_medida'  => 'integer',
        'unidad_de_medida'  => 'string'
    ];
}
