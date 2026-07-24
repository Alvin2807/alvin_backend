<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetalleArticuloRequest extends FormRequest
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
           'detalleArticulos'                         =>'sometimes|array|min:1',
           'detalleArticulos.*.fk_ubicacion'          => 'required|integer|exists:App\Models\UbicacionArticulo,id_ubicacion',
           'detalleArticulos.*.fk_detalle_compra'     => 'nullable|integer|exists:App\Models\DetallesCompras,id_detalle',
           'detalleArticulos.*.numero_serie'          => 'nullable|string|max:50',
           'detalleArticulos.*.descripcion'           => 'nullable|string|max:1000',
           'detalleArticulos.*.codigo_barra'          => 'nullable|string|max:20',
           'detalleArticulos.*.numero_activo'         => 'nullable|string|max:15',
           'detalleArticulos.*.id_color'              => 'nullable|integer|exists:App\Models\Colores,id_color',
           'detalleArticulos.*.estatus'               => 'nullable|string|max:1',
           'detalleArticulos.*.fk_fer_localizacion'   => 'nullable|integer|exists:App\Models\LocalizarFr,id_fer_localizacion',
           'detalleArticulos.*.usuario'               =>'required|string|max:35'
        ];
    }
}
