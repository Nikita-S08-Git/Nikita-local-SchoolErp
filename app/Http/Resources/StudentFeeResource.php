<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentFeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fee_head' => $this->feeHead?->name,
            'amount' => $this->amount,
            'paid_amount' => $this->paid_amount,
            'balance' => $this->amount - $this->paid_amount,
            'payment_status' => $this->payment_status,
            'due_date' => $this->due_date?->format('Y-m-d'),
        ];
    }
}
