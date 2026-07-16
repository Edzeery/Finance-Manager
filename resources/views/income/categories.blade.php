<x-app-layout>
    <x-slot:title>{{ __('income.categories') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('income.categories') }}</x-slot>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-body p-4">
                    <h5 class="mb-3" style="font-size:15px; font-weight:600">{{ __('general.add') }} {{ __('income.category') }}</h5>
                    <form action="{{ route('income.categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ __('general.name') }} <span class="badge-custom badge-status" style="background:rgba(255,193,7,0.12); color:var(--accent); font-size:10px">AR</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" class="form-custom @error('name_ar') is-invalid @enderror" value="{{ old('name_ar') }}" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ __('general.name') }} <span class="badge-custom badge-status" style="background:rgba(59,130,246,0.12); color:var(--info); font-size:10px">FR</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_fr" class="form-custom @error('name_fr') is-invalid @enderror" value="{{ old('name_fr') }}" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ __('general.name') }} <span class="badge-custom badge-status" style="background:rgba(34,197,94,0.12); color:var(--success); font-size:10px">EN</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" class="form-custom @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}" required maxlength="255">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label-custom">{{ __('general.icon') }}</label>
                                <input type="text" name="icon" class="form-custom" value="{{ old('icon', 'bi-currency-dollar') }}" maxlength="50" placeholder="bi-currency-dollar">
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">{{ __('general.color') }}</label>
                                <input type="color" name="color" class="form-custom" value="{{ old('color', '#22C55E') }}" style="height:42px; padding:4px">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">{{ __('income.type') }}</label>
                            <select name="type" class="form-custom">
                                <option value="variable" {{ old('type') === 'variable' ? 'selected' : '' }}>{{ __('income.variable') }}</option>
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>{{ __('income.fixed') }}</option>
                                <option value="recurring" {{ old('type') === 'recurring' ? 'selected' : '' }}>{{ __('income.recurring') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <x-toggle-switch name="is_active" :checked="old('is_active', '1')" label="{{ __('general.active') }}" />
                        </div>
                        <button type="submit" class="btn btn-accent btn-custom w-100">
                            <i class="bi bi-plus-lg me-1"></i>{{ __('general.add') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-0">
                    @if($categories->count())
                        <div class="table-responsive">
                            <table class="table-custom">
                            <thead>
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>{{ __('general.icon') }}</th>
                                    <th>{{ __('general.name') }}</th>
                                    <th>{{ __('income.type') }}</th>
                                    <th>{{ __('general.status') }}</th>
                                    <th class="text-center" style="width:100px">{{ __('general.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $cat)
                                    <tr>
                                        <td style="color:var(--text-muted)">{{ $loop->iteration }}</td>
                                        <td>
                                            <span style="display:inline-flex; align-items:center; gap:6px">
                                                <i class="{{ $cat->icon }}" style="color:{{ $cat->color }}"></i>
                                                <span style="width:16px; height:16px; border-radius:4px; background:{{ $cat->color }}; display:inline-block"></span>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-weight:500">{{ locale_name($cat) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-custom badge-income">{{ __("income.{$cat->type}") }}</span>
                                        </td>
                                        <td>
                                            <x-status-badge domain="general" :status="$cat->is_active ? 'active' : 'inactive'" set="bi" />
                                        </td>
                                        <td class="text-center">
                                            <div class="action-group justify-content-center">
                                                <button class="action-btn" @click="editCategory({{ $cat->id }})" title="{{ __('general.edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="action-btn" title="{{ __('general.delete') }}" @click="confirmDeleteCategory({{ $cat->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    @else
                        @include('components.empty-state', [
                            'icon' => 'bi-tag',
                            'title' => __('income.no_categories'),
                            'message' => __('income.create_first_category'),
                        ])
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-custom">
                <form id="editForm" method="POST" data-categories='@json($categories->items())'>
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('income.edit_category') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ __('general.name') }} <span class="badge-custom badge-status" style="background:rgba(255,193,7,0.12); color:var(--accent); font-size:10px">AR</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" id="edit_name_ar" class="form-custom" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ __('general.name') }} <span class="badge-custom badge-status" style="background:rgba(59,130,246,0.12); color:var(--info); font-size:10px">FR</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_fr" id="edit_name_fr" class="form-custom" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ __('general.name') }} <span class="badge-custom badge-status" style="background:rgba(34,197,94,0.12); color:var(--success); font-size:10px">EN</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" id="edit_name_en" class="form-custom" required maxlength="255">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label-custom">{{ __('general.icon') }}</label>
                                <input type="text" name="icon" id="edit_icon" class="form-custom" maxlength="50">
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">{{ __('general.color') }}</label>
                                <input type="color" name="color" id="edit_color" class="form-custom" style="height:42px; padding:4px">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">{{ __('income.type') }}</label>
                            <select name="type" id="edit_type" class="form-custom">
                                <option value="variable">{{ __('income.variable') }}</option>
                                <option value="fixed">{{ __('income.fixed') }}</option>
                                <option value="recurring">{{ __('income.recurring') }}</option>
                            </select>
                        </div>
                        <div>
                            <x-toggle-switch name="is_active" id="edit_is_active" description="{{ __('general.active') }}" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i>{{ __('general.cancel') }}</button>
                        <button type="submit" class="btn btn-accent btn-custom">{{ __('general.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Hidden delete forms --}}
    @foreach($categories as $cat)
        <form id="delete-form-category-{{ $cat->id }}" action="{{ route('income.categories.destroy', $cat) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endforeach

    @push('scripts')
    <script>
    function confirmDeleteCategory(id) {
        showConfirmModal(
            '{{ __('general.confirm') }}',
            '{{ __('messages.confirm_delete') }}',
            (confirmed) => {
                if (confirmed) {
                    document.getElementById('delete-form-category-' + id)?.submit();
                }
            },
            '{{ __('general.delete') }}',
            'btn-danger'
        );
    }

    function editCategory(id) {
        var el = document.getElementById('editForm');
        var cats = el ? JSON.parse(el.dataset.categories) : [];
        var cat = cats.find(function(c) { return c.id == id; });
        if (!cat) return;
        document.getElementById('edit_name_ar').value = cat.name_ar;
        document.getElementById('edit_name_fr').value = cat.name_fr;
        document.getElementById('edit_name_en').value = cat.name_en;
        document.getElementById('edit_icon').value = cat.icon || 'bi-currency-dollar';
        document.getElementById('edit_color').value = cat.color || '#22C55E';
        document.getElementById('edit_type').value = cat.type;
        setToggle('edit_is_active', Boolean(cat.is_active));
        document.getElementById('editForm').action = '{{ route('income.categories.update', ':id') }}'.replace(':id', id);
        const modalEl = document.getElementById('editModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }
    </script>
    @endpush
</x-app-layout>
