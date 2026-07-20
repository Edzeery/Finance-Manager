<?php

use App\Models\User;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $secret = '';

    public string $qrCodeInline = '';

    public string $code = '';

    public bool $enabled = false;

    public bool $showingQrCode = false;

    public array $recoveryCodes = [];

    public bool $showingRecoveryCodes = false;

    public ?string $confirming = null;

    public ?string $confirmingMethod = null;

    public string $setupMethod = '';

    public bool $showingEmailInput = false;

    public bool $emailCodeSent = false;

    public string $emailCode = '';

    public bool $emailVerified = false;

    public function mount(TwoFactorAuthenticationService $twoFactor): void
    {
        $user = Auth::user();
        $this->enabled = $user->hasTwoFactorEnabled();

        if ($this->enabled) {
            $this->recoveryCodes = $user->two_factor_recovery_codes ?? [];
            $this->showingRecoveryCodes = !empty($this->recoveryCodes);
        } else {
            $this->secret = $twoFactor->generateSecretKey();
            $this->qrCodeInline = $twoFactor->getQrCodeInline($user, $this->secret);
        }
    }

    public function isMethodEnabled(string $method): bool
    {
        return Auth::user()->hasTwoFactorMethod($method);
    }

    public function startSetup(string $method): void
    {
        $this->setupMethod = $method;
        $this->resetErrorBag();

        if ($method === TwoFactorAuthenticationService::METHOD_APP && !$this->secret) {
            $twoFactor = app(TwoFactorAuthenticationService::class);
            $user = Auth::user();
            $this->secret = $twoFactor->generateSecretKey();
            $this->qrCodeInline = $twoFactor->getQrCodeInline($user, $this->secret);
        }
    }

    public function cancelSetup(): void
    {
        $this->setupMethod = '';
        $this->showingQrCode = false;
        $this->showingEmailInput = false;
        $this->emailCodeSent = false;
        $this->emailVerified = false;
        $this->code = '';
        $this->emailCode = '';
        $this->resetErrorBag();
    }

    public function showQrCode(): void
    {
        $this->showingQrCode = true;
    }

    public function sendEmailCode(TwoFactorAuthenticationService $twoFactor): void
    {
        $rateLimitKey = '2fa-send-email:' . Auth::id() . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->addError('emailCode', trans('auth.throttle', ['seconds' => $seconds]));
            return;
        }

        RateLimiter::hit($rateLimitKey, 60);

        $twoFactor->sendEmailCode(Auth::user());
        $this->emailCodeSent = true;
        $this->showingEmailInput = true;
    }

    public function confirmEmailCode(TwoFactorAuthenticationService $twoFactor): void
    {
        $this->validate(['emailCode' => 'required|string|size:6']);

        /** @var User $user */
        $user = Auth::user();

        $rateLimitKey = '2fa-confirm-email:' . $user->id . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->addError('emailCode', trans('auth.throttle', ['seconds' => $seconds]));
            return;
        }

        RateLimiter::hit($rateLimitKey, 60);

        if (!$twoFactor->verifyEmailCode($user, $this->emailCode)) {
            $this->addError('emailCode', trans('auth.invalid_2fa_code'));
            return;
        }

        RateLimiter::clear($rateLimitKey);

        $twoFactor->enable($user, TwoFactorAuthenticationService::METHOD_EMAIL);

        if (!$user->two_factor_recovery_codes) {
            $this->recoveryCodes = $twoFactor->generateRecoveryCodes();
            $user->forceFill(['two_factor_recovery_codes' => $this->recoveryCodes])->save();
        } else {
            $this->recoveryCodes = $user->two_factor_recovery_codes;
        }

        $this->enabled = true;
        $this->emailVerified = true;
        $this->showingRecoveryCodes = true;
        $this->emailCode = '';
    }

    public function confirm(TwoFactorAuthenticationService $twoFactor): void
    {
        $this->validate(['code' => 'required|string|size:6']);

        /** @var User $user */
        $user = Auth::user();

        $rateLimitKey = '2fa-confirm:' . $user->id . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->addError('code', trans('auth.throttle', ['seconds' => $seconds]));
            return;
        }

        RateLimiter::hit($rateLimitKey, 60);

        if (! $twoFactor->verify($this->secret, $this->code)) {
            $this->addError('code', trans('google2fa.error_messages.wrong_otp'));
            return;
        }

        RateLimiter::clear($rateLimitKey);

        $user->forceFill(['google2fa_secret' => $this->secret])->save();
        $twoFactor->enable($user, TwoFactorAuthenticationService::METHOD_APP);

        if (!$user->two_factor_recovery_codes) {
            $this->recoveryCodes = $twoFactor->generateRecoveryCodes();
            $user->forceFill(['two_factor_recovery_codes' => $this->recoveryCodes])->save();
        } else {
            $this->recoveryCodes = $user->two_factor_recovery_codes;
        }

        $this->enabled = true;
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = true;
        $this->code = '';
    }

    public function disableMethod(string $method, TwoFactorAuthenticationService $twoFactor): void
    {
        $twoFactor->disable(Auth::user(), $method);

        $user = Auth::user();
        $this->enabled = $user->hasTwoFactorEnabled();

        if (!$this->enabled) {
            $this->secret = $twoFactor->generateSecretKey();
            $this->qrCodeInline = $twoFactor->getQrCodeInline($user, $this->secret);
            $this->showingRecoveryCodes = false;
            $this->recoveryCodes = [];
        }
    }

    public function disable(TwoFactorAuthenticationService $twoFactor): void
    {
        $twoFactor->disable(Auth::user());

        $this->enabled = false;
        $this->showingRecoveryCodes = false;
        $this->recoveryCodes = [];
        $this->setupMethod = '';
        $this->showingQrCode = false;
        $this->showingEmailInput = false;
        $this->emailCodeSent = false;
        $this->emailVerified = false;

        $user = Auth::user();
        $this->secret = $twoFactor->generateSecretKey();
        $this->qrCodeInline = $twoFactor->getQrCodeInline($user, $this->secret);
    }

    public function confirmDisable(): void
    {
        $this->confirming = 'disable';
    }

    public function confirmDisableMethod(string $method): void
    {
        $this->confirming = 'disableMethod';
        $this->confirmingMethod = $method;
    }

    public function executeConfirmed(TwoFactorAuthenticationService $twoFactor): void
    {
        if ($this->confirming === 'disable') {
            $this->disable($twoFactor);
        } elseif ($this->confirming === 'disableMethod' && $this->confirmingMethod) {
            $this->disableMethod($this->confirmingMethod, $twoFactor);
        }
        $this->confirming = null;
        $this->confirmingMethod = null;
    }

    public function cancelConfirmation(): void
    {
        $this->confirming = null;
        $this->confirmingMethod = null;
    }

    public function regenerateRecoveryCodes(TwoFactorAuthenticationService $twoFactor): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->recoveryCodes = $twoFactor->generateRecoveryCodes();
        $user->forceFill(['two_factor_recovery_codes' => $this->recoveryCodes])->save();
    }

    public function getAvailableMethodsProperty(): array
    {
        return [
            TwoFactorAuthenticationService::METHOD_APP => [
                'label' => __('general.authenticator_app'),
                'desc' => __('general.use_auth_app_desc'),
                'icon' => 'bi-phone',
            ],
            TwoFactorAuthenticationService::METHOD_EMAIL => [
                'label' => __('general.email_method'),
                'desc' => __('general.use_email_desc'),
                'icon' => 'bi-envelope',
            ],
        ];
    }

    public function getFutureMethodsProperty(): array
    {
        return [
            [
                'icon' => 'bi-chat-dots',
            ],
        ];
    }
}; ?>

