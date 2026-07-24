<?php

namespace App\Utils;
use Carbon\Carbon;

class Utilidades  
{
    public static function formatoFecha($fecha)
    {
        return Carbon::createFromFormat('Y-m-d', $fecha)->toDateString();
    }

    public static function formatoFechaAproximada($fecha)
    {
        return Carbon::createFromFormat('d/m/Y', $fecha)->toDateString();
    }

    public static function fechaActual($actual) 
    { 
        return Carbon::now()->formatoFechaActual('Y-m-d', $actual)->toDateString();
    }
    
}
