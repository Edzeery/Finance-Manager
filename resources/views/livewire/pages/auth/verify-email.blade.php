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
        <x-button wire-click="sendVerification" icon="bi bi-envelope" variant="accent" class="flex-grow-1" wire-target="sendVerification">{{ __('general.resend_verification_email') }}</x-button>
        <x-button wire-click="logout" variant="outline" icon="bi bi-box-arrow-right" title="{{ __('general.logout') }}"></x-button>
    </div>

</div>
