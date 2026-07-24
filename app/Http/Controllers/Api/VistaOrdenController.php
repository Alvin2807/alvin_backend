<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VistaDetalleOrdenCompra;
use App\Models\VistaOrden;
use App\Models\VistaOrdenCompra;

class VistaOrdenController extends Controller
{
   
    public function index()
    {
        //Listar ordenes de compra en almacén
        $vistaOrden = VistaOrden::
        select('fk_tipo_solicitud','fk_solicitud','id_detalle','orden_compra','codigo','descripcion','marca','modelo','cantidad_almacen','fk_articulo')
        ->where('cantidad_almacen', '>', 0)
        ->get();
        return response([
            "ok" =>true,
            "data" =>$vistaOrden,
            "Total"=>count($vistaOrden)
        ]);
    }

    public function solicitudesComprasMias()
    {
        //solicitudes de compras de mi despacho
        $vistaOrden = VistaOrdenCompra::
        select('id_solicitud','fk_tipo_solicitud','tipo_solicitud','orden_compra','fecha_orden','fecha_factura','no_caja_menuda','cantidad_pedida',
        'cantidad_recibida','recibida_en_almacen','recibida_en_sitio','estado_solicitud','fk_solicitado')
        //->where('VW_ORDEN_COMPRAS_RESUMIDO.FK_SOLICITADO', $codigo_despacho)
        ->orderBy('id_solicitud','desc')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaOrden
        ]);
    }


    public function TotalsolicitudesComprasMiasPendientes($codigo_despacho)
    {
        //solicitudes de compras de mi despacho
        $vistaOrden = VistaOrdenCompra::
        select('id_solicitud','estado_solicitud')
        ->where('VW_ORDEN_COMPRAS_RESUMIDO.FK_SOLICITADO', $codigo_despacho)
        ->where('estado_solicitud', 'Pendiente')
        ->count();
        return response()->json([
            "ok" =>true,
            "data" =>$vistaOrden
        ]);
    }


    public function mostrarDetalleSolicitudMias($id_solicitud){
        $vistaOrden = VistaOrdenCompra::
        select('id_solicitud','no_nota','fecha_nota','observacion','aprobado_por','orden_compra',
        'fecha_orden','plazo_entrega','fecha_aprox_entrega','fecha_real_entrega','no_solicitud_linea',
        'no_solicitud_bienes','no_factura','fecha_factura','proveedor',
        'periodo_entrega','termino_entrega','estado_solicitud','fecha_publicacion','fecha_referendo','seccion_interna',
        'sub_total','itbms','total')
        ->where('id_solicitud',$id_solicitud)
        ->first();
        $detallesComprasArticulos = VistaDetalleOrdenCompra::
        select('id_detalle','codigo','descripcion','marca','modelo','unidad_de_medida','cantidad_pedida','cantidad_recibida',
        'cantidad_almacen','cantidad_sitio','localizacion','estado_detalle','item')
        ->where('fk_solicitud', $id_solicitud)
        ->orderBy('estado_detalle','desc')
        ->get();
        $vistaOrden->articulosCompras = $detallesComprasArticulos; 
        return response()->json([
            "ok"   => true,
            "data" =>$vistaOrden
        ]);
    }

}
