<?php

use App\Models\User;
use App\Services\RedirectService;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $code = '';
    public ?string $recoveryCode = null;
    public string $mode = 'code';
    public string $authMethod = 'app';
    public array $methods = ['app'];
    public string $emailCode = '';
    public bool $emailCodeSent = false;

    public function mount(TwoFactorAuthenticationService $twoFactor): void
    {
        $userId = Session::get('two_factor:user_id');

        if (!$userId) {
            return;
        }

        $user = User::find($userId);
        if ($user && $user->hasTwoFactorEnabled()) {
            $this->methods = $user->two_factor_methods ?? ['app'];
            $this->authMethod = $this->methods[0] ?? 'app';

            if ($this->authMethod === TwoFactorAuthenticationService::METHOD_EMAIL) {
                $this->sendEmailCodeIfNeeded($twoFactor, $user);
            }
        }
    }

    protected function sendEmailCodeIfNeeded(TwoFactorAuthenticationService $twoFactor, User $user): void
    {
        if (!$user->two_factor_email_code_at || now()->diffInMinutes($user->two_factor_email_code_at) > 1) {
            $twoFactor->sendEmailCode($user);
            $this->emailCodeSent = true;
        }
    }

    public function selectMethod(string $method, TwoFactorAuthenticationService $twoFactor): void
    {
        $this->authMethod = $method;
        $this->resetErrorBag();
        $this->code = '';
        $this->emailCode = '';

        if ($method === TwoFactorAuthenticationService::METHOD_EMAIL) {
            $userId = Session::get('two_factor:user_id');
            $user = User::find($userId);
            if ($user) {
                $this->sendEmailCodeIfNeeded($twoFactor, $user);
            }
        }
    }

    public function resendEmailCode(TwoFactorAuthenticationService $twoFactor): void
    {
        $userId = Session::get('two_factor:user_id');
        if (!$userId) {
            return;
        }

        $user = User::find($userId);
        if ($user && $user->hasTwoFactorEnabled()) {
            $twoFactor->sendEmailCode($user);
            $this->emailCodeSent = true;
            $this->resetErrorBag();
        }
    }

    public function verify(TwoFactorAuthenticationService $twoFactor): void
    {
        $userId = Session::get('two_factor:user_id');

        if (!$userId) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $user = User::find($userId);

        if (!$user || !$user->hasTwoFactorEnabled()) {
            Session::forget('two_factor:user_id');
            Session::forget('two_factor:remember');
            Session::forget('two_factor:methods');
            $this->redirect(route('login'), navigate: true);
            return;
        }

        if ($this->authMethod === TwoFactorAuthenticationService::METHOD_EMAIL) {
            $this->validate(['emailCode' => 'required|string|size:6']);

            if (!$twoFactor->verifyEmailCode($user, $this->emailCode)) {
                $this->addError('emailCode', trans('auth.invalid_2fa_code'));
                return;
            }
        } elseif ($this->mode === 'code') {
            $this->validate(['code' => 'required|string|size:6']);
            $secret = $user->getGoogle2faSecret();
            if (!$secret || !$twoFactor->verify($secret, $this->code)) {
                $this->addError('code', trans('google2fa.error_messages.wrong_otp'));
                return;
            }
        } else {
            $this->validate(['recoveryCode' => 'required|string']);
            if (!$twoFactor->verifyRecoveryCode($user, $this->recoveryCode)) {
                $this->addError('recoveryCode', __('messages.invalid_recovery_code'));
                return;
            }
        }

        Auth::login($user, Session::get('two_factor:remember', false));
        Session::forget('two_factor:user_id');
        Session::forget('two_factor:remember');
        Session::forget('two_factor:methods');
        Session::regenerate();

        $intended = app(RedirectService::class)->getIntendedUrl($user);
        $this->redirect($intended);
    }

    public function switchMode(): void
    {
        $this->mode = $this->mode === 'code' ? 'recovery' : 'code';
        $this->resetErrorBag();
    }
}; ?>

