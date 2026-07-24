<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colores extends Model
{
    use HasFactory;

    public      $table          = "inv_colores";
    protected   $primaryKey      = "id_color";
    protected   $fillable       = ['id_color', 'descripcion', 'usuario_crea', 'fecha_crea', 'fecha_modifica', 'usuario_modifica'];
    public      $incrementing   = true;
    public      $timestamps     = false;

    protected $casts = [
        'id_color' =>'integer',
        'descripcion' =>'string',
        'usuario_crea' =>'string',
        'usuario_modifica' =>'string'
    ];

}
