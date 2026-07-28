<?php
// resources\views\livewire\pages\onboarding\payment-result.blade.php
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\PaymentService;
use App\Services\PaymentStatusService;
use App\Services\Payments\GatewayManager;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.guest')] class extends Component {
    public ?Payment $payment = null;
    public ?string $error = null;
    public bool $isPending = true;
    public int $pollCount = 0;
    public int $maxPolls = 12;
    public bool $autoRedirecting = false;
    public string $autoRedirectUrl = '';
    public ?array $invoice = null;
    public ?array $plan = null;
    public string $view = 'loading';

    public function mount(?Payment $payment = null): void
    {
        $user = auth()->user();
        $checkoutId = request()->query('checkout_id');

        if ($payment) {
            $this->payment = $payment;
            $this->loadPaymentRelations();
            if (request()->query('cancel')) {
                $this->handleCancel();
                return;
            }
            $this->evaluateStatus();
            return;
        }

        if ($checkoutId) {
            $this->resolveFromCheckout($checkoutId);
            return;
        }

        $paymentId = session('pending_payment_id');
        if ($paymentId) {
            $this->payment = Payment::withoutWorkspace()->find($paymentId);
            if ($this->payment && $this->payment->user_id === auth()->id()) {
                $this->loadPaymentRelations();
                if (request()->query('cancel')) {
                    $this->handleCancel();
                    return;
                }
                $this->evaluateStatus();
                return;
            }
        }

        $this->handleNoPayment($user);
    }

    private function loadPaymentRelations(): void
    {
        if (!$this->payment) {
            return;
        }

        $this->payment->loadMissing('subscription.plan', 'verification');

        $sub = $this->payment->subscription;
        if ($sub && $sub->relationLoaded('plan') && $sub->plan) {
            $this->plan = $sub->plan->toArray();
            $invoiceModel = $sub
                ->invoices()
                ->where('user_id', auth()->id())
                ->latest()
                ->first();
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
        if (!$p) {
            $this->view = 'error';
            return;
        }

        if ($p->isCompleted()) {
            $this->view = 'completed';
            $this->isPending = false;
            return;
        }

        if ($p->isFailed()) {
            $this->view = 'failed';
            $this->error = __('onboarding.payment_failed');
            $this->isPending = false;
            return;
        }

        if ($p->status === PaymentStatus::CheckoutCanceled) {
            $this->view = 'canceled';
            $this->error = __('onboarding.payment_cancelled_desc');
            $this->isPending = false;
            return;
        }

        if ($p->isPending() && $p->verification) {
            $this->view = 'pending_manual';
            $this->isPending = false;
            return;
        }

        if ($p->isPending()) {
            $this->view = 'waiting';
            $this->isPending = true;
            return;
        }

        $this->view = 'error';
        $this->isPending = false;
    }

    private function resolveFromCheckout(string $checkoutId): void
    {
        $paymentId = Payment::withoutWorkspace()->where('transaction_id', $checkoutId)->orWhere('chargily_checkout_id', $checkoutId)->value('id');

        if ($paymentId) {
            $this->payment = Payment::withoutWorkspace()->find($paymentId);
            session()->put('pending_payment_id', $paymentId);
            $this->loadPaymentRelations();
            if (request()->query('cancel')) {
                $this->handleCancel();
                return;
            }
            $this->evaluateStatus();
            return;
        }

        $paymentId = session('pending_payment_id');
        if ($paymentId) {
            $this->payment = Payment::withoutWorkspace()->find($paymentId);
            if ($this->payment) {
                $this->loadPaymentRelations();
                $this->view = 'waiting';
                return;
            }
        }

        $this->error = __('onboarding.no_pending_payment');
        $this->view = 'error';
        $this->isPending = false;
    }

    private function handleCancel(): void
    {
        $payment = $this->payment;
        if (!$payment) {
            $this->error = __('onboarding.no_pending_payment');
            $this->view = 'error';
            $this->isPending = false;
            return;
        }

        if ($payment->isCompleted()) {
            $this->redirect(route('onboarding.setup', absolute: false), navigate: true);
            return;
        }

        if ($payment->isFailed() || $payment->status->value === PaymentStatus::CheckoutCanceled->value) {
            $this->view = 'canceled';
            $this->isPending = false;
            return;
        }

        // Verify with gateway for ALL methods (including manual) before canceling
        try {
            $gateway = app(GatewayManager::class)->driver($payment->paymentMethod?->key);
            $result = $gateway->verify($payment);

            if ($result->success && ($result->metadata['status'] ?? '') === 'paid') {
                app(PaymentStatusService::class)->markPaid($payment);
                $this->redirect(route('onboarding.setup', absolute: false), navigate: true);
                return;
            }
        } catch (\Throwable $e) {
            Log::warning('Cancel verify failed', [
                'payment_id' => $payment->id,
                'method' => $payment->paymentMethod?->key,
                'error' => $e->getMessage(),
            ]);
            // Continue with cancellation even if verify fails
        }

        $this->cancelPaymentAndCleanup($payment);
        $this->view = 'canceled';
        $this->isPending = false;
    }

    private function cancelPaymentAndCleanup(Payment $payment): void
    {
        app(PaymentStatusService::class)->cancelWithSubscriptionCleanup($payment);
    }

    private function handleNoPayment($user): void
    {
        if ($user->hasCompletedOnboarding()) {
            $this->redirect(route('dashboard', absolute: false), navigate: false);
            return;
        }
        if ($user->hasConfirmedPlan()) {
            $this->redirect(route('onboarding.setup', absolute: false), navigate: false);
            return;
        }
        $this->error = __('onboarding.no_pending_payment');
        $this->view = 'error';
        $this->isPending = false;
    }

    public function checkStatus(): void
    {
        if (!$this->payment) {
            return;
        }
        $this->pollCount++;

        $this->payment->refresh();
        $this->loadPaymentRelations();
        $this->evaluateStatus();

        if ($this->view === 'waiting' && $this->pollCount >= $this->maxPolls) {
            $this->error = __('onboarding.payment_verification_timeout');
            $this->view = 'error';
            $this->isPending = false;
        }

        if (in_array($this->view, ['completed', 'failed', 'canceled', 'pending_manual', 'error'])) {
            $this->scheduleAutoRedirect();
        }
    }

    private function scheduleAutoRedirect(): void
    {
        if ($this->autoRedirecting) {
            return;
        }

        $url = match ($this->view) {
            'completed' => route('onboarding.setup', absolute: false),
            'failed' => route('payment.status', ['payment' => $this->payment?->id ?? 0], absolute: false),
            'canceled' => route('payment.status', ['payment' => $this->payment?->id ?? 0], absolute: false),
            default => null,
        };

        if (!$url) {
            return;
        }

        $this->autoRedirecting = true;
        $this->autoRedirectUrl = $url;

        $this->js("setTimeout(() => window.location.href = '{$url}', 2500)");
    }

    public function remainingSeconds(): int
    {
        return max(0, ($this->maxPolls - $this->pollCount) * 5);
    }

    public function retry(): void
    {
        if (!$this->payment) {
            return;
        }
        $this->loadPaymentRelations();

        if ($this->payment->isPending()) {
            app(PaymentStatusService::class)->cancelWithSubscriptionCleanup($this->payment);
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
                app(PaymentStatusService::class)->resetToPending($this->payment, [
                    'transaction_id' => $result->transactionId,
                    'gateway_reference' => $result->reference,
                    'gateway_payload' => $result->metadata,
                    'redirect_url' => $result->redirectUrl,
                    'gateway_response' => $result->metadata ?? [],
                ]);

                if (OnboardingService::isOnline($this->payment->method) && $result->redirectUrl) {
                    $this->js("window.location.href = '" . addslashes($result->redirectUrl) . "'");
                    return;
                }

                $this->redirect(route('onboarding.manual-proof', ['payment' => $this->payment->id], absolute: false), navigate: true);
            }
        } catch (\Exception $e) {
            $this->error = __('onboarding.payment_init_failed');
            $this->view = 'error';
        }
    }

    public function manualProof(): void
    {
        if ($this->payment) {
            $this->redirect(route('onboarding.manual-proof', ['payment' => $this->payment->id], absolute: false), navigate: true);
        }
    }

    public function switchGateway(): void
    {
        $this->redirect(route('onboarding.plan', absolute: false), navigate: true);
    }

    public function proceed(): void
    {
        session()->forget('pending_payment_id');
        $this->redirect(route('onboarding.setup', absolute: false), navigate: false);
    }

    public function statusBadgeClass(string $status): string
    {
        return match ($status) {
            \App\Enums\PaymentStatus::CheckoutPending->value => 'bg-warning text-dark',
            \App\Enums\PaymentStatus::CheckoutPaid->value => 'bg-success text-white',
            \App\Enums\PaymentStatus::CheckoutFailed->value, \App\Enums\PaymentStatus::CheckoutCanceled->value => 'bg-danger text-white',
            \App\Enums\PaymentStatus::CheckoutExpired->value => 'bg-secondary text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function methodLabel(?string $method): string
    {
        if (!$method) {
            return '-';
        }
        $key = 'onboarding.method_' . $method;
        $trans = __($key);
        return $trans !== $key ? $trans : ucfirst($method);
    }

    public function formatAmount(float $amount, ?string $currency = null): string
    {
        $currency = $currency ?? ($this->payment?->currency ?? 'USD');
        return number_format($amount, 2) . ' ' . CurrencyHelper::symbol($currency);
    }

    public function getReceiptDataUrlProperty(): ?string
    {
        if (!$this->payment) {
            return null;
        }
        $this->payment->loadMissing('verification');
        $verification = $this->payment->verification;
        if (!$verification || !$verification->receipt_path) {
            return null;
        }

        $path = $verification->receipt_path;
        if (!Storage::disk('local')->exists($path)) {
            return null;
        }

        return 'data:' . Storage::disk('local')->mimeType($path) . ';base64,' . base64_encode(Storage::disk('local')->get($path));
    }
}; ?>

<div>
    <div class="auth-card animate-fade-in">
        {{-- WAITING — animated payment processing --}}
        @if ($view === 'waiting')
            <style>
                .payment-ring {
                    width: 80px;
                    height: 80px;
                    position: relative;
                    margin: 0 auto;
                }

                .payment-ring .ring {
                    position: absolute;
                    inset: 0;
                    border-radius: 50%;
                    border: 3px solid transparent;
                    animation: ring-spin 1.5s cubic-bezier(0.5, 0, 0.5, 1) infinite;
                }

                .payment-ring .ring:nth-child(1) {
                    border-top-color: var(--accent);
                    animation-delay: 0s;
                }

                .payment-ring .ring:nth-child(2) {
                    border-right-color: var(--info);
                    animation-delay: 0.2s;
                }

                .payment-ring .ring:nth-child(3) {
                    border-bottom-color: var(--success);
                    animation-delay: 0.4s;
                }

                .payment-ring .ring:nth-child(4) {
                    border-left-color: var(--warning);
                    animation-delay: 0.6s;
                }

                @keyframes ring-spin {
                    0% {
                        transform: rotate(0deg)
                    }

                    100% {
                        transform: rotate(360deg)
                    }
                }

                .payment-ring .check-icon {
                    position: absolute;
                    inset: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.8rem;
                    color: var(--accent);
                    opacity: 0.7;
                }

                .pulse-dot {
                    display: inline-block;
                    width: 6px;
                    height: 6px;
                    border-radius: 50%;
                    background: var(--accent);
                    margin: 0 3px;
                    animation: pulse-dot 1.4s ease-in-out infinite;
                }

                .pulse-dot:nth-child(2) {
                    animation-delay: 0.2s;
                }

                .pulse-dot:nth-child(3) {
                    animation-delay: 0.4s;
                }

                @keyframes pulse-dot {

                    0%,
                    80%,
                    100% {
                        opacity: 0.3;
                        transform: scale(0.8)
                    }

                    40% {
                        opacity: 1;
                        transform: scale(1.2)
                    }
                }

                .progress-glow {
                    height: 4px;
                    border-radius: 4px;
                    background: var(--border);
                    overflow: hidden;
                    max-width: 240px;
                    margin: 0 auto;
                    position: relative;
                }

                .progress-glow .bar {
                    height: 100%;
                    border-radius: 4px;
                    background: linear-gradient(90deg, var(--accent), var(--info), var(--accent));
                    background-size: 200% 100%;
                    animation: glow-move 2s linear infinite;
                    width: {{ min(100, (($maxPolls - $this->remainingSeconds() / 5) / $maxPolls) * 100) }}%;
                    transition: width 1s ease;
                }

                @keyframes glow-move {
                    0% {
                        background-position: 200% 0
                    }

                    100% {
                        background-position: -200% 0
                    }
                }
            </style>
            <div class="text-center">
                <div class="auth-logo">
                    <div class="logo-icon">FM</div>
                    <span class="logo-text">{{ __('general.app_name') }}</span>
                </div>
                <div class="my-4">
                    <div class="payment-ring">
                        <div class="ring"></div>
                        <div class="ring"></div>
                        <div class="ring"></div>
                        <div class="ring"></div>
                        <div class="check-icon"><i class="bi bi-credit-card"></i></div>
                    </div>
                </div>
                <p class="fw-semibold">{{ __('onboarding.processing_payment') }}</p>
                <p class="small text-muted">{{ __('onboarding.payment_processing_hint') }}</p>
                <div class="mt-3">
                    <div class="progress-glow">
                        <div class="bar"></div>
                    </div>
                    <p class="small text-muted mt-2"><span class="fw-medium">{{ $this->remainingSeconds() }}s</span>
                        {{ __('onboarding.remaining') }}</p>
                </div>
                <div class="mt-3">
                    <span class="pulse-dot"></span>
                    <span class="pulse-dot"></span>
                    <span class="pulse-dot"></span>
                </div>
                <div class="mt-3">
                    <x-button wire-click="checkStatus" variant="outline-accent" size="sm" icon="bi bi-arrow-repeat">{{ __('onboarding.check_status') }}</x-button>
                </div>
                <div wire:poll.5s="checkStatus" class="d-none"></div>
            </div>

            {{-- COMPLETED / REDIRECTING --}}
        @elseif ($view === 'completed')
            <style>
                .success-anim {
                    width: 80px;
                    height: 80px;
                    margin: 0 auto;
                    position: relative;
                }

                .success-anim .circle {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    background: var(--success);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    animation: success-pop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }

                .success-anim .circle i {
                    font-size: 2.5rem;
                    color: #fff;
                    animation: success-check 0.4s 0.2s both;
                }

                @keyframes success-pop {
                    0% {
                        transform: scale(0);
                        opacity: 0
                    }

                    100% {
                        transform: scale(1);
                        opacity: 1
                    }
                }

                @keyframes success-check {
                    0% {
                        transform: scale(0) rotate(-45deg)
                    }

                    100% {
                        transform: scale(1) rotate(0deg)
                    }
                }

                .redirect-timer {
                    display: inline-block;
                    width: 12px;
                    height: 12px;
                    border: 2px solid var(--accent);
                    border-top-color: transparent;
                    border-radius: 50%;
                    animation: spin 0.8s linear infinite;
                    vertical-align: middle;
                    margin-right: 6px;
                }

                @keyframes spin {
                    to {
                        transform: rotate(360deg)
                    }
                }
            </style>
            <div class="text-center">
                <div class="auth-logo">
                    <div class="logo-icon">FM</div>
                    <span class="logo-text">{{ __('general.app_name') }}</span>
                </div>
                <div class="my-4">
                    <div class="success-anim">
                        <div class="circle"><x-status-icon domain="general" status="success" set="bi" /></div>
                    </div>
                </div>
                <p class="fw-bold">{{ __('onboarding.payment_success') }}</p>
                <p class="small text-muted">{{ __('onboarding.proof_approved_desc') }}</p>
                @if ($autoRedirecting)
                    <p class="small text-accent mt-2"><span
                            class="redirect-timer"></span>{{ __('onboarding.redirecting') }}</p>
                @endif
                <div class="mt-2">
                    <x-button wire-click="proceed" variant="accent" icon="bi bi-arrow-right">{{ __('onboarding.continue') }}</x-button>
                </div>
            </div>

            {{-- FAILED --}}
        @elseif ($view === 'failed')
            <style>
                .fail-anim {
                    width: 80px;
                    height: 80px;
                    margin: 0 auto;
                    position: relative;
                }

                .fail-anim .circle {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    background: var(--danger);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    animation: fail-shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97);
                }

                .fail-anim .circle i {
                    font-size: 2.5rem;
                    color: #fff;
                }

                @keyframes fail-shake {
                    0% {
                        transform: translateX(0)
                    }

                    15% {
                        transform: translateX(-8px)
                    }

                    30% {
                        transform: translateX(8px)
                    }

                    45% {
                        transform: translateX(-5px)
                    }

                    60% {
                        transform: translateX(5px)
                    }

                    80% {
                        transform: translateX(-2px)
                    }

                    100% {
                        transform: translateX(0)
                    }
                }
            </style>
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text">{{ __('general.app_name') }}</span>
            </div>
            <div class="text-center mb-3">
                <div class="fail-anim">
                    <div class="circle"><x-status-icon domain="general" status="failed" set="bi" /></div>
                </div>
            </div>
            <div class="alert alert-danger py-2 small">{{ $error }}</div>
            @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')
            @if ($autoRedirecting)
                <p class="small text-accent text-center mt-2"><span
                        class="redirect-timer"></span>{{ __('onboarding.redirecting') }}</p>
            @endif
            <div class="d-grid gap-2 mt-3">
                <x-button wire-click="retry" variant="accent" icon="bi bi-arrow-repeat">{{ __('onboarding.retry_payment') }}</x-button>
                @if ($payment && OnboardingService::isManual($payment->paymentMethod?->key))
                    <x-button wire-click="manualProof" variant="outline-accent" icon="bi bi-upload">{{ __('onboarding.upload_manual_proof') }}</x-button>
                @endif
                <x-button wire-click="switchGateway" variant="outline-accent" icon="bi bi-arrow-left-right">{{ __('onboarding.switch_gateway') }}</x-button>
            </div>
            @include('livewire.pages.onboarding.partials.auth-footer')

            {{-- CANCELED --}}
        @elseif ($view === 'canceled')
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text">{{ __('general.app_name') }}</span>
            </div>
            <div class="text-center mb-3">
                <div class="text-warning" style="font-size:3rem"><x-status-icon domain="general" status="cancelled"
                        set="bi" /></div>
            </div>
            <div class="alert alert-warning py-2 small">{{ $error }}</div>
            @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')
            @if ($autoRedirecting)
                <p class="small text-accent text-center mt-2"><span
                        class="redirect-timer"></span>{{ __('onboarding.redirecting') }}</p>
            @endif
            <div class="d-grid gap-2 mt-3">
                <x-button wire-click="retry" variant="accent" icon="bi bi-arrow-repeat">{{ __('onboarding.retry_payment') }}</x-button>
                @if ($payment && OnboardingService::isManual($payment->paymentMethod?->key))
                    <x-button wire-click="manualProof" variant="outline-accent" icon="bi bi-upload">{{ __('onboarding.upload_manual_proof') }}</x-button>
                @endif
                <x-button wire-click="switchGateway" variant="outline-accent" icon="bi bi-arrow-left-right">{{ __('onboarding.switch_gateway') }}</x-button>
            </div>
            @include('livewire.pages.onboarding.partials.auth-footer')

            {{-- PENDING MANUAL VERIFICATION --}}
        @elseif ($view === 'pending_manual')
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text">{{ __('general.app_name') }}</span>
            </div>
            <div class="text-center mb-3">
                <div class="text-info" style="font-size:3rem"><i class="bi bi-shield-check"></i></div>
            </div>
            <div class="alert alert-info py-2 small d-flex align-items-center gap-2">
                <i class="bi bi-info-circle"></i>{{ __('onboarding.proof_pending_review_desc') }}
            </div>
            @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')
            @if ($payment && $payment->verification)
                @php $v = $payment->verification; @endphp
                <div class="info-section mb-3">
                    <div class="info-section-header">
                        <i class="bi bi-shield-checkms-1"></i>{{ __('onboarding.proof_details_title') }}
                    </div>
                    <div class="info-grid">
                        <div class="info-row">
                            <span class="info-label">{{ __('onboarding.transaction_reference') }}</span>
                            <span class="info-value"
                                style="direction:ltr;font-family:monospace;font-size:12px">{{ $v->transaction_reference ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">{{ __('onboarding.verification_status') }}</span>
                            <span class="info-value">
                                @php
                                    $vBadge = match ($v->status->value) {
                                        'pending' => [
                                            'bg' => 'bg-warning text-dark',
                                            'label' => __('onboarding.status_pending'),
                                        ],
                                        'approved' => [
                                            'bg' => 'bg-success text-white',
                                            'label' => __('onboarding.status_approved'),
                                        ],
                                        'rejected' => [
                                            'bg' => 'bg-danger text-white',
                                            'label' => __('onboarding.status_rejected'),
                                        ],
                                        default => [
                                            'bg' => 'bg-secondary text-white',
                                            'label' => ucfirst($v->status->value),
                                        ],
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $vBadge['bg'] }}">{{ $vBadge['label'] }}</span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">{{ __('onboarding.submitted_on') }}</span>
                            <span class="info-value">{{ $v->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if ($v->receipt_path)
                            <div class="info-row">
                                <span class="info-label">{{ __('onboarding.receipt_preview') }}</span>
                                <span class="info-value">
                                    <button type="button" @click="Livewire.dispatch('openReceiptModal')"
                                        style="color:var(--info);font-size:12px;text-decoration:none;border:none;background:none;padding:0;cursor:pointer">
                                        <i class="bi bi-eyems-1"></i>{{ __('onboarding.receipt_preview') }}
                                    </button>
                                </span>
                            </div>
                        @endif
                        @if ($v->admin_notes)
                            <div class="info-row">
                                <span class="info-label">{{ __('super-admin.reject_reason') ?? 'Admin Notes' }}</span>
                                <span class="info-value"
                                    style="font-size:12px;max-width:180px;text-align:right">{{ $v->admin_notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            @include('livewire.pages.onboarding.partials.auth-footer')

            {{-- ERROR / TIMEOUT --}}
        @elseif ($view === 'error')
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text">{{ __('general.app_name') }}</span>
            </div>
            <div class="text-center mb-3">
                <x-status-icon domain="general" status="danger" set="bi" style="font-size:3rem" />
            </div>
            <div class="alert alert-danger py-2 small">{{ $error }}</div>
            @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')
            <div class="d-grid gap-2 mt-3">
                @if ($payment)
                    <x-button wire-click="retry" variant="accent" icon="bi bi-arrow-repeat">{{ __('onboarding.retry_payment') }}</x-button>
                    <x-button wire-click="switchGateway" variant="outline-accent" icon="bi bi-arrow-left-right">{{ __('onboarding.switch_gateway') }}</x-button>
                @endif
            </div>
            @include('livewire.pages.onboarding.partials.auth-footer')
        @endif
    </div>

    @if ($this->receiptDataUrl)
        <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true" wire:ignore>
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="background:var(--card-bg);border:1px solid var(--border)">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title">{{ __('onboarding.receipt_preview') }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <img src="{{ $this->receiptDataUrl }}" alt="{{ __('onboarding.receipt_preview') }}"
                            style="max-width:100%;max-height:70vh;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.1)">
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                function initPaymentResult() {
                    if (!window._paymentReceiptListener) {
                        Livewire.on('openReceiptModal', function() {
                            var modal = new bootstrap.Modal(document.getElementById('receiptModal'));
                            modal.show();
                        });
                        window._paymentReceiptListener = true;
                    }
                }
                initPaymentResult();
            </script>
        @endpush
    @endif
</div>
