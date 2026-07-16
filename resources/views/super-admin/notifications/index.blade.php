<x-super-admin-layout>
    <x-slot:title>{{ __('admin.notifications') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('admin.notifications') }}</x-slot>
    <x-slot:page-description>{{ __('admin.notifications_description', ['count' => $notifications->total()]) }}</x-slot>

    {{-- Filter Tabs --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex gap-1">
            <a href="{{ route('super.admin.notifications.index', ['filter' => 'all', 'type' => request('type')]) }}"
               class="btn btn-sm rounded-pill {{ $filter === 'all' ? 'btn-accent' : 'btn-outline-secondary' }}">
                {{ __('general.all') }}
            </a>
            <a href="{{ route('super.admin.notifications.index', ['filter' => 'unread', 'type' => request('type')]) }}"
               class="btn btn-sm rounded-pill {{ $filter === 'unread' ? 'btn-accent' : 'btn-outline-secondary' }}">
                {{ __('admin.new') }}
            </a>
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
                   class="btn btn-sm rounded-pill {{ ($type ?? '') === $typeKey ? 'btn-accent' : 'btn-outline-secondary' }}">
                    {{ $typeLabel }}
                </a>
            @endforeach
        </div>

        <div class="d-flex gap-2">
            @php $unreadCount = $notifications->where('is_read', false)->count(); @endphp
            @if ($unreadCount > 0)
                <form action="{{ route('super.admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-check-all me-1"></i>{{ __('admin.mark_all_read') }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="card">
        <div class="card-body p-0">
            @forelse ($notifications as $notification)
                @php
                    $notificationStatus = status('notification', $notification->type);
                    $title = locale_name($notification, 'title');
                    $message = locale_name($notification, 'message');
                @endphp
                <div class="notification-item p-3 border-bottom notification-transition {{ $notification->is_read ? '' : 'bg-light' }}" data-id="{{ $notification->id }}">
                    <div class="d-flex align-items-start gap-3">
                        <div class="notification-icon flex-shrink-0">
                            <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center {{ $notificationStatus->color() }}" style="width:36px;height:36px;font-size:14px">
                                {!! $notificationStatus->icon('bi') !!}
                            </span>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 {{ $notification->is_read ? '' : 'fw-bold' }}">
                                        <a href="{{ route('super.admin.notifications.show', $notification) }}" class="text-decoration-none text-reset stretched-link">
                                            {{ $title ?: $notification->title_en }}
                                        </a>
                                        @if (!$notification->is_read)
                                            <span class="badge bg-primary ms-1" style="font-size:10px">{{ __('admin.new') }}</span>
                                        @endif
                                    </h6>
                                    <p class="mb-0 text-muted small text-truncate" style="max-width:600px">
                                        {{ $message ?: $notification->message_en }}
                                    </p>
                                </div>
                                <small class="text-muted text-nowrap ms-3 flex-shrink-0">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="d-flex gap-2 mt-2 position-relative" style="z-index:2">
                                @if (!$notification->is_read)
                                    <form action="{{ route('super.admin.notifications.read', $notification) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size:12px">
                                            <i class="bi bi-check me-1"></i>{{ __('admin.mark_read') }}
                                        </button>
                                    </form>
                                @endif
                                @if ($notification->data)
                                    <span class="text-muted small" style="font-size:12px">
                                        <i class="bi bi-info-circle me-1"></i>
                                        @switch($notification->type)
                                            @case('new_user')
                                                {{ $notification->data['email'] ?? '' }}
                                                @break
                                            @case('new_payment')
                                                {{ isset($notification->data['amount']) ? currency_format($notification->data['amount'], $notification->data['currency'] ?? null) : '' }}
                                                @break
                                            @case('subscription_activated')
                                                {{ $notification->data['plan'] ?? '' }}
                                                @break
                                            @case('backup_completed')
                                                {{ $notification->data['filename'] ?? '' }}
                                                @break
                                        @endswitch
                                    </span>
                                @endif
                                <form id="delete-notification-{{ $notification->id }}" action="{{ route('super.admin.notifications.destroy', $notification) }}" method="POST" class="d-inline ms-auto" style="display:none">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-danger"
                                    style="font-size:12px"
                                    @click="showConfirmModal(
                                        '{{ __('general.confirm') }}',
                                        '{{ __('admin.confirm_delete_notification') }}',
                                        function(c) { if(c) { document.getElementById('delete-notification-{{ $notification->id }}').submit(); } },
                                        '{{ __('general.delete') }}',
                                        'btn-danger'
                                    )">
                                    <x-status-badge domain="notification" status="delete" set="bi" class="text-lg" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash display-4 text-muted"></i>
                    <p class="mt-3 text-muted">{{ __('admin.no_notifications') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">
        {{ $notifications->withQueryString()->links() }}
    </div>
</x-super-admin-layout>
