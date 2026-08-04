<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'full_name' => $this->full_name,
            'tipo_id' => $this->tipo_id,
            'fecha_nacimiento' => $this->fecha_nacimiento?->format('Y-m-d'),
            'telefono' => $this->telefono,
            'email' => $this->email,
        ];
    }
}
