<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoTraspaso extends Model
{
    use HasFactory;

    public $table         = 'inv_movimiento_enc';
    protected $primaryKey = 'id_movimiento_enc';
    protected $fillable   = ['id_movimiento_enc','tipo_movimiento','fk_despacho_origen','fk_despacho_destino','no_movimiento',
    'fecha_entrega','solicitado_por','aprobado_por','entregado_por','recibido_por','observacion','estado','usuario_crea',
    'usuario_modifica','uibp_origen','uibp_destino','fecha_doc','fecha_nota','no_nota','observacion'];
    public $incrementing  = true;
    public $timestamps    = false;

    protected $casts      = [
        'id_movimiento_enc'   => 'integer',
        'tipo_movimiento'     => 'string',
        'fk_despacho_origen'  => 'string',
        'fk_despacho_destino' => 'string',
        'fecha_entrega'       => 'datetime:Y-m-d',
        'fecha_doc'           => 'datetime:Y-m-d',
        'fecha_nota'          => 'datetime:Y-m-d',
        'solicitado_por'      => 'string',
        'aprobado_por'        => 'string',
        'entregado_por'       => 'string',
        'recibido_por'        => 'string',
        'observacion'         => 'string',
        'estado'              => 'string',
        'usuario_crea'        => 'string',
        'usuario_modifica'    => 'string',
        'uibp_origen'         => 'string',
        'uibp_destino'        => 'string',
        'no_nota'             => 'string',
        'observacion'         => 'string'

    ];
}
