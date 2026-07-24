<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionDespacho extends Model
{
    use HasFactory;
    public $table = "SECCIONES_POR_DESPACHO";
    protected $primaryKey = "id_seccion";
    protected $fillable = ['id_seccion','fk_despacho','descripcion','estado'];
    protected $keyType = "string";
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        
        'id_seccion' => 'integer',
        
    ];

}
