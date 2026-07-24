<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarMovimientoRequest extends FormRequest
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
            //relas de editar movimiento
            'id_movimiento_enc' => 'required|integer',
            'entregado_por'     => 'required|string|max:80',
            'fecha_entrega'     => 'required|date',
            'fk_despacho_destino' => 'required|string|max:8',
            'fk_despacho_origen' => 'required|string|max:8',
            'observacion' => 'nullable|string|max:300',
            'solicitado_por' => 'required|string|max:80',
            'recibido_por' => 'nullable|string|max:80',
            'uibp_destino' => 'nullable|string|max:80',
            'uibp_origen' => 'nullable|string|max:80',
            'usuario' => 'required|string|max:30'



        ];
    }
}
