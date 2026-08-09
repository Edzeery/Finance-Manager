<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\PaymentVerificationStatus;
use App\Enums\SubscriptionStatus;
use App\Events\SubscriptionActivated;
use App\Exceptions\PaymentException;
use App\Mail\PaymentFailed;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentVerification;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Workspace;
use App\Services\Payments\GatewayManager;
use App\Services\Payments\PaymentTransitionValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private readonly GatewayManager $gatewayManager,
        private readonly SubscriptionActivationService $activationService,
        private readonly PaymentTransitionValidator $transitionValidator,
    ) {}

    public function chargeForPlan(
        ?Workspace $workspace,
        SubscriptionPlan $plan,
        string $billingPeriod,
        ?string $couponCode,
        string $paymentMethod,
        ?int $userId = null,
        ?float $overrideAmount = null,
        ?float $overrideOriginalAmount = null,
        float $prorationRemainingValue = 0,
        ?float $overrideDiscountUsd = null,
    ): Payment {
        if (! $workspace || ! $workspace->id) {
            throw new PaymentException('Workspace is required to process payment.');
        }
        $priceUsd = $billingPeriod === 'yearly' ? $plan->yearly_price : $plan->monthly_price;

        $coupon = $this->resolveCoupon($couponCode, $priceUsd, $paymentMethod);

        $discountUsd = $coupon ? $coupon->applyDiscount($priceUsd) : 0;
        $finalUsd = max($priceUsd - $discountUsd, 0);

        if ($overrideAmount !== null) {
            if ($coupon && $priceUsd > 0 && $discountUsd > 0) {
                $ratio = min($overrideAmount / $priceUsd, 1);
                $proportionalDiscount = round($discountUsd * $ratio, 2);
                $finalUsd = max($overrideAmount - $proportionalDiscount, 0);
                $discountUsd = $proportionalDiscount;
            } else {
                $finalUsd = $overrideAmount;
            }
        }

        if ($overrideDiscountUsd !== null) {
            $discountUsd = $overrideDiscountUsd;
        }

        $priceUsd = $overrideOriginalAmount !== null ? $overrideOriginalAmount : $priceUsd;

        return DB::transaction(function () use (
            $workspace, $plan, $billingPeriod, $paymentMethod,
            $priceUsd, $finalUsd, $discountUsd, $coupon, $userId, $prorationRemainingValue
        ) {
            $gateway = $this->gatewayManager->driver($paymentMethod);
            $supported = $gateway->supportedCurrencies();
            $planCurrency = $plan->activePrices()->first()?->currency ?? 'USD';
            $payCurrency = in_array($planCurrency, $supported) ? $planCurrency : $supported[0];

            $amount = CurrencyHelper::convert($finalUsd, 'USD', $payCurrency);
            $originalAmount = CurrencyHelper::convert($priceUsd, 'USD', $payCurrency);
            $discountAmount = CurrencyHelper::convert($discountUsd, 'USD', $payCurrency);

            // --- حساب رسوم البوابة والضرائب من tax_rates المرتبطة ---
            $pmModel = PaymentMethod::where('key', $paymentMethod)->first();
            $gatewayFeeUsd = 0.0;
            $taxAddedUsd = 0.0;
            $taxDisclosedUsd = 0.0;

            if ($pmModel) {
                $allLinks = $pmModel->taxRates()->withPivot('charge_type')->get();
                foreach ($allLinks as $taxRate) {
                    $chargeType = $taxRate->pivot->charge_type;
                    $calculated = $taxRate->calculateForAmount($finalUsd);
                    match ($chargeType) {
                        'gateway_fee' => $gatewayFeeUsd += $calculated,
                        'tax_added' => $taxAddedUsd += $calculated,
                        'tax_disclosed' => $taxDisclosedUsd += $calculated,
                    };
                }
            }

            $gatewayFee = CurrencyHelper::convert($gatewayFeeUsd, 'USD', $payCurrency);
            $taxAdded = CurrencyHelper::convert($taxAddedUsd, 'USD', $payCurrency);
            $taxDisclosed = CurrencyHelper::convert($taxDisclosedUsd, 'USD', $payCurrency);

            // المبلغ المستحق = السعر بعد الخصم + رسوم البوابة + الضريبة المضافة
            $totalUsd = $finalUsd + $gatewayFeeUsd + $taxAddedUsd;
            $totalAmount = CurrencyHelper::convert($totalUsd, 'USD', $payCurrency);

            $metadata = [
                'billing_period' => $billingPeriod,
                'plan_slug' => $plan->slug,
                'coupon_code' => $coupon?->code,
            ];

            if ($prorationRemainingValue > 0) {
                $metadata['proration_remaining_value'] = $prorationRemainingValue;
                $metadata['is_prorated'] = true;
            }

            $payment = Payment::create([
                'workspace_id' => $workspace->id,
                'user_id' => $userId,
                'coupon_id' => $coupon?->id,
                'method_id' => $pmModel?->id,
                'amount' => $totalAmount,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'gateway_fee' => $gatewayFee,
                'tax_added' => $taxAdded,
                'tax_disclosed' => $taxDisclosed,
                'currency' => $payCurrency,
                'status' => PaymentStatus::CheckoutPending,
                'reference' => $this->generateReference($paymentMethod),
                'metadata' => $metadata,
            ]);

            return $payment;
        });
    }

    private function resolveCoupon(?string $code, ?float $amount = null, ?string $paymentMethod = null): ?Coupon
    {
        if (! $code) {
            return null;
        }

        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon || ! $coupon->isValid()) {
            return null;
        }
        if ($amount && $coupon->min_amount && $amount < $coupon->min_amount) {
            return null;
        }

        if ($paymentMethod) {
            $pm = PaymentMethod::where('key', $paymentMethod)->first();
            if (! $pm || ! $pm->is_active) {
                return null;
            }
            if ($coupon->paymentMethods()->exists()) {
                $allowed = $coupon->paymentMethods()->where('payment_method_id', $pm->id)->exists();
                if (! $allowed) {
                    return null;
                }
            }
        }

        return $coupon;
    }

    public function generateReference(string $method): string
    {
        $prefix = match ($method) {
            'chargily' => 'CHG',
            'edahabia' => 'EDH',
            'baridimob' => 'BCP',
            'ccp' => 'BCP',
            'cash' => 'CSH',
            'delivery' => 'DLV',
            'paypal' => 'PPL',
            'redotpay' => 'RDP',
            'noest' => 'NST',
            default => 'PAY',
        };

        return $prefix.'-'.strtoupper(Str::random(10));
    }

    public function verifyPayment(Payment $payment, PaymentVerificationStatus $status, ?int $adminId, ?string $notes = null, ?string $transactionReference = null): ?PaymentVerification
    {
        return DB::transaction(function () use ($payment, $status, $adminId, $notes, $transactionReference) {
            $existing = PaymentVerification::withoutWorkspace()->where('payment_id', $payment->id)->first();

            $data = [
                'verified_by' => $adminId,
                'status' => $status->value,
                'admin_notes' => $notes,
                'verified_at' => now(),
            ];

            if ($transactionReference) {
                $data['transaction_reference'] = $transactionReference;
            } elseif ($status === PaymentVerificationStatus::Approved && ! $existing?->transaction_reference) {
                $data['transaction_reference'] = 'ADMIN-'.strtoupper(Str::random(10));
            }

            if ($existing) {
                if ($existing->status === $status) {
                    return $existing;
                }

                $existing->update($data);
                $this->applyPaymentSideEffects($payment, $status);

                return $existing;
            }

            $data['workspace_id'] = $payment->workspace_id;
            $verification = $payment->verification()->create($data);
            $this->applyPaymentSideEffects($payment, $status);

            return $verification;
        });
    }

    public function applyPaymentSideEffects(Payment $payment, PaymentVerificationStatus $status): void
    {
        DB::transaction(function () use ($payment, $status) {
            $payment = Payment::withoutWorkspace()->lockForUpdate()->find($payment->id);
            if (! $payment) {
                return;
            }

            if ($status === PaymentVerificationStatus::Approved) {
                if ($payment->isCompleted()) {
                    return;
                }

                $this->transitionValidator->transition($payment, PaymentStatus::CheckoutPaid);

                if ($payment->subscription_id) {
                    $newSub = Subscription::withoutWorkspace()->lockForUpdate()->find($payment->subscription_id);

                    if ($newSub) {
                        $activeSub = Subscription::withoutWorkspace()
                            ->where('workspace_id', $payment->workspace_id)
                            ->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Trialing->value])
                            ->where('id', '!=', $newSub->id)
                            ->lockForUpdate()
                            ->first();

                        if ($activeSub) {
                            $activeSub->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
                        }

                        $wasPastDue = $newSub->status === SubscriptionStatus::PastDue;
                        $endsAt = $newSub->billing_period === 'yearly' ? now()->addYear() : now()->addMonth();
                        $newSub->update([
                            'status' => SubscriptionStatus::Active->value,
                            'ends_at' => $newSub->ends_at ?? $endsAt,
                        ]);

                        SubscriptionActivated::dispatch($newSub, $payment);

                        $prorationCredit = (float) ($payment->metadata['proration_remaining_value'] ?? 0);
                        if ($wasPastDue && $payment->workspace && $newSub->plan) {
                            $this->activationService->generateInvoice(
                                $newSub,
                                $payment->workspace,
                                $newSub->plan,
                                $payment,
                                $newSub->billing_period ?? 'monthly',
                                prorationCredit: $prorationCredit,
                            );
                        }
                    }
                }

                if ($payment->coupon_id && $payment->coupon) {
                    $payment->coupon->markUsed();
                }
            } elseif ($status === PaymentVerificationStatus::Rejected) {
                $this->transitionValidator->transition($payment, PaymentStatus::CheckoutFailed);
                if ($payment->subscription_id) {
                    $rejectedSub = Subscription::withoutWorkspace()->find($payment->subscription_id);
                    if ($rejectedSub && $rejectedSub->status === SubscriptionStatus::PastDue) {
                        $rejectedSub->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
                    }
                }

                if ($payment->user && $payment->user->email) {
                    Mail::to($payment->user->email)
                        ->queue(new PaymentFailed($payment));
                }
            }
        });
    }

    public function getPendingPaymentsCount(): int
    {
        return Payment::pending()->count();
    }

    public function getRevenueByPeriod(?string $start = null, ?string $end = null): float
    {
        return Payment::byStatus(PaymentStatus::CheckoutPaid)
            ->when($start, fn ($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn ($q) => $q->whereDate('created_at', '<=', $end))
            ->sum('amount');
    }
}
