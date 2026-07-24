<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelarOrdenRequest extends FormRequest
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
            'usuario' => 'required|string|max:35',
            'id_solicitud' => 'required|integer',
            'articulosCompras' =>'sometimes|array|min:1',
            'articulosCompras.*.fk_articulo' =>'required|integer',
            'articulosCompras.*.id_detalle'  =>'required|integer',
            'articulosCompras.*.cantidad_pedida_articulos' =>'required|integer',
            'articulosCompras.*.pedida_detalle' =>'required|integer'
        ];
    }
}
