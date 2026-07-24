<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Depositos;

class DespachoController extends Controller
{
    public function traerDespacho()
    {
        $despacho = DB::table('despacho')
        ->where(function($query) { 
        $query->whereIn('tipo', ['SPA','INQ'])
        ->orWhere('tipo2','PGN');
        })      
        ->where('estatus','A')
        ->select('codigo','descripcion')
        ->get();
        return response()->json([ 
        "ok"    => true,
        "data"  =>$despacho,
        "TOTAL" =>count($despacho)
        ]);
    }

    public function traerDespachoMovimientoDestino($codigo){
        $despacho =  Depositos::
        join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->join('inv_localizaciones','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->select('inv_depositos.fk_despacho','exp.despacho.descripcion',
        'inv_localizaciones.estado','exp.despacho.codigo')
        ->where('exp.despacho.codigo', '<>', $codigo)
        ->where('inv_localizaciones.estado','<>', 'I')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  =>$despacho
            ]); 
    }

    public function traerDespachoMovimientoOrigenEntrada($codigo){
        $despacho =  Depositos::
        join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->join('vw_articulo_ubicacion','vw_articulo_ubicacion.fk_despacho','exp.despacho.codigo')
        ->join('inv_localizaciones','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->select('inv_depositos.fk_despacho','exp.despacho.descripcion',
        'inv_localizaciones.estado','exp.despacho.codigo')
        ->where('exp.despacho.codigo', '<>', $codigo)
        ->where('inv_localizaciones.estado','<>', 'I')
        ->where('vw_articulo_ubicacion.cantidad_stock','>', 0)
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  =>$despacho
            ]); 
    }

    public function traerUnidadAlmacen () { 
        $unidadesAlmacen = DB::table('despacho')
        ->where('descripcion','LIKE', '%ALMACEN%')
        ->orWhere('descripcion', 'like','%SECCIÓN DE ALMACÉN%')
        ->select('codigo','descripcion','direccion')
        ->first();
        return response()->json([ 
        "ok"    => true,
        "data"  =>$unidadesAlmacen,
        
        ]);
    }

    public function mostrarDespachoInformatica () { 
        $traerDespachoInformatica = DB::table('despacho')
        ->where('descripcion', 'LIKE','%UNIDAD DE INFORMÁTICA%')
        ->orWhere('descripcion','LIKE','%DIRECCIÓN DE INFORMÁTICA%')
        ->select('despacho.codigo','despacho.descripcion')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  =>$traerDespachoInformatica
        ]);
    }
    public function mostrarDireccionBienesPatrimoniales()
    {
        //mostrar direccion actual de Bienes Patrimoniales
        $despacho = DB::table('despacho')
        ->select('direccion as direccion_bienes','codigo as codigo_bienes')
        ->where('codigo', '8080801F')
        ->first();
        return response()->json([
            "ok" =>true,
            "data" =>$despacho
        ]);
    }

    public function mostrarConsultaOtroDespacho(){
        $despacho = DB::table('despacho')
        ->where(function($query) { 
        $query->whereIn('tipo', ['SPA','INQ'])
        ->orWhere('tipo2','PGN');
        })      
        ->where('estatus','A')
        ->select('codigo','descripcion')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  =>$despacho,
        ]);

    }
}
