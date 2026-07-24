<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarCajaRequest extends FormRequest
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
            'no_factura'          => 'required|string',
            'fecha_factura'       => 'required|date',
            'fecha_nota'          => 'required|date',
            'fk_seccion'          => 'required|integer',
            'fk_solicitado'       => 'required|string|max:8',
            'aprobado_por'        => 'required|string|max:100',
            'nombre_solicitante'  => 'required|string|max:100',
            'fk_proveedor'        => 'required|integer',
            "observacion"         => 'nullable|string|max:1000',
            'no_caja_menuda'      => 'required|string|max:20',
            'articulosCompras'    => 'sometimes|array|min:1',
            'articulosCompras.*.id_detalle'      => 'required|integer',
            'articulosCompras.*.cantidad_pedida' => 'required|integer',
            'articulosCompras.*.precio'          => 'required|numeric',
            'articulosCompras.*.porcentaje'      => 'required|integer',
            'articulosCompras.*.fk_articulo'     => 'required|integer',
            'articulosCompras.*.fk_localizacion' => 'required|integer',
            'articulosCompras.*.refe_proveedor'  => 'nullable|string|max:50',
            
        ];
    }
}
