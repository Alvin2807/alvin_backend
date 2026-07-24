<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empleados;
use Illuminate\Support\Facades\DB;
class EmpleadosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       

    }

    public function mostrarEmpleados($fk_despacho)
    {    
        $empleados = DB::select("select codigo_despacho, nombre_empleado  from VISTA_EMPLEADOS_DESPACHO@DB_EMP where codigo_despacho = '$fk_despacho'");
        return response()->json([ 
            "ok" =>true,
            "data" =>$empleados
        ]);

    }

    public function empleadosBienesPatrimoniales(){

      
        $empleados = DB::select( DB::raw("select codigo_despacho, trim(nombre_empleado)  from VISTA_EMPLEADOS_DESPACHO@DB_EMP where codigo_despacho = '8080801F'"));
        return response()->json([ 
            "ok" =>true,
            "data" =>$empleados
        ]);
    }

    public function empleadosBienesPatrimonialesDestino(){
        $empleados = DB::select( DB::raw("select codigo_despacho, trim(nombre_empleado)  from VISTA_EMPLEADOS_DESPACHO@DB_EMP where codigo_despacho = '8080801F'"));
        return response()->json([ 
            "ok" =>true,
            "data" =>$empleados
        ]);
    }


   
}
