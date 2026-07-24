<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleMovimientos extends Model
{
    use HasFactory;

    public $table = 'inv_movimiento_det';
    protected $primaryKey = 'id_movimiento_det';
    protected $fillable   = ['inv_movimiento_det.id_movimiento_det','inv_movimiento_det.fk_movimiento_enc',
    'inv_movimiento_det.fk_ubicacion_origen','inv_movimiento.fk_detalle_origen','inv_movimiento_det.cantidad',
    'inv_movimiento_det.estatus','inv_movimiento_det.usuario_crea','inv_movimiento_det.fk_localizacion_origen',
    'inv_movimiento_det.fk_localizacion_destino'];
    public $incrementing = true;
    public $timestamps   = false;

    protected $casts     = 
        [
            'id_movimiento_det'       => 'integer',
            'fk_movimiento_enc'       => 'integer',
            'fk_ubicacion_origen'     => 'integer',
            'fk_detalle_origen'       => 'integer',
            'cantidad'                => 'integer',
            'estatus'                 => 'string',
            'usuario_crea'            => 'string',
            'usuario_modifica'        => 'string',
            'observaciones'           => 'string',
            'fk_localizacion_origen'  => 'integer',
            'fk_localizacion_destino' => 'integer'
        ];

}
