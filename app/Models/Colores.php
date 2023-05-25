<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colores extends Model
{
    use HasFactory;
    protected $table         = "inv_colores";
    protected $primarykey    = "id_color";
    protected $fillable      = ['id_color','codigo_color','color'];
    public    $incrementing  = true;
    public    $timestamps    = false;

    protected $casts = [
        'id_color'     => 'integer',
        'codigo_color' => 'integer',
        'color'        => 'string'
    ];

}
