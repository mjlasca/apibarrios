<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClasificacionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cod' => ['required', 'integer', 'min:0'],
            'nombre' => ['required', 'string', 'max:150'],
            'id_actividad' => ['required', 'integer', 'exists:actividades,id'],
        ];
    }
}
