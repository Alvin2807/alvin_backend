<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Localizaciones;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreRequestLocalizaciones;
use App\Http\Requests\StoreRequestEditarLocalizacion;
use App\Http\Requests\CambiarActivoRequest;
use App\Http\Requests\CambiarInactivoRequest;
use Illuminate\Http\Request;
use App\Http\Requests\CambiarPredeterminadoRequest;

class LocalizacionController extends Controller
{
    
    public function index()
    {
        $localizaciones = localizaciones::
        join('inv_depositos','id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion','inv_localizaciones.fk_deposito','inv_depositos.descripcion as deposito',
        'inv_localizaciones.descripcion','inv_localizaciones.estado','exp.despacho.descripcion as despacho', 'inv_localizaciones.usuario_modifica','exp.despacho.codigo as fk_despacho')
        ->get();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }

    public function localizacionesOtroDespacho($codigo_despacho)
    {
        $localizaciones = localizaciones::
        join('inv_depositos','id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion','inv_localizaciones.fk_deposito','inv_depositos.descripcion as deposito',
        'inv_localizaciones.descripcion','inv_localizaciones.estado','exp.despacho.descripcion as despacho', 'inv_localizaciones.usuario_modifica','exp.despacho.codigo as fk_despacho')
        ->where('exp.despacho.codigo','<>',$codigo_despacho)
        ->get();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }

    public function misLocalizaciones($codigo_despacho)
    {
        $localizaciones = localizaciones::
        join('inv_depositos','id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion','inv_localizaciones.fk_deposito','inv_depositos.descripcion as deposito',
        'inv_localizaciones.descripcion','inv_localizaciones.estado','exp.despacho.descripcion as despacho', 'inv_localizaciones.usuario_modifica','exp.despacho.codigo as fk_despacho')
        ->where('exp.despacho.codigo',$codigo_despacho)
        ->get();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }

    public function store(StoreRequestLocalizaciones $request)
    {
        try {
            $fk_deposito  = strtoupper($request->input('fk_deposito'));
            $item = $request->input('localizaciones');
            $usuario =  strtoupper($request->input('usuario'));
            for ($i=0; $i <count($item) ; $i++) {
                $localizacion = strtoupper($item[$i]['descripcion']);

                $existeLocalizacion = Localizaciones::where('descripcion',  $localizacion)->count();
                if ($existeLocalizacion) {
                    return [ 
                            "ok"    => false,
                            "existeLocalizacion"   => 'Ya existe la localización '  .$localizacion
                    ];
                }
            }
        
           
            for ($i=0; $i <count($item) ; $i++) { 
                $registrarLocalizacion = new Localizaciones();
                $registrarLocalizacion->fk_deposito = $fk_deposito;
                $registrarLocalizacion->descripcion = strtoupper($item[$i]['descripcion']);
                $registrarLocalizacion->estado = 'A';
                $registrarLocalizacion->usuario_crea = $usuario;
                $registrarLocalizacion->save();
            }
         
            
            return response()->json([ 
                "ok" =>true,
                "data" => $registrarLocalizacion,
                "aprobado" => 'Se guardo satisfactoriamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([ 
                "ok" =>false,
                "data" =>$e->getMessage(),
                "errorLocalizacion" => 'Hubo un error consulte con el Administrador del sistema.'
            ]);
        }
    }


