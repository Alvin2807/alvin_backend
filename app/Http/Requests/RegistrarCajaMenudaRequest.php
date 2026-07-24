<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarCajaMenudaRequest extends FormRequest
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
            'no_caja_menuda'      => 'required|string|max:20',
            'fecha_nota'          => 'required|date',
            'no_factura'          => 'required|string|max:50',
            'fecha_factura'       => 'required|date',
            'no_nota'             => 'required|string|max:20',
            'usuario'             => 'required|string|max:35',
            'fk_solicitado'       => 'required|string|max:8',
            'fk_seccion'          => 'required|integer',
            'aprobado_por'        => 'required|string|max:100',
            'solicitado_por'      => 'required|string|max:100',
            'fk_proveedor'        => 'required|integer',
            'observacion'         => 'nullable|string|max:1000',
            'articulosCompras'    => 'sometimes|array|min:1',
            'articulosCompras.*.no_item'         => 'required|integer',
            'articulosCompras.*.fk_articulo'     => 'required|integer',
            'articulosCompras.*.refe_proveedor'  => 'nullable|string|max:50',
            'articulosCompras.*.cantidad_pedida' => 'required|integer',
            'articulosCompras.*.precio'          => 'required|numeric',
            'articulosCompras.*.porcentaje'      => 'required|integer',
            'articulosCompras.*.fk_localizacion' => 'nullable|integer'
        ];
    }
}
