<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FR extends Model
{
    use HasFactory;
    
    public      $table          = "inv_fer_encabe";
    protected   $primaryKey     = "id_fer_encabe";
    protected   $fillable       = ['id_fer_encabe','fk_despacho', 'no_control','fecha_entrega','lugar_entrega','solicitado_por','aprobado_por',
    'entregado_por','recibido_por','observacion','estado','fecha_crea','fecha_modifica','usuario_modifica','usuario_crea'];
    public      $incrementing   = true;
    public      $timestamps     = false;

    protected $casts = [
        
       'id_fer_encabe' => 'integer',
       'fecha_entrega' => 'datetime:Y-m-d',
       'fecha_crea' => 'datetime:Y-m-d',
       'fk_despacho' =>'string',
       'no_control'=>'string',
       'lugar_entrega'=>'string',
       'solicitado_por'=>'string',
       'aprobado_por'=>'string',
       'entregado_por'=>'string',
       'recibido_por'=>'string',
       'observacion'=>'string',
       'estado'=>'string',
       'usuario_crea'=>'string',
       'usuario_modifica'=>'string'
    ];




   
}

