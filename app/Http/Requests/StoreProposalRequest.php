<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tomador.documento' => ['required', 'string', 'max:100'],
            'tomador.tipo_id' => ['sometimes', 'string', 'max:100'],
            'tomador.nombres' => ['required', 'string', 'max:150'],
            'tomador.apellidos' => ['required', 'string', 'max:150'],
            'tomador.fecha_nacimiento' => ['required', 'date'],
            'tomador.telefono' => ['nullable', 'string', 'max:100'],
            'tomador.email' => ['nullable', 'email', 'max:100'],

            'cobertura' => ['required', 'string', 'max:100'],
            'meses' => ['required', 'integer', 'min:1', 'max:6'],
            'fecha_desde' => ['nullable', 'date_format:Y-m-d'],
            'formadepago' => ['sometimes', 'string', Rule::in(['CONTADO', 'CREDITO'])],

            'asegurados' => ['required', 'array', 'min:1'],
            'asegurados.*.documento' => ['required', 'string', 'max:100'],
            'asegurados.*.nombres' => ['required', 'string', 'max:150'],
            'asegurados.*.apellidos' => ['required', 'string', 'max:150'],
            'asegurados.*.fecha_nacimiento' => ['required', 'date'],
            'asegurados.*.id_actividad' => ['required', 'integer', 'exists:actividades,id'],
            'asegurados.*.id_clasificacion' => ['required', 'integer', 'exists:clasificaciones,id'],

            'barrios' => ['sometimes', 'array'],
            'barrios.*' => ['string', 'max:100'],
            'grupos' => ['sometimes', 'array'],
            'grupos.*' => ['integer', Rule::exists('gruposbarrios', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'asegurados.min' => 'Debe agregar al menos un cliente vinculado.',
            'tomador.nombres.required' => 'Los nombres del tomador son obligatorios.',
            'tomador.apellidos.required' => 'Los apellidos del tomador son obligatorios.',
            'asegurados.*.id_actividad.exists' => 'Existe una actividad inválida en la grilla.',
            'asegurados.*.id_clasificacion.exists' => 'Existe una clasificación inválida en la grilla.',
        ];
    }
}
