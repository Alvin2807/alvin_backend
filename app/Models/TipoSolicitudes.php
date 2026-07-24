<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSolicitudes extends Model
{
    use HasFactory;

    public     $table        = 'inv_tipo_solicitudes';
    protected  $primaryKey   = 'id_tipo_solicitud';
    protected  $fillable     = ['id_tipo_solicitud','descripcion'];
    public     $incrementing = true;
    public     $timestamps   = false;
}
