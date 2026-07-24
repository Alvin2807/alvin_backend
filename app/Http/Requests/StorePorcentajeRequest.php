<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePorcentajeRequest extends FormRequest
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
            'tipo_porcentaje'  => 'required|string|max:10',
            'descripcion'      => 'required|string|max:80',
            'porcentaje'       => 'required|integer',
            'fecha_inicial'    => 'required|date',
            'usuario'          => 'required|string|max:35'
        ];
    }
}
