<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Porcentajes;
use Illuminate\Http\Request;
use App\Utils\Utilidades;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EditarPorcentajesrequest;
use App\Http\Requests\StorePorcentajeRequest;
class PorcentajesController extends Controller
{
   
    public function index()
    {
        //mostrar los porcentajes
        $porcentajes = Porcentajes::select('inv_porcentajes.id_porcentaje', 'inv_porcentajes.tipo', 'inv_porcentajes.descripcion', 'inv_porcentajes.porcentaje', 'INV_porcentajes.estatus', 
        'inv_porcentajes.periodo_inicial', 'inv_porcentajes.periodo_final')
        ->where('inv_porcentajes.estatus','A')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  => $porcentajes
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(StorePorcentajeRequest $request)
    {
        try {

            DB::beginTransaction();
            $tipo_porcentaje = strtoupper($request->input('tipo_porcentaje'));
            $descripcion = strtoupper($request->input('descripcion'));
            $porcentaje = strtoupper($request->input('porcentaje'));
            $usuario_crea = strtoupper($request->input('usuario'));
            $periodo_inicial = Utilidades::formatoFecha($request->input('fecha_inicial'));
            $verificar = Porcentajes::where('tipo', $tipo_porcentaje)->count();
            $promPorcentaje = $porcentaje;
 
            $registrarPorcentajes = new Porcentajes;
            $registrarPorcentajes->tipo = $tipo_porcentaje;
            $registrarPorcentajes->porcentaje = $promPorcentaje;
            $registrarPorcentajes->descripcion = $descripcion;
            $registrarPorcentajes->periodo_inicial = Utilidades::formatoFecha($request->input('fecha_inicial'));
            $registrarPorcentajes->periodo_inicial = $periodo_inicial;
            $registrarPorcentajes->usuario_crea = $usuario_crea;
            $registrarPorcentajes->estatus = 'A';
            $registrarPorcentajes->save();

            DB::commit();
            return response()->json([ 
                "ok"    =>true,
                "data"  =>$registrarPorcentajes,
                "aprobado"  => 'Se guardo satisfactoriamente'
            ]);
            
         } catch (\Exception $e) {
             DB::rollBack();
             return response()->json([ 
                 "ok"    =>false,
                 "data"  =>$e->getMessage()
             ]);
         }
    }

   
    public function modificarPorcentaje(EditarPorcentajesrequest $request)
    {
        $id_porcentaje = $request->input('id_porcentaje');
        $data['descripcion'] = strtoupper($request->input('descripcion'));
        $data['tipo'] = strtoupper($request->input('tipo'));
        $data['usuario_modifica'] = strtoupper($request->input('usuario'));
        $data['porcentaje'] = strtoupper($request->input('porcentaje'));
        $data['periodo_inicial'] = Utilidades::formatoFecha($request->input('fecha_inicial_modifica'));

        try {
            DB::beginTransaction();
           $editarPorcentaje = Porcentajes::where('id_porcentaje',$id_porcentaje)->update($data);
           DB::commit();
           return response()->json([ 
               "ok" =>true,
               "data" =>$editarPorcentaje,
               "modificado" => 'Se guardo satisfactoriamente'
           ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([ 
                "ok" =>false,
                "data" =>$e->getMessage(),
                "error" => 'Hubo un error, consulte con el Administrador del sistema.'
            ]);
        }
    }

    public function activarPorcentajes(Request $request) {
        $data = $request->validate([
            'id_porcentaje' => 'required|integer|exists:App\Models\Porcentajes',
        ]);

        try {
            DB::beginTransaction();
            $porcentajeNuevo = DB::transaction(function () use ($data) {
                $porcentajeNuevo = Porcentajes::findOrFail($data['id_porcentaje']);

                $porcentajeNuevo->update(['estatus' => Porcentajes::ACTIVO]);

                $porcentajes = Porcentajes::where('id_porcentaje', '!=', $porcentajeNuevo->id_porcentaje)
                    ->where('estatus', '!=', Porcentajes::INACTIVO)
                    ->where('tipo', $porcentajeNuevo->tipo)
                    ->get();

                foreach($porcentajes as $porcentaje){
                    $porcentaje->update(['estatus' => Porcentajes::INACTIVO, 'periodo_final' => now()]);
                }

                return $porcentajeNuevo;
                
            });
             DB::commit();
            return response()->json([ 
                "ok" => true,
                "data" => $porcentajeNuevo,
                "mensajeActivar" => 'Se activo el porcentaje satisfactoriamente' 
            ]);
           
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([ 
                "ok" => false,
                "data" =>$e->getMessage(),
                "errorMensaje" => 'Hubo un error consulte con el administrador del sistema.'
            ]);
        }
    }

    public function desactivarPorcentajes(Request $request) { 
        $desActivar = $request->validate([
            'id_porcentaje' => 'required|integer|exists:App\Models\Porcentajes',
        ]);
        $id_porcentaje = $request->input('id_porcentaje');
        $desActivarPocentaje['estatus'] = strtoupper($request->input('estado'));
        $desActivarPocentaje['usuario_modifica'] = strtoupper($request->input('usuario'));
        $desActivarPocentaje['periodo_final'] = Utilidades::formatoFecha($request->input('fecha_final'));
        
        try {
            DB::beginTransaction();
           $desActivar = Porcentajes::where('id_porcentaje',$id_porcentaje)->update($desActivarPocentaje);
           DB::commit();
           return response()->json([ 
               "ok" => true,
               "data" =>$desActivar,
               "desaActivar" => 'Se desactivo el porcentaje satisfactoriamente' 
           ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([ 
                "ok" => false,
                "data" =>$e->getMessage(),
                "errorMensajeDesactivar" => 'Hubo un error consulte con el administrador del sistema.'
            ]);
        }
    }

    public function porcentajesActivos () { 
        $itbms = Porcentajes::select('inv_porcentajes.id_porcentaje', 'inv_porcentajes.tipo', 'inv_porcentajes.porcentaje', 'inv_porcentajes.estatus')
        ->where('inv_porcentajes.estatus', 'A')
        ->where('inv_porcentajes.tipo', 'ITBMS')
        ->first();
        return response ()->json([ 
            "ok"    => true,
            "data"  =>$itbms,
        ]);
    }

    

    
}
