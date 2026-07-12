<x-app-layout>
    <x-slot:title>{{ __('workspace.roles') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('workspace.roles') }}</x-slot>
    <x-slot:page-description>{{ __('workspace.roles_desc') }}</x-slot>

    <div class="row g-3 stagger-fade-in">
        @foreach($roles as $role)
            <div class="col-md-6 col-lg-4">
                <div class="role-card">
                    <div class="role-card-header">
                        <div>
                            <h3 class="role-card-name">{{ __("super-admin.role_name_{$role->slug}") }}</h3>
                            <span class="role-card-slug">{{ __('general.'.$role->slug) }} </span>
                        </div>
                        <div style="text-align:end">
                            <div class="role-card-users">{{ $role->users_count }}</div>
                            <div class="role-card-users-label">{{ __('general.users') }}</div>
                        </div>
                    </div>
                    <p class="role-card-desc">{{ $role->description }}</p>
                    <div class="role-card-perms">
                        @forelse($role->permissions->take(5) as $perm)
                            <span class="role-card-perm-badge">{{ __("super-admin.perm_" . str_replace('.', '_', $perm->slug)) }}</span>
                        @empty
                            <span style="font-size:12px;color:var(--text-muted)">—</span>
                        @endforelse
                        @if($role->permissions->count() > 5)
                            <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:2px 8px;border-radius:4px">+{{ $role->permissions->count() - 5 }}</span>
                        @endif
                    </div>
                    <a href="{{ route('settings.workspace.roles.show', $role) }}" class="btn" style="width:100%;padding:9px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer;margin-top:16px">
                        <i class="bi bi-eye"></i>{{ __('general.view') }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
