<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelarRequestTraspasoSalida extends FormRequest
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

            'id_movimiento_enc' =>'required|integer',
            'articulosMovimiento' => 'sometimes|array|min:1',
            'articulosMovimiento.*.id_movimiento_det' =>'required|integer',
            'articulosMovimiento.*.fk_detalle_origen' =>'nullable|integer'
        ];
    }
}
