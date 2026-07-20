<?php

use App\Livewire\Forms\LoginForm;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.super-admin.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $throttleKey = 'super-admin:' . Str::transliterate(Str::lower($this->form->email) . '|' . request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            event(new Lockout(request()));
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'form.email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $user = User::where('email', $this->form->email)->first();

        if (!$user || !$user->hasRole('super_admin')) {
            RateLimiter::hit($throttleKey);
            $this->addError('form.email', __('auth.failed'));
            return;
        }

        try {
            $this->form->authenticate();
        } catch (ValidationException) {
            RateLimiter::hit($throttleKey);
            $this->addError('form.email', __('auth.failed'));
            return;
        }

        if (Session::has('two_factor:user_id')) {
            $this->redirectRoute('two-factor.challenge', navigate: true);
            return;
        }

        RateLimiter::clear($throttleKey);
        Session::regenerate();
        $this->redirect(route('super.admin.dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="auth-card animate-fade-in" style="max-width:420px; margin:80px auto">
    <div class="auth-logo">
        <div class="logo-icon" style="background:linear-gradient(135deg,#6366F1,#8B5CF6)">
            <i class="bi bi-shield-shaded"></i>
        </div>
        <span class="logo-text">{{ __('super-admin.super_dashboard') }}</span>
        <span class="logo-sub">{{ __('super-admin.login_title') }}</span>
    </div>

    <x-auth-session-status :status="session('status')" />

    <form wire:submit="login">
        <div class="mb-3">
            <label for="email" class="form-label-custom">{{ __('general.email') }}</label>
            <input wire:model="form.email" id="email" type="email" name="email" required autofocus
                   class="form-custom @error('form.email') is-invalid @enderror"
                   placeholder="admin@example.com">
            @error('form.email')
                <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="form-label-custom">{{ __('general.password') }}</label>
            <x-password-input wire:model="form.password" id="password" name="password" required
                error="form.password" />
            @error('form.password')
                <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-accent btn-custom w-100 py-2" style="background:linear-gradient(135deg,#6366F1,#8B5CF6); border:none">
            <div wire:loading wire:target="login" class="spinner-border spinner-border-sm me-2" role="status"></div>
            <i class="bi bi-shield-lock me-2" wire:loading.remove wire:target="login"></i>
            {{ __('super-admin.login_btn') }}
        </button>
    </form>

    <div class="auth-divider">{{ __('general.or') }}</div>

    <div class="auth-footer">
        <a href="{{ route('login') }}" wire:navigate style="color:var(--accent); text-decoration:none">
            <i class="bi bi-arrow-leftms-1"></i>{{ __('super-admin.back_to_user_login') }}
        </a>
    </div>
</div>
