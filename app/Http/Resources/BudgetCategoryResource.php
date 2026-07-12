<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'allocated_amount' => (float) $this->allocated_amount,
            'spent_amount' => (float) $this->spent_amount,
            'remaining_amount' => (float) ($this->allocated_amount - $this->spent_amount),
            'category' => new ExpenseCategoryResource($this->whenLoaded('category')),
        ];
    }
}
