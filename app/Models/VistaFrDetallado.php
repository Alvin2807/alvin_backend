<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaFrDetallado extends Model
{
    use HasFactory;
    public    $table      = "VW_FRS_ORDEN_COMPRA_DETALLADO";
    protected $fillable   = ['id_fer_encabe','fk_despacho','despacho','no_control','fecha_entrega','entregado_por','recibido_por','estado','id_fer_detalle',
    'cantidad_pedida','cantidad_recibida','id_detalle','no_item','fk_articulo','codigo','descripcion','marca','modelo','fk_solicitud','orden_compra','fecha_orden',
    'no_nota','fecha_nota','no_factura','no_factura','no_caja_menuda','fk_tipo_solicitud','no_solicitud_bienes','no_solicitud_linea','id_fer_localizacion',
    'fk_localizacion','ubicacion','cantidad'];

    protected $casts      = [
        'id_fer_encabe'   => 'integer',
        'fk_despacho'     => 'string|max:500',
        'no_control'      => 'string|max:30',
        'fecha_entrega'   => 'date-time:Y-m-d',
        'entregado_por'   => 'string|max:80',
        'recibido_por'    => 'string|max:80',
        'id_fer_detalle'  => 'integer',
        'cantidad_pedida' => 'integer',
        'cantidad_recibida' => 'integer',
        'no_item'         => 'integer',
        'fk_articulo'     => 'integer',
        'codigo'          => 'integer',
        'fk_solicitud'    => 'integer',
        'fk_tipo_solicitud' =>'integer',
        'fecha_orden'       =>'datetime:Y-m-d',
        'no_nota'           => 'string|max:20',
        'fecha_nota'        => 'datetime:Y-m-d',
        'no_factura'        => 'string|max:50',
        'fecha_factura'     => 'datetime:Y-m-d',
        'no_caja_menuda'    => 'string|max:25',
        'no_solicitud_bienes' =>'string|max:50',
        'no_solicitud_linea' =>'string|max:50',
        'id_fer_localizacion' =>'integer',
        'fk_localizacion'     =>'integer',
        'cantidad'            =>'integer',


    ];

}
