<x-layouts.guest :title="__('account.suspended')">
    <div class="status-page">
        <div class="status-card">
            <div class="status-icon-wrap" style="background:var(--danger-light);color:var(--danger)">
                <x-status-icon domain="user" status="suspended" set="bi" />
            </div>
            <h2>{{ __('account.suspended_title') }}</h2>
            <p class="status-desc">{{ __('account.suspended_description') }}</p>

            @if(auth()->user()->statusRecord?->status_reason)
                <div class="status-info-card" style="background:var(--danger-light);color:var(--danger)">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        <strong>{{ __('account.reason') }}:</strong>
                        {{ auth()->user()->statusRecord->status_reason }}
                    </div>
                </div>
            @endif

            @if(auth()->user()->statusRecord?->expires_at)
                <div class="status-info-card" style="background:var(--warning-light);color:var(--warning)">
                    <i class="bi bi-clock-history"></i>
                    <div>
                        <strong>{{ __('account.suspended_expires') }}:</strong>
                        {{ auth()->user()->statusRecord->expires_at->format('Y/m/d H:i') }}
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
