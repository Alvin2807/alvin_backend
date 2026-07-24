<?php

namespace App\Http\Controllers;

use App\Models\FerDetalle;
use Illuminate\Http\Request;

class FerDetalleController extends Controller
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
     * @param  \App\Models\FerDetalle  $ferDetalle
     * @return \Illuminate\Http\Response
     */
    public function mostrarDetalleFR()
    {
        $ferDetalle = FerDetalle::select('inv_fer_detalle.id_fer_detalle','inv_fer_detalle.fk_fer_encabe','inv_fer_detalle.fk_detalle_compra',
        'inv_fer_detalle.cantidad_pedida','inv_fer_detalle.cantidad_recibida','inv_fer_detalle.estado','inv_fer_detalle.usuario_crea')
        ->get();
        return response()->json([
            "ok"    =>true,
            "data"  =>$ferDetalle
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FerDetalle  $ferDetalle
     * @return \Illuminate\Http\Response
     */
    public function edit(FerDetalle $ferDetalle)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FerDetalle  $ferDetalle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FerDetalle $ferDetalle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FerDetalle  $ferDetalle
     * @return \Illuminate\Http\Response
     */
    public function destroy(FerDetalle $ferDetalle)
    {
        //
    }
}