<div class="auth-card animate-fade-in">
    <div class="settings-card col-md-6 mx-auto" style="border:none;box-shadow:none;padding:0;">
        <div class="auth-logo">
            <div class="logo-icon"><i class="bi bi-shield-lock"></i></div>
            <span class="logo-text">{{ __('general.app_name') }}</span>
            <span class="logo-sub">{{ __('general.two_factor_auth') }}</span>
        </div>

        {{-- Method selector --}}
        @if (count($methods) > 1)
            <div class="d-flex justify-content-center gap-2 mb-4">
                @foreach ($methods as $method)
                    <button wire:click="selectMethod('{{ $method }}')" type="button"
                        class="btn btn-sm px-3 py-2 d-flex align-items-center gap-1"
                        style="border:2px solid {{ $authMethod === $method ? 'var(--accent)' : 'var(--border)' }};background:{{ $authMethod === $method ? 'var(--accent-subtle, rgba(21,183,108,0.05))' : 'transparent' }};color:{{ $authMethod === $method ? 'var(--accent)' : 'var(--text-muted)' }};font-weight:{{ $authMethod === $method ? '600' : '400' }};transition:all 0.2s;border-radius:var(--radius-sm,8px);">
                        @if ($method === 'app')
                            <i class="bi bi-phone"></i>
                        @else
                            <i class="bi bi-envelope"></i>
                        @endif
                        <span>{{ $method === 'app' ? __('general.authenticator_app') : __('general.email_method') }}</span>
                    </button>
                @endforeach
            </div>
        @endif

        @if ($authMethod === 'email')
            <form wire:submit="verify">
                <p class="text-muted text-center mb-4" style="font-size:14px">
                    {{ __('messages.enter_email_auth_code') }}
                </p>

                @if ($emailCodeSent)
                    <div class="mb-4">
                        <label for="emailCode" class="form-label-custom">{{ __('general.verification_code') }}</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope input-icon-left"></i>
                            <input wire:model="emailCode" id="emailCode" type="text" inputmode="numeric"
                                autocomplete="one-time-code"
                                class="form-custom has-icon-left text-center tracking-wide @error('emailCode') is-invalid @enderror"
                                placeholder="000 000" maxlength="6" required autofocus
                                style="letter-spacing:.3em; font-size:1.25rem; font-weight:600;">
                        </div>
                        @error('emailCode')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-accent btn-custom w-100 mb-3">
                        <div wire:loading wire:target="verify" class="spinner-border spinner-border-sm me-2"
                            role="status"></div>
                        <i class="bi bi-check-circle me-2" wire:loading.remove wire:target="verify"></i>
                        {{ __('general.verify') }}
                    </button>

                    <div class="text-center">
                        <button type="button" wire:click="resendEmailCode" class="btn-text-link">
                            <div wire:loading wire:target="resendEmailCode"
                                class="spinner-border spinner-border-sm me-1" role="status"
                                style="width:12px;height:12px;"></div>
                            <i class="bi bi-arrow-clockwise me-1" wire:loading.remove wire:target="resendEmailCode"></i>
                            {{ __('general.resend_code') }}
                        </button>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="spinner-border text-accent mb-3" role="status" style="width:2rem;height:2rem;">
                        </div>
                        <p class="text-muted">{{ __('messages.sending_code') }}</p>
                    </div>
                @endif
            </form>
        @elseif ($mode === 'code')
            <form wire:submit="verify">
                <p class="text-muted text-center mb-4" style="font-size:14px">
                    {{ __('messages.enter_auth_code') }}
                </p>

                <div class="mb-4">
                    <label for="code" class="form-label-custom">{{ __('general.authentication_code') }}</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-grid-3x2-gap input-icon-left"></i>
                        <input wire:model="code" id="code" type="text" inputmode="numeric"
                            autocomplete="one-time-code"
                            class="form-custom has-icon-left text-center tracking-wide @error('code') is-invalid @enderror"
                            placeholder="000 000" maxlength="6" required autofocus
                            style="letter-spacing:.3em; font-size:1.25rem; font-weight:600;">
                    </div>
                    @error('code')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-accent btn-custom w-100 mb-3">
                    <div wire:loading wire:target="verify" class="spinner-border spinner-border-sm me-2" role="status">
                    </div>
                    <i class="bi bi-check-circle me-2" wire:loading.remove wire:target="verify"></i>
                    {{ __('general.verify') }}
                </button>

                <div class="text-center">
                    <button type="button" wire:click="switchMode" class="btn-text-link">
                        <i class="bi bi-key me-1"></i>{{ __('general.use_recovery_code') }}
                    </button>
                </div>
            </form>
        @else
            <form wire:submit="verify">
                <p class="text-muted text-center mb-4" style="font-size:14px">
                    {{ __('messages.enter_recovery_code') }}
                </p>

                <div class="mb-4">
                    <label for="recoveryCode" class="form-label-custom">{{ __('general.recovery_code') }}</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-key input-icon-left"></i>
                        <input wire:model="recoveryCode" id="recoveryCode" type="text" autocomplete="off"
                            class="form-custom has-icon-left text-center @error('recoveryCode') is-invalid @enderror"
                            placeholder="XXXXXX-XXXXXX" required autofocus style="letter-spacing:.05em;">
                    </div>
                    @error('recoveryCode')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-accent btn-custom w-100 mb-3">
                    <div wire:loading wire:target="verify" class="spinner-border spinner-border-sm me-2"
                        role="status"></div>
                    <i class="bi bi-check-circle me-2" wire:loading.remove wire:target="verify"></i>
                    {{ __('general.verify') }}
                </button>

                <div class="text-center">
                    <button type="button" wire:click="switchMode" class="btn-text-link">
                        <i class="bi bi-phone me-1"></i>{{ __('general.use_auth_code') }}
                    </button>
                </div>
            </form>
        @endif

    </div>

    <div class="auth-divider">{{ __('general.or') }}</div>

    <div class="auth-footer">
        <a href="{{ route('login') }}" wire:navigate class="link-muted">
            <i class="bi bi-arrow-left me-1"></i>{{ __('general.back_to_login') }}
        </a>
    </div>

</div>
