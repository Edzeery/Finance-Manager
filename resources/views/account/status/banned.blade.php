<x-layouts.guest :title="__('account.banned')">
    <div class="min-vh-100 d-flex align-items-center justify-content-center p-4">
        <div class="text-center" style="max-width: 480px">
            <div class="mb-4">
                {!! \Edzeery\MyStatusKit\Facades\Status::for('user', 'banned')->icon('bi', 'status-icon-lg') !!}
            </div>
            <h2 class="mb-3">{{ __('account.banned_title') }}</h2>
            <p class="text-muted mb-4">
                {{ __('account.banned_description') }}
            </p>
            @if(auth()->user()->statusRecord?->status_reason)
                <div class="alert alert-danger mb-4">
                    <i class="bi bi-x-octagon me-1"></i>
                    <strong>{{ __('general.reason') }}:</strong> {{ auth()->user()->statusRecord->status_reason }}
                </div>
            @endif
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="{{ route('logout') }}" class="btn btn-outline-secondary"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    {{ __('auth.logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</x-layouts.guest>
