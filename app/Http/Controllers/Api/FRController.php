<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FerDetalle;
use App\Models\FR;
use App\Models\Articulos;
use Illuminate\Http\Request;
use App\Models\DetallesCompras;
use App\Models\LocalizarFr;
use App\Utils\Utilidades;
use App\Models\Localizaciones;
use App\Models\UbicacionArticulo;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\FrStoreRequest;
use App\Http\Requests\FrModificarDatosGeneralesRequest;
use App\Http\Requests\CancelarArticuloFrRequest;
use App\Http\Requests\CancelarFrRequest;
use App\Http\Requests\ConfirmarFrRequest;
use App\Http\Requests\RevertirRequestFR;
use App\Models\DetalleArticulo;
use App\Models\DetallesArticulos;
use App\Models\VistaFrDetallado;
use PhpParser\Node\Stmt\Foreach_;

class FRController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Mostrar FR pendeientes
    
        $fr = FR::
        join('exp.despacho', 'exp.despacho.codigo','inv_fer_encabe.fk_despacho')
        ->select('inv_fer_encabe.id_fer_encabe','exp.despacho.descripcion as despacho','inv_fer_encabe.no_control','inv_fer_encabe.fecha_entrega','inv_fer_encabe.lugar_entrega','inv_fer_encabe.solicitado_por',
        'inv_fer_encabe.aprobado_por','inv_fer_encabe.entregado_por','inv_fer_encabe.recibido_por','inv_fer_encabe.observacion','inv_fer_encabe.estado','inv_fer_encabe.fecha_crea', 'inv_fer_encabe.usuario_crea',
        'inv_fer_encabe.fecha_modifica','inv_fer_encabe.usuario_modifica')
        ->where('inv_fer_encabe.estado','P')
        ->get();
        return response()->json([ 
         "ok"  => true,
         "data"  => $fr,
         "PendientesFR" =>count($fr)
        ]);
 
    }

    public function mostrarHistorialFR()
    {
        $fr = FR::
        join('exp.despacho', 'exp.despacho.codigo','inv_fer_encabe.fk_despacho')
        ->select('inv_fer_encabe.id_fer_encabe','exp.despacho.descripcion as despacho','inv_fer_encabe.no_control','inv_fer_encabe.solicitado_por','inv_fer_encabe.estado')
        ->where('inv_fer_encabe.estado','P')
        ->orwhere('inv_fer_encabe.estado','C')
        ->get();
        return response()->json([ 
         "ok"  => true,
         "data"  => $fr,
        ]);
    }

  
    public function store(FrStoreRequest $request)
    {
        //Registrar FR

        try {
          
            DB::beginTransaction();
          $fr = new FR();
          $fr->fk_despacho      =  $request->input('fk_despacho');
          $fr->fecha_entrega    =  $request->input('fecha_entrega');
          $fr->lugar_entrega    = strtoupper($request->input('lugar_entrega'));
          $fr->solicitado_por   = ucwords($request->input('solicitado_por'));
          $fr->aprobado_por     = ucwords($request->input('aprobado_por'));
          $fr->entregado_por    = strtoupper($request->input('entregado_por'));
          $fr->recibido_por     = ucwords($request->input('recibido_por'));
          $fr->observacion      = ucfirst($request->input('observacion'));
          $fr->usuario_crea     = strtoupper($request->input('usuario_crea'));
          $fr->estado           = 'P';
          $fr->save();

          $item = $request->input('articulosFR');

          for ($i=0; $i <count($item) ; $i++) { 
          
             $ferDetalles = new FerDetalle();
             $ferDetalles->fk_fer_encabe       = $fr->id_fer_encabe;
             $ferDetalles->fk_detalle_compra   = $item[$i]['id_detalle'];
             $ferDetalles->cantidad_pedida     = $item[$i]['cantidad_solicitar'];
             $ferDetalles->cantidad_recibida   = $item[$i]['cantidad_solicitar'] - $item[$i]['cantidad_solicitar'];
             $ferDetalles->usuario_crea        = strtoupper($request->input('usuario_crea'));
             $ferDetalles->estado              = 'P';
             $ferDetalles->save();

             $localizacionFR = new LocalizarFr();
             $localizacionFR->fk_fer_detalle      = $ferDetalles->id_fer_detalle;
             $localizacionFR->fk_localizacion     = $request->input('fk_localizacion');
             $localizacionFR->cantidad_recibida   = $item[$i]['cantidad_solicitar'] - $item[$i]['cantidad_solicitar'];
             $localizacionFR->usuario_crea        = strtoupper($request->input('usuario_crea'));
             $localizacionFR->save();


          }

          DB::commit();
          return response()->json([ 
            "ok" =>true,
            "data" =>$fr,
            "aprobado" => 'Se guardo satistactoriamente'
          ]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([ 
                "ok" => false,
                "data"   =>$ex->getMessage(),
                "error" =>'Hubo un error consulte con el administrador del sistema.'
            ]);
 
        }
    }

    public function mostrarFrPendientes ($id_fer_encabe) { 
        $frPendientes = FR::
        join('exp.despacho','exp.despacho.codigo','inv_fer_encabe.fk_despacho')
        ->select('inv_fer_encabe.id_fer_encabe','inv_fer_encabe.estado as estatus','inv_fer_encabe.entregado_por','inv_fer_encabe.recibido_por','inv_fer_encabe.aprobado_por',
        'inv_fer_encabe.solicitado_por','inv_fer_encabe.no_control','inv_fer_encabe.observacion','inv_fer_encabe.fecha_entrega','inv_fer_encabe.lugar_entrega',
        'inv_fer_encabe.usuario_modifica','exp.despacho.descripcion as despacho', 'exp.despacho.codigo as fk_solicitado','inv_fer_encabe.fecha_crea')
        ->where('inv_fer_encabe.id_fer_encabe',$id_fer_encabe)
        ->first();
        

        $detallesArticulosFR = FerDetalle::
        join('inv_fer_encabe','inv_fer_encabe.id_fer_encabe','inv_fer_detalle.fk_fer_encabe')
        ->join('inv_detalle_compras','inv_detalle_compras.id_detalle','inv_fer_detalle.fk_detalle_compra')
        ->join('inv_articulos','inv_articulos.id_articulo','inv_detalle_compras.fk_articulo')
        ->join('exp.despacho','exp.despacho.codigo','inv_fer_encabe.fk_despacho')
        ->join('inv_solicitud_compras','inv_solicitud_compras.id_solicitud','inv_detalle_compras.fk_solicitud')
        ->leftJoin('inv_marcas','inv_marcas.id_marca','inv_articulos.fk_marca')
        ->leftJoin('inv_modelos','inv_modelos.id_modelo','inv_articulos.fk_modelo')
        ->join('inv_unidad_medidas','inv_unidad_medidas.id_unidad_medida','inv_articulos.fk_unidad_medida')
        ->join('inv_fer_localizacion','inv_fer_detalle.id_fer_detalle','inv_fer_localizacion.fk_fer_detalle')
        ->join('inv_localizaciones','inv_localizaciones.id_localizacion','inv_fer_localizacion.fk_localizacion')
        ->join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->leftJoin("inv_ubicacion_articulos", function($join){
            $join->on("inv_fer_localizacion.fk_localizacion","=","inv_ubicacion_articulos.fk_localizacion")
                ->on("inv_detalle_compras.fk_articulo","=","inv_ubicacion_articulos.fk_articulo");
        })
    
        ->where('inv_fer_detalle.fk_fer_encabe',$id_fer_encabe)
        
        ->select('inv_fer_detalle.id_fer_detalle','inv_fer_detalle.fk_fer_encabe','inv_fer_detalle.fk_detalle_compra','inv_fer_detalle.cantidad_pedida as solicitada_fr',
        'inv_fer_detalle.cantidad_recibida as recibida_fr','inv_fer_detalle.estado','inv_detalle_compras.cantidad_pedida','inv_detalle_compras.cantidad_recibida',
        'inv_detalle_compras.cantidad_almacen','inv_solicitud_compras.orden_compra','inv_solicitud_compras.no_factura','inv_articulos.codigo','inv_articulos.descripcion',
        'inv_articulos.id_articulo','inv_articulos.cantidad_stock','inv_articulos.cantidad_almacen as almacen_articulos','inv_marcas.nombre_marca','inv_modelos.nombre_modelo',
        'inv_unidad_medidas.descripcion as medida','inv_fer_localizacion.id_fer_localizacion','inv_localizaciones.descripcion as deposito','inv_localizaciones.id_localizacion',
        'inv_ubicacion_articulos.id_ubicacion','inv_ubicacion_articulos.cantidad_stock as unidades_ubicacion','inv_detalle_compras.id_detalle','exp.despacho.codigo as coddespacho',
        'inv_localizaciones.descripcion as localizacion','inv_depositos.descripcion as deposito')
        ->get();
        $frPendientes->articulosFR = $detallesArticulosFR;

        return response()->json([ 
            "ok"    => true,
            "data"  => $frPendientes
        ]);
    }

    public function confirmarFrLocalizacion (Request $request) { 

        
        DB::beginTransaction();
        $item = $request->input('ubicaciones');

        for ($i=0; $i <count($item) ; $i++) { 
            $confirmar = new FerDetalle();
               $data['cantidad_pedida']     =   $item[$i]['solicitada_fr'];
               $data['cantidad_recibida']   =   $item[$i]['solicitada_fr']; - $item[$i]['recibida_fr'];
               $data['usuario_modifica']    =   $request->input('usuario_modifica');
               if ($data['cantidad_pedida'] ==  $data['cantidad_recibida']); { 
                   $data['estado'] = 'C';
               }
               $confirmar = FerDetalle::where('id_fer_detalle',$item[$i]['id_fer_detalle'])->update($data);


                if (isset($item[$i]['id_fer_localizacion'])) {
                    $localizacionFR= new LocalizarFr();
                    $ferLocalizacionFr['cantidad_recibida'] = $item[$i]['cantidad_confirmar'];
                    $ferLocalizacionFr['fk_fer_detalle'] =    $item[$i]['id_fer_detalle'];
                    $ferLocalizacionFr['fk_localizacion'] =   $item[$i]['id_localizacion'];
                    $ferLocalizacionFr['usuario_modifica'] =  $request->input('usuario_modifica');
                    $localizacionFR = LocalizarFr::where('id_fer_localizacion', $item[$i]['id_fer_localizacion'])->update($ferLocalizacionFr);
                } else {
                    $localizacion = new LocalizarFr;
                    $localizacion->fk_fer_detalle  = ($item[$i]['id_fer_detalle']);
                    $localizacion->fk_localizacion = $item[$i]['id_localizacion'];
                    $localizacion->cantidad_recibida = $item[$i]['cantidad_confirmar'];
                    $localizacion->usuario_crea = $request->input('usuario_modifica');
                    $localizacion->save();
                }

                $detalles = new DetallesCompras();
                $detalleCompra['cantidad_almacen'] =  $item[$i]['cantidad_pedida'] - $item[$i]['solicitada_fr'];
                $detalleCompra['usuario_modifica'] =  $request->input('usuario_modifica');
                $detalles = DetallesCompras::where('id_detalle', $item[$i]['id_detalle'])->update($detalleCompra);

                $articulos = new Articulos();
                $dataArticulo['cantidad_stock'] =   $item[$i]['cantidad_stock'] + $item[$i]['solicitada_fr'];
                $dataArticulo['cantidad_almacen'] = $item[$i]['almacen_articulos'] - $item[$i]['solicitada_fr'];
                $articulos = Articulos::where('id_articulo', $item[$i]['id_articulo'])->update($dataArticulo);

                if(isset($item[$i]['id_ubicacion'])) {
                    $ubicacionArticulo = new UbicacionArticulo();
                    $ubicacion['fk_localizacion'] = ($item[$i]['id_localizacion']);
                    $ubicacion['fk_articulo'] =  ($item[$i]['id_articulo']);
                    $ubicacion['usuario_modifica'] = $request->input('usuario_modifica');
                    $ubicacion['cantidad_stock'] =   $item[$i]['cantidad_stock'] + $item[$i]['cantidad_confirmar'];
                    $ubicacionArticulo = UbicacionArticulo::where('id_ubicacion', $item[$i]['id_ubicacion'])->update($ubicacion);
                } else {
                    $ubicarArticulo = new UbicacionArticulo;
                    $ubicarArticulo->fk_localizacion = ($item[$i]['id_localizacion']);
                    $ubicarArticulo->fk_articulo =  ($item[$i]['id_articulo']);
                    $ubicarArticulo->usuario_crea = $request->input('usuario_modifica');
                    $ubicarArticulo->cantidad_stock = $item[$i]['cantidad_confirmar'];
                    $ubicarArticulo->save();

                }

                $estadoFR = new FR();
                $datosFr['estado'] = 'C';
                $datosFr['usuario_modifica'] = $request->input('usuario_modifica');
                $estadoFR =  FR::where('id_fer_encabe', $item[$i]['fk_fer_encabe'])->update($datosFr);

        }

        try {
            DB::commit();
            return response()->json([
                "ok"    => true,
                "data"  => $confirmar,
                "confirmar"    => 'Se guardo satisfactoriamente  '
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"    => false,
                "data"  => $ex->getMessage()
            ]);
        }
            
        }

        public function mostrarFr () { 
            $detallesFR = DB::table('inv_fer_detalle as fd')
            ->select('fd.id_fer_detalle','fd.cantidad_recibida', 'fd.estado')
            ->get();
            return response()->json([ 
                "ok"    => true,
                "data"  =>$detallesFR
            ]);
        }

        public function mostrarDepositosFR ($coddespacho) { 
            $depositosFR = DB::table('inv_depositos dep')
            ->join('exp.despacho','exp.despacho.codigo','dep.fk_despacho')
            ->select('dep.id_deposito','dep.descripcion as deposito','exp.despacho.codigo')
            ->where('exp.despacho.codigo',$coddespacho)
            ->get();
            return response()->json([ 
                "ok"    =>true,
                "data"  =>$depositosFR
            ]);
        }

        public function mostrarSolicitudFR () { 
            $solicitudesFR = DB::table('inv_fer_encabe as enc')
            ->join('exp.despacho as desp','desp.codigo','enc.fk_despacho')
            ->select('enc.id_fer_encabe','desp.descripcion as despacho','enc.no_control','enc.fecha_entrega','enc.solicitado_por','enc.aprobado_por','enc.entregado_por',
            'enc.recibido_por','enc.observacion','enc.estado','enc.fecha_crea','enc.usuario_crea','enc.lugar_entrega')
            ->get();
            return response()->json([ 
                "ok"    => true,
                "data"  =>$solicitudesFR
            ]);
        }

        public function verDetallesFr ($id_fer_encabe) { 
            $mostrarDetallesFr = DB::table('inv_fer_encabe as enc')
            ->join('inv_fer_detalle as det','enc.id_fer_encabe','det.fk_fer_encabe')
            ->join('inv_detalle_compras as dc','dc.id_detalle','det.fk_detalle_compra')
            ->join('inv_articulos as art','art.id_articulo','dc.fk_articulo')
            ->join('inv_marcas as marc','marc.id_marca','art.fk_marca')
            ->join('inv_modelos as mdl','mdl.id_modelo','art.fk_modelo')
            ->select('det.id_fer_detalle','dc.id_detalle','art.id_articulo','dc.no_item','art.codigo','art.descripcion as articulo','marc.nombre_marca','mdl.nombre_modelo','det.cantidad_pedida',
            'det.cantidad_recibida','det.estado as estatus')
            ->where('enc.id_fer_encabe',$id_fer_encabe)
            ->get();
            return response()->json([ 
                "ok"    => true,
                "data"  =>$mostrarDetallesFr
            ]);
        }

        public function contarNumeroControl()
    {
        $numeroControl = FR::select('inv_fer_encabe.id_fer_encabe','inv_fer_encabe.no_control')
        ->orderBy('inv_fer_encabe.id_fer_encabe','desc')
        ->first();
        return response()->json([ 
            "ok"    => true,
            "data"  =>$numeroControl
        ]);
    }

    public function detallesArticulosPendientesFR($id_fer_encabe) { 
        $frDatosGenerales = FR::
        join('exp.despacho','exp.despacho.codigo','inv_fer_encabe.fk_despacho')
        ->join('inv_depositos','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->join('inv_localizaciones','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->select('inv_fer_encabe.id_fer_encabe','inv_fer_encabe.fk_despacho','inv_fer_encabe.no_control','inv_fer_encabe.fecha_entrega',
        'inv_fer_encabe.lugar_entrega','inv_fer_encabe.solicitado_por','inv_fer_encabe.aprobado_por','inv_fer_encabe.entregado_por',
        'inv_fer_encabe.recibido_por','inv_fer_encabe.observacion','inv_fer_encabe.usuario_modifica','exp.despacho.descripcion as despacho',
        'inv_depositos.id_deposito','inv_depositos.descripcion as deposito','inv_localizaciones.id_localizacion', 
        'inv_localizaciones.descripcion as localizacion')
        ->where('inv_fer_encabe.id_fer_encabe',$id_fer_encabe)
        ->where('inv_fer_encabe.estado','P')
        ->first();

        $detallesArticulosFr = FerDetalle::
        join('inv_detalle_compras','inv_detalle_compras.id_detalle','inv_fer_detalle.fk_detalle_compra')
        ->join('inv_articulos','inv_articulos.id_articulo','inv_detalle_compras.fk_articulo')
        ->join('inv_solicitud_compras','inv_solicitud_compras.id_solicitud','inv_detalle_compras.fk_solicitud')
        ->leftJoin('inv_marcas','inv_marcas.id_marca','inv_articulos.fk_marca')
        ->join('inv_fer_localizacion','inv_fer_detalle.id_fer_detalle','inv_fer_localizacion.fk_fer_detalle')
        ->leftJoin('inv_modelos','inv_modelos.id_modelo','inv_articulos.fk_modelo')
        ->where('inv_fer_detalle.estado','P')
        ->where('inv_fer_detalle.fk_fer_encabe', $id_fer_encabe)
        ->select('inv_fer_detalle.id_fer_detalle','inv_fer_detalle.fk_fer_encabe','inv_fer_detalle.fk_detalle_compra','inv_fer_detalle.cantidad_pedida',
        'inv_fer_detalle.cantidad_recibida','inv_fer_detalle.estado','inv_fer_detalle.usuario_modifica','inv_articulos.descripcion as articulo',
        'inv_articulos.id_articulo','inv_articulos.codigo','inv_marcas.nombre_marca','inv_modelos.nombre_modelo','inv_detalle_compras.cantidad_almacen',
        'inv_fer_localizacion.id_fer_localizacion','inv_solicitud_compras.orden_compra','inv_detalle_compras.id_detalle')
        ->get();

        $frDatosGenerales->articulosFR =  $detallesArticulosFr;

        return response ()->json([
            "ok"    => true,
            "data"  => $frDatosGenerales,
           
        ]);
    }

    public function detallesHistorialFR($id_fer_encabe) { 
        $frDatosGenerales = FR::
        join('exp.despacho','exp.despacho.codigo','inv_fer_encabe.fk_despacho')
        ->select('inv_fer_encabe.id_fer_encabe','inv_fer_encabe.fk_despacho','inv_fer_encabe.no_control','inv_fer_encabe.fecha_entrega',
        'inv_fer_encabe.lugar_entrega','inv_fer_encabe.solicitado_por','inv_fer_encabe.aprobado_por','inv_fer_encabe.entregado_por',
        'inv_fer_encabe.recibido_por','inv_fer_encabe.observacion','inv_fer_encabe.usuario_modifica','exp.despacho.descripcion as despacho','inv_fer_encabe.estado')
        ->where('inv_fer_encabe.id_fer_encabe',$id_fer_encabe)
        ->first();

        $detallesArticulosFr = VistaFrDetallado::
        join('inv_articulos','inv_articulos.id_articulo','VW_FRS_ORDEN_COMPRA_DETALLADO.fk_articulo')
        ->join('inv_detalle_compras','inv_detalle_compras.id_detalle','VW_FRS_ORDEN_COMPRA_DETALLADO.id_detalle')
        ->select('VW_FRS_ORDEN_COMPRA_DETALLADO.id_fer_encabe','VW_FRS_ORDEN_COMPRA_DETALLADO.fk_articulo','VW_FRS_ORDEN_COMPRA_DETALLADO.codigo',
        'VW_FRS_ORDEN_COMPRA_DETALLADO.descripcion','VW_FRS_ORDEN_COMPRA_DETALLADO.marca','VW_FRS_ORDEN_COMPRA_DETALLADO.modelo',
        'VW_FRS_ORDEN_COMPRA_DETALLADO.cantidad_pedida','VW_FRS_ORDEN_COMPRA_DETALLADO.cantidad_recibida','VW_FRS_ORDEN_COMPRA_DETALLADO.estado',
        'VW_FRS_ORDEN_COMPRA_DETALLADO.fk_tipo_solicitud','VW_FRS_ORDEN_COMPRA_DETALLADO.fk_solicitud','VW_FRS_ORDEN_COMPRA_DETALLADO.ubicacion','VW_FRS_ORDEN_COMPRA_DETALLADO.orden_compra',
        'VW_FRS_ORDEN_COMPRA_DETALLADO.fk_localizacion','VW_FRS_ORDEN_COMPRA_DETALLADO.id_fer_localizacion','VW_FRS_ORDEN_COMPRA_DETALLADO.id_fer_detalle','VW_FRS_ORDEN_COMPRA_DETALLADO.id_detalle',
        'inv_articulos.cantidad_stock as stock_articulo','inv_articulos.cantidad_almacen','inv_detalle_compras.cantidad_almacen as almacen_detalle')
        ->where('VW_FRS_ORDEN_COMPRA_DETALLADO.id_fer_encabe', $id_fer_encabe)
        ->where('VW_FRS_ORDEN_COMPRA_DETALLADO.estado','<>', 'I')
       
        ->get();

        $frDatosGenerales->articulosFR =  $detallesArticulosFr;

        return response ()->json([
            "ok"    => true,
            "data"  => $frDatosGenerales,
           
        ]);
    }

    public function guardarDatosGenerales(FrModificarDatosGeneralesRequest $request) { 
        //Modificar datos generales
        $id_fer_encabe = $request->input('id_fer_encabe');
        $data['fk_despacho']      = $request->input('fk_despacho');
        $data['fecha_entrega']    = $request->input('fecha_entrega');
        $data['lugar_entrega']    = strtoupper($request->input('lugar_entrega'));
        $data['entregado_por']    = strtoupper($request->input('entregado_por'));
        $data['recibido_por']     = ucwords($request->input('recibido_por'));
        $data['solicitado_por']   = ucwords($request->input('solicitado_por'));
        $data['aprobado_por']     = ucwords($request->input('aprobado_por'));
        $data['observacion']      = ucfirst($request->input('observacion'));
        $data['usuario_modifica'] = strtoupper($request->input('usuario_modifica'));

        $item = $request->input('articulosFR');
        for ($i=0; $i <count($item) ; $i++) { 
           if (isset($item[$i]['id_fer_localizacion'])) {
            $localizacionesFR = new LocalizarFr();
            $localizacion['fk_localizacion']  =  $request->input('id_localizacion');
            $localizacion['usuario_modifica'] =  strtoupper($request->input('usuario_modifica'));
            $localizacionesFR = LocalizarFr::where('id_fer_localizacion', $item[$i]['id_fer_localizacion'])->update($localizacion);
           }
        }

        try {
            DB::beginTransaction();
            $editarDatosGenerales = FR::where('id_fer_encabe', $id_fer_encabe)->update($data);
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" => $editarDatosGenerales,
                "aprobado" =>'Se guardo satisfactoriamente'
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data" =>$ex->getMessage(),
                "error" =>'Hubo un error con el registro, consulte con el administrador de sistema'
            ]);
        }
    }

    public function detallesArticulosCompletadosFR($id_fer_encabe) { 
        $frDatosGenerales = FR::
        join('exp.despacho','exp.despacho.codigo','inv_fer_encabe.fk_despacho')
        ->select('inv_fer_encabe.id_fer_encabe','inv_fer_encabe.fk_despacho','inv_fer_encabe.no_control','inv_fer_encabe.fecha_entrega',
        'inv_fer_encabe.lugar_entrega','inv_fer_encabe.solicitado_por','inv_fer_encabe.aprobado_por','inv_fer_encabe.entregado_por',
        'inv_fer_encabe.recibido_por','inv_fer_encabe.observacion','inv_fer_encabe.usuario_modifica','exp.despacho.descripcion as despacho')
        ->where('inv_fer_encabe.id_fer_encabe',$id_fer_encabe)
        ->where('inv_fer_encabe.estado','C')
        ->first();

        $detallesArticulosFr = FerDetalle::
        join('inv_detalle_compras','inv_detalle_compras.id_detalle','inv_fer_detalle.fk_detalle_compra')
        ->join('inv_articulos','inv_articulos.id_articulo','inv_detalle_compras.fk_articulo')
        ->leftJoin('inv_marcas','inv_marcas.id_marca','inv_articulos.fk_marca')
        ->leftJoin('inv_modelos','inv_modelos.id_modelo','inv_articulos.fk_modelo')
        ->select('inv_fer_detalle.id_fer_detalle','inv_fer_detalle.fk_fer_encabe','inv_fer_detalle.fk_detalle_compra','inv_fer_detalle.cantidad_pedida',
        'inv_fer_detalle.cantidad_recibida','inv_fer_detalle.estado','inv_fer_detalle.usuario_modifica','inv_articulos.descripcion as descripcion',
        'inv_articulos.id_articulo','inv_articulos.codigo','inv_marcas.nombre_marca','inv_modelos.nombre_modelo','inv_detalle_compras.cantidad_almacen')
        ->where('inv_fer_detalle.estado','C')
        ->where('inv_fer_detalle.fk_fer_encabe',$id_fer_encabe)
        ->get();

        $frDatosGenerales->articulosFR =  $detallesArticulosFr;

        return response ()->json([
            "ok"    => true,
            "data"  => $frDatosGenerales,
           
        ]);
    }


    public function verDetallesFRporRevertir($id_fer_encabe) { 
        $frDatosGenerales = FR::
        join('exp.despacho','exp.despacho.codigo','inv_fer_encabe.fk_despacho')
        ->select('inv_fer_encabe.id_fer_encabe','inv_fer_encabe.fk_despacho','inv_fer_encabe.no_control','inv_fer_encabe.fecha_entrega',
        'inv_fer_encabe.lugar_entrega','inv_fer_encabe.solicitado_por','inv_fer_encabe.aprobado_por','inv_fer_encabe.entregado_por',
        'inv_fer_encabe.recibido_por','inv_fer_encabe.observacion','inv_fer_encabe.usuario_modifica','exp.despacho.descripcion as despacho')
        ->where('inv_fer_encabe.id_fer_encabe',$id_fer_encabe)
        ->where('inv_fer_encabe.estado','C')
        ->first();

        $detallesArticulosFr = FerDetalle::
        join('inv_detalle_compras','inv_detalle_compras.id_detalle','inv_fer_detalle.fk_detalle_compra')
        ->join('inv_articulos','inv_articulos.id_articulo','inv_detalle_compras.fk_articulo')
        ->leftJoin('inv_marcas','inv_marcas.id_marca','inv_articulos.fk_marca')
        ->leftJoin('inv_modelos','inv_modelos.id_modelo','inv_articulos.fk_modelo')
        ->join('inv_fer_localizacion','inv_fer_detalle.id_fer_detalle','inv_fer_localizacion.fk_fer_detalle')
        ->leftJoin("inv_ubicacion_articulos", function($join){
            $join->on("inv_fer_localizacion.fk_localizacion","=","inv_ubicacion_articulos.fk_localizacion")
                ->on("inv_detalle_compras.fk_articulo","=","inv_ubicacion_articulos.fk_articulo");
        })
        ->join('inv_localizaciones','inv_localizaciones.id_localizacion','inv_ubicacion_articulos.fk_localizacion')
        ->join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->select('inv_fer_detalle.id_fer_detalle','inv_fer_detalle.fk_fer_encabe','inv_fer_detalle.fk_detalle_compra','inv_fer_detalle.cantidad_pedida',
        'inv_fer_detalle.cantidad_recibida','inv_fer_detalle.estado','inv_fer_detalle.usuario_modifica','inv_articulos.descripcion as descripcion',
        'inv_articulos.id_articulo','inv_articulos.codigo','inv_marcas.nombre_marca','inv_modelos.nombre_modelo','inv_detalle_compras.cantidad_almacen',
        'inv_fer_localizacion.id_fer_localizacion','inv_fer_localizacion.cantidad_recibida as recibida_localizacion_fr','inv_ubicacion_articulos.id_ubicacion',
        'inv_localizaciones.descripcion as localizacion','inv_depositos.descripcion as deposito')
        ->where('inv_fer_detalle.estado','C')
        ->where('inv_fer_detalle.fk_fer_encabe',$id_fer_encabe)
        ->get();

        $frDatosGenerales->articulosFR =  $detallesArticulosFr;

        return response ()->json([
            "ok"    => true,
            "data"  => $frDatosGenerales,
           
        ]);
    }

    public function mostrarDatosSolicitantesFR($id_fer_encabe){
        $fr = FR::
        select('id_fer_encabe','solicitado_por','aprobado_por','recibido_por')
        ->where('id_fer_encabe', $id_fer_encabe)
        ->first();
        return response()->json([
            'ok' =>true,
            "data"=>$fr
        ]);
    }


    public function mostrarListaFRCompletos()
    {
        $fr = FR::
       join('exp.despacho', 'exp.despacho.codigo','inv_fer_encabe.fk_despacho')
       ->select('inv_fer_encabe.id_fer_encabe','exp.despacho.descripcion as despacho','inv_fer_encabe.no_control','inv_fer_encabe.fecha_entrega','inv_fer_encabe.lugar_entrega','inv_fer_encabe.solicitado_por',
       'inv_fer_encabe.aprobado_por','inv_fer_encabe.entregado_por','inv_fer_encabe.recibido_por','inv_fer_encabe.observacion','inv_fer_encabe.estado','inv_fer_encabe.fecha_crea', 'inv_fer_encabe.usuario_crea',
       'inv_fer_encabe.fecha_modifica','inv_fer_encabe.usuario_modifica')
       ->where('inv_fer_encabe.estado','C')
       ->get();
       return response()->json([ 
        "ok"  => true,
        "data"  => $fr,
        "PendientesFR" =>count($fr)
       ]);
    }

    public function guadarDetalleArticuloFR(Request $request)
    {
        DB::beginTransaction();
        $id_fer_encabe = $request->input('id_fer_encabe');
      
        $data['fecha_entrega']    = $request->input('fecha_entrega');
        $data['lugar_entrega']    = strtoupper($request->input('lugar_entrega'));
        $data['entregado_por']    = strtoupper($request->input('entregado_por'));
        $data['recibido_por']     = ucwords($request->input('recibido_por'));
        $data['solicitado_por']   = ucwords($request->input('solicitado_por'));
        $data['aprobado_por']     = ucwords($request->input('aprobado_por'));
        $data['fk_despacho']      = $request->input('fk_despacho');
        $data['observacion']      = ucfirst($request->input('observacion'));
        $data['usuario_modifica'] = $request->input('usuario');

       
        $item = $request->input('articulosFR');
       for ($i=0; $i <count($item) ; $i++) { 
       
        if (isset($item[$i]['id_fer_detalle'])) {
            $detalles_fr = new FerDetalle;
            $articuloFR['cantidad_pedida'] = $item[$i]['cantidad_pedida'];
            $articuloFR['usuario_modifica'] = $item[$i]['usuario_modifica'];
            $detalles_fr = FerDetalle::where('id_fer_detalle', $item[$i]['id_fer_detalle'])->update($articuloFR);
        } else {

            $ferDetalles = new FerDetalle();
            $ferDetalles->fk_fer_encabe     = $item[$i]['fk_fer_encabe'];
            $ferDetalles->fk_detalle_compra = $item[$i]['id_detalle'];
            $ferDetalles->cantidad_pedida   = $item[$i]['cantidad_pedida'];
            $ferDetalles->cantidad_recibida = $item[$i]['cantidad_pedida'] - $item[$i]['cantidad_pedida'];
            $ferDetalles->estado            = 'P';
            $ferDetalles->usuario_crea      =  $item[$i]['usuario_modifica'];
            $ferDetalles->save();
        }

        if (isset($item[$i]['id_fer_localizacion'])) {
            $localizacionesFR = new LocalizarFr();
            $localizacion['fk_localizacion'] =  $request->input('id_localizacion');
            $localizacion['usuario_modifica'] = $item[$i]['usuario_modifica'];
            $localizacionesFR = LocalizarFr::where('id_fer_localizacion', $item[$i]['id_fer_localizacion'])->update($localizacion);
        } else {
            $localizacionFR = new LocalizarFr();
            $localizacionFR->fk_fer_detalle      = $ferDetalles->id_fer_detalle;
            $localizacionFR->fk_localizacion     = $request->input('id_localizacion');
            $localizacionFR->cantidad_recibida   = $item[$i]['cantidad_pedida'] - $item[$i]['cantidad_pedida'];
            $localizacionFR->usuario_crea        = $item[$i]['usuario_modifica'];
            $localizacionFR->save();
        }
       }

        try {
            $editarDetallesArticulos = FR::where('id_fer_encabe', $id_fer_encabe)->update($data);
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>  $editarDetallesArticulos,
                "exitoso" =>'Se guardo satisfactoriamente'
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data" =>$ex->getMessage(),
              
               
                "errorMensaje" =>'Hubo un error con el registro, consulte con el administrador de sistema'
            ]);
            
        }
    }

    public function  revertirFR (RevertirRequestFR $request) { 
        DB::beginTransaction();
        $item = $request->input('articulosFR');
        for ($i=0; $i <count($item) ; $i++) { 

            $mostrarUbicacion = UbicacionArticulo::select('inv_ubicacion_articulos.id_ubicacion' ,'inv_ubicacion_articulos.cantidad_stock','detalle')
            ->where('fk_articulo',     $item[$i]['fk_articulo'])
            ->where('fk_localizacion', $item[$i]['fk_localizacion'])
            ->get();

            if (count($mostrarUbicacion) > 0) {
               if ($mostrarUbicacion[0]['cantidad_stock'] >= $item[$i]['cantidad_recibida']) {
                  $revertir = new UbicacionArticulo();
                  $UbicacionArticulos['cantidad_stock'] = $mostrarUbicacion[0]['cantidad_stock'] - $item[$i]['cantidad_recibida'];
                  $revertir = UbicacionArticulo::where('id_ubicacion', $mostrarUbicacion[0]['id_ubicacion'])->update($UbicacionArticulos);

                  $articulos = new Articulos();
                  $actualizarArticulo['cantidad_stock'] = $item[$i]['stock_articulo'] - $item[$i]['cantidad_recibida'];
                  $actualizarArticulo['cantidad_almacen'] = $item[$i]['cantidad_almacen'] + $item[$i]['cantidad_recibida'];
                  $articulos = Articulos::where('id_articulo', $item[$i]['fk_articulo'])->update($actualizarArticulo);

                  $detalleCompras = new DetallesCompras();
                  $detalle['cantidad_almacen'] = $item[$i]['cantidad_recibida']; - $item[$i]['almacen_detalle'];
                  $detalleCompras = DetallesCompras::where('id_detalle', $item[$i]['id_detalle'])->update( $detalle);

                  $ferLocalizacion = new LocalizarFr();
                  $frLocalizacion['cantidad_recibida'] = $item[$i]['cantidad_pedida'] - $item[$i]['cantidad_recibida'];
                  $ferLocalizacion = LocalizarFr::where('id_fer_localizacion', $item[$i]['id_fer_localizacion'])->update($frLocalizacion);

                  $fer_detalle = new FerDetalle();
                  $frDetalle['estado'] = 'P';
                  $frDetalle['cantidad_recibida'] = $item[$i]['cantidad_pedida'] - $item[$i]['cantidad_recibida'];
                  $fer_detalle = FerDetalle::where('id_fer_detalle', $item[$i]['id_fer_detalle'])->update($frDetalle);

                  $FR = new FR();
                  $fr['estado'] = 'P';
                  $FR = FR::where('id_fer_encabe', $item[$i]['id_fer_encabe'])->update($fr);

               }
            } 
        }

        try {
            DB::commit();
            return response()->json([
                "ok"    => true,
                "data"  => $revertir,
                "aprobado"    => 'Se guardo satisfactoriamente  '
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"    => false,
                "data"  => $ex->getMessage(),
                "errorRevertir" =>'Hubo un error consulte con el administrador del sistema.'
            ]);
        }
    }

    public function verificarRevertirFR(Request $request){
        DB::beginTransaction();
        try {
            $item = $request->input('articulosFR');
            for ($i=0; $i <count($item) ; $i++) { 
                $mostrarUbicacion = UbicacionArticulo::select('inv_ubicacion_articulos.id_ubicacion' ,'inv_ubicacion_articulos.cantidad_stock','detalle')
                ->where('fk_articulo',     $item[$i]['fk_articulo'])
                ->where('fk_localizacion', $item[$i]['fk_localizacion'])
                ->get();

                if (count($mostrarUbicacion) > 0) {
                   if ($mostrarUbicacion[0]['cantidad_stock'] >= $item[$i]['cantidad_recibida']) {
                    return response()->json([
                        "ok" => true,
                        "preguntar" => '¿Estas seguro de revertir este FR?'
                    ]);
                   } else {
                    return [
                        "reprobado" => 'No se puede revertir este FR.'
                    ];
                   }
                } 
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"    => false,
                "data"  => $ex->getMessage(),
                "errorFR" =>'Hubo un error consulte con el administrador del sistema.'
            ]);
        }
    }

    public function cancelarArticuloFR(CancelarArticuloFrRequest $request){
        try {

            DB::beginTransaction();
            $id_fer_detalle = $request->input('id_fer_detalle');
            $detalle = FerDetalle::
            select('id_fer_detalle')
            ->where('cantidad_recibida', '=', 0)
            ->where('id_fer_detalle', $id_fer_detalle)
            ->first();

            if ($detalle) {
                $id = $request->input('id_fer_detalle');
                $id_fer_localizacion = $request->input('id_fer_localizacion');
                $detallefr = new FerDetalle();
                $detalles['estado'] = 'I';
                $detalles['usuario_modifica'] = strtoupper($request->input('usuario_modifica'));
                $detallefr = FerDetalle::where('id_fer_detalle', $id)->update($detalles);
    
                $ferLocalizacion = LocalizarFr::where('id_fer_localizacion', $id_fer_localizacion)->delete();
                DB::commit();

                return response()->json([
                    "ok" =>true,
                    "data"=>$detalle,
                    "mensajeAprobado" =>'Se cancelo el artículo el artículo satisfactoriamente'
                ]);
    
            } else {
               return response()->json([
                "ok" =>false,
                "errorCancelar" =>'No se puede cancelar este artículo'
               ]);
            }
           
         
        } catch (\Exception $ex) {
            DB::rollBack();
           return response()->json([
            "ok" =>false,
            "error" =>'Hubo un error consulte con el administrador del sistema.',
            "data" =>$ex->getMessage()
           ]);
        }
    }

    public function cancelarCompletoFR(CancelarFrRequest $request){
        try {

            DB::beginTransaction();
            $id_fer_encabe = $request->input('id_fer_encabe');
            $datosGenerales = new FR();
            $generales['estado'] = 'I';
            $generales['usuario_modifica'] = strtoupper($request->input('usuario_modifica'));
            $datosGenerales = FR::where('id_fer_encabe', $id_fer_encabe)->update($generales);

            $item = $request->input('articulosFR');

            for ($i=0; $i <count($item) ; $i++) { 
               if ($item[$i]['recibida_fr'] == 0) {
                    if (isset($item[$i]['id_fer_detalle'])) {
                        $detalleFR = new FerDetalle();
                        $detalle['estado'] = 'I';
                        $detalle['usuario_modifica'] = strtoupper($item[$i]['usuario_modifica']);
                        $detalleFR = FerDetalle::where('id_fer_detalle', $item[$i]['id_fer_detalle'])->update($detalle);
                    }

                    if (isset($item[$i]['id_fer_localizacion'])) {
                        $localizacionfr = LocalizarFr::where('id_fer_localizacion', $item[$i]['id_fer_localizacion'])->delete();
                    }
               } else { 
                   return response()->json([
                    "ok" =>false,
                    "errorCancelarCompleto" => 'No se puede cancelar este FR'
                   ]);
               }
            }

            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$datosGenerales,
                "mensajeAprobado" =>'Se canceló satisfactoriamente'
            ]);

           
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "error" => 'Hubo un error consulte con el administrador del sistema.'
            ]);
        }
    }

    public function verificarExisteCantidadRecibidaFR($id_fer_encabe){
        $fr = FerDetalle::
        select('inv_fer_detalle.id_fer_detalle','inv_fer_detalle.fk_fer_encabe')
        ->where('inv_fer_detalle.cantidad_recibida','>',0)
        ->where('inv_fer_detalle.fk_fer_encabe', $id_fer_encabe)
        ->count();
        return response()->json([
            "ok" =>true,
            "data" =>$fr,
        ]);

    }



    public function confirmarTodoFR (ConfirmarFrRequest $request) { 
        
        try {

            DB::beginTransaction();
            $item = $request->input('articulosFR');
            $id_fer_encabe = $request->input('id_fer_encabe');

            for ($i=0; $i <count($item) ; $i++) { 
                
               $confirmarTodos = new FerDetalle();
               $data['estado'] = 'C';
               $data['cantidad_pedida'] = $item[$i]['solicitada_fr'];
               $data['cantidad_recibida'] =  $item[$i]['solicitada_fr'] - ($item[$i]['recibida_fr']);
               $data['usuario_modifica'] = strtoupper($request->input('usuario_modifica'));
               if ($data['cantidad_pedida'] == $data['cantidad_recibida']); { 
                   $data['estado'] = 'C';
               }
                 
               $confirmarTodos = FerDetalle::where('id_fer_detalle',$item[$i]['id_fer_detalle'])->update($data);

               $articulos = new Articulos();
               $articulo['cantidad_stock'] =  ($item[$i]['cantidad_stock']) + ($item[$i]['solicitada_fr']);
               $articulo['cantidad_almacen'] = ($item[$i]['almacen_articulos']) - ($item[$i]['solicitada_fr']);
               $articulo['usuario_modifica'] = strtoupper($request->input('usuario_modifica'));
               $articulos = Articulos::where('id_articulo', $item[$i]['id_articulo'])->update($articulo);

               $detallesCompras = new DetallesCompras();
               $detalleCompra['cantidad_almacen'] =   ($item[$i]['cantidad_almacen']) - ($item[$i]['solicitada_fr']);
               $detalleCompra['usuario_modifica'] = strtoupper($request->input('usuario_modifica'));
               $detallesCompras = DetallesCompras::where('id_detalle',  $item[$i]['fk_detalle_compra'])->update($detalleCompra);
            
              
               $ubicacion =  $item[$i]['ubicaciones'];

                for ($u=0; $u <count($ubicacion) ; $u++) { 

                    if (isset($ubicacion[$u]['id_fer_localizacion'])) {
                        $ferLocalizacion = new LocalizarFr();
                        $dataFerLocalizacion['cantidad_recibida'] = $ubicacion[$u]['cantidad_confirmar'];
                        $dataFerLocalizacion['fk_localizacion']   = $ubicacion[$u]['id_localizacion'];
                        $dataFerLocalizacion['usuario_modifica']  = strtoupper($request->input('usuario_modifica'));
                        $ferLocalizacion  = LocalizarFr::where('id_fer_localizacion',$ubicacion[$u]['id_fer_localizacion'])->update($dataFerLocalizacion);    
                    } else {
                        $frLocalizacion = new LocalizarFr;
                        $frLocalizacion->fk_localizacion = $ubicacion[$u]['id_localizacion'];
                        $frLocalizacion->fk_fer_detalle = $ubicacion[$u]['id_fer_detalle'];
                        $frLocalizacion->cantidad_recibida = $ubicacion[$u]['cantidad_confirmar'];
                        $frLocalizacion->usuario_crea = strtoupper($request->input('usuario_modifica'));
                        $frLocalizacion->save();
                    }

                    $mostrarUbicacion = UbicacionArticulo::select('inv_ubicacion_articulos.id_ubicacion' ,'inv_ubicacion_articulos.cantidad_stock as stock_ubicacion')
                    ->where('fk_articulo', $ubicacion[$u]['id_articulo'])
                    ->where('fk_localizacion', $ubicacion[$u]['id_localizacion'])
                    ->get();
                   

                    if (count($mostrarUbicacion) > 0) {
                        $ubicar = new UbicacionArticulo;
                        $ubicacionArticulo['fk_localizacion'] = $ubicacion[$u]['id_localizacion'];
                        $ubicacionArticulo['fk_articulo'] =     $ubicacion[$u]['id_articulo'];
                        $ubicacionArticulo['usuario_modifica'] = strtoupper($request->input('usuario_modifica'));
                        $ubicacionArticulo['cantidad_stock'] =   $mostrarUbicacion[0]['stock_ubicacion'] + $ubicacion[$u]['cantidad_confirmar'];
                        $ubicar = UbicacionArticulo::where('id_ubicacion', $mostrarUbicacion[0]['id_ubicacion'])->update($ubicacionArticulo);
                    } else { 
                        $ubicarArticulo = new UbicacionArticulo;
                        $ubicarArticulo->fk_localizacion = $ubicacion[$u]['id_localizacion'];
                        $ubicarArticulo->fk_articulo =     $ubicacion[$u]['id_articulo'];
                        $ubicarArticulo->usuario_crea =    strtoupper($request->input('usuario_modifica'));
                        $ubicarArticulo->cantidad_stock =  $ubicacion[$u]['cantidad_confirmar'];
                        $ubicarArticulo->save();
                   }
                  
                }


               

            }

            $confirmarFR = new FR();
            $datosFr['estado'] = 'C';
            $datosFr['usuario_modifica'] = strtoupper($request->input('usuario_modifica'));
            $datosFr['recibido_por'] = ucwords($request->input('recibido_por'));
            $datosFr['fecha_entrega'] =  $request->input('fecha_entrega');
            $datosFr['observacion'] = ucfirst($request->input('observacion'));
            $confirmarFR =  FR::where('id_fer_encabe', $id_fer_encabe)->update($datosFr);
           
           DB::commit();
           return response()->json([
               "ok"    => true,
               "data"  => $confirmarFR,
               "confirmado" => 'Se guardo satistactoriamente'
           ],200);

           
        } catch (\Exception $th) {
             DB::rollBack();
           return response()->json([
               "ok"    => false,
               "data"  => $th->getMessage(),
               //"mensajeError"=>'Hubo un error en el registro, consulte con el administrador del sistema.'
           ]);
        } 
    }

}
