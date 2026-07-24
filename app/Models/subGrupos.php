<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subGrupos extends Model
{
    use HasFactory;

    public    $table        = 'inv_sub_grupos';
    protected $primaryKey   = "id_sub_grupo";
    protected $fillable     = ['id_sub_grupo', 'fk_grupo', 'descripcion'];
    public    $incrementing = true;
    public    $timestamps   = false;

    protected $casts = [
        
        'id_sub_grupo' => 'integer',
        'fk_grupo'     => 'integer',
        'descripcion'  => 'string'
    ];

    public function articulos()
    {
        return $this->hasMany(Articulos::class, 'fk_sub_grupo', 'id_sub_grupo');
    }

}
