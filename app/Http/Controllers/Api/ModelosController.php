<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Modelos;
use Illuminate\Http\Request;
use App\Http\Requests\RegistrarModeloRequest;
use App\Http\Requests\EditarRequestModelo;
use App\Models\VistaDetalleOrdenCompra;
use Illuminate\Support\Facades\DB;
class ModelosController extends Controller
{
   
    public function index()
    {
        //mostrar modelos
        $modelos = Modelos::join('inv_marcas','inv_marcas.id_marca', 'inv_modelos.fk_marca')
        ->select('inv_modelos.id_modelo', 'inv_modelos.nombre_modelo', 'inv_modelos.fk_marca','inv_marcas.nombre_marca')
        ->orderBy('id_modelo','desc')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  => $modelos
        ]);
    }

   
    public function store(RegistrarModeloRequest $request)
    {
        //registrar modelos
        try {
            DB::beginTransaction();
            $fk_marca = $request->input('id_marca');
            $item     = $request->input('modelos');
            $usuario  =  strtoupper($request->input('usuario'));

            for ($i=0; $i <count($item) ; $i++) {
                $modelo = strtoupper($item[$i]['nombre_modelo']);
                $validar = Modelos::where('nombre_modelo', $modelo)->count();
                if ($validar) {
                    return [ 
                        "ok" => true,
                        "existeModelo" => 'Ya existe el modelo '.$modelo
                    ];
                } else {
                    for ($i=0; $i <count($item) ; $i++) { 
                        $registraModelos   = new Modelos();
                        $registraModelos->nombre_modelo = strtoupper($item[$i]['nombre_modelo']);
                        $registraModelos ->fk_marca     = $fk_marca;
                        $registraModelos ->usuario_crea = $usuario;
                        $registraModelos ->save();
                    }
        
                    DB::commit();
                    return response ()->json([ 
                        "ok"    => true,
                        "data"  => $registraModelos,
                        "registroModelo"  => 'Se guardo satisfactoriamente'
                    ]);
        
                }
            }

        } catch (\Exception $ex) {
            DB::rollBack();
            return response ()->json([ 
                "ok"    => false,
                "data"  => $ex->getMessage(),
                "errorModelo"  => 'Hubo un error consulte con el administrador del sistema'
            ]);
        }
    }

    public function mostrarModelos($id_marca)
    {
        // Mostrar lista de modelos para las marcas
        $selectModelos = Modelos::select('id_modelo', 'inv_modelos.fk_marca', 'inv_modelos.nombre_modelo')
        ->where('inv_modelos.fk_marca', [$id_marca])
        ->orderBy('inv_modelos.nombre_modelo', 'asc')
        ->get();
        return response () ->json([
            "ok"    =>true,
            "data"  =>$selectModelos
        ]);

    }

   
    public function editarModelo(EditarRequestModelo $request)
    {
        //editar modelo
        try {
            DB::beginTransaction();
            $id_modelo     = $request->input('id_modelo');
            $nombre_modelo = strtoupper($request->input('nombre_modelo'));
            $consulta = Modelos::
            select('id_modelo','nombre_modelo')
            ->where('nombre_modelo', $nombre_modelo)->where('id_modelo','<>', $id_modelo)
            ->get();
            if (count($consulta) > 0) {
               return response()->json([
                "ok" => true,
                "existeModelo" => 'Ya existe un modelo '.$nombre_modelo
               ]);
            } else {
                $modelos = new Modelos();
                $data['nombre_modelo']    = $nombre_modelo;
                $data['usuario_modifica'] = strtoupper($request->input('usuario'));
                $modelos = Modelos::where('id_modelo', $id_modelo)->update($data);
                DB::commit();
                return response()->json([
                    "ok"       =>true,
                    "data"     =>$modelos,
                    "aprobado" => 'Se guardo satisfactoriamente'
                ]);
            }
        
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok" => false,
                "data" =>$ex->getMessage(),
                "errorModifica" => 'Hubo un error consulte con el administrador del sistema.'
            ]);
        }
    
    }

    public function verificarModeloExiteCompra($id_modelo) {
        $consulta = VistaDetalleOrdenCompra::
        select('fk_modelo')
        ->where('fk_modelo', $id_modelo)
        ->get();
        if (count($consulta) > 0) {
           return response()->json([
            "ok" => true,
            "mensaje" => 'No se puede modificar este modelo'
           ]);
        }
    }

}
