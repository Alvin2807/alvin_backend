<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarDetalleCajaMenuda extends FormRequest
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
            'id_solicitud'   => 'required|integer',
            'no_nota'        => 'required|string|max:20',
            'fecha_nota'     => 'required|date',
            'no_caja_menuda' => 'required|string|max:25',
            'no_factura'     => 'required|string|max:50',
            'fecha_factura'  => 'required|date',
            'observacion'    => 'nullable|string|max:1000',
            'fk_solicitado'  => 'required|string|max:8',
            'aprobado_por'   => 'required|string|max:100',
            'solicitado_por' => 'required|string|max:80',
            'fk_proveedor'   => 'required|integer',
            'fk_seccion'     => 'required|integer',
            'usuario'        => 'required|string|max:35',
            'articulosCaja'  => 'sometimes|array|min:1',
            'articulosCaja.*.refe_proveedor'  => 'nullable|string|max:50',
            'articulosCaja.*.cantidad'        => 'required|integer',
            'articulosCaja.*.precio'          => 'required|numeric',


        ];
    }
}
