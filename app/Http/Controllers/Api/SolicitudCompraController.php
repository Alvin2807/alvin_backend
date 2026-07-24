<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SolicitudCompra;
use App\Http\Requests\StoreRequestOrdenCompra;
use App\Http\Requests\CancelarOrdenRequest;
use App\Models\DetallesCompras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Utils\Utilidades;
use App\Models\Articulos;
use App\Models\VistaOrdenCompra;
use App\Models\VistaDetalleOrdenCompra;
use App\Http\Requests\ConfirmarRequestOrdenCompra;
use App\Http\Requests\ConfirmarRequestTodoSitio;
use App\Http\Requests\EditarSolicitudRequest;
use App\Models\Proveedor;
use App\Models\UbicacionArticulo;
use App\Models\VistaArticulo;
use Carbon\Carbon;
use App\Http\Requests\ConfirmacionMixtaGlobal;
use App\Http\Requests\CancelarArticuloOrdenCompra;
use App\Http\Requests\ConfirmarParcialRequest;
use App\Http\Requests\RegistrarCajaMenudaRequest;
use App\Http\Requests\EditarCajaRequest;
use App\Http\Requests\ConfirmarCajaRequest;
use App\Http\Requests\MostrarCajaMenudaRequest;
class SolicitudCompraController extends Controller
{
    
