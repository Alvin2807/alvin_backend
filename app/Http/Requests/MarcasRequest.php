<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class MarcasRequest extends FormRequest
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
            //reglas de validación
            'nombre_marca' => 'required|string|max:100|unique:App\Models\Marcas',
        ];
    }
}
