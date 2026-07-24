<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestLocalizaciones extends FormRequest
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
            'fk_deposito' => 'required|integer',
            'usuario'     => 'required|string|max:30',
            'localizaciones' => 'sometimes|array|min:1',
            'localizaciones.*.descripcion' => 'required|string|max:200'
        ];
    }
}
