<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Articulos;

class UbicacionArticulo extends Model
{
    use HasFactory;
    public $table = 'inv_ubicacion_articulos';
    protected $primaryKey = 'id_ubicacion';
    protected $fillable   = ['id_ubicacion','fk_localizacion','cantidad_stock','fk_articulo','usuario_crea','usuario_modifica','detalle'];
    public $incrementing  = true; 
    public $timestamps    = false;

    protected      $casts = [
        'fk_localizacion'=> 'integer',
        'id_ubicacion'   =>'integer',
        'cantidad_stock' =>'integer',
        'fk_articulo'    =>'integer',
        'usuario_crea'   =>'string',
        'usuario_modifica'=>'string'
        
    ];

    public function Articulos(){
        return $this->belongsTo(Articulos::class,'fk_articulo', 'id_articulo');
    }

   
}
