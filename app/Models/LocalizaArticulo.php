<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalizaArticulo extends Model
{
    use HasFactory;
    public $table = 'inv_localiza_articulos';
    protected $primaryKey = 'id_localizacion';
    protected $fillable = ['id_localizacion','fk_ubicacion','fk_deposito','cantidad_stock','fecha_crea','usuario_crea','fecha_modifica','usuario_modifica'];
    protected $keyType = 'string';
    public $incrementing = true;
    public $timestamps = false;
}
