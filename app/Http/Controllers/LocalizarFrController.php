<?php

namespace App\Http\Controllers;

use App\Models\DetallesArticulos;
use App\Models\LocalizarFr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocalizarFrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LocalizarFr  $localizarFr
     * @return \Illuminate\Http\Response
     */
    public function mostrarLocalizacionFr()
    {
        $localizarFr = LocalizarFr:: 
        join('inv_fer_detalle', 'inv_fer_detalle.id_fer_detalle','inv_fer_localizacion.fk_fer_detalle')
        ->join('inv_localizaciones', 'inv_localizaciones.id_localizacion', 'inv_fer_localizacion.fk_localizacion')
        ->join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->join('inv_detalle_compras', 'inv_detalle_compras.id_detalle','inv_fer_detalle.fk_detalle_compra')
        ->join('inv_articulos','inv_articulos.id_articulo','inv_detalle_compras.fk_articulo')
        ->select('inv_fer_localizacion.id_fer_localizacion','inv_fer_detalle.id_fer_detalle','inv_fer_localizacion.cantidad_recibida','inv_localizaciones.descripcion as localizacion',
        'inv_depositos.descripcion as deposito','exp.despacho.descripcion as despacho','inv_articulos.descripcion as articulo','inv_articulos.codigo')
        ->where('inv_fer_localizacion.cantidad_recibida','>', 0)
        ->get();
        return response()->json([
            "ok"    => true,
            "data"  => $localizarFr
        ]);
    }

    public function verificarExisteLocalizacion($id_fer_localizacion){
        $existencia = DetallesArticulos::
        select('inv_detalle_articulo.id_detalle_articulo','inv_detalle_articulo.fk_fer_localizacion')
        ->where('inv_detalle_articulo.fk_fer_localizacion', $id_fer_localizacion)
        ->first();
        return response()->json([ 
        "ok" =>true,
        "data" =>$existencia
        ]);
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LocalizarFr  $localizarFr
     * @return \Illuminate\Http\Response
     */
    public function mostrarLocalizacionArticuloFR($id_fer_encabe)
    {
        $localizar = LocalizarFr::
        join('inv_fer_detalle','inv_fer_detalle.id_fer_detalle','inv_fer_localizacion.fk_fer_detalle')
        ->join('inv_detalle_compras','inv_detalle_compras.id_detalle','inv_fer_detalle.fk_detalle_compra')
        ->join('inv_articulos','inv_articulos.id_articulo', 'inv_detalle_compras.fk_articulo')
        ->leftJoin('inv_marcas','inv_marcas.id_marca','inv_articulos.fk_marca')
        ->leftJoin('inv_modelos','inv_modelos.id_modelo','inv_articulos.fk_modelo')
        ->join('inv_localizaciones','inv_localizaciones.id_localizacion','inv_fer_localizacion.fk_localizacion')
        ->join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('inv_fer_encabe','inv_fer_encabe.id_fer_encabe','inv_fer_detalle.fk_fer_encabe')
        ->select('inv_fer_localizacion.id_fer_localizacion', 'inv_fer_localizacion.fk_fer_detalle', 
        'inv_fer_localizacion.cantidad_recibida', 'inv_fer_detalle.id_fer_detalle', 'inv_fer_detalle.fk_detalle_compra', 
        'inv_detalle_compras.id_detalle','inv_detalle_compras.fk_articulo', 'inv_articulos.descripcion', 'inv_articulos.codigo', 
        'inv_marcas.nombre_marca', 'inv_modelos.nombre_modelo', 'inv_localizaciones.id_localizacion',
        'inv_localizaciones.descripcion as localizacion', 'inv_depositos.descripcion as deposito', 'inv_depositos.id_deposito', 'inv_fer_encabe.id_fer_encabe',
        'inv_fer_encabe.estado')
        ->where('inv_fer_encabe.id_fer_encabe', $id_fer_encabe)
        ->where('inv_fer_encabe.estado', 'C')
        ->get();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizar
            ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LocalizarFr  $localizarFr
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LocalizarFr $localizarFr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LocalizarFr  $localizarFr
     * @return \Illuminate\Http\Response
     */
    public function destroy(LocalizarFr $localizarFr)
    {
        //
    }

    public function mostrarLocalizacionPrederetimadaFR($id_localizacion) { 
        $localizarFr = LocalizarFr::
        join('inv_localizaciones','inv_localizaciones.id_localizacion','inv_fer_localizacion.fk_localizacion')
        ->join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->select('inv_fer_localizacion.id_fer_localizacion','inv_fer_localizacion.fk_localizacion','inv_localizaciones.descripcion','inv_localizaciones.estado',
        'inv_depositos.descripcion as deposito','inv_depositos.id_deposito')
        ->where('inv_localizaciones.id_localizacion', $id_localizacion)
        ->where('inv_localizaciones.estado','P')
        ->first();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizarFr
        ]);
    }
}
