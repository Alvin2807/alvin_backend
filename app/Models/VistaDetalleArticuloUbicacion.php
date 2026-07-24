<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaDetalleArticuloUbicacion extends Model
{
    use HasFactory;

    public $table = 'vw_detalle_articulo_ubicacion';
    protected $fillable = ['codigo','descripcion','marca','modelo','id_ubicacion','fk_localizacion','localizacion','id_deposito','deposito','fk_despacho','despacho',
    'descripcion_especifica','numero_serie','numero_activo','codigo_barra','codigo_qr','fk_color','color','estatus','fk_fer_localizacion','fk_detalle_compra','id_detalle_articulo',
    'id_articulo','disponible'];

    protected $casts = [
        'id_articulo'               => 'integer',
        'descripcion'               => 'string',
        'marca'                     => 'string',
        'modelo'                    => 'string',
        'id_ubicacion'              =>'integer',
        'fk_localizacion'           =>'integer',
        'localizacion'              => 'string',
        'id_deposito'               => 'integer',
        'deposito'                  => 'string',
        'fk_despacho'               => 'string',
        'despacho'                  => 'string',
        'descripcion_especifica'    => 'string',
        'numero_serie'              => 'string',
        'numero_activo'             => 'string',
        'codigo_barra'              => 'string',
        'fk_color'                  => 'integer',
        'color'                     =>'string',
        'estatus'                   => 'string',
        'fk_fer_localizacion'       => 'integer',
        'fk_detalle_compra'         => 'integer',
        'id_detalle_articulo'       => 'integer',
        'disponible'                => 'string'

    ];
}
