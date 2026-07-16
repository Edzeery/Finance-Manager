<?php

namespace App\Http\Requests\Api\Subscription;

use App\Http\Requests\Api\ApiRequest;
use App\Models\SubscriptionPlan;

class ChangePlanRequest extends ApiRequest
{
    private ?SubscriptionPlan $targetPlan = null;

    protected function prepareForValidation(): void
    {
        $this->targetPlan = SubscriptionPlan::where('slug', $this->input('plan_slug'))->first();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allowedMethods = 'chargily,baridimob,cash,delivery,paypal,redotpay,wise,wise_manual,stripe,payoneer,noest';

        return [
            'plan_slug' => ['required', 'string', 'exists:subscription_plans,slug'],
            'billing' => ['required', 'in:monthly,yearly'],
            'coupon' => ['nullable', 'string', 'max:50'],
            'payment_method' => [
                $this->targetPlan && ! $this->targetPlan->isFree() ? 'required' : 'nullable',
                'string',
                "in:$allowedMethods",
            ],
        ];
    }
}
