<?php
// resources\views\livewire\pages\onboarding\manual-proof.blade.php
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\Payments\GatewayManager;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.guest')] class extends Component
{
    use WithFileUploads;

    public Payment $payment;
    public $receipt = null;
    public string $transactionReference = '';
    public string $view = 'form';
    public ?string $errorMessage = null;
    public int $pollCount = 0;
    public int $maxPolls = 180;

    protected array $fieldLabels = [
        'rip_number' => 'onboarding.field_rip_number',
        'account_holder_name' => 'onboarding.field_account_holder_name',
        'account_id' => 'onboarding.field_account_id',
        'account_email' => 'onboarding.field_account_email',
        'amount' => 'onboarding.field_amount',
        'currency' => 'onboarding.field_currency',
    ];

    public ?string $pageError = null;

    public ?string $confirming = null;

    public function mount(Payment $payment): void
    {
        $user = auth()->user();

        if ($payment->user_id !== $user->id) {
            $this->pageError = __('onboarding.payment_not_found');
            return;
        }

        $this->payment = $payment;
        $this->payment->loadMissing('verification');
        $this->evaluateStatus();
    }

    private function evaluateStatus(): void
    {
        if ($this->payment->isCompleted()) {
            $this->view = 'completed';
            return;
        }

        $v = $this->payment->verification;
        if ($v) {
            $this->view = match ($v->status->value) {
                'approved' => 'completed',
                'rejected' => 'rejected',
                default => 'submitted',
            };
            return;
        }

        $this->view = 'form';
    }

    public function checkStatus(): void
    {
        if (!$this->payment) return;
        $this->pollCount++;

        $this->payment->refresh();
        $this->payment->loadMissing('verification');
        $this->evaluateStatus();

        if ($this->view === 'submitted' && $this->pollCount >= $this->maxPolls) {
            $this->payment->update(['status' => PaymentStatus::CheckoutFailed]);
            $this->payment->refresh();
            $this->view = 'form';
            $this->errorMessage = __('onboarding.payment_verification_timeout');
        }
    }

    public function getPaymentDetailsProperty(): array
    {
        $response = $this->payment->metadata['gateway_response'] ?? [];

        $details = [];
        foreach ($response as $key => $value) {
            if ($key === 'instructions' || $value === null || $value === '') {
                continue;
            }
            $details[] = [
                'label' => isset($this->fieldLabels[$key]) ? __($this->fieldLabels[$key]) : $key,
                'value' => $value,
            ];
        }

        return $details;
    }

    public function getPaymentInstructionsProperty(): ?string
    {
        return $this->payment->metadata['gateway_response']['instructions'] ?? null;
    }

    public function submit(): void
    {
        $this->validate([
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'transactionReference' => ['required', 'string', 'max:100'],
        ]);

        try {
            app(OnboardingService::class)->submitManualPaymentProof(
                $this->payment,
                $this->receipt,
                $this->transactionReference,
            );
            $this->payment->refresh();
            $this->payment->loadMissing('verification');
            $this->evaluateStatus();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function retry(): void
    {
        if (!$this->payment) return;

        $this->payment->update(['status' => PaymentStatus::CheckoutCanceled, 'canceled_at' => now()]);
        $v = $this->payment->verification;
        if ($v) {
            $v->delete();
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

                $this->payment->refresh();
                $this->payment->loadMissing('verification');
                $this->pollCount = 0;
                $this->evaluateStatus();
            }
        } catch (\Exception $e) {
            $this->errorMessage = __('onboarding.payment_init_failed');
        }
    }

    public function switchGateway(): void
    {
        $this->redirect(route('onboarding.plan', absolute: false), navigate: true);
    }

    public function changePlan(): void
    {
        $this->redirect(route('onboarding.plan', absolute: false), navigate: true);
    }

    public function proceed(): void
    {
        session()->forget('pending_payment_id');
        $this->redirect(route('onboarding.setup', absolute: false), navigate: false);
    }

    public function confirmCancel(): void
    {
        $this->confirming = 'cancel';
    }

    public function executeConfirmed(): void
    {
        if ($this->confirming === 'cancel') {
            $this->cancelAndChangeMethod();
        }
        $this->confirming = null;
    }

    public function cancelConfirmation(): void
    {
        $this->confirming = null;
    }

    public function cancelAndChangeMethod(): void
    {
        if ($this->payment->isCompleted()) {
            return;
        }

        $this->payment->update(['status' => PaymentStatus::CheckoutCanceled, 'canceled_at' => now()]);

        $verification = $this->payment->verification;
        if ($verification) {
            $verification->delete();
        }

        session()->flash('success', __('onboarding.payment_cancelled_change_method'));

        $this->redirect(route('onboarding.plan', absolute: false), navigate: true);
    }

    public function getReceiptDataUrlProperty(): ?string
    {
        $verification = $this->payment->verification;
        if (!$verification || !$verification->receipt_path) {
            return null;
        }

        $path = $verification->receipt_path;
        if (!Storage::disk('local')->exists($path)) {
            return null;
        }

        $mime = Storage::disk('local')->mimeType($path);
        $data = Storage::disk('local')->get($path);

        return 'data:' . $mime . ';base64,' . base64_encode($data);
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

    public function statusBadgeClass(string $status): string
    {
        return match ($status) {
            'pending', 'checkout.pending' => 'bg-warning text-dark',
            'approved', 'checkout.paid' => 'bg-success text-white',
            'rejected', 'checkout.failed', 'checkout.canceled' => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }
}; ?>

<div>
<div class="auth-card animate-fade-in">
    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text">{{ __('general.app_name') }}</span>
        <span class="logo-sub">{{ __('onboarding.manual_payment_title') }}</span>
    </div>

    @if ($pageError)
        <div class="alert alert-danger py-2 small mb-3">{{ $pageError }}</div>
        <div class="auth-footer mt-3">
            <a href="{{ route('onboarding.plan') }}" wire:navigate>{{ __('onboarding.back_to_plans') }}</a>
        </div>

    @elseif ($view === 'completed')
        <div class="text-center mb-4">
            <div class="mb-3" style="font-size:4rem"><i class="bi bi-check-circle-fill text-success"></i></div>
            <h5>{{ __('onboarding.payment_success') }}</h5>
            <p class="text-muted small">{{ __('onboarding.proof_approved_desc') }}</p>
        </div>
        @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')
        <div class="d-grid gap-2 mt-3">
            <button wire:click="proceed" class="btn btn-accent btn-custom">
                <i class="bi bi-arrow-right me-1"></i>{{ __('onboarding.continue') }}
            </button>
        </div>

    @elseif ($view === 'rejected')
        @php $v = $payment->verification; @endphp
        <div class="text-center mb-4">
            <div class="mb-3" style="font-size:3rem"><i class="bi bi-x-circle-fill text-danger"></i></div>
            <h5>{{ __('onboarding.proof_rejected') }}</h5>
            <p class="text-muted small">{{ __('onboarding.proof_rejected_desc') }}</p>
        </div>
        @if($v)
            <div class="info-section mb-3">
                <div class="info-section-header">
                    <i class="bi bi-receipt me-1"></i>{{ __('onboarding.proof_details_title') }}
                </div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.verification_status') }}</span>
                        <span class="info-value">
                            <span class="badge rounded-pill bg-danger text-white">{{ __('onboarding.status_rejected') }}</span>
                        </span>
                    </div>
                    @if ($v->admin_notes)
                    <div class="info-row">
                        <span class="info-label">{{ __('super-admin.reject_reason') ?? 'Admin Notes' }}</span>
                        <span class="info-value" style="font-size:12px;max-width:180px;text-align:right">{{ $v->admin_notes }}</span>
                    </div>
                    @endif
                </div>
            </div>
        @endif
        @includeWhen($payment, 'livewire.pages.onboarding.partials.payment-details')
        <hr>
        <p class="small text-muted mb-2">{{ __('onboarding.retry_or_change_method') }}</p>
        <div class="d-grid gap-2">
            <button wire:click="retry" class="btn btn-accent btn-custom">
                <i class="bi bi-arrow-repeat me-1"></i>{{ __('onboarding.retry_payment') }}
            </button>
            <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                <i class="bi bi-arrow-left-right me-1"></i>{{ __('onboarding.switch_gateway') }}
            </button>
            <button wire:click="changePlan" class="btn btn-outline-secondary btn-custom">
                <i class="bi bi-grid me-1"></i>{{ __('onboarding.change_plan') }}
            </button>
        </div>

    @elseif ($view === 'submitted')
        @php $v = $payment->verification; @endphp
        <div class="text-center mb-4">
            <i class="bi bi-clock-history text-warning" style="font-size:3rem;"></i>
            <h5 class="mt-3">{{ __('onboarding.proof_pending_review') }}</h5>
            <p class="text-muted small">{{ __('onboarding.proof_pending_review_desc') }}</p>
        </div>
        @if($v)
            <div class="info-section mb-3">
                <div class="info-section-header">
                    <i class="bi bi-receipt me-1"></i>
                    {{ __('onboarding.proof_details_title') }}
                </div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.transaction_reference') }}</span>
                        <span class="info-value" style="direction:ltr;font-family:monospace">{{ $v->transaction_reference ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.verification_status') }}</span>
                        <span class="info-value">
                            <span class="badge rounded-pill {{ $this->statusBadgeClass($v->status->value) }}">{{ $v->status->label() }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('onboarding.submitted_on') }}</span>
                        <span class="info-value">{{ $v->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
            @if($this->receiptDataUrl)
                <div class="info-section mb-3">
                    <div class="info-section-header">
                        <i class="bi bi-image me-1"></i>
                        {{ __('onboarding.receipt_preview') }}
                    </div>
                    <div class="text-center p-3">
                        <button type="button" class="btn btn-sm btn-outline-accent" @click="Livewire.dispatch('openReceiptModal')" style="border:1px solid var(--border);border-radius:8px;padding:6px 16px">
                            <i class="bi bi-eye me-1"></i>{{ __('general.open_in_new_tab') }}
                        </button>
                    </div>
                </div>
            @endif
        @endif

        <div class="text-muted small text-center mt-2">
            <i class="bi bi-arrow-repeat me-1"></i>{{ __('onboarding.auto_checking') }}
        </div>
        <div wire:poll.5s="checkStatus" class="d-none"></div>

        <div class="d-grid gap-2 mt-3">
            <button type="button" wire:click="confirmCancel"
                class="btn btn-outline-danger btn-custom mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-x-circle me-1"></i>{{ __('onboarding.cancel_change_method') }}
            </button>
        </div>
    @else
        <p class="text-muted small mb-3">{{ __('onboarding.manual_payment_instructions', ['method' => __('onboarding.method_' . $payment->method)]) }}</p>

        @if (count($this->paymentDetails) > 0)
            <div class="payment-details-box mb-3">
                @foreach ($this->paymentDetails as $detail)
                    <div class="payment-detail-row">
                        <span class="detail-label">{{ $detail['label'] }}</span>
                        <span class="detail-value">{{ $detail['value'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($this->paymentInstructions)
            <div class="alert alert-info py-2 small mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle me-1"></i>{{ $this->paymentInstructions }}
            </div>
        @endif

        @if ($errorMessage)
            <div class="alert alert-danger py-2 small">{{ $errorMessage }}</div>
        @endif

        <form wire:submit="submit">
            <div class="mb-3">
                <label class="form-label-custom">{{ __('onboarding.transaction_reference') }}</label>
                <input type="text" wire:model="transactionReference" class="form-custom @error('transactionReference') is-invalid @enderror"
                    placeholder="{{ __('onboarding.transaction_reference_placeholder') }}">
                @error('transactionReference') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label-custom">{{ __('onboarding.upload_receipt') }}</label>
                <div class="drop-zone @error('receipt') drop-zone-error @enderror"
                     x-data="{ dragging: false }"
                     x-on:dragover.prevent="dragging = true"
                     x-on:dragleave.prevent="dragging = false"
                     x-on:drop.prevent="dragging = false; $wire.upload('receipt', $event.dataTransfer.files[0])"
                     x-bind:class="{ 'drop-zone-active': dragging }"
                     wire:loading.class="drop-zone-loading" wire:target="receipt">
                    <div class="drop-zone-content" x-show="!$wire.receipt">
                        <i class="bi bi-cloud-upload" style="font-size:2rem;color:var(--text-muted,#888)"></i>
                        <p class="small text-muted mb-1">{{ __('onboarding.drag_drop_hint') }}</p>
                        <p class="small text-muted">{{ __('onboarding.or') }}</p>
                        <label class="btn btn-sm btn-outline-accent mt-1" style="cursor:pointer">
                            {{ __('onboarding.browse_files') }}
                            <input type="file" wire:model="receipt" accept=".jpg,.jpeg,.png,.pdf" hidden>
                        </label>
                    </div>
                    <div class="drop-zone-preview" x-show="$wire.receipt" style="display:none">
                        <div class="text-center p-3">
                            <i class="bi bi-file-check text-success" style="font-size:2rem"></i>
                            <p class="small mb-0 mt-2" x-text="$wire.receipt ? $wire.receipt.name : ''"></p>
                            <a href="#" class="small text-danger" x-on:click.prevent="$wire.receipt = null">{{ __('general.remove') }}</a>
                        </div>
                    </div>
                    <div class="drop-zone-loading-indicator" wire:loading wire:target="receipt">
                        <div class="progress" style="height:4px;border-radius:4px;overflow:hidden;margin-top:8px">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width:100%;height:4px"></div>
                        </div>
                        <p class="small text-muted mt-1 mb-0">{{ __('onboarding.uploading') }}</p>
                    </div>
                </div>
                @error('receipt') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                <div wire:loading.remove wire:target="receipt" class="mt-2">
                    <div x-data="{ preview: null }"
                         x-init="$watch('$wire.receipt', val => {
                             if (!val) { preview = null; return }
                             if (!val.type?.startsWith('image/')) return
                             const reader = new FileReader()
                             reader.onload = e => preview = e.target.result
                             reader.readAsDataURL(val)
                         })">
                        <template x-if="preview">
                            <div class="text-center mt-2">
                                <img :src="preview" class="receipt-preview-img"
                                     alt="{{ __('onboarding.receipt_preview') }}" style="max-height:200px">
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-accent btn-custom w-100" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit">{{ __('onboarding.submit_proof') }}</span>
                <span wire:loading wire:target="submit">{{ __('onboarding.uploading') }}</span>
            </button>
        </form>

        <div class="d-grid gap-2 mt-3">
            <button type="button" wire:click="confirmCancel"
                class="btn btn-outline-danger btn-custom mb-0 d-flex align-items-center gap-2 justify-center">
                <i class="bi bi-x-circle me-1"></i>{{ __('onboarding.cancel_change_method') }}
            </button>
        </div>
    @endif

    @if ($confirming)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
            <div style="background:var(--card-bg,#fff);border-radius:var(--radius-md,12px);max-width:400px;width:100%;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                <h5 style="font-size:16px;font-weight:600;margin-bottom:8px;">{{ __('general.confirm') }}</h5>
                <p style="font-size:14px;color:var(--text-muted);margin-bottom:1.5rem;">{{ __('onboarding.cancel_confirm_desc') }}</p>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button wire:click="cancelConfirmation" type="button" class="btn btn-outline-secondary btn-custom" style="font-size:13px;padding:7px 16px;">
                        {{ __('general.cancel') }}
                    </button>
                    <button wire:click="executeConfirmed" type="button" class="btn btn-danger btn-custom" style="font-size:13px;padding:7px 16px;">
                        {{ __('onboarding.cancel_change_method') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@if ($this->receiptDataUrl)
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background:var(--card-bg);border:1px solid var(--border)">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title">{{ __('onboarding.receipt_preview') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="{{ $this->receiptDataUrl }}"
                         alt="{{ __('onboarding.receipt_preview') }}"
                         style="max-width:100%;max-height:70vh;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.1)">
                </div>
            </div>
        </div>
    </div>
@endif
</div>
    @push('scripts')
    <script>
        function initManualProof() {
            if (!window._manualReceiptListener) {
                Livewire.on('openReceiptModal', function () {
                    var modal = new bootstrap.Modal(document.getElementById('receiptModal'));
                    modal.show();
                });
                window._manualReceiptListener = true;
            }
        }
        initManualProof();
    </script>
    @endpush

