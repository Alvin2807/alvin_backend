<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Login;
use Illuminate\Support\Facades\DB;


class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mostrarPerfil =  DB::table('inv_roles_usuario as rol')
        ->join('WEB.usuarios', 'WEB.usuarios.usuario', 'rol.fk_usuario')
        ->join('EXP.despacho','EXP.despacho.codigo', 'rol.fk_despacho')
        ->select('rol.id_rol','WEB.usuarios.usuario', 'WEB.usuarios.cedula','rol.estado','rol.rol','WEB.usuarios.correo','EXP.despacho.descripcion as despacho', 'WEB.usuarios.password')
        ->get();
        return response()->json([ 
            "ok"    => true,
            "data"  =>$mostrarPerfil,
            "mensaje"   => 'Genial'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\login  $login
     * @return \Illuminate\Http\Response
     */
    public function selecionarUsuarios(Request $request)

    {
        $usuario = strtoupper($request->input('usuario'));
        $contrasena = strtolower($request->input('contrasena'));
        $login = Login::
        join('inv_roles_usuario', 'inv_roles_usuario.fk_usuario', 'usuarios.usuario')->
        join('EXP.despacho', 'inv_roles_usuario.fk_despacho', 'EXP.despacho.codigo')->
        
        select('inv_roles_usuario.fk_usuario','usuarios.password','inv_roles_usuario.rol','usuarios.nombre','usuarios.apellido','usuarios.cedula','usuarios.correo','exp.despacho.descripcion', 'exp.despacho.codigo as codigo_despacho')->
        where('usuario', $usuario)->
        get();

     
        if ($login->count()) {
            if (password_verify($contrasena, $login[0]['password'])) {
                return response ()->json([
                    "ok"  => true,
                    "aprobado"    => 'Ha ingresado existosamente',
                    "data" => $login[0],
                ]);
            } else  {
                return response ()->json([
                    "ok"  => false,
                    "errorUsuario"    => 'Usuario o contraseña incorrecta.'
                ]);
            }
        } else { 
            return response ()->json([
                "ok"  => false,
                "errorUsuario"    => 'Usuario o contraseña incorrecta'
            ]);
        } 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\login  $login
     * @return \Illuminate\Http\Response
     */
    public function edit(login $login)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\login  $login
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, login $login)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\login  $login
     * @return \Illuminate\Http\Response
     */
    public function destroy(login $login)
    {
        //
    }

 
}
