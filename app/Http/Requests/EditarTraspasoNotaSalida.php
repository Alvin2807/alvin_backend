<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarTraspasoNotaSalida extends FormRequest
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
            'tipo_movimiento'                               =>'required|max:3|string',
            'fk_despacho_origen'                            => 'required|string',
            'fk_despacho_destino'                           => 'required|string',
            'solicitado_por'                                => 'required|string|max:80',
            'fecha_entrega'                                 => 'required|date',
            'entregado_por'                                 => 'required|string|max:80',
            'recibido_por'                                  => 'nullable|string|max:80',
            'no_nota'                                       => 'required|string:max:20',
            'fecha_nota'                                    => 'required|date',
            'aprobado_por'                                  => 'nullable|string|max:80',
            'uibp_origen'                                   => 'nullable|string|max:80',
            'uibp_destino'                                  => 'nullable|string|max:80',
            'usuario'                                       => 'required|string|max:30',
            'articulosMovimiento'                           => 'sometimes|array|min:1',
            'articulosMovimiento.*.id_ubicacion'            => 'required|integer',
            'articulosMovimiento.*.fk_detalle_origen'       => 'nullable|integer',
            'articulosMovimiento.*.cantidad'                => 'required|integer',
            'articulosMovimiento.*.observaciones'           => 'nullable|string|max:200',
            'articulosMovimiento.*.fk_localizacion'         => 'required|integer',
            'articulosMovimiento.*.fk_localizacion_destino' => 'required|integer',
            'articulosMovimiento.*.cantidad_stock'          => 'nullable|integer'
        ];
    }
}
