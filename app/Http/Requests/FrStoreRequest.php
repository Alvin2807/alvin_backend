<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrStoreRequest extends FormRequest
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
            //reglas de validación de la tabla de inv_fer_encabe
            'fk_despacho' =>'required|string|max:8',
             'fecha_entrega' =>'nullable|date_format:Y-m-d',
             'lugar_entrega' =>'required|string|max:50',
             'solicitado_por' =>'required|string|max:80',
             'aprobado_por' =>'required|string|max:80',
             'entregado_por' =>'required|string|max:80',
             'recibido_por' =>'nullable|string|max:80',
             'observacion'=>'nullable|string|max:100',
             'usuario_crea'=>'required|string|max:30',
             'fk_localizacion' =>'required|integer',

             //DATOS DE FER_DETALLE
             'articulosFR'       => 'sometimes|array|min:1',
             'articulosFR.*.id_detalle' => 'required|integer',
             'articulosFR.*.cantidad_solicitar' =>'required|integer',
             
            
             
        ];
    }
}
