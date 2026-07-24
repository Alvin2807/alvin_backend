<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubGrupos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreSubGruposRequest;
use App\Http\Requests\EditarSubGrupoRequest;
class SubGruposController extends Controller
{
    
    public function index()
    {
        //mostrar subgrupos
        $subGrupos = subGrupos::
        join('inv_grupos','inv_grupos.id_grupo','inv_sub_grupos.fk_grupo')
       ->select('inv_sub_grupos.id_sub_grupo', 'inv_sub_grupos.fk_grupo', 'inv_sub_grupos.descripcion as subgrupo','inv_grupos.descripcion as grupo')
       ->get();
       return response ()->json([
           "ok"    => true,
           "data"  => $subGrupos
       ]);
    }

    
    public function store(StoreSubGruposRequest $request)
    {
        try {
            DB::beginTransaction();
            $fk_grupo    = $request->input('fk_grupo');
            $usuario     =  strtoupper($request->input('usuario'));
            $item = $request->input('subGrupos');
 
            for ($i=0; $i <count($item) ; $i++) { 
                $subGrupo = strtoupper($item[$i]['descripcion']);
                $validar     = subGrupos::where('descripcion', $subGrupo)->count();
                if ($validar) {
                 return [
                  "ok"    => false,
                  "validarSubGrupo"   => 'Ya existe un subgrupo ' .$subGrupo
                 ];
             }  
            }
 
            for ($i=0; $i <count($item) ; $i++) { 
             $registraSubGrupos = new subGrupos();
             $registraSubGrupos->descripcion = strtoupper($item[$i]['descripcion']);
             $registraSubGrupos->fk_grupo = $fk_grupo;
             $registraSubGrupos->usuario_crea = $usuario;
             $registraSubGrupos->save();
            }
                DB::commit();
                return response ()->json([
                 "ok"    => true,
                 "data"  => $registraSubGrupos,
                 "registroExistoso"  => 'Se guardo satisfactoriamente'
                ]);
 
            
         } catch (\Exception $ex) {
             DB::rollBack();
             return response ()->json([
                 "ok"    => true,
                 "data"  =>$ex->getMessage(),
             ]);
             
         }
    }

    public function mostrarSubgrupos($id_grupo)
    {
        $selectSubGrupos = SubGrupos::select('id_sub_grupo', 'fk_grupo', 'inv_sub_grupos.descripcion as subgrupo')
        ->where('inv_sub_grupos.fk_grupo', [$id_grupo])
        ->orderBy('subgrupo', 'asc')
        ->get();
        return response () ->json([
            "ok"    =>true,
            "data"  =>$selectSubGrupos
        ]);
    }

    public function editarSubgrupos(EditarSubGrupoRequest $request)
    {
        $id_sub_grupo = $request->input('id_sub_grupo');
        $data['descripcion'] = strtoupper($request->input('subgrupo'));
        $data['usuario_modifica'] = strtoupper($request->input('usuario'));
        $validar = subGrupos::where('descripcion', $data['descripcion'])->count();

        if ($validar) {
            return response()->json([ 
                "ok"    => false,
                "error" => 'Ya existe un subgrupo ' .$data['descripcion']
            ]);
        }

        try {
            DB::beginTransaction();
            $modificarSubGrupos = subGrupos::where('id_sub_grupo', $id_sub_grupo)
                                ->where(function($q){
                                    $q->doesnthave('articulos.detalleCompras');

                                })->first();

            if($modificarSubGrupos){
                $modificarSubGrupos->update($data);
                DB::commit();
                return response()->json([ 
                    "ok" => true,
                    "data"  => $modificarSubGrupos,
                    "modificado"    => 'Se suardo satisfactoriamente'
                ]);
            }
             
           
            return response()->json([ 
                "ok" => false,
                "data"  => "No puede modificar la marca porque existen registros relacionados.",
            ]);
            
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([ 
                "ok"    => false,
                "data"  =>$ex->getMessage(),
                "errorMensaje"  => 'On no, hubo un error en el sistema.'
            ]);
        }
       
    }

   
}
