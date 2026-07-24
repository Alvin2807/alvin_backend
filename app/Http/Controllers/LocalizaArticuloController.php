<?php

namespace App\Http\Controllers;

use App\Models\LocalizaArticulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocalizaArticuloController extends Controller
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
     * @param  \App\Models\LocalizaArticulo  $localizaArticulo
     * @return \Illuminate\Http\Response
     */
    public function mostrarLocalizaArticulos()
    {
        $localizacion = DB::table('inv_localiza_articulos as lca')
        ->join('inv_ubicacion_articulos as ua','ua.id_ubicacion','lca.fk_ubicacion')
        ->join('inv_articulos as ar','ar.id_articulo','ua.fk_articulo')
        ->join('inv_marcas as marc','marc.id_marca','ar.fk_marca')
        ->join('inv_modelos as mdl','mdl.id_modelo','ar.fk_modelo')
        ->join('inv_depositos as dep', 'dep.id_deposito','lca.fk_deposito')
        ->join('exp.despacho as desp', 'desp.codigo','dep.fk_despacho')
        ->select('lca.id_localizacion', 'ua.id_ubicacion', 'lca.fk_deposito', 'desp.descripcion as despacho', 'ar.descripcion as articulo',
         'marc.nombre_marca','mdl.nombre_modelo','dep.descripcion as deposito','lca.cantidad_stock')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  => $localizacion
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LocalizaArticulo  $localizaArticulo
     * @return \Illuminate\Http\Response
     */
    public function mostrarCantidadStock()
    {
       /*  $contarStock = LocalizaArticulo::select('inv_localiza_articulos.cantidad_stock')
        ->get()->sum('cantidad_stock');
        return response([ 
            "ok"    => true,
            "data"  =>$contarStock
        ]); */
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LocalizaArticulo  $localizaArticulo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LocalizaArticulo $localizaArticulo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LocalizaArticulo  $localizaArticulo
     * @return \Illuminate\Http\Response
     */
    public function destroy(LocalizaArticulo $localizaArticulo)
    {
        //
    }
}
