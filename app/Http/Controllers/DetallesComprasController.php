<?php

namespace App\Http\Controllers;
use App\Models\DetallesCompras;
use App\Models\Articulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Utils\Utilidades;
use App\Models\UbicacionArticulo;
use App\Http\Requests\CancelarArticuloOrdenCompra;
use App\Http\Controllers\Api\SolicitudCompraController;
use App\Models\SolicitudCompra;
use App\Models\VistaDetalleOrdenCompra;

class DetallesComprasController extends Controller
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
     * @param  \App\Models\DetallesCompras  $detallesCompras
     * @return \Illuminate\Http\Response
     */
    public function mostrarArticulosFR(Request $request, $id_solicitud)
    {
        $selectOrdenFR = SolicitudCompra::
        join('inv_detalle_compras', 'inv_solicitud_compras.id_solicitud', 'inv_detalle_compras.fk_solicitud')
        ->join('inv_articulos', 'inv_articulos.id_articulo', 'inv_detalle_compras.fk_articulo')
        ->select('inv_solicitud_compras.id_solicitud', 'inv_articulos.descripcion as articulo', 'inv_articulos.id_articulo','inv_detalle_compras.id_detalle', 'inv_articulos.cantidad_almacen')
        ->where('id_solicitud', $id_solicitud)
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  => $selectOrdenFR
        ]);
    }




   



}
