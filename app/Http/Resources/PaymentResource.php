<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'workspace_id' => $this->workspace_id,
            'subscription_id' => $this->subscription_id,
            'method' => $this->method,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'reference' => $this->reference,
            'transaction_id' => $this->transaction_id,
            'notes' => $this->notes,
            'paid_at' => $this->paid_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
