<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedoresRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'codigo_proveedor'  => 'required|integer',
            'nombre'            => 'required|string',
            'ruc'               => 'nullable|string',
            'dv'                => 'nullable|string',
            'direccion'         => 'nullable|string',
            'telefono1'         => 'nullable|string',
            'telefono2'         => 'nullable|string',
            'celular'           => 'nullable|string',
            'fax'               => 'nullable|string',
            'apartado'          => 'nullable|string',
            'email'             => 'nullable|email',
            'pagina_web'        => 'nullable|string',
            'contacto'          => 'nullable|string',
            'pagina_web'        => 'nullable|string',
            'usuario'           => 'required|string'
        ];
    }
}
