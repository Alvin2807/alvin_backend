<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarRequestColores extends FormRequest
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
            //Reglas de editar colores
            'id_color'  =>'required|integer',
            'color'     => 'required|string|max:100',
            'usuario'   => 'required|string|max:30'
        ];
    }
}
