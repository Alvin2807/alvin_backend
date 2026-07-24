<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevertirRequestFR extends FormRequest
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
            'articulosFR' => 'sometimes|array|min:1',
            'articulosFR.*.cantidad_recibida' =>'required|integer',
            'articulosFR.*.fk_articulo' =>'required|integer',
            'articulosFR.*.fk_localizacion' =>'required|integer',
            'articulosFR.*.stock_articulo' => 'required|integer',
            'articulosFR.*.cantidad_almacen'=>'required|integer',
            'articulosFR.*.id_detalle' =>'required|integer',
            'articulosFR.*.almacen_detalle'=>'required|integer',
            'articulosFR.*.id_fer_encabe' =>'required|integer',
            'articulosFR.*.id_fer_detalle' =>'required|integer'

        ];
    }
}
