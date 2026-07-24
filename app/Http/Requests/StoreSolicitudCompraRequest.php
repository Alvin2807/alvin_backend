<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudCompraRequest extends FormRequest
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
            //reglas de validación
            'no_nota'             => 'required|string|max:20',
            'fecha_nota'          => 'required|date',
            'observacion'         => 'nullable|string|max:1000',
            'fk_solicitado'       => 'required|string|max:8',
            'orden_compra'        => 'required|string|max:50',
            'fecha_orden'         => 'required|date',
            'fk_tipo_solicitud'   => 'integer|exists:App\Models\TipoSolicitudes,id_tipo_solicitud',
            'plazo_entrega'       => 'nullable|numeric',
            'fecha_aprox_entrega' => 'nullable|date',
            'no_solicitud_linea'  => 'nullable|string|max:50',
            'no_solicitud_bienes' => 'nullable|string|max:50',
            'no_factura'          => 'nullable|string|max:50',
            'fecha_factura'       => 'nullable|date',
            'fk_proveedor'        => 'integer|exists:App\Models\Proveedor,id_proveedor',
            'sub_total'           => 'nullable|numeric',
            'itbms'               => 'nullable|numeric',
            'total'               => 'nullable|numeric',
            'usuario_crea'        => 'nullable|string|max:35',
            'estado'              => 'nullable|string|max:1',
            'fk_seccion'          => 'integer|exists:App\Models\SessionDespacho,id_seccion',
            'fecha_referendo'     => 'nullable|date',
            'fecha_publicacion'   => 'nullable|date',
            'periodo_entrega'     => 'nullable|string',
            'termino_entrega'     => 'nullable|string'
        ];
        
    }
}