<div>
    <div class="settings-card col-md-6 mx-auto">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width:64px;height:64px;background:var(--accent-subtle, rgba(21,183,108,0.1));">
                <i class="bi bi-shield-check" style="font-size:28px;color:var(--accent);"></i>
            </div>
            <h4 class="mb-1" style="font-weight:600;">{{ __('general.two_factor_authentication') }}</h4>
            <p class="text-muted mb-0" style="font-size:14px;">
                {{ __('messages.add_extra_security_to_account') }}
            </p>
        </div>

        {{-- Setup flow for a specific method --}}
        @if ($setupMethod && (!$enabled || !$this->isMethodEnabled($setupMethod)))

            @if ($setupMethod === 'app')

                {{-- App Method Setup --}}
                @if (! $showingQrCode)
                    <div class="text-center py-3">
                        <p class="text-muted mb-3" style="font-size:14px;line-height:1.6;">
                            {{ __('messages.two_factor_setup_description') }}
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-3 mb-3">
                            <div class="text-center p-3 rounded" style="min-width:120px;background:var(--bg-subtle);">
                                <div class="mb-1"><i class="bi bi-phone" style="font-size:22px;color:var(--accent);"></i></div>
                                <div style="font-size:12px;color:var(--text-muted);">1. {{ __('general.install_app') }}</div>
                            </div>
                            <div class="text-center p-3 rounded" style="min-width:120px;background:var(--bg-subtle);">
                                <div class="mb-1"><i class="bi bi-qr-code" style="font-size:22px;color:var(--accent);"></i></div>
                                <div style="font-size:12px;color:var(--text-muted);">2. {{ __('general.scan_qr') }}</div>
                            </div>
                            <div class="text-center p-3 rounded" style="min-width:120px;background:var(--bg-subtle);">
                                <div class="mb-1"><i class="bi bi-check-circle" style="font-size:22px;color:var(--accent);"></i></div>
                                <div style="font-size:12px;color:var(--text-muted);">3. {{ __('general.verify_code') }}</div>
                            </div>
                        </div>
                        <button wire:click="showQrCode" class="btn btn-accent btn-custom px-4">
                            <i class="bi bi-qr-code me-2"></i>
                            {{ __('general.setup_2fa') }}
                        </button>
                    </div>
                @else
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                             style="width:40px;height:40px;background:var(--accent-subtle, rgba(21,183,108,0.1));">
                            <span style="font-size:16px;font-weight:700;color:var(--accent);">1</span>
                        </div>
                        <p class="mb-0" style="font-size:14px;font-weight:500;">{{ __('messages.scan_qr_code') }}</p>
                        <p class="text-muted" style="font-size:13px;">{{ __('messages.use_auth_app_to_scan') }}</p>
                    </div>
                    <div class="d-flex justify-content-center mb-4">
                        <div style="padding:16px;background:#fff;border-radius:12px;border:2px solid var(--border);display:inline-block;">
                            <img src="{{ $this->qrCodeInline }}" alt="QR Code" style="width:180px;height:180px;display:block;">
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label-custom mb-0">{{ __('general.setup_key') }}</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0"
                                    @click="navigator.clipboard.writeText('{{ $this->secret }}')"
                                    title="{{ __('general.copy') }}">
                                <i class="bi bi-clipboard" style="font-size:12px;"></i>
                            </button>
                        </div>
                        <div style="background:var(--bg-subtle);border-radius:8px;padding:10px 14px;font-family:monospace;font-size:13px;word-break:break-all;border:1px solid var(--border);">
                            {{ $this->secret }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                             style="width:40px;height:40px;background:var(--accent-subtle, rgba(21,183,108,0.1));">
                            <span style="font-size:16px;font-weight:700;color:var(--accent);">2</span>
                        </div>
                        <label for="code" class="form-label-custom">{{ __('general.enter_verification_code') }}</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-grid-3x2-gap input-icon-left"></i>
                            <input wire:model="code" id="code" type="text" inputmode="numeric"
                                   class="form-custom has-icon-left text-center tracking-wide @error('code') is-invalid @enderror"
                                 autocomplete="one-time-code"  placeholder="000 000" maxlength="6" autofocus
                                   style="letter-spacing:.3em;font-size:1.15rem;font-weight:600;">
                        </div>
                        @error('code')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button wire:click="confirm" class="btn btn-accent btn-custom w-100">
                        <div wire:loading wire:target="confirm" class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <i class="bi bi-shield-check me-2" wire:loading.remove wire:target="confirm"></i>
                        {{ __('general.confirm_enable') }}
                    </button>
                @endif

            @elseif ($setupMethod === 'email')

                {{-- Email Method Setup --}}
                @if (! $emailCodeSent)
                    <div class="text-center py-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                             style="width:56px;height:56px;background:var(--accent-subtle, rgba(21,183,108,0.1));">
                            <i class="bi bi-envelope" style="font-size:26px;color:var(--accent);"></i>
                        </div>
                        <p class="mb-2" style="font-size:14px;font-weight:500;">{{ __('general.email_verification') }}</p>
                        <p class="text-muted mb-3" style="font-size:13px;">
                            {{ __('messages.email_2fa_description') }}
                        </p>
                        <button wire:click="sendEmailCode" class="btn btn-accent btn-custom px-4">
                            <div wire:loading wire:target="sendEmailCode" class="spinner-border spinner-border-sm me-2" role="status"></div>
                            <i class="bi bi-send me-2" wire:loading.remove wire:target="sendEmailCode"></i>
                            {{ __('general.send_code_to_email') }}
                        </button>
                    </div>
                @else
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                             style="width:40px;height:40px;background:var(--accent-subtle, rgba(21,183,108,0.1));">
                            <i class="bi bi-envelope" style="font-size:18px;color:var(--accent);"></i>
                        </div>
                        <p class="mb-0" style="font-size:14px;font-weight:500;">{{ __('general.enter_email_code') }}</p>
                        <p class="text-muted" style="font-size:13px;">
                            {{ __('messages.email_code_sent') }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label for="emailCode" class="form-label-custom">{{ __('general.verification_code') }}</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-grid-3x2-gap input-icon-left"></i>
                            <input wire:model="emailCode" id="emailCode" type="text" inputmode="numeric"
                                   class="form-custom has-icon-left text-center tracking-wide @error('emailCode') is-invalid @enderror"
                              autocomplete="one-time-code"     placeholder="000 000" maxlength="6" autofocus
                                   style="letter-spacing:.3em;font-size:1.15rem;font-weight:600;">
                        </div>
                        @error('emailCode')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button wire:click="confirmEmailCode" class="btn btn-accent btn-custom w-100 mb-2">
                        <div wire:loading wire:target="confirmEmailCode" class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <i class="bi bi-shield-check me-2" wire:loading.remove wire:target="confirmEmailCode"></i>
                        {{ __('general.confirm_enable') }}
                    </button>
                    <div class="text-center">
                        <button wire:click="sendEmailCode" class="btn-text-link">
                            <i class="bi bi-arrow-clockwisems-1"></i>{{ __('general.resend_code') }}
                        </button>
                    </div>
                @endif

            @endif

            <div class="text-center mt-3">
                <button wire:click="cancelSetup" class="btn-text-link">
                    <i class="bi bi-arrow-leftms-1"></i>{{ __('general.back') }}
                </button>
            </div>

        @else

            {{-- Method cards grid --}}
            <div class="py-3">
                <p class="text-muted mb-4 text-center" style="font-size:14px;line-height:1.6;">
                    {{ __('messages.two_factor_setup_description') }}
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-3 mb-3">
                    @foreach ($this->availableMethods as $methodKey => $methodInfo)
                        @php $methodEnabled = $this->isMethodEnabled($methodKey); @endphp
                        <div class="d-flex flex-column align-items-center gap-1 px-4 py-3 rounded"
                             style="min-width:160px;border:2px solid {{ $methodEnabled ? 'var(--accent)' : 'var(--border)' }};background:{{ $methodEnabled ? 'var(--accent-subtle, rgba(21,183,108,0.05))' : 'transparent' }};">
                            <i class="bi {{ $methodInfo['icon'] }}" style="font-size:24px;color:{{ $methodEnabled ? 'var(--accent)' : 'var(--text-muted)' }};"></i>
                            <span style="font-size:13px;font-weight:600;">{{ $methodInfo['label'] }}</span>
                            <span style="font-size:11px;font-weight:400;color:var(--text-muted);text-align:center;">{{ $methodInfo['desc'] }}</span>

                            @if ($methodEnabled)
                                <span class="badge-success mt-1" style="font-size:11px;">{{ __('general.enabled') }}</span>
                                @if ($enabled)
                                    <button wire:click="confirmDisableMethod('{{ $methodKey }}')"
                                            class="btn btn-sm btn-outline-danger mt-2 px-3">
                                        <i class="bi bi-shield-slashms-1"></i>{{ __('general.disable') }}
                                    </button>
                                @endif
                            @else
                                <button wire:click="startSetup('{{ $methodKey }}')" class="btn btn-sm btn-accent mt-2 px-3">
                                    <i class="bi bi-plus-circlems-1"></i>{{ __('general.enable') }}
                                </button>
                            @endif
                        </div>
                    @endforeach

                    {{-- Future methods placeholder --}}
                    @foreach ($this->futureMethods as $future)
                        <div class="d-flex flex-column align-items-center gap-1 px-4 py-3 rounded opacity-50"
                             style="min-width:160px;border:2px dashed var(--border);background:transparent;cursor:not-allowed;">
                            <i class="bi {{ $future['icon'] }}" style="font-size:24px;color:var(--text-muted);"></i>
                            <span style="font-size:13px;font-weight:600;color:var(--text-muted);">{{ __('general.phone_method') }}</span>
                            <span style="font-size:11px;font-weight:400;color:var(--text-muted);text-align:center;">{{ __('general.coming_soon') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Recovery codes --}}
            @if ($enabled && $showingRecoveryCodes && !empty($recoveryCodes))
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-size:14px;font-weight:600;">{{ __('general.recovery_codes') }}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0"
                                @click="navigator.clipboard.writeText(`{{ collect($this->recoveryCodes)->implode("\n") }}`)"
                                title="{{ __('general.copy_all') }}">
                            <i class="bi bi-clipboard" style="font-size:12px;"></i>
                        </button>
                    </div>
                    <p class="text-muted mb-2" style="font-size:13px;">{{ __('messages.store_recovery_codes') }}</p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                        @foreach ($this->recoveryCodes as $index => $recoveryCode)
                            <div style="font-family:monospace;font-size:13px;padding:8px 10px;background:var(--bg-subtle);border-radius:6px;border:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                                <span style="color:var(--text-muted);font-size:11px;min-width:18px;">{{ $index + 1 }}.</span>
                                <span>{{ $recoveryCode }}</span>
                            </div>
                        @endforeach
                    </div>
                    <button wire:click="regenerateRecoveryCodes" class="btn btn-sm btn-outline-secondary mt-2">
                        <i class="bi bi-arrow-clockwisems-1"></i>
                        {{ __('general.regenerate_recovery_codes') }}
                    </button>
                </div>
            @endif

            {{-- Disable all --}}
            @if ($enabled)
                <button wire:click="confirmDisable" class="btn btn-outline-danger w-100 mt-2">
                    <i class="bi bi-shield-slash me-2"></i>
                    {{ __('general.disable') }}
                </button>
            @endif

            @if ($confirming)
                <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
                    <div style="background:var(--card-bg,#fff);border-radius:var(--radius-md,12px);max-width:400px;width:100%;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                        <h5 style="font-size:16px;font-weight:600;margin-bottom:8px;">{{ __('general.confirm') }}</h5>
                        <p style="font-size:14px;color:var(--text-muted);margin-bottom:1.5rem;">{{ __('messages.confirm_disable_2fa') }}</p>
                        <div style="display:flex;gap:8px;justify-content:flex-end;">
                            <button wire:click="cancelConfirmation" type="button" class="btn btn-outline-secondary btn-custom" style="font-size:13px;padding:7px 16px;">
                                {{ __('general.cancel') }}
                            </button>
                            <button wire:click="executeConfirmed" type="button" class="btn btn-danger btn-custom" style="font-size:13px;padding:7px 16px;">
                                {{ __('general.disable') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        @endif
    </div>
</div>
