<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MovimientoTraspaso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Utils\Utilidades;
use App\Http\Requests\StoreMovimientoTraspasoRequest;
use App\Http\Requests\EditarMovimientoDetalleRequest;
use App\Http\Requests\StoreRequestMovimientoNotaSalida;
use App\Http\Requests\EliminarArticuloMovimientoRequest;
use App\Http\Requests\EditarRequestTraspasoNota;
use App\Http\Requests\EditarTraspasoNotaSalida;
use App\Http\Requests\ConfirmarRequestNota;
use App\Http\Requests\EditarMovimientoRequest;
use App\Http\Requests\CancelarRequestTraspasoSalida;
use App\Http\Requests\ConfirmarRequestSalida;
use App\Models\DetalleMovimientos;
use App\Models\DetallesArticulos;
use App\Models\UbicacionArticulo;
use PhpParser\Node\Stmt\TryCatch;

class MovimientoTraspasoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Mostrar movimiento de traspaso
        $movimientoTraspaso = MovimientoTraspaso::
        join('exp.despacho as dep_origen','dep_origen.codigo','inv_movimiento_enc.fk_despacho_origen')
        ->join('exp.despacho as dep_destino','dep_destino.codigo','inv_movimiento_enc.fk_despacho_destino')
        ->select('inv_movimiento_enc.id_movimiento_enc','inv_movimiento_enc.fk_despacho_origen','inv_movimiento_enc.fk_despacho_destino','inv_movimiento_enc.no_movimiento',
        'inv_movimiento_enc.estado','dep_origen.descripcion as despacho_origen','dep_destino.descripcion as despacho_destino','inv_movimiento_enc.tipo_movimiento')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$movimientoTraspaso
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMovimientoTraspasoRequest $request)
    {
        //Registrar movimiento de traspaso
        try {
           DB::beginTransaction();

           $tipo_movimiento     = strtoupper($request->input('tipo_movimiento'));
           $fk_despacho_origen  = strtoupper($request->input('fk_despacho_origen'));
           $fk_despacho_destino = strtoupper($request->input('fk_despacho_destino'));
           $solicitado_por      = ucwords($request->input('solicitado_por'));
           $entregado_por       = ucwords($request->input('entregado_por'));
           $aprobado_por        = ucwords($request->input('aprobado_por'));
           $recibido_por        = ucwords($request->input('recibido_por'));
           $usuario_crea        = strtoupper($request->input('usuario_crea'));
           $uibp_origen         = ucwords($request->input('uibp_origen'));
           $uibp_destino        = trim(ucwords($request->input('uibp_destino')));
           $fecha_entrega       = Utilidades::formatoFecha($request->input('fecha_entrega'));
           $fecha_doc           = now()->format('Y-m-d');
           $observacion         = ucfirst($request->input('observacion'));

           if ($tipo_movimiento == 'TRS' || $tipo_movimiento == 'TRE') {
                $movimientoTraspaso = new MovimientoTraspaso();
                $movimientoTraspaso->tipo_movimiento     = $tipo_movimiento;
                $movimientoTraspaso->fk_despacho_origen  = $fk_despacho_origen;
                $movimientoTraspaso->fk_despacho_destino = $fk_despacho_destino;
                $movimientoTraspaso->solicitado_por      = $solicitado_por;
                $movimientoTraspaso->entregado_por       = $entregado_por;
                $movimientoTraspaso->aprobado_por        = $aprobado_por;
                $movimientoTraspaso->recibido_por        = $recibido_por;
                $movimientoTraspaso->usuario_crea        = $usuario_crea;
                $movimientoTraspaso->uibp_origen         = $uibp_origen;
                $movimientoTraspaso->uibp_destino        = $uibp_destino;
                $movimientoTraspaso->fecha_entrega       = $fecha_entrega;
                $movimientoTraspaso->fecha_doc           = $fecha_doc;
                $movimientoTraspaso->observacion         = $observacion;
                $movimientoTraspaso->estado              = 'P';
                $movimientoTraspaso->save();

            $item = $request->input('articulosMovimiento');
            
            for ($i=0; $i <count($item) ; $i++) { 
               $detalleMovimiento = new DetalleMovimientos();
               $detalleMovimiento->fk_movimiento_enc       = $movimientoTraspaso->id_movimiento_enc;
                $detalleMovimiento->fk_ubicacion_origen     = $item[$i]['id_ubicacion'];
                $detalleMovimiento->fk_detalle_origen       = $item[$i]['id_detalle_articulo'];
                $detalleMovimiento->fk_localizacion_origen  = $item[$i]['fk_localizacion'];
                $detalleMovimiento->fk_localizacion_destino = $item[$i]['fk_localizacion_destino'];
                $detalleMovimiento->cantidad                = $item[$i]['cantidad'];
                $detalleMovimiento->estatus                 = $movimientoTraspaso->estado;
                $detalleMovimiento->usuario_crea            = $movimientoTraspaso->usuario_crea;
                $detalleMovimiento->observaciones           = ucfirst($item[$i]['observaciones']);
                $detalleMovimiento->save();

                $detalleArticulo = new DetallesArticulos();
                if (isset($item[$i]['id_detalle_articulo'])) {
                   $detalleOrigen['disponible'] = 'NO';
                   $detalleArticulo = DetallesArticulos::where('id_detalle_articulo',$item[$i]['id_detalle_articulo'])->update($detalleOrigen);
                }
                
            }


           } else {
               return response()->json([
                "ok" =>false,
                "mensaje" => 'No se puede realizar este tipo de movimiento'
               ]);
           }

           DB::commit(); 
           return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "mensajeAprobado" => 'Se guardo satisfactoriamente'
           ]);        

        } catch (\Exception $e) {
            DB::rollBack();
           return response()->json([
            "ok" => false,
            "data" =>$e->getMessage(),
            "mensajeError" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
           ]);
        }
    }

    public function RegistrarnotaSalida(StoreRequestMovimientoNotaSalida $request)
    {
        //Registrar movimiento de traspaso
        try {
           DB::beginTransaction();

           $tipo_movimiento     = strtoupper($request->input('tipo_movimiento'));
           $fk_despacho_origen  = strtoupper($request->input('fk_despacho_origen'));
           $fk_despacho_destino = strtoupper($request->input('fk_despacho_destino'));
           $solicitado_por      = ucwords($request->input('solicitado_por'));
           $no_nota             = strtoupper($request->input('no_nota'));
           $entregado_por       = ucwords($request->input('entregado_por'));
           $recibido_por        = ucwords($request->input('recibido_por'));
           $aprobado_por        = ucwords($request->input('aprobado_por'));
           $usuario_crea        = strtoupper($request->input('usuario'));
           $uibp_origen         = ucwords($request->input('uibp_origen'));
           $uibp_destino        = trim(ucwords($request->input('uibp_destino')));
           $fecha_entrega       = Utilidades::formatoFecha($request->input('fecha_entrega'));
           $fecha_nota          = Utilidades::formatoFecha($request->input('fecha_nota'));
           $fecha_doc           = now()->format('Y-m-d');
           $observacion         = ucfirst($request->input('observacion'));

           if ($tipo_movimiento == 'NTS' || $tipo_movimiento == 'NTE') {
                $movimientoTraspaso = new MovimientoTraspaso();
                $movimientoTraspaso->tipo_movimiento     = $tipo_movimiento;
                $movimientoTraspaso->fk_despacho_origen  = $fk_despacho_origen;
                $movimientoTraspaso->fk_despacho_destino = $fk_despacho_destino;
                $movimientoTraspaso->solicitado_por      = $solicitado_por;
                $movimientoTraspaso->entregado_por       = $entregado_por;
                $movimientoTraspaso->no_nota             = $no_nota;
                $movimientoTraspaso->fecha_nota          = $fecha_nota;
                $movimientoTraspaso->recibido_por        = $recibido_por;
                $movimientoTraspaso->aprobado_por        = $aprobado_por;
                $movimientoTraspaso->usuario_crea        = $usuario_crea;
                $movimientoTraspaso->uibp_origen         = $uibp_origen;
                $movimientoTraspaso->uibp_destino        = $uibp_destino;
                $movimientoTraspaso->fecha_entrega       = $fecha_entrega;
                $movimientoTraspaso->fecha_doc           = $fecha_doc;
                $movimientoTraspaso->observacion         = $observacion;
                $movimientoTraspaso->estado              = 'P';
                $movimientoTraspaso->save();

            $item = $request->input('articulosMovimiento');
            
            for ($i=0; $i <count($item) ; $i++) { 
               $detalleMovimiento = new DetalleMovimientos();
               $detalleMovimiento->fk_movimiento_enc       = $movimientoTraspaso->id_movimiento_enc;
                $detalleMovimiento->fk_ubicacion_origen     = $item[$i]['id_ubicacion'];
                $detalleMovimiento->fk_detalle_origen       = $item[$i]['id_detalle_articulo'];
                $detalleMovimiento->fk_localizacion_origen  = $item[$i]['fk_localizacion'];
                $detalleMovimiento->fk_localizacion_destino = $item[$i]['fk_localizacion_destino'];
                $detalleMovimiento->cantidad                = $item[$i]['cantidad'];
                $detalleMovimiento->estatus                 = $movimientoTraspaso->estado;
                $detalleMovimiento->usuario_crea            = $movimientoTraspaso->usuario_crea;
                $detalleMovimiento->observaciones           = ucfirst($item[$i]['observaciones']);
                $detalleMovimiento->save();

                $detalleArticulo = new DetallesArticulos();
                if (isset($item[$i]['id_detalle_articulo'])) {
                   $detalleOrigen['disponible'] = 'NO';
                   $detalleArticulo = DetallesArticulos::where('id_detalle_articulo',$item[$i]['id_detalle_articulo'])->update($detalleOrigen);
                }
                
            }


           } else {
               return response()->json([
                "ok" =>false,
                "mensaje" => 'No se puede realizar este tipo de movimiento'
               ]);
           }

           DB::commit(); 
           return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "mensajeAprobado" => 'Se guardo satisfactoriamente'
           ]);        

        } catch (\Exception $e) {
            DB::rollBack();
           return response()->json([
            "ok" => false,
            "data" =>$e->getMessage(),
            "mensajeError" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
           ]);
        }
    }

    public function editarMovimientoGenerales(EditarMovimientoRequest $request)
    {
        //Editar movimiento
     
       try {
        DB::beginTransaction();
           $movimientoTraspaso = new MovimientoTraspaso();
           $id_movimiento_enc = $request->input('id_movimiento_enc');
           $data['fk_despacho_origen'] = strtoupper($request->input('fk_despacho_origen'));
           $data['fk_despacho_destino'] = strtoupper($request->input('fk_despacho_destino'));
           $data['fecha_entrega'] = $request->input('fecha_entrega');
           $data['solicitado_por'] = ucwords($request->input('solicitado_por'));
           $data['recibido_por']  = ucwords($request->input('recibido_por'));
           $data['entregado_por'] = ucwords($request->input('entregado_por'));
           $data['aprobado_por'] = ucwords($request->input('aprobado_por'));
           $data['observacion'] = ucfirst($request->input('observacion'));
           $data['uibp_origen'] = ucwords($request->input('uibp_origen'));
           $data['uibp_destino'] = ucwords($request->input('uibp_destino'));
           $data['usuario_modifica'] = strtoupper($request->input('usuario'));
           $movimientoTraspaso = MovimientoTraspaso::where('id_movimiento_enc', $id_movimiento_enc)->update($data);
           

        DB::commit(); 
        return response()->json([
            "ok" =>true,
            "data" =>$movimientoTraspaso,
            "aprobado" => 'Se guardo satisfactoriamente'
           ]);

       } catch (\Exception $th) {
        DB::rollBack();
        return response()->json([
         "ok" => false,
         "data" =>$th->getMessage(),
         "mensajeError" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
        ]);
       }
    }

    public function editarMovimientoDetalle(EditarMovimientoDetalleRequest $request) {
        try {
            DB::beginTransaction();
            $movimientoTraspaso = new MovimientoTraspaso();
            $id_movimiento_enc = $request->input('id_movimiento_enc');
            $data['fk_despacho_origen'] = strtoupper($request->input('fk_despacho_origen'));
            $data['fk_despacho_destino'] = strtoupper($request->input('fk_despacho_destino'));
            $data['fecha_entrega'] = $request->input('fecha_entrega');
            $data['solicitado_por'] = ucwords($request->input('solicitado_por'));
            $data['recibido_por'] = ucwords($request->input('recibido_por'));
            $data['aprobado_por'] = ucwords($request->input('aprobado_por'));
            $data['entregado_por'] = ucwords($request->input('entregado_por'));
            $data['observacion'] = ucfirst($request->input('observacion'));
            $data['uibp_origen'] = ucwords($request->input('uibp_origen'));
            $data['uibp_destino'] = ucwords($request->input('uibp_destino'));
            $data['usuario_modifica'] = strtoupper($request->input('usuario'));
            $movimientoTraspaso = MovimientoTraspaso::where('id_movimiento_enc', $id_movimiento_enc)->update($data);

            $detalles = $request->input('articulosMovimiento');
            for ($i=0; $i <count($detalles) ; $i++) { 
               
                if (isset($detalles[$i]['id_movimiento_det'])) {
                    $detalleMovimientoSalida = new DetalleMovimientos();
                    $detalleMovimiento['fk_localizacion_destino'] = $detalles[$i]['fk_localizacion_destino'];
                    $detalleMovimiento['fk_localizacion_origen'] = $detalles[$i]['fk_localizacion_origen'];
                    $detalleMovimiento['cantidad'] = $detalles[$i]['cantidad'];
                    $detalleMovimiento['fk_ubicacion_origen'] = $detalles[$i]['fk_ubicacion_origen'];
                    $detalleMovimiento['observaciones'] =  ucfirst( $detalles[$i]['observaciones']);
                    $detalleMovimiento['usuario_modifica'] =  $data['usuario_modifica'];
                    $detalleMovimiento['fk_detalle_origen'] = $detalles[$i]['fk_detalle_origen'];
                    $detalleMovimientoSalida = DetalleMovimientos::where('id_movimiento_det', $detalles[$i]['id_movimiento_det'])->update($detalleMovimiento);
                } else {
                    $registrarDetalle = new DetalleMovimientos();
                    $registrarDetalle->fk_localizacion_destino = $detalles[$i]['fk_localizacion_destino'];
                    $registrarDetalle->fk_movimiento_enc = $id_movimiento_enc;
                    $registrarDetalle->fk_localizacion_origen  = $detalles[$i]['fk_localizacion_origen'];
                    $registrarDetalle->cantidad = $detalles[$i]['cantidad'];
                    $registrarDetalle->fk_ubicacion_origen = $detalles[$i]['fk_ubicacion_origen'];
                    $registrarDetalle->observaciones = ucfirst( $detalles[$i]['observaciones']);
                    $registrarDetalle->usuario_crea = $data['usuario_modifica'];
                    $registrarDetalle->estatus = 'P';
                    $registrarDetalle->fk_detalle_origen = $detalles[$i]['fk_detalle_origen'];
                    $registrarDetalle->save();

                    $detalleArticulo = new DetallesArticulos();
                    if (isset($detalles[$i]['fk_detalle_origen'])) {
                       $detalleOrigen['disponible'] = 'NO';
                       $detalleArticulo = DetallesArticulos::where('id_detalle_articulo',$detalles[$i]['fk_detalle_origen'])->update($detalleOrigen);
                    }
                }
              
            }

            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "aprobadoDetalle" => 'Se guardo satisfactoriamente'
               ]);

        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
             "ok" => false,
             "data" =>$th->getMessage(),
             "mensajeErrorDetalle" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }
    }

 

    public function editarTraspasoNota(EditarRequestTraspasoNota $request){
        try {
            //Editar traspaso de nota
            DB::beginTransaction();
            $movimientoTraspaso          = new MovimientoTraspaso();
            $id_movimiento_enc           = $request->input('id_movimiento_enc');
            $data['fecha_entrega']       = $request->input('fecha_entrega');
            $data['fecha_nota']          = $request->input('fecha_nota');
            $data['solicitado_por']      = ucwords($request->input('solicitado_por'));
            $data['recibido_por']        = ucwords($request->input('recibido_por'));
            $data['aprobado_por']        = ucwords($request->input('aprobado_por'));
            $data['entregado_por']       = ucwords($request->input('entregado_por'));
            $data['observacion']         = ucfirst($request->input('observacion'));
            $data['uibp_origen']         = ucwords($request->input('uibp_origen'));
            $data['uibp_destino']        = ucwords($request->input('uibp_destino'));
            $data['no_nota']             = ucwords($request->input('no_nota'));
            $data['usuario_modifica']    = strtoupper($request->input('usuario'));
            $movimientoTraspaso          = MovimientoTraspaso::where('id_movimiento_enc', $id_movimiento_enc)->update($data);
    
                $detalles = $request->input('articulosMovimiento');
                for ($i=0; $i <count($detalles) ; $i++) { 
                   if (isset($detalles[$i]['id_movimiento_det'])) {
                    $traspasoNota = new DetalleMovimientos();
                    $detalleMovimiento['fk_localizacion_destino'] =  $detalles[$i]['fk_localizacion_destino'];
                    $detalleMovimiento['fk_localizacion_origen']  =  $detalles[$i]['fk_localizacion_origen'];
                    $detalleMovimiento['cantidad']                =  $detalles[$i]['cantidad'];
                    $detalleMovimiento['observaciones']           =  ucfirst($detalles[$i]['observaciones']);
                    $detalleMovimiento['usuario_modifica']        =  $data['usuario_modifica'];
                    $detalleMovimiento['fk_detalle_origen']       =  $detalles[$i]['fk_detalle_origen'];
                    $traspasoNota = DetalleMovimientos::where('id_movimiento_det', $detalles[$i]['id_movimiento_det'])->update($detalleMovimiento);
                   } else {
                    $registrarDetalle = new DetalleMovimientos();
                    $registrarDetalle->fk_localizacion_destino = $detalles[$i]['fk_localizacion_destino'];
                    $registrarDetalle->fk_movimiento_enc       = $id_movimiento_enc;
                    $registrarDetalle->fk_localizacion_origen  = $detalles[$i]['fk_localizacion_origen'];
                    $registrarDetalle->cantidad                = $detalles[$i]['cantidad'];
                    $registrarDetalle->fk_ubicacion_origen     = $detalles[$i]['fk_ubicacion_origen'];
                    $registrarDetalle->observaciones           = ucfirst( $detalles[$i]['observaciones']);
                    $registrarDetalle->usuario_crea            = $data['usuario_modifica'];
                    $registrarDetalle->estatus                 = 'P';
                    $registrarDetalle->fk_detalle_origen       = $detalles[$i]['fk_detalle_origen'];
                    $registrarDetalle->save();
    
                    $detalleArticulo = new DetallesArticulos();
                    if (isset($detalles[$i]['fk_detalle_origen'])) {
                       $detalleOrigen['disponible'] = 'NO';
                       $detalleArticulo = DetallesArticulos::where('id_detalle_articulo',$detalles[$i]['fk_detalle_origen'])->update($detalleOrigen);
                    }
                   }
            }
           
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "modificado" => 'Se guardo satisfactoriamente'
               ]);

            DB::beginTransaction();
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
             "ok" => false,
             "data" =>$th->getMessage(),
             "mensajeErrorModificado" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }
    }

    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MovimientoTraspaso  $movimientoTraspaso
     * @return \Illuminate\Http\Response
     */
    public function mostrarTraspasoPendiente()
    {
        //Mostrar traspaso pendientes
        $movimientoTraspaso = MovimientoTraspaso::
        join('exp.despacho as dep_origen','dep_origen.codigo','inv_movimiento_enc.fk_despacho_origen')
        ->join('exp.despacho as dep_destino','dep_destino.codigo','inv_movimiento_enc.fk_despacho_destino')
        ->select('inv_movimiento_enc.id_movimiento_enc','inv_movimiento_enc.fk_despacho_origen','inv_movimiento_enc.fk_despacho_destino','inv_movimiento_enc.no_movimiento',
        'inv_movimiento_enc.estado','dep_origen.descripcion as despacho_origen','dep_destino.descripcion as despacho_destino','inv_movimiento_enc.tipo_movimiento')
        ->where('inv_movimiento_enc.estado','P')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$movimientoTraspaso,
            "TraspasosPendientes" =>count($movimientoTraspaso)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MovimientoTraspaso  $movimientoTraspaso
     * @return \Illuminate\Http\Response
     */
    public function mostrarTraspasoPendientePorDetalleMovimineto($id_movimiento_enc)
    {
        //Mostrar movimineto y su detalle
        $movimientoTraspaso = MovimientoTraspaso::
        join('exp.despacho','exp.despacho.codigo','inv_movimiento_enc.fk_despacho_origen')
        ->join('exp.despacho as destino','destino.codigo','inv_movimiento_enc.fk_despacho_destino')
        ->select('inv_movimiento_enc.id_movimiento_enc','inv_movimiento_enc.fk_despacho_origen','exp.despacho.descripcion as despacho','inv_movimiento_enc.fk_despacho_destino',
        'inv_movimiento_enc.solicitado_por','inv_movimiento_enc.entregado_por','inv_movimiento_enc.aprobado_por','inv_movimiento_enc.observacion','inv_movimiento_enc.uibp_origen','inv_movimiento_enc.uibp_destino',
        'inv_movimiento_enc.fecha_entrega','inv_movimiento_enc.no_movimiento','exp.despacho.direccion','destino.descripcion as despacho_destino','destino.direccion as direccion_destino',
        'inv_movimiento_enc.fecha_doc','exp.despacho.jefe','destino.jefe as jefe_destino','inv_movimiento_enc.recibido_por')
        ->where('inv_movimiento_enc.id_movimiento_enc', $id_movimiento_enc)
        ->where('inv_movimiento_enc.estado', 'P')
        ->first();

       $detalleMovimiento = DetalleMovimientos::
       join('VW_ARTICULO_UBICACION', 'VW_ARTICULO_UBICACION.ID_UBICACION','inv_movimiento_det.fk_ubicacion_origen')
       ->join('inv_articulos','inv_articulos.id_articulo','VW_ARTICULO_UBICACION.id_articulo')
       ->join('inv_localizaciones','inv_localizaciones.id_localizacion','inv_movimiento_det.fk_localizacion_destino')
       ->leftJoin('inv_detalle_articulo','inv_detalle_articulo.id_detalle_articulo','inv_movimiento_det.fk_detalle_origen')
       ->leftJoin('inv_detalle_articulo','inv_detalle_articulo.id_detalle_articulo','inv_movimiento_det.fk_detalle_origen')
       ->select('inv_movimiento_det.id_movimiento_det','inv_movimiento_det.fk_movimiento_enc','inv_movimiento_det.fk_ubicacion_origen','inv_movimiento_det.fk_detalle_origen',
       'inv_movimiento_det.cantidad','inv_movimiento_det.fk_localizacion_origen','inv_movimiento_det.fk_localizacion_destino','inv_movimiento_det.observaciones',
       'VW_ARTICULO_UBICACION.id_articulo','VW_ARTICULO_UBICACION.codigo','VW_ARTICULO_UBICACION.descripcion','VW_ARTICULO_UBICACION.marca','VW_ARTICULO_UBICACION.modelo',
       'VW_ARTICULO_UBICACION.localizacion','VW_ARTICULO_UBICACION.deposito','VW_ARTICULO_UBICACION.cantidad_stock','VW_ARTICULO_UBICACION.cantidad_en_movimiento',
       'inv_localizaciones.descripcion as localizacion_detino','inv_detalle_articulo.numero_serie','inv_detalle_articulo.numero_activo')
       ->where('inv_movimiento_det.fk_movimiento_enc', $id_movimiento_enc)
       ->where('inv_movimiento_det.estatus', 'P')
       ->orderBy('inv_movimiento_det.id_movimiento_det', 'asc')
       

       ->get();

       $movimientoTraspaso->articulosMovimiento = $detalleMovimiento;
       return response()->json([
        "ok" => true,
        "data" => $movimientoTraspaso
       ]);

    }

    public function mostrarTraspasoPendientePorDetalleMoviminetoEntrada($id_movimiento_enc)
    {
        //Mostrar movimineto y su detalle
        $movimientoTraspaso = MovimientoTraspaso::
        join('exp.despacho','exp.despacho.codigo','inv_movimiento_enc.fk_despacho_destino')
        ->join('exp.despacho as destino','destino.codigo','inv_movimiento_enc.fk_despacho_origen')
        ->select('inv_movimiento_enc.id_movimiento_enc','inv_movimiento_enc.fk_despacho_origen','exp.despacho.descripcion as despacho','inv_movimiento_enc.fk_despacho_destino',
        'inv_movimiento_enc.solicitado_por','inv_movimiento_enc.entregado_por','inv_movimiento_enc.observacion','inv_movimiento_enc.uibp_origen','inv_movimiento_enc.uibp_destino',
        'inv_movimiento_enc.fecha_entrega','inv_movimiento_enc.no_movimiento','exp.despacho.direccion','destino.descripcion as despacho_destino','destino.direccion as direccion_destino',
        'inv_movimiento_enc.fecha_doc','exp.despacho.jefe','destino.jefe as jefe_destino','inv_movimiento_enc.recibido_por','destino.descripcion as despacho_origen','inv_movimiento_enc.aprobado_por')
        ->where('inv_movimiento_enc.id_movimiento_enc', $id_movimiento_enc)
        ->where('inv_movimiento_enc.estado', 'P')
        ->first();

       $detalleMovimiento = DetalleMovimientos::
       join('VW_ARTICULO_UBICACION', 'VW_ARTICULO_UBICACION.ID_UBICACION','inv_movimiento_det.fk_ubicacion_origen')
       ->join('inv_articulos','inv_articulos.id_articulo','VW_ARTICULO_UBICACION.id_articulo')
       ->join('inv_localizaciones','inv_localizaciones.id_localizacion','inv_movimiento_det.fk_localizacion_destino')
       ->leftJoin('inv_detalle_articulo','inv_detalle_articulo.id_detalle_articulo','inv_movimiento_det.fk_detalle_origen')
       ->select('inv_movimiento_det.id_movimiento_det','inv_movimiento_det.fk_movimiento_enc','inv_movimiento_det.fk_ubicacion_origen','inv_movimiento_det.fk_detalle_origen',
       'inv_movimiento_det.cantidad','inv_movimiento_det.fk_localizacion_origen','inv_movimiento_det.fk_localizacion_destino','inv_movimiento_det.observaciones',
       'VW_ARTICULO_UBICACION.id_articulo','VW_ARTICULO_UBICACION.codigo','VW_ARTICULO_UBICACION.descripcion','VW_ARTICULO_UBICACION.marca','VW_ARTICULO_UBICACION.modelo',
       'VW_ARTICULO_UBICACION.localizacion','VW_ARTICULO_UBICACION.deposito','VW_ARTICULO_UBICACION.cantidad_stock','VW_ARTICULO_UBICACION.cantidad_en_movimiento',
       'inv_localizaciones.descripcion as localizacion_detino','inv_detalle_articulo.numero_serie','inv_detalle_articulo.numero_activo')
       ->where('inv_movimiento_det.fk_movimiento_enc', $id_movimiento_enc)
       ->where('inv_movimiento_det.estatus', 'P')
       ->orderBy('inv_movimiento_det.id_movimiento_det', 'asc')
       

       ->get();

       $movimientoTraspaso->articulosMovimiento = $detalleMovimiento;
       return response()->json([
        "ok" => true,
        "data" => $movimientoTraspaso
       ]);

    }

    public function mostrarTraspasoPendientePorNota($id_movimiento_enc)
    {
         //Mostrar movimineto y su detalle
         $movimientoTraspaso = MovimientoTraspaso::
         //join('exp.despacho','exp.despacho.codigo','inv_movimiento_enc.fk_despacho_origen')
         //->join('exp.despacho as destino','destino.codigo','inv_movimiento_enc.fk_despacho_destino')
         select('inv_movimiento_enc.id_movimiento_enc','inv_movimiento_enc.tipo_movimiento','inv_movimiento_enc.no_movimiento','inv_movimiento_enc.no_nota',
         'inv_movimiento_enc.fecha_nota','inv_movimiento_enc.fecha_entrega','inv_movimiento_enc.solicitado_por','inv_movimiento_enc.recibido_por',  
         'inv_movimiento_enc.aprobado_por','inv_movimiento_enc.entregado_por','inv_movimiento_enc.observacion','inv_movimiento_enc.uibp_origen',
         'inv_movimiento_enc.uibp_destino','inv_movimiento_enc.fk_despacho_origen','inv_movimiento_enc.fk_despacho_destino'
         /* 'inv_movimiento_enc.fk_despacho_origen','exp.despacho.descripcion as despacho','inv_movimiento_enc.fk_despacho_destino',
         'inv_movimiento_enc.solicitado_por','inv_movimiento_enc.entregado_por','inv_movimiento_enc.aprobado_por','inv_movimiento_enc.observacion','inv_movimiento_enc.uibp_origen','inv_movimiento_enc.uibp_destino',
         'inv_movimiento_enc.fecha_entrega','inv_movimiento_enc.no_movimiento','inv_movimiento_enc.tipo_movimiento','exp.despacho.direccion','destino.descripcion as despacho_destino','destino.direccion as direccion_destino',
         'inv_movimiento_enc.fecha_doc','exp.despacho.jefe','destino.jefe as jefe_destino','inv_movimiento_enc.recibido_por','inv_movimiento_enc.no_nota','inv_movimiento_enc.fecha_nota' */)
         ->where('inv_movimiento_enc.id_movimiento_enc', $id_movimiento_enc)
         ->where('inv_movimiento_enc.estado', 'P')
         ->first();
 
        $detalleMovimiento = DetalleMovimientos::
      /*   join('VW_ARTICULO_UBICACION', 'VW_ARTICULO_UBICACION.ID_UBICACION','inv_movimiento_det.fk_ubicacion_origen')
        ->leftJoin('inv_detalle_articulo','inv_detalle_articulo.id_detalle_articulo','inv_movimiento_det.fk_detalle_origen') 
        ->join('inv_localizaciones','inv_localizaciones.id_localizacion','inv_movimiento_det.fk_localizacion_destino') */
       /*  ->join('inv_articulos','inv_articulos.id_articulo','VW_ARTICULO_UBICACION.id_articulo')
        ->join('inv_localizaciones','inv_localizaciones.id_localizacion','inv_movimiento_det.fk_localizacion_destino')
        ->leftJoin('inv_detalle_articulo','inv_detalle_articulo.id_detalle_articulo','inv_movimiento_det.fk_detalle_origen')
        ->leftJoin('inv_detalle_articulo','inv_detalle_articulo.id_detalle_articulo','inv_movimiento_det.fk_detalle_origen') */
        join('VW_ARTICULO_UBICACION', 'VW_ARTICULO_UBICACION.ID_UBICACION','inv_movimiento_det.fk_ubicacion_origen')
        ->leftJoin('inv_detalle_articulo','inv_detalle_articulo.id_detalle_articulo','inv_movimiento_det.fk_detalle_origen')
        ->select('inv_movimiento_det.id_movimiento_det','inv_movimiento_det.fk_movimiento_enc','inv_movimiento_det.fk_ubicacion_origen','inv_movimiento_det.fk_detalle_origen',
        'inv_movimiento_det.cantidad','inv_movimiento_det.fk_localizacion_origen','inv_movimiento_det.fk_localizacion_destino','inv_movimiento_det.observaciones',
        'VW_ARTICULO_UBICACION.id_articulo','VW_ARTICULO_UBICACION.codigo','VW_ARTICULO_UBICACION.descripcion','VW_ARTICULO_UBICACION.marca','VW_ARTICULO_UBICACION.modelo',
        'VW_ARTICULO_UBICACION.localizacion','inv_detalle_articulo.numero_serie','inv_detalle_articulo.numero_activo','VW_ARTICULO_UBICACION.cantidad_stock','VW_ARTICULO_UBICACION.cantidad_en_movimiento'
        
        
        /* 'inv_movimiento_det.fk_movimiento_enc','inv_movimiento_det.fk_ubicacion_origen','inv_movimiento_det.fk_detalle_origen',
        'inv_movimiento_det.cantidad','inv_movimiento_det.fk_localizacion_origen','inv_movimiento_det.fk_localizacion_destino','inv_movimiento_det.observaciones',
        'VW_ARTICULO_UBICACION.id_articulo','VW_ARTICULO_UBICACION.codigo','VW_ARTICULO_UBICACION.descripcion','VW_ARTICULO_UBICACION.marca','VW_ARTICULO_UBICACION.modelo',
        'VW_ARTICULO_UBICACION.localizacion','VW_ARTICULO_UBICACION.deposito','VW_ARTICULO_UBICACION.cantidad_stock','VW_ARTICULO_UBICACION.cantidad_en_movimiento',
        'inv_localizaciones.descripcion as localizacion_detino','inv_detalle_articulo.numero_serie','inv_detalle_articulo.numero_activo' */)
        ->where('inv_movimiento_det.fk_movimiento_enc', $id_movimiento_enc)
        ->where('inv_movimiento_det.estatus', 'P')
        ->orderBy('inv_movimiento_det.id_movimiento_det', 'asc')
        ->get();
 
        $movimientoTraspaso->articulosMovimiento = $detalleMovimiento;
        return response()->json([
         "ok" => true,
         "data" => $movimientoTraspaso
        ]);
 

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MovimientoTraspaso  $movimientoTraspaso
     * @return \Illuminate\Http\Response
     */
    public function cancelarTraspasoSalida(CancelarRequestTraspasoSalida $request)
    {
        //cancelar traspaso de salida
        try {
            DB::beginTransaction();
            $id_movimiento_enc = $request->input('id_movimiento_enc');
            $movimientoTraspaso = new MovimientoTraspaso();
            $validar = MovimientoTraspaso::where('id_movimiento_enc',$id_movimiento_enc)->count();

            if ($validar) {
               $data['estado'] = 'I';
               $data['usuario_modifica'] = strtoupper($request->input('usuario'));
               $movimientoTraspaso = MovimientoTraspaso::where('id_movimiento_enc',$id_movimiento_enc)->update($data);

               $detalles = $request->input('articulosMovimiento');
               for ($i=0; $i <count($detalles) ; $i++) { 
                   $detalleArticulos = new DetalleMovimientos();
                   $detalleMovimiento['estatus'] = 'I';
                   $detalleMovimiento['cantidad'] = 0;
                   $detalleMovimiento['usuario_modifica'] =  $data['usuario_modifica'];
                   $detalleArticulo = DetalleMovimientos::where('id_movimiento_det',$detalles[$i]['id_movimiento_det'])->update($detalleMovimiento);

                   $detalleArticulo = new DetallesArticulos();
                    if (isset($detalles[$i]['fk_detalle_origen'])) {
                       $detalleOrigen['disponible'] = 'SI';
                       $detalleArticulo = DetallesArticulos::where('id_detalle_articulo',$detalles[$i]['fk_detalle_origen'])->update($detalleOrigen);
                    }
               }
            } else {
                return 'No existe ningún parametro con este identificador.';
            }
           
           
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "mensajeCancelado" =>'Se cancelo satisfactoriamente'
            ]);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data" =>$error->getMessage(),
                "errorCancelar" =>'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }
    }

    public function cancelarTraspaso(CancelarRequestTraspasoSalida $request)
    {
        //cancelar traspaso de salida
        try {
            DB::beginTransaction();
            $id_movimiento_enc = $request->input('id_movimiento_enc');
            $movimientoTraspaso = new MovimientoTraspaso();
            $validar = MovimientoTraspaso::where('id_movimiento_enc',$id_movimiento_enc)->count();

            if ($validar) {
               $data['estado'] = 'I';
               $data['usuario_modifica'] = strtoupper($request->input('usuario'));
               $movimientoTraspaso = MovimientoTraspaso::where('id_movimiento_enc',$id_movimiento_enc)->update($data);

               $detalles = $request->input('articulosMovimiento');
               for ($i=0; $i <count($detalles) ; $i++) { 
                   $detalleArticulos = new DetalleMovimientos();
                   $detalleMovimiento['estatus'] = 'I';
                   $detalleMovimiento['cantidad'] = 0;
                   $detalleMovimiento['usuario_modifica'] =  $data['usuario_modifica'];
                   $detalleArticulo = DetalleMovimientos::where('id_movimiento_det',$detalles[$i]['id_movimiento_det'])->update($detalleMovimiento);

                   $detalleArticulo = new DetallesArticulos();
                    if (isset($detalles[$i]['fk_detalle_origen'])) {
                       $detalleOrigen['disponible'] = 'SI';
                       $detalleArticulo = DetallesArticulos::where('id_detalle_articulo',$detalles[$i]['fk_detalle_origen'])->update($detalleOrigen);
                    }
               }
            } else {
                return 'No existe ningún parametro con este identificador.';
            }
           
           
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "cancelado" =>'Se cancelo satisfactoriamente'
            ]);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data" =>$error->getMessage(),
                "errorCancelar" =>'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }
    }

    public function cancelarNotaEntrega(CancelarRequestTraspasoSalida $request)
    {
        //cancelar nota de entrega
        try {
            DB::beginTransaction();
            $id_movimiento_enc = $request->input('id_movimiento_enc');
            $movimientoTraspaso = new MovimientoTraspaso();
            $validar = MovimientoTraspaso::where('id_movimiento_enc',$id_movimiento_enc)->count();

            if ($validar) {
               $data['estado'] = 'I';
               $data['usuario_modifica'] = strtoupper($request->input('usuario'));
               $movimientoTraspaso = MovimientoTraspaso::where('id_movimiento_enc',$id_movimiento_enc)->update($data);

               $detalles = $request->input('articulosMovimiento');
               for ($i=0; $i <count($detalles) ; $i++) { 
                   $detalleArticulos = new DetalleMovimientos();
                   $detalleMovimiento['estatus'] = 'I';
                   $detalleMovimiento['cantidad'] = 0;
                   $detalleMovimiento['usuario_modifica'] =  $data['usuario_modifica'];
                   $detalleArticulo = DetalleMovimientos::where('id_movimiento_det',$detalles[$i]['id_movimiento_det'])->update($detalleMovimiento);

                   $detalleArticulo = new DetallesArticulos();
                    if (isset($detalles[$i]['fk_detalle_origen'])) {
                       $detalleOrigen['disponible'] = 'SI';
                       $detalleArticulo = DetallesArticulos::where('id_detalle_articulo',$detalles[$i]['fk_detalle_origen'])->update($detalleOrigen);
                    }
               }
            } else {
                return 'No existe ningún parametro con este identificador.';
            }
           
           
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "mensajeCancelado" =>'Se cancelo satisfactoriamente'
            ]);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data" =>$error->getMessage(),
                "errorCancelar" =>'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }
    }

    public function cancelarTraspasoEntrada(CancelarRequestTraspasoSalida $request)
    {
        //cancelar traspaso de salida
        try {
            DB::beginTransaction();
            $id_movimiento_enc = $request->input('id_movimiento_enc');
            $movimientoTraspaso = new MovimientoTraspaso();
            $validar = MovimientoTraspaso::where('id_movimiento_enc',$id_movimiento_enc)->count();

            if ($validar) {
               $data['estado'] = 'I';
               $data['usuario_modifica'] = strtoupper($request->input('usuario'));
               $movimientoTraspaso = MovimientoTraspaso::where('id_movimiento_enc',$id_movimiento_enc)->update($data);

               $detalles = $request->input('articulosMovimiento');
               for ($i=0; $i <count($detalles) ; $i++) { 
                   $detalleArticulos = new DetalleMovimientos();
                   $detalleMovimiento['estatus'] = 'I';
                   $detalleMovimiento['cantidad'] = 0;
                   $detalleMovimiento['usuario_modifica'] =  $data['usuario_modifica'];
                   $detalleArticulo = DetalleMovimientos::where('id_movimiento_det',$detalles[$i]['id_movimiento_det'])->update($detalleMovimiento);

                   $detalleArticulo = new DetallesArticulos();
                    if (isset($detalles[$i]['fk_detalle_origen'])) {
                       $detalleOrigen['disponible'] = 'SI';
                       $detalleArticulo = DetallesArticulos::where('id_detalle_articulo',$detalles[$i]['fk_detalle_origen'])->update($detalleOrigen);
                    }
               }
            } else {
                return 'No existe ningún parametro con este identificador.';
            }
           
           
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "mensajeCancelado" =>'Se cancelo satisfactoriamente'
            ]);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data" =>$error->getMessage(),
                "errorCancelar" =>'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MovimientoTraspaso  $movimientoTraspaso
     * @return \Illuminate\Http\Response
     */
    public function eliminarArticuloMovimiento(EliminarArticuloMovimientoRequest  $request)
    {
            $id_movimiento_det = $request->input('id_movimiento_det');
            $fk_detalle_origen = $request->input('fk_detalle_origen');
        try {
                if (isset($fk_detalle_origen)) {
                    $detalle = new DetallesArticulos();
                    $detalleArticulo['disponible'] = 'SI';
                    $detalle = DetallesArticulos::where('id_detalle_articulo',$fk_detalle_origen)->update($detalleArticulo);
                    $movimientoTraspaso = DetalleMovimientos::where('id_movimiento_det', $id_movimiento_det)->delete();
                } else {
                    $movimientoTraspaso = DetalleMovimientos::where('id_movimiento_det', $id_movimiento_det)->delete();
                }
           
            DB::commit();
           
            return response()->json([
                "ok" =>true,
                "data" => $movimientoTraspaso,
                "eliminarArticulo" => 'Se eliminó satisfactoriamente'
            ]);

        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" => false,
                "data" =>$th->getMessage(),
                "error" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }
        
    }

    public function confirmarTraspasoSalida(ConfirmarRequestSalida $request)
    {
        try {
            DB::beginTransaction();
            $id_movimiento_enc = $request->input('id_movimiento_enc');
            $movimientoTraspaso = new MovimientoTraspaso();
            $validar = MovimientoTraspaso::
            where('id_movimiento_enc',$id_movimiento_enc)
            ->where('estado', 'P')
            ->count();

            if ($validar) {
               $movimientoTraspaso          = new MovimientoTraspaso();
               $data['estado']              = 'C';
               $data['fecha_entrega']       = $request->input('fecha_entrega');
               $data['solicitado_por']      = ucwords($request->input('solicitado_por'));
               $data['recibido_por']        = ucwords($request->input('recibido_por'));
               $data['entregado_por']       = ucwords($request->input('entregado_por'));
               $data['aprobado_por']        = ucwords($request->input('aprobado_por'));
               $data['observacion']         = ucfirst($request->input('observacion'));
               $data['uibp_origen']         = ucwords($request->input('uibp_origen'));
               $data['uibp_destino']        = ucwords($request->input('uibp_destino'));
               $data['usuario_modifica']    = strtoupper($request->input('usuario'));
               $movimientoTraspaso          = MovimientoTraspaso::where('id_movimiento_enc', $id_movimiento_enc)->update($data);

               $detalle = $request->input('articulosMovimiento');
               for ($i=0; $i <count($detalle) ; $i++) { 
                $detalleMovimiento = new DetalleMovimientos();
                $validarDetalle = DetalleMovimientos::
                where('id_movimiento_det',$detalle[$i]['id_movimiento_det'])
                ->where('estatus', 'P')
                ->count();
                if ($validarDetalle) {
                    $detalleArticulo['estatus']  = 'C';
                    $detalleArticulo['cantidad'] = $detalle[$i]['cantidad'];
                    $detalleArticulo['fk_localizacion_destino'] = $detalle[$i]['fk_localizacion_destino'];
                    $detalleArticulo['usuario_modifica'] =  $data['usuario_modifica'];
                    $detalleMovimiento = DetalleMovimientos::where('id_movimiento_det', $detalle[$i]['id_movimiento_det'])->update($detalleArticulo);
                } else {
                    return 'No se puede confirmar este traspaso';
                }

                $ubicaciones = new UbicacionArticulo();
                $validarUbicacion = UbicacionArticulo::
                where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])
                ->count();
                if ($validarUbicacion) {
                   //resta la cantidad de stock de origen
                   $dataUbicacion['cantidad_stock'] = $detalle[$i]['cantidad_stock'] - $detalle[$i]['cantidad'];
                   $dataUbicacion['cantidad_en_movimiento'] = $detalle[$i]['cantidad_en_movimiento'] - $detalle[$i]['cantidad_en_movimiento'];
                   $dataUbicacion['usuario_modifica'] = $data['usuario_modifica'];
                   $ubicaciones = UbicacionArticulo::where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])->update($dataUbicacion);

                   if ($dataUbicacion['cantidad_stock'] == 0) {
                     $dataUbicacion['detalle'] = 'NO';
                     $dataUbicacion['usuario_modifica'] = $data['usuario_modifica'];
                     $ubicaciones = UbicacionArticulo::where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])->update($dataUbicacion);
                   }

                } else {
                 return 'No se puede confirmar este traspaso';
                }

                $mostrarUbicacion = UbicacionArticulo::select('inv_ubicacion_articulos.id_ubicacion' ,'inv_ubicacion_articulos.cantidad_stock as stock_destino','detalle')
                   ->where('fk_articulo', $detalle[$i]['id_articulo'])
                   ->where('fk_localizacion', $detalle[$i]['fk_localizacion_destino'])
                   ->get();

                   if (count($mostrarUbicacion) > 0) {
                    $ubicar = new UbicacionArticulo;
                    $ubicacionArticulo['fk_localizacion'] = $detalle[$i]['fk_localizacion_destino'];
                    $ubicacionArticulo['fk_articulo'] =     $detalle[$i]['id_articulo'];
                    $ubicacionArticulo['usuario_modifica'] =  $data['usuario_modifica'];
                    $ubicacionArticulo['cantidad_stock'] =   $mostrarUbicacion[0]['stock_destino'] + $detalle[$i]['cantidad'];
                    $ubicar = UbicacionArticulo::where('id_ubicacion', $mostrarUbicacion[0]['id_ubicacion'])->update($ubicacionArticulo);

                    $detalleMovimiento = new DetallesArticulos();
                    $dataDetalle['disponible'] = 'SI';
                    $dataDetalle['fk_ubicacion'] =   $mostrarUbicacion[0]['id_ubicacion'];
                    $dataDetalle['usuario_modifica'] = $data['usuario_modifica'];
                    $detalleMovimiento = DetallesArticulos::where('id_detalle_articulo', $detalle[$i]['fk_detalle_origen'])->update($dataDetalle);
                  
                } else { 
                    $ubicarArticulo = new UbicacionArticulo;
                    if (isset($detalle[$i]['fk_detalle_origen'])) {
                        $ubicarArticulo->fk_localizacion = $detalle[$i]['fk_localizacion_destino'];
                        $ubicarArticulo->fk_articulo =     $detalle[$i]['id_articulo'];
                        $ubicarArticulo->usuario_crea =    $data['usuario_modifica'];
                        $ubicarArticulo->cantidad_stock =  $detalle[$i]['cantidad'];
                        $ubicarArticulo->detalle = 'SI';
                        $ubicarArticulo->save();
    
                        $detalleMovimiento = new DetallesArticulos();
                        $dataDetalle['disponible'] = 'SI';
                        $dataDetalle['fk_ubicacion'] =  $ubicarArticulo->id_ubicacion;
                        $dataDetalle['usuario_modifica'] = $data['usuario_modifica'];
                        $detalleMovimiento = DetallesArticulos::where('id_detalle_articulo', $detalle[$i]['fk_detalle_origen'])->update($dataDetalle);
    
                    } else {
                        $ubicarArticulo->fk_localizacion = $detalle[$i]['fk_localizacion_destino'];
                        $ubicarArticulo->fk_articulo =     $detalle[$i]['id_articulo'];
                        $ubicarArticulo->usuario_crea =    $data['usuario_modifica'];
                        $ubicarArticulo->cantidad_stock =  $detalle[$i]['cantidad'];
                        $ubicarArticulo->detalle = 'NO';
                        $ubicarArticulo->save();
    
                    }

                }

                
               }

              /*  for ($i=0; $i <count($detalle) ; $i++) { 
                   $detalleMovimiento = new DetalleMovimientos();
                   $detalleArticulo['estatus'] = 'C';
                   $detalleArticulo['usuario_modifica'] =  $data['usuario_modifica'];
                   if (isset($detalle[$i]['id_movimiento_det'])) {
                    $detalleMovimiento = DetalleMovimientos::where('id_movimiento_det', $detalle[$i]['id_movimiento_det'])->update($detalleArticulo);
                   } else {
                       return 'El identificador del detalle de movimiento no existe.';
                   }

                   $ubicaciones = new UbicacionArticulo();
                   $validarUbicacion = UbicacionArticulo::
                   where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])
                   ->count();
                   if ($validarUbicacion) {
                      //resta la cantidad de stock de origen
                      $dataUbicacion['cantidad_stock'] = $detalle[$i]['cantidad_stock'] - $detalle[$i]['cantidad_en_movimiento'];
                      $dataUbicacion['cantidad_en_movimiento'] = $detalle[$i]['cantidad_en_movimiento'] - $detalle[$i]['cantidad_en_movimiento'];
                      $dataUbicacion['usuario_modifica'] = $data['usuario_modifica'];
                      $ubicaciones = UbicacionArticulo::where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])->update($dataUbicacion);

                      if ($dataUbicacion['cantidad_stock'] == 0) {
                        $dataUbicacion['detalle'] = 'NO';
                        $dataUbicacion['usuario_modifica'] = $data['usuario_modifica'];
                        $ubicaciones = UbicacionArticulo::where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])->update($dataUbicacion);
                      }

                   } else {
                    return 'No existe ningún parametro con este identificador de ubicacion.';
                   }

                   $mostrarUbicacion = UbicacionArticulo::select('inv_ubicacion_articulos.id_ubicacion' ,'inv_ubicacion_articulos.cantidad_stock as stock_destino','detalle')
                   ->where('fk_articulo', $detalle[$i]['id_articulo'])
                   ->where('fk_localizacion', $detalle[$i]['fk_localizacion_destino'])
                   ->get();

                   if (count($mostrarUbicacion) > 0) {
                    $ubicar = new UbicacionArticulo;
                    $ubicacionArticulo['fk_localizacion'] = $detalle[$i]['fk_localizacion_destino'];
                    $ubicacionArticulo['fk_articulo'] =     $detalle[$i]['id_articulo'];
                    $ubicacionArticulo['usuario_modifica'] =  $data['usuario_modifica'];
                    $ubicacionArticulo['cantidad_stock'] =   $mostrarUbicacion[0]['stock_destino'] + $detalle[$i]['cantidad'];
                    $ubicar = UbicacionArticulo::where('id_ubicacion', $mostrarUbicacion[0]['id_ubicacion'])->update($ubicacionArticulo);

                    $detalleMovimiento = new DetallesArticulos();
                    $dataDetalle['disponible'] = 'SI';
                    $dataDetalle['fk_ubicacion'] =   $mostrarUbicacion[0]['id_ubicacion'];
                    $dataDetalle['usuario_modifica'] = $data['usuario_modifica'];
                    $detalleMovimiento = DetallesArticulos::where('id_detalle_articulo', $detalle[$i]['fk_detalle_origen'])->update($dataDetalle);
                  
                } else { 
                    $ubicarArticulo = new UbicacionArticulo;
                    if (isset($detalle[$i]['fk_detalle_origen'])) {
                        $ubicarArticulo->fk_localizacion = $detalle[$i]['fk_localizacion_destino'];
                        $ubicarArticulo->fk_articulo =     $detalle[$i]['id_articulo'];
                        $ubicarArticulo->usuario_crea =    $data['usuario_modifica'];
                        $ubicarArticulo->cantidad_stock =  $detalle[$i]['cantidad'];
                        $ubicarArticulo->detalle = 'SI';
                        $ubicarArticulo->save();
    
                        $detalleMovimiento = new DetallesArticulos();
                        $dataDetalle['disponible'] = 'SI';
                        $dataDetalle['fk_ubicacion'] =  $ubicarArticulo->id_ubicacion;
                        $dataDetalle['usuario_modifica'] = $data['usuario_modifica'];
                        $detalleMovimiento = DetallesArticulos::where('id_detalle_articulo', $detalle[$i]['fk_detalle_origen'])->update($dataDetalle);
    
                    } else {
                        $ubicarArticulo->fk_localizacion = $detalle[$i]['fk_localizacion_destino'];
                        $ubicarArticulo->fk_articulo =     $detalle[$i]['id_articulo'];
                        $ubicarArticulo->usuario_crea =    $data['usuario_modifica'];
                        $ubicarArticulo->cantidad_stock =  $detalle[$i]['cantidad'];
                        $ubicarArticulo->detalle = 'NO';
                        $ubicarArticulo->save();
    
                    }

               }


               } */
            } else {
                return 'No se puede confirmar este traspaso';
            }
         
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "confirmarSalida" => 'Se confirmó satisfactoriamente'
            ]);
        } catch (\Exception $th) {
            return response()->json([
                "ok" => false,
                "data" =>$th->getMessage(),
                "errorConfrimar" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }
    }

    public function confirmarTraspasoEntrada(ConfirmarRequestSalida $request)
    {
        try {
            DB::beginTransaction();
            $id_movimiento_enc = $request->input('id_movimiento_enc');
            $movimientoTraspaso = new MovimientoTraspaso();
            $validar = MovimientoTraspaso::
            where('id_movimiento_enc',$id_movimiento_enc)
            ->where('estado', 'P')
            ->count();

            if ($validar) {
               $movimientoTraspaso = new MovimientoTraspaso();
               $data['estado'] = 'C';
               $data['fecha_entrega']       = $request->input('fecha_entrega');
               $data['solicitado_por']      = ucwords($request->input('solicitado_por'));
               $data['recibido_por']        = ucwords($request->input('recibido_por'));
               $data['entregado_por']       = ucwords($request->input('entregado_por'));
               $data['aprobado_por']        = ucwords($request->input('aprobado_por'));
               $data['observacion']         = ucfirst($request->input('observacion'));
               $data['uibp_origen']         = ucwords($request->input('uibp_origen'));
               $data['uibp_destino']        = ucwords($request->input('uibp_destino'));
               $data['usuario_modifica']    = strtoupper($request->input('usuario'));
               $movimientoTraspaso = MovimientoTraspaso::where('id_movimiento_enc', $id_movimiento_enc)->update($data);

               $detalle = $request->input('articulosMovimiento');

               for ($i=0; $i <count($detalle) ; $i++) { 
                   $detalleMovimiento = new DetalleMovimientos();
                   $detalleArticulo['estatus'] = 'C';
                   $detalleArticulo['cantidad'] = $detalle[$i]['cantidad'];
                   $detalleArticulo['fk_localizacion_destino'] = $detalle[$i]['fk_localizacion_destino'];
                   $detalleArticulo['usuario_modifica'] =  $data['usuario_modifica'];
                   $detalleMovimiento = DetalleMovimientos::where('id_movimiento_det', $detalle[$i]['id_movimiento_det'])->update($detalleArticulo);

                   $ubicaciones = new UbicacionArticulo();
                   $validarUbicacion = UbicacionArticulo::
                   where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])
                   ->count();
                   if ($validarUbicacion) {
                      //resta la cantidad de stock de origen
                      $dataUbicacion['cantidad_stock'] = $detalle[$i]['cantidad_stock'] - $detalle[$i]['cantidad'];
                      $dataUbicacion['cantidad_en_movimiento'] = $detalle[$i]['cantidad_en_movimiento'] - $detalle[$i]['cantidad_en_movimiento'];
                      $dataUbicacion['usuario_modifica'] = $data['usuario_modifica'];
                      $ubicaciones = UbicacionArticulo::where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])->update($dataUbicacion);

                   } else {
                    return 'No existe ningún parametro con este identificador de ubicacion.';
                   }

                   $mostrarUbicacion = UbicacionArticulo::select('inv_ubicacion_articulos.id_ubicacion' ,'inv_ubicacion_articulos.cantidad_stock as stock_destino')
                   ->where('fk_articulo', $detalle[$i]['id_articulo'])
                   ->where('fk_localizacion', $detalle[$i]['fk_localizacion_destino'])
                   ->get();

                   if (count($mostrarUbicacion) > 0) {
                    $ubicar = new UbicacionArticulo;
                    $ubicacionArticulo['fk_localizacion'] = $detalle[$i]['fk_localizacion_destino'];
                    $ubicacionArticulo['fk_articulo'] =     $detalle[$i]['id_articulo'];
                    $ubicacionArticulo['usuario_modifica'] =  $data['usuario_modifica'];
                    $ubicacionArticulo['cantidad_stock'] =   $mostrarUbicacion[0]['stock_destino'] + $detalle[$i]['cantidad'];
                    $ubicar = UbicacionArticulo::where('id_ubicacion', $mostrarUbicacion[0]['id_ubicacion'])->update($ubicacionArticulo);

                    $detalleMovimiento = new DetallesArticulos();
                    $dataDetalle['disponible'] = 'SI';
                    $dataDetalle['fk_ubicacion'] =   $mostrarUbicacion[0]['id_ubicacion'];
                    $dataDetalle['usuario_modifica'] = $data['usuario_modifica'];
                    $detalleMovimiento = DetallesArticulos::where('id_detalle_articulo', $detalle[$i]['fk_detalle_origen'])->update($dataDetalle);
                  
                } else { 
                    $ubicarArticulo = new UbicacionArticulo;
                    if (isset($detalle[$i]['fk_detalle_origen'])) {
                        $ubicarArticulo->fk_localizacion = $detalle[$i]['fk_localizacion_destino'];
                        $ubicarArticulo->fk_articulo =     $detalle[$i]['id_articulo'];
                        $ubicarArticulo->usuario_crea =    $data['usuario_modifica'];
                        $ubicarArticulo->cantidad_stock =  $detalle[$i]['cantidad'];
                        $ubicarArticulo->detalle = 'SI';
                        $ubicarArticulo->save();

                        $detalleMovimiento = new DetallesArticulos();
                        $dataDetalle['disponible'] = 'SI';
                        $dataDetalle['fk_ubicacion'] =   $ubicarArticulo->id_ubicacion;
                        $dataDetalle['usuario_modifica'] = $data['usuario_modifica'];
                        $detalleMovimiento = DetallesArticulos::where('id_detalle_articulo', $detalle[$i]['fk_detalle_origen'])->update($dataDetalle);
                    } else {
                        $ubicarArticulo->fk_localizacion = $detalle[$i]['fk_localizacion_destino'];
                        $ubicarArticulo->fk_articulo =     $detalle[$i]['id_articulo'];
                        $ubicarArticulo->usuario_crea =    $data['usuario_modifica'];
                        $ubicarArticulo->cantidad_stock =  $detalle[$i]['cantidad'];
                        $ubicarArticulo->detalle = 'NO';
                        $ubicarArticulo->save();
    
                    }
                  
               }


               }
            } else {
                return 'No se puede confirmar este traspaso';
            }
         
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "confirmarSalida" => 'Se confirmó satisfactoriamente'
            ]);
        } catch (\Exception $th) {
            return response()->json([
                "ok" => false,
                "data" =>$th->getMessage(),
                "errorConfrimar" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }
    }

    public function confirmarTraspasoNota(ConfirmarRequestNota $request)
    {
        try {
            DB::beginTransaction();
            $id_movimiento_enc = $request->input('id_movimiento_enc');
            $movimientoTraspaso = new MovimientoTraspaso();
            $validar = MovimientoTraspaso::
            where('id_movimiento_enc',$id_movimiento_enc)
            ->where('estado', 'P')
            ->count();

            if ($validar) {
               $movimientoTraspaso          = new MovimientoTraspaso();
               $id_movimiento_enc           = $request->input('id_movimiento_enc');
               $data['fecha_entrega']       = $request->input('fecha_entrega');
               $data['fecha_nota']          = $request->input('fecha_nota');
               $data['solicitado_por']      = ucwords($request->input('solicitado_por'));
               $data['recibido_por']        = ucwords($request->input('recibido_por'));
               $data['aprobado_por']        = ucwords($request->input('aprobado_por'));
               $data['entregado_por']       = ucwords($request->input('entregado_por'));
               $data['observacion']         = ucfirst($request->input('observacion'));
               $data['uibp_origen']         = ucwords($request->input('uibp_origen'));
               $data['uibp_destino']        = ucwords($request->input('uibp_destino'));
               $data['no_nota']             = ucwords($request->input('no_nota'));
               $data['usuario_modifica']    = strtoupper($request->input('usuario'));
               $data['estado']              = 'C';
               $movimientoTraspaso          = MovimientoTraspaso::where('id_movimiento_enc', $id_movimiento_enc)->update($data);

               $detalle = $request->input('articulosMovimiento');
               for ($i=0; $i <count($detalle) ; $i++) { 
                $detalleMovimiento = new DetalleMovimientos();
                $detalleArticulo['estatus']                 = 'C';
                $detalleArticulo['usuario_modifica']        = $data['usuario_modifica'];
                $detalleArticulo['fk_localizacion_destino'] = $detalle[$i]['fk_localizacion_destino'];
                $detalleArticulo['cantidad']                = $detalle[$i]['cantidad'];
                $detalleArticulo['observaciones']           = ucfirst($detalle[$i]['observaciones']);
                $detalleMovimiento = DetalleMovimientos::where('id_movimiento_det', $detalle[$i]['id_movimiento_det'])->update($detalleArticulo);

                $ubicaciones = new UbicacionArticulo();
                $validarUbicacion = UbicacionArticulo::
                where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])
                ->count();
                if ($validarUbicacion) {
                   //resta la cantidad de stock de origen
                   $dataUbicacion['cantidad_stock'] = $detalle[$i]['cantidad_stock'] - $detalle[$i]['cantidad'];
                   $dataUbicacion['cantidad_en_movimiento'] = $detalle[$i]['cantidad_en_movimiento'] - $detalle[$i]['cantidad'];
                   $dataUbicacion['usuario_modifica'] = $data['usuario_modifica'];
                   $ubicaciones = UbicacionArticulo::where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])->update($dataUbicacion);

                   if ($dataUbicacion['cantidad_stock'] == 0) {
                     $dataUbicacion['detalle'] = 'NO';
                     $dataUbicacion['usuario_modifica'] = $data['usuario_modifica'];
                     $ubicaciones = UbicacionArticulo::where('id_ubicacion', $detalle[$i]['fk_ubicacion_origen'])->update($dataUbicacion);
                   }

                } else {
                 return 'No existe ningún parametro con este identificador de ubicacion.';
                }

                $mostrarUbicacion = UbicacionArticulo::select('inv_ubicacion_articulos.id_ubicacion' ,'inv_ubicacion_articulos.cantidad_stock as stock_destino','detalle')
                ->where('fk_articulo', $detalle[$i]['id_articulo'])
                ->where('fk_localizacion', $detalle[$i]['fk_localizacion_destino'])
                ->get();

                if (count($mostrarUbicacion) > 0) {
                 $ubicar = new UbicacionArticulo;
                 $ubicacionArticulo['fk_localizacion']  =  $detalle[$i]['fk_localizacion_destino'];
                 $ubicacionArticulo['fk_articulo']      =  $detalle[$i]['id_articulo'];
                 $ubicacionArticulo['usuario_modifica'] =  $data['usuario_modifica'];
                 $ubicacionArticulo['cantidad_stock']   =  $mostrarUbicacion[0]['stock_destino'] + $detalle[$i]['cantidad'];
                 $ubicar = UbicacionArticulo::where('id_ubicacion', $mostrarUbicacion[0]['id_ubicacion'])->update($ubicacionArticulo);

                 $detalleMovimiento = new DetallesArticulos();
                 $dataDetalle['disponible']       = 'SI';
                 $dataDetalle['fk_ubicacion']     =   $mostrarUbicacion[0]['id_ubicacion'];
                 $dataDetalle['usuario_modifica'] = $data['usuario_modifica'];
                 $detalleMovimiento = DetallesArticulos::where('id_detalle_articulo', $detalle[$i]['fk_detalle_origen'])->update($dataDetalle);
               
             } else { 
                 $ubicarArticulo = new UbicacionArticulo;
                 if (isset($detalle[$i]['fk_detalle_origen'])) {
                     $ubicarArticulo->fk_localizacion = $detalle[$i]['fk_localizacion_destino'];
                     $ubicarArticulo->fk_articulo =     $detalle[$i]['id_articulo'];
                     $ubicarArticulo->usuario_crea =    $data['usuario_modifica'];
                     $ubicarArticulo->cantidad_stock =  $detalle[$i]['cantidad'];
                     $ubicarArticulo->detalle = 'SI';
                     $ubicarArticulo->save();
 
                     $detalleMovimiento = new DetallesArticulos();
                     $dataDetalle['disponible'] = 'SI';
                     $dataDetalle['fk_ubicacion'] =  $ubicarArticulo->id_ubicacion;
                     $dataDetalle['usuario_modifica'] = $data['usuario_modifica'];
                     $detalleMovimiento = DetallesArticulos::where('id_detalle_articulo', $detalle[$i]['fk_detalle_origen'])->update($dataDetalle);
 
                 } else {
                     $ubicarArticulo->fk_localizacion = $detalle[$i]['fk_localizacion_destino'];
                     $ubicarArticulo->fk_articulo     = $detalle[$i]['id_articulo'];
                     $ubicarArticulo->usuario_crea    = $data['usuario_modifica'];
                     $ubicarArticulo->cantidad_stock  = $detalle[$i]['cantidad'];
                     $ubicarArticulo->detalle = 'NO';
                     $ubicarArticulo->save();
 
                 }

             }

               }

            } else {
                return 'No se puede confirmar este traspaso';
            }
         
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data" =>$movimientoTraspaso,
                "confirmado" => 'Se confirmó satisfactoriamente'
            ]);
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" => false,
                "data" =>$th->getMessage(),
                "errorConfirmar" => 'Ha habido un error en el momento de realizar el registro, consulte con el administrador del sistema.'
            ]);
        }
    }

}
