<div class="profile-dropdown" x-show="open" @click.outside="close()" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak>
    <a href="{{ route('super.admin.account.profile') }}" class="profile-dropdown-item {{ request()->routeIs('super.admin.account.profile') ? 'active' : '' }}" wire:navigate @click="close()">
        <i class="bi bi-person-fill"></i>
        <span>{{ __('general.profile') }}</span>
    </a>
    <a href="{{ route('super.admin.settings.index') }}" class="profile-dropdown-item {{ request()->routeIs('super.admin.settings*') ? 'active' : '' }}" wire:navigate @click="close()">
        <i class="bi bi-gear-fill"></i>
        <span>{{ __('super-admin.settings') }}</span>
    </a>
    <a href="{{ route('super.admin.activity-log') }}" class="profile-dropdown-item {{ request()->routeIs('super.admin.activity-log') ? 'active' : '' }}" wire:navigate @click="close()">
        <i class="bi bi-clock-history"></i>
        <span>{{ __('super-admin.activity_log') }}</span>
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