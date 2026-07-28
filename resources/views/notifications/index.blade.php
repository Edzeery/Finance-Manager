<x-app-layout>
    <x-slot:title>{{ __('notifications.page_title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('notifications.page_title') }}</x-slot>
    <x-slot:page-description>{{ __('notifications.page_description') }}</x-slot>

    @php
        $locale = app()->getLocale();
        $notifIconMap = [
            'budget_exceeded' => ['bg' => 'rgba(239,68,68,0.1)', 'color' => 'var(--danger)', 'icon' => 'bi-exclamation-triangle'],
            'budget_nearing_limit' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => 'var(--warning)', 'icon' => 'bi-exclamation-circle'],
            'debt_reminder' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => 'var(--warning)', 'icon' => 'bi-credit-card-2-front'],
            'goal_achieved' => ['bg' => 'rgba(34,197,94,0.1)', 'color' => 'var(--success)', 'icon' => 'bi-flag-fill'],
            'goal_milestone' => ['bg' => 'rgba(34,197,94,0.1)', 'color' => 'var(--success)', 'icon' => 'bi-flag'],
            'goal_deadline' => ['bg' => 'rgba(59,130,246,0.1)', 'color' => 'var(--info)', 'icon' => 'bi-clock-history'],
            'zakat_reminder' => ['bg' => 'rgba(139,92,246,0.1)', 'color' => 'var(--sa-indigo)', 'icon' => 'bi-heart-fill'],
            'zakat_approaching' => ['bg' => 'rgba(99,102,241,0.1)', 'color' => '#6366F1', 'icon' => 'bi-hourglass-split'],
            'login_new_device' => ['bg' => 'rgba(59,130,246,0.1)', 'color' => 'var(--info)', 'icon' => 'bi-phone'],
            'login_suspicious' => ['bg' => 'rgba(239,68,68,0.1)', 'color' => 'var(--danger)', 'icon' => 'bi-shield-exclamation'],
            'password_changed' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => 'var(--warning)', 'icon' => 'bi-key'],
            'two_factor_enabled' => ['bg' => 'rgba(34,197,94,0.1)', 'color' => 'var(--success)', 'icon' => 'bi-shield-lock'],
            'two_factor_disabled' => ['bg' => 'rgba(239,68,68,0.1)', 'color' => 'var(--danger)', 'icon' => 'bi-shield-x'],
            'session_revoked' => ['bg' => 'rgba(249,115,22,0.1)', 'color' => '#F97316', 'icon' => 'bi-box-arrow-right'],
            'email_changed' => ['bg' => 'rgba(59,130,246,0.1)', 'color' => 'var(--info)', 'icon' => 'bi-envelope-at'],
            'workspace_member_login' => ['bg' => 'rgba(34,197,94,0.1)', 'color' => 'var(--success)', 'icon' => 'bi-person-check'],
            'role_changed' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => 'var(--warning)', 'icon' => 'bi-shield-check'],
        ];

        $today = $notifications->filter(fn($n) => $n->created_at->isToday());
        $yesterday = $notifications->filter(fn($n) => $n->created_at->isYesterday());
        $older = $notifications->filter(fn($n) => !$n->created_at->isToday() && !$n->created_at->isYesterday());
    @endphp

    <x-filter-tabs :tabs="[
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-bell'],
        'unread' => ['label' => __('general.unread'), 'count' => $countUnread, 'icon' => 'bi-bell-fill'],
        'read' => ['label' => __('general.read'), 'count' => $countRead, 'icon' => 'bi-check-circle'],
    ]" current="{{ $status ?? 'all' }}" keyParam="status" defaultKey="all"
        :preserve="['per_page']" />

    {{-- Header actions --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div></div>
        <div class="d-flex align-items-center gap-2">
            @if($countUnread > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <x-button submit size="sm" variant="outline-accent" icon="bi bi-check-all">{{ __('notifications.mark_all_read') }}</x-button>
                </form>
            @endif
            <x-button href="{{ route('notifications.settings') }}" size="sm" variant="outline" icon="bi bi-sliders2">{{ __('notifications.preferences') }}</x-button>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-body p-0">
            @forelse(['today' => $today, 'yesterday' => $yesterday, 'older' => $older] as $group => $items)
                @if($items->isEmpty()) @continue @endif

                <div class="px-4 pt-3 pb-1" style="font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;background:var(--bg-subtle)">
                    @if($group === 'today')
                        {{ __('general.today') }}
                    @elseif($group === 'yesterday')
                        {{ __('general.yesterday') }}
                    @else
                        {{ __('general.older') }}
                    @endif
                </div>

                @foreach($items as $notification)
                    @php
                        $icon = $notifIconMap[$notification->type] ?? ['bg' => 'rgba(59,130,246,0.1)', 'color' => 'var(--info)', 'icon' => 'bi-info-circle'];
                    @endphp
                    <div class="d-flex align-items-start gap-3 px-4 py-3 {{ $notification->is_read ? '' : 'notif-item--unread' }}"
                         style="border-bottom:1px solid var(--border); transition:background 0.2s">
                        <div style="flex-shrink:0;width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:{{ $icon['bg'] }};color:{{ $icon['color'] }}">
                            <i class="bi {{ $icon['icon'] }}"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:14px;font-weight:500;color:var(--text)">{{ $notification->{'title_' . $locale} }}</span>
                                @if(!$notification->is_read)
                                    <span class="notif-unread-dot"></span>
                                @endif
                            </div>
                            <div style="font-size:13px;color:var(--text-muted);margin-top:2px">{{ $notification->{'message_' . $locale} }}</div>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">
                                <i class="bi bi-clock" style="font-size:10px"></i>
                                {{ $notification->created_at->diffForHumans() }}
                                @if($notification->read_at)
                                    · {{ __('notifications.read_at') }} {{ $notification->read_at->diffForHumans() }}
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1" style="flex-shrink:0">
                            @if(!$notification->is_read)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm p-1" style="color:var(--accent);background:none;border:none;font-size:18px;line-height:1" title="{{ __('notifications.mark_read') }}">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="btn btn-sm p-1" style="color:var(--text-muted);background:none;border:none;font-size:14px;line-height:1"
                                    title="{{ __('general.delete') }}"
                                    onclick="if(confirm('{{ __('notifications.delete_confirm') }}')){fetch('{{ route('notifications.destroy', $notification) }}',{method:'DELETE',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}}).then(()=>this.closest('.d-flex').remove())}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="py-5 text-center">
                    <div style="width:56px;height:56px;border-radius:50%;background:var(--bg-subtle);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
                        <i class="bi bi-bell-slash" style="font-size:24px;color:var(--text-muted);opacity:0.5"></i>
                    </div>
                    <p style="font-size:14px;font-weight:500;color:var(--text-muted);margin:0">{{ __('general.no_notifications') }}</p>
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
