<x-layouts.guest :title="__('account.banned')">
    <div class="status-page">
        <div class="status-card">
            <div class="status-icon-wrap" style="background:rgba(239,68,68,0.12);color:var(--danger)">
                <x-status-icon domain="user" status="banned" set="bi" />
            </div>
            <h2>{{ __('account.banned_title') }}</h2>
            <p class="status-desc">{{ __('account.banned_description') }}</p>

            @if(auth()->user()->statusRecord?->status_reason)
                <div class="status-info-card" style="background:rgba(239,68,68,0.08);color:var(--danger)">
                    <i class="bi bi-x-octagon"></i>
                    <div>
                        <strong>{{ __('account.reason') }}:</strong>
                        {{ auth()->user()->statusRecord->status_reason }}
                    </div>
                </div>
            @endif

            <div class="status-actions">
                <a href="mailto:support@{{ config('app.domain', 'example.com') }}" class="btn-error btn-error-secondary">
                    <i class="bi bi-envelope"></i>
                    {{ __('general.contact_support') }}
                </a>
                <a href="{{ route('logout') }}" class="btn-error btn-error-primary"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    {{ __('actions.log_out') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>

            <div class="status-footer">{{ config('app.name') }} &mdash; {{ now()->year }}</div>
        </div>
    </div>
</x-layouts.guest>
