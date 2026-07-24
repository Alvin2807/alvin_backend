<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModelosRequest extends FormRequest
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
            //Reglas de validación
            'fk_marca' =>'required|integer|unique:App\Models\Modelos',
            'nombre_modelo' =>'required|string|max:100',
            'usuario_crea' =>'nullable|string',
        ];
    }
}
