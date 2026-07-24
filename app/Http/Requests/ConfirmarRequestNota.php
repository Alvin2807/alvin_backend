<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarRequestNota extends FormRequest
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
            'id_movimiento_enc'   =>'required|integer',
            'usuario'             => 'required|string|max:30',
            'entregado_por'       => 'required|string|max:80',
            'fecha_entrega'       => 'required|date',
            'fecha_nota'          => 'required|date',
            'no_nota'             => 'required',
            'observacion'         => 'nullable|string|max:300',
            'solicitado_por'      => 'required|string|max:80',
            'recibido_por'        => 'required|string|max:80',
            'aprobado_por'        => 'required|string|max:80',
            'uibp_destino'        => 'nullable|string|max:80',
            'uibp_origen'         => 'nullable|string|max:80',
            'usuario'             => 'required|string|max:30',
            'articulosMovimiento' => 'sometimes|array|min:1',
            'articulosMovimiento.*.id_movimiento_det'       => 'required|integer',
            'articulosMovimiento.*.fk_ubicacion_origen'     => 'required|integer',
            'articulosMovimiento.*.cantidad'                => 'required|integer',
            'articulosMovimiento.*.id_articulo'             => 'required|integer',
            'articulosMovimiento.*.cantidad_en_movimiento'  => 'required|integer',
            'articulosMovimiento.*.cantidad_stock'          => 'required|integer',
            'articulosMovimiento.*.observaciones'           => 'nullable|string|max:200',
            'articulosMovimiento.*.fk_localizacion_destino' => 'required|integer',
            'articulosMovimiento.*.fk_localizacion_origen'  => 'required|integer',
            'articulosMovimiento.*.ubicacion_destino'       => 'nullable|integer',
            'articulosMovimiento.*.stock_destino'           => 'nullable|integer'
        ];
    }
}
