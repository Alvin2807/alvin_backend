<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetalleArticuloStoreRequest extends FormRequest
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
            //Reglas de validacion
            'detalleArticulos' => 'sometimes|array|min:1',
            'detalleArticulos.*.fk_ubicacion' => 'required|integer',
            'detalleArticulos.*.fk_detalle_compra' => 'nullable|integer',
            'detalleArticulos.*.numero_serie' => 'nullable|string|max:50',
            'detalleArticulos.*.descripcion' => 'nullable|string|max:1000',
            'detalleArticulos.*.codigo_barra' => 'nullable|string|max:20',
            'detalleArticulos.*.numero_activo' => 'nullable|string|max:15',
            'detalleArticulos.*.usuario' => 'required|string|max:35',
            'detalleArticulos.*.fk_color' => 'nullable|integer',
            'detalleArticulos.*.fk_fer_localizacion' => 'nullable|integer'
        ];
    }
}
