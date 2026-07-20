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

        <button type="submit" class="btn btn-accent btn-custom w-100">
            <div wire:loading wire:target="sendPasswordResetLink" class="spinner-border spinner-border-sm ms-2" role="status"></div>
            <i class="bi bi-envelope ms-2" wire:loading.remove wire:target="sendPasswordResetLink"></i>
            {{ __('general.send_reset_link') }}
        </button>

    </form>

    <div class="auth-footer">
        <a href="{{ route('login') }}" wire:navigate class="link-muted">
            <i class="bi bi-arrow-leftms-1"></i>{{ __('general.back_to_login') }}
        </a>
    </div>

</div>
