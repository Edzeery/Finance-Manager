{{-- Workspace Info --}}
<div class="settings-card mb-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
            <i class="bi bi-building" style="color:var(--accent);font-size:16px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('settings.workspace_info') }}</h5>
            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('settings.workspace_desc') }}</p>
        </div>
    </div>
    @can('update', $workspace)
    <form action="{{ route('settings.workspace.update') }}" method="POST">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label-custom">{{ __('settings.workspace_name') }}</label>
                <input type="text" name="name" class="form-custom" value="{{ $workspace->name ?? '' }}" required maxlength="100">
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">{{ __('settings.workspace_type') }}</label>
                <input type="text" class="form-custom" value="{{ ucfirst($workspace->type ?? 'personal') }}" disabled style="opacity:0.7">
            </div>
        </div>
        <x-button submit>{{ __('settings.save') }}</x-button>
    </form>
    @else
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label-custom">{{ __('settings.workspace_name') }}</label>
            <input type="text" class="form-custom" value="{{ $workspace->name ?? '' }}" disabled style="opacity:0.7">
        </div>
        <div class="col-md-6">
            <label class="form-label-custom">{{ __('settings.workspace_type') }}</label>
            <input type="text" class="form-custom" value="{{ ucfirst($workspace->type ?? 'personal') }}" disabled style="opacity:0.7">
        </div>
    </div>
    @endcan
</div>

{{-- Owner Info --}}
<div class="settings-card">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
            <i class="bi bi-person-gear" style="color:var(--accent);font-size:16px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('settings.owner_info') }}</h5>
            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('settings.owner_info_desc') }}</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div style="width:40px;height:40px;border-radius:10px;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex-shrink:0;">
            {{ strtoupper(substr($workspaceOwner?->name ?? '-', 0, 1)) }}
        </div>
        <div>
            <div style="font-weight:600;font-size:14px;">{{ $workspaceOwner?->name }}</div>
            <div style="font-size:12px;color:var(--text-muted);">{{ $workspaceOwner?->email }}</div>
        </div>
        <x-status-badge domain="general" status="paid" set="bi" size="xs" class="ms-auto" />
    </div>
</div>
