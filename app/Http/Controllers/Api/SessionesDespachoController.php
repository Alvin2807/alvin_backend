<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessionDespacho;
use Illuminate\Http\Request;

class SessionesDespachoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //mostrar las seciones de informatica
        $sessionesDespacho = sessionDespacho::select('id_seccion', 'fk_despacho','descripcion as departamento', 'estado')
        ->where('estado', 'A')
        ->get();
        return response()->json([ 
            "ok" => true,
            "data" =>$sessionesDespacho
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
     * @param  \App\Models\SessionDespacho  $sessionDespacho
     * @return \Illuminate\Http\Response
     */
    public function show(SessionDespacho $sessionDespacho)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SessionDespacho  $sessionDespacho
     * @return \Illuminate\Http\Response
     */
    public function edit(SessionDespacho $sessionDespacho)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SessionDespacho  $sessionDespacho
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SessionDespacho $sessionDespacho)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SessionDespacho  $sessionDespacho
     * @return \Illuminate\Http\Response
     */
    public function destroy(SessionDespacho $sessionDespacho)
    {
        //
    }
}
