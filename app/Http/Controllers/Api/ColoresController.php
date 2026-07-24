<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Colores;
use Illuminate\Http\Request;
use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\EditarRequestColores;
use App\Models\DetallesArticulos;
use Illuminate\Support\Facades\DB;
class ColoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //mostrar lista de Colores
        $colores = Colores::
        select('id_color','descripcion')
        ->get();
        return response()->json([
            "ok"    => true,
            "data"  =>$colores
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mostrarColorExiste(Request $request)
    {
        //Verificar si el color existe al editar
        $color    = $request->input('color');
        $id_color = $request->input('id_color');
        $colores = Colores::
        select('id_color','descripcion')
        ->where('descripcion', $color)->where('id_color','<>', $id_color)->count();
        return response()->json([
            "ok"   => true,
            "data" =>$colores
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreColorRequest $request)
    {
        //Registrar color
        try {
            DB::beginTransaction();
            $descripcion = strtoupper($request->input('descripcion'));
            $usuario     = strtoupper($request->input('usuario'));
            $validar     = Colores::where('descripcion', $descripcion)->count();
 
            if ($validar) {
               return [
                   "ok"      => false,
                   "mensaje" => 'Ya existe el color ' .$descripcion
               ];
            } else { 
                $registrarColores = new Colores;
                $registrarColores->descripcion  = $descripcion;
                $registrarColores->usuario_crea = $usuario;
                $registrarColores->save();

                DB::commit();
                return response()->json([
                 "ok"        => true,
                 "data"      => $registrarColores,
                 "aprobado"  => 'Se guardo satisfactoriamente'
                ]);
            }
 
         } catch (\Exception $ex) {
             DB::rollBack();
             return response()->json([ 
                 "ok"    => false,
                 "data"  =>$ex->getMessage(),
                 "error" => 'Hubo un error consulte con el administrador del sistema.'
             ]);
         }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Colores  $colores
     * @return \Illuminate\Http\Response
     */
    public function verificarColorExisteDetalleArticulo($id_color)
    {
        //Verificar si existe el color en detalle de articulo
        $colores = DetallesArticulos::
        join('inv_colores','inv_colores.id_color','inv_detalle_articulo.fk_color')
        ->select('inv_detalle_articulo.fk_color')
        ->where('inv_colores.id_color', $id_color)
        ->count();
        return response()->json([
            "ok" =>true,
            "data" =>$colores
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Colores  $colores
     * @return \Illuminate\Http\Response
     */
    public function editarColores(EditarRequestColores $request)
    {
        //Editar color
        $id_color = $request->input('id_color');
        $data['descripcion']      = strtoupper($request->input('color'));
        $data['usuario_modifica'] = strtoupper($request->input('usuario'));
        $verifique = Colores::where('descripcion',  $data['descripcion'])->count();
        if ($verifique > 0) {
            return response()->json([
                "ok" =>false,
                "mensajeExiste" => 'Ya existe el color ' .$data['descripcion']
            ]);
        } else {
            try {
                DB::beginTransaction();
                $editarColor = Colores::where('id_color', $id_color)->update($data);
                DB::commit();
                return response()->json([ 
                    "ok"   =>true,
                    "data" => $editarColor,
                    "modificado" => 'Se guardo satisfactoriamente'
                ]);
            } catch (\Exception $th) {
                DB::rollBack();
               return response()->json([ 
                   "ok"   => false,
                   "data" =>$th->getMessage(),
                   "errorEditar" => 'Hubo un error consulte con el administrador del sistema.'
               ]);
            }
        }

    }

}
