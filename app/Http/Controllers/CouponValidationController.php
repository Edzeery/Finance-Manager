<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;

class CouponValidationController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService,
    ) {}

    public function check(string $code, ?float $amount = null)
    {
        $coupon = $this->subscriptionService->validateCoupon($code, $amount);
        if (! $coupon) {
            return response()->json(['valid' => false, 'message' => __('messages.coupon_invalid')]);
        }

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $coupon->applyDiscount($amount ?? 0),
            'message' => __('messages.coupon_applied'),
        ]);
    }
}
