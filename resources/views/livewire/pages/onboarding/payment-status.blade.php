<?php
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\Payments\GatewayManager;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public ?Payment $payment = null;
    public ?string $errorMessage = null;
    public ?array $plan = null;
    public ?array $invoice = null;
    public string $view = 'loading';

    public function mount(Payment $payment): void
    {
        $user = auth()->user();

        if ($payment->user_id !== $user->id) {
            $this->errorMessage = __('onboarding.payment_not_found');
            $this->view = 'error';
            return;
        }

        $this->payment = $payment;
        $this->loadPaymentRelations();
        $this->evaluateStatus();
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

    private function evaluateStatus(): void
    {
        $p = $this->payment;
        if (!$p) { $this->view = 'error'; return; }

        if ($p->isCompleted()) {
            $this->redirect(route('onboarding.setup', absolute: false), navigate: true);
            return;
        }

        if ($p->isPending() && !$p->verification) {
            $this->view = 'pending';
            return;
        }

        if ($p->status === PaymentStatus::CheckoutCanceled) {
            $this->view = 'canceled';
            return;
        }

        if ($p->isFailed()) {
            $this->view = 'failed';
            return;
        }

        if ($p->isPending() && $p->verification) {
            $this->view = 'pending_manual';
            return;
        }

        $this->view = 'error';
    }

    public function retry(): void
    {
        if (!$this->payment) return;

        if ($this->payment->isPending()) {
            $this->payment->update(['status' => PaymentStatus::CheckoutCanceled, 'canceled_at' => now()]);
            if ($this->payment->subscription && $this->payment->subscription->status === SubscriptionStatus::PastDue) {
                $this->payment->subscription->update(['status' => SubscriptionStatus::Canceled->value, 'canceled_at' => now()]);
            }
        }

        $this->payment->refresh();

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
                    'status' => PaymentStatus::CheckoutPending,
                    'canceled_at' => null,
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

    public function cancelPaymentAndChangePlan(): void
    {
        if (!$this->payment) return;

        $this->payment->update(['status' => PaymentStatus::CheckoutCanceled, 'canceled_at' => now()]);

        $this->redirect(route('onboarding.plan', absolute: false), navigate: true);
    }

    public function switchGateway(): void
    {
        $this->redirect(route('onboarding.plan', absolute: false), navigate: true);
    }

    public function manualProof(): void
    {
        if ($this->payment) {
            $this->redirect(route('onboarding.manual-proof', ['payment' => $this->payment->id], absolute: false), navigate: true);
        }
    }

    public function proceed(): void
    {
        session()->forget('pending_payment_id');
        $this->redirect(route('onboarding.setup', absolute: false), navigate: false);
    }

    public function statusBadgeClass(string $status): string
    {
        return match ($status) {
            PaymentStatus::CheckoutPending->value => 'bg-warning text-dark',
            PaymentStatus::CheckoutPaid->value => 'bg-success text-white',
            PaymentStatus::CheckoutFailed->value, PaymentStatus::CheckoutCanceled->value => 'bg-danger text-white',
            PaymentStatus::CheckoutExpired->value => 'bg-secondary text-white',
            default => 'bg-secondary text-white',
        };
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
        @if ($errorMessage && $view === 'pending')
            <div class="alert alert-danger py-2 small">{{ $errorMessage }}</div>
        @endif

        {{-- PENDING (resume) --}}
        @if ($view === 'pending')
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text">{{ __('general.app_name') }}</span>
                <span class="logo-sub">{{ __('onboarding.resume_payment') }}</span>
            </div>
            <div class="text-center mb-3">
                <x-status-icon domain="general" status="pending" set="bi" style="font-size:3rem" />
                <p class="mt-2">{{ __('onboarding.payment_pending_desc') ?? __('onboarding.payment_pending') }}</p>
            </div>

            @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')

            <div class="d-grid gap-2 mt-3">
                <button wire:click="continueWithGateway" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-right me-1"></i>{{ __('onboarding.continue_payment') ?? __('onboarding.resume_payment') }}
                </button>
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i>{{ __('onboarding.switch_gateway') ?? __('onboarding.cancel_change_method') }}
                </button>
                <button wire:click="cancelPaymentAndChangePlan" class="btn btn-outline-secondary btn-custom">
                    <i class="bi bi-x-lg me-1"></i>{{ __('onboarding.cancel_payment') }}
                </button>
            </div>

        {{-- WAITING --}}
        @elseif ($view === 'waiting')
            <div class="text-center">
                <div class="auth-logo">
                    <div class="logo-icon">FM</div>
                    <span class="logo-text">{{ __('general.app_name') }}</span>
                </div>
                <div class="my-4">
                    <div class="spinner-border text-accent my-4" role="spinner"></div>
                </div>
                <p>{{ __('onboarding.processing_payment') }}</p>
                <p class="small text-muted">{{ __('onboarding.payment_processing_hint') }}</p>
            </div>

        {{-- FAILED --}}
        @elseif ($view === 'failed')
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text">{{ __('general.app_name') }}</span>
            </div>
            <div class="text-center mb-3">
                <x-status-icon domain="general" status="failed" set="bi" style="font-size:3rem" />
            </div>
            <div class="alert alert-danger py-2 small">{{ $errorMessage ?? __('onboarding.payment_failed') }}</div>
            @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')
            <div class="d-grid gap-2 mt-3">
                <button wire:click="retry" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-repeat me-1"></i>{{ __('onboarding.retry_payment') }}
                </button>
                @if ($payment && OnboardingService::isManual($payment->method))
                <button wire:click="manualProof" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-upload me-1"></i>{{ __('onboarding.upload_manual_proof') }}
                </button>
                @endif
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i>{{ __('onboarding.switch_gateway') }}
                </button>
            </div>

        {{-- CANCELED --}}
        @elseif ($view === 'canceled')
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text">{{ __('general.app_name') }}</span>
            </div>
            <div class="text-center mb-3">
                <x-status-icon domain="general" status="cancelled" set="bi" style="font-size:3rem" />
            </div>
            <div class="alert alert-warning py-2 small text-center">{{ __('onboarding.payment_cancelled_desc') }}</div>

            {{-- Timeline --}}
            <div class="info-section mb-3">
                <div class="info-section-header">
                    <i class="bi bi-clock-history me-1"></i>
                    {{ __('onboarding.timeline') ?? 'Timeline' }}
                </div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.timeline_initiated') }}</span>
                        <span class="info-value">{{ $payment->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.timeline_cancelled') }}</span>
                        <span class="info-value">{{ ($payment->canceled_at ? \Carbon\Carbon::parse($payment->canceled_at)->format('d M Y, H:i') : now()->format('d M Y, H:i')) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.timeline_retry') }}</span>
                        <span class="info-value text-accent fw-bold">{{ __('onboarding.timeline_retry_action') ?? __('onboarding.retry_payment') }}</span>
                    </div>
                </div>
            </div>

            @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')
            <div class="d-grid gap-2 mt-3">
                <button wire:click="retry" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-repeat me-1"></i>{{ __('onboarding.retry_payment') }}
                </button>
                @if ($payment && OnboardingService::isManual($payment->method))
                <button wire:click="manualProof" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-upload me-1"></i>{{ __('onboarding.upload_manual_proof') }}
                </button>
                @endif
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i>{{ __('onboarding.switch_gateway') }}
                </button>
            </div>

        {{-- PENDING MANUAL VERIFICATION --}}
        @elseif ($view === 'pending_manual')
            @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')

            <div class="trust-banner mt-3 mb-3">
                <div class="trust-item">
                    <i class="bi bi-shield-check"></i>
                    <span>{{ __('onboarding.secure_payment') }}</span>
                </div>
                <div class="trust-item">
                    <i class="bi bi-lock"></i>
                    <span>{{ __('onboarding.encrypted') }}</span>
                </div>
            </div>

        {{-- ERROR --}}
        @elseif ($view === 'error')
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text">{{ __('general.app_name') }}</span>
            </div>
            <div class="text-center mb-3">
                <x-status-icon domain="general" status="expired" set="bi" style="font-size:3rem" />
            </div>
            <div class="alert alert-danger py-2 small">{{ $errorMessage ?? __('onboarding.no_pending_payment') }}</div>
            @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')
            <div class="d-grid gap-2 mt-3">
                @if ($payment)
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i>{{ __('onboarding.switch_gateway') }}
                </button>
                @endif
            </div>
        @endif

        @if ($payment && $view !== 'pending_manual')
            <div class="trust-banner mt-3 mb-3">
                <div class="trust-item">
                    <i class="bi bi-shield-check"></i>
                    <span>{{ __('onboarding.secure_payment') }}</span>
                </div>
                <div class="trust-item">
                    <i class="bi bi-lock"></i>
                    <span>{{ __('onboarding.encrypted') }}</span>
                </div>
                <div class="trust-item">
                    <i class="bi bi-credit-card-2-front"></i>
                    <span>{{ __('onboarding.multiple_methods') }}</span>
                </div>
            </div>
        @endif

        @include('livewire.pages.onboarding.partials.auth-footer')
    </div>
</div>
