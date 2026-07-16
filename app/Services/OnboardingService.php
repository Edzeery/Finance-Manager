<?php

namespace App\Services;

use App\Enums\PaymentMethodType;
use App\Enums\PaymentStatus;
use App\Enums\PaymentVerificationStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Payments\GatewayManager;
use Illuminate\Support\Facades\DB;

class OnboardingService
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly SubscriptionService $subscriptionService,
        private readonly GatewayManager $gatewayManager,
        private readonly WorkspaceService $workspaceService,
    ) {}

    public function getAvailablePlans(): array
    {
        return SubscriptionPlan::active()->public()->orderBy('sort_order')->get()->toArray();
    }

    public function selectPlan(User $user, string $planSlug): bool
    {
        $plan = SubscriptionPlan::where('slug', $planSlug)
            ->where('is_active', true)
            ->where('is_public', true)
            ->first();
        if (! $plan) {
            return false;
        }

        $user->update(['pending_plan_id' => $plan->id]);

        return true;
    }

    private function ensureWorkspace(User $user): Workspace
    {
        $workspace = $user->currentWorkspace;

        if ($workspace) {
            return $workspace;
        }

        $workspace = $this->workspaceService->createForUser($user);
        $user->refresh();

        return $workspace;
    }

    public function processFreePlan(User $user): bool
    {
        $plan = SubscriptionPlan::find($user->pending_plan_id);
        if (! $plan || ! $plan->is_free) {
            return false;
        }

        $workspace = $this->ensureWorkspace($user);
        if ($this->subscriptionService->hasPendingPayment($workspace)) {
            return false;
        }

        return DB::transaction(function () use ($user, $plan) {
            $user = $user->fresh();
            $workspace = $this->ensureWorkspace($user);
            $workspace = $workspace->fresh();

            $currentSub = $workspace->allSubscriptions()->lockForUpdate()->first();
            if ($currentSub) {
                $currentSub->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
            }

            $workspace->allSubscriptions()->create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => SubscriptionStatus::Active->value,
                'starts_at' => now(),
                'ends_at' => now()->addYears(10),
                'payment_method' => 'free',
                'billing_period' => 'monthly',
                'plan_price_amount' => 0,
            ]);

            $user->markPlanConfirmed();

            return true;
        });
    }

    public function processTrialPlan(User $user): bool
    {
        $plan = SubscriptionPlan::find($user->pending_plan_id);
        if (! $plan || $plan->is_free || ! $plan->hasTrial()) {
            return false;
        }

        $workspace = $this->ensureWorkspace($user);
        if ($this->subscriptionService->hasPendingPayment($workspace)) {
            return false;
        }

        return DB::transaction(function () use ($user, $plan) {
            $user = $user->fresh();
            $workspace = $this->ensureWorkspace($user);
            $workspace = $workspace->fresh();

            $currentSub = $workspace->allSubscriptions()->lockForUpdate()->first();
            if ($currentSub) {
                $currentSub->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
            }

            $trialDays = $plan->trial_days ?? config('finance.trial_days', 30);
            $trialEndsAt = now()->addDays($trialDays);

            $workspace->allSubscriptions()->create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => SubscriptionStatus::Trialing->value,
                'starts_at' => now(),
                'ends_at' => $trialEndsAt,
                'trial_ends_at' => $trialEndsAt,
                'payment_method' => 'free',
                'billing_period' => 'monthly',
                'plan_price_amount' => 0,
                'auto_renew' => false,
            ]);

            $user->markPlanConfirmed();

            return true;
        });
    }

    private const FALLBACK_MANUAL = ['baridimob', 'redotpay', 'wise_manual', 'cash', 'delivery'];

    private const FALLBACK_ONLINE = ['chargily', 'paypal', 'stripe', 'wise', 'payoneer'];

    private const FALLBACK_AUTO_COMPLETE = ['noest'];

    public static function manualMethods(): array
    {
        return PaymentMethod::active()->byType(PaymentMethodType::Manual->value)->pluck('key')->toArray() ?: self::FALLBACK_MANUAL;
    }

    public static function onlineMethods(): array
    {
        return PaymentMethod::active()->byType(PaymentMethodType::Online->value)->pluck('key')->toArray() ?: self::FALLBACK_ONLINE;
    }

    public static function autoCompleteMethods(): array
    {
        return PaymentMethod::active()->byType(PaymentMethodType::AutoComplete->value)->pluck('key')->toArray() ?: self::FALLBACK_AUTO_COMPLETE;
    }

    public static function isManual(string $method): bool
    {
        return in_array($method, self::manualMethods());
    }

    public static function isOnline(string $method): bool
    {
        return in_array($method, self::onlineMethods());
    }

    public static function isAutoComplete(string $method): bool
    {
        return in_array($method, self::autoCompleteMethods());
    }

    /** @deprecated Use manualMethods() instead */
    public const MANUAL_METHODS = ['baridimob', 'redotpay', 'wise_manual', 'cash', 'delivery'];

    /** @deprecated Use onlineMethods() instead */
    public const ONLINE_METHODS = ['chargily', 'paypal', 'stripe', 'wise', 'payoneer'];

    /** @deprecated Use autoCompleteMethods() instead */
    public const AUTO_COMPLETE_METHODS = ['noest'];

    public function initiatePaidPlanPayment(
        User $user,
        string $paymentMethod,
        string $billingPeriod = 'monthly',
        ?string $couponCode = null,
        array $gatewayData = [],
    ): ?Payment {
        $plan = SubscriptionPlan::find($user->pending_plan_id);
        if (! $plan || $plan->is_free || $plan->hasTrial()) {
            return null;
        }

        $workspace = $this->ensureWorkspace($user);

        // ✅ التحقق المسبق: لا يتم إنشاء أي سجل Payment حتى يمر validation
        $gateway = $this->gatewayManager->driver($paymentMethod);
        $validation = $gateway->validate($gatewayData);
        if ($validation->fails()) {
            throw new \RuntimeException($validation->message());
        }

        Payment::withoutWorkspace()
            ->where('user_id', $user->id)
            ->where('status', PaymentStatus::CheckoutPending->value)
            ->update([
                'status' => PaymentStatus::CheckoutCanceled,
                'canceled_at' => now(),
            ]);
        logger()->channel('payments')->info('Cancelled stale pending payment(s) before new payment', [
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'plan_id' => $plan->id,
        ]);

        // ✅ Transaction واحدة: إنشاء Payment + استدعاء Gateway معاً
        // إذا فشل الـ Gateway يتم التراجع عن الـ Payment تلقائياً
        try {
            $result = DB::transaction(function () use ($workspace, $plan, $billingPeriod, $couponCode, $paymentMethod, $user, $gateway, $gatewayData) {
                $payment = $this->paymentService->chargeForPlan(
                    workspace: $workspace,
                    plan: $plan,
                    billingPeriod: $billingPeriod,
                    couponCode: $couponCode,
                    paymentMethod: $paymentMethod,
                    userId: $user->id,
                );

                if ($payment->amount <= 0) {
                    logger()->channel('payments')->info('Zero-amount payment completed', [
                        'payment_id' => $payment->id,
                        'plan_id' => $plan->id,
                        'coupon_id' => $payment->coupon_id,
                        'user_id' => $user->id,
                    ]);
                    $payment->update(['status' => PaymentStatus::CheckoutPaid, 'paid_at' => now()]);
                    $this->subscriptionService->activateFromPayment($payment, $plan, $billingPeriod);
                    $user->markPlanConfirmed();
                    session()->put('pending_payment_id', $payment->id);

                    return $payment;
                }

                $result = $gateway->charge(array_merge([
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                ], $gatewayData));

                if (! $result->success && ! $result->isPending()) {
                    throw new \RuntimeException($result->message);
                }

                $payment->update([
                    'transaction_id' => $result->transactionId,
                    'gateway_reference' => $result->reference,
                    'gateway_payload' => $result->metadata,
                    'chargily_checkout_id' => $result->metadata['chargily_checkout_id'] ?? null,
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'redirect_url' => $result->redirectUrl,
                        'gateway_response' => $result->metadata ?? [],
                    ]),
                ]);

                $payment->refresh();

                if ($result->isPending()) {
                    // لا تُفعّل الاشتراك — قيد الانتظار (مثلاً Noest بانتظار التسليم)
                } elseif (self::isAutoComplete($paymentMethod) && $payment->transaction_id) {
                    $payment->update(['status' => PaymentStatus::CheckoutPaid, 'paid_at' => now()]);
                    $this->subscriptionService->activateFromPayment($payment, $plan, $billingPeriod);
                    $user->markPlanConfirmed();
                }

                session()->put('pending_payment_id', $payment->id);

                return $payment;
            });
        } catch (\RuntimeException $e) {
            // Transaction تم rollback — لم يتم إنشاء سجل Payment
            throw $e;
        }

        return $result;
    }

    public function handlePaymentSuccess(User $user, Payment $payment): void
    {
        if ($payment->subscription_id) {
            return;
        }

        if (! $payment->isCompleted()) {
            logger()->channel('payments')->warning('handlePaymentSuccess called with non-completed payment', [
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'user_id' => $user->id,
            ]);

            return;
        }

        DB::transaction(function () use ($user, $payment) {
            $payment = Payment::lockForUpdate()->find($payment->id);
            if (! $payment) {
                return;
            }

            if ($payment->subscription_id) {
                $sub = Subscription::withoutWorkspace()->lockForUpdate()->find($payment->subscription_id);
                if ($sub && $sub->isActive()) {
                    $user->markPlanConfirmed();

                    return;
                }
            }

            $plan = SubscriptionPlan::find($user->pending_plan_id);
            if (! $plan) {
                logger()->channel('payments')->error('handlePaymentSuccess: pending plan not found', [
                    'user_id' => $user->id,
                    'pending_plan_id' => $user->pending_plan_id,
                ]);

                return;
            }

            $workspace = $this->ensureWorkspace($user);

            $this->subscriptionService->activateFromPayment($payment, $plan);

            $user->markPlanConfirmed();

            logger()->channel('payments')->info('Onboarding payment successful', [
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'plan_id' => $plan->id,
            ]);
        });
    }

    public function submitManualPaymentProof(Payment $payment, $receiptFile, string $transactionReference): void
    {
        DB::transaction(function () use ($payment, $receiptFile, $transactionReference) {
            if ($payment->isCompleted() || $payment->verification()->exists()) {
                throw new \RuntimeException(__('onboarding.payment_already_processed'));
            }

            $path = $receiptFile->store("receipts/{$payment->workspace_id}", 'local');

            $payment->verification()->create([
                'status' => PaymentVerificationStatus::Pending->value,
                'receipt_path' => $path,
                'transaction_reference' => $transactionReference,
            ]);
        });
    }

    public function completeOnboarding(User $user, array $workspaceData = []): void
    {
        $workspace = $this->ensureWorkspace($user);
        if (! empty($workspaceData['name'])) {
            $workspace->update(['name' => $workspaceData['name']]);
        }

        $user->markOnboardingComplete();
    }
}
