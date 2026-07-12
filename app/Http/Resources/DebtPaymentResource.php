<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'payment_date' => $this->payment_date->format('Y-m-d'),
            'notes' => $this->notes,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
