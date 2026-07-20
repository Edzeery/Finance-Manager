<?php

namespace App\Http\Controllers\Account;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\CurrencyHelper;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(SubscriptionService $subscriptionService)
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        $subscription = $user->activeSubscription() ?? $workspace?->owner()?->first()?->activeSubscription();

        $allSubscriptions = Subscription::withoutWorkspace()
            ->where('user_id', $user->id)
            ->with('plan', 'workspace')
            ->latest('starts_at')
            ->get();

        $subscriptionIds = $allSubscriptions->pluck('id');

        $payments = $subscriptionIds->isNotEmpty()
            ? Payment::withoutWorkspace()
                ->whereIn('subscription_id', $subscriptionIds)
                ->latest()
                ->take(10)
                ->get()
            : collect();

        $hasSubscriptionHistory = $allSubscriptions->count() > 1
            || ($allSubscriptions->isNotEmpty() && ! in_array($allSubscriptions->first()->status, [SubscriptionStatus::Active, SubscriptionStatus::Trialing], true));

        $plans = SubscriptionPlan::active()
            ->public()
            ->with('planFeatures')
            ->when($hasSubscriptionHistory, fn ($q) => $q->where('is_free', false))
            ->orderBy('sort_order')
            ->get();

        $userCurrency = $user->currency ?? config('finance.currency', 'USD');

        $paymentMethods = PaymentMethod::active()->public()->byCurrency($userCurrency)->ordered()->get()->map(fn ($m) => [
            'id' => $m->key,
            'name' => __("onboarding.method_{$m->key}") !== "onboarding.method_{$m->key}"
                ? __("onboarding.method_{$m->key}")
                : $m->name,
        ])->toArray();

        $pendingPayment = $subscriptionIds->isNotEmpty()
            ? Payment::withoutWorkspace()
                ->whereIn('subscription_id', $subscriptionIds)
                ->where('status', PaymentStatus::CheckoutPending->value)
                ->latest()
                ->first()
            : null;

        $isOwner = $workspace && $user->isWorkspaceOwner($workspace);

        return view('account.subscriptions', compact(
            'subscription',
            'payments',
            'plans',
            'pendingPayment',
            'userCurrency',
            'workspace',
            'isOwner',
            'hasSubscriptionHistory',
            'paymentMethods',
            'allSubscriptions',
        ));
    }

    public function cancelPayment(Request $request, Payment $payment)
    {
        $user = auth()->user();

        if ($payment->user_id !== $user->id) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.unauthorized'));
        }

        if (! $payment->isPending()) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.payment_not_pending'));
        }

        $payment->update([
            'status' => PaymentStatus::CheckoutCanceled->value,
            'canceled_at' => now(),
        ]);

        return redirect()->route('account.subscriptions')
            ->with('success', __('messages.payment_cancelled'));
    }

    public function resumeSubscription()
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        if (! $workspace || ! $user->isWorkspaceOwner($workspace)) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.unauthorized'));
        }

        $subscription = $workspace->owner()?->first()?->activeSubscription();
        if (! $subscription) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.no_subscription'));
        }

        if (! $subscription->canceled_at || ! $subscription->isOnGrace()) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.subscription_not_cancelled'));
        }

        $subscription->update([
            'status' => SubscriptionStatus::Active->value,
            'canceled_at' => null,
        ]);

        return redirect()->route('account.subscriptions')
            ->with('success', __('messages.subscription_resumed'));
    }

    public function updatePaymentMethod(Request $request)
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        if (! $workspace || ! $user->isWorkspaceOwner($workspace)) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.unauthorized'));
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:chargily,baridimob,cash,delivery,paypal,redotpay,wise,wise_manual,stripe,payoneer,noest'],
        ]);

        $subscription = $workspace->owner()?->first()?->activeSubscription();
        if (! $subscription) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.no_active_subscription'));
        }

        $subscription->update(['payment_method' => $validated['payment_method']]);

        return redirect()->route('account.subscriptions')
            ->with('success', __('messages.payment_method_updated'));
    }

    public function feeBreakdown(Request $request, SubscriptionService $subscriptionService)
    {
        $data = $request->validate([
            'plan_slug' => 'required|string|exists:subscription_plans,slug',
            'billing' => 'required|in:monthly,yearly',
            'payment_method' => 'required|string',
            'coupon' => 'nullable|string|max:50',
        ]);

        $plan = SubscriptionPlan::where('slug', $data['plan_slug'])->firstOrFail();

        $priceUsd = $data['billing'] === 'yearly'
            ? (float) $plan->yearly_price
            : (float) $plan->monthly_price;

        $discountUsd = 0;
        if (! empty($data['coupon'])) {
            $coupon = $subscriptionService->validateCoupon($data['coupon'], $priceUsd);
            if ($coupon) {
                $discountUsd = $coupon->applyDiscount($priceUsd);
            }
        }

        $finalUsd = max($priceUsd - $discountUsd, 0);

        $gatewayFeeUsd = 0.0;
        $taxAddedUsd = 0.0;
        $taxDisclosedUsd = 0.0;

        $pmModel = PaymentMethod::where('key', $data['payment_method'])->first();
        if ($pmModel) {
            $links = $pmModel->taxRates()->withPivot('charge_type')->get();
            foreach ($links as $taxRate) {
                $calculated = $taxRate->calculateForAmount($finalUsd);
                match ($taxRate->pivot->charge_type) {
                    'gateway_fee' => $gatewayFeeUsd += $calculated,
                    'tax_added' => $taxAddedUsd += $calculated,
                    'tax_disclosed' => $taxDisclosedUsd += $calculated,
                };
            }
        }

        $userCurrency = auth()->user()->currency ?? config('finance.currency', 'USD');

        return response()->json([
            'original_usd' => $priceUsd,
            'original' => CurrencyHelper::fromUsd($priceUsd, $userCurrency),
            'discount_usd' => $discountUsd,
            'discount' => CurrencyHelper::fromUsd($discountUsd, $userCurrency),
            'final_after_discount_usd' => $finalUsd,
            'gateway_fee_usd' => $gatewayFeeUsd,
            'gateway_fee' => CurrencyHelper::fromUsd($gatewayFeeUsd, $userCurrency),
            'tax_added_usd' => $taxAddedUsd,
            'tax_added' => CurrencyHelper::fromUsd($taxAddedUsd, $userCurrency),
            'tax_disclosed_usd' => $taxDisclosedUsd,
            'tax_disclosed' => CurrencyHelper::fromUsd($taxDisclosedUsd, $userCurrency),
            'total_usd' => $finalUsd + $gatewayFeeUsd + $taxAddedUsd,
            'total' => CurrencyHelper::fromUsd($finalUsd + $gatewayFeeUsd + $taxAddedUsd, $userCurrency),
            'currency' => $userCurrency,
        ]);
    }
}
