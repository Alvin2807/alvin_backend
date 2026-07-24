<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarUnidadMedidaRequest extends FormRequest
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
            'medida'      => 'required|string|max:100',
            'factor_unidad'    => 'required|integer',
            'usuario'          => 'required|string|max:30',
            'id_unidad_medida' => 'required|integer'
        ];
    }
}
