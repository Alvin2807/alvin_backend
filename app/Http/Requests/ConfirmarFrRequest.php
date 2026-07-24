<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarFrRequest extends FormRequest
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
           
            //Validación de confirmar FR
            'id_fer_encabe' => 'required|integer',
            'recibido_por' =>'required|string|max:80',
            'fecha_entrega' =>'required|date',
            'observacion' =>'nullable|string|max:100',
            'usuario_modifica' =>'required|string|max:30',
            'articulosFR' =>'sometimes|array|min:1',
            'articulosFR.*.id_fer_detalle' =>'required|integer',
            'articulosFR.*.cantidad_pedida' =>'required|integer', // cantidad pedida del detalle FR
            'articulosFR.*.solicitada_fr' =>'required|integer', 
            //'articulosFR.*.id_ubicacion' =>'required|integer',
            'articulosFR.*.recibida_fr' =>'required|integer', // cantidad recibida de detalle de FR
            'articulosFR.*.id_fer_detalle' =>'required|integer',
            'articulosFR.*.cantidad_stock' =>'required|integer', // cantidad stock de articulos
            'articulosFR.*.almacen_articulos' =>'required|integer', // cantidad almacen de articulos
            'articulosFR.*.id_articulo' =>'required|integer',
            'articulosFR.*.fk_detalle_compra' =>'required|integer',
            'articulosFR.*.cantidad_almacen' =>'required|integer' // cantidad almacen del detalle de compra
            


        ];
    }
}
