<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProveedoresRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\DesactivarProveedorRequest;

class ProveedoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //mostrar proveedores
        $proveedores = Proveedor:: select('id_proveedor', 'codigo_proveedor','nombre', 'ruc', 'dv', 'direccion','telefono1','telefono2','celular',
        'fax','apartado','email','pagina_web','contacto','estado', 'usuario_crea', 'fecha_crea', 'usuario_modifica', 'fecha_modifica', 'fecha_ult_compra')
        ->get();
        return response () ->json([ 
            "ok"    => true,
            "data"  => $proveedores
        ]);
    }

    public function traerProveedor() { 
        $proveedores = Proveedor:: select('id_proveedor', 'codigo_proveedor','nombre', 'ruc', 'dv', 'direccion','telefono1','telefono2','celular',
        'fax','apartado','email','pagina_web','contacto','estado', 'usuario_crea', 'fecha_crea', 'usuario_modifica', 'fecha_modifica', 'fecha_ult_compra')
        ->where('estado','<>','I')
        ->get();
        return response () ->json([ 
            "ok"    => true,
            "data"  => $proveedores
        ]);

    }

   
   
    public function store(StoreProveedoresRequest $request)
    {
        //registrar proveedores
        try {
            DB::beginTransaction();
            $codigo_proveedor = strtoupper($request->input('codigo_proveedor'));
            $consulta = Proveedor::
            select('id_proveedor', 'codigo_proveedor')
            ->where('codigo_proveedor',  $codigo_proveedor)
            ->get();
            if (count($consulta) > 0) {
                return response ()->json([
                    "ok"  => false,
                    "error"    => 'Ya existe un proveedor con el codigo '.$codigo_proveedor
                ]);
            } else {
                $proveedores = new Proveedor();
                $proveedores->codigo_proveedor  = $codigo_proveedor;
                $proveedores->nombre            = strtoupper($request->input('nombre'));
                $proveedores->ruc               = strtoupper($request->input('ruc'));
                $proveedores->dv                = strtoupper($request->input('dv'));
                $proveedores->direccion         = strtoupper($request->input('direccion'));
                $proveedores->telefono1         = strtoupper($request->input('telefono1'));
                $proveedores->telefono2         = strtoupper($request->input('telefono2'));
                $proveedores->celular           = strtoupper($request->input('celular'));
                $proveedores->fax               = strtoupper($request->input('fax'));
                $proveedores->apartado          = strtoupper($request->input('apartado'));
                $proveedores->email             = strtolower($request->input('email'));
                $proveedores->pagina_web        = strtolower($request->input('pagina_web'));
                $proveedores->contacto          = strtoupper($request->input('contacto'));
                $proveedores->estado            = strtoupper('a');
                $proveedores->usuario_crea      = strtoupper($request->input('usuario'));
                $proveedores->save();

                DB::commit();
                return response()->json([
                    "ok"   => true,
                    "data" =>$proveedores,
                    "mensaje" => 'Se ha guardado satisfactoriamente'
                ]);

            }

           
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"   => false,
                "data" =>$ex->getMessage(),
                "error" => 'Hubo un error con el registro consulte con el administrador del sistema.'
            ]);
        }
    }

   
    public function verificarProveedorEnCompra($id_proveedor)
    {
        //verifica si el proveedor tiene orden de compra pendiente
        $proveedores = Proveedor::
        join('inv_solicitud_compras', 'inv_solicitud_compras.fk_proveedor', 'inv_proveedores.id_proveedor')
        ->select('inv_proveedores.id_proveedor','inv_solicitud_compras.id_solicitud')
        ->where('inv_solicitud_compras.fk_proveedor', $id_proveedor)
        ->get();
        if (count($proveedores) > 0) {
            return response ()->json([
                "ok"  => false,
                "error"    => 'No se puede modificar este proveedor'
            ]);
        } 
    }

    public function verificarProveedorParaDesactivar($id_proveedor)
    {
        //verifica si el proveedor tiene orden de compra pendiente
        $proveedores = Proveedor::
        join('inv_solicitud_compras', 'inv_solicitud_compras.fk_proveedor', 'inv_proveedores.id_proveedor')
        ->select('inv_proveedores.id_proveedor','inv_solicitud_compras.id_solicitud')
        ->where('inv_solicitud_compras.estado','P')
        ->where('inv_solicitud_compras.fk_proveedor', $id_proveedor)
        ->get();
        if (count($proveedores) > 0) {
            return response ()->json([
                "ok"  => false,
                "error"    => 'No se puede desactivar este proveedor'
            ]);
        } 
    }

   
    public function editarProveedor(StoreProveedoresRequest $request)
    {
        //editar proveedor
        try {
            DB::beginTransaction();
            $codigo_proveedor = strtoupper($request->input('codigo_proveedor'));
            $id_proveedor     = $request->input('id_proveedor');
            $consulta = Proveedor::
            select('id_proveedor', 'codigo_proveedor')
            ->where('codigo_proveedor',  $codigo_proveedor)->where('id_proveedor', '<>', $id_proveedor)
            ->get();
            if (count($consulta) > 0) {
                return response ()->json([
                    "ok"  => false,
                    "error"    => 'Ya existe un proveedor con el codigo '.$codigo_proveedor
                ]);
            } else {
                $proveedores = new Proveedor();
                $data['nombre']           = strtoupper($request->input('nombre'));
                $data['ruc']              = strtoupper($request->input('ruc'));
                $data['dv']               = strtoupper($request->input('dv'));
                $data['direccion']        = strtoupper($request->input('direccion'));
                $data['telefono1']        = strtoupper($request->input('telefono1'));
                $data['telefono2']        = strtoupper($request->input('telefono2'));
                $data['celular']          = strtoupper($request->input('celular'));
                $data['fax']              = strtoupper($request->input('fax'));
                $data['apartado']         = strtoupper($request->input('apatado'));
                $data['email']            = strtolower($request->input('email'));
                $data['pagina_web']       = strtolower($request->input('pagina_web'));
                $data['contacto']         = strtoupper($request->input('contacto'));
                $data['usuario_modifica'] = strtoupper($request->input('usuario'));
                $proveedores  = Proveedor::where('id_proveedor', $id_proveedor)->update($data);
                DB::commit();
                return response ()->json([
                    "ok"   => true,
                    "data" =>$proveedores,
                    "mensaje" => 'Se guardo satisfactoriamente'
                ]);

            }
        } catch (\Exception $ex) {
            DB::rollBack();
            return response ()->json([
                "ok"   => false,
                "data" =>$ex->getMessage(),
                "errorModificar" => 'Hubo un error consulte con el admiistrador del sistema.'
            ]);
        }
    }

    public function desactivarProveedor (DesactivarProveedorRequest $request){
        try {
            DB::beginTransaction();
            $id_proveedor = $request->input('id_proveedor');
            $consulta = Proveedor::
            select('id_proveedor', 'nombre', 'estado')
            ->where('id_proveedor', $id_proveedor)
            ->where('estado', 'I')
            ->get();
            if (count($consulta) > 0) {
                return response()->json([
                    "ok"  => false,
                    "errorInactivo" =>'Este proveedor ya esta inactivo',
                ]);
            } else {
                $proveedores    = new Proveedor();
                $id_proveedor   = $request->input('id_proveedor');
                $data['estado'] = 'I';
                $data['usuario_modifica'] = strtoupper($request->input('usuario'));
                $proveedores = Proveedor::where('id_proveedor', $id_proveedor)->update($data);
                DB::commit();
                return response()->json([
                    "ok"   => true,
                    "data" =>$proveedores,
                    "mensajeDesactivar" => 'Se desactivo satisfactoriamente'
                ]);
            }

        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"   => false,
                "data" =>$ex->getMessage(),
                "errorDesactivar" => 'Hubo un error consulte con el administrador del sistema.'
            ]);
        }
    }

    public function activarProveedor (DesactivarProveedorRequest $request){
        try {
            DB::beginTransaction();
            $id_proveedor = $request->input('id_proveedor');
            $consulta = Proveedor::
            select('id_proveedor', 'nombre', 'estado')
            ->where('id_proveedor', $id_proveedor)
            ->where('estado', 'A')
            ->get();
            if (count($consulta) > 0) {
                return response()->json([
                    "ok"  => false,
                    "errorActivo" =>'Este proveedor ya esta activo',
                ]);
            } else {
                $proveedores    = new Proveedor();
                $id_proveedor   = $request->input('id_proveedor');
                $data['estado'] = 'A';
                $data['usuario_modifica'] = strtoupper($request->input('usuario'));
                $proveedores = Proveedor::where('id_proveedor', $id_proveedor)->update($data);
                DB::commit();
                return response()->json([
                    "ok"   => true,
                    "data" =>$proveedores,
                    "mensajeActivar" => 'Se activo satisfactoriamente'
                ]);
            }

        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"   => false,
                "data" =>$ex->getMessage(),
                "errorActivar" => 'Hubo un error consulte con el administrador del sistema.'
            ]);
        }
    }

    
}
