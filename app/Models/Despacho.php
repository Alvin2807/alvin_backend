<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despacho extends Model
{
    use HasFactory;
    public          $table          = "despacho";
    protected       $primary_key    = "codigo";
    protected       $fillable       = ['codigo','descripcion'];
    protected       $keyType        = 'string';
    public          $incrementing   = true;
    public          $timestamps     = false;
   
}
