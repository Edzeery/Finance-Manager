{{-- Password --}}
<div class="settings-card mb-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle"
            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
            <i class="bi bi-shield-lock" style="color:var(--accent);font-size:16px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                {{ __('general.update_password') }}</h5>
            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                {{ __('profile.password_help') }}</p>
        </div>
    </div>
    <livewire:profile.update-password-form />
</div>

{{-- Two-Factor Authentication --}}
<div class="settings-card mb-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle"
            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
            <i class="bi bi-shield-check" style="color:var(--accent);font-size:16px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                {{ __('settings.two_factor') }}</h5>
            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                {{ __('messages.add_2fa_security') }}</p>
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <span style="font-size:14px;font-weight:500;">{{ __('general.status') }}</span>
            @if ($user->hasTwoFactorEnabled())
                <x-status-badge domain="general" status="yes" set="bi" />
            @else
                <x-status-badge domain="general" status="no" set="bi" />
            @endif
        </div>
        <x-button href="{{ route('two-factor.setup') }}" size="sm"
            icon="bi bi-shield-plus">{{ __('general.manage') }}</x-button>
    </div>
</div>

{{-- All Sessions --}}
<div class="settings-card mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle"
                style="width:36px;height:36px;background:rgba(59,130,246,0.1);flex-shrink:0;">
                <i class="bi bi-pc-display" style="color:var(--accent);font-size:16px;"></i>
            </div>
            <div>
                <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                    {{ __('settings.active_sessions') }}</h5>
                <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                    {{ __('settings.active_sessions_help') }}</p>
            </div>
        </div>
        @if ($allSessions->where('status', '!=', 'logged_out')->count() > 1)
            <livewire:revoke-all-sessions />
        @endif
    </div>

    <div class="sessions-list">
        @forelse ($allSessions as $session)
            @php
                $statusBadge = match (true) {
                    $session->is_current => 'current_device',
                    $session->status === 'active' => 'session_active',
                    $session->status === 'inactive' => 'session_inactive',
                    default => 'session_ended',
                };
                $deviceIcon = match ($session->device) {
                    'phone' => 'bi-phone',
                    'tablet' => 'bi-tablet',
                    default => 'bi-pc-display',
                };
            @endphp
            <div
                class="session-item d-flex align-items-center justify-content-between py-3 {{ $loop->first ? '' : 'border-top' }}">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                        style="width:40px;height:40px;background:{{ $session->is_current ? 'rgba(21,183,108,0.1)' : 'var(--bg-secondary)' }};flex-shrink:0;">
                        <i class="bi {{ $deviceIcon }}"
                            style="color:{{ $session->is_current ? 'var(--accent)' : 'var(--text-muted)' }};font-size:18px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-weight:500;font-size:14px;">{{ $session->browser }} on
                                {{ $session->os }}</span>
                            <x-status-badge domain="general" :status="$statusBadge" set="bi" size="xs" />
                        </div>
                        <div class="d-flex gap-1" style="font-size:12px;color:var(--text-muted);">
                            <i class="bi bi-globe"></i>{{ $session->ip_address }}
                            @if ($session->login_at)
                                &middot;
                                <i class="bi bi-box-arrow-in-right"></i>{{ $session->login_at->diffForHumans() }}
                            @endif
                            @if ($session->last_activity)
                                &middot;
                                <i
                                    class="bi bi-clock"></i>{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                </div>
                @if (!$session->is_current && $session->id)
                    <form method="POST" action="{{ route('settings.account.sessions.revoke', $session->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm"
                            style="background:transparent;color:var(--text-muted);border:1px solid var(--border-color);"
                            title="{{ __('settings.revoke_session') }}">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="text-center py-3">
                <x-empty-state icon="bi bi-pc-display" :title="__('settings.no_sessions')" />
            </div>
        @endforelse
    </div>
</div>

{{-- Danger Zone --}}
<div class="settings-card" style="border-color:rgba(239,68,68,0.2);">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle"
            style="width:36px;height:36px;background:rgba(239,68,68,0.1);flex-shrink:0;">
            <i class="bi bi-exclamation-triangle" style="color:var(--danger);font-size:16px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="font-weight:600;font-size:15px;color:var(--danger);">
                {{ __('general.danger_zone') }}</h5>
            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                {{ __('profile.delete_account_help') }}</p>
        </div>
    </div>
    <livewire:profile.delete-user-form />
</div>
