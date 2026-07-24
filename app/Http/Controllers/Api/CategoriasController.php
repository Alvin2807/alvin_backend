<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCategoriaRequest;
use App\Models\Articulos;
use App\Http\Requests\EditarCategoriaRequest;

class CategoriasController extends Controller
{
    
    public function index()
    {
        //mostrar categorias
        $categorias = Categorias::
        select('id_categoria','descripcion')
        ->orderBy('id_categoria', 'desc')
        ->get();
        return response()->json([
            "ok"   =>true,
            "data" =>$categorias
        ]);
    }

    public function store(StoreCategoriaRequest $request)
    {
        //registrar categorias
        try {
           DB::beginTransaction();
           $categoria = strtoupper($request->input('descripcion'));
           $consulta  = Categorias::
           select('id_categoria','descripcion')
           ->where('descripcion', $categoria)
           ->get();
           if (count($consulta) > 0) {
              return response()->json([
                "ok"     => true,
                "existe" => 'Ya existe una categoría '.$categoria
              ]);
           } else {
               $categorias = new Categorias();
               $categorias->descripcion = $categoria;
               $categorias->usuario_crea = strtoupper($request->input('usuario'));
               $categorias->save();

               DB::commit();
               return response()->json([
                "ok"       =>true,
                "data"     =>$categorias,
                "aprobado" => 'Se guardo satisfactoriamente'
               ]);
           }
        } catch (\Exception $ex) {
            return response()->json([
                "ok"    =>false,
                "data"  =>$ex->getMessage(),
                "errorRegistro" =>'Hubo un error consulte con el administrador del sistema'
            ]);
        }
    }

    public function verificarExisteCategoriaCompra($id_categoria)
    {
        //verifica si existe la categoria en detalles de compra
        $consulta = Articulos::
        join('inv_detalle_compras','inv_detalle_compras.fk_articulo','inv_articulos.id_articulo')
        ->leftJoin('inv_categorias','inv_categorias.id_categoria','inv_articulos.fk_categoria')
        ->where('inv_categorias.id_categoria', $id_categoria)
        ->get();
        if (count($consulta) > 0) {
           return response()->json([
            "ok" =>true,
            "mensajeExisteCompra" => 'No se puede modificar esta categoría'
           ]);
        }
    }

    public function editarCategoria(EditarCategoriaRequest $request)
    {
        // editar categoria
        try {
            DB::beginTransaction();
           $categoria = strtoupper($request->input('descripcion'));
           $id_categoria = $request->input('id_categoria');
           $consulta  = Categorias::
           select('id_categoria','descripcion')
           ->where('descripcion', $categoria)->where('id_categoria','<>', $id_categoria)
           ->get();
           if (count($consulta) > 0) {
              return response()->json([
                "ok"     => true,
                "existeCategoria" => 'Ya existe una categoría '.$categoria
              ]);
           } else {
               $categorias = new Categorias();
               $data['descripcion']     = $categoria;
               $data['usuario_modifica'] = strtoupper($request->input('usuario'));
               $categorias = Categorias::where('id_categoria', $id_categoria)->update($data);

               DB::commit();
               return response()->json([
                "ok"    =>true,
                "data"  =>$categorias,
                "modificado" => 'Se guardo satisfactoriamente'
               ]);

           }
        } catch (\Exception $ex) {
           DB::rollBack();
           return response()->json([
            "ok" =>false,
            "data" =>$ex->getMessage(),
            "errorModifica" => 'Hubo un error consulte con el administrador del sistema'
           ]);
        }
    }

}
