<x-super-admin-layout>
    <x-slot:title>{{ __("super-admin.role_name_{$role->slug}") }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __("super-admin.role_name_{$role->slug}") }}</x-slot>
    <x-slot:page-description>{{ $role->description }}</x-slot>

    <form action="{{ route('super.admin.roles.update', $role) }}" method="POST">
        @csrf @method('PUT')

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-info-circle"></i>{{ __('super-admin.role_details') }}</h5>
                    </div>
                    <div class="section-card-body">
                        <div class="form-floating-group">
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('general.name') }}" value="{{ old('name', $role->name) }}" required maxlength="255">
                            <label>{{ __('general.name') }} <span class="text-danger">*</span></label>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-floating-group">
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="{{ __('general.description') }}" rows="3" maxlength="500" style="height:auto;min-height:80px;padding-top:20px">{{ old('description', $role->description) }}</textarea>
                            <label>{{ __('general.description') }}</label>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <span style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px">{{ __('super-admin.role_slug') }}</span>
                            <code style="font-size:13px;background:var(--bg-subtle);padding:4px 10px;border-radius:6px;display:inline-block">{{ $role->slug }}</code>
                        </div>
                        <button type="submit" class="btn" style="width:100%;padding:9px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px">
                            <i class="bi bi-check-lg"></i>{{ __('general.save') }}
                        </button>
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
                        <div class="mb-3 d-flex flex-wrap gap-2">
                            <button type="button" class="btn" style="padding:5px 12px;font-size:11px;border-radius:var(--radius-xs);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer" @click="toggleAllPermissions(true)">{{ __('general.select_all') }}</button>
                            <button type="button" class="btn" style="padding:5px 12px;font-size:11px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer" @click="toggleAllPermissions(false)">{{ __('general.deselect_all') }}</button>
                        </div>

                        @foreach($permissions as $group => $groupPerms)
                            <div class="mb-4">
                                <h6 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:capitalize;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px">
                                    <i class="bi bi-folder2-open" style="font-size:14px"></i>
                                    {{ __("super-admin.permission_group_{$group}") }}
                                    <span class="badge" style="font-size:10px;background:var(--bg-subtle);color:var(--text-muted);padding:1px 8px;border-radius:4px;font-weight:500">{{ count($groupPerms) }}</span>
                                </h6>
                                <div class="permission-grid">
                                    @foreach($groupPerms as $perm)
                                        <label class="permission-card {{ in_array($perm->id, $rolePermissions) ? 'has' : '' }} {{ $role->slug === 'super_admin' ? 'disabled' : '' }}">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}
                                                {{ $role->slug === 'super_admin' ? 'disabled' : '' }}
                                                class="perm-check"
                                                @change="$el.closest('.permission-card').classList.toggle('has', $el.checked)">
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

                        @if($role->slug === 'super_admin')
                            <div class="d-flex align-items-center gap-2" style="font-size:13px;background:var(--info-light);color:var(--info);border-radius:var(--radius-sm);padding:10px 14px">
                                <i class="bi bi-info-circle"></i>
                                {{ __('super-admin.super_admin_permissions_locked') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
    function toggleAllPermissions(checked) {
        document.querySelectorAll('.perm-check:not(:disabled)').forEach(cb => {
            cb.checked = checked;
            cb.closest('.permission-card')?.classList.toggle('has', checked);
        });
    }
    </script>
    @endpush
</x-super-admin-layout>
