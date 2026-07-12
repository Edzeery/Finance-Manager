<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'workspace_id' => $this->workspace_id,
            'status' => $this->status,
            'starts_at' => $this->starts_at->toISOString(),
            'ends_at' => $this->ends_at?->toISOString(),
            'trial_ends_at' => $this->trial_ends_at?->toISOString(),
            'canceled_at' => $this->canceled_at?->toISOString(),
            'auto_renew' => $this->auto_renew,
            'plan_price_amount' => (float) $this->plan_price_amount,
            'plan' => new SubscriptionPlanResource($this->whenLoaded('plan')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