    public function index()
    {
        //Mostrar solicitudes
        $solicitudCompra = VistaOrdenCompra::
        join('inv_tipo_solicitudes','inv_tipo_solicitudes.id_tipo_solicitud','VW_ORDEN_COMPRAS_RESUMIDO.fk_tipo_solicitud')
        ->select('id_solicitud','fk_tipo_solicitud','orden_compra','no_caja_menuda','estado','fk_solicitado','solicitado_por','inv_tipo_solicitudes.descripcion as tipo_solicitud',
        'VW_ORDEN_COMPRAS_RESUMIDO.cantidad_pedida','VW_ORDEN_COMPRAS_RESUMIDO.cantiadad_almacen','VW_ORDEN_COMPRAS_RESUMIDO.cantiadad_recibida')
        ->where('estado', '<>', 'I')
        ->orderBy('id_solicitud', 'desc')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$solicitudCompra,
        ]); 
    }

    public function store(StoreRequestOrdenCompra $request)
    {
        //registrar solicitud de compra
        try {
           DB::beginTransaction();
           $solicitudCompra = new SolicitudCompra();
           $solicitudCompra->orden_compra        =  strtoupper($request->input('orden_compra'));
           $solicitudCompra->fecha_orden         =  Utilidades::formatoFecha($request->input('fecha_orden'));
           $solicitudCompra->fecha_nota          =  Utilidades::formatoFecha($request->input('fecha_nota'));
           $solicitudCompra->fecha_referendo     =  $request->input('fecha_referendo');
           $solicitudCompra->fecha_publicacion   =  $request->input('fecha_publicacion');
           $solicitudCompra->no_factura          =  strtoupper($request->input('no_factura'));
           $solicitudCompra->no_nota             =  strtoupper($request->input('no_nota'));
           $solicitudCompra->fecha_factura       =  $request->input('fecha_factura');
           $solicitudCompra->no_solicitud_linea  =  strtoupper($request->input('no_solicitud_linea'));
           $solicitudCompra->no_solicitud_bienes =  strtoupper($request->input('no_solicitud_bienes'));
           $solicitudCompra->fk_solicitado       =  strtoupper($request->input('fk_solicitado'));
           $solicitudCompra->fk_seccion          =  $request->input('fk_seccion');
           $solicitudCompra->aprobado_por        =  ucwords($request->input('aprobado_por'));
           $solicitudCompra->plazo_entrega       =  $request->input('plazo_entrega');
           $solicitudCompra->periodo_entrega     =  strtoupper($request->input('periodo_entrega'));
           $solicitudCompra->termino_entrega     =  strtoupper($request->input('termino_entrega'));
           $solicitudCompra->fecha_aprox_entrega =  $request->input('fecha_aprox_entrega');
           $solicitudCompra->fk_proveedor        =  $request->input('fk_proveedor');
           $solicitudCompra->fk_tipo_solicitud   =  1;
           $solicitudCompra->observacion         =  ucfirst($request->input('observacion'));
           $solicitudCompra->estado              =  strtoupper('p');         
           $solicitudCompra->usuario_crea        =  strtoupper($request->input('usuario'));
           $solicitudCompra->save();

           $items = $request->input('articulosCompras');
           for ($i=0; $i <count($items) ; $i++) { 
               $detalleCompras = new DetallesCompras();
               $detalleCompras->fk_solicitud      = $solicitudCompra->id_solicitud;
               $detalleCompras->no_item           = $items[$i]['no_item'];
               $detalleCompras->fk_articulo       = $items[$i]['fk_articulo'];
               $detalleCompras->porcentaje        = $items[$i]['porcentaje'];
               $detalleCompras->refe_proveedor    = strtoupper($items[$i]['refe_proveedor']);
               $detalleCompras->cantidad_pedida   = $items[$i]['cantidad_pedida'];
               $detalleCompras->sub_tota          = $items[$i]['precio'] * $items[$i]['cantidad_pedida'];
               $detalleCompras->itbms             = ($detalleCompras->sub_tota) * ($items[$i]['porcentaje']/100);
               $detalleCompras->total             = $detalleCompras->sub_tota + $detalleCompras->itbms;
               $detalleCompras->cantidad_recibida = 0;
               $detalleCompras->cantidad_almacen  = 0;
               $detalleCompras->cantidad_sitio    = 0;
               $detalleCompras->precio            = $items[$i]['precio'];
               $detalleCompras->estado            = $solicitudCompra->estado;
               $detalleCompras->usuario_crea      = $solicitudCompra->usuario_crea;
               $detalleCompras->save();

               $consultaArticulos = Articulos::
               select('id_articulo','cantidad_pedida')
               ->where('id_articulo', $items[$i]['fk_articulo'])
               ->get();
               if (count($consultaArticulos ) > 0) {
                  $dataArticulos = new Articulos();
                  $articulos['cantidad_pedida']  = $consultaArticulos[0]['cantidad_pedida'] + $items[$i]['cantidad_pedida'];
                  $articulos['fecha_ult_compra'] = $solicitudCompra->fecha_orden;
                  $articulos['ultimo_precio']    = $items[$i]['precio'];
                  $articulos['usuario_modifica'] = $solicitudCompra->usuario_crea;
                  $dataArticulos = Articulos::where('id_articulo', $items[$i]['fk_articulo'])->update($articulos);
               }
           }

           $solicitud = new SolicitudCompra();
           $data['sub_total'] = $this->sumarSubtotal($detalleCompras->fk_solicitud);
           $data['itbms']     = $this->sumarITBMS($detalleCompras->fk_solicitud);
           $data['total']     = $this->sumarTotal($detalleCompras->fk_solicitud);
           $solicitud         = SolicitudCompra::where('id_solicitud', $detalleCompras->fk_solicitud)->update($data);

           $proveedores = new Proveedor();
           $dataProveedor['fecha_ult_compra'] =  $solicitudCompra->fecha_orden;
           $dataProveedor['usuario_modifica'] =  $solicitudCompra->usuario_crea;
           $proveedores = Proveedor::where('id_proveedor', $solicitudCompra->fk_proveedor)->update($dataProveedor);

           DB::commit();
           return response()->json([
               "ok" => true,
               "data" =>$solicitudCompra,
               "aprobado" => 'Se guardo satisfactoriamente'

           ]);

        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok" => false,
                "data" =>$ex->getMessage(),
                "error" => 'Hubo un error en el registro, consulte con el administrador del sistema'
 
            ]);
            
        }
    }

    public function mostrarNotas(Request $request)
    {
        //
        $nota = $request->input('nota');
        $id_solicitud = $request->input('id_solicitud');

        $solicitudCompra = SolicitudCompra:: 
        select('id_solicitud','no_nota')
        ->where('no_nota',$nota)->where('id_solicitud', '<>', $id_solicitud)->count();
        return response()->json([
            "ok" =>true,
            "data"=>$solicitudCompra,
        ]);
        
    }

    public function verificarOrdenCompraExistente(Request $request, SolicitudCompra $solicitudCompra)
    {
        //verifica si la orden de compra existe
        $orden_compra = $request->input('orden_compra');
        $id_solicitud = $request->input('id_solicitud');

        $solicitudCompra = SolicitudCompra:: 
        select('id_solicitud','orden_compra')
        ->where('orden_compra', $orden_compra)->where('id_solicitud','<>',  $id_solicitud)->count();
        return response()->json([
            "ok" =>true,
            "data"=>$solicitudCompra,
        ]);
    }

    public function mostrarSolicitudDeCompraPendiente(SolicitudCompra $solicitudCompra)
    {
        //Mostrar solicitud de compras pendientes
        $solicitudCompra  = VistaOrdenCompra::
        select('id_solicitud','orden_compra','fk_solicitado','solicitado_por','cantidad_pedida','cantidad_recibida','recibida_en_almacen','recibida_en_sitio','estado_solicitud','fk_tipo_solicitud',
        'tipo_solicitud')
        ->where('estado_solicitud','Pendiente')
        ->orderBy('id_solicitud', 'desc')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  => $solicitudCompra ,
            "Pendientes" =>count($solicitudCompra ),
           
        ]);
    }

    public function sumarSubtotal($id_solicitud){
      $subTotalDetalleCompra = DetallesCompras::
      select('fk_solicitud','sub_tota')
      ->where('fk_solicitud',$id_solicitud)
      ->sum('sub_tota');
      return $subTotalDetalleCompra;
    }

    public function sumarITBMS($id_solicitud){
        $subTotalDetalleCompra = DetallesCompras::
        select('fk_solicitud','itbms')
        ->where('fk_solicitud',$id_solicitud)
        ->sum('itbms');
        return $subTotalDetalleCompra;
    }

    public function sumarTotal($id_solicitud){
        $subTotalDetalleCompra = DetallesCompras::
        select('fk_solicitud','total')
        ->where('fk_solicitud',$id_solicitud)
        ->sum('total');
        return $subTotalDetalleCompra;
    }

    public function detalle_de_solicitud_de_compras_pendientes($id_solicitud)
    {
        //mostrar solicitud de orden de compra pendiente
        $solicitudCompra = VistaOrdenCompra::
        select('id_solicitud','fk_solicitado','orden_compra','fecha_orden','aprobado_por','no_nota','fecha_nota',
        'no_solicitud_linea','no_solicitud_bienes','fk_proveedor','no_factura','fecha_factura','fk_seccion','plazo_entrega','periodo_entrega', 'termino_entrega','fecha_referendo',
        'fecha_publicacion','fecha_aprox_entrega','observacion','codigo_proveedor','tipo_solicitud','seccion_interna','solicitado_por','proveedor','sub_total','itbms','total')
        ->where('id_solicitud', $id_solicitud)
        ->where('estado_solicitud', 'Pendiente')
        ->first();

        $detallesComprasArticulos = VistaDetalleOrdenCompra::
        join('vw_articulos','vw_articulos.id_articulo','vw_detalle_orden_compras.fk_articulo')
        ->select('vw_detalle_orden_compras.fk_articulo','vw_detalle_orden_compras.fk_tipo_solicitud','vw_detalle_orden_compras.item',
        'vw_detalle_orden_compras.codigo','vw_detalle_orden_compras.descripcion','vw_detalle_orden_compras.marca','vw_detalle_orden_compras.modelo',
        'vw_detalle_orden_compras.cantidad_pedida','vw_detalle_orden_compras.fk_unidad_medida',
        'vw_detalle_orden_compras.refe_proveedor','vw_detalle_orden_compras.porcentaje','vw_detalle_orden_compras.precio',
        'vw_detalle_orden_compras.cantidad_recibida','vw_detalle_orden_compras.sub_total','vw_detalle_orden_compras.itbms','vw_detalle_orden_compras.total',
        'vw_detalle_orden_compras.unidad_de_medida','vw_detalle_orden_compras.id_detalle','vw_articulos.cantidad_pedida as cantidad_pedida_articulos',
        'vw_detalle_orden_compras.cantidad_pedida as pedida_detalle')
        ->where('vw_detalle_orden_compras.estado_detalle','Pendiente')
        ->where('vw_detalle_orden_compras.fk_solicitud', $id_solicitud)
        ->orderBy('vw_detalle_orden_compras.id_detalle', 'asc')
        ->get();

        $solicitudCompra->articulosCompras = $detallesComprasArticulos; 
        return response()->json([ 
            "ok" =>true,
            "data" =>$solicitudCompra,
        ]);
        
    }

    public function totalSolicitudesCompras() {
        $solicitudCompra = VistaOrdenCompra::
        select('id_solicitud','fk_tipo_solicitud','estado_solicitud')
        ->count();
        return response()->json([
            "ok"   => true,
            "data" =>$solicitudCompra
        ]);
        
    }

    public function ultimasSolicitudCompras(){
        //muestra los ultimas 10 compras registradas
        $solicitudCompra = VistaOrdenCompra::
        select('id_solicitud','fk_tipo_solicitud','orden_compra','fecha_orden','no_caja_menuda','solicitado_por','Fecha_factura')
        ->where('estado_solicitud','Completado')
        ->orderBy('id_solicitud','desc')
        ->offset(0)->limit(5)->get();
        return response()->json([
            "ok"   => true,
            "data" =>$solicitudCompra,
            "totalConfirmadas" =>count($solicitudCompra)
        ]);
       
    }

    public function editarSolicitud(EditarSolicitudRequest $request){
        try {
            DB::beginTransaction();
            $id_solicitud    = $request->input('id_solicitud');
            $validar =SolicitudCompra::
            where('id_solicitud',$id_solicitud)
            ->where('estado', 'P')
            ->count(); 
            if ($validar) {
              $data['orden_compra']        = strtoupper($request->input('orden_compra'));
              $data['fecha_orden']         = $request->input('fecha_orden');
              $data['no_nota']             = strtoupper($request->input('no_nota'));
              $data['no_factura']          = strtoupper($request->input('no_factura'));
              $data['fecha_nota']          = $request->input('fecha_nota');
              $data['fecha_factura']       = $request->input('fecha_factura');
              $data['fecha_referendo']     = $request->input('fecha_referendo');
              $data['fecha_publicacion']   = $request->input('fecha_publicacion');
              $data['no_solicitud_linea']  = strtoupper($request->input('no_solicitud_linea'));
              $data['no_solicitud_bienes'] = strtoupper($request->input('no_solicitud_bienes'));
              $data['fk_seccion']          = $request->input('fk_seccion');
              $data['fk_solicitado']       = strtoupper($request->input('fk_solicitado'));
              $data['aprobado_por']        = ucwords($request->input('aprobado_por'));
              $data['plazo_entrega']       = $request->input('plazo_entrega');
              $data['periodo_entrega']     = strtoupper($request->input('periodo_de_entrega'));
              $data['termino_entrega']     = strtoupper($request->input('termino_de_entrega'));
              $data['fk_proveedor']        = $request->input('fk_proveedor');
              $data['fecha_aprox_entrega'] = $request->input('fecha_aprox_entrega');
              $data['observacion']         = ucfirst($request->input('observacion'));
              $data['usuario_modifica']    = strtoupper($request->input('usuario'));
              $solicitudCompra = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($data);

              $item = $request->input('articulosCompras');
              for ($i=0; $i <count($item) ; $i++) { 
                 if (isset($item[$i]['id_detalle'])) {
                    $detalleCompras = new DetallesCompras();
                    $detalles['cantidad_pedida']  = $item[$i]['cantidad_pedida'];
                    $detalles['precio']           = $item[$i]['precio'];
                    $detalles['porcentaje']       = $item[$i]['porcentaje'];
                    $detalles['sub_tota']         = $item[$i]['precio']   * $item[$i]['cantidad_pedida'];
                    $detalles['itbms']            = $detalles['sub_tota'] * $item[$i]['porcentaje'] / 100;
                    $detalles['total']            = $detalles['sub_tota'] + $detalles['itbms'];
                    $detalleCompras['estado']     = $data['usuario_modifica'];
                    $detalleCompras = DetallesCompras::where('id_detalle', $item[$i]['id_detalle'])->update($detalles);

                    $solicitudCompras = new SolicitudCompra();
                    $solicitud['sub_total'] =  $this->sumarSubtotal($id_solicitud);
                    $solicitud['itbms']     =  $this->sumariTBMS($id_solicitud);
                    $solicitud['total']     =  $this->sumarTotal($id_solicitud);
                    $solicitudCompras = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($solicitud);

                    $dataArticulos = new Articulos();
                    $articulos['cantidad_pedida']  = $item[$i]['cantidad_pedida_articulos'] - $item[$i]['pedida_detalle'] + $item[$i]['cantidad_pedida'];
                    $articulos['fecha_ult_compra'] = $data['fecha_orden'];
                    $articulos['ultimo_precio']    = $item[$i]['precio'];
                    $articulos['usuario_modifica'] = $data['usuario_modifica'];
                    $dataArticulos = Articulos::where('id_articulo', $item[$i]['fk_articulo'])->update($articulos);
                    
                 } else {
                    $detallesCompras = new DetallesCompras;
                    $detallesCompras->fk_solicitud    = $id_solicitud;
                    $detallesCompras->fk_articulo     = $item[$i]['fk_articulo'];
                    $detallesCompras->no_item         = $item[$i]['item'];
                    $detallesCompras->refe_proveedor  = $item[$i]['refe_proveedor'];
                    $detallesCompras->precio          = $item[$i]['precio'];
                    $detallesCompras->sub_tota        = $item[$i]['precio'] * $item[$i]['cantidad_pedida'];
                    $detallesCompras->itbms           = $detallesCompras->sub_tota * $item[$i]['porcentaje']/100;
                    $detallesCompras->total           = $detallesCompras->sub_tota + $detallesCompras->itbms; 
                    $detallesCompras->cantidad_pedida = $item[$i]['cantidad_pedida'];
                    $detallesCompras->cantidad_recibida = 0;
                    $detallesCompras->cantidad_almacen  =  0;
                    $detallesCompras->estado            = 'P';
                    $detallesCompras->porcentaje        = $item[$i]['porcentaje'];
                    $detallesCompras->usuario_crea      = $data['usuario_modifica'];
                    $detallesCompras->save();
                    $consultaArticulos = Articulos::
                    select('id_articulo','cantidad_pedida')
                    ->where('id_articulo', $item[$i]['fk_articulo'])
                    ->get();
                    if (count($consultaArticulos ) > 0) {
                        $articulos = new Articulos();
                        $articulos_detalles['fecha_ult_compra'] = $data['fecha_orden'];
                        $articulos_detalles['ultimo_precio']    = $item[$i]['precio'];
                        $articulos_detalles['cantidad_pedida']  = $consultaArticulos[0]['cantidad_pedida'] + $item[$i]['cantidad_pedida'];
                        $articulos = Articulos::where('id_articulo', $item[$i]['fk_articulo'])->update($articulos_detalles);
                    }
                 }
              }
              
              DB::commit();
              return response()->json([
                  "ok" =>true,
                  "data" => $solicitudCompra,
                  "mensaje" =>'Se guardo satisfactoriamente'
              ]);

            } else {
                return 'No se puede editar esta solicitud de compra.';
            }

        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data" =>$ex->getMessage(),
                "errorEdicion" =>'Hubo un error con el registro, consulte con el administrador de sistema'
            ]);
            
        }

    }

    public function datosPDFSolicitudCompra($id_solicitud)
    {
        //mostrar solicitud de orden de compra pendiente
        $solicitudCompra = VistaOrdenCompra::
        join('inv_proveedores','inv_proveedores.id_proveedor','VW_ORDEN_COMPRAS_RESUMIDO.fk_proveedor')
       ->select('VW_ORDEN_COMPRAS_RESUMIDO.id_solicitud','VW_ORDEN_COMPRAS_RESUMIDO.fk_solicitado','VW_ORDEN_COMPRAS_RESUMIDO.orden_compra','VW_ORDEN_COMPRAS_RESUMIDO.fecha_orden'
       ,'VW_ORDEN_COMPRAS_RESUMIDO.aprobado_por','VW_ORDEN_COMPRAS_RESUMIDO.no_nota','VW_ORDEN_COMPRAS_RESUMIDO.fecha_nota',
        'VW_ORDEN_COMPRAS_RESUMIDO.no_solicitud_linea','VW_ORDEN_COMPRAS_RESUMIDO.no_solicitud_bienes',
        'VW_ORDEN_COMPRAS_RESUMIDO.fk_proveedor','VW_ORDEN_COMPRAS_RESUMIDO.no_factura','VW_ORDEN_COMPRAS_RESUMIDO.fecha_factura','VW_ORDEN_COMPRAS_RESUMIDO.fk_seccion',
        'VW_ORDEN_COMPRAS_RESUMIDO.plazo_entrega','VW_ORDEN_COMPRAS_RESUMIDO.periodo_entrega', 'VW_ORDEN_COMPRAS_RESUMIDO.termino_entrega','VW_ORDEN_COMPRAS_RESUMIDO.fecha_referendo',
        'VW_ORDEN_COMPRAS_RESUMIDO.fecha_publicacion','VW_ORDEN_COMPRAS_RESUMIDO.proveedor','inv_proveedores.direccion','inv_proveedores.telefono1','inv_proveedores.ruc','inv_proveedores.contacto',
       'inv_proveedores.dv', 'inv_proveedores.email','VW_ORDEN_COMPRAS_RESUMIDO.solicitado_por',
        'VW_ORDEN_COMPRAS_RESUMIDO.fecha_aprox_entrega','VW_ORDEN_COMPRAS_RESUMIDO.observacion', 'inv_proveedores.codigo_proveedor','VW_ORDEN_COMPRAS_RESUMIDO.sub_total',
        'VW_ORDEN_COMPRAS_RESUMIDO.itbms','VW_ORDEN_COMPRAS_RESUMIDO.total','VW_ORDEN_COMPRAS_RESUMIDO.nombre_solicitante','VW_ORDEN_COMPRAS_RESUMIDO.no_caja_menuda')
        ->where('VW_ORDEN_COMPRAS_RESUMIDO.id_solicitud', $id_solicitud)
        ->where('VW_ORDEN_COMPRAS_RESUMIDO.estado_solicitud', '<>', 'Inactivo')
        ->first();

        $detallesComprasArticulos = VistaDetalleOrdenCompra::
        select('id_detalle','item','descripcion','cantidad_pedida','precio','itbms','sub_total','total','codigo','fk_unidad_medida','unidad_de_medida')
        ->where('fk_solicitud', $id_solicitud)
        ->where('estado_detalle','<>', 'Inactivo')
        ->orderBy('id_detalle', 'asc')
        ->get(); 
    
        $solicitudCompra->articulosComprasPDF = $detallesComprasArticulos;
        return response()->json([ 
            "ok" =>true,
            "data" =>$solicitudCompra,
        ]);
        
    }

    public function cancelarOrdenCompra(CancelarOrdenRequest $request){
        try {
            DB::beginTransaction();
                $id_solicitud = $request->input('id_solicitud');
                $solicitudCompras = new SolicitudCompra();
                $dataSolicitud['estado'] = 'I';
                $dataSolicitud['usuario_modifica'] =  strtoupper($request->input('usuario'));
                $solicitudCompras = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($dataSolicitud);

                $item = $request->input('articulosCompras');
                for ($i=0; $i <count($item) ; $i++) { 
                    if (isset($item[$i]['id_detalle'])) {
                        $detalles_compras = new DetallesCompras;
                        $detalles['estado'] = 'I';
                        $detalles['usuario_modifica'] = $dataSolicitud['usuario_modifica'];
                        $detalles_compras = DetallesCompras::where('id_detalle', $item[$i]['id_detalle'])->update($detalles);

                        $articulos = new Articulos;
                        $articulos_detalles['cantidad_pedida'] =  $item[$i]['cantidad_pedida_articulos'] - $item[$i]['pedida_detalle'];
                        $articulos_detalles['usuario_modifica'] = $dataSolicitud['usuario_modifica'];
                        $articulos = Articulos::where('id_articulo', $item[$i]['fk_articulo'])->update($articulos_detalles);
                    } 
                }

                DB::commit();
                return response()->json([
                    "ok" => true,
                    "data" =>$solicitudCompras,
                    "mensaje" => 'Se cancelo satisfcatoriamente'
                ]);
            
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok" => false,
                "data" =>$ex->getMessage(),
                "errorCancelarOrden" => 'Hubo un error consulte con el administrador del sistema'
            ]);
        }
    }

    public function verificarExisteCantidadRecibidaOrdenCompra($id_solicitud){
        $solicitudCompras = VistaOrdenCompra::
        select('id_solicitud','cantidad_recibida')
        ->where('id_solicitud', $id_solicitud)
        ->where('cantidad_recibida', '>', 0)
        ->where('estado_solicitud', 'Pendiente')
        ->get();
        if (count($solicitudCompras) > 0) {
           return response()->json([
               "ok" =>true,
               "data"=>$solicitudCompras,
               "mensajeExisteCantidad" => 'No se puede cancelar esta orden de compra'
           ]);
        }
    }

    public function confirmarTodoAlmacen(ConfirmarRequestOrdenCompra $request){

        try {
            DB::beginTransaction();
            $id_solicitud = $request->input('id_solicitud');
            $validar = SolicitudCompra::
            where('id_solicitud',$id_solicitud)
            ->where('estado', 'P')
            ->count(); 
            if ($validar) {
                $solicitudCompra             = new SolicitudCompra();
                $data['no_factura']          = strtoupper($request->input('no_factura'));
                $data['fecha_factura']       = ($request->input('fecha_factura'));
                $data['usuario_modifica']    = strtoupper($request->input('usuario'));
                $data['observacion']         = ucfirst($request->input('observacion'));
                $solicitudCompra = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($data);
                $item = $request->input('articulosCompras');
                for ($i=0; $i <count($item) ; $i++) { 
                  if ($item[$i]['recibir_en'] == 'A') {
                      // CONFIRMAR TODO EN ALMACÉN
                      $consulta = DetallesCompras::
                      select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_almacen')
                      ->where('id_detalle', $item[$i]['id_detalle'])
                      ->where('estado','P')
                      ->get();
                      if (count($consulta) > 0) {
                         $detalleCompra = new DetallesCompras();
                         $detalle['cantidad_recibida'] = $item[$i]['cantidad_confirmar'] + $consulta[0]['cantidad_recibida'];
                         $detalle['cantidad_almacen']  = $item[$i]['cantidad_confirmar'] + $consulta[0]['cantidad_almacen'];
                         $detalle['recibido_en']       = strtoupper('a');
                         $detalle['usuario_modifica']  = $data['usuario_modifica'];
                         $detalleCompra = DetallesCompras::findOrFail($consulta[0]['id_detalle']);
                         $detalleCompra->update($detalle);
                      }

                      $consultaCantidad = VistaDetalleOrdenCompra::
                      select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_almacen')
                      ->where('id_detalle', $item[$i]['id_detalle'])
                      ->where('estado_detalle','Pendiente')
                      ->get();
                      if (count($consultaCantidad) > 0) {
                        $detalleCantidad = new DetallesCompras();
                        if ($consultaCantidad[0]['cantidad_pedida'] == $consultaCantidad[0]['cantidad_recibida'] ) {
                            $estadoDetalle['estado']           = strtoupper('c');
                            $estadoDetalle['usuario_modifica'] = $data['usuario_modifica'];
                            $detalleCantidad = DetallesCompras::where('id_detalle', $consultaCantidad[0]['id_detalle'])->update($estadoDetalle);
                        }
                        
                      }

                      //Consultar la cantidad pedida y recibida de la vista VW_ORDEN_COMPRAS_RESUMIDO
                      $solicitud = VistaOrdenCompra::
                      select('id_solicitud','cantidad_pedida','cantidad_recibida')
                      ->where('id_solicitud', $id_solicitud)
                      ->where('estado_solicitud', 'Pendiente')
                      ->get();
                      if (count($solicitud) > 0) {
                         $solicitudEstado = new SolicitudCompra();
                         if ($solicitud[0]['cantidad_pedida'] == $solicitud[0]['cantidad_recibida']) {
                            $estadoCompra['estado'] = strtoupper('c');
                            $estadoCompra['usuario_modifica']   = $data['usuario_modifica'];
                            $estadoCompra['fecha_real_entrega'] = Carbon::now()->format('Y-m-d');
                            $solicitudEstado = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($estadoCompra);
                         }
                      }

                      $articulos = VistaArticulo::
                      select('id_articulo','cantidad_pedida','cantidad_almacen')
                      ->where('id_articulo',  $item[$i]['fk_articulo'])
                      ->get();
                      if (count($articulos) > 0) {
                        $detalleCompra->articulo()->update([
                            'precio_promedio'  =>  $this->precioPromedio(DetallesCompras::all()),
                            'cantidad_almacen' =>  $item[$i]['cantidad_confirmar']  + $articulos[0]['cantidad_almacen'],
                            'cantidad_pedida'  =>  $articulos[0]['cantidad_pedida'] - $item[$i]['cantidad_confirmar'],
                            'usuario_modifica' =>  $data['usuario_modifica']
                        ]);
                      }
                  } 
                }
                DB::commit();
                return response()->json([
                    "ok" =>true,
                    "data" =>$solicitudCompra,
                    "confirmado" => 'Se confirmó satisfactoriamente'
                ]);

            } else {
                return 'No se puede confirmar esta solicitud';
            }
           
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" => false,
                "data" =>$th->getMessage(),
                "errorConfirmar" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }

    }

    public function confirmacionMixtaGlobal(ConfirmacionMixtaGlobal $request){
        try {
            DB::beginTransaction();
            $id_solicitud = $request->input('id_solicitud');
            $validar = SolicitudCompra::
            where('id_solicitud',$id_solicitud)
            ->where('estado', 'P')
            ->count(); 
            if ($validar) {
                $solicitudCompra             = new SolicitudCompra();
                $data['no_factura']          = strtoupper($request->input('no_factura'));
                $data['fecha_factura']       = $request->input('fecha_factura');
                $data['usuario_modifica']    = strtoupper($request->input('usuario'));
                $data['observacion']         = ucfirst($request->input('observacion'));
                $solicitudCompra = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($data);

                $item = $request->input('articulosCompras');
                for ($i=0; $i <count($item) ; $i++) { 
                    //Confirmar en almacén
                   if ($item[$i]['recibir_en'] == 'A') {
                    $consulta = DetallesCompras::
                    select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_almacen')
                    ->where('id_detalle', $item[$i]['id_detalle'])
                    ->where('estado','P')
                    ->get();
                    if (count($consulta) > 0) {
                       $detalleCompra = new DetallesCompras();
                       $detalle['cantidad_recibida'] = $item[$i]['cantidad_confirmar'] + $consulta[0]['cantidad_recibida'];
                       $detalle['cantidad_almacen']  = $item[$i]['cantidad_confirmar'] + $consulta[0]['cantidad_almacen'];
                       $detalle['recibido_en']       = strtoupper('a');
                       $detalle['usuario_modifica']  = $data['usuario_modifica'];
                       $detalleCompra = DetallesCompras::findOrFail($consulta[0]['id_detalle']);
                       $detalleCompra->update($detalle);
                    }

                    $consultaCantidad = VistaDetalleOrdenCompra::
                    select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_almacen')
                    ->where('id_detalle', $item[$i]['id_detalle'])
                    ->where('estado_detalle','Pendiente')
                    ->get();
                    if (count($consultaCantidad) > 0) {
                      $detalleCantidad = new DetallesCompras();
                      if ($consultaCantidad[0]['cantidad_pedida'] == $consultaCantidad[0]['cantidad_recibida'] ) {
                          $estadoDetalle['estado']           = strtoupper('c');
                          $estadoDetalle['usuario_modifica'] = $data['usuario_modifica'];
                          $detalleCantidad = DetallesCompras::where('id_detalle', $consultaCantidad[0]['id_detalle'])->update($estadoDetalle);
                      }
                      
                    }

                      //Consultar la cantidad pedida y recibida de la vista VW_ORDEN_COMPRAS_RESUMIDO
                      $solicitud = VistaOrdenCompra::
                      select('id_solicitud','cantidad_pedida','cantidad_recibida')
                      ->where('id_solicitud', $id_solicitud)
                      ->where('estado_solicitud', 'Pendiente')
                      ->get();
                      if (count($solicitud) > 0) {
                         $solicitudEstado = new SolicitudCompra();
                         if ($solicitud[0]['cantidad_pedida'] == $solicitud[0]['cantidad_recibida']) {
                            $estadoCompra['estado'] = strtoupper('c');
                            $estadoCompra['usuario_modifica']   = $data['usuario_modifica'];
                            $estadoCompra['fecha_real_entrega'] = Carbon::now()->format('Y-m-d');
                            $solicitudEstado = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($estadoCompra);
                         }
                      }

                      $articulos = VistaArticulo::
                      select('id_articulo','cantidad_pedida','cantidad_almacen')
                      ->where('id_articulo',  $item[$i]['fk_articulo'])
                      ->get();
                      if (count($articulos) > 0) {
                        $detalleCompra->articulo()->update([
                            'precio_promedio'  =>  $this->precioPromedio(DetallesCompras::all()),
                            'cantidad_almacen' =>  $item[$i]['cantidad_confirmar']  + $articulos[0]['cantidad_almacen'],
                            'cantidad_pedida'  =>  $articulos[0]['cantidad_pedida'] - $item[$i]['cantidad_confirmar'],
                            'usuario_modifica' =>  $data['usuario_modifica']
                        ]);
                      }

                      
                   }

                   if ($item[$i]['recibir_en'] == 'S') {
                    $solicitudCompra             = new SolicitudCompra();
                    $data['no_factura']          = strtoupper($request->input('no_factura'));
                    $data['fecha_factura']       = $request->input('fecha_factura');
                    $data['usuario_modifica']    = strtoupper($request->input('usuario'));
                    $data['observacion']         = ucfirst($request->input('observacion'));
                    $solicitudCompra = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($data);
    
                    $consultaSitio = DetallesCompras::
                    select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_sitio')
                    ->where('id_detalle', $item[$i]['id_detalle'])
                    ->where('estado','P')
                    ->get();
                    if (count( $consultaSitio) > 0) {
                       $detalleCompra = new DetallesCompras();
                       $detalleSitio['cantidad_recibida'] = $item[$i]['cantidad_confirmar'] +  $consultaSitio[0]['cantidad_recibida'];
                       $detalleSitio['cantidad_sitio']    = $item[$i]['cantidad_confirmar'] +  $consultaSitio[0]['cantidad_sitio'];
                       $detalleSitio['fk_localizacion']   = $item[$i]['id_localizacion'];
                       $detalleSitio['recibido_en_sitio'] = strtoupper('s');
                       $detalleSitio['usuario_modifica']  = $data['usuario_modifica'];
                       $detalleCompra = DetallesCompras::findOrFail($consultaSitio[0]['id_detalle']);
                       $detalleCompra->update($detalleSitio);
                    }

                    $consultaCantidadSitio = VistaDetalleOrdenCompra::
                      select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_sitio')
                      ->where('id_detalle', $item[$i]['id_detalle'])
                      ->where('estado_detalle','Pendiente')
                      ->get();
                      if (count($consultaCantidadSitio) > 0) {
                        $detalleCantidadSitio = new DetallesCompras();
                        if ($consultaCantidadSitio[0]['cantidad_pedida'] == $consultaCantidadSitio[0]['cantidad_recibida'] ) {
                            $estadoDetalleSitio['estado']           = strtoupper('c');
                            $estadoDetalleSitio['usuario_modifica'] = $data['usuario_modifica'];
                            $detalleCantidadSitio = DetallesCompras::where('id_detalle', $consultaCantidadSitio[0]['id_detalle'])->update($estadoDetalleSitio);
                        }
                        
                      }

                       //Consultar la cantidad pedida y recibida de la vista VW_ORDEN_COMPRAS_RESUMIDO
                       $solicitudSiTIO = VistaOrdenCompra::
                       select('id_solicitud','cantidad_pedida','cantidad_recibida')
                       ->where('id_solicitud', $id_solicitud)
                       ->where('estado_solicitud', 'Pendiente')
                       ->get();
                       if (count($solicitudSiTIO) > 0) {
                          $solicitudEstadoSitio = new SolicitudCompra();
                          if ($solicitudSiTIO[0]['cantidad_pedida'] == $solicitudSiTIO[0]['cantidad_recibida']) {
                             $estadoCompraSitio['estado'] = strtoupper('c');
                             $estadoCompraSitio['usuario_modifica']   = $data['usuario_modifica'];
                             $estadoCompraSitio['fecha_real_entrega'] = Carbon::now()->format('Y-m-d');
                             $solicitudEstadoSitio = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($estadoCompraSitio);
                          }
                       }

                       $articulosSitio = VistaArticulo::
                       select('id_articulo','cantidad_pedida','cantidad_stock')
                       ->where('id_articulo',  $item[$i]['fk_articulo'])
                       ->get();
                       if (count($articulosSitio) > 0) {
                         $detalleCompra->articulo()->update([
                             'precio_promedio'  =>  $this->precioPromedio(DetallesCompras::all()),
                             'cantidad_stock'   =>  $item[$i]['cantidad_confirmar']  + $articulosSitio[0]['cantidad_stock'],
                             'cantidad_pedida'  =>  $articulosSitio[0]['cantidad_pedida'] - $item[$i]['cantidad_confirmar'],
                             'usuario_modifica' =>  $data['usuario_modifica']
                         ]);
                       }

                       $mostrarUbicacion = UbicacionArticulo::select('id_ubicacion' ,'cantidad_stock')
                       ->where('fk_articulo', $item[$i]['fk_articulo'])
                       ->where('fk_localizacion', $item[$i]['id_localizacion'])
                       ->get();
                       if (count($mostrarUbicacion) > 0) {
                           $localizarArticulo = new UbicacionArticulo();
                           $localizacion['fk_localizacion']  = $item[$i]['id_localizacion'];
                           $localizacion['fk_articulo']      = $item[$i]['fk_articulo'];
                           $localizacion['cantidad_stock']   = $mostrarUbicacion[0]['cantidad_stock'] + ($item[$i]['cantidad_confirmar']);
                           $localizacion['usuario_modifica'] = $data['usuario_modifica'];
                           $localizarArticulo = UbicacionArticulo::where('id_ubicacion',  $mostrarUbicacion[0]['id_ubicacion'])->update($localizacion);
                       } else { 
                           $ubicarArticulo = new UbicacionArticulo;
                           $ubicarArticulo->fk_localizacion = $item[$i]['id_localizacion'];
                           $ubicarArticulo->fk_articulo     = $item[$i]['fk_articulo'];
                           $ubicarArticulo->cantidad_stock  = $item[$i]['cantidad_confirmar'];
                           $ubicarArticulo->usuario_crea    = $data['usuario_modifica'];
                           $ubicarArticulo->save();
                       } 


                   }
                }
                DB::commit();
                return response()->json([
                    "ok"=> true,
                    "data"=>$solicitudCompra,
                    "exitosoMixta" => 'Se confirmó satisfatoriamente'
                ]);
                
            } else {
                return 'No se puede confirmar esta solicitud';
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"    => false,
                "data"  =>$ex->getMessage(),
                "errorMixta" =>'Hubo un error consulte con el administrador del sistema'
            ]);
        }

    }


    private function precioPromedio($detallesCompras){
        $primero = $detallesCompras->first();
        $ultimo = $detallesCompras->last();

        $precioSubTotalPrimero = $primero->cantidad_pedida * $primero->precio;
        $precioSubTotalUltimo = $ultimo->cantidad_pedida * $ultimo->precio;

        $precioPromedio = ($precioSubTotalPrimero + $precioSubTotalUltimo)/($primero->cantidad_pedida + $ultimo->cantidad_pedida);

        return $precioPromedio;
    }

    public function confirmarTodoSitio(ConfirmarRequestTodoSitio $request){
        try {
            DB::beginTransaction();
            $id_solicitud = $request->input('id_solicitud');
            $validar = SolicitudCompra::
            where('id_solicitud',$id_solicitud)
            ->where('estado', 'P')
            ->count(); 
            if ($validar) {
                $solicitudCompra             = new SolicitudCompra();
                $data['no_factura']          = strtoupper($request->input('no_factura'));
                $data['fecha_factura']       = ($request->input('fecha_factura'));
                $data['usuario_modifica']    = strtoupper($request->input('usuario'));
                $solicitudCompra = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($data);

                $item = $request->input('articulosCompras');
                for ($i=0; $i <count($item) ; $i++) { 
                   if ($item[$i]['recibir_en'] == 'S') {
                    $consulta = DetallesCompras::
                    select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_almacen','cantidad_sitio')
                    ->where('id_detalle', $item[$i]['id_detalle'])
                    ->where('estado','P')
                    ->get();
                    if (count($consulta) > 0) {
                       $detalleCompra = new DetallesCompras();
                       $detalle['cantidad_recibida'] = $item[$i]['cantidad_confirmar'] + $consulta[0]['cantidad_recibida'];
                       $detalle['cantidad_sitio']    = $item[$i]['cantidad_confirmar'] + $consulta[0]['cantidad_sitio'];
                       $detalle['recibido_en_sitio'] = strtoupper('s');
                       $detalle['fk_localizacion']   = $item[$i]['id_localizacion'];
                       $detalle['usuario_modifica']  = $data['usuario_modifica'];
                       $detalleCompra = DetallesCompras::findOrFail($consulta[0]['id_detalle']);
                       $detalleCompra->update($detalle);
                    }

                    $consultaCantidad = VistaDetalleOrdenCompra::
                      select('id_detalle','cantidad_pedida','cantidad_recibida')
                      ->where('id_detalle', $item[$i]['id_detalle'])
                      ->where('estado_detalle','Pendiente')
                      ->get();
                      if (count($consultaCantidad) > 0) {
                        $detalleCantidad = new DetallesCompras();
                        if ($consultaCantidad[0]['cantidad_pedida'] == $consultaCantidad[0]['cantidad_recibida'] ) {
                            $estadoDetalle['estado']           = strtoupper('c');
                            $estadoDetalle['usuario_modifica'] = $data['usuario_modifica'];
                            $detalleCantidad = DetallesCompras::where('id_detalle', $consultaCantidad[0]['id_detalle'])->update($estadoDetalle);
                        }
                        
                      }

                      //Consultar la cantidad pedida y recibida de la vista VW_ORDEN_COMPRAS_RESUMIDO
                      $solicitud = VistaOrdenCompra::
                      select('id_solicitud','cantidad_pedida','cantidad_recibida')
                      ->where('id_solicitud', $id_solicitud)
                      ->where('estado_solicitud', 'Pendiente')
                      ->get();
                      if (count($solicitud) > 0) {
                         $solicitudEstado = new SolicitudCompra();
                         if ($solicitud[0]['cantidad_pedida'] == $solicitud[0]['cantidad_recibida']) {
                            $estadoCompra['estado'] = strtoupper('c');
                            $estadoCompra['usuario_modifica']   = $data['usuario_modifica'];
                            $estadoCompra['fecha_real_entrega'] = Carbon::now()->format('Y-m-d');
                            $solicitudEstado = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($estadoCompra);
                         }
                      }

                      $articulos = VistaArticulo::
                      select('id_articulo','cantidad_pedida','cantidad_almacen', 'cantidad_stock')
                      ->where('id_articulo',  $item[$i]['fk_articulo'])
                      ->get();
                      if (count($articulos) > 0) {
                        $detalleCompra->articulo()->update([
                            'precio_promedio'  =>  $this->precioPromedio(DetallesCompras::all()),
                            'cantidad_stock'   =>  $item[$i]['cantidad_confirmar']  + $articulos[0]['cantidad_stock'],
                            'cantidad_pedida'  =>  $articulos[0]['cantidad_pedida'] - $item[$i]['cantidad_confirmar'],
                            'usuario_modifica' =>  $data['usuario_modifica']
                        ]);
                      }

                      $mostrarUbicacion = UbicacionArticulo::select('id_ubicacion' ,'cantidad_stock')
                        ->where('fk_articulo', $item[$i]['fk_articulo'])
                        ->where('fk_localizacion', $item[$i]['id_localizacion'])
                        ->get();
                        if (count($mostrarUbicacion) > 0) {
                            $localizarArticulo = new UbicacionArticulo();
                            $localizacion['fk_localizacion']  = $item[$i]['id_localizacion'];
                            $localizacion['fk_articulo']      = $item[$i]['fk_articulo'];
                            $localizacion['cantidad_stock']   = $mostrarUbicacion[0]['cantidad_stock'] + ($item[$i]['cantidad_confirmar']);
                            $localizacion['usuario_modifica'] = $data['usuario_modifica'];
                            $localizarArticulo = UbicacionArticulo::where('id_ubicacion',  $mostrarUbicacion[0]['id_ubicacion'])->update($localizacion);
                        } else { 
                            $ubicarArticulo = new UbicacionArticulo;
                            $ubicarArticulo->fk_localizacion = $item[$i]['id_localizacion'];
                            $ubicarArticulo->fk_articulo     = $item[$i]['fk_articulo'];
                            $ubicarArticulo->cantidad_stock  = $item[$i]['cantidad_confirmar'];
                            $ubicarArticulo->usuario_crea    = $data['usuario_modifica'];
                            $ubicarArticulo->save();
                        } 
                      
                   }
                }
                DB::commit();
                return response()->json([
                    "ok" =>true,
                    "data" =>$solicitudCompra,
                    "confirmadoSitio" => 'Se confirmó satisfactoriamente'
                ]);
            } else {
                return 'No se puede confirmar esta solicitud';
            }

        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" => false,
                "data" =>$th->getMessage(),
                "errorConfirmarSitio" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }

    }

    public function totalSolicitudesPendientesCompras(){
        $solicitudCompras = VistaOrdenCompra::
        select('fk_solicitud')
        ->where('estado_solicitud','Pendiente')
        ->count();
        return response()->json([
            "ok"   => true,
            "data" => $solicitudCompras
        ]);
    }

    public function traerArticulosEnAlmacenDetalleCompras($fk_articulo){
        $articulos = VistaArticulo::
        select('id_articulo','codigo','descripcion','marca','modelo','cantidad_almacen')
        ->where('id_articulo', $fk_articulo)
        ->first();
        $solicitudCompras = VistaDetalleOrdenCompra::
        select('fk_solicitud','fk_tipo_solicitud','tipo_solicitud','estado_solicitud','orden_compra','no_caja_menuda','cantidad_almacen')
        ->where('fk_articulo', $fk_articulo)
        ->where('cantidad_almacen', '>', 0)
        ->get();
        $articulos->articulos = $solicitudCompras; 
        return response()->json([
            "ok"   => true,
            "data" => $articulos
        ]);

    }

    public function cancelarArticuloCompra (CancelarArticuloOrdenCompra $request) {
        
        DB::beginTransaction();
        try {
            $id_detalle   = $request->input('id_detalle');
            $fk_articulo  = $request->input('fk_articulo');
            $cantidad_pedida_articulos = $request->input('cantidad_pedida_articulos');
            $cantidad_pedida = $request->input('cantidad_pedida');
            $usuario = strtoupper($request->input('usuario'));

            $detalleCompra = VistaDetalleOrdenCompra::
            select('id_detalle','fk_articulo','estado','cantidad_recibida')
            ->where('id_detalle',  $id_detalle)
            ->where('fk_articulo', $fk_articulo)
            ->where('cantidad_recibida', '>', 0)
            ->count();

            if ($detalleCompra) {
                return response()->json([
                    "ok"   =>true,
                    "data" =>$detalleCompra,
                    "mensajeNoCancelado"=> 'No se puede cancelar este artículo, porque tiene cantidad recibida.'
                ]);
            } else {
               $actualizar = new DetallesCompras();
               $data['estado'] = 'I';
               $data['fk_localizacion'] = '';
               $data['usuario_modifica'] = $usuario;
               $actualizar = DetallesCompras::where('id_detalle', $id_detalle)->update($data);

               $articulos = new Articulos();
               $dataArticulo['usuario_modifica'] = $usuario;
               $dataArticulo['cantidad_pedida']  = $cantidad_pedida_articulos - $cantidad_pedida;
               $articulos = Articulos::where('id_articulo', $fk_articulo)->update($dataArticulo);

               DB::commit();
               return response()->json([
                   "ok" =>true,
                   "data" =>$detalleCompra,
                   "cancelarArticulo"=> 'Se guardo satisfactoriamente'
               ]);
            }
          
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"   =>false,
                "data" =>$ex->getMessage(),
                "errorCancelarArticulo" => 'Hubo un error, consulte con el administrador del sistema.',

            ]);
        }

    }

    public function DisponibleAlmacen ($id_detalle){ 
        $disponible_almacen = DB::select("select FU_DISPONIBLE_ALMACEN($id_detalle) as disponible_almacen from dual");
        return response()->json([ 
            "ok" =>true,
            "data"=>$disponible_almacen
        ]);
    }

    public function ConfirmacionParcial(ConfirmarParcialRequest $request){
        try {
            DB::beginTransaction();
            $id_solicitud = $request->input('id_solicitud');
            $id_detalle   = $request->input('id_detalle');
            $fk_articulo  = $request->input('fk_articulo');
            $id_localizacion = $request->input('id_localizacion');
            $cantidad_confirmar = $request->input('cantidad_confirmar');
            $recibir_en   = strtoupper($request->input('recibir_en'));
            $usuario      = strtoupper($request->input('usuario'));
            $validar = VistaDetalleOrdenCompra::
            where('id_detalle',$id_detalle)
            ->where('estado_detalle', 'Pendiente')
            ->count(); 
            if ($validar) {
               if ($recibir_en == 'A') {
                $solicitudCompra             = new SolicitudCompra();
                $data['no_factura']          = strtoupper($request->input('no_factura'));
                $data['fecha_factura']       = ($request->input('fecha_factura'));
                $data['usuario_modifica']    = strtoupper($request->input('usuario'));
                $solicitudCompra = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($data);
                $consulta = DetallesCompras::
                select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_almacen')
                ->where('id_detalle',  $id_detalle)
                ->where('estado','P')
                ->get();
                if (count($consulta) > 0) {
                   $detalleCompra = new DetallesCompras();
                   $detalle['cantidad_recibida'] =  $cantidad_confirmar + $consulta[0]['cantidad_recibida'];
                   $detalle['cantidad_almacen']  =  $cantidad_confirmar + $consulta[0]['cantidad_almacen'];
                   $detalle['recibido_en']       =  strtoupper('a');
                   $detalle['usuario_modifica']  =  $data['usuario_modifica'];
                   $detalleCompra = DetallesCompras::findOrFail($consulta[0]['id_detalle']);
                   $detalleCompra->update($detalle);
                }
                $consultaCantidad = VistaDetalleOrdenCompra::
                      select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_almacen')
                      ->where('id_detalle', $id_detalle)
                      ->where('estado_detalle','Pendiente')
                      ->get();
                      if (count($consultaCantidad) > 0) {
                        $detalleCantidad = new DetallesCompras();
                        if ($consultaCantidad[0]['cantidad_pedida'] == $consultaCantidad[0]['cantidad_recibida']) {
                            $estadoDetalle['estado']           = strtoupper('c');
                            $estadoDetalle['usuario_modifica'] = $data['usuario_modifica'];
                            $detalleCantidad = DetallesCompras::where('id_detalle', $consultaCantidad[0]['id_detalle'])->update($estadoDetalle);
                        }
                        
                      }

                      //Consultar la cantidad pedida y recibida de la vista VW_ORDEN_COMPRAS_RESUMIDO
                      $solicitud = VistaOrdenCompra::
                      select('id_solicitud','cantidad_pedida','cantidad_recibida')
                      ->where('id_solicitud', $id_solicitud)
                      ->where('estado_solicitud', 'Pendiente')
                      ->get();
                      if (count($solicitud) > 0) {
                         $solicitudEstado = new SolicitudCompra();
                         if ($solicitud[0]['cantidad_pedida'] == $solicitud[0]['cantidad_recibida']) {
                            $estadoCompra['estado'] = strtoupper('c');
                            $estadoCompra['usuario_modifica']   = $data['usuario_modifica'];
                            $estadoCompra['fecha_real_entrega'] = Carbon::now()->format('Y-m-d');
                            $solicitudEstado = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($estadoCompra);
                         }
                      }

                      $articulos = VistaArticulo::
                      select('id_articulo','cantidad_pedida','cantidad_almacen')
                      ->where('id_articulo', $fk_articulo)
                      ->get();
                      if (count($articulos) > 0) {
                        $detalleCompra->articulo()->update([
                            'precio_promedio'  =>  $this->precioPromedio(DetallesCompras::all()),
                            'cantidad_almacen' =>  $cantidad_confirmar  + $articulos[0]['cantidad_almacen'],
                            'cantidad_pedida'  =>  $articulos[0]['cantidad_pedida'] - $cantidad_confirmar,
                            'usuario_modifica' =>  $data['usuario_modifica']
                        ]);
                      }
               } else if ($recibir_en == 'S') {
                    $solicitudCompra             = new SolicitudCompra();
                    $data['no_factura']          = strtoupper($request->input('no_factura'));
                    $data['fecha_factura']       = ($request->input('fecha_factura'));
                    $data['observacion']         = ucfirst($request->input('observacion'));
                    $data['usuario_modifica']    = strtoupper($request->input('usuario'));
                    $solicitudCompra = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($data);

                    $consultaSitio = DetallesCompras::
                    select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_sitio')
                    ->where('id_detalle',  $id_detalle)
                    ->where('estado','P')
                    ->get();
                    if (count( $consultaSitio) > 0) {
                    $detalleCompra = new DetallesCompras();
                    $detalleSitio['cantidad_recibida'] =  $cantidad_confirmar +  $consultaSitio[0]['cantidad_recibida'];
                    $detalleSitio['cantidad_sitio']    =  $cantidad_confirmar +  $consultaSitio[0]['cantidad_sitio'];
                    $detalleSitio['fk_localizacion']   =  $id_localizacion;
                    $detalleSitio['recibido_en_sitio'] =  strtoupper('s');
                    $detalleSitio['usuario_modifica']  =  $data['usuario_modifica'];
                    $detalleCompra = DetallesCompras::findOrFail($consultaSitio[0]['id_detalle']);
                    $detalleCompra->update($detalleSitio);
                    }

                    $consultaCantidadSitio = VistaDetalleOrdenCompra::
                    select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_almacen')
                    ->where('id_detalle', $id_detalle)
                    ->where('estado_detalle','Pendiente')
                    ->get();
                    if (count($consultaCantidadSitio) > 0) {
                    $detalleCantidadSitio = new DetallesCompras();
                    if ($consultaCantidadSitio[0]['cantidad_pedida'] == $consultaCantidadSitio[0]['cantidad_recibida'] ) {
                        $estadoDetalleSitio['estado']           = strtoupper('c');
                        $estadoDetalleSitio['usuario_modifica'] = $data['usuario_modifica'];
                        $detalleCantidadSitio = DetallesCompras::where('id_detalle', $consultaCantidadSitio[0]['id_detalle'])->update($estadoDetalleSitio);
                    }
                    
                    }

                    $articulosSitio = VistaArticulo::
                    select('id_articulo','cantidad_pedida','cantidad_stock')
                    ->where('id_articulo', $fk_articulo)
                    ->get();
                    if (count($articulosSitio) > 0) {
                    $detalleCompra->articulo()->update([
                        'precio_promedio'  =>  $this->precioPromedio(DetallesCompras::all()),
                        'cantidad_stock'   =>  $cantidad_confirmar + $articulosSitio[0]['cantidad_stock'],
                        'cantidad_pedida'  =>  $articulosSitio[0]['cantidad_pedida'] - $cantidad_confirmar,
                        'usuario_modifica' =>  $data['usuario_modifica']
                    ]);
                    }

                    $mostrarUbicacion = UbicacionArticulo::select('id_ubicacion' ,'cantidad_stock')
                    ->where('fk_articulo', $fk_articulo)
                    ->where('fk_localizacion', $id_localizacion)
                    ->get();
                    if (count($mostrarUbicacion) > 0) {
                        $localizarArticulo = new UbicacionArticulo();
                        $localizacion['fk_localizacion']  = $id_localizacion;
                        $localizacion['fk_articulo']      = $fk_articulo;
                        $localizacion['cantidad_stock']   = $mostrarUbicacion[0]['cantidad_stock'] + ($cantidad_confirmar);
                        $localizacion['usuario_modifica'] = $data['usuario_modifica'];
                        $localizarArticulo = UbicacionArticulo::where('id_ubicacion',  $mostrarUbicacion[0]['id_ubicacion'])->update($localizacion);
                    } else { 
                        $ubicarArticulo = new UbicacionArticulo;
                        $ubicarArticulo->fk_localizacion = $id_localizacion;
                        $ubicarArticulo->fk_articulo     = $fk_articulo;
                        $ubicarArticulo->cantidad_stock  = $cantidad_confirmar;
                        $ubicarArticulo->usuario_crea    = $data['usuario_modifica'];
                        $ubicarArticulo->save();
                    } 
               }

               DB::commit();

               return response()->json([
                   "ok"    => true,
                   "data"  => $detalleCompra,
                   "confirmadoParcial" => 'Se confirmó satisfactoriamente  '
               ]);
                
            } else {
                return 'No se puede confirmar este detalle de solicitud';
            }

            
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data" =>$ex->getMessage(),
                "errorConfirmacionParcial" =>'Hubo un error consulte con el administrador del sistema'
            ]);
        }

    }

    public function registrarCajamenuda(RegistrarCajaMenudaRequest $request){
        try {
            DB::beginTransaction();
            $solicitudCompra = new SolicitudCompra();
            $solicitudCompra->fecha_nota          =  Utilidades::formatoFecha($request->input('fecha_nota'));
            $solicitudCompra->no_factura          =  strtoupper($request->input('no_factura'));
            $solicitudCompra->no_caja_menuda      =  strtoupper($request->input('no_caja_menuda'));
            $solicitudCompra->no_nota             =  strtoupper($request->input('no_nota'));
            $solicitudCompra->fecha_factura       =  $request->input('fecha_factura');
            $solicitudCompra->fk_solicitado       =  strtoupper($request->input('fk_solicitado'));
            $solicitudCompra->fk_seccion          =  $request->input('fk_seccion');
            $solicitudCompra->aprobado_por        =  ucwords($request->input('aprobado_por'));
            $solicitudCompra->solicitado_por      =  ucwords($request->input('solicitado_por'));
            $solicitudCompra->fk_proveedor        =  $request->input('fk_proveedor');
            $solicitudCompra->fk_tipo_solicitud   =  2;
            $solicitudCompra->observacion         =  ucfirst($request->input('observacion'));
            $solicitudCompra->estado              =  strtoupper('p');         
            $solicitudCompra->usuario_crea        =  strtoupper($request->input('usuario'));
            $solicitudCompra->save();

            $items = $request->input('articulosCompras');
            for ($i=0; $i <count($items) ; $i++) { 
                $detalleCompras = new DetallesCompras();
                $detalleCompras->fk_solicitud      = $solicitudCompra->id_solicitud;
                $detalleCompras->no_item           = $items[$i]['no_item'];
                $detalleCompras->fk_articulo       = $items[$i]['fk_articulo'];
                $detalleCompras->fk_localizacion   = $items[$i]['fk_localizacion'];
                $detalleCompras->porcentaje        = $items[$i]['porcentaje'];
                $detalleCompras->refe_proveedor    = strtoupper($items[$i]['refe_proveedor']);
                $detalleCompras->cantidad_pedida   = $items[$i]['cantidad_pedida'];
                $detalleCompras->sub_tota          = $items[$i]['precio'] * $items[$i]['cantidad_pedida'];
                $detalleCompras->itbms             = ($detalleCompras->sub_tota) * ($items[$i]['porcentaje']/100);
                $detalleCompras->total             = $detalleCompras->sub_tota + $detalleCompras->itbms;
                $detalleCompras->cantidad_recibida = 0;
                $detalleCompras->cantidad_almacen  = 0;
                $detalleCompras->cantidad_sitio    = 0;
                $detalleCompras->precio            = $items[$i]['precio'];
                $detalleCompras->estado            = $solicitudCompra->estado;
                $detalleCompras->usuario_crea      = $solicitudCompra->usuario_crea;
                $detalleCompras->save();
 
                $consultaArticulos = Articulos::
                select('id_articulo','cantidad_pedida')
                ->where('id_articulo', $items[$i]['fk_articulo'])
                ->get();
                if (count($consultaArticulos ) > 0) {
                   $dataArticulos = new Articulos();
                   $articulos['cantidad_pedida']  = $consultaArticulos[0]['cantidad_pedida'] + $items[$i]['cantidad_pedida'];
                   $articulos['fecha_ult_compra'] = $solicitudCompra->fecha_nota;
                   $articulos['ultimo_precio']    = $items[$i]['precio'];
                   $articulos['usuario_modifica'] = $solicitudCompra->usuario_crea;
                   $dataArticulos = Articulos::where('id_articulo', $items[$i]['fk_articulo'])->update($articulos);
                }
            }

                $solicitud = new SolicitudCompra();
                $data['sub_total'] = $this->sumarSubtotal($detalleCompras->fk_solicitud);
                $data['itbms']     = $this->sumarITBMS($detalleCompras->fk_solicitud);
                $data['total']     = $this->sumarTotal($detalleCompras->fk_solicitud);
                $solicitud         = SolicitudCompra::where('id_solicitud', $detalleCompras->fk_solicitud)->update($data);
    
                $proveedores = new Proveedor();
                $dataProveedor['fecha_ult_compra'] =  $solicitudCompra->fecha_nota;
                $dataProveedor['usuario_modifica'] =  $solicitudCompra->usuario_crea;
                $proveedores = Proveedor::where('id_proveedor', $solicitudCompra->fk_proveedor)->update($dataProveedor);
 
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$solicitudCompra,
                "existoso"=>'Se guardo satisfactoriamente'
            ]);
         } catch (\Exception $th) {
           DB::rollBack();
           return response ()->json([
            "ok" =>false,
            "data" =>$th->getMessage(),
            "error" =>'Hubo un error consulte con el administrador del sistema'
           ]);
        }
    }

    public function detalle_de_caja_menuda_pendientes($id_solicitud)
    {
        //mostrar solicitud de orden de compra pendiente
        $solicitudCompra = VistaOrdenCompra::
        select('id_solicitud','fk_solicitado','aprobado_por','no_nota','fecha_nota','fk_proveedor','no_factura','fecha_factura','fk_seccion',
        'observacion','codigo_proveedor','tipo_solicitud','seccion_interna','solicitado_por','proveedor','sub_total','itbms','total','no_caja_menuda','nombre_solicitante')
        ->where('id_solicitud', $id_solicitud)
        ->where('estado_solicitud', 'Pendiente')
        ->first();

        $detallesComprasArticulos = VistaDetalleOrdenCompra::
        join('vw_articulos','vw_articulos.id_articulo','vw_detalle_orden_compras.fk_articulo')
        ->select('vw_detalle_orden_compras.fk_articulo','vw_detalle_orden_compras.fk_tipo_solicitud','vw_detalle_orden_compras.item',
        'vw_detalle_orden_compras.codigo','vw_detalle_orden_compras.descripcion','vw_detalle_orden_compras.marca','vw_detalle_orden_compras.modelo',
        'vw_detalle_orden_compras.cantidad_pedida','vw_detalle_orden_compras.fk_unidad_medida',
        'vw_detalle_orden_compras.refe_proveedor','vw_detalle_orden_compras.porcentaje','vw_detalle_orden_compras.precio',
        'vw_detalle_orden_compras.cantidad_recibida','vw_detalle_orden_compras.sub_total','vw_detalle_orden_compras.itbms','vw_detalle_orden_compras.total',
        'vw_detalle_orden_compras.unidad_de_medida','vw_detalle_orden_compras.id_detalle','vw_articulos.cantidad_pedida as cantidad_pedida_articulos','vw_detalle_orden_compras.fk_localizacion',
        'vw_detalle_orden_compras.cantidad_pedida as pedida_detalle')
        ->where('vw_detalle_orden_compras.estado_detalle','Pendiente')
        ->where('vw_detalle_orden_compras.fk_solicitud', $id_solicitud)
        ->orderBy('vw_detalle_orden_compras.id_detalle', 'asc')
        ->get();

        $solicitudCompra->articulosCompras = $detallesComprasArticulos; 
        return response()->json([ 
            "ok" =>true,
            "data" =>$solicitudCompra,
        ]);
        
    }

    public function editarCajaMenuda(EditarCajaRequest $request){
        try {
            DB::beginTransaction();
            $id_solicitud    = $request->input('id_solicitud');
            $validar =SolicitudCompra::
            where('id_solicitud',$id_solicitud)
            ->where('estado', 'P')
            ->count(); 
            if ($validar) {
              $data['no_caja_menuda']      = strtoupper($request->input('no_caja_menuda'));
              $data['no_nota']             = strtoupper($request->input('no_nota'));
              $data['fk_solicitado']       = strtoupper($request->input('fk_solicitado'));
              $data['no_factura']          = strtoupper($request->input('no_factura'));
              $data['fecha_nota']          = $request->input('fecha_nota');
              $data['fecha_factura']       = $request->input('fecha_factura');
              $data['fk_seccion']          = $request->input('fk_seccion');
              $data['aprobado_por']        = ucwords($request->input('aprobado_por'));
              $data['solicitado_por']      = ucwords($request->input('nombre_solicitante'));
              $data['fk_proveedor']        = $request->input('fk_proveedor');
              $data['observacion']         = ucfirst($request->input('observacion'));
              $data['usuario_modifica']    = strtoupper($request->input('usuario'));
              $solicitudCompra = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($data);

              $item = $request->input('articulosCompras');
              for ($i=0; $i <count($item) ; $i++) { 
                 if (isset($item[$i]['id_detalle'])) {
                    $detalleCompras = new DetallesCompras();
                    $detalles['cantidad_pedida']  = $item[$i]['cantidad_pedida'];
                    $detalles['precio']           = $item[$i]['precio'];
                    $detalles['porcentaje']       = $item[$i]['porcentaje'];
                    $detalles['sub_tota']         = $item[$i]['precio']   * $item[$i]['cantidad_pedida'];
                    $detalles['itbms']            = $detalles['sub_tota'] * $item[$i]['porcentaje'] / 100;
                    $detalles['total']            = $detalles['sub_tota'] + $detalles['itbms'];
                    $detalles['fk_localizacion']  = $item[$i]['fk_localizacion'];
                    $detalleCompras['estado']     = $data['usuario_modifica'];
                    $detalleCompras = DetallesCompras::where('id_detalle', $item[$i]['id_detalle'])->update($detalles);

                    $solicitudCompras = new SolicitudCompra();
                    $solicitud['sub_total'] =  $this->sumarSubtotal($id_solicitud);
                    $solicitud['itbms']     =  $this->sumariTBMS($id_solicitud);
                    $solicitud['total']     =  $this->sumarTotal($id_solicitud);
                    $solicitudCompras = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($solicitud);

                    $dataArticulos = new Articulos();
                    $articulos['cantidad_pedida']  = $item[$i]['cantidad_pedida_articulos'] - $item[$i]['pedida_detalle'] + $item[$i]['cantidad_pedida'];
                    $articulos['fecha_ult_compra'] = $data['fecha_nota'];
                    $articulos['ultimo_precio']    = $item[$i]['precio'];
                    $articulos['usuario_modifica'] = $data['usuario_modifica'];
                    $dataArticulos = Articulos::where('id_articulo', $item[$i]['fk_articulo'])->update($articulos);
                    
                 } else {
                    $detallesCompras = new DetallesCompras;
                    $detallesCompras->fk_solicitud    = $id_solicitud;
                    $detallesCompras->fk_articulo     = $item[$i]['fk_articulo'];
                    $detallesCompras->no_item         = $item[$i]['item'];
                    $detallesCompras->refe_proveedor  = $item[$i]['refe_proveedor'];
                    $detallesCompras->precio          = $item[$i]['precio'];
                    $detallesCompras->sub_tota        = $item[$i]['precio'] * $item[$i]['cantidad_pedida'];
                    $detallesCompras->itbms           = $detallesCompras->sub_tota * $item[$i]['porcentaje']/100;
                    $detallesCompras->total           = $detallesCompras->sub_tota + $detallesCompras->itbms; 
                    $detallesCompras->cantidad_pedida = $item[$i]['cantidad_pedida'];
                    $detallesCompras->cantidad_recibida = 0;
                    $detallesCompras->cantidad_almacen  =  0;
                    $detallesCompras->estado            = 'P';
                    $detallesCompras->porcentaje        = $item[$i]['porcentaje'];
                    $detallesCompras->usuario_crea      = $data['usuario_modifica'];
                    $detallesCompras->fk_localizacion   = $item[$i]['fk_localizacion'];
                    $detallesCompras->save();
                    $consultaArticulos = Articulos::
                    select('id_articulo','cantidad_pedida')
                    ->where('id_articulo', $item[$i]['fk_articulo'])
                    ->get();
                    if (count($consultaArticulos ) > 0) {
                        $articulos = new Articulos();
                        $articulos_detalles['fecha_ult_compra'] = $data['fecha_nota'];
                        $articulos_detalles['ultimo_precio']    = $item[$i]['precio'];
                        $articulos_detalles['cantidad_pedida']  = $consultaArticulos[0]['cantidad_pedida'] + $item[$i]['cantidad_pedida'];
                        $articulos = Articulos::where('id_articulo', $item[$i]['fk_articulo'])->update($articulos_detalles);
                    }
                 }
              }
              
              DB::commit();
              return response()->json([
                  "ok" =>true,
                  "data" => $solicitudCompra,
                  "mensaje" =>'Se guardo satisfactoriamente'
              ]);

            } else {
                return 'No se puede editar esta solicitud de compra.';
            }

        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data" =>$ex->getMessage(),
                "errorEdicion" =>'Hubo un error con el registro, consulte con el administrador de sistema'
            ]);
            
        }
    }

    public function confirmarCajaMenuda(ConfirmarCajaRequest $request){
        try {
            DB::beginTransaction();
            $id_solicitud = $request->input('id_solicitud');
            $validar = SolicitudCompra::
            where('id_solicitud',$id_solicitud)
            ->where('estado', 'P')
            ->count(); 
            if ($validar) {
                $solicitudCompra             = new SolicitudCompra();
                $data['no_caja_menuda']      = strtoupper($request->input('no_caja_menuda'));
                $data['no_nota']             = strtoupper($request->input('no_nota'));
                $data['fk_solicitado']       = strtoupper($request->input('fk_solicitado'));
                $data['no_factura']          = strtoupper($request->input('no_factura'));
                $data['fecha_nota']          = $request->input('fecha_nota');
                $data['fecha_factura']       = $request->input('fecha_factura');
                $data['fk_seccion']          = $request->input('fk_seccion');
                $data['aprobado_por']        = ucwords($request->input('aprobado_por'));
                $data['solicitado_por']      = ucwords($request->input('nombre_solicitante'));
                $data['fk_proveedor']        = $request->input('fk_proveedor');
                $data['observacion']         = ucfirst($request->input('observacion'));
                $data['usuario_modifica']    = strtoupper($request->input('usuario'));

                $item = $request->input('articulosCompras');
                for ($i=0; $i <count($item) ; $i++) { 
                    $consulta = DetallesCompras::
                    select('id_detalle','cantidad_pedida','cantidad_recibida','cantidad_almacen','cantidad_sitio')
                    ->where('id_detalle', $item[$i]['id_detalle'])
                    ->where('estado','P')
                    ->get();
                    if (count($consulta) > 0) {
                       $detalleCompra = new DetallesCompras();
                       $detalle['cantidad_recibida'] = $item[$i]['cantidad_pedida'] + $consulta[0]['cantidad_recibida'];
                       $detalle['cantidad_sitio']    = $item[$i]['cantidad_pedida'] + $consulta[0]['cantidad_sitio'];
                       $detalle['recibido_en_sitio'] = strtoupper('s');
                       $detalle['fk_localizacion']   = $item[$i]['fk_localizacion'];
                       $detalle['usuario_modifica']  = $data['usuario_modifica'];
                       $detalleCompra = DetallesCompras::findOrFail($consulta[0]['id_detalle']);
                       $detalleCompra->update($detalle);
                    }

                    $consultaCantidad = VistaDetalleOrdenCompra::
                      select('id_detalle','cantidad_pedida','cantidad_recibida')
                      ->where('id_detalle', $item[$i]['id_detalle'])
                      ->where('estado_detalle','Pendiente')
                      ->get();
                      if (count($consultaCantidad) > 0) {
                        $detalleCantidad = new DetallesCompras();
                        if ($consultaCantidad[0]['cantidad_pedida'] == $consultaCantidad[0]['cantidad_recibida'] ) {
                            $estadoDetalle['estado']           = strtoupper('c');
                            $estadoDetalle['usuario_modifica'] = $data['usuario_modifica'];
                            $detalleCantidad = DetallesCompras::where('id_detalle', $consultaCantidad[0]['id_detalle'])->update($estadoDetalle);
                        }
                        
                      }

                      //Consultar la cantidad pedida y recibida de la vista VW_ORDEN_COMPRAS_RESUMIDO
                      $solicitud = VistaOrdenCompra::
                      select('id_solicitud','cantidad_pedida','cantidad_recibida')
                      ->where('id_solicitud', $id_solicitud)
                      ->where('estado_solicitud', 'Pendiente')
                      ->get();
                      if (count($solicitud) > 0) {
                         $solicitudEstado = new SolicitudCompra();
                         if ($solicitud[0]['cantidad_pedida'] == $solicitud[0]['cantidad_recibida']) {
                            $estadoCompra['estado'] = strtoupper('c');
                            $estadoCompra['usuario_modifica']   = $data['usuario_modifica'];
                            $estadoCompra['fecha_real_entrega'] = Carbon::now()->format('Y-m-d');
                            $solicitudEstado = SolicitudCompra::where('id_solicitud', $id_solicitud)->update($estadoCompra);
                         }
                      }

                      $articulos = VistaArticulo::
                      select('id_articulo','cantidad_pedida','cantidad_almacen', 'cantidad_stock')
                      ->where('id_articulo',  $item[$i]['fk_articulo'])
                      ->get();
                      if (count($articulos) > 0) {
                        $detalleCompra->articulo()->update([
                            'precio_promedio'  =>  $this->precioPromedio(DetallesCompras::all()),
                            'cantidad_stock'   =>  $item[$i]['cantidad_pedida']  + $articulos[0]['cantidad_stock'],
                            'cantidad_pedida'  =>  $articulos[0]['cantidad_pedida'] - $item[$i]['cantidad_pedida'],
                            'usuario_modifica' =>  $data['usuario_modifica']
                        ]);
                      }

                      $mostrarUbicacion = UbicacionArticulo::select('id_ubicacion' ,'cantidad_stock')
                        ->where('fk_articulo', $item[$i]['fk_articulo'])
                        ->where('fk_localizacion', $item[$i]['fk_localizacion'])
                        ->get();
                        if (count($mostrarUbicacion) > 0) {
                            $localizarArticulo = new UbicacionArticulo();
                            $localizacion['fk_localizacion']  = $item[$i]['fk_localizacion'];
                            $localizacion['fk_articulo']      = $item[$i]['fk_articulo'];
                            $localizacion['cantidad_stock']   = $mostrarUbicacion[0]['cantidad_stock'] + ($item[$i]['cantidad_pedida']);
                            $localizacion['usuario_modifica'] = $data['usuario_modifica'];
                            $localizarArticulo = UbicacionArticulo::where('id_ubicacion',  $mostrarUbicacion[0]['id_ubicacion'])->update($localizacion);
                        } else { 
                            $ubicarArticulo = new UbicacionArticulo;
                            $ubicarArticulo->fk_localizacion = $item[$i]['fk_localizacion'];
                            $ubicarArticulo->fk_articulo     = $item[$i]['fk_articulo'];
                            $ubicarArticulo->cantidad_stock  = $item[$i]['cantidad_pedida'];
                            $ubicarArticulo->usuario_crea    = $data['usuario_modifica'];
                            $ubicarArticulo->save();
                        } 
                }
                DB::commit();
                return response()->json([
                    "ok" =>true,
                    "data" =>$solicitudCompra,
                    "confirmado" => 'Se confirmó satisfactoriamente'
                ]);
            } else {
                return 'No se puede confirmar esta solicitud';
            }

        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" => false,
                "data" =>$th->getMessage(),
                "errorConfirmar" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }

    }

    public function mostraCajaMenuda(MostrarCajaMenudaRequest $request)
    {
        
      try {
          DB::beginTransaction();
          $no_caja_menuda = strtoupper($request->input('no_caja_menuda'));
          $id_solicitud = $request->input('id_solicitud');
  
          $solicitudCompra = SolicitudCompra:: 
          select('id_solicitud','no_caja_menuda')
          ->where('no_caja_menuda',$no_caja_menuda)->where('id_solicitud', '<>', $id_solicitud)->count();
          DB::commit();
          return response()->json([
              "ok" =>true,
              "data"=>$solicitudCompra,
          ]);
      } catch (\Exception $ex) {
         DB::rollBack();
         return response()->json([
            "ok" =>false,
            "data" =>$ex->getMessage(),
            "error" =>'Hubo un error consulte con el administrador del sistema'
         ]);
      }
        
    }

    public function detalle_de_caja_menuda($id_solicitud)
    {
        //mostrar solicitud de orden de compra pendiente
        $solicitudCompra = VistaOrdenCompra::
        select('id_solicitud','fk_solicitado','aprobado_por','no_nota','fecha_nota','fk_proveedor','no_factura','fecha_factura','fk_seccion',
        'observacion','codigo_proveedor','tipo_solicitud','seccion_interna','solicitado_por','proveedor','sub_total','itbms','total','no_caja_menuda','nombre_solicitante')
        ->where('id_solicitud', $id_solicitud)
        ->where('tipo_solicitud','CAJA MENUDA')
        ->where('estado_solicitud', '<>', 'Inactiva')
        ->first();

        $detallesComprasArticulos = VistaDetalleOrdenCompra::
        join('vw_articulos','vw_articulos.id_articulo','vw_detalle_orden_compras.fk_articulo')
        ->select('vw_detalle_orden_compras.fk_articulo','vw_detalle_orden_compras.fk_tipo_solicitud','vw_detalle_orden_compras.item',
        'vw_detalle_orden_compras.codigo','vw_detalle_orden_compras.descripcion','vw_detalle_orden_compras.marca','vw_detalle_orden_compras.modelo',
        'vw_detalle_orden_compras.cantidad_pedida','vw_detalle_orden_compras.fk_unidad_medida',
        'vw_detalle_orden_compras.refe_proveedor','vw_detalle_orden_compras.porcentaje','vw_detalle_orden_compras.precio',
        'vw_detalle_orden_compras.cantidad_recibida','vw_detalle_orden_compras.sub_total','vw_detalle_orden_compras.itbms','vw_detalle_orden_compras.total',
        'vw_detalle_orden_compras.unidad_de_medida','vw_detalle_orden_compras.id_detalle','vw_articulos.cantidad_pedida as cantidad_pedida_articulos','vw_detalle_orden_compras.fk_localizacion',
        'vw_detalle_orden_compras.cantidad_pedida as pedida_detalle','vw_detalle_orden_compras.estado_detalle')
        ->where('vw_detalle_orden_compras.estado_detalle','<>','Inactiva')
        ->where('vw_detalle_orden_compras.tipo_solicitud','CAJA MENUDA')
        ->where('vw_detalle_orden_compras.fk_solicitud', $id_solicitud)
        ->orderBy('vw_detalle_orden_compras.id_detalle', 'asc')
        ->get();

        $solicitudCompra->articulosCompras = $detallesComprasArticulos; 
        return response()->json([ 
            "ok" =>true,
            "data" =>$solicitudCompra,
        ]);
        
    }

}
