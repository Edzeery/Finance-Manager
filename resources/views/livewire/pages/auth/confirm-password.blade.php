<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    public function confirmPassword(): void
    {
        $this->validate(['password' => ['required', 'string']]);

        if (! Auth::guard('web')->validate([
            'email'    => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="auth-card animate-fade-in">

    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text">{{ __('general.app_name') }}</span>
        <span class="logo-sub">{{ __('general.confirm_password') }}</span>
    </div>

    <p class="text-muted text-center mb-4" style="font-size:14px">
        {{ __('general.confirm_password_desc') }}
    </p>

    <form wire:submit="confirmPassword">

        <div class="mb-4">
            <label for="password" class="form-label-custom">{{ __('general.password') }}</label>
            <x-password-input wire:model="password" id="password" name="password" required
                autocomplete="current-password" error="password" />
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <x-button submit icon="bi bi-shield-check" variant="accent" block>{{ __('general.confirm') }}</x-button>

    </form>

</div>

