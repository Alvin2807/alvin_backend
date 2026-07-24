<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleSolicitudCompra extends Model
{
    use HasFactory;

    public $table = 'inv_detalle_compras';
    protected $primaryKey = 'id_detalle';
    protected $fillable = ['id_detalle'];
    public $incrementing = true;
    public $timestamps = false;

}
