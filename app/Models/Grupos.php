<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupos extends Model
{
    use HasFactory;

    public    $table        = 'inv_grupos';
    protected $primaryKey   = 'id_grupo';
    public    $fillable     = ['id_grupo', 'fk_categoria', 'descripcion', 'usuario_crea', 'fecha_crea'];
    public    $incrementing = true;
    public    $timestamps   = false;

    protected $casts = [
        
        'id_grupo'         => 'integer',
        'fk_categoria'     => 'integer',
        'descripcion'      => 'string',
        'usuario_crea'     => 'string',
        'usuario_modifica' => 'string'
    ];

    public function articulos()
    {
        return $this->hasMany(Articulos::class, 'fk_grupo', 'id_grupo');
    }
}