    public function traerLocalizaciones($id_localizacion){
        $localizaciones = Localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->select('inv_localizaciones.id_localizacion','inv_localizaciones.fk_deposito','inv_depositos.descripcion')
        ->where('inv_localizaciones.id_localizacion', $id_localizacion)
        ->first();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones

        ]);
    }

    public function listarDepositosPorLocalizaciones($id_localizacion){
        //Listar depositos con localizaciones
        $localizaciones = Localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion', 'inv_localizaciones.fk_deposito', 'inv_depositos.descripcion', 'exp.despacho.descripcion as despacho',
        'inv_localizaciones.estado as estado_localizacion','inv_localizaciones.descripcion as localizacion')
        ->where('inv_localizaciones.id_localizacion', $id_localizacion)
        ->first();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }

    public function localizacionesPrederteminadas ($codigo) { 
        $localizaciones = localizaciones::
        join('inv_depositos','inv_depositos.id_deposito', 'inv_localizaciones.fk_deposito')
        ->select('inv_localizaciones.id_localizacion','inv_localizaciones.descripcion as localizacion','inv_localizaciones.fk_deposito','inv_depositos.descripcion as deposito')
        ->where('inv_localizaciones.estado','P')
        ->where('inv_depositos.inventario','INV')
        ->where('inv_depositos.fk_despacho', $codigo)
        ->first();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }

    public function mostrarDepositosLocalizacion($fk_despacho)
    {
        $depositoLocalizacion = localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->select('inv_localizaciones.id_localizacion','inv_localizaciones.descripcion as localizacion','inv_depositos.fk_despacho',
        'inv_localizaciones.estado','inv_depositos.descripcion as deposito')
        ->orWhere('inv_localizaciones.estado','P')
        ->where('inv_depositos.fk_despacho',[$fk_despacho])
        ->first();
       
        return response()->json([ 
            "ok" =>true,
            "data" =>$depositoLocalizacion
        ]);
    }


    public function editarLocalizacion(StoreRequestEditarLocalizacion $request)
    {
        //
        $id_localizacion          = $request->input('id_localizacion');
        $data['descripcion']      =  strtoupper( $request->input('localizacion'));
        $data['estado']           = strtoupper($request->input('estado'));
        $data['usuario_modifica'] = strtoupper($request->input('usuario'));
        $mostrarLocalizacion = Localizaciones:: 
        select('id_localizacion','descripcion')
        ->where('descripcion', $data['descripcion'] )
        ->where('id_localizacion', '<>', $id_localizacion)
        ->count();

        if ($mostrarLocalizacion) {
            return response()->json([ 
                "ok"    => false,
                "existe" => 'Ya existe esta localización.'
            ]);
        }
        try {
            DB::beginTransaction();
            $editarLocalizacion   = Localizaciones::where('id_localizacion', $id_localizacion)->update($data);
            DB::commit();
            return response()->json([ 
                "ok"   => true,
                "data" => $editarLocalizacion,
                "modificado" => 'Se guardo satisfacoriamente'
            ]);
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([ 
                "ok"    => false,
                "data"  => $th->getMessage(),
                "errorModifica" => 'Hubo un error consulte con el Administrador del sistema.'
            ]);
        }
    }


     public function traerLocalizacionesPederteminadas ($fk_deposito) { 
        $localizacion = Localizaciones::
        select('inv_localizaciones.id_localizacion','inv_localizaciones.fk_deposito','inv_localizaciones.descripcion','inv_localizaciones.estado',
        'inv_localizaciones.usuario_crea','inv_localizaciones.usuario_modifica')
        ->where('inv_localizaciones.fk_deposito', $fk_deposito)
        ->where('inv_localizaciones.estado', 'A')
        ->first();
        return response()->json([ 
            "data" => true,
            "data" =>$localizacion
        ]);
    }

    public function mostrarLocalizacionPrederteminada() {
        $localizacion = Localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('exp.despacho.codigo','inv_localizaciones.id_localizacion','exp.despacho.descripcion as despacho')
        ->where('inv_localizaciones.estado','P')
        ->get();
        return response()->json([ 
            "data" => true,
            "data" =>$localizacion
        ]);
    }

    public function mostrarDepositosCajaMenuda($id_localizacion) { 
        $localizaciones =  localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->select('inv_localizaciones.id_localizacion','inv_localizaciones.fk_deposito','inv_depositos.descripcion as deposito')
        ->where('inv_localizaciones.id_localizacion', $id_localizacion)
        ->first();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }

    public function mostrar_localizaciones_tipo_deposito_inventario($codigo_despacho) { 
        $localizaciones = localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion','inv_depositos.id_deposito','inv_depositos.descripcion as deposito', 
        'inv_localizaciones.descripcion as localizacion','exp.despacho.codigo','exp.despacho.descripcion','inv_localizaciones.estado')
        ->where('inv_localizaciones.estado', '<>', 'I')
        ->where('inv_depositos.inventario', 'INV')
        ->where('exp.despacho.codigo', $codigo_despacho)
        ->orderBy('inv_localizaciones.estado','desc')
        ->get();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }

    public function mostrarLocalizacionesDepositoInventario($codigo_despacho) { 
        $localizaciones = localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion','inv_depositos.id_deposito','inv_depositos.descripcion as deposito', 
        'inv_localizaciones.descripcion as localizacion','exp.despacho.codigo','exp.despacho.descripcion','inv_localizaciones.estado')
        ->where('inv_localizaciones.estado', '<>', 'I')
        ->where('inv_depositos.inventario', 'INV')
        ->where('exp.despacho.codigo', $codigo_despacho)
        ->orderBy('inv_localizaciones.estado','desc')
        ->get();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }


    public function mostrarLocalizacionesMovimientos($codigo_despacho) {
        $localizaciones = localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion','inv_localizaciones.descripcion as localizacion','exp.despacho.descripcion as despacho_destino')
        ->where('inv_localizaciones.estado', '<>', 'I')
        ->where('inv_depositos.inventario', '<>', 'DES')
        ->where('exp.despacho.codigo', $codigo_despacho)
        ->get();
        return response()->json([
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }


    public function mostrarDepositoMovimiento($id_localizacion) {
        $localizaciones = Localizaciones::
        join('inv_depositos','inv_localizaciones.fk_deposito','inv_depositos.id_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion','inv_localizaciones.fk_deposito','inv_depositos.descripcion as deposito',
        'exp.despacho.descripcion as despacho_destino','inv_localizaciones.descripcion as localizacion')
        ->where('inv_localizaciones.id_localizacion', $id_localizacion)
        ->first();
        return response()->json([
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }



    public function mostrarLocalizacionesCajaMenuda($codigo_despacho) { 
        $localizaciones = localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion','inv_depositos.id_deposito','inv_depositos.descripcion as deposito', 
        'inv_localizaciones.descripcion as localizacion','exp.despacho.codigo','exp.despacho.descripcion','inv_localizaciones.estado')
        ->where('inv_localizaciones.estado','<>','I')
        ->where('inv_depositos.inventario','INV')
        ->where('exp.despacho.codigo', $codigo_despacho)
        ->orderBy('inv_localizaciones.estado','desc')
        ->get();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }

    public function mostrarLocalizacionesTipoDespacho($codigo_despacho) { 
        $localizaciones = localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion','inv_depositos.id_deposito','inv_depositos.descripcion as deposito', 
        'inv_localizaciones.descripcion as localizacion','exp.despacho.codigo','exp.despacho.descripcion','inv_localizaciones.estado','inv_depositos.inventario as tipo_deposito')
        ->where('inv_localizaciones.estado','<>','I')
        ->where('inv_depositos.inventario','DEP')
        ->where('exp.despacho.codigo', $codigo_despacho)
        ->orderBy('inv_localizaciones.estado','desc')
        ->get();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }

    public function mostrarLocalizacionesTipoInventario($codigo_despacho) { 
        $localizaciones = localizaciones::
        join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
        ->join('exp.despacho','exp.despacho.codigo','inv_depositos.fk_despacho')
        ->select('inv_localizaciones.id_localizacion','inv_depositos.id_deposito','inv_depositos.descripcion as deposito', 
        'inv_localizaciones.descripcion as localizacion','exp.despacho.codigo','exp.despacho.descripcion','inv_localizaciones.estado','inv_depositos.inventario as tipo_deposito')
        ->where('inv_localizaciones.estado','<>','I')
        ->where('inv_depositos.inventario','INV')
        ->where('exp.despacho.codigo', $codigo_despacho)
        ->orderBy('inv_localizaciones.estado','desc')
        ->get();
        return response()->json([ 
            "ok" =>true,
            "data" =>$localizaciones
        ]);
    }


    public function cambiarPredeterminado (CambiarPredeterminadoRequest $request) { 
        $id_localizacion = $request->input('id_localizacion');
        $estado = 'P';
        $fk_deposito = $request->input('fk_deposito');
        $predeterminado['usuario_modifica'] = strtoupper($request->input('usuario_modifica'));
        $predeterminado['estado'] = 'P';
        $verificar = Localizaciones:: 
        where('estado', $estado)
        ->where('fk_deposito',$fk_deposito)
        ->count();
        if ($verificar) {
     
          return [
            "errorVerificar" => false,
            "verificar" => 'Ya existe una localización predeterminada '
          ];
        }
        try {
            DB::beginTransaction();
            $cambiarPreterminado = Localizaciones::where('id_localizacion',$id_localizacion)->update($predeterminado);
            DB::commit();
            return response()->json([ 
                "ok" =>true,
                "data" => $cambiarPreterminado,
                "predeterminado" => 'Se guardo satisfactoriamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([ 
                "ok" =>false,
                "data" =>$e->getMessage(),
                "errorPredeterminado" => 'Hubo un error consulte con el Administrador del sistema.'
            ]);
        }
    }

    public function cambiarInactivo (CambiarInactivoRequest $request) { 
        DB::beginTransaction();
        $id_localizacion = $request->input('id_localizacion');
        $inactivo['estado'] = 'I';
        $inactivo['usuario_modifica'] = strtoupper($request->input('usuario'));
        try {
            $localizacionInactivo = Localizaciones::where('id_localizacion',$id_localizacion)->update($inactivo);
            DB::commit();
            return response()->json([ 
                "ok" =>true,
                "data" =>$localizacionInactivo,
                "inactivo" => 'Se guardo satisfactoriamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([ 
                "ok" =>false,
                "data" =>$e->getMessage(),
                "errorInactivo" => 'Hubo un error consulte con el Administrador del sistema.'
            ]);
        }
    }

    public function cambiarActivo (CambiarActivoRequest $request) { 
        DB::beginTransaction();
        $id_localizacion = $request->input('id_localizacion');
        $activo['estado'] = 'A';
        $activo['usuario_modifica'] = strtoupper($request->input('usuario'));
        try {
            $localizacionActivo = Localizaciones::where('id_localizacion',$id_localizacion)->update($activo);
            DB::commit();
            return response()->json([ 
                "ok" =>true,
                "data" =>$localizacionActivo,
                "activo" => 'Se guardo satisfactoriamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([ 
                "ok" =>false,
                "data" =>$e->getMessage(),
                "errorActivo" => 'Hubo un error consulte con el Administrador del sistema.'
            ]);
        }
    }

    public function verificarExisteLocalizacionPredeterminada ($fk_despacho) { 
      $localizacion = Localizaciones::
      join('inv_depositos','inv_depositos.id_deposito','inv_localizaciones.fk_deposito')
      ->select('inv_localizaciones.id_localizacion','inv_localizaciones.fk_deposito','inv_localizaciones.estado')
      ->where('inv_localizaciones.estado','P')
      ->where('inv_depositos.fk_despacho', $fk_despacho)
      ->count();
      return response()->json([ 
        "ok" =>true,
        "data" =>$localizacion,
    ]);
    }



}
