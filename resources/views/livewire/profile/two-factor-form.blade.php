<?php

use App\Models\User;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Volt\Component;

new class extends Component
{
    public string $secret = '';

    public string $qrCodeInline = '';

    public string $code = '';

    public bool $enabled = false;

    public bool $showingQrCode = false;

    public array $recoveryCodes = [];

    public bool $showingRecoveryCodes = false;

    public ?string $confirming = null;

    public function mount(TwoFactorAuthenticationService $twoFactor): void
    {
        $this->enabled = Auth::user()->hasTwoFactorEnabled();

        if (! $this->enabled) {
            $this->secret = $twoFactor->generateSecretKey();
            $this->qrCodeInline = $twoFactor->getQrCodeInline(Auth::user(), $this->secret);
        }
    }

    public function showQrCode(): void
    {
        $this->showingQrCode = true;
    }

    public function confirm(TwoFactorAuthenticationService $twoFactor): void
    {
        $this->validate(['code' => 'required|string|size:6']);

        /** @var User $user */
        $user = Auth::user();

        $rateLimitKey = '2fa-confirm-profile:' . $user->id . '|' . request()->ip();

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
        $twoFactor->enable($user);

        $this->recoveryCodes = $twoFactor->generateRecoveryCodes();
        $user->forceFill(['two_factor_recovery_codes' => $this->recoveryCodes])->save();

        $this->enabled = true;
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = true;
        $this->code = '';
    }

    public function confirmDisable(): void
    {
        $this->confirming = 'disable';
    }

    public function executeConfirmed(TwoFactorAuthenticationService $twoFactor): void
    {
        if ($this->confirming === 'disable') {
            $this->disable($twoFactor);
        }
        $this->confirming = null;
    }

    public function cancelConfirmation(): void
    {
        $this->confirming = null;
    }

    public function disable(TwoFactorAuthenticationService $twoFactor): void
    {
        /** @var User $user */
        $user = Auth::user();
        $twoFactor->disable($user);

        $this->enabled = false;
        $this->showingRecoveryCodes = false;
        $this->secret = $twoFactor->generateSecretKey();
        $this->qrCodeInline = $twoFactor->getQrCodeInline($user, $this->secret);
    }

    public function regenerateRecoveryCodes(TwoFactorAuthenticationService $twoFactor): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->recoveryCodes = $twoFactor->generateRecoveryCodes();
        $user->forceFill(['two_factor_recovery_codes' => $this->recoveryCodes])->save();
    }
}; ?>

<div>
    @if (! $this->enabled)
        @if (! $this->showingQrCode)
            <p class="text-muted" style="font-size:14px;">
                {{ __('messages.add_2fa_security') }}
            </p>
            <x-button wire-click="showQrCode" class="mt-2">{{ __('general.setup_2fa') }}</x-button>
        @else
            <div class="text-center my-3">
                <p>{{ __('messages.scan_qr_code') }}</p>
                <img src="{{ $this->qrCodeInline }}" alt="QR Code" style="max-width:200px;">
            </div>

            <div class="mb-3">
                <label class="form-label-custom">{{ __('general.enter_key_manually') }}</label>
                <div class="d-flex align-items-center gap-2">
                    <code style="font-size:14px; padding:8px; background:var(--bg-card); border-radius:6px; word-break:break-all;">{{ $this->secret }}</code>
                </div>
            </div>

            <div class="mb-3">
                <label for="code" class="form-label-custom">{{ __('general.verify_code') }}</label>
                <input wire:model="code" id="code" type="text" inputmode="numeric"
                       class="form-custom @error('code') is-invalid @enderror"
                       placeholder="000000" maxlength="6">
                @error('code')
                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                @enderror
            </div>

            <x-button wire-click="confirm" wire-target="confirm">{{ __('general.confirm_enable') }}</x-button>
        @endif
    @else
        <div class="alert alert-success d-flex align-items-center gap-2" style="font-size:14px;">
            <i class="bi bi-shield-check"></i>
            {{ __('messages.two_factor_enabled') }}
        </div>

        @if ($this->showingRecoveryCodes)
            <div class="mb-3">
                <p><strong>{{ __('general.recovery_codes') }}</strong></p>
                <p class="text-muted" style="font-size:13px;">
                    {{ __('messages.store_recovery_codes') }}
                </p>
                <div style="background:var(--bg-card); padding:12px; border-radius:6px;">
                    @foreach ($this->recoveryCodes as $recoveryCode)
                        <div style="font-family:monospace; font-size:14px; padding:4px 0;">{{ $recoveryCode }}</div>
                    @endforeach
                </div>
                <x-button variant="outline" size="sm" wire-click="regenerateRecoveryCodes" class="mt-2">{{ __('general.regenerate_recovery_codes') }}</x-button>
            </div>
        @endif

        <x-button variant="danger" wire-click="confirmDisable" class="mt-2">{{ __('general.disable') }}</x-button>

        @if ($confirming)
            <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
                <div style="background:var(--card-bg,#fff);border-radius:var(--radius-md,12px);max-width:400px;width:100%;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                    <h5 style="font-size:16px;font-weight:600;margin-bottom:8px;">{{ __('general.confirm') }}</h5>
                    <p style="font-size:14px;color:var(--text-muted);margin-bottom:1.5rem;">{{ __('messages.confirm_disable_2fa') }}</p>
                    <div style="display:flex;gap:8px;justify-content:flex-end;">
                        <x-button variant="outline" wire-click="cancelConfirmation" style="font-size:13px;padding:7px 16px;">{{ __('general.cancel') }}</x-button>
                        <x-button variant="danger" wire-click="executeConfirmed" style="font-size:13px;padding:7px 16px;">{{ __('general.disable') }}</x-button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
