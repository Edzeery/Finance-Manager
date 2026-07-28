<?php

use App\Services\NotificationService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    public function resetPassword(NotificationService $notificationService): void
    {
        $this->validate([
            'token'    => ['required'],
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($notificationService) {
                $user->forceFill([
                    'password'       => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
                $notificationService->passwordChanged($user->id);
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status));
        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="auth-card animate-fade-in">

    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text">{{ __('general.app_name') }}</span>
        <span class="logo-sub">{{ __('general.reset_password') }}</span>
    </div>

    <form wire:submit="resetPassword" autocomplete="off">

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label-custom">{{ __('general.email') }}</label>
            <div class="input-icon-wrap">
                <i class="bi bi-envelope input-icon-left"></i>
                <input wire:model="email"
                       id="email" type="email" name="email"
                       required autofocus autocomplete="username"
                       class="form-custom has-icon-left @error('email') is-invalid @enderror"
                       placeholder="email@example.com">
            </div>
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- New Password --}}
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

        <x-button submit icon="bi bi-shield-check" variant="accent" block wire-target="resetPassword">{{ __('general.reset_password') }}</x-button>

    </form>

    <div class="auth-footer">
        <a href="{{ route('login') }}" wire:navigate class="link-muted">
            <i class="bi bi-arrow-leftms-1"></i>{{ __('general.back_to_login') }}
        </a>
    </div>

</div>

