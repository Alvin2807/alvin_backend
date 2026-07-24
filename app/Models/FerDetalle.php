<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FerDetalle extends Model
{
    use HasFactory;
    
    public      $table = "inv_fer_detalle";
    protected   $primaryKey = "id_fer_detalle";
    protected   $fillable = ['id_fer_detalle', 'fk_fer_encabe','fk_detalle_compra','cantidad_pedida','cantidad_recibida',
    'estado','usuario_crea','fecha_crea','usuario_modifica','fecha_modifica'];
    public      $incrementing = true;
    public      $timestamps = false;

    protected $casts = [
        
        'id_fer_detalle' => 'integer',
        'fk_fer_encabe' => 'integer',
        'fk_detalle_compra' => 'integer',
        'cantidad_pedida' => 'integer',
        'cantidad_recibida' => 'integer'
    
    ];
}
