<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UbicacionArticulo;

class Articulos extends Model
{
    use HasFactory;
    public      $table          = "inv_articulos";
    protected   $primaryKey     = "id_articulo";
    protected   $fillable       =  ['id_articulo', 'codigo', 'descripcion', 'fecha_crea', 'cantidad_stock', 'garantia', 'fk_marca', 'fk_modelo', 
    'fk_unidad_medida','cantidad_minima', 'fk_tipo_uso', 'fk_categoria', 'fk_grupo', 'fk_sub_grupo','periodo_grantia','requiere_activo'];
    public      $incrementing   = true;
    public      $timestamps     = false;

    protected $casts = [
        
        'id_articulo'      => 'integer',
        'codigo'           =>'integer',
        'descripcion'      => 'string',
        'fk_marca'         => 'integer',
        'fk_modelo'        => 'integer',
        'fk_categoria'     => 'integer',
        'fk_grupo'         => 'integer',
        'fk_sub_grupo'     => 'integer',
        'fk_tipo_uso'      => 'integer',
        'fk_unidad_medida' => 'integer',
        'cantidad_pedida'  => 'integer',
        'cantidad_almacen' => 'integer',
        'cantidad_stock'   => 'integer',
        'cantidad_minima'  => 'integer',
        'fecha_ult_compra' => 'datetime:Y-m-d',
        'ultimo_precio'    => 'float',
        'usuario_crea'     =>'string',
        'usuario_modifica' =>'string',
        'garantia'         => 'integer',
        'periodo_grantia'  =>'string',
        'requiere_activo'  =>'string', 
    ];


    public function UbicacionArticulo()
    {
        $this->hasMany(UbicacionArticulo::class);
    }

    public function detalleCompras()
    {
        return $this->hasMany(DetallesCompras::class, 'fk_articulo', 'id_articulo');
    }
}
