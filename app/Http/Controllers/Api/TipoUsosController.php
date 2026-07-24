<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoUsos;
use Illuminate\Http\Request;

class TipoUsosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //mostrar los tipo de usos
         $tipoUsos = TipoUsos::select('inv_tipo_usos.id_tipo_uso', 'inv_tipo_usos.descripcion as tipo_uso')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  => $tipoUsos
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TipoUsos  $tipoUsos
     * @return \Illuminate\Http\Response
     */
    public function show(TipoUsos $tipoUsos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TipoUsos  $tipoUsos
     * @return \Illuminate\Http\Response
     */
    public function edit(TipoUsos $tipoUsos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoUsos  $tipoUsos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TipoUsos $tipoUsos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TipoUsos  $tipoUsos
     * @return \Illuminate\Http\Response
     */
    public function destroy(TipoUsos $tipoUsos)
    {
        //
    }
}
