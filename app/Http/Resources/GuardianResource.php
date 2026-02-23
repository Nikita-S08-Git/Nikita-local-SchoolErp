<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'relation' => $this->relation,
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
            'occupation' => $this->occupation,
            'annual_income' => $this->annual_income,
            'is_primary' => $this->is_primary ?? false,
        ];
    }
}
