<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarArticulosRequest extends FormRequest
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
            //
            'id_articulo'      =>'required|integer',
            'codigo'           =>'required|integer',
            'descripcion'      =>'required|string',
            'fk_marca'         =>'nullable|integer',
            'fk_modelo'        =>'nullable|integer',
            'fk_categoria'     =>'nullable|integer',
            'fk_grupo'         =>'nullable|integer',
            'fk_sub_grupo'     =>'nullable|integer',
            'fk_tipo_uso'      =>'required|integer',
            'periodo_grantia'  =>'nullable|string',
            'grantia'          =>'nullable|integer',
            'cantidad_minima'  =>'nullable|integer',
            'requiere_activo'  =>'required|string',
            'usuario'          =>'required|string'
        ];
    }
}
