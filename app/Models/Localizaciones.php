<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localizaciones extends Model
{
    use HasFactory;
    public $table = "inv_localizaciones";
    protected $primaryKey = "id_localizacion";
    protected $fillable   = ['id_localizacion','fk_deposito','descripcion','estado','usuario_crea','usuario_modifica'];
    public $incrementing  = true;
    public $timestamps    = false;

    protected $casts = [
        'id_localizacion'  => 'integer',
        'fk_deposito'      => 'integer',
        'descripcion'      => 'string',
        'estado'           => 'string',
        'usuario_crea'     => 'string',
        'usuario_modifica' => 'string'
        
        
    ];
}
