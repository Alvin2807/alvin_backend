<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestOrdenCompra extends FormRequest
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
            'orden_compra'        => 'required|string|max:10',
            'fecha_orden'         => 'required|date',
            'fecha_nota'          => 'required|date',
            'fecha_referendo'     => 'nullable|date',
            'fecha_publicacion'   => 'nullable|date',
            'no_factura'          => 'nullable|string|max:50',
            'fecha_factura'       => 'nullable|date',
            'no_nota'             => 'required|string|max:20',
            'usuario'             => 'required|string|max:35',
            'no_solicitud_linea'  => 'required|string|max:50',
            'no_solicitud_bienes' => 'required|string|max:50',
            'fk_solicitado'       => 'required|string|max:8',
            'fk_seccion'          => 'required|integer',
            'aprobado_por'        => 'required|string|max:100',
            'plazo_entrega'       => 'nullable|integer:max:3',
            'periodo_entrega'     => 'nullable|string|max:1',
            'termino_entrega'     => 'nullable|string|max:1',
            'fecha_prox_entrega'  => 'nullable|date',
            'fk_proveedor'        => 'required|integer',
            'observacion'         => 'nullable|string|max:1000',
            'articulosCompras'    => 'sometimes|array|min:1',
            'articulosCompras.*.no_item'         => 'required|integer',
            'articulosCompras.*.fk_articulo'     => 'required|integer',
            'articulosCompras.*.refe_proveedor'  => 'nullable|string|max:50',
            'articulosCompras.*.cantidad_pedida' => 'required|integer',
            'articulosCompras.*.precio'          => 'required|numeric',
            'articulosCompras.*.porcentaje'      => 'required|integer'


        ];
    }
}
