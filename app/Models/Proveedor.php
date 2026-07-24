<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    public      $table          ="inv_proveedores";
    protected   $primaryKey     = "id_proveedor";
    protected   $fillable       = ['id_proveedor', 'codigo_proveedor','nombre', 'ruc', 'dv', 'direccion','telefono1','telefono2','celular',
    'fax','apartado','email','pagina_web','contacto','estado','fecha_ult_compra'];
    public      $incrementing   = true;
    public      $timestamps     = false;

    protected $casts = [
        'id_proveedor'     => 'integer',
        'codigo_proveedor' => 'string',
        'nombre'           => 'string',
        'ruc'              =>'string',
        'dv'               =>'string',
        'direccion'        =>'string',
        'telefono1'        =>'string',
        'telefono2'        =>'string',
        'celular'          =>'string',
        'fax'              =>'string',
        'apartado'         =>'string',
        'email'            =>'string',
        'pagina_web'       =>'string',
        'contacto'         =>'string',
        'estado'           =>'string',
        'fecha_ult_compra' =>'datetime:Y-m-d',

    ];
    
}
