<x-app-layout>
    <x-slot:title>{{ __("super-admin.role_name_{$role->slug}") }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __("super-admin.role_name_{$role->slug}") }}</x-slot>
    <x-slot:page-description>{{ $role->description }}</x-slot>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-info-circle"></i>{{ __('workspace.role_details') }}</h5>
                </div>
                <div class="section-card-body">
                    <div class="mb-3">
                        <span style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px">{{ __('general.name') }}</span>
                        <span style="font-size:14px;font-weight:500">{{ __("super-admin.role_name_{$role->slug}") }}</span>
                    </div>
                    <div class="mb-3">
                        <span style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px">{{ __('super-admin.role_slug') }}</span>
                        <code style="font-size:13px;background:var(--bg-subtle);padding:4px 10px;border-radius:6px;display:inline-block">{{ $role->slug }}</code>
                    </div>
                    <div class="mb-3">
                        <span style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px">{{ __('general.description') }}</span>
                        <span style="font-size:13px;color:var(--text)">{{ $role->description }}</span>
                    </div>
                    <div class="mb-3">
                        <span style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px">{{ __('general.users') }}</span>
                        <span style="font-size:14px;font-weight:500">{{ $role->users_count }}</span>
                    </div>
                    <a href="{{ route('settings.workspace.index', ['tab' => 'roles']) }}" class="btn" style="width:100%;padding:9px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer">
                        <i class="bi bi-arrow-left"></i>{{ __('general.back') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-shield-check"></i>{{ __('super-admin.permissions') }}
                        <span class="badge" style="font-size:10px;background:var(--bg-subtle);color:var(--text-muted);padding:2px 10px;border-radius:6px;font-weight:500">{{ $permissions->flatten()->count() }} {{ __('super-admin.total') }}</span>
                    </h5>
                </div>
                <div class="section-card-body">
                    @foreach($permissions as $group => $groupPerms)
                        <div class="mb-4">
                            <h6 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:capitalize;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px">
                                <i class="bi bi-folder2-open" style="font-size:14px"></i>
                                {{ __("super-admin.permission_group_{$group}") }}
                                <span class="badge" style="font-size:10px;background:var(--bg-subtle);color:var(--text-muted);padding:1px 8px;border-radius:4px;font-weight:500">{{ count($groupPerms) }}</span>
                            </h6>
                            <div class="permission-grid">
                                @foreach($groupPerms as $perm)
                                    <label class="permission-card {{ in_array($perm->id, $rolePermissions) ? 'has' : '' }}" style="cursor:default">
                                        <input type="checkbox" {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }} disabled class="perm-check">
                                        <div>
                                            <span class="perm-label">{{ __("super-admin.perm_" . str_replace('.', '_', $perm->slug)) }}</span>
                                            <span class="perm-desc">{{ $perm->description }}</span>
                                        </div>
                                        <span class="perm-indicator {{ in_array($perm->id, $rolePermissions) ? 'granted' : 'denied' }}"></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
