<x-app-layout>
    <x-slot:title>{{ __('notifications.page_title') ?? __('general.notifications') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('notifications.page_title') ?? __('general.notifications') }}</x-slot>
    <x-slot:page-description>{{ __('notifications.page_description') ?? '' }}</x-slot>

    <x-filter-tabs :tabs="[
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-bell'],
        'unread' => ['label' => __('general.unread'), 'count' => $countUnread, 'icon' => 'bi-bell-fill'],
        'read' => ['label' => __('general.read'), 'count' => $countRead, 'icon' => 'bi-check-circle'],
    ]" current="{{ $status ?? 'all' }}" keyParam="status" defaultKey="all"
        :preserve="['per_page']" />

    <div class="card-custom">
        <div class="card-body p-0">
            @php $locale = app()->getLocale(); @endphp

            @forelse($notifications as $notification)
                <div class="d-flex align-items-start gap-3 px-4 py-3 {{ !$notification->is_read ? '' : '' }}" style="{{ !$notification->is_read ? 'background:rgba(21,183,108,0.03)' : '' }}; border-bottom:1px solid var(--border); transition:background 0.2s">
                    @php
                        $notifIcon = match($notification->type) {
                            'budget_exceeded', 'budget_nearing_limit' => ['bg' => 'rgba(239,68,68,0.1)', 'color' => 'var(--danger)', 'icon' => 'bi-exclamation-triangle'],
                            'debt_reminder' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => 'var(--warning)', 'icon' => 'bi-credit-card-2-front'],
                            'goal_achieved', 'goal_milestone' => ['bg' => 'rgba(34,197,94,0.1)', 'color' => 'var(--success)', 'icon' => 'bi-flag'],
                            'goal_deadline' => ['bg' => 'rgba(59,130,246,0.1)', 'color' => 'var(--info)', 'icon' => 'bi-clock'],
                            'zakat_reminder' => ['bg' => 'rgba(139,92,246,0.1)', 'color' => 'var(--sa-indigo)', 'icon' => 'bi-heart'],
                            'role_changed' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => 'var(--warning)', 'icon' => 'bi-shield-check'],
                            default => ['bg' => 'rgba(59,130,246,0.1)', 'color' => 'var(--info)', 'icon' => 'bi-info-circle'],
                        };
                    @endphp
                    <div style="flex-shrink:0; width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:{{ $notifIcon['bg'] }}; color:{{ $notifIcon['color'] }}">
                        <i class="bi {{ $notifIcon['icon'] }}"></i>
                    </div>
                    <div style="flex:1; min-width:0">
                        <div style="font-size:14px; font-weight:500; color:var(--text)">
                            {{ $notification->{'title_' . $locale} }}
                        </div>
                        <div style="font-size:13px; color:var(--text-muted); margin-top:2px">
                            {{ $notification->{'message_' . $locale} }}
                        </div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:4px">
                            {{ $notification->created_at->diffForHumans() }}
                            @if ($notification->read_at)
                                · {{ __('notifications.read_at') ?? 'Read' }} {{ $notification->read_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                    @if (!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}" style="flex-shrink:0">
                            @csrf
                            <button type="submit" class="btn btn-sm p-1" style="color:var(--accent); background:none; border:none; font-size:18px; line-height:1" title="{{ __('notifications.mark_read') ?? 'Mark as read' }}">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="py-4">
                    <x-empty-state
                        icon="bi bi-bell-slash"
                        :title="__('general.no_notifications')" />
                </div>
            @endforelse
        </div>
    </div>

    @if ($notifications->hasPages())
        <div class="mt-3">
            {{ $notifications->appends(request()->except('page'))->links() }}
        </div>
    @endif
</x-app-layout>
