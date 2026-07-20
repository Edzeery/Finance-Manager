<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Subscription\ChangePlanRequest;
use App\Http\Requests\Api\Subscription\ValidateCouponRequest;
use App\Http\Resources\SubscriptionPlanResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService,
    ) {}

    public function plans(): JsonResource
    {
        $plans = SubscriptionPlan::active()->public()->with('planFeatures')->orderBy('sort_order')->get();

        return SubscriptionPlanResource::collection($plans);
    }

    public function current(Request $request): JsonResponse|JsonResource
    {
        $workspace = $request->user()->currentWorkspace;

        if (! $workspace) {
            return response()->json(['message' => __('messages.no_workspace_selected')], 400);
        }

        $subscription = $workspace->owner()?->first()?->activeSubscription()?->loadMissing('plan');

        if (! $subscription) {
            return response()->json(['message' => __('messages.no_active_subscription')], 404);
        }

        return new SubscriptionResource($subscription);
    }

    public function changePlan(ChangePlanRequest $request): JsonResponse|JsonResource
    {
        $workspace = $request->user()->currentWorkspace;

        if (! $workspace) {
            return response()->json(['message' => __('messages.no_workspace_selected')], 400);
        }

        $currentPlan = $workspace->activePlan();
        $targetPlan = $this->subscriptionService->getPlan($request->plan_slug);

        if (! $targetPlan) {
            return response()->json(['message' => __('messages.plan_not_found')], 404);
        }

        if ($currentPlan && $targetPlan->sort_order < $currentPlan->sort_order) {
            $check = $this->subscriptionService->canDowngrade($workspace, $targetPlan);
            if (! $check['can_downgrade']) {
                return response()->json(['message' => implode(' ', $check['errors'])], 422);
            }
        }

        $result = $this->subscriptionService->changePlan(
            $workspace,
            $request->plan_slug,
            $request->billing,
            $request->coupon,
            $request->payment_method,
        );

        if (! $result['subscription']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return new SubscriptionResource($result['subscription']->load('plan'));
    }

    public function cancel(Request $request): JsonResponse
    {
        $workspace = $request->user()->currentWorkspace;

        if (! $workspace) {
            return response()->json(['message' => __('messages.no_workspace_selected')], 400);
        }

        $subscription = $workspace->owner()?->first()?->activeSubscription();

        if (! $subscription || $subscription->isExpired()) {
            return response()->json(['message' => __('messages.no_active_subscription')], 404);
        }

        $this->subscriptionService->cancelSubscription($subscription);

        return response()->json(['message' => __('messages.subscription_canceled')]);
    }

    public function validateCoupon(ValidateCouponRequest $request): JsonResponse
    {
        $coupon = $this->subscriptionService->validateCoupon($request->code, $request->amount);

        if (! $coupon) {
            return response()->json(['valid' => false, 'message' => __('messages.coupon_invalid')], 404);
        }

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $coupon->applyDiscount($request->amount ?? 0),
        ]);
    }
}
