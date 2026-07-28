<nav class="-mx-3 flex flex-1 justify-end">
    @auth
        <x-button href="{{ route('dashboard') }}" icon="bi bi-grid-1x2-fill" style="padding:6px 16px; font-size:13px">{{ __('general.dashboard') }}</x-button>
    @else
        <x-button href="{{ route('login') }}" size="sm" variant="outline" icon="bi bi-box-arrow-in-right" style="color:var(--sidebar-text); border-color:rgba(255,255,255,0.2)">{{ __('general.login') }}</x-button>
        @if (Route::has('register'))
            <x-button href="{{ route('register') }}" size="sm" icon="bi bi-person-plus" class="ms-2">{{ __('general.register') }}</x-button>
        @endif
    @endauth
</nav>
