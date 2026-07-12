<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'description' => $this->description,
            'currency' => $this->currency,
            'timezone' => $this->timezone,
            'is_active' => $this->is_active,
            'trial_ends_at' => $this->trial_ends_at?->toISOString(),
            'plan' => $this->when($this->relationLoaded('subscription'), function () {
                return $this->activePlan()?->name;
            }),
            'role' => $this->when($this->relationLoaded('subscription'), function () {
                return $this->workspaceRole(auth()->user()) ?? null;
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
