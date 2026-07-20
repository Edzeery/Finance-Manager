<?php
// resources\views\livewire\pages\onboarding\setup.blade.php
use App\Models\Payment;
use App\Services\OnboardingService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;

new #[Layout('layouts.guest')] class extends Component
{
    #[Rule('required|string|max:255')]
    public string $workspaceName = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->workspaceName = $user->currentWorkspace?->name ?? '';

        if ($user->hasCompletedOnboarding()) {
            $this->redirect(route('dashboard', absolute: false), navigate: true);
            return;
        }

        $plan = $user->pendingPlan;
        if ($plan && !$plan->is_free && !$user->hasActivePaidAccess() && !$user->hasPendingManualPayment()) {
            $pendingOnline = Payment::where('user_id', $user->id)
                ->where('status', 'pending')
                ->whereIn('method', App\Services\OnboardingService::onlineMethods())
                ->first();

            if ($pendingOnline) {
                $this->redirect(route('payment.status', $pendingOnline, absolute: false), navigate: true);
            } else {
                $this->redirect(route('onboarding.plan', absolute: false), navigate: true);
            }
            return;
        }
    }

    public function complete(): void
    {
        $this->validate();

        app(OnboardingService::class)->completeOnboarding(auth()->user(), [
            'name' => $this->workspaceName,
        ]);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function skip(): void
    {
        app(OnboardingService::class)->completeOnboarding(auth()->user());

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="auth-card animate-fade-in">
    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text">{{ __('general.app_name') }}</span>
        <span class="logo-sub">{{ __('onboarding.setup_workspace') }}</span>
    </div>

    <p class="text-muted small mb-4">{{ __('onboarding.setup_workspace_desc') }}</p>

    <form wire:submit="complete">
        <div class="mb-4">
            <label for="workspaceName" class="form-label-custom">{{ __('onboarding.workspace_name') }}</label>
            <input wire:model="workspaceName" id="workspaceName" type="text"
                class="form-custom @error('workspaceName') is-invalid @enderror"
                placeholder="{{ __('onboarding.workspace_placeholder') }}">
            @error('workspaceName')
                <div class="text-danger mt-1 small">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-accent btn-custom w-100 mb-2"
            wire:loading.attr="disabled" wire:target="complete">
            <div wire:loading wire:target="complete" class="spinner-border spinner-border-sm ms-2" role="status"></div>
            {{ __('onboarding.finish') }}
        </button>

        <button type="button" class="btn btn-outline-accent btn-custom w-100" wire:click="skip">
            {{ __('onboarding.skip') }}
        </button>
    </form>
</div>
