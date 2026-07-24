<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Articulos;
use App\Models\VistaArticulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RegistrarArticulosRequest;
use App\Http\Requests\EditarArticulosRequest;
use App\Models\DetallesCompras;

class ArticulosController extends Controller
{
    
    public function index()
    {
        //mostrar articulos
        $articulos = VistaArticulo::all();
        return response()->json([
            'ok'    => true,
            'data'  => $articulos
        ]);
    }

    
    public function create()
    {
        //
    }


    public function store(RegistrarArticulosRequest $request)
    {
       try {
            DB::beginTransaction();
            //registrar articulos
            $codigo_articulo = $request->input('codigo');
            $consulta = Articulos::
            select('id_articulo','codigo')
            ->where('codigo', $codigo_articulo)
            ->get();
            if (count($consulta) > 0) {
            return response ()->json([
                "ok" => false,
                "errorCodigo" => 'Ya existe un artículo con este código'
            ]);
            } else {
                $articulos = new Articulos();
                $articulos->codigo           = strtoupper($request->input('codigo'));
                $articulos->descripcion      = strtoupper($request->input('descripcion'));
                $articulos->fk_marca         = $request->input('fk_marca');
                $articulos->fk_modelo        = $request->input('fk_modelo');
                $articulos->fk_categoria     = $request->input('fk_categoria');
                $articulos->fk_grupo         = $request->input('fk_grupo');
                $articulos->fk_sub_grupo     = $request->input('fk_sub_grupo');
                $articulos->fk_tipo_uso      = $request->input('fk_tipo_uso');
                $articulos->fk_unidad_medida = $request->input('fk_unidad_medida');
                $articulos->cantidad_pedida  = 0;
                $articulos->cantidad_almacen = 0;
                $articulos->cantidad_stock   = 0;
                $articulos->garantia          = $request->input('garantia');
                $articulos->periodo_grantia = strtoupper($request->input('>periodo_garantia'));
                $articulos->requiere_activo  = strtoupper($request->input('requiere_activo'));
                $articulos->usuario_crea     = strtoupper($request->input('usuario'));
                $articulos->save();

                DB::commit();
                return response()->json([
                    "ok"       => true,
                    "data"     => $articulos,
                    "mensaje" => 'Se guardo satisfactoriamente'
                ]);
            }

       } catch (\Exception $ex) {
           DB::rollBack();
           return response()->json([
            "ok"    => false,
            "data"  => $ex->getMessage(),
            "errorRegistro" => 'Hubo un error consulte con el administrado del sistema'
        ]);
       }
    }

   
    public function buscarArticuloEnCompraEditar($id_articulo)
    {
        //Verifica si el artículo está en una orden de compra para no modicarlo, en caso contrario se modifica
           $consulta = DetallesCompras::
           select('fk_articulo')
           ->where('fk_articulo', $id_articulo)
           ->get();
           if (count($consulta) > 0) {
             return response()->json([
                "ok" => true,
                "mensajeVerificar" => 'No se puede modificar este artículo' 
             ]);
           } else {

           }
    }

    public function editarArticulos(EditarArticulosRequest $request)
    {
      
        try {
           DB::beginTransaction();
             //editar artículos
            strtoupper( $codigo_articulo = $request->input('codigo'));
            $id_articulo = $request->input('id_articulo');
            $consulta = Articulos::
            select('id_articulo','codigo')
            ->where('codigo', $codigo_articulo)->where('id_articulo', '<>', $id_articulo)
            ->get();
            if (count($consulta) > 0) {
                return response ()->json([
                    "ok" => false,
                    "errorCodigo" => 'Ya existe un artículo con este código'
                ]);
            } else {
                $articulos = new Articulos();
                $periodo_garantia = $request->input('periodo_grantia');
                if (isset($periodo_garantia) == 'Años') {
                    $periodo = 'A';
                } else if (isset($periodo_garantia) == 'Días') {
                    $periodo = 'D';
                } else if (isset($periodo_garantia) == 'Meses') {
                    $periodo = 'M';
                }
                $data['codigo']            = $codigo_articulo;
                $data['descripcion']       = strtoupper($request->input('descripcion'));
                $data['fk_marca']          = $request->input('fk_marca');
                $data['fk_modelo']         = $request->input('fk_modelo');
                $data['fk_categoria']      = $request->input('fk_categoria');
                $data['fk_grupo']          = $request->input('fk_grupo');
                $data['fk_sub_grupo']      = $request->input('fk_sub_grupo');
                $data['fk_tipo_uso']       = $request->input('fk_tipo_uso');
                $data['fk_unidad_medida']  = $request->input('fk_unidad_medida');
                $data['cantidad_minima']   = $request->input('cantidad_minima');
                $data['garantia']          = $request->input('garantia');
                $data['periodo_grantia']   = $periodo;
                $data['requiere_activo']   = strtoupper($request->input('requiere_activo'));
                $articulos = Articulos::where('id_articulo', $id_articulo)->update($data);
                DB::commit();

                return response()->json([
                    "ok"   => true,
                    "data" => $articulos,
                    "mensajeEdicion" => 'Se guardo satisfactoriamente'
                ]);
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                "ok"   => false,
                "data" => $ex->getMessage(),
                "errorEdicion" => 'Hubo un error consulte con el administrador del sistema'
            ]);
        }
    }



    public function UnidadesEnAlmacen()
    {
        //muestra la cantidad de unidades de articulos en almacen por retirar
        $articulos = VistaArticulo::
        select('id_articulo','cantidad_almacen')
        ->where('cantidad_almacen','>', 0)
        ->sum('cantidad_almacen');
        return response()->json([
            "ok"   => true,
            "data" => $articulos,
        ]);

    }
}
