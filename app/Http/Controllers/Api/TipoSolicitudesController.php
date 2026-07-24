<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoSolicitudes;
use Illuminate\Http\Request;

class TipoSolicitudesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //mostrando tipo de solicitudes
        $tipoSolicitudes = TipoSolicitudes::all();
        return response()->json([ 
            "data" => true,
            "tipo_solicitudes" =>$tipoSolicitudes
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
     * @param  \App\Models\TipoSolicitudes  $tipoSolicitudes
     * @return \Illuminate\Http\Response
     */
    public function show(TipoSolicitudes $tipoSolicitudes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TipoSolicitudes  $tipoSolicitudes
     * @return \Illuminate\Http\Response
     */
    public function edit(TipoSolicitudes $tipoSolicitudes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoSolicitudes  $tipoSolicitudes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TipoSolicitudes $tipoSolicitudes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TipoSolicitudes  $tipoSolicitudes
     * @return \Illuminate\Http\Response
     */
    public function destroy(TipoSolicitudes $tipoSolicitudes)
    {
        //
    }
}
