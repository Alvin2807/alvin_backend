<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallesCompras extends Model
{
    use HasFactory;

    public      $table        = "inv_detalle_compras";
    protected   $primaryKey   = "id_detalle";
    protected   $fillable     = ['id_detalle', 'fk_solicitud', 'no_item', 'fk_articulo', 'refe_proveedor', 'cantidad_pedida', 'precio', 
    'sub_tota', 'itbms', 'total', 'cantidad_recibida', 'estado', 'usuario_crea', 'fecha_crea', 'usuario_modifica', 'fecha_modifica',
    'cantidad_almacen','porcentaje','recibido_en', 'fk_localizacion', 'recibido_en_sitio','cantidad_sitio'];
    
    public      $incrementing = true;
    public      $timestamps   = false;

    protected $casts          = 
    [
        'no_item'             => 'integer',
        'fk_solicitud'        =>'integer',
        'refe_proveedor'      =>'string',
        'cantidad_pedida'     =>'integer',
        'cantidad_almacen'    =>'integer',
        'cantidad_recibida'   =>'integer',
        'sub_tota'            => 'float',
        'itbms'               => 'float',
        'total'               => 'float',
        'id_detalle'          => 'integer',
        'fk_localizacion'     => 'integer',
        'fk_articulo'         => 'integer',
        'cantidad_sitio'      =>'integer',
        'recibido_en'         =>'string',
        'precio'              =>'float'
        
    ];

    public function articulo(){
        return $this->belongsTo(Articulos::class, 'fk_articulo');
    }

  

}
