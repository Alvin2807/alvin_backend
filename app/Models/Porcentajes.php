<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Porcentajes extends Model
{
    use HasFactory;

    public      $table          = 'inv_porcentajes';
    protected   $primaryKey     = 'id_porcentaje';
    protected   $fillable       = ['id_porcentaje', 'tipo', 'descripcion', 'porcentaje', 'estatus', 'periodo_inicial', 'periodo_final', 'usuario_crea'];
    public     $incrementing    = true;
    public     $timestamps      = false;
    
    const INACTIVO = 'I';
    //const PENDIENTE = 'P';
    const ACTIVO = 'A';

    protected $casts = [
        'id_porcentaje'   => 'integer',
        'descripcion'     => 'string',
        'tipo'            => 'string',
        'porcentaje'      => 'integer',
        'estatus'         => 'string',
        'periodo_inicial' => 'datetime:Y-m-d',
        'periodo_final'   =>'datetime:Y-m-d'
    ];

}
