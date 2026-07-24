<?php

namespace App\Http\Requests;
use App\Models\Articulos;
use Illuminate\Foundation\Http\FormRequest;

class RegistrarArticulosRequest extends FormRequest
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
            //reglas de registrar artículos
            'codigo'           =>'required|integer',
            'descripcion'      =>'required|string',
            'fk_marca'         =>'nullable|integer',
            'fk_modelo'        =>'nullable|integer',
            'fk_categoria'     =>'nullable|integer',
            'fk_grupo'         =>'nullable|integer',
            'fk_sub_grupo'     =>'nullable|integer',
            'fk_tipo_uso'      =>'required|integer',
            'periodo_garantia' =>'nullable|string',
            'garantia'         =>'nullable|integer',
            'requiere_activo'  =>'required|string',
            'cantidad_pedida'  =>'nullable|integer',
            'cantidad_minima'  =>'required|integer',
            'cantidad_almacen' =>'nullable|integer',
            'cantidad_stok'    =>'nullable|integer',
            'fecha_ult_compra' => 'nullable|datetime:Y-m-d',
            'ultimo_precio'    =>'nullable|float',
            'precio_promedio'  =>'nullable|float',
            'usuario'          =>'required|string'
        ];
    }
}
