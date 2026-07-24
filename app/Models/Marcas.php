<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marcas extends Model
{
    use HasFactory;

    public          $table          = "inv_marcas";
    protected       $primaryKey     = "id_marca";
    protected       $fillable       = ['id_marca', 'nombre_marca','usuario_crea','usuario_modifica'];
    public          $incrementing   = true;
    public          $timestamps     = false;

    protected $casts = [
        
        'id_marca'        => 'integer',
        'nombre_marca'    => 'string',
        'usuario_crea'    =>'string',
        'usuario_modifica'=>'string'
    ];

    public function articulos()
    {
        return $this->hasMany(Articulos::class, 'fk_marca', 'id_marca');
    }
}
