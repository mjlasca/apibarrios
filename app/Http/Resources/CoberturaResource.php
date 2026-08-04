<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoberturaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'suma' => $this->suma,
            'gastos' => $this->gastos,
            'deducible' => $this->deducible,
            'vrMensual' => $this->vrMensual,
            'vrTrimestral' => $this->vrTrimestral,
            'vrSemestral' => $this->vrSemestral,
            'x21' => $this->x21,
            'x32' => $this->x32,
            'x64' => $this->x64,
        ];
    }
}
