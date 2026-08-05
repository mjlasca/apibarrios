<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActividadFormRequest extends FormRequest
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
        ];
    }
}
