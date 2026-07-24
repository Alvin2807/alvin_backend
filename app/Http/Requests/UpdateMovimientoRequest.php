<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovimientoRequest extends FormRequest
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
            //reglas de validacion de editar
            'tipo_movimiento' =>'required|string|max:3',
            'fk_despacho_origen' =>'required|string|max:8',
            'fk_localizacion_origen' =>'required|integer',
            'fk_despacho_destino' =>'required|string|max:8',
            'fk_localizacion_destino' =>'required|integer',
            'no_movimiento' =>'nullable|string',
            'no_nota' =>'nullable|string|max:30',
            'solicitado_por'=>'required|string|max:80',
            'aprobado_por' =>'required|string|max:80',
            'entregado_por' =>'required|string|max:80',
            'recibido_por' =>'nullable|string|max:80',
            'observacion' =>'nullable|string|max:300',
            'usuario'=>'required|string|max:30',
            'uibp_origen' =>'nullable|string|max:80',    //nombre del empleado de origen de Bienes patrimoniales
            'uibp_destino'=>'nullable|string|max:80',   //nombre del empleado de destino de Bienes patrimoniales
            'cantidad_stock'=>'required|integer',
            'detallesMovimiento'=>'sometimes|array|min:1',
            'detallesMovimiento.*.fk_ubicacion_origen'=>'required|integer',
            'detallesMovimiento.*.id_movimiento_det'=>'required|integer',
            'detallesMovimiento.*.fk_detalle_origen'=>'required|integer',
            'detallesMovimiento.*.cantidad'=>'required|integer',
            'detallesMovimiento.*.observaciones'=>'nullable|string|max:200',
            'detallesMovimiento.*.usuario'=>'required|string|max:35'
        ];
    }
}
