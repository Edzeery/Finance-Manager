<?php
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\PaymentService;
use App\Services\Payments\GatewayManager;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public ?Payment $payment = null;
    public ?string $errorMessage = null;
    public ?array $plan = null;
    public ?array $invoice = null;

    public function mount(Payment $payment): void
    {
        $user = auth()->user();

        if ($payment->user_id !== $user->id) {
            $this->errorMessage = __('onboarding.payment_not_found');
            return;
        }

        if (!$payment->isPending()) {
            $this->redirect(route('payment.retry', $payment), navigate: true);
            return;
        }

        $this->payment = $payment;
        $this->loadPaymentRelations();
    }

    private function loadPaymentRelations(): void
    {
        if (!$this->payment) return;

        $this->payment->loadMissing('subscription.plan', 'verification');

        $sub = $this->payment->subscription;
        if ($sub && $sub->relationLoaded('plan') && $sub->plan) {
            $this->plan = $sub->plan->toArray();
            $invoiceModel = $sub->invoices()->where('user_id', auth()->id())->latest()->first();
            if ($invoiceModel) {
                $this->invoice = $invoiceModel->toArray();
            }
        } elseif (auth()->user()->pending_plan_id) {
            $planModel = \App\Models\SubscriptionPlan::find(auth()->user()->pending_plan_id);
            if ($planModel) {
                $this->plan = $planModel->toArray();
            }
        }
    }

    public function retry(): void
    {
        if (!$this->payment) return;

        $this->payment->update(['status' => PaymentStatus::CheckoutCanceled, 'canceled_at' => now()]);

        $this->redirect(route('onboarding.payment', absolute: false), navigate: true);
    }

    public function continueWithGateway(): void
    {
        if (!$this->payment) return;

        $continueUrl = $this->payment->getContinueUrl();

        if ($continueUrl) {
            $this->js("window.location.href = '" . addslashes($continueUrl) . "'");
            return;
        }

        try {
            $gateway = app(GatewayManager::class)->driver($this->payment->method);
            $result = $gateway->charge([
                'amount' => $this->payment->amount,
                'currency' => $this->payment->currency,
                'payment_id' => $this->payment->id,
                'user_id' => auth()->id(),
                'workspace_id' => $this->payment->workspace_id,
            ]);

            if ($result->success) {
                $this->payment->update([
                    'transaction_id' => $result->transactionId,
                    'gateway_reference' => $result->reference,
                    'gateway_payload' => $result->metadata,
                    'metadata' => array_merge($this->payment->metadata ?? [], [
                        'redirect_url' => $result->redirectUrl,
                        'gateway_response' => $result->metadata ?? [],
                    ]),
                ]);

                if (OnboardingService::isOnline($this->payment->method) && $result->redirectUrl) {
                    $this->js("window.location.href = '" . addslashes($result->redirectUrl) . "'");
                    return;
                }

                $this->redirect(route('onboarding.manual-proof', ['payment' => $this->payment->id], absolute: false), navigate: true);
                return;
            }

            $this->redirect(route('onboarding.manual-proof', ['payment' => $this->payment->id], absolute: false), navigate: true);
        } catch (\Exception $e) {
            $this->redirect(route('onboarding.manual-proof', ['payment' => $this->payment->id], absolute: false), navigate: true);
        }
    }

    public function cancelPayment(): void
    {
        if (!$this->payment) return;

        $this->payment->update(['status' => PaymentStatus::CheckoutCanceled, 'canceled_at' => now()]);

        $this->redirect(route('onboarding.plan', absolute: false), navigate: true);
    }

    public function switchGateway(): void
    {
        $this->redirect(route('onboarding.payment', absolute: false), navigate: true);
    }

    public function methodLabel(?string $method): string
    {
        if (!$method) return '-';
        $key = 'onboarding.method_' . $method;
        $trans = __($key);
        return $trans !== $key ? $trans : ucfirst($method);
    }

    public function formatAmount(float $amount, ?string $currency = null): string
    {
        $currency = $currency ?? $this->payment?->currency ?? 'USD';
        return number_format($amount, 2) . ' ' . CurrencyHelper::symbol($currency);
    }

    public function displayPrice(float $usdAmount): string
    {
        $userCurrency = auth()->user()?->currency ?? config('finance.currency', 'DZD');
        $converted = CurrencyHelper::fromUsd($usdAmount, $userCurrency);
        return number_format($converted, 2) . ' ' . CurrencyHelper::symbol($userCurrency);
    }
}; ?>

