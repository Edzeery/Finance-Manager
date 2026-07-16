<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\OnboardingService;
use App\Services\Payments\GatewayManager;
use App\Services\PaymentService;
use App\Services\SubscriptionActivationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RenewSubscriptions extends Command
{
    protected $signature = 'subscriptions:renew';

    protected $description = 'Process auto-renewals for subscriptions with auto_renew enabled';

    public function handle(
        PaymentService $paymentService,
        GatewayManager $gatewayManager,
        SubscriptionActivationService $activationService,
    ): int {
        $renewed = 0;
        $skipped = 0;
        $failed = 0;

        Subscription::withoutWorkspace()
            ->where('auto_renew', true)
            ->where('status', SubscriptionStatus::Active->value)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now()->addDay())
            ->where('ends_at', '>', now()->subHours(6))
            ->chunk(50, function ($subscriptions) use ($paymentService, $gatewayManager, $activationService, &$renewed, &$skipped, &$failed) {
                foreach ($subscriptions as $subscription) {
                    $result = $this->processRenewal($subscription, $paymentService, $gatewayManager, $activationService);
                    match ($result) {
                        'renewed' => $renewed++,
                        'skipped' => $skipped++,
                        'failed' => $failed++,
                        default => null,
                    };
                }
            });

        $this->info("Renewed: {$renewed}, Skipped: {$skipped}, Failed: {$failed}");

        return self::SUCCESS;
    }

    private function processRenewal(
        Subscription $subscription,
        PaymentService $paymentService,
        GatewayManager $gatewayManager,
        SubscriptionActivationService $activationService,
    ): string {
        $paymentMethod = $subscription->payment_method;
        if (! $paymentMethod || OnboardingService::isManual($paymentMethod)) {
            return 'skipped';
        }

        $hasPendingPayment = Payment::withoutWorkspace()
            ->where('subscription_id', $subscription->id)
            ->where('status', PaymentStatus::CheckoutPending->value)
            ->exists();

        if ($hasPendingPayment) {
            return 'skipped';
        }

        $plan = $subscription->plan;
        if (! $plan || $plan->is_free) {
            return 'skipped';
        }

        $workspace = $subscription->workspace;
        if (! $workspace) {
            return 'skipped';
        }

        $billingPeriod = $subscription->billing_period ?? 'monthly';

        try {
            return DB::transaction(function () use ($subscription, $plan, $workspace, $billingPeriod, $paymentMethod, $paymentService, $gatewayManager, $activationService) {
                $payment = $paymentService->chargeForPlan(
                    workspace: $workspace,
                    plan: $plan,
                    billingPeriod: $billingPeriod,
                    couponCode: null,
                    paymentMethod: $paymentMethod,
                    userId: $subscription->user_id,
                );

                $payment->update(['subscription_id' => $subscription->id]);

                if ($payment->amount <= 0) {
                    $payment->update(['status' => PaymentStatus::CheckoutPaid, 'paid_at' => now()]);
                    $this->applyRenewalActivation($subscription, $workspace, $plan, $payment, $billingPeriod, $activationService);

                    return 'renewed';
                }

                if (OnboardingService::isAutoComplete($paymentMethod)) {
                    $gateway = $gatewayManager->driver($paymentMethod);
                    $result = $gateway->charge([
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'payment_id' => $payment->id,
                        'user_id' => $subscription->user_id,
                        'workspace_id' => $workspace->id,
                    ]);

                    if ($result->success && $payment->transaction_id) {
                        $payment->update(['status' => PaymentStatus::CheckoutPaid, 'paid_at' => now()]);
                        $this->applyRenewalActivation($subscription, $workspace, $plan, $payment, $billingPeriod, $activationService);

                        return 'renewed';
                    }

                    $payment->update(['status' => PaymentStatus::CheckoutFailed, 'failed_at' => now()]);

                    return 'failed';
                }

                $gateway = $gatewayManager->driver($paymentMethod);
                $result = $gateway->charge([
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'payment_id' => $payment->id,
                    'user_id' => $subscription->user_id,
                    'workspace_id' => $workspace->id,
                ]);

                if ($result->success || $result->isPending()) {
                    $payment->update([
                        'transaction_id' => $result->transactionId,
                        'gateway_reference' => $result->reference,
                        'gateway_payload' => $result->metadata,
                    ]);

                    if ($result->isPending()) {
                        return 'renewed';
                    }

                    $this->applyRenewalActivation($subscription, $workspace, $plan, $payment, $billingPeriod, $activationService);

                    return 'renewed';
                }

                $payment->update(['status' => PaymentStatus::CheckoutFailed, 'failed_at' => now()]);

                return 'failed';
            });
        } catch (\Throwable $e) {
            logger()->channel('subscriptions')->error('Auto-renewal failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return 'failed';
        }
    }

    private function applyRenewalActivation(
        Subscription $subscription,
        $workspace,
        $plan,
        Payment $payment,
        string $billingPeriod,
        SubscriptionActivationService $activationService,
    ): void {
        $oldEndsAt = $subscription->ends_at;
        $newStartsAt = $oldEndsAt ?? now();
        $newEndsAt = $billingPeriod === 'yearly' ? $newStartsAt->copy()->addYear() : $newStartsAt->copy()->addMonth();

        $subscription->update([
            'starts_at' => $newStartsAt,
            'ends_at' => $newEndsAt,
            'status' => SubscriptionStatus::Active->value,
        ]);

        $activationService->generateInvoice(
            $subscription, $workspace, $plan, $payment, $billingPeriod,
        );
    }
}
