<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarConfirmacionOrdenDetallesCompraRequest extends FormRequest
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
            'id_detalle'            => 'required|integer|exists:App\Models\DetallesCompras',
            'cantidad_confirmar'    => 'required|integer',
            'cantidad_pedida'       => 'required|integer',
            'cantidad_almacen'      => 'required|integer',
            'precio'                => 'required|numeric'
        ];
    }
}
