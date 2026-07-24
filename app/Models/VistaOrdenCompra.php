<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaOrdenCompra extends Model
{
    use HasFactory;

    public    $table    = "VW_ORDEN_COMPRAS_RESUMIDO";
    protected $fillable = ['fk_tipo_solicitud','id_solicitud','fecha_orden','orden_compra','fk_solicitado','solicitado_por','aprobado_por','fk_seccion','seccion_interna',
    'no_nota','fecha_nota','no_solicitud_linea','solicitud_bienes','no_caja_menuda','observacion','fk_proveedor','proveedor','no_factura','fecha_factura','fecha_aprox_entrega',
    'fecha_real_entrega','plazo_entrega','periodo_entrega','termino_entrega','fecha_referendo','fecha_publicacion','sub_total','itbms','total','estado_solicitud','items','cantidad_pedida',
    'cantidad_recibida','recibida_almacen','recibida_en_sitio','codigo_proveedor','tipo_solicitud','nombre_solicitante'];

    protected $casts    = 
    [
        'fk_tipo_solicitud'  =>  'integer',
        'id_solicitud'       =>  'integer',
        'fecha_orden'        =>  'datetime:Y-m-d',
        'orden_compra'       =>  'string',
        'fk_solicitado'      =>  'string',
        'aprobado_por'       =>  'string',
        'fk_seccion'         =>  'integer',
        'seccion_interna'    =>  'string',
        'no_nota'            =>  'string',
        'fecha_nota'         =>  'datetime:Y-m-d',
        'no_solicitud_linea' =>  'string',
        'no_solicitud_bienes'=>  'string',
        'no_caja_menuda'     =>  'string',
        'observacion'        =>  'string',
        'fk_proveedor'       =>  'integer',
        'proveedor'          =>  'string',
        'no_factura'         =>  'string',
        'fecha_factura'      =>  'datetime:Y-m-d',
        'fecha_aprox_entrega'=>  'datetime:Y-m-d',
        'fecha_real_entrega' =>  'datetime:Y-m-d',
        'plazo_entrega'      =>  'integer',
        'periodo_entrega'    =>  'string',
        'termino_entrega'    =>  'string',
        'fecha_referendo'    =>  'datetime:Y-m-d',
        'fecha_publicacion'  =>  'datetime:Y-m-d',
        'sub_total'          =>  'float',
        'itbms'              =>  'float',
        'total'              =>  'float',
        'estado_solicitud'   =>  'string',
        'items'              =>  'string',
        'cantidad_pedida'    =>  'integer',
        'cantidad_recibida'  =>  'integer',
        'recibida_en_almacen'  => 'integer',
        'recibida_en_sitio'    => 'integer',
        'codigo_proveedor'     => 'string',
        'tipo_solicitud'       => 'string',
        'nombre_solicitante'   => 'string'

    ];
}
