<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelarFrRequest extends FormRequest
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
            //Reglas de validación de cancelar FR completo
            'usuario_modifica' =>'required|string|max:30',
            'id_fer_encabe'    => 'required|integer',
            'articulosFR' =>'sometimes|array|min:1',
            'articulosFR.*.id_fer_detalle' =>'required|integer',
            'articulosFR.*.id_fer_localizacion' =>'required|integer',
            'articulosFR.*.recibida_fr' =>'required|integer'
        ];
    }
}
