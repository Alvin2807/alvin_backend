<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaUbicacionArticulo extends Model
{
    use HasFactory;

    public $table = "vw_articulo_ubicacion";
    protected $fillable = ['id_articulo','codigo','marca','modelo','ultimo_precio','precio_promedio','periodo_grantia','garantia',
    'id_ubicacion','fk_localizacion','localizacion','id_deposito','deposito','fk_despacho','despacho','cantidad_stock','descripcion',
    'detalle','cantidad_en_movimiento'];

    protected $casts = [
        'id_articulo'         =>'integer',
        'codigo'              =>'integer',
        'descripcion'         =>'string',
        'marca'               =>'string',
        'modelo'              =>'string',
        'precio_promedio'     =>'float',
        'ultimo_precio'       =>'float',
        'periodo_garantia'    =>'string',
        'garantia'            =>'integer',
        'id_ubicacion'        =>'integer',
        'fk_localizacion'     =>'integer',
        'localizacion'        =>'string',
        'id_deposito'         =>'integer',
        'deposito'            =>'string',
        'fk_despacho'         =>'string',
        'despacho'            =>'string',
        'cantidad_stock'      =>'integer',
        'detalle'             => 'string',
        'cantidad_en_movimiento' =>'integer'
    ];
}
