<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallesArticulos extends Model
{
    use HasFactory;
    public $table = "inv_detalle_articulo";
    protected $primaryKey = "id_detalle_articulo";
    protected $fillable = ['id_detalle_articulo','fk_ubicacion','fk_detalle_compra','numero_serie','descripcion',
    'codigo_barra','numero_activo','fk_color','estatus','usuario_crea','fecha_crea','usuario_modifica',
    'fecha_modifica','fk_fer_localizacion','disponible'];
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id_detalle_articulo' =>'integer',
        'fk_ubicacion' =>'integer',
        'fk_detalle_compra' =>'integer',
        'numero_serie' =>'string',
        'descripcion' =>'string',
        'codigo_barra' =>'string',
        'numero_activo' =>'string',
        'fk_color' =>'integer',
        'usuario_crea' =>'string',
        'usuario_modifica' =>'string',
        'fk_fer_localizacion' =>'integer',
        'disponible' =>'string'
    ];

}
