<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarParcialRequest extends FormRequest
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
            'id_solicitud'  => 'required|integer',
            'no_factura'    => 'required|string|max:50',
            'fecha_factura' => 'required|date',
            'id_detalle'    => 'required|integer',
            'id_localizacion' => 'nullable|integer',
            'fk_articulo'   => 'required|integer',
            'recibir_en'    => 'required|string|max:1',
            'cantidad_confirmar' =>'required|integer',
            'usuario' => 'required|string|max:35'
        ];
    }
}
