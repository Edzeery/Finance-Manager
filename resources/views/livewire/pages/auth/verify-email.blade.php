<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        Auth::user()->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    public function checkVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirect(route('onboarding.plan', absolute: false), navigate: true);
        }
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="auth-card animate-fade-in" wire:poll.3s="checkVerification">

    <div class="auth-logo">
        <div class="logo-icon">
            <i class="bi bi-envelope-check" style="font-size:1.4rem;"></i>
        </div>
        <span class="logo-text">{{ __('general.app_name') }}</span>
        <span class="logo-sub">{{ __('general.verify_email') }}</span>
    </div>

    <p class="text-muted text-center mb-4" style="font-size:14px; line-height:1.6">
        {{ __('general.verify_email_desc') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert-success-custom mb-3">
            <i class="bi bi-check-circle-fill ms-2"></i>
            {{ __('general.verification_link_sent') }}
        </div>
    @endif

    <div class="d-flex gap-3 mt-2">
        <button wire:click="sendVerification" class="btn btn-accent btn-custom flex-grow-1">
            <div wire:loading wire:target="sendVerification" class="spinner-border spinner-border-sm ms-2" role="status"></div>
            <i class="bi bi-envelope ms-2" wire:loading.remove wire:target="sendVerification"></i>
            {{ __('general.resend_verification_email') }}
        </button>
        <button wire:click="logout" class="btn btn-outline-secondary btn-custom btn-icon" title="{{ __('general.logout') }}">
            <i class="bi bi-box-arrow-right"></i>
        </button>
    </div>

</div>
