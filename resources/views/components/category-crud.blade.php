@props([
    'type' => 'expense',
    'categories',
    'storeRoute',
    'updateRoute',
    'destroyRoute',
    'types',
    'defaultColor' => '#EF4444',
    'defaultIcon' => 'bi-cart',
    'badgeClass' => 'badge-expense',
])

@php
    $categoryLabel = __('general.category');
    $addLabel = __('general.add');
    $editLabel = __('general.edit');
    $typeLabel = __('general.type');
    $iconLabel = __('general.icon');
    $colorLabel = __('general.color');
    $nameLabel = __('general.name');
    $statusLabel = __('general.status');
    $actionsLabel = __('general.actions');
    $activeLabel = __('general.active');
    $cancelLabel = __('general.cancel');
    $saveLabel = __('general.save');
    $confirmLabel = __('general.confirm');
    $deleteLabel = __('general.delete');
    $noDataLabel = __('general.no_data');
    $noResultsLabel = __('messages.no_results');
    $confirmDeleteMsg = __('messages.confirm_delete');
@endphp

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-body p-4">
                <h5 class="mb-3" style="font-size:15px; font-weight:600">{{ $addLabel }} {{ $categoryLabel }}</h5>
                <form action="{{ $storeRoute }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ $nameLabel }} <span class="badge-custom badge-status" style="background:rgba(255,193,7,0.12); color:var(--accent); font-size:10px">AR</span> <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-custom @error('name_ar') is-invalid @enderror" value="{{ old('name_ar') }}" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ $nameLabel }} <span class="badge-custom badge-status" style="background:rgba(59,130,246,0.12); color:var(--info); font-size:10px">FR</span> <span class="text-danger">*</span></label>
                        <input type="text" name="name_fr" class="form-custom @error('name_fr') is-invalid @enderror" value="{{ old('name_fr') }}" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ $nameLabel }} <span class="badge-custom badge-status" style="background:rgba(34,197,94,0.12); color:var(--success); font-size:10px">EN</span> <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-custom @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}" required maxlength="255">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <x-icon-picker name="icon" :value="old('icon', $defaultIcon)" />
                        </div>
                        <div class="col-6">
                            <label class="form-label-custom">{{ $colorLabel }}</label>
                            <input type="color" name="color" class="form-custom" value="{{ old('color', $defaultColor) }}" style="height:42px; padding:4px">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">{{ $typeLabel }}</label>
                        <select name="type" class="form-custom">
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <x-toggle-switch name="is_active" :checked="old('is_active', '1')" :label="$activeLabel" />
                    </div>
                    <x-button submit block icon="bi bi-plus-lg">{{ $addLabel }}</x-button>
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
                                <th>{{ $iconLabel }}</th>
                                <th>{{ $nameLabel }}</th>
                                <th>{{ $typeLabel }}</th>
                                <th>{{ $statusLabel }}</th>
                                <th class="text-center" style="width:100px">{{ $actionsLabel }}</th>
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
                                        <span class="badge badge-custom {{ $badgeClass }}">{{ $types[$cat->type] ?? $cat->type }}</span>
                                    </td>
                                    <td>
                                        <x-status-badge domain="general" :status="$cat->is_active ? 'active' : 'inactive'" set="bi" />
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group justify-content-center">
                                            <button class="action-btn" @click="editCategory({{ $cat->id }})" title="{{ $editLabel }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="action-btn" title="{{ $deleteLabel }}" @click="confirmDeleteCategory({{ $cat->id }})">
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
                        'title' => $noDataLabel,
                        'message' => $noResultsLabel,
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
            <form id="editForm" method="POST" data-categories='@json($categories->items())' data-update-route="{{ $updateRoute }}" data-default-icon="{{ $defaultIcon }}" data-default-color="{{ $defaultColor }}">
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editLabel }} {{ $categoryLabel }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ $nameLabel }} <span class="badge-custom badge-status" style="background:rgba(255,193,7,0.12); color:var(--accent); font-size:10px">AR</span> <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" id="edit_name_ar" class="form-custom" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ $nameLabel }} <span class="badge-custom badge-status" style="background:rgba(59,130,246,0.12); color:var(--info); font-size:10px">FR</span> <span class="text-danger">*</span></label>
                        <input type="text" name="name_fr" id="edit_name_fr" class="form-custom" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom mb-0 d-flex align-items-center gap-2">{{ $nameLabel }} <span class="badge-custom badge-status" style="background:rgba(34,197,94,0.12); color:var(--success); font-size:10px">EN</span> <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" id="edit_name_en" class="form-custom" required maxlength="255">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <x-icon-picker name="icon" id="edit_icon" value="" />
                        </div>
                        <div class="col-6">
                            <label class="form-label-custom">{{ $colorLabel }}</label>
                            <input type="color" name="color" id="edit_color" class="form-custom" style="height:42px; padding:4px">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">{{ $typeLabel }}</label>
                        <select name="type" id="edit_type" class="form-custom">
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-toggle-switch name="is_active" id="edit_is_active" :description="$activeLabel" />
                    </div>
                </div>
                <div class="modal-footer">
                    <x-button variant="outline" icon="bi bi-x-lg" data-bs-dismiss="modal">{{ $cancelLabel }}</x-button>
                    <x-button submit>{{ $saveLabel }}</x-button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden delete forms --}}
@foreach($categories as $cat)
    <form id="delete-form-category-{{ $cat->id }}" action="{{ route($destroyRoute, $cat) }}" method="POST" style="display:none">
        @csrf @method('DELETE')
    </form>
@endforeach

@push('scripts')
<script>
(function() {
    var form = document.getElementById('editForm');
    if (!form) return;
    var updateRoute = form.dataset.updateRoute;
    var defaultIcon = form.dataset.defaultIcon;
    var defaultColor = form.dataset.defaultColor;

    window.confirmDeleteCategory = function(id) {
        showConfirmModal(
            '{{ $confirmLabel }}',
            '{{ $confirmDeleteMsg }}',
            (confirmed) => {
                if (confirmed) {
                    document.getElementById('delete-form-category-' + id)?.submit();
                }
            },
            '{{ $deleteLabel }}',
            'btn-danger'
        );
    };

    window.editCategory = function(id) {
        var cats = JSON.parse(form.dataset.categories || '[]');
        var cat = cats.find(function(c) { return c.id == id; });
        if (!cat) return;
        document.getElementById('edit_name_ar').value = cat.name_ar;
        document.getElementById('edit_name_fr').value = cat.name_fr;
        document.getElementById('edit_name_en').value = cat.name_en;
        var iconVal = cat.icon || defaultIcon;
        var iconInput = document.getElementById('edit_icon');
        if (iconInput) iconInput.value = iconVal;
        var wrap = iconInput ? iconInput.closest('.icon-picker-wrap') : null;
        if (wrap) {
            var uid = wrap.getAttribute('x-data').match(/iconPicker_(\w+)/);
            if (uid) {
                window.dispatchEvent(new CustomEvent('icon-picker-set', { detail: { id: uid[1], value: iconVal } }));
            }
        }
        document.getElementById('edit_color').value = cat.color || defaultColor;
        document.getElementById('edit_type').value = cat.type;
        setToggle('edit_is_active', Boolean(cat.is_active));
        form.action = updateRoute.replace(':id', id);
        const modalEl = document.getElementById('editModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    };
})();
</script>
@endpush
