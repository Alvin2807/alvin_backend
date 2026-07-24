<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depositos extends Model
{
    use HasFactory;

    public      $table          = "inv_depositos";
    protected   $primaryKey     = "id_deposito";
    protected   $fillable       = ['id_deposito', 'fk_despacho','descripcion','fecha_crea','fecha_modifica','usuario_modifica', 'usuacrio_crea','inventario'];
    public      $incrementing   = true;
    public      $timestamps     = false;

    protected $casts = [
        
        'id_deposito' => 'integer',
        'fk_despacho' =>'string',
        'descripcion' =>'string',
        'inventario'  => 'string'
    ];
}
