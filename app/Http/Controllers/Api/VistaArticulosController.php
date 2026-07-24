<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VistaArticulo;

class VistaArticulosController extends Controller
{
   
    public function index()
    {
        //
        $vistaArticulo = VistaArticulo::all();
        return response()->json([ 
            "ok" => true,
            "data" =>$vistaArticulo
        ]);
    }


    public function aticulosEnCantidadPedidaGlobal()
    {
        //articulos en cantidad pedida
        $vistaArticulo = VistaArticulo::
        join('inv_detalle_compras','inv_detalle_compras.fk_articulo','VW_ARTICULOS.ID_ARTICULO')
        ->join('inv_solicitud_compras', 'inv_solicitud_compras.id_solicitud', 'inv_detalle_compras.fk_solicitud')
        ->select('VW_ARTICULOS.ID_ARTICULO')
        ->where('VW_ARTICULOS.CANTIDAD_PEDIDA', '>', 0)
        ->count();
        return response()->json([
            "ok" =>true,
            "data"=>$vistaArticulo
        ]);
    }

    public function articulosCantidadPedida()
    {
        $vistaArticulo = VistaArticulo::
        join('inv_detalle_compras','inv_detalle_compras.fk_articulo','VW_ARTICULOS.ID_ARTICULO')
        ->join('inv_solicitud_compras', 'inv_solicitud_compras.id_solicitud', 'inv_detalle_compras.fk_solicitud')
        ->join('inv_tipo_solicitudes','inv_tipo_solicitudes.id_tipo_solicitud','inv_solicitud_compras.fk_tipo_solicitud')
        ->join('EXP.DESPACHO','EXP.DESPACHO.CODIGO', 'inv_solicitud_compras.FK_SOLICITADO')
        ->select('VW_ARTICULOS.ID_ARTICULO','VW_ARTICULOS.CODIGO', 'VW_ARTICULOS.DESCRIPCION', 'VW_ARTICULOS.MARCA', 'VW_ARTICULOS.MODELO', 'VW_ARTICULOS.CANTIDAD_PEDIDA',
        'inv_solicitud_compras.orden_compra','inv_solicitud_compras.fk_tipo_solicitud','inv_tipo_solicitudes.descripcion as tipo_solicitud','inv_solicitud_compras.no_caja_menuda',
        'exp.despacho.descripcion as despacho')
        ->where('VW_ARTICULOS.CANTIDAD_PEDIDA', '>', 0)
        ->get();
        return response()->json([
            "ok" =>true,
            "data"=>$vistaArticulo
        ]);
    }


    public function articulosEnAlmacen(VistaArticulo $vistaArticulo)
    {
        //
        $vistaArticulo = VistaArticulo::
        select('id_articulo','codigo','descripcion','marca','modelo','cantidad_almacen')
        ->where('cantidad_almacen', '>', 0)
        ->get();
        return response()->json([
            "ok" =>true,
            "data"=>$vistaArticulo
        ]);
    }
}
