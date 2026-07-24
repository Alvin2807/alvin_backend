<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelarArticuloFrRequest extends FormRequest
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
            //reglas de validación de cancelar articulo en Fr
            'id_fer_detalle'       =>'required|integer',
            //'usuario_modifica'     =>'required|string|max:35',
            'id_fer_localizacion'  =>'required|integer',
            'cantidad_recibida'    =>'required|integer'
           
        ];
    }
}
