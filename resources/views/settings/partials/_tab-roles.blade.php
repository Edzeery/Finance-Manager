@if($roles->count())
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
                            <x-status-badge domain="general" status="info" set="bi" size="xs" class="ms-1" />
                        @endif
                    </div>
                    <x-button href="{{ route('settings.workspace.roles.show', $role) }}" block icon="bi bi-eye" style="margin-top:16px">{{ __('general.view') }}</x-button>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="settings-card">
        <div class="text-center py-4">
            <x-empty-state icon="bi bi-shield-check" :title="__('workspace.no_roles')" />
        </div>
    </div>
@endif
