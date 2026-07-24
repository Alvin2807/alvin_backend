<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarPorcentajesrequest extends FormRequest
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
            'tipo'             => 'required|string|max:10',
            'descripcion'      => 'required|string|max:80',
            'porcentaje'       => 'required|integer',
            'usuario'          => 'required|string|max:35'
        ];
    }
}
