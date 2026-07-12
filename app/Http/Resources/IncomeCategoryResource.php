<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomeCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en ?? $this->name_fr ?? $this->name_ar,
            'icon' => $this->icon,
            'color' => $this->color,
            'type' => $this->type,
        ];
    }
}
