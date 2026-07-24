<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnidadMedida;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EditarUnidadMedidaRequest;
use App\Http\Requests\StoreUnidadMedidaRequest;
class UnidadMedidaController extends Controller
{
   
    public function index()
    {
        //Mostrar las unidades de medidas
        $unidadMedida  =  UnidadMedida::select('id_unidad_medida','descripcion as medida', 'factor_unidad')
        ->get();
        return response () ->json([
            "ok"    => true,
            "data"  => $unidadMedida
        ]);
    }

    

    public function store(StoreUnidadMedidaRequest $request)
    {
        try {

            DB::beginTransaction();
            $descripcion   = strtoupper($request ->input('medida'));
            $factor_unidad = $request ->input('factor_unidad');
            $usuario       = strtoupper($request->input('usuario'));
            $validar       = UnidadMedida::where('descripcion', $descripcion)->count();

            if ($validar) {
                return [
                    "ok"    => false,
                    "validar" => 'Ya existe la unidad de medida ' .$descripcion
                ];
            } else {

                $listadoUnidad =  new UnidadMedida;
                $listadoUnidad->descripcion  = $descripcion;
                $listadoUnidad->factor_unidad = $factor_unidad;
                $listadoUnidad->usuario_crea  = $usuario;
                $listadoUnidad->save();
                DB::commit();
                return response () ->json([
                    "ok"    => true,
                    "data"  => $listadoUnidad,
                    "aprovado" => 'Se guardo satisfactoriamente'
                ]);

            }
        } catch (\Exception $th) {
            DB::rollBack();
            return response ()->json([
                "ok"    =>  false,
                "data"  =>  $th->getMessage(),
            ]);
        }
    }

    public function editarUnidadMedida(EditarUnidadMedidaRequest $request)
    {
        $id_unidad_medida         = $request->input('id_unidad_medida');
        $data['descripcion']      = strtoupper($request->input('medida'));
        $data['factor_unidad']    = strtoupper($request->input('factor_unidad'));
        $data['usuario_modifica'] = strtoupper($request->input('usuario'));

        
        try {
            DB::beginTransaction();
            $unidadMedida = UnidadMedida::where('id_unidad_medida', $id_unidad_medida)
                        ->where(function($q){
                            $q->doesnthave('articulos.detalleCompras');

                        })->first();

            if($unidadMedida){
                $unidadMedida->update($data);
                DB::commit();
                return response()->json([ 
                    "ok"    => true,
                    "data"  => $unidadMedida,
                    "modificado"    => 'Se guardo satisfactoriamente'
                ]);
            }

            return response()->json([ 
                "ok" => false,
                "data"  => $unidadMedida,
                "mensajeError" =>'No puede modificar está unidad de medida porque existe en registros relacionados.'
            ]);

            
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([ 
                "ok"    => false,
                "data"  => $ex->getMessage(),
                "errorMensaje" => 'Ya existe está unidad de medida'
            ]);
            
        }
    }

}
