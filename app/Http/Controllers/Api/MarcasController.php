<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marcas;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMarcasRequest;
use App\Http\Requests\EditarMarcasRequest;
use Illuminate\Support\Facades\DB;
use App\Models\VistaDetalleOrdenCompra;

class MarcasController extends Controller
{
  
    public function index()
    {
        //mostrar marcas
        $marcas = Marcas::
        select('id_marca','nombre_marca')
        ->orderBy('id_marca','desc')
        ->get();
        return response()->json([
            "ok"   =>true,
            "data" =>$marcas
        ]);
    }


    public function store(StoreMarcasRequest $request)
    {
        //registrar marcas
       try {
            DB::beginTransaction();
            $nombre_marca = strtoupper($request->input('nombre_marca'));
            $id_marca     = $request->input('id_marca');
            $consultar = Marcas::
            select('id_marca','nombre_marca')
            ->where('nombre_marca', $nombre_marca)->where('id_marca','<>', $id_marca)->count();
            if ($consultar) {
              return[
                 "ok" => true,
                 "existeMarcaRegistro" => 'Ya existe una marca '.$nombre_marca
              ];

            } else {
                $marcas = new Marcas();
                $marcas->nombre_marca = strtoupper($request->input('nombre_marca'));
                $marcas->usuario_crea = strtoupper($request->input('usuario'));
                $marcas->save();
                DB::commit();
                return response()->json([
                    "ok"   => true,
                    "data" =>$marcas,
                    "mensaje" => 'Se guardo satisfactoriamente'
                ]);
            }
          
       } catch (\Exception $ex) {
           DB::rollBack();
           return response()->json([
            "ok"    => false,
            "data"  =>$ex->getMessage(),
            "errorRegistro" => 'Hubo un error consulte con el administrador del sistema'
           ]);
       }
    }

   
    public function verificarMarcaDetalleCompra($id_marca)
    {
        //verifica si la marca esta en un detallde de compra
       
        $consulta = VistaDetalleOrdenCompra::
        select('fk_marca')
        ->where('fk_marca', $id_marca)
        ->get();
        if (count($consulta)) {
            return response()->json([
                "ok"     => false,
                "existe" => 'No se puede modificar esta marca'
            ]); 
        }


    }

   
    public function editarMarca(EditarMarcasRequest $request)
    {
        //editar marca
        try {
           DB::beginTransaction();
           $nombre_marca = strtoupper($request->input('nombre_marca'));
           $id_marca     = $request->input('id_marca');
           $consultar = Marcas::
           select('id_marca','nombre_marca')
           ->where('nombre_marca', $nombre_marca)->where('id_marca','<>', $id_marca)->count();
           if ($consultar) {
             return[
                "ok" => true,
                "existeMarca" => 'Ya existe una marca '.$nombre_marca
             ];
           } else {
                $marcas = new Marcas();
                $data['nombre_marca']     = $nombre_marca;
                $data['usuario_modifica'] = strtoupper($request->input('usuario'));
                $marcas = Marcas::where('id_marca', $id_marca)->update($data);
                DB::commit();
                return response()->json([
                    "ok"       => true,
                    "data"     =>$marcas,
                    "aprobado" => 'Se guardo satisfactoriamente',
                ]);
           }

          
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"     => false,
                "data"   => $ex->getMessage(),
                "errorModifica" => 'Hubo un error consulte con el administrador del sistema'
              ]);
        }
    }

    
    
        
}
