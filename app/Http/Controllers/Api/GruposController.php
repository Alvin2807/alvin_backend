<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grupos;
use Illuminate\Http\Request;
use App\Http\Requests\StoreGruposRequest;
use App\Http\Requests\EditarRequestGrupos;
class GruposController extends Controller
{
  
    public function index()
    {
        //mostrar grupos
        $grupos = Grupos::
        select('id_grupo','fk_categoria','descripcion')
        ->orderBy('id_grupo','desc')
        ->get();
        return response()->json([
            "ok"   =>true,
            "data" =>$grupos
        ]);

    }


    public function store(StoreGruposRequest $request)
    {
        try {
            
            $fk_categoria = $request->input('id_categoria');
            $usuario      = strtoupper($request->input('usuario'));
            $item = $request->input('grupos');

            for ($i=0; $i <count($item) ; $i++) { 
                $grupo = strtoupper($item[$i]['descripcion']);
                $validar = Grupos::where('descripcion', $grupo)->count();
                if ($validar) {
                    return [
                     "ok"    => false,
                     "verificarGrupo"   => 'Ya existe el grupo '.$grupo
                    ];
     
                 } 
            }

            for ($i=0; $i <count($item) ; $i++) { 
                $registrarGrupos   = new Grupos;
                $registrarGrupos->descripcion  = strtoupper($item[$i]['descripcion']);
                $registrarGrupos->fk_categoria = $fk_categoria;
                $registrarGrupos->usuario_crea = $usuario;
                $registrarGrupos->save();
            }
           
                return response()->json([
                    "ok"    => true,
                    "data"  => $registrarGrupos,
                    "registraGrupo"  => 'Se guardo satisfactoriamente'
                ]);

            
        } catch (\Exception $ex) {
            return response ()->json([
                "ok"    => false,
                "data"  =>$ex->getMessage(),
            ]);
            
        }
    }

    public function mostrarGrupos($id_categoria)
    {
    
        $selectGrupos  = Grupos::select('inv_grupos.id_grupo','inv_grupos.fk_categoria','inv_grupos.descripcion as grupo')
        ->where('inv_grupos.fk_categoria', [$id_categoria])
        ->orderBy('grupo', 'asc')
        ->get();
        return response () ->json([
            "ok"   => true,
            "data" => $selectGrupos
        ]);
    }


    public function editarGrupos(EditarRequestGrupos $request)
    {
        $id_grupo = $request->input('id_grupo');
        $data['descripcion'] = strtoupper($request->input('descripcion'));
        $data['usuario_modifica'] = strtoupper($request->input('usuario'));
      
        $validar = Grupos::where('descripcion', $data['descripcion'])->count();

        if ($validar) {
            return response()->json([ 
                "ok"    => false,
                "existeGrupo" => 'Ya existe un grupo ' .$data['descripcion']
            ]);
        }

        try {
            $modificarGrupos = Grupos::where('id_grupo', $id_grupo)
                            ->where(function($q){
                                $q->doesnthave('articulos.detalleCompras');

                            })->first();
            
            if($modificarGrupos){
                $modificarGrupos->update($data);

                return response()->json([ 
                    "ok"    => true,
                    "data"  => $modificarGrupos,
                    "modificado"    => 'Se guardo satisfactoriamente'
                ]); 

            }

            return response()->json([ 
                "ok" => false,
                "data"  => "No puede modificar la marca porque existen registros relacionados.",
            ]);
            

        } catch (\Exception $ex) {
            return response()->json([ 
                "ok"    => false,
                "data" => $ex->getMessage(),
                "error" => 'Oh no, hubo un error en el sistema'
            ]);
        }


    }



    
  
    

}
