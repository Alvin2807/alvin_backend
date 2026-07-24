<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetallesArticulos;
use Illuminate\Http\Request;
use App\Http\Requests\DetalleArticuloStoreRequest;
use App\Models\UbicacionArticulo;
use App\Http\Requests\EditarRequestDetalleArticulo;
use Illuminate\Support\Facades\DB;
class DetalleArticulosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //mostrar detalle de articulos 
        $detallesArticulos = DetallesArticulos::
        join('inv_detalle_compras','inv_detalle_compras.id_detalle','inv_detalle_articulo.fk_detalle_compra')
        ->join('inv_articulos','inv_articulos.id_articulo','inv_detalle_compras.fk_articulo')
        ->leftJoin('inv_marcas','inv_marcas.id_marca','inv_articulos.fk_marca')
        ->leftJoin('inv_modelos','inv_modelos.id_modelo','inv_articulos.fk_modelo')
        ->leftJoin('inv_categorias','inv_categorias.id_categoria','inv_articulos.fk_categoria')
        ->leftJoin('inv_tipo_usos','inv_tipo_usos.id_tipo_uso','inv_articulos.fk_tipo_uso')
        ->select('inv_detalle_articulo.id_detalle_articulo','inv_detalle_articulo.fk_ubicacion','inv_detalle_articulo.fk_detalle_compra','inv_detalle_articulo.fk_fer_localizacion',
        'inv_detalle_articulo.fk_color','inv_detalle_articulo.descripcion','inv_detalle_articulo.codigo_barra','inv_detalle_articulo.usuario_crea','inv_detalle_articulo.numero_serie',
        'inv_detalle_articulo.numero_activo','inv_articulos.descripcion as articulo','inv_marcas.nombre_marca','inv_modelos.nombre_modelo','inv_categorias.descripcion as categoria',
        'inv_tipo_usos.descripcion as tipo_uso','inv_articulos.requiere_activo')
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$detallesArticulos
        ]);
    }

    public function mostrarActivoExiste($numero_activo) { 
        $activo_existe = DetallesArticulos::
        select('id_detalle_articulo','numero_activo')
        ->where('numero_activo',$numero_activo)->get();
        $resp = count($activo_existe) > 0;
        return response()->json([ 
            "ok" =>true,
            "data" =>$resp
        ]);
    }

    public function verificarActivoExiste(Request $request){
        $numero_activo = $request->input('numero_activo');
        $id_detalle_articulo = $request->input('id_detalle_articulo');

        $detallesArticulos = DetallesArticulos::
        select('id_detalle_articulo','numero_activo')
        ->where('numero_activo', $numero_activo)->where('id_detalle_articulo','<>', $id_detalle_articulo)->count();
        return response()->json([
            "ok" =>true,
            "data" =>$detallesArticulos
        ]);
    }

    public function verificarNumeroSerieExiste(Request $request){
        $numero_serie = $request->input('numero_serie');
        $id_detalle_articulo = $request->input('id_detalle_articulo');

        $detallesArticulos = DetallesArticulos::
        select('id_detalle_articulo','numero_serie')
        ->where('numero_serie', $numero_serie)->where('id_detalle_articulo','<>', $id_detalle_articulo)->count();
        return response()->json([
            "ok" =>true,
            "data" =>$detallesArticulos
        ]);
    }

    public function mostrarNumeroSerieExiste($numero_serie) { 
        $serie_existe = DetallesArticulos::
        select('id_detalle_articulo','numero_serie')
        ->where('numero_serie',$numero_serie)->get();
        $resp = count( $serie_existe) > 0;
        return response()->json([ 
            "ok" =>true,
            "data" =>$resp
        ]);
    }




    public function store(DetalleArticuloStoreRequest $request)
    {
        //registrar detalle de articulos
       
       try {
        DB::beginTransaction();
        $item = $request->input('detalleArticulos');
        //verifica si existe numero activo

        for ($i=0; $i <count($item) ; $i++) { 
            $numero_activo = strtoupper($item[$i]['numero_activo']);

            $verificarActivo = DetallesArticulos::where('numero_activo', $numero_activo)->count();
            if ($verificarActivo) {
               return [
                   "ok" =>false,
                   "existeActivo" => 'Ya existe el numero de activo '.$numero_activo
               ];
            }
        }

        for ($i=0; $i <count($item) ; $i++) { 
            $numero_serie = strtoupper($item[$i]['numero_serie']);

            $verificarSerie = DetallesArticulos::where('numero_serie', $numero_serie)->count();
            if ($verificarSerie) {
               return [
                   "ok" =>false,
                   "existeSerie" => 'Ya existe el numero de serie '.$numero_serie
               ];
            }
        }

        for ($i=0; $i <count($item) ; $i++) { 
          $detallesArticulos = new DetallesArticulos();
          $detallesArticulos->fk_ubicacion        = $item[$i]['fk_ubicacion'];
          $detallesArticulos->fk_detalle_compra   = $item[$i]['fk_detalle_compra'];
          $detallesArticulos->numero_serie        = strtoupper($item[$i]['numero_serie']);
          $detallesArticulos->descripcion         = strtoupper($item[$i]['descripcion']);
          $detallesArticulos->codigo_barra        = strtoupper($item[$i]['codigo_barra']);
          $detallesArticulos->numero_activo       = strtoupper($item[$i]['numero_activo']);
          $detallesArticulos->fk_color            = $item[$i]['id_color'];
          $detallesArticulos->estatus             = 'A';
          $detallesArticulos->usuario_crea        = strtoupper($item[$i]['usuario']);
          $detallesArticulos->fk_fer_localizacion = $item[$i]['fk_fer_localizacion'];
          $detallesArticulos->save();

          $ubicacionArticulo = new UbicacionArticulo();
          $dataUbicacion['detalle'] = 'SI';
          $ubicacionArticulo = UbicacionArticulo::where('id_ubicacion', $item[$i]['fk_ubicacion'])->update($dataUbicacion);
         
        }
         DB::commit();
        return response()->json([
            "ok" =>true,
            "data"=>$item,
            "mensajeExistoso" => 'Se guardo satisfactoriamente'
        ]);
       } catch (\Exception $ex) {
           DB::rollBack();
          return response()->json([
              "ok" =>false,
              "data" =>$ex->getMessage(),
              "mensajeError" =>'Hubo un error en el registro, consulte con el administrador del sistema.'
          ]);
       }
    }

    public function editarDetalleArticulo(EditarRequestDetalleArticulo $request) {
        try {
            DB::beginTransaction();
            $id_detalle_articulo          = $request->input('id_detalle_articulo');
            $detallesArticulos            = new DetallesArticulos();
            $detalles['fk_ubicacion']     = $request->input('fk_ubicacion');
            $detalles['numero_serie']     = strtoupper($request->input('numero_serie'));
            $detalles['codigo_barra']     = strtoupper($request->input('codigo_barra'));
            $detalles['numero_activo']    = strtoupper($request->input('numero_activo'));
            $detalles['fk_color']         = $request->input('fk_color');
            $detalles['usuario_modifica'] = strtoupper($request->input('usuario_modifica'));
            $detalles['estatus']          = 'A';
            $detallesArticulos = DetallesArticulos::where('id_detalle_articulo', $id_detalle_articulo)->update($detalles);

            DB::commit();
            return response()->json([
                "ok"   =>true,
                "data" =>$detallesArticulos,
                "modificado" => 'Se guardo satisfactoriamente'
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"    =>false,
                "data"  =>$ex->getMessage(),
                "error" => 'Hubo un error consulte con el administrador del sistema.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DetallesArticulos  $detallesArticulos
     * @return \Illuminate\Http\Response
     */


    
}
