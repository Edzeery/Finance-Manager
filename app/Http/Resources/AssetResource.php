<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'quantity' => (float) $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'total_value' => (float) $this->total_value,
            'currency' => $this->currency,
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'karat' => $this->karat,
            'weight_grams' => $this->weight_grams !== null ? (float) $this->weight_grams : null,
            'is_liquid' => $this->is_liquid,
            'is_zakatable' => $this->is_zakatable,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
