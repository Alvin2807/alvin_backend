<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelos extends Model
{
    use HasFactory;

    public      $table          = "inv_modelos";
    protected   $primaryKey     = "id_modelo";
    protected   $fillable       = ['id_modelo','nombre_modelo', 'fk_marca', 'usuario_crea', 'fecha_crea', 'usuario_modifica'];
    public      $incrementing   = true;
    public      $timestamps     = false;

    protected $casts = [
        
        'id_modelo'       => 'integer',
        'nombre_modelo'   => 'string',
        'fk_marca'        => 'integer',
        'usuario_crea'    => 'string',
        'usuario_modifica'=> 'string'
    ];

    public function articulos()
    {
        return $this->hasMany(Articulos::class, 'fk_modelo', 'id_modelo');
    }
}
