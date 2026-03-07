<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DivisionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'division_name' => $this->division_name,
            'capacity' => $this->capacity,
            'is_active' => $this->is_active,
        ];
    }
}
