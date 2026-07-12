<nav class="-mx-3 flex flex-1 justify-end">
    @auth
        <a href="{{ route('dashboard') }}" class="btn btn-accent btn-custom" style="padding:6px 16px; font-size:13px">
            <i class="bi bi-grid-1x2-fill me-1"></i>{{ __('general.dashboard') }}
        </a>
    @else
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary btn-custom" style="color:var(--sidebar-text); border-color:rgba(255,255,255,0.2)">
            <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('general.login') }}
        </a>
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="btn btn-sm btn-accent btn-custom ms-2">
                <i class="bi bi-person-plus me-1"></i>{{ __('general.register') }}
            </a>
        @endif
    @endauth
</nav>
