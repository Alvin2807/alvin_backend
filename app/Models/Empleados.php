<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
    use HasFactory;
    public $table = 'vista_empleados_despacho@db_emp';
    protected $keyType = "string";
}
