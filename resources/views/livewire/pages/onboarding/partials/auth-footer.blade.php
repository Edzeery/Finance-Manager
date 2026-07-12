@php $isOnboarded = auth()->check() && auth()->user()->hasCompletedOnboarding(); @endphp
<div class="auth-footer mt-3">
    @if ($isOnboarded)
        <a href="{{ route('account.subscriptions') }}" wire:navigate>{{ __('settings.subscriptions') }}</a>
        <span class="mx-2">|</span>
        <a href="{{ route('dashboard') }}" wire:navigate>{{ __('onboarding.back_to_dashboard') }}</a>
    @else
        <a href="{{ route('onboarding.plan') }}" wire:navigate>{{ __('onboarding.back_to_plans') }}</a>
        <span class="mx-2">|</span>
        <a href="{{ route('dashboard') }}" wire:navigate>{{ __('onboarding.back_to_dashboard') }}</a>
    @endif
</div>
