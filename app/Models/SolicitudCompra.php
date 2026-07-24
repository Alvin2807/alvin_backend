<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class SolicitudCompra extends Model
{
    use HasFactory;

    public      $table           = "inv_solicitud_compras";
    protected   $primaryKey      = "id_solicitud";
    protected   $fillable        = ['id_solicitud','no_nota','observacion','fk_solicitado','aprobado_por','solicitado_por','orden_compra','fecha_orden','fk_tipo_solicitud',
    'plazo_entrega','fecha_aprox_entrega','fecha_real_entrega','no_solicitud_linea','no_solicitud_bienes','no_factura','fecha_factura','fk_proveedor','sub_total',
    'itbms','total','usuario_crea', 'fecha_crea', 'usuario_modifica', 'fecha_modifica','estado','fk_seccion',
    'fecha_nota','fecha_referendo','fecha_publicacion','periodo_entrega','termino_entrega','no_caja_menuda'];
    public      $incrementing    = true;
    public      $timestamps      = false;

    protected $casts             = 
    [
        'fecha_orden'            => 'datetime:Y-m-d',
        'fecha_aprox_entrega'    => 'datetime:Y-m-d',
        'fecha_nota'             => 'datetime:Y-m-d',
        'fecha_referendo'        => 'datetime:Y-m-d',
        'fecha_publicacion'      => 'datetime:Y-m-d',
        'fecha_factura'          => 'datetime:Y-m-d',
        'sub_total'              => 'float',
        'itbms'                  => 'float',
        'total'                  => 'float',
        'id_solicitud'           => 'integer',
        'fk_proveedor'           => 'integer',
        'plazo_entrega'          => 'integer',
        'fk_tipo_solicitud'      =>'integer',
        'fk_seccion'             => 'integer',
        'fk_localizacion'        => 'integer',
        'fk_articulo'            => 'integer',
        'fecha_real_entrega'     => 'datetime:Y-m-d',
    ];
}