<div>
    <div class="auth-card animate-fade-in">
        <div class="auth-logo">
            <div class="logo-icon">FM</div>
            <span class="logo-text">{{ __('general.app_name') }}</span>
            <span class="logo-sub">{{ __('onboarding.resume_payment') }}</span>
        </div>

        @if ($errorMessage)
            <div class="alert alert-danger py-2 small">{{ $errorMessage }}</div>
        @endif

        @if ($payment)
            <div class="text-center mb-3">
                <div class="text-warning" style="font-size:3rem"><i class="bi bi-clock-history"></i></div>
                <p class="mt-2">{{ __('onboarding.payment_pending_desc') }}</p>
            </div>

            <div class="info-section mb-3">
                <div class="info-section-header">
                    <i class="bi bi-credit-card-2-front me-1"></i>
                    {{ __('onboarding.payment_info') }}
                </div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.payment_method_label') }}</span>
                        <span class="info-value">{{ $this->methodLabel($payment->method) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.plan_name') }}</span>
                        <span class="info-value">{{ $plan['name'] ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.price') }}</span>
                        <span class="info-value">{{ $this->formatAmount((float) ($payment->original_amount ?: $payment->amount)) }}</span>
                    </div>
                    @if ($payment->discount_amount > 0)
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.coupon_discount') }}</span>
                        <span class="info-value text-success">-{{ $this->formatAmount((float) $payment->discount_amount) }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.payment_date') }}</span>
                        <span class="info-value">{{ $payment->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            @php
                $hasFees = $payment->gateway_fee > 0 || $payment->tax_added > 0 || $payment->tax_disclosed > 0;
            @endphp
            @if ($hasFees)
            <div class="price-breakdown mb-3">
                <div class="price-row original">
                    <span>{{ __('onboarding.plan_price') }}</span>
                    <span>{{ $this->formatAmount((float) ($payment->original_amount ?: $payment->amount)) }}</span>
                </div>
                @if ($payment->discount_amount > 0)
                <div class="price-row discount">
                    <span>{{ __('onboarding.coupon_discount') }}</span>
                    <span>-{{ $this->formatAmount((float) $payment->discount_amount) }}</span>
                </div>
                @endif
                @if ($payment->gateway_fee > 0)
                <div class="price-row fee">
                    <span>{{ __('onboarding.gateway_fee') }}</span>
                    <span>+{{ $this->formatAmount((float) $payment->gateway_fee) }}</span>
                </div>
                @endif
                @if ($payment->tax_added > 0)
                <div class="price-row fee">
                    <span>{{ __('onboarding.tax_added') }}</span>
                    <span>+{{ $this->formatAmount((float) $payment->tax_added) }}</span>
                </div>
                @endif
                @if ($payment->tax_disclosed > 0)
                <div class="price-row">
                    <span>{{ __('onboarding.tax_disclosed') }}</span>
                    <span>{{ $this->formatAmount((float) $payment->tax_disclosed) }}</span>
                </div>
                @endif
                <div class="price-divider"></div>
                <div class="price-row total">
                    <span>{{ __('onboarding.total') }}</span>
                    <span>{{ $this->formatAmount((float) $payment->amount) }}</span>
                </div>
            </div>
            @else
            <div class="info-row mb-3">
                <span class="info-label fw-bold">{{ __('onboarding.total') }}</span>
                <span class="info-value fw-bold">{{ $this->formatAmount((float) $payment->amount) }}</span>
            </div>
            @endif

            <div class="d-grid gap-2 mt-3">
                <button wire:click="continueWithGateway" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-right me-1"></i>{{ __('onboarding.continue_payment') }}
                </button>
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i>{{ __('onboarding.switch_gateway') }}
                </button>
                <button wire:click="cancelPayment" class="btn btn-outline-secondary btn-custom">
                    <i class="bi bi-x-lg me-1"></i>{{ __('onboarding.cancel_payment') }}
                </button>
            </div>
        @endif

        @include('livewire.pages.onboarding.partials.auth-footer')
    </div>
</div>
