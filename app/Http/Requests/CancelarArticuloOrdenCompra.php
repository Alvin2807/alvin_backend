<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelarArticuloOrdenCompra extends FormRequest
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
            'id_detalle'                => 'required|integer',
            'fk_articulo'               => 'required|integer',
            'cantidad_pedida_articulos' => 'required|integer',
            'cantidad_pedida'           => 'required|integer',
            'usuario'                   => 'required|string|max:35'
        ];
    }
}
