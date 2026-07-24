<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelarCajaRequest extends FormRequest
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
            //reglas de validacion
            
            'id_solicitud'  => 'required|integer',
            'usuario'       => 'required|string',
            'articulosCaja' =>'sometimes|array|min:1',
            'articulosCaja.*.id_detalle' =>'required|integer',
            'articulosCaja.*.fk_articulo' =>'required|integer',
           
        ];
    }
}
