<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarRequestOrdenCompra extends FormRequest
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
            //validación
            'id_solicitud'        => 'required|integer',
            'no_factura'          => 'required|string|max:50',
            'fecha_factura'       => 'required|date',
            'usuario'             => 'required|string|max:35',
            'articulosCompras'    =>'sometimes|array|min:1',
            'articulosCompras.*.id_detalle'         => 'required|integer',
            'articulosCompras.*.cantidad_confirmar' => 'required|integer',
            'articulosCompras.*.fk_articulo'        => 'required|integer',
            'articulosCompras.*.recibir_en'         => 'required|string|max:1',
        ];
    }
}
