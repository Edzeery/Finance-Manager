<x-super-admin-layout>
    <x-slot:title>{{ $feature ? __('super-admin.edit_feature') : __('super-admin.create_feature') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $feature ? __('super-admin.edit_feature') : __('super-admin.create_feature') }}</x-slot>
    <x-slot:page-description>{{ $feature ? __('super-admin.edit_feature_desc') : __('super-admin.create_feature_desc') }}</x-slot>

    <div style="max-width:720px">
        <form method="POST" action="{{ $feature ? route('super.admin.features.update', $feature) : route('super.admin.features.store') }}">
            @csrf @if($feature) @method('PUT') @endif

            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-list-check"></i>{{ __('super-admin.feature_information') }}</h5>
                </div>
                <div class="section-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="{{ __('super-admin.feature_slug') }}" value="{{ old('slug', $feature->slug ?? '') }}" maxlength="100" required>
                                <label>{{ __('super-admin.feature_slug') }} <span class="text-danger">*</span></label>
                                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="boolean" {{ old('type', $feature->type ?? '') === 'boolean' ? 'selected' : '' }}>Boolean</option>
                                    <option value="value" {{ old('type', $feature->type ?? '') === 'value' ? 'selected' : '' }}>Value</option>
                                    <option value="text" {{ old('type', $feature->type ?? '') === 'text' ? 'selected' : '' }}>Text</option>
                                </select>
                                <label>{{ __('super-admin.feature_type') }} <span class="text-danger">*</span></label>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-floating-group">
                        <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="{{ __('super-admin.feature_name_en') }}" value="{{ old('name_en', $feature->name_en ?? '') }}" maxlength="255" required>
                        <label>{{ __('super-admin.feature_name_en') }} <span class="text-danger">*</span></label>
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" placeholder="{{ __('super-admin.feature_name_ar') }}" value="{{ old('name_ar', $feature->name_ar ?? '') }}" maxlength="255">
                                <label>{{ __('super-admin.feature_name_ar') }}</label>
                                @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="text" name="name_fr" class="form-control @error('name_fr') is-invalid @enderror" placeholder="{{ __('super-admin.feature_name_fr') }}" value="{{ old('name_fr', $feature->name_fr ?? '') }}" maxlength="255">
                                <label>{{ __('super-admin.feature_name_fr') }}</label>
                                @error('name_fr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" placeholder="{{ __('super-admin.feature_icon') }}" value="{{ old('icon', $feature->icon ?? '') }}" maxlength="100">
                                <label>{{ __('super-admin.feature_icon') }}</label>
                                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="{{ __('super-admin.feature_order') }}" value="{{ old('sort_order', $feature->sort_order ?? '') }}" min="0">
                                <label>{{ __('super-admin.feature_order') }}</label>
                                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                            <x-toggle-switch name="is_core" :checked="$feature->is_core ?? false" />
                            <span style="font-size:13px;font-weight:500;color:var(--text)">{{ __('super-admin.core_feature') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:var(--primary);font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                    {{ $feature ? __('general.update') : __('general.create') }}
                </button>
                <a href="{{ route('super.admin.features.index') }}" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                    {{ __('general.cancel') }}
                </a>
            </div>
        </form>
    </div>
</x-super-admin-layout>
