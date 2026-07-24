<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestEditarLocalizacion extends FormRequest
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
            'id_localizacion'  => 'required|integer',
            'localizacion'     => 'required|string|max:200',
            'usuario'          => 'required|string|max:30',
            'estado'           => 'required|string|max:1'
        ];
    }
}
