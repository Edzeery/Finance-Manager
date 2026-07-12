<?php
// app/Services/SubscriptionActivationService.php
namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Events\SubscriptionActivated;
use App\Exceptions\SubscriptionException;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\TaxRate;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class SubscriptionActivationService
{
    public function __construct(
        private readonly InvoiceNumberGenerator $invoiceNumberGenerator,
    ) {}

    public function activateFromPayment(Payment $payment, SubscriptionPlan $plan, ?string $billingPeriod = null): Subscription
    {
        return DB::transaction(function () use ($payment, $plan, $billingPeriod) {
            $payment->fresh();
            $plan->fresh();

            $period = $billingPeriod ?? ($payment->metadata['billing_period'] ?? 'monthly');
            $planPrice = $plan->activePrices()->forPeriod($period)->first();
            $planCurrency = $planPrice?->currency;
            $expectedPrice = (float) ($planPrice?->price ?? 0);

            if (!$payment->isCompleted()) {
                throw new SubscriptionException('Cannot activate: payment is not completed');
            }

            if (!$payment->coupon_id) {
                $compareAmount = $payment->currency !== $planCurrency
                    ? CurrencyHelper::convert($payment->amount, $payment->currency, $planCurrency)
                    : $payment->amount;

                if ($compareAmount < $expectedPrice * 0.99) {
                    logger()->channel('payments')->warning('Payment amount mismatch on activation', [
                        'payment_id' => $payment->id,
                        'payment_amount' => $payment->amount,
                        'payment_currency' => $payment->currency,
                        'expected_price' => $expectedPrice,
                        'plan_currency' => $planCurrency,
                        'plan_id' => $plan->id,
                        'plan_slug' => $plan->slug,
                    ]);
                    throw new SubscriptionException('Payment amount does not match plan price');
                }
            }

            $workspace = $payment->workspace()->lockForUpdate()->first();
            $endsAt = $period === 'yearly' ? now()->addYear() : now()->addMonth();

            if ($payment->subscription_id) {
                $existingSub = Subscription::withoutWorkspace()->lockForUpdate()->find($payment->subscription_id);
                if ($existingSub && $existingSub->status === SubscriptionStatus::PastDue) {
                    $currentSub = $workspace->allSubscriptions()
                        ->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Trialing->value])
                        ->where('id', '!=', $existingSub->id)
                        ->lockForUpdate()
                        ->first();
                    $oldEndsAt = $currentSub?->ends_at;
                    if ($currentSub) {
                        $currentSub->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
                    }
                    $existingSub->update([
                        'status' => SubscriptionStatus::Active->value,
                        'starts_at' => now(),
                        'ends_at' => $oldEndsAt ?: $endsAt,
                        'payment_method' => $payment->method,
                        'payment_reference' => $payment->reference,
                        'auto_renew' => !OnboardingService::isManual($payment->method),
                        'plan_price_amount' => $period === 'yearly' ? $plan->yearly_price : $plan->monthly_price,
                    ]);
                    $payment->update(['status' => PaymentStatus::CheckoutPaid, 'paid_at' => now()]);
                    if ($payment->coupon_id && $payment->coupon) {
                        $payment->coupon->markUsed();
                    }
                    $this->generateInvoice($existingSub, $workspace, $plan, $payment, $period);
                    SubscriptionActivated::dispatch($existingSub, $payment);
                    logger()->channel('subscriptions')->info('Subscription activated (from past_due)', [
                        'subscription_id' => $existingSub->id,
                        'workspace_id' => $workspace->id,
                        'plan_id' => $plan->id,
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                    ]);
                    return $existingSub;
                }
                if ($existingSub && $existingSub->isActive()) {
                    return $existingSub;
                }
            }

            $currentSub = $workspace->allSubscriptions()
                ->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Trialing->value])
                ->lockForUpdate()
                ->first();
            if ($currentSub) {
                $currentSub->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
            }

            $subscription = $workspace->allSubscriptions()->create([
                'user_id' => $payment->user_id,
                'subscription_plan_id' => $plan->id,
                'status' => SubscriptionStatus::Active->value,
                'starts_at' => now(),
                'ends_at' => $endsAt,
                'payment_method' => $payment->method,
                'payment_reference' => $payment->reference,
                'auto_renew' => !OnboardingService::isManual($payment->method),
                'billing_period' => $period,
                'plan_price_amount' => $period === 'yearly' ? $plan->yearly_price : $plan->monthly_price,
            ]);

            $payment->update([
                'subscription_id' => $subscription->id,
                'status' => PaymentStatus::CheckoutPaid,
                'paid_at' => now(),
            ]);

            if ($payment->coupon_id && $payment->coupon) {
                $payment->coupon->markUsed();
            }

            $this->generateInvoice($subscription, $workspace, $plan, $payment, $period);
            SubscriptionActivated::dispatch($subscription, $payment);

            logger()->channel('subscriptions')->info('Subscription activated', [
                'subscription_id' => $subscription->id,
                'workspace_id' => $workspace->id,
                'plan_id' => $plan->id,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'period' => $period,
            ]);

            return $subscription;
        });
    }

    public function generateInvoice(
        Subscription $subscription,
        Workspace $workspace,
        SubscriptionPlan $plan,
        ?Payment $payment = null,
        string $billingPeriod = 'monthly',
        ?float $overrideGatewayFee = null,
        ?float $overrideTaxAdded = null,
        ?float $overrideTaxDisclosed = null,
        float $prorationCredit = 0,
    ): Invoice {
        if ($payment) {
            $existing = Invoice::withoutWorkspace()
                ->where('subscription_id', $subscription->id)
                ->where('payment_id', $payment->id)
                ->first();
            if ($existing) {
                return $existing;
            }

            $subtotal = $payment->original_amount;
            $discount = $payment->discount_amount;
            $currency = $payment->currency;
            $gatewayFee = $overrideGatewayFee ?? $payment->gateway_fee;
            $taxAdded = $overrideTaxAdded ?? $payment->tax_added;
            $taxDisclosed = $overrideTaxDisclosed ?? $payment->tax_disclosed;
        } else {
            $planPrice = $plan->activePrices()->forPeriod($billingPeriod)->first();
            $subtotal = (float) ($planPrice?->price ?? 0);
            $discount = 0;
            $currency = $planPrice?->currency ?? 'USD';
            $gatewayFee = $overrideGatewayFee ?? 0;
            $taxAdded = $overrideTaxAdded ?? 0;
            $taxDisclosed = $overrideTaxDisclosed ?? 0;
        }

        $afterDiscount = max($subtotal - $discount, 0);

        $total = $afterDiscount + $gatewayFee + $taxAdded;

        return Invoice::create([
            'workspace_id' => $workspace->id,
            'subscription_id' => $subscription->id,
            'user_id' => $workspace->owner()->first()?->id,
            'coupon_id' => $payment?->coupon_id,
            'payment_id' => $payment?->id,
            'number' => $this->invoiceNumberGenerator->generate(),
            'status' => $payment && $payment->isCompleted() ? \App\Enums\InvoiceStatus::Paid->value : \App\Enums\InvoiceStatus::Draft->value,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'gateway_fee' => $gatewayFee,
            'tax_added' => $taxAdded,
            'tax_disclosed' => $taxDisclosed,
            'proration_credit' => $prorationCredit,
            'total' => $total,
            'currency' => $currency,
            'billing_period' => $billingPeriod,
            'period_start' => now(),
            'period_end' => $billingPeriod === 'yearly' ? now()->addYear() : now()->addMonth(),
            'due_at' => $payment ? now() : now()->addDays(7),
        ]);
    }
}
