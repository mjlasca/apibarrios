<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClasificacionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'reg' => $this->reg,
            'cod' => $this->cod,
            'nombre' => $this->nombre,
        ];
    }
}
