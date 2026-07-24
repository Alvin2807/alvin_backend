<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetalleMovimientos;
use App\Models\VistaUbicacionArticulo;
use Illuminate\Http\Request;

class VistaUbicacionArticulosController extends Controller
{
   
    public function index()
    {
        //Ubicacion de artículos
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        select('id_articulo','codigo','marca','modelo','descripcion','ultimo_precio','precio_promedio','periodo_grantia','garantia',
        'id_ubicacion','fk_localizacion','localizacion','id_deposito','deposito','fk_despacho','despacho','cantidad_stock','detalle',
        'cantidad_en_movimiento')
        ->where('cantidad_stock', '>', 0)
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    public function ubicacionesOtroDespacho($codigo_despacho)
    {
        //Ubicacion de artículos de otro despacho
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        select('id_articulo','codigo','marca','modelo','descripcion','ultimo_precio','precio_promedio','periodo_grantia','garantia',
        'id_ubicacion','fk_localizacion','localizacion','id_deposito','deposito','fk_despacho','despacho','cantidad_stock','detalle',
        'cantidad_en_movimiento')
        ->where('cantidad_stock', '>', 0)
        ->where('fk_despacho','<>',$codigo_despacho)
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    public function misUbicaciones($codigo_despacho)
    {
        //Ubicacion de artículos de otro despacho
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        select('id_articulo','codigo','marca','modelo','descripcion','ultimo_precio','precio_promedio','periodo_grantia','garantia',
        'id_ubicacion','fk_localizacion','localizacion','id_deposito','deposito','fk_despacho','despacho','cantidad_stock','detalle',
        'cantidad_en_movimiento')
        ->where('cantidad_stock', '>', 0)
        ->where('fk_despacho',$codigo_despacho)
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    public function verificarExisteCantidadUbicacionDespacho($codigo_despacho){
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        select('fk_despacho')
        ->where('fk_despacho',$codigo_despacho)
        ->whereRaw('cantidad_stock - cantidad_en_movimiento > 0')
        ->count();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    public function mostrarArticulosLocalizacionDespacho($codigo_despacho){
        //Muestra los articulos disponibles en localizacion por despachos
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        select('id_articulo','codigo','marca','modelo','descripcion','ultimo_precio','precio_promedio','periodo_grantia','garantia',
        'id_ubicacion','fk_localizacion','localizacion','id_deposito','deposito','fk_despacho','despacho','cantidad_stock','detalle',
        'cantidad_en_movimiento')
        ->where('fk_despacho', $codigo_despacho)
        ->whereRaw('cantidad_stock - cantidad_en_movimiento > 0')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    public function mostrarArticulosLocalizacionesTipoDespacho($codigo_despacho){
        //Muestra los articulos disponibles en localizacion por despachos
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        select('id_articulo','codigo','marca','modelo','descripcion','ultimo_precio','precio_promedio','periodo_grantia','garantia',
        'id_ubicacion','fk_localizacion','localizacion','id_deposito','deposito','fk_despacho','despacho','cantidad_stock','detalle',
        'cantidad_en_movimiento','inventario')
        ->where('fk_despacho', $codigo_despacho)
        ->where('inventario', '<>','DES')
        ->whereRaw('cantidad_stock - cantidad_en_movimiento > 0')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    public function mostrarArticulosDepositoInventario($codigo_despacho){
        //Muestra los articulos disponibles en localizacion por despachos
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        leftjoin('inv_localizaciones','inv_localizaciones.id_localizacion','vw_articulo_ubicacion.fk_localizacion')
        ->select('vw_articulo_ubicacion.id_articulo','vw_articulo_ubicacion.inventario','vw_articulo_ubicacion.codigo','vw_articulo_ubicacion.marca',
        'vw_articulo_ubicacion.modelo','vw_articulo_ubicacion.descripcion','vw_articulo_ubicacion.ultimo_precio',
        'vw_articulo_ubicacion.precio_promedio','vw_articulo_ubicacion.periodo_grantia','vw_articulo_ubicacion.garantia',
        'vw_articulo_ubicacion.id_ubicacion','vw_articulo_ubicacion.fk_localizacion','vw_articulo_ubicacion.localizacion',
        'vw_articulo_ubicacion.id_deposito','vw_articulo_ubicacion.deposito','vw_articulo_ubicacion.fk_despacho','vw_articulo_ubicacion.despacho',
        'vw_articulo_ubicacion.cantidad_stock','vw_articulo_ubicacion.detalle',
        'vw_articulo_ubicacion.cantidad_en_movimiento','vw_articulo_ubicacion.inventario')
        ->where('vw_articulo_ubicacion.fk_despacho', $codigo_despacho)
        ->where('vw_articulo_ubicacion.inventario','INV')
        ->where('inv_localizaciones.estado', '<>', 'I')
        ->whereRaw('vw_articulo_ubicacion.cantidad_stock - vw_articulo_ubicacion.cantidad_en_movimiento > 0')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    public function mostrarArticulosDepositoDespacho($codigo_despacho){
        //Muestra los articulos disponibles en localizacion por despachos
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        leftjoin('inv_localizaciones','inv_localizaciones.id_localizacion','vw_articulo_ubicacion.fk_localizacion')
        ->select('vw_articulo_ubicacion.id_articulo','vw_articulo_ubicacion.inventario','vw_articulo_ubicacion.codigo','vw_articulo_ubicacion.marca',
        'vw_articulo_ubicacion.modelo','vw_articulo_ubicacion.descripcion','vw_articulo_ubicacion.ultimo_precio',
        'vw_articulo_ubicacion.precio_promedio','vw_articulo_ubicacion.periodo_grantia','vw_articulo_ubicacion.garantia',
        'vw_articulo_ubicacion.id_ubicacion','vw_articulo_ubicacion.fk_localizacion','vw_articulo_ubicacion.localizacion',
        'vw_articulo_ubicacion.id_deposito','vw_articulo_ubicacion.deposito','vw_articulo_ubicacion.fk_despacho','vw_articulo_ubicacion.despacho',
        'vw_articulo_ubicacion.cantidad_stock','vw_articulo_ubicacion.detalle',
        'vw_articulo_ubicacion.cantidad_en_movimiento','vw_articulo_ubicacion.inventario')
        ->where('vw_articulo_ubicacion.fk_despacho', $codigo_despacho)
        ->where('vw_articulo_ubicacion.inventario','DEP')
        ->where('inv_localizaciones.estado', '<>', 'I')
        ->whereRaw('vw_articulo_ubicacion.cantidad_stock - vw_articulo_ubicacion.cantidad_en_movimiento > 0')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    

    public function traerArticuloPorLocalizacion($fk_localizacion){
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        select('id_articulo','codigo','descripcion','marca','modelo','cantidad_stock','localizacion','deposito',
        'fk_localizacion','id_ubicacion','id_deposito','fk_despacho','despacho')
        ->where('fk_localizacion', $fk_localizacion)
        ->first();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    public function verificarExisteFK_Ubicacion_Origen($fk_ubicacion_origen){
        //verifica si existe un fk_ubicacion_origen en la tabla de inv_movimiento_det
        $vistaUbicacionArticulo = DetalleMovimientos::
        select('inv_movimiento_det.fk_ubicacion_origen','inv_movimiento_det.fk_movimiento_enc','inv_movimiento_enc.no_movimiento','inv_movimiento_det.cantidad')
        ->join('inv_movimiento_enc','inv_movimiento_enc.id_movimiento_enc','inv_movimiento_det.fk_movimiento_enc')
        ->where('inv_movimiento_det.fk_ubicacion_origen', $fk_ubicacion_origen)
        ->where('inv_movimiento_enc.estado','P')
        ->first();
        return response()->json([
            "ok" => true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    public function verificarExisteUbicacionMovimineto($id_ubicacion) {
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        //join('inv_movimiento_det','vw_articulo_ubicacion.id_ubicacion','inv_movimiento_det.fk_ubicacion_origen')
        select('vw_articulo_ubicacion.id_ubicacion')
        ->where('vw_articulo_ubicacion.id_ubicacion', $id_ubicacion)
        ->where('vw_articulo_ubicacion.cantidad_en_movimiento', '>', 0)
        ->count();
        return response()->json([
            "ok" => true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

    public function ubicacionesArticulos(){
        $vistaUbicacionArticulo = VistaUbicacionArticulo::
        select('id_articulo','codigo','descripcion','marca','modelo','id_ubicacion','fk_localizacion','localizacion',
        'deposito','id_deposito','cantidad_stock','detalle','cantidad_en_movimiento','fk_despacho','despacho')
        ->where('cantidad_stock', '>', 0)
        ->get();
        return response()->json([
            "ok" => true,
            "data" =>$vistaUbicacionArticulo
        ]);
    }

   


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VistaUbicacionArticulo  $vistaUbicacionArticulo
     * @return \Illuminate\Http\Response
     */
  
   
}
