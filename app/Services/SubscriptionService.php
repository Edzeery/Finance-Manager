<?php
// app\Services\SubscriptionService.php
namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Services\OnboardingService;
use App\Services\Payments\GatewayManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SubscriptionService
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly InvoiceNumberGenerator $invoiceNumberGenerator,
        private readonly GatewayManager $gatewayManager,
        private readonly SubscriptionProrationService $prorationService,
        private readonly SubscriptionActivationService $activationService,
        private readonly SubscriptionCancellationService $cancellationService,
    ) {}

    public function validateCoupon(?string $code, ?float $amount = null, ?string $paymentMethod = null): ?Coupon
    {
        if (!$code) {
            return null;
        }

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return null;
        }

        if (!$coupon->isValid()) {
            return null;
        }

        if ($amount && $coupon->min_amount && $amount < $coupon->min_amount) {
            return null;
        }

        if ($paymentMethod) {
            if ($coupon->paymentMethods()->exists()) {
                $pm = \App\Models\PaymentMethod::where('key', $paymentMethod)->first();
                if ($pm && !$coupon->paymentMethods()->where('payment_method_id', $pm->id)->exists()) {
                    return null;
                }
            }
        }

        return $coupon;
    }

    public function hasPendingPayment(Workspace $workspace): bool
    {
        return Payment::withoutWorkspace()
            ->where('workspace_id', $workspace->id)
            ->where('status', PaymentStatus::CheckoutPending->value)
            ->exists();
    }

    /**
     * إلغاء أي اشتراكات past_due سابقة لنفس المساحة مع دفعاتها المعلقة
     * يُستدعى قبل إنشاء اشتراك جديد لمنع تراكم السجلات المعلقة
     */
    public function cancelStalePendingSubscriptions(Workspace $workspace): void
    {
        $staleSubs = Subscription::withoutWorkspace()
            ->where('workspace_id', $workspace->id)
            ->where('status', SubscriptionStatus::PastDue->value)
            ->lockForUpdate()
            ->get();

        foreach ($staleSubs as $stale) {
            Payment::withoutWorkspace()
                ->where('subscription_id', $stale->id)
                ->where('status', PaymentStatus::CheckoutPending->value)
                ->update(['status' => PaymentStatus::CheckoutCanceled, 'canceled_at' => now()]);

            $stale->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
        }
    }

    public function calculateProration(Workspace $workspace, SubscriptionPlan $targetPlan, string $billingPeriod): array
    {
        return $this->prorationService->calculateProration($workspace, $targetPlan, $billingPeriod);
    }

    public function activateFromPayment(Payment $payment, SubscriptionPlan $plan, ?string $billingPeriod = null): Subscription
    {
        return $this->activationService->activateFromPayment($payment, $plan, $billingPeriod);
    }

    public function getPlan(string $slug): ?SubscriptionPlan
    {
        return SubscriptionPlan::where('slug', $slug)->first();
    }

    public function getAvailablePlans(?SubscriptionPlan $current = null): array
    {
        return SubscriptionPlan::active()->public()->orderBy('sort_order')->get()->toArray();
    }

    public function canDowngrade(Workspace $workspace, SubscriptionPlan $targetPlan, ?Subscription $subscription = null): array
    {
        $errors = [];

        if ($workspace->userCount() > $targetPlan->max_users) {
            $errors[] = __('messages.downgrade_too_many_users', [
                'count' => $workspace->userCount(),
                'limit' => $targetPlan->max_users,
            ]);
        }

        $subscription ??= $workspace->owner()?->first()?->activeSubscription();

        if ($subscription && $subscription->plan && !$subscription->plan->is_free) {
            $currentPrice = $subscription->billing_period === 'yearly'
                ? $subscription->plan->yearly_price
                : $subscription->plan->monthly_price;
            $targetPrice = $targetPlan->monthly_price;

            if ($currentPrice > 0) {
                $discountPercent = (($currentPrice - $targetPrice) / $currentPrice) * 100;
                $minDiscount = (int) config('finance.downgrade_min_discount_percent', 10);

                if ($discountPercent < $minDiscount) {
                    $errors[] = __('subscription.downgrade_min_discount', [
                        'min' => $minDiscount,
                        'current' => number_format($discountPercent, 1),
                    ]);
                }
            }
        }

        return [
            'can_downgrade' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Change plan with proration support. Returns an array with subscription and optional redirect info.
     *
     * @return array{subscription: ?Subscription, payment: ?Payment, redirect_url: ?string, message: string}
     */
    public function changePlan(Workspace $workspace, string $planSlug, string $billingPeriod = 'monthly', ?string $couponCode = null, ?string $paymentMethod = null): array
    {
        $plan = $this->getPlan($planSlug);
        if (!$plan) {
            return [
                'subscription' => null,
                'payment' => null,
                'redirect_url' => null,
                'message' => 'Plan not found.',
            ];
        }

        if ($this->hasPendingPayment($workspace)) {
            return [
                'subscription' => null,
                'payment' => null,
                'redirect_url' => null,
                'message' => __('settings.pending_payment_toast'),
            ];
        }

        if ($plan->is_free) {
            $hasHistory = Subscription::withoutWorkspace()
                ->where('workspace_id', $workspace->id)
                ->whereNotNull('starts_at')
                ->exists();

            if ($hasHistory) {
                return [
                    'subscription' => null,
                    'payment' => null,
                    'redirect_url' => null,
                    'message' => __('messages.personal_plan_new_users_only'),
                ];
            }
        }

        return DB::transaction(function () use ($workspace, $plan, $billingPeriod, $couponCode, $paymentMethod) {
            // [جديد] إلغاء أي اشتراكات past_due سابقة لنفس المساحة
            $this->cancelStalePendingSubscriptions($workspace);

            $currentSub = $workspace->owner()?->first()?->activeSubscription();
            $oldPlanName = $currentSub?->plan?->name;

            $proration = null;
            $price = $billingPeriod === 'yearly' ? $plan->yearly_price : $plan->monthly_price;

            $isPlanChange = $currentSub && $currentSub->isActive() && !$currentSub->plan->isFree()
                && $plan->slug !== $currentSub->plan->slug;

            if ($isPlanChange) {
                $proration = $this->calculateProration($workspace, $plan, $billingPeriod);
                if ($proration['remaining_days'] > 0) {
                    $price = $proration['amount_due'];
                }
            }

            $endsAt = $billingPeriod === 'yearly' ? now()->addYear() : now()->addMonth();

            $subscription = $workspace->allSubscriptions()->create([
                'user_id' => $workspace->owner()?->first()?->id,
                'subscription_plan_id' => $plan->id,
                'status' => $plan->is_free ? SubscriptionStatus::Active->value : SubscriptionStatus::PastDue->value,
                'starts_at' => now(),
                'ends_at' => null, // past_due: actual ends_at set after payment success
                'payment_method' => $paymentMethod,
                'auto_renew' => $price > 0 && !OnboardingService::isManual($paymentMethod),
                'billing_period' => $billingPeriod,
                'plan_price_amount' => $price,
            ]);

            // إذا الخطة مجانية
            if ($plan->is_free) {
                $this->cancellationService->cancelCurrentSubscription($currentSub, $subscription);
                $this->sendPlanChangeEmail($subscription, $currentSub, $plan, $isPlanChange, $oldPlanName);
                return [
                    'subscription' => $subscription,
                    'payment' => null,
                    'redirect_url' => null,
                    'message' => __('settings.plan_changed'),
                ];
            }

            // إذا السعر 0 أو أقل (ترقية مجانية / تخفيض برصيد) — فعّل مباشرة بدون دفعة
            if ($price <= 0) {
                $oldEndsAt = $currentSub?->ends_at;
                $this->cancellationService->cancelCurrentSubscription($currentSub, $subscription);
                $subscription->update([
                    'status' => SubscriptionStatus::Active->value,
                    'ends_at' => $isPlanChange && $oldEndsAt ? $oldEndsAt : $endsAt,
                ]);
                $creditAmount = $price < 0 ? abs($price) : ($proration['remaining_value'] ?? 0);
                $this->activationService->generateInvoice(
                    $subscription, $workspace, $plan, null, $billingPeriod, 0, 0, 0,
                    prorationCredit: $creditAmount
                );
                $this->sendPlanChangeEmail($subscription, $currentSub, $plan, $isPlanChange, $oldPlanName);
                return [
                    'subscription' => $subscription,
                    'payment' => null,
                    'redirect_url' => null,
                    'message' => __('settings.plan_activated'),
                ];
            }

            // إنشاء الدفعة (السعر > 0)
            if ($price > 0 && $paymentMethod) {
                $payment = $this->paymentService->chargeForPlan(
                    workspace: $workspace,
                    plan: $plan,
                    billingPeriod: $billingPeriod,
                    couponCode: $couponCode,
                    paymentMethod: $paymentMethod,
                    userId: $workspace->owner()?->first()?->id,
                    overrideAmount: $isPlanChange ? $price : null,
                    overrideOriginalAmount: $isPlanChange ? ($billingPeriod === 'yearly' ? $plan->yearly_price : $plan->monthly_price) : null,
                    prorationRemainingValue: $isPlanChange ? max($proration['remaining_value'] ?? 0, 0) : 0,
                );

                $payment->update(['subscription_id' => $subscription->id]);

                if ($payment->isCompleted()) {
                    $oldEndsAt = $currentSub?->ends_at;
                    $this->cancellationService->cancelCurrentSubscription($currentSub, $subscription);
                $subscription->update([
                    'status' => SubscriptionStatus::Active->value,
                    'ends_at' => $isPlanChange && $oldEndsAt ? $oldEndsAt : $endsAt,
                ]);
                $this->activationService->generateInvoice(
                        $subscription, $workspace, $plan, $payment, $billingPeriod,
                        prorationCredit: $isPlanChange ? max($proration['remaining_value'] ?? 0, 0) : 0
                    );
                    $this->sendPlanChangeEmail($subscription, $currentSub, $plan, $isPlanChange, $oldPlanName);
                }

                $gateway = $this->gatewayManager->driver($paymentMethod);
                $result = $gateway->charge([
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'payment_id' => $payment->id,
                    'user_id' => $workspace->owner()?->first()?->id,
                    'workspace_id' => $workspace->id,
                ]);

                if ($result->success || $result->isPending()) {
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
                        // لا تُفعّل الاشتراك — قيد الانتظار
                    } elseif (OnboardingService::isAutoComplete($paymentMethod) && $payment->transaction_id) {
                        $payment->update(['status' => PaymentStatus::CheckoutPaid, 'paid_at' => now()]);
                        $oldEndsAt = $currentSub?->ends_at;
                        $this->cancellationService->cancelCurrentSubscription($currentSub, $subscription);
                        $subscription->update([
                    'status' => SubscriptionStatus::Active->value,
                            'ends_at' => $isPlanChange && $oldEndsAt ? $oldEndsAt : $endsAt,
                        ]);
                        $this->activationService->generateInvoice(
                            $subscription, $workspace, $plan, $payment, $billingPeriod,
                            prorationCredit: $isPlanChange ? max($proration['remaining_value'] ?? 0, 0) : 0
                        );
                        $this->sendPlanChangeEmail($subscription, $currentSub, $plan, $isPlanChange, $oldPlanName);
                    }

                    return [
                        'subscription' => $subscription,
                        'payment' => $payment,
                        'redirect_url' => $result->redirectUrl,
                        'message' => __('settings.redirecting_to_payment'),
                    ];
                }

                $payment->update(['status' => PaymentStatus::CheckoutFailed, 'failed_at' => now()]);
                $subscription->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);

                return [
                    'subscription' => null,
                    'payment' => null,
                    'redirect_url' => null,
                    'message' => __('settings.payment_gateway_error'),
                ];
            }

            if ($price > 0 && !$paymentMethod) {
                $subscription->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
                return [
                    'subscription' => null,
                    'payment' => null,
                    'redirect_url' => null,
                    'message' => __('settings.payment_gateway_error'),
                ];
            }

            return [
                'subscription' => $subscription,
                'payment' => null,
                'redirect_url' => null,
                'message' => __('general.error'),
            ];
        });
    }

    public function cancelSubscription(Subscription $subscription, string $type = 'period_end'): void
    {
        $this->cancellationService->cancelSubscription($subscription, $type);
    }

    public function isTrialExpired(Workspace $workspace): bool
    {
        $sub = $workspace->owner()?->first()?->activeSubscription();
        if (!$sub) return true;

        if ($sub->plan->is_free) return false;

        return $sub->trial_ends_at && $sub->trial_ends_at->isPast() && $sub->status === SubscriptionStatus::Trialing;
    }

    public function transactionsThisMonth(Workspace $workspace): int
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $incomes = \App\Models\Income::withoutWorkspace()
            ->where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $expenses = \App\Models\Expense::withoutWorkspace()
            ->where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        return $incomes + $expenses;
    }

    public function maxTransactionsPerMonth(Workspace $workspace): int
    {
        $sub = $workspace->owner()?->first()?->activeSubscription();
        if (!$sub || !$sub->plan) return 0;

        return (int) ($sub->plan->getFeatureValue('transactions_per_month') ?? 1000);
    }

    public function canCreateTransaction(Workspace $workspace): bool
    {
        if ($this->isTrialExpired($workspace)) return false;

        $current = $this->transactionsThisMonth($workspace);
        $max = $this->maxTransactionsPerMonth($workspace);

        return $current < $max;
    }

    public function canCreateWorkspace(User $user): bool
    {
        $count = $user->workspaces()->count();

        if ($count === 0) return true;

        $sub = $user->subscriptions()
            ->withoutWorkspace()
            ->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Trialing->value])
            ->latest()
            ->first();

        if (!$sub || !$sub->plan) return false;

        $max = $sub->plan->max_workspaces;

        return $max === null || $count < $max;
    }

    public function getActiveSubscriptionsCount(): int
    {
        return Subscription::withoutWorkspace()->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Trialing->value])->count();
    }

    public function getExpiredSubscriptionsCount(): int
    {
        return Subscription::withoutWorkspace()->where('status', SubscriptionStatus::Expired->value)->count();
    }

    private function sendPlanChangeEmail(Subscription $newSubscription, ?Subscription $oldSub, SubscriptionPlan $newPlan, bool $isPlanChange, ?string $oldPlanName): void
    {
        if (!$isPlanChange || !$oldSub || !$oldPlanName) {
            return;
        }

        $user = $newSubscription->user;
        if (!$user || !$user->email) {
            return;
        }

        $oldPrice = $oldSub->plan?->monthly_price ?? 0;
        $newPrice = $newPlan->monthly_price;

        $mailable = $newPrice > $oldPrice
            ? new \App\Mail\SubscriptionUpgraded($newSubscription, $oldPlanName)
            : new \App\Mail\SubscriptionDowngraded($newSubscription, $oldPlanName);

        Mail::to($user->email)->queue($mailable);
    }
}
