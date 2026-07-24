<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaOrden extends Model
{
    use HasFactory;

    public $table = 'vw_detalle_orden_compras';
    protected $fillable = ['fk_tipo_solicitud','fk_solicitud','fecha_orden','orden_compra','no_nota','fecha_nota','no_solicitud_linea','no_solicitud_bienes','no_caja_menuda',
    'no_factura','fecha_factura','sub_total','itbms','total','estado','cantidad_pedida','cantidad_recibida','cantidad_almacen'];
   
    protected $casts = [
        'fk_tipo_solicitud' =>'integer',
        'fk_solicitud' => 'integer',
        'orden_compra' => 'string',
        'fecha_orden' =>'datetime:Y-m-d',
        'no_nota' =>'string',
        'fecha_nota' =>'datetime:Y-m-d',
        'no_factura' =>'string',
        'fecha_factura' =>'datetime:Y-m-d',
        'no_caja_menuda'=>'string',
        'no_solicitud_bienes'=>'string',
        'no_solicitud_linea'=>'string',
        'item' =>'integer',
        'refe_proveedor'=>'string',
        'precio'=>'float',
        'sub_total'=>'float',
        'itbms'=>'float',
        'total' =>'float',
        'fk_articulo' =>'integer',
        'codigo' =>'integer',
        'porcentaje'=>'integer',
        'descripcion' =>'string',
        'marca' =>'string',
        'fk_marca' =>'integer',
        'modelo' =>'string',
        'fk_modelo' =>'integer',
        'cantidad_almacen' =>'integer',
        'cantidad_pedida' =>'integer',
        'cantidad_recibida' =>'integer',
        'id_detalle'=>'integer',
        'estado'=>'string'
    ];

}
