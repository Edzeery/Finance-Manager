<x-super-admin-layout>
    <x-slot:title>{{ __('notifications.page_title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>
        <a href="{{ route('super.admin.notifications.index') }}" class="text-decoration-none text-reset">
            <i class="bi bi-arrow-left ms-1"></i>{{ __('notifications.page_title') }}
        </a>
    </x-slot:page-title>
    <x-slot:page-description>{{ locale_name($notification, 'title') ?: $notification->title_en }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body">
                    @php
                        $locale = app()->getLocale();
                        $notifIconMap = [
                            'new_user'               => ['bg' => 'rgba(59,130,246,0.1)',  'color' => 'var(--info)',     'icon' => 'bi-person-plus'],
                            'new_payment'            => ['bg' => 'rgba(34,197,94,0.1)',  'color' => 'var(--success)',  'icon' => 'bi-cash-stack'],
                            'subscription_activated' => ['bg' => 'rgba(99,102,241,0.1)', 'color' => '#6366F1',         'icon' => 'bi-stars'],
                            'backup_completed'       => ['bg' => 'rgba(139,92,246,0.1)', 'color' => 'var(--sa-indigo)', 'icon' => 'bi-cloud-check'],
                            'system_alert'           => ['bg' => 'rgba(239,68,68,0.1)',  'color' => 'var(--danger)',   'icon' => 'bi-exclamation-triangle'],
                        ];
                        $icon = $notifIconMap[$notification->type] ?? ['bg' => 'rgba(59,130,246,0.1)', 'color' => 'var(--info)', 'icon' => 'bi-bell'];
                        $title = $notification->{"title_{$locale}"} ?: $notification->title_en;
                        $message = $notification->{"message_{$locale}"} ?: $notification->message_en;
                    @endphp

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="flex-shrink:0;width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:{{ $icon['bg'] }};color:{{ $icon['color'] }}">
                            <i class="bi {{ $icon['icon'] }}" style="font-size:20px"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">{{ $title }}</h4>
                            <div class="d-flex gap-3 text-muted small">
                                <span><i class="bi bi-clock ms-1"></i>{{ $notification->created_at->format('Y/m/d H:i') }}</span>
                                <span><i class="bi bi-tag ms-1"></i>{{ $notification->type }}</span>
                                <span>
                                    @if ($notification->is_read)
                                        <i class="bi bi-check-circle text-success ms-1"></i>{{ __('notifications.read_at') }}
                                    @else
                                        <i class="bi bi-circle text-warning ms-1"></i>{{ __('general.unread') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                        <p class="mb-0" style="font-size:14px;line-height:1.7">{{ $message }}</p>
                    </div>

                    @if ($notification->data)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle ms-1"></i>{{ __('general.details') }}</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0" style="font-size:13px">
                                    <tbody>
                                        @foreach ($notification->data as $key => $value)
                                            <tr>
                                                <th style="width:140px;background:var(--bg-subtle)" class="text-muted fw-normal">{{ $key }}</th>
                                                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ route('super.admin.notifications.index') }}" class="btn btn-sm btn-outline-secondary btn-custom"><i class="bi bi-arrow-left ms-1"></i>{{ __('general.back') }}</a>
                        @if (!$notification->is_read)
                            <form action="{{ route('super.admin.notifications.read', $notification) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-accent btn-custom"><i class="bi bi-check2-circle ms-1"></i>{{ __('notifications.mark_read') }}</button>
                            </form>
                        @endif
                        <form action="{{ route('super.admin.notifications.destroy', $notification) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('notifications.delete_confirm') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger btn-custom"><i class="bi bi-trash3 ms-1"></i>{{ __('general.delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
