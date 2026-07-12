<x-super-admin-layout>
    <x-slot:title>{{ __('admin.notifications') }}</x-slot:title>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-muted mb-0">
                {{ __('admin.notifications_description', ['count' => $notifications->total()]) }}
            </p>
        </div>
        @if ($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('super.admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-check-all me-1"></i>{{ __('admin.mark_all_read') }}
                </button>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="card-body p-0">
            @forelse ($notifications as $notification)
                <div class="notification-item p-3 border-bottom {{ $notification->is_read ? '' : 'bg-light' }}" data-id="{{ $notification->id }}">
                    <div class="d-flex align-items-start gap-3">
                        <div class="notification-icon flex-shrink-0">
                            @php
                                $iconMap = [
                                    'new_user' => 'bi-person-plus',
                                    'new_payment' => 'bi-cash-stack',
                                    'subscription_activated' => 'bi-stars',
                                    'backup_completed' => 'bi-cloud-check',
                                    'system_alert' => 'bi-exclamation-triangle',
                                ];
                                $icon = $iconMap[$notification->type] ?? 'bi-bell';
                                $colorMap = [
                                    'new_user' => 'primary',
                                    'new_payment' => 'success',
                                    'subscription_activated' => 'info',
                                    'backup_completed' => 'secondary',
                                    'system_alert' => 'warning',
                                ];
                                $color = $colorMap[$notification->type] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} rounded-circle p-2">
                                <i class="bi {{ $icon }}"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="mb-1 {{ $notification->is_read ? '' : 'fw-bold' }}">
                                    {{ $notification->title_en }}
                                    @if (!$notification->is_read)
                                        <span class="badge bg-primary ms-1">{{ __('admin.new') }}</span>
                                    @endif
                                </h6>
                                <small class="text-muted text-nowrap ms-2">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-muted small">{{ $notification->message_en }}</p>
                            <div class="d-flex gap-2">
                                @if (!$notification->is_read)
                                    <form action="{{ route('super.admin.notifications.read', $notification) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none">
                                            <i class="bi bi-check me-1"></i>{{ __('admin.mark_read') }}
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('super.admin.notifications.destroy', $notification) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none text-danger"
                                        onclick="return confirm('{{ __('admin.confirm_delete_notification') }}')">
                                        <i class="bi bi-trash me-1"></i>{{ __('admin.delete') }}
                                    </button>
                                </form>
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
        {{ $notifications->links() }}
    </div>
</x-super-admin-layout>
