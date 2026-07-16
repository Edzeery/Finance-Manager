<x-app-layout>
    <x-slot:title>{{ __('goal.edit') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('goal.edit') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('goal.update', $goal) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('goal.single') }} ({{ __('general.ar') }}) <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" value="{{ old('name_ar', $goal->name_ar) }}" class="form-custom @error('name_ar') is-invalid @enderror" required>
                                @error('name_ar') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('goal.single') }} ({{ __('general.fr') }})</label>
                                <input type="text" name="name_fr" value="{{ old('name_fr', $goal->name_fr) }}" class="form-custom">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('goal.single') }} ({{ __('general.en') }})</label>
                                <input type="text" name="name_en" value="{{ old('name_en', $goal->name_en) }}" class="form-custom">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('goal.target_amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="target_amount" value="{{ old('target_amount', $goal->target_amount) }}" class="form-custom" required>
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('goal.current_amount') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="current_amount" value="{{ old('current_amount', $goal->current_amount) }}" class="form-custom">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('goal.target_date') }}</label>
                                <input type="date" name="target_date" value="{{ old('target_date', $goal->target_date?->format('Y-m-d')) }}" class="form-custom">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('goal.status') }} <span class="text-danger">*</span></label>
                                <x-status-select
                                    domain="goal"
                                    name="status"
                                    :selected="old('status', $goal->status->value)"
                                    size="md"
                                    set="bi"
                                    placeholder="{{ __('goal.in_progress') }}"
                                />
                                @error('status') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('goal.icon') }}</label>
                                <select name="icon" class="form-custom">
                                    <option value="bi-flag" {{ old('icon', $goal->icon) === 'bi-flag' ? 'selected' : '' }}><i class="bi bi-flag"></i> {{ __('goal.icon_flag') }}</option>
                                    <option value="bi-house" {{ old('icon', $goal->icon) === 'bi-house' ? 'selected' : '' }}><i class="bi bi-house"></i> {{ __('goal.icon_house') }}</option>
                                    <option value="bi-car" {{ old('icon', $goal->icon) === 'bi-car' ? 'selected' : '' }}><i class="bi bi-car"></i> {{ __('goal.icon_car') }}</option>
                                    <option value="bi-book" {{ old('icon', $goal->icon) === 'bi-book' ? 'selected' : '' }}><i class="bi bi-book"></i> {{ __('goal.icon_book') }}</option>
                                    <option value="bi-heart" {{ old('icon', $goal->icon) === 'bi-heart' ? 'selected' : '' }}><i class="bi bi-heart"></i> {{ __('goal.icon_heart') }}</option>
                                    <option value="bi-gem" {{ old('icon', $goal->icon) === 'bi-gem' ? 'selected' : '' }}><i class="bi bi-gem"></i> {{ __('goal.icon_gem') }}</option>
                                    <option value="bi-globe" {{ old('icon', $goal->icon) === 'bi-globe' ? 'selected' : '' }}><i class="bi bi-globe"></i> {{ __('goal.icon_globe') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('goal.color') }}</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    @foreach (['#3B82F6', '#22C55E', '#EF4444', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#FFC107', '#64748B'] as $c)
                                        <label style="width:32px; height:32px; border-radius:50%; background:{{ $c }}; cursor:pointer; border:2px solid {{ old('color', $goal->color) === $c ? 'var(--text)' : 'transparent' }}">
                                            <input type="radio" name="color" value="{{ $c }}" {{ old('color', $goal->color) === $c ? 'checked' : '' }} style="display:none" @change="document.querySelectorAll('[name=color]').forEach(r=>r.closest('label').style.borderColor='transparent'); $el.closest('label').style.borderColor='var(--text)'">
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('goal.notes') }}</label>
                                <textarea name="notes" class="form-custom" rows="3" maxlength="1000">{{ old('notes', $goal->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-check-lg me-1"></i>{{ __('general.save') }}
                            </button>
                            <a href="{{ route('goal.index') }}" class="btn btn-outline-secondary btn-custom">
                                <i class="bi bi-x-lg me-1"></i>{{ __('general.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
