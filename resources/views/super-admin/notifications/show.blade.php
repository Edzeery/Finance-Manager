<x-super-admin-layout>
    <x-slot:title>{{ __('admin.notifications') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>
        <a href="{{ route('super.admin.notifications.index') }}" class="text-decoration-none text-reset">
            <i class="bi bi-arrow-leftms-1"></i>{{ __('admin.notifications') }}
        </a>
    </x-slot>
    <x-slot:page-description>{{ locale_name($notification, 'title') ?: $notification->title_en }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    @php
                        $notificationStatus = status('notification', $notification->type);
                        $title = locale_name($notification, 'title');
                        $message = locale_name($notification, 'message');
                    @endphp

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="badge rounded-circle p-3 d-inline-flex align-items-center justify-content-center {{ $notificationStatus->color() }}" style="width:48px;height:48px;font-size:20px">
                            {!! $notificationStatus->icon('bi') !!}
                        </span>
                        <div>
                            <h4 class="mb-1">{{ $title ?: $notification->title_en }}</h4>
                            <div class="d-flex gap-3 text-muted small">
                                <span><i class="bi bi-clockms-1"></i>{{ $notification->created_at->format('Y/m/d H:i') }}</span>
                                <span><i class="bi bi-tagms-1"></i>{{ $notification->type }}</span>
                                <span>
                                    @if ($notification->is_read)
                                        <i class="bi bi-check-circle text-successms-1"></i>{{ __('admin.mark_read') }}
                                    @else
                                        <i class="bi bi-circle text-warningms-1"></i>{{ __('admin.new') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border-light)">
                        <p class="mb-0" style="font-size:14px;line-height:1.7">{{ $message ?: $notification->message_en }}</p>
                    </div>

                    @if ($notification->data)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2"><i class="bi bi-info-circlems-1"></i>{{ __('general.details') }}</h6>
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
                        <a href="{{ route('super.admin.notifications.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-leftms-1"></i>{{ __('general.back') }}
                        </a>
                        @if (!$notification->is_read)
                            <form action="{{ route('super.admin.notifications.read', $notification) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-checkms-1"></i>{{ __('admin.mark_read') }}
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('super.admin.notifications.destroy', $notification) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('{{ __('admin.confirm_delete_notification') }}')">
                                <i class="bi bi-trashms-1"></i>{{ __('admin.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
