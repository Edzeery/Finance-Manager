<x-super-admin-layout>
    <x-slot:title>{{ __('super-admin.backups') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('super-admin.backups') }}</x-slot>
    <x-slot:page-description>{{ __('super-admin.backups_desc') }}</x-slot>

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left"></div>
            <div class="data-grid-toolbar-right">
                <form method="POST" action="{{ route('super.admin.backups.create') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                        <i class="bi bi-cloud-arrow-up"></i>{{ __('super-admin.create_backup') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="data-grid-body">
            @if(count($backups))
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('general.file') }}</th>
                            <th>{{ __('general.size') }}</th>
                            <th>{{ __('general.date') }}</th>
                            <th class="col-actions">{{ __('general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                            <tr>
                                <td>
                                    <i class="bi bi-file-earmark-zip me-2" style="color:var(--text-muted)"></i>
                                    {{ $backup['name'] }}
                                </td>
                                <td>
                                    @php
                                        $size = $backup['size'];
                                        if ($size >= 1073741824) {
                                            $formatted = number_format($size / 1073741824, 2) . ' GB';
                                        } elseif ($size >= 1048576) {
                                            $formatted = number_format($size / 1048576, 2) . ' MB';
                                        } else {
                                            $formatted = number_format($size / 1024, 1) . ' KB';
                                        }
                                    @endphp
                                    {{ $formatted }}
                                </td>
                                <td class="cell-muted">{{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->format('Y-m-d H:i') }}</td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <a href="{{ route('super.admin.backups.download', [$backup['directory'], $backup['name']]) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.download') }}">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form id="restore-backup-{{ $loop->index }}" method="POST" action="{{ route('super.admin.backups.restore', [$backup['directory'], $backup['name']]) }}" style="display:none">
                                            @csrf
                                        </form>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--warning);font-size:13px;transition:all 0.15s" title="{{ __('general.restore') }}" @click="confirmRestoreBackup({{ $loop->index }})">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <form id="delete-backup-{{ $loop->index }}" method="POST" action="{{ route('super.admin.backups.destroy', [$backup['directory'], $backup['name']]) }}" style="display:none">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" @click="confirmDeleteBackup({{ $loop->index }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-archive"></i></div>
                    <h4>{{ __('general.no_data') }}</h4>
                    <p>{{ __('super-admin.no_backups') }}</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function confirmRestoreBackup(index) {
        const form = document.getElementById('restore-backup-' + index);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_restore_backup') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.restore') }}',
            'btn-warning'
        );
    }
    function confirmDeleteBackup(index) {
        const form = document.getElementById('delete-backup-' + index);
        if (!form) return;
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('super-admin.confirm_delete_backup') }}',
            (confirmed) => { if (confirmed) form.submit(); },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }
    </script>
    @endpush
</x-super-admin-layout>