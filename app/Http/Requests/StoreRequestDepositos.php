<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestDepositos extends FormRequest
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
            'fk_despacho'   => 'required|string|max:8',
            'descripcion'   => 'required|string|max:200',
            'tipo_deposito' => 'required|string|max:3',
            'usuario'       => 'required|string|max:30'
        ];
    }
}
