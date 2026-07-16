<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_free' => $this->is_free,
            'monthly_price' => (float) $this->monthly_price,
            'yearly_price' => (float) $this->yearly_price,
            'prices' => $this->activePrices->map(fn ($p) => [
                'billing_period' => $p->billing_period,
                'currency' => $p->currency,
                'price' => (float) $p->price,
            ]),
            'features' => $this->planFeatures->map(fn ($f) => [
                'slug' => $f->slug,
                'name' => $f->{'name_'.app()->getLocale()} ?? $f->name_en,
                'value' => $f->pivot->value,
                'icon' => $f->icon,
                'type' => $f->type,
            ]),
            'max_users' => $this->max_users,
            'max_workspaces' => $this->max_workspaces,
            'button_text' => $this->button_text,
            'button_link' => $this->button_link,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
