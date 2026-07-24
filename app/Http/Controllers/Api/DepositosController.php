<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestDepositos;
use App\Models\Depositos;
use App\Models\VistaUbicacionArticulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositosController extends Controller
{
    public function index()
    {
        $depositos = Depositos::
        join('exp.despacho', 'exp.despacho.codigo', 'inv_depositos.fk_despacho')
        ->select('inv_depositos.id_deposito', 'inv_depositos.descripcion', 'inv_depositos.fk_despacho', 'exp.despacho.descripcion as despacho', 'exp.despacho.codigo','inv_depositos.inventario as tipo_deposito')
        ->orderBy('inv_depositos.descripcion','asc')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  =>  $depositos
        ]);
    }

   
    public function store(StoreRequestDepositos $request)
    {
        //Registrar depositos

        try {
            DB::beginTransaction();
            $fk_despacho       = strtoupper($request->input('fk_despacho'));
            $descripcion       = strtoupper($request->input('descripcion'));
            $usuario_crea      = strtoupper($request->input('usuario'));
            $tipo_deposito     = strtoupper($request->input('tipo_deposito'));
            $verifiqueRegistro = Depositos::
            where('descripcion', $descripcion)
            ->where('fk_despacho', $fk_despacho)
            ->count();

            if ($verifiqueRegistro) {
                return response()->json([
                   "ok"    => false,
                   "verifiqueRegistro"  => 'Ya existe un deposito ' .$descripcion
                ]);
              }  

              if ($tipo_deposito == 'DEP' ||  $tipo_deposito == 'INV' || $tipo_deposito == 'DES') {
                $depositos = new Depositos;
                $depositos->fk_despacho  = $fk_despacho;
                $depositos->descripcion  = $descripcion;
                $depositos->usuario_crea = $usuario_crea;
                $depositos->inventario   = $tipo_deposito;
                $depositos->save();
              } else {
                  return 'No se reconoce este tipo de deposito.';
              }

              DB::commit();
              return response()->json([ 
               "ok"    => true,
               "data"  => $depositos,
               "aprobado"  => 'Se guardo satisfactoriamente'
              ]);

        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([ 
                "ok"    => false,
                "data"  => $ex->getMessage(),
                "error" => 'Hubo un error con el registro, consulte con el Administrador del sistema'
               ]);
        }
    }

  
    public function MisDepositos($codigo_despacho)
    {
        //Listado de mis depositos
        $depositos = Depositos::
        join('exp.despacho', 'exp.despacho.codigo', 'inv_depositos.fk_despacho')
        ->select('inv_depositos.id_deposito', 'inv_depositos.descripcion', 'inv_depositos.fk_despacho', 'exp.despacho.descripcion as despacho', 'exp.despacho.codigo','inv_depositos.inventario as tipo_deposito')
        ->where('exp.despacho.codigo',$codigo_despacho)
        ->orderBy('inv_depositos.descripcion','asc')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  =>  $depositos
        ]);
    }

    public function depositosOtroDespacho($codigo_despacho)
    {
        //Listado de mis depositos
        $depositos = Depositos::
        join('exp.despacho', 'exp.despacho.codigo', 'inv_depositos.fk_despacho')
        ->select('inv_depositos.id_deposito', 'inv_depositos.descripcion', 'inv_depositos.fk_despacho', 'exp.despacho.descripcion as despacho', 'exp.despacho.codigo','inv_depositos.inventario as tipo_deposito')
        ->where('exp.despacho.codigo', '<>',$codigo_despacho)
        ->orderBy('inv_depositos.descripcion','asc')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  =>  $depositos
        ]);
    }


   
    public function verificarStockDepositoInventario($id_deposito)
    {
        //verififica si el deposito cuenta con stock en el tipo de inventario INV
        $depositos = VistaUbicacionArticulo::
        select('VW_ARTICULO_UBICACION.ID_DEPOSITO')
        ->join('inv_depositos','inv_depositos.id_deposito','VW_ARTICULO_UBICACION.ID_DEPOSITO')
        ->where('vw_articulo_ubicacion.cantidad_stock', '>=', 1)
        ->where('vw_articulo_ubicacion.id_deposito', $id_deposito)
        ->where('inv_depositos.inventario','INV')
        ->get();

        if (count($depositos) > 0) {
            return response()->json([ 
                "ok"    => true,
                "mensaje" => 'No se puede modificar este deposito'
            ]);
        } else {
            return response()->json([ 
                "ok"    => false,
                "preguntar" => '¿Estas seguro de modificar este deposito?'
            ]);
        }
    }

  
    public function editarDepositos(Request $request, Depositos $depositos)
    {
        //Editar deposito

        try {
            DB::beginTransaction();
            $depositos = $request->validate([
                'fk_despacho'     => 'required|integer',
                'descripcion'     =>'required|string|max:200',
                'tipo_deposito'   =>'required|string|max:3'
            ]);
            $id_deposito = $request->input('id_deposito');
            $data['fk_despacho']      = strtoupper($request->input('fk_despacho'));
            $data['descripcion']      = strtoupper($request->input('descripcion'));
            $data['inventario']       = strtoupper($request->input('tipo_deposito'));
            $data['usuario_modifica'] = strtoupper($request->input('usuario'));
            $depositos = Depositos::where('id_deposito', $id_deposito)->update($data);
            DB::commit();
            return response()->json([ 
               "ok"   =>true,
               "data" =>$depositos,
               "aprobadoEditar" => 'Se guardo satisfactoriamente' 
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response ()->json([
                "ok"    =>false,
                "data"  =>$ex->getMessage(),
                "mensajeError"  => 'Hubo un error, consulte con el Administrador del sistema.'
            ]);
        }
    }

  
    public function mostrarDespositosDespachos ($fk_despacho) { 
        $depositos = Depositos::select('inv_depositos.id_deposito','inv_depositos.fk_despacho','inv_depositos.descripcion')
        ->where('inv_depositos.fk_despacho',[$fk_despacho])
        ->get();
        return response()->json([ 
            "ok" =>true,
            "data" => $depositos
        ]);
    }

    

}
