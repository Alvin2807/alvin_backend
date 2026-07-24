<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarSolicitudRequest extends FormRequest
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
            'id_solicitud'        => 'required|integer',
            'usuario'             => 'required|string|max:35',
            'no_nota'             => 'required|string|max:20',
            'orden_compra'        => 'required|string|max:50',
            'fecha_orden'         => 'required|date',
            'fecha_nota'          => 'required|date',
            'no_factura'          => 'nullable|string',
            'fecha_factura'       => 'nullable|string',
            'fecha_referendo'     => 'nullable|date',
            'fecha_publicacion'   => 'nullable|date',
            'no_solicitud_linea'  => 'required|string|max:50',
            'no_solicitud_bienes' => 'required|string|max:50',
            'fk_seccion'          => 'required|integer',
            'fk_solicitado'       => 'required|string|max:8',
            'aprobado_por'        => 'required|string|max:100',
            'plazo_entrega'       => 'nullable|integer',
            'periodo_de_entrega'  => 'nullable|string|max:1',
            'termino_de_entrega'  => 'nullable|string|max:1',
            'fecha_aprox_entrega' => 'nullable|date',
            'fk_proveedor'        => 'required|integer',
            "observacion"         => 'nullable|string|max:1000',
            'articulosCompras'    => 'sometimes|array|min:1',
            'articulosCompras.*.id_detalle'      => 'nullable|integer',
            'articulosCompras.*.cantidad_pedida' => 'nullable|integer',
            'articulosCompras.*.precio'          => 'required|numeric',
            'articulosCompras.*.porcentaje'      => 'required|integer',
            'articulosCompras.*.fk_articulo'     => 'nullable|integer',
            'articulosCompras.*.refe_proveedor'  => 'nullable|string|max:50',
            'articulosCompras.*.item' =>'nullable|integer',
         ];
    }
}
