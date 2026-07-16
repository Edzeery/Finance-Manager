<x-app-layout>
    <x-slot:title>{{ locale_name($budget) }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ locale_name($budget) }}</x-slot>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-list-ul"></i>
                        <span>{{ __('budget.categories') }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($budget->categories->count())
                        <div class="table-responsive">
                            <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>{{ __('budget.category') }}</th>
                                    <th class="text-end">{{ __('budget.allocated_amount') }}</th>
                                    <th class="text-end">{{ __('budget.spent_amount') }}</th>
                                    <th class="text-end">{{ __('budget.remaining') }}</th>
                                    <th style="width:120px">{{ __('budget.adherence') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($budget->categories as $bc)
                                    @php
                                        $pct = $bc->allocated_amount > 0 ? round(($bc->spent_amount / $bc->allocated_amount) * 100, 1) : 0;
                                        $color = $pct > 100 ? 'var(--danger)' : ($pct > 80 ? 'var(--warning)' : 'var(--success)');
                                    @endphp
                                    <tr>
                                        <td>
                                            <i class="{{ $bc->category?->icon ?? 'bi-tag' }}" style="color:{{ $bc->category?->color ?? '#64748B' }}"></i>
                                            {{ locale_name($bc->category ?? new stdClass) }}
                                        </td>
                                        <td class="text-end">{{ number_format($bc->allocated_amount, 2) }}</td>
                                        <td text-start fw-bold style="color:var(--danger)">{{ number_format($bc->spent_amount, 2) }}</td>
                                        <td text-start fw-bold style="color:{{ $bc->spent_amount > $bc->allocated_amount ? 'var(--danger)' : 'var(--success)' }}">
                                            {{ number_format(max(0, $bc->allocated_amount - $bc->spent_amount), 2) }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress" style="flex:1; height:6px; background:var(--border); border-radius:3px">
                                                    <div class="progress-bar" style="width:{{ min($pct, 100) }}%; background:{{ $color }}; border-radius:3px"></div>
                                                </div>
                                                <span style="font-size:12px; font-weight:600; color:{{ $color }}">{{ $pct }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="font-weight:700">
                                    <td>{{ __('budget.total_amount') }}</td>
                                    <td class="text-end">{{ number_format($budget->total_amount, 2) }}</td>
                                    <td class="text-end" style="color:var(--danger)">{{ number_format($budget->totalSpent, 2) }}</td>
                                    <td class="text-end" style="color:{{ $budget->is_exceeded ? 'var(--danger)' : 'var(--success)' }}">
                                        {{ number_format(max(0, $budget->total_amount - $budget->totalSpent), 2) }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress" style="flex:1; height:8px; background:var(--border); border-radius:4px">
                                                <div class="progress-bar" style="width:{{ min($budget->adherence_rate, 100) }}%; background:{{ $budget->is_exceeded ? 'var(--danger)' : ($budget->adherence_rate > 80 ? 'var(--warning)' : 'var(--success)') }}; border-radius:4px"></div>
                                            </div>
                                            <span style="font-size:13px; font-weight:700">{{ $budget->adherence_rate }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    @else
                        <div class="p-4 text-center" style="color:var(--text-muted)">{{ __('general.no_data') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle"></i>
                        <span>{{ __('budget.single') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">{{ __('budget.type') }}</span>
                        <span class="info-value">{{ __("budget.{$budget->type}") }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('budget.total_amount') }}</span>
                        <span class="info-value">{{ number_format($budget->total_amount, 2) }} {{ config('finance.currency_symbol') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('budget.spent') }}</span>
                        <span class="info-value" style="color:var(--danger)">{{ number_format($budget->totalSpent, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('budget.remaining') }}</span>
                        <span class="info-value" style="color:{{ $budget->is_exceeded ? 'var(--danger)' : 'var(--success)' }}">
                            {{ number_format(max(0, $budget->total_amount - $budget->totalSpent), 2) }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('budget.start_date') }}</span>
                        <span class="info-value">{{ $budget->start_date->format('Y/m/d') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('budget.end_date') }}</span>
                        <span class="info-value">{{ $budget->end_date?->format('Y/m/d') ?: 'â€”' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('budget.is_active') }}</span>
                        <span class="info-value">{{ $budget->is_active ? __('general.yes') : __('general.no') }}</span>
                    </div>
                </div>
            </div>

            @if($budget->notes)
                <div class="card-custom mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">{{ __('budget.notes') }}</h6>
                        <p style="font-size:14px; color:var(--text-muted); margin:0">{{ $budget->notes }}</p>
                    </div>
                </div>
            @endif

            @php $canEditBudget = auth()->user()->hasPermission('budget.update'); $canDeleteBudget = auth()->user()->hasPermission('budget.delete'); @endphp
            <div class="d-flex gap-2 mt-4">
                @if($canEditBudget)
                    <a href="{{ route('budget.edit', $budget) }}" class="btn btn-outline-secondary btn-custom" style="flex:1">
                        <i class="bi bi-pencil me-1"></i>{{ __('general.edit') }}
                    </a>
                @endif
                @if($canDeleteBudget)
                    <form action="{{ route('budget.destroy', $budget) }}" method="POST" id="delete-budget-{{ $budget->id }}" style="display:none">
                        @csrf @method('DELETE')
                    </form>
                    <button type="button" class="btn btn-outline-danger btn-custom w-100" @click="window.confirmDelete('budget', {{ $budget->id }})">
                        <i class="bi bi-trash me-1"></i>{{ __('general.delete') }}
                    </button>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
