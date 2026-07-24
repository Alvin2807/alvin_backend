<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalizarFr extends Model
{
    use HasFactory;

    public $table = "inv_fer_localizacion";
    protected $primaryKey = "id_fer_localizacion";
    protected $fillable = ['id_fer_localizacion','fk_fer_detalle','fk_localizacion','cantidad_recibida'];
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id_fer_localizacion' => 'integer',
        'fk_fer_detalle' => 'integer',
        'fk_localizacion' => 'integer',
        'cantidad_recibida' => 'integer'
    ];
}
