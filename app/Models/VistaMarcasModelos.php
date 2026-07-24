<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaMarcasModelos extends Model
{
    use HasFactory;

    public    $table    = 'VW_INV_POR_MARCA_MODELO';
    protected $fillable = ['id_marca','marca','id_modelo','cantidad_pedida','cantidad_almacen','cantidad_stock'];

    protected $casts    = 
    [
        'id_marca'        => 'integer',
        'marca'           => 'string',
        'id_modelo'       => 'integer',
        'modelo'          => 'string',
        'cantidad_pedida' => 'integer',
        'cantidad_almacen'=> 'integer',
        'cantidad_stock'  => 'integer'
    ];
}
