<x-super-admin-layout>
    <x-slot:title>{{ __('notifications.page_title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('notifications.page_title') }}</x-slot>
    <x-slot:page-description>{{ __('admin.notifications_description', ['count' => $notifications->total()]) }}</x-slot>

    @php
        $notifIconMap = [
            'new_user'               => ['bg' => 'rgba(59,130,246,0.1)',  'color' => 'var(--info)',     'icon' => 'bi-person-plus'],
            'new_payment'            => ['bg' => 'rgba(34,197,94,0.1)',  'color' => 'var(--success)',  'icon' => 'bi-cash-stack'],
            'subscription_activated' => ['bg' => 'rgba(99,102,241,0.1)', 'color' => '#6366F1',         'icon' => 'bi-stars'],
            'backup_completed'       => ['bg' => 'rgba(139,92,246,0.1)', 'color' => 'var(--sa-indigo)', 'icon' => 'bi-cloud-check'],
            'system_alert'           => ['bg' => 'rgba(239,68,68,0.1)',  'color' => 'var(--danger)',   'icon' => 'bi-exclamation-triangle'],
        ];

        $locale = app()->getLocale();
        $today = $notifications->getCollection()->filter(fn($n) => $n->created_at->isToday());
        $yesterday = $notifications->getCollection()->filter(fn($n) => $n->created_at->isYesterday());
        $older = $notifications->getCollection()->filter(fn($n) => !$n->created_at->isToday() && !$n->created_at->isYesterday());
    @endphp

    {{-- Filter Tabs --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex gap-1 flex-wrap">
            <a href="{{ route('super.admin.notifications.index', ['filter' => 'all', 'type' => request('type')]) }}"
               class="btn btn-sm rounded-pill {{ $filter === 'all' ? 'btn-accent' : 'btn-outline-secondary' }}">{{ __('general.all') }}</a>
            <a href="{{ route('super.admin.notifications.index', ['filter' => 'unread', 'type' => request('type')]) }}"
               class="btn btn-sm rounded-pill {{ $filter === 'unread' ? 'btn-accent' : 'btn-outline-secondary' }}">{{ __('general.unread') }}</a>
            <a href="{{ route('super.admin.notifications.index', ['filter' => 'read', 'type' => request('type')]) }}"
               class="btn btn-sm rounded-pill {{ $filter === 'read' ? 'btn-accent' : 'btn-outline-secondary' }}">{{ __('general.read') }}</a>
            @php
                $typeFilters = [
                    'new_user' => __('super-admin.users'),
                    'new_payment' => __('super-admin.payments'),
                    'subscription_activated' => __('super-admin.subscriptions'),
                    'backup_completed' => __('super-admin.backups'),
                    'system_alert' => __('super-admin.system'),
                ];
            @endphp
            @foreach ($typeFilters as $typeKey => $typeLabel)
                <a href="{{ route('super.admin.notifications.index', ['filter' => $filter, 'type' => $typeKey === ($type ?? '') ? null : $typeKey]) }}"
                   class="btn btn-sm rounded-pill {{ ($type ?? '') === $typeKey ? 'btn-accent' : 'btn-outline-secondary' }}">{{ $typeLabel }}</a>
            @endforeach
        </div>

        <div class="d-flex gap-2">
            @php $unreadCount = $notifications->where('is_read', false)->count(); @endphp
            @if ($unreadCount > 0)
                <form action="{{ route('super.admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-accent btn-custom"><i class="bi bi-check-all ms-1"></i>{{ __('notifications.mark_all_read') }}</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Notifications List --}}
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
                        $icon = $notifIconMap[$notification->type] ?? ['bg' => 'rgba(59,130,246,0.1)', 'color' => 'var(--info)', 'icon' => 'bi-bell'];
                        $title = $notification->{"title_{$locale}"} ?: $notification->title_en;
                        $message = $notification->{"message_{$locale}"} ?: $notification->message_en;
                    @endphp
                    <div class="d-flex align-items-start gap-3 px-4 py-3 {{ $notification->is_read ? '' : 'notif-item--unread' }}"
                         style="border-bottom:1px solid var(--border);transition:background 0.2s">
                        <div style="flex-shrink:0;width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:{{ $icon['bg'] }};color:{{ $icon['color'] }}">
                            <i class="bi {{ $icon['icon'] }}"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('super.admin.notifications.show', $notification) }}" style="font-size:14px;font-weight:500;color:var(--text);text-decoration:none">{{ $title }}</a>
                                @if(!$notification->is_read)
                                    <span class="notif-unread-dot"></span>
                                @endif
                            </div>
                            <div style="font-size:13px;color:var(--text-muted);margin-top:2px">{{ $message }}</div>
                            @if($notification->data)
                                <div style="font-size:12px;color:var(--text-muted);margin-top:4px">
                                    <i class="bi bi-info-circle" style="font-size:10px"></i>
                                    @switch($notification->type)
                                        @case('new_user')
                                            {{ $notification->data['user_email'] ?? '' }}
                                            @break
                                        @case('new_payment')
                                            {{ isset($notification->data['amount']) ? currency_format($notification->data['amount'], $notification->data['currency'] ?? null) : '' }}
                                            @break
                                        @case('subscription_activated')
                                            {{ $notification->data['plan_name'] ?? '' }}
                                            @break
                                        @case('backup_completed')
                                            {{ $notification->data['file_name'] ?? '' }}
                                            @break
                                    @endswitch
                                </div>
                            @endif
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
                                <form method="POST" action="{{ route('super.admin.notifications.read', $notification) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm p-1" style="color:var(--accent);background:none;border:none;font-size:18px;line-height:1" title="{{ __('notifications.mark_read') }}">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('super.admin.notifications.destroy', $notification) }}" style="display:inline" onsubmit="return confirm('{{ __('notifications.delete_confirm') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm p-1" style="color:var(--text-muted);background:none;border:none;font-size:14px;line-height:1" title="{{ __('general.delete') }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="py-5 text-center">
                    <div style="width:56px;height:56px;border-radius:50%;background:var(--bg-subtle);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
                        <i class="bi bi-bell-slash" style="font-size:24px;color:var(--text-muted);opacity:0.5"></i>
                    </div>
                    <p style="font-size:14px;font-weight:500;color:var(--text-muted);margin:0">{{ __('notifications.empty') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">
        {{ $notifications->withQueryString()->links() }}
    </div>
</x-super-admin-layout>
