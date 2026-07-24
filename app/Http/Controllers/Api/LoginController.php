<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function login(Request $request)
    {
        $usuario = strtoupper($request->input('usuario'));
        $contrasena = strtolower($request->input('contrasena'));
        $login = Login::
        join('inv_roles_usuario', 'inv_roles_usuario.fk_usuario', 'usuarios.usuario')->
        join('EXP.despacho', 'inv_roles_usuario.fk_despacho', 'EXP.despacho.codigo')->
        
        select('inv_roles_usuario.fk_usuario','usuarios.password','inv_roles_usuario.rol',
        'usuarios.nombre','usuarios.apellido','usuarios.cedula','usuarios.correo','exp.despacho.descripcion', 
        'exp.despacho.codigo as codigo_despacho')->
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
     * Display the specified resource.
     *
     * @param  \App\Models\Login  $login
     * @return \Illuminate\Http\Response
     */
    public function show(Login $login)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Login  $login
     * @return \Illuminate\Http\Response
     */
    public function edit(Login $login)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Login  $login
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Login $login)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Login  $login
     * @return \Illuminate\Http\Response
     */
    public function destroy(Login $login)
    {
        //
    }
}
