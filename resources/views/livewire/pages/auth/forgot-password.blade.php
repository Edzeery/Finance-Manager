<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate(['email' => ['required', 'string', 'email']]);

        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }
}; ?>

<div class="auth-card animate-fade-in">

    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text">{{ __('general.app_name') }}</span>
        <span class="logo-sub">{{ __('general.forgot_password') }}</span>
    </div>

    <p class="text-muted text-center mb-4" style="font-size:14px">
        {{ __('general.forgot_password_desc') }}
    </p>

    <x-auth-session-status :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">

        <div class="mb-4">
            <label for="email" class="form-label-custom">{{ __('general.email') }}</label>
            <div class="input-icon-wrap">
                <i class="bi bi-envelope input-icon-left"></i>
                <input wire:model="email"
                       id="email" type="email" name="email"
                       required autofocus
                       class="form-custom has-icon-left @error('email') is-invalid @enderror"
                       placeholder="email@example.com">
            </div>
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <x-button submit icon="bi bi-envelope" variant="accent" block wire-target="sendPasswordResetLink">{{ __('general.send_reset_link') }}</x-button>

    </form>

    <div class="auth-footer">
        <a href="{{ route('login') }}" wire:navigate class="link-muted">
            <i class="bi bi-arrow-leftms-1"></i>{{ __('general.back_to_login') }}
        </a>
    </div>

</div>
