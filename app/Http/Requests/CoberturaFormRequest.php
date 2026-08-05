<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoberturaFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'suma' => ['required', 'numeric', 'min:0'],
            'gastos' => ['required', 'numeric', 'min:0'],
            'deducible' => ['required', 'numeric', 'min:0'],
            'vrMensual' => ['required', 'numeric', 'min:0'],
            'vrTrimestral' => ['nullable', 'numeric', 'min:0'],
            'vrSemestral' => ['nullable', 'numeric', 'min:0'],
            'x21' => ['nullable', 'numeric', 'min:0'],
            'x32' => ['nullable', 'numeric', 'min:0'],
            'x64' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
