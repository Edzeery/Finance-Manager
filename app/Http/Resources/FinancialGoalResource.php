<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialGoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en ?? $this->name_fr ?? $this->name_ar,
            'target_amount' => (float) $this->target_amount,
            'current_amount' => (float) $this->current_amount,
            'progress_percentage' => $this->target_amount > 0
                ? round(($this->current_amount / $this->target_amount) * 100, 2)
                : 0,
            'target_date' => $this->target_date->format('Y-m-d'),
            'status' => $this->status,
            'icon' => $this->icon,
            'color' => $this->color,
            'notes' => $this->notes,
            'completed_at' => $this->completed_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
