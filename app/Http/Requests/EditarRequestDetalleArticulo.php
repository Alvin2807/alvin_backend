<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarRequestDetalleArticulo extends FormRequest
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
            //Reglas de validación de editar detalle de artículo
            'id_detalle_articulo' => 'required|integer',
            'fk_ubicacion' => 'required|integer',
            'fk_detalle_compra' =>'nullable|integer',
            'numero_serie' =>'nullable|string|max:50',
            'descripcion' =>'nullable|string|max:1000',
            'codigo_barra' =>'nullable|string|max:20',
            'numero_activo' =>'nullable|string|max:15',
            'fk_color' =>'nullable|integer',
            'usuario_modifica' =>'required|string|max:35',
            'fk_fer_localizacion' =>'nullable|integer'
        ];
    }
}
