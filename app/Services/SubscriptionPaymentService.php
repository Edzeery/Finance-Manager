<?php

namespace App\Services;

use App\Jobs\ActivateSubscription;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\Workspace;
use App\Services\Payments\GatewayManager;
use Illuminate\Support\Facades\DB;

class SubscriptionPaymentService
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly GatewayManager $gatewayManager,
    ) {}

    public function createOnlinePayment(
        Workspace $workspace,
        SubscriptionPlan $plan,
        string $billingPeriod,
        ?string $couponCode,
        string $paymentMethod,
        int $userId,
        string $successUrl,
        string $failureUrl,
    ): Payment {
        $gateway = $this->gatewayManager->driver($paymentMethod);

        // ✅ Validate before creating any payment record
        $validation = $gateway->validate([]);
        if ($validation->fails()) {
            throw new \RuntimeException($validation->message());
        }

        // ✅ Wrap Payment creation + gateway charge in a single transaction
        return DB::transaction(function () use ($workspace, $plan, $billingPeriod, $couponCode, $paymentMethod, $userId, $successUrl, $failureUrl, $gateway) {
            $payment = $this->paymentService->chargeForPlan(
                workspace: $workspace,
                plan: $plan,
                billingPeriod: $billingPeriod,
                couponCode: $couponCode,
                paymentMethod: $paymentMethod,
                userId: $userId,
            );

            if ($gateway->isOnline() && $payment->isPending()) {
                $result = $gateway->charge([
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'payment_id' => $payment->id,
                    'user_id' => $userId,
                    'workspace_id' => $workspace->id,
                    'success_url' => $successUrl,
                    'failure_url' => $failureUrl,
                    'webhook_url' => route('payment.webhook.'.$paymentMethod),
                ]);

                if (! $result->success) {
                    throw new \RuntimeException($result->message);
                }

                $payment->update([
                    'transaction_id' => $result->transactionId,
                    'gateway_reference' => $result->reference,
                    'gateway_payload' => $result->metadata,
                    'chargily_checkout_id' => $result->metadata['chargily_checkout_id'] ?? null,
                ]);
            }

            return $payment->fresh();
        });
    }

    public function activateFromWebhook(Payment $payment, string $billingPeriod = 'monthly'): void
    {
        $planId = $payment->user?->pending_plan_id;

        if (! $planId) {
            $planId = $payment->workspace?->owner()?->first()?->activeSubscription()?->subscription_plan_id;
        }

        if (! $planId) {
            return;
        }

        ActivateSubscription::dispatch($payment->id, $planId, $billingPeriod);
    }
}
