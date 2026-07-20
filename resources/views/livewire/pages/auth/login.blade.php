<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();

        if (Session::has('two_factor:user_id')) {
            $this->redirectRoute('two-factor.challenge', navigate: true);
            return;
        }

        Session::regenerate();

        $token = Session::get('invitation_token');
        if ($token) {
            Session::forget('invitation_token');
            $this->redirect(route('invitations.accept', $token), navigate: true);
            return;
        }

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="auth-card animate-fade-in">

    {{-- Logo & Header --}}
    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text">{{ __('general.app_name') }}</span>
        <span class="logo-sub">{{ __('general.login') }}</span>
    </div>

    <x-auth-session-status :status="session('status')" />

    <form wire:submit="login" autocomplete="on">

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label-custom">{{ __('general.email') }}</label>
            <div class="input-icon-wrap">
                <i class="bi bi-envelope input-icon-left"></i>
                <input wire:model="form.email" id="email" type="email" name="email" required autofocus
                    autocomplete="username" class="form-custom has-icon-left @error('form.email') is-invalid @enderror"
                    placeholder="email@example.com">
            </div>
            @error('form.email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label-custom mb-0">{{ __('general.password') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate class="link-muted">
                        {{ __('general.forgot_password') }}
                    </a>
                @endif
            </div>

            <x-password-input wire:model="form.password" id="password" name="password" required
                autocomplete="current-password" error="form.password" />
            @error('form.password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="mb-4">
            <label class="form-check-custom">
                <input wire:model="form.remember" type="checkbox" name="remember">
                <span>{{ __('general.remember_me') }}</span>
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-accent btn-custom w-100">
            <div wire:loading wire:target="login" class="spinner-border spinner-border-sm me-2" role="status"></div>
            <i class="bi bi-box-arrow-in-right me-2" wire:loading.remove wire:target="login"></i>
            {{ __('general.login') }}
        </button>

    </form>

    <div class="auth-divider">{{ __('general.or') }}</div>

    <div class="auth-footer mb-2">
        @lang('general.no_account')
        <a href="{{ route('register') }}" wire:navigate>{{ __('general.register') }}</a>
    </div>

    <div class="text-center mt-3 pt-2" style="border-top:1px solid var(--border);">
        <a href="{{ route('super.admin.login') }}" wire:navigate class="link-muted" style="opacity:.7">
            <i class="bi bi-shieldms-1"></i>{{ __('super-admin.enter_panel') }}
        </a>
    </div>

</div>
