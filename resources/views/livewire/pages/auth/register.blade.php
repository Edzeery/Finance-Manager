<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Rules\PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', new PasswordRule],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['locale']   = session('locale', app()->getLocale());
        $validated['currency'] = session('currency', config('payment.default_currency'));

        $user = User::create($validated);
        event(new Registered($user));
        Auth::login($user);

        $token = session('invitation_token');
        if ($token) {
            session()->forget('invitation_token');
            $this->redirect(route('invitations.accept', $token), navigate: true);
            return;
        }

        $this->redirect(route('verification.notice', absolute: false), navigate: true);
    }
}; ?>

<div class="auth-card animate-fade-in">

    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text">{{ __('general.app_name') }}</span>
        <span class="logo-sub">{{ __('general.register') }}</span>
    </div>

    <form wire:submit="register" autocomplete="on">

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label-custom">{{ __('general.name') }}</label>
            <div class="input-icon-wrap">
                <i class="bi bi-person input-icon-left"></i>
                <input wire:model="name"
                       id="name" type="text" name="name"
                       required autofocus autocomplete="name"
                       class="form-custom has-icon-left @error('name') is-invalid @enderror"
                       placeholder="{{ __('general.full_name') }}">
            </div>
            @error('name')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label-custom">{{ __('general.email') }}</label>
            <div class="input-icon-wrap">
                <i class="bi bi-envelope input-icon-left"></i>
                <input wire:model="email"
                       id="email" type="email" name="email"
                       required autocomplete="username"
                       class="form-custom has-icon-left @error('email') is-invalid @enderror"
                       placeholder="email@example.com">
            </div>
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label-custom">{{ __('general.password') }}</label>
            <x-password-input wire:model="password" id="password" name="password" required
                autocomplete="new-password" error="password" />
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label-custom">{{ __('general.confirm_password') }}</label>
            <x-password-input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation"
                required autocomplete="new-password" />
        </div>

        <button type="submit" class="btn btn-accent btn-custom w-100">
            <div wire:loading wire:target="register" class="spinner-border spinner-border-sm me-2" role="status"></div>
            <i class="bi bi-person-plus me-2" wire:loading.remove wire:target="register"></i>
            {{ __('general.register') }}
        </button>

    </form>

    <div class="auth-divider">{{ __('general.or') }}</div>

    <div class="auth-footer">
        @lang('general.has_account')
        <a href="{{ route('login') }}" wire:navigate>{{ __('general.login') }}</a>
    </div>

</div>

