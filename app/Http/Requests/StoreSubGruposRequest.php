<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubGruposRequest extends FormRequest
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
            'fk_grupo'  => 'required|integer',
            'usuario'   => 'required|string',
            'subGrupos' => 'sometimes|array|min:1',
            'subGrupos.*.descripcion' => 'required|string|max:100'
        ];
    }
}
