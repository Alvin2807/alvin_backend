<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    use HasFactory;
    
    public      $table          = "inv_categorias";
    protected   $primaryKey     = "id_categoria";
    protected   $fillable       = ['id_categoria', 'descripcion','usuario_crea','fecha_crea', 'usuario_modifica'];
    public      $incrementing   = true;
    public      $timestamps     = false;

    protected $casts = [
        
        'id_categoria' => 'integer',
        'descripcion'  => 'string'
    ];

    public function articulos()
    {
        return $this->hasMany(Articulos::class, 'fk_categoria', 'id_categoria');
    }

}
