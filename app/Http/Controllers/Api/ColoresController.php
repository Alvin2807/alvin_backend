<?php

namespace App\Http\Controllers\Api;

use App\Models\Colores;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Colores\StoreColoresRequest;
use Illuminate\Support\Facades\DB;

class ColoresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreColoresRequest $request)
    {
        //Registrar color
        try {
           DB::beginTransaction();
            $colores = new Colores();
            $colores->codigo_color = $request->input('codigo_color');;
            $colores->color = ucfirst($request->input('color'));
            $colores->save();
            DB::commit();

           return response()->json([
            "ok" =>true,
            "data" =>$colores,
            "mensaje" => 'Se guardo satisfactoriamente'
           ]);
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok"    =>false,
                "data"  =>$th->getMessage(),
                "error" => 'Hubo un error consulte con el administrador del sistema'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Colores $colores)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Colores $colores)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Colores $colores)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Colores $colores)
    {
        //
    }
}
