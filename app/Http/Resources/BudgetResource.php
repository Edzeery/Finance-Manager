<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en ?? $this->name_fr ?? $this->name_ar,
            'type' => $this->type,
            'total_amount' => (float) $this->total_amount,
            'total_allocated' => $this->totalAllocated,
            'total_spent' => $this->totalSpent,
            'remaining_amount' => (float) ($this->total_amount - $this->totalSpent),
            'adherence_rate' => $this->adherenceRate,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'categories' => BudgetCategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
