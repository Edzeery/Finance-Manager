<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'counterparty_name' => $this->counterparty_name,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'remaining_amount' => (float) ($this->total_amount - $this->paid_amount),
            'due_date' => $this->due_date->format('Y-m-d'),
            'status' => $this->status,
            'description' => $this->description,
            'reminder_date' => $this->reminder_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'payments' => DebtPaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
