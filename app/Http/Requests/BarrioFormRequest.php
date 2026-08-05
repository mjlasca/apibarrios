<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarrioFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'max:100'],
            'nombre' => ['required', 'string', 'max:200'],
            'telefono' => ['nullable', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'email' => ['nullable', 'email', 'max:100'],
            'sub_barrio' => ['nullable', 'string', 'max:100'],
            'clase_barrio' => ['nullable', 'string', 'max:100'],
            'suma_muerte' => ['nullable', 'numeric', 'min:0'],
            'suma_gm' => ['nullable', 'numeric', 'min:0'],
            'suma_rc' => ['nullable', 'numeric', 'min:0'],
            'exige' => ['nullable', 'string', 'max:100'],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ];
    }
}
