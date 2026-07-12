<div class="profile-dropdown" x-show="open" @click.outside="close()" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak>
    <a href="{{ route('account.profile') }}" class="profile-dropdown-item {{ request()->routeIs('account.profile') ? 'active' : '' }}" wire:navigate @click="close()">
        <i class="bi bi-person-fill"></i>
        <span>{{ __('general.profile') }}</span>
    </a>
    <a href="{{ route('account.settings') }}" class="profile-dropdown-item {{ request()->routeIs('account.settings') ? 'active' : '' }}" wire:navigate @click="close()">
        <i class="bi bi-sliders2"></i>
        <span>{{ __('general.preferences') }}</span>
    </a>
    <a href="{{ route('account.subscriptions') }}" class="profile-dropdown-item {{ request()->routeIs('account.subscriptions') ? 'active' : '' }}" wire:navigate @click="close()">
        <i class="bi bi-credit-card-fill"></i>
        <span>{{ __('settings.subscriptions') }}</span>
    </a>
    <a href="{{ route('account.invoices.index') }}" class="profile-dropdown-item {{ request()->routeIs('account.invoices.*') ? 'active' : '' }}" wire:navigate @click="close()">
        <i class="bi bi-receipt"></i>
        <span>{{ __('settings.invoices') }}</span>
    </a>
    <a href="{{ route('account.settings.developer') }}" class="profile-dropdown-item {{ request()->routeIs('account.settings.developer*') ? 'active' : '' }}" wire:navigate @click="close()">
        <i class="bi bi-code-slash"></i>
        <span>{{ __('general.developers') }}</span>
    </a>
    <a href="{{ route('two-factor.setup') }}" class="profile-dropdown-item" wire:navigate @click="close()">
        <i class="bi bi-shield-lock-fill"></i>
        <span>{{ __('general.security') }}</span>
    </a>
    <hr class="profile-dropdown-divider">
    <a href="{{ route('settings.index') }}" class="profile-dropdown-item {{ request()->routeIs('settings.index') ? 'active' : '' }}" wire:navigate @click="close()">
        <i class="bi bi-people-fill"></i>
        <span>{{ __('workspace.team') }}</span>
    </a>
    <hr class="profile-dropdown-divider">

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="profile-dropdown-item profile-dropdown-logout" @click="close()">
            <i class="bi bi-box-arrow-right"></i>
            <span>{{ __('general.logout') }}</span>
        </button>
    </form>
</div>
