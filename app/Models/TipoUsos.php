<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUsos extends Model
{
    use HasFactory;

    public      $table          = "inv_tipo_usos";
    protected   $primaryKey     = "id_tipo_uso";
    protected   $fillable       = ['id_tipo_uso','descripcion'];
    public      $incrementing   = true;
    public      $timestamps     = false;

    protected $casts = [
        
        'id_tipo_uso' => 'integer',
        'descripcion' => 'string'
    ];
}
