<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DetallesArticulos;
use App\Models\UbicacionArticulo;
use App\Models\VistaDetalleArticuloUbicacion;
use Illuminate\Http\Request;

class VistaDetalleArticuloUbicacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Mostrar detalles de articulos

        $vistaDetalleArticuloUbicacion = VistaDetalleArticuloUbicacion::
        select('id_articulo','codigo','descripcion','marca','modelo','id_ubicacion','fk_localizacion','localizacion','id_deposito','deposito',
        'fk_despacho','despacho','descripcion_especifica','numero_serie','numero_activo','codigo_barra','fk_color','color','estatus','fk_fer_localizacion',
        'fk_detalle_compra','id_detalle_articulo')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaDetalleArticuloUbicacion
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mostrarDetalleArticulo($id_ubicacion)
    {
        //mostrar detalle de articulos por ubicacion
        $vistaDetalleArticuloUbicacion = UbicacionArticulo::
        join('inv_articulos','inv_articulos.id_articulo','inv_ubicacion_articulos.fk_articulo')
        ->leftJoin('inv_marcas','inv_marcas.id_marca','inv_articulos.fk_marca')
        ->leftJoin('inv_modelos','inv_modelos.id_modelo','inv_articulos.fk_modelo')
        ->join('inv_localizaciones','inv_localizaciones.id_localizacion','inv_ubicacion_articulos.fk_localizacion')
        ->join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_ubicacion_articulos.id_ubicacion','inv_articulos.codigo','inv_articulos.descripcion as articulo','inv_marcas.nombre_marca','inv_modelos.nombre_modelo',
        'inv_ubicacion_articulos.fk_localizacion','inv_localizaciones.descripcion as localizacion','inv_depositos.descripcion as deposito',
        'inv_ubicacion_articulos.cantidad_stock','exp.despacho.descripcion as despacho','exp.despacho.codigo as codigo_despacho','inv_articulos.requiere_activo')
        ->where('inv_ubicacion_articulos.id_ubicacion', $id_ubicacion)
        ->where('inv_ubicacion_articulos.detalle', 'SI')
        ->first();

        $detalleArticulos = DetallesArticulos::
        leftJoin('inv_movimiento_det','inv_detalle_articulo.id_detalle_articulo','inv_movimiento_det.fk_detalle_origen')
        ->leftJoin('inv_colores','inv_colores.id_color','inv_detalle_articulo.fk_color')
        ->select('inv_detalle_articulo.id_detalle_articulo','inv_movimiento_det.fk_detalle_origen','inv_detalle_articulo.numero_activo','inv_detalle_articulo.numero_serie',
        'inv_detalle_articulo.codigo_barra','inv_colores.descripcion as color','inv_detalle_articulo.fk_color','inv_detalle_articulo.descripcion',
        'inv_detalle_articulo.estatus','inv_detalle_articulo.fk_ubicacion')
        ->where('inv_detalle_articulo.fk_ubicacion', $id_ubicacion)
        ->get();

        $vistaDetalleArticuloUbicacion->detalleArticulo = $detalleArticulos;
        return response()->json([
            "ok" => true,
            "data" => $vistaDetalleArticuloUbicacion
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VistaDetalleArticuloUbicacion  $vistaDetalleArticuloUbicacion
     * @return \Illuminate\Http\Response
     */
    public function mostrarDetalleArticulosPorUbicacion($id_ubicacion)
    {
        //Mostrar articulos por ubicacion en detalle
        $vistaDetalleArticuloUbicacion =  VistaDetalleArticuloUbicacion::
      
        select('id_articulo','codigo','descripcion','marca','modelo','id_ubicacion','fk_localizacion','localizacion','id_deposito','deposito','fk_despacho','despacho',
        'numero_serie','numero_activo','codigo_barra','color','estatus','id_detalle_articulo','disponible','descripcion_especifica')
        ->where('id_ubicacion', $id_ubicacion)
        ->where('estatus','A')
        ->where('disponible', '<>', 'NO')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaDetalleArticuloUbicacion
        ]);
    }

    
}
