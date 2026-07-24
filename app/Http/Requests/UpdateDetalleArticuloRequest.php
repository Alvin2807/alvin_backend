<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDetalleArticuloRequest extends FormRequest
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
            'fk_ubicacion'          => 'required|integer|exists:App\Models\UbicacionArticulo,id_ubicacion',
            'fk_detalle_compra'     => 'nullable|integer|exists:App\Models\DetallesCompras,id_detalle',
            'numero_serie'          => 'nullable|string|max:50',
            'descripcion'           => 'nullable|string|max:200',
            'codigo_barra'          => 'nullable|string|max:20',
            'codigo_qr'             => 'nullable|string|max:100',
            'numero_activo'         => 'nullable|string|max:15',
            'fk_color'              => 'nullable|integer|exists:App\Models\Colores,id_color',
            'estatus'               => 'nullable|string|max:1',
            'fk_fer_localizacion'   => 'nullable|integer|exists:App\Models\LocalizarFr,id_fer_localizacion'
        ];
    }
}
