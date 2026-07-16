<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'description' => $this->description,
            'date' => $this->date instanceof Carbon
                ? $this->date->format('Y-m-d')
                : $this->date,
            'category' => $this->category,
            'created_at' => $this->created_at instanceof Carbon
                ? $this->created_at->toISOString()
                : $this->created_at,
        ];
    }
}
