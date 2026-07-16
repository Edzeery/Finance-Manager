@php
    $perm = fn($p) => auth()->user()->hasPermission("goal.$p");
    $canCreate = $perm('create');
    $canUpdate = $perm('update');
    $canDelete = $perm('delete');
    $canRestore = $perm('restore');
    $canForceDelete = $perm('force-delete');
    $canExport = $perm('export');
    $hasActions = $canUpdate || $canDelete || $canRestore || $canForceDelete;
@endphp

<x-app-layout>
    <x-slot:title>{{ __('goal.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('goal.title') }}</x-slot>

    <x-filter-tabs :tabs="$tabs" current="{{ $tab }}" defaultKey="all" :preserve="['search','per_page']" />

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form method="GET" action="{{ route('goal.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <x-search-filter name="search" :value="request('search')" size="sm" />
            <x-clear-filters :filters="['search']" :route="route('goal.index')" />
        </form>

        <div class="d-flex gap-2 align-items-center">
            <x-data-toolbar entity="goal" :show-export="$canExport" />
            <x-per-page :current="request('per_page', 15)" :route="route('goal.index')" :preserve="['search','tab']" />
            @if($tab !== 'trashed' && $canCreate)
                <a href="{{ route('goal.create') }}" class="btn btn-accent btn-custom">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('goal.add') }}
                </a>
            @endif
        </div>
    </div>

    <form id="bulkForm" method="POST"
          data-bulk-delete-route="{{ route('goal.bulk-delete') }}"
          data-bulk-force-delete-route="{{ route('goal.bulk-force-delete') }}">
        @csrf
    </form>

    @foreach($goals as $goal)
        <form id="delete-form-goal-{{ $goal->id }}" action="{{ route('goal.destroy', $goal) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
        <form id="force-delete-form-goal-{{ $goal->id }}" action="{{ route('goal.force-delete', $goal->id) }}" method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endforeach

    <div class="bulk-bar mb-3" id="bulkBar" style="display:none">
        <div class="d-flex align-items-center gap-3">
            <input type="checkbox" id="selectAll" @change="toggleSelectAll($el)" style="cursor:pointer">
            <span style="color:var(--text-muted); font-size:13px"><span id="selectedCount">0</span> {{ __('general.selected') }}</span>
            @if($tab === 'trashed')
                @if($canRestore)
                    <button type="button" class="btn btn-sm btn-outline-success btn-custom" @click="submitBulk('{{ route('goal.bulk-restore') }}')">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('general.restore') }}
                    </button>
                @endif
                @if($canForceDelete)
                    <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkForceDelete()">
                        <i class="bi bi-trash3 me-1"></i>{{ __('general.force_delete') }}
                    </button>
                @endif
            @else
                @if($canDelete)
                    <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkDelete('goal')">
                        <i class="bi bi-trash me-1"></i>{{ __('general.delete') }}
                    </button>
                @endif
            @endif
        </div>
    </div>

    @if($goals->count())
        <div class="row g-4">
            @foreach($goals as $goal)
                @php
                    $pct = $goal->progress;
                    $isCompleted = $goal->status === \App\Enums\GoalStatus::Completed;
                    $isBehind = !$isCompleted && $goal->target_date && $goal->target_date->isPast();
                    $remaining = $goal->days_remaining;
                    $color = $goal->color ?: '#3B82F6';
                    $icon = $goal->icon ?: 'bi-flag';
                @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="card-custom goal-card">
                        <div class="card-body position-relative">
                            <input type="checkbox" name="ids[]" value="{{ $goal->id }}" class="select-item" form="bulkForm" style="position:absolute; top:12px; right:12px; cursor:pointer; z-index:2">
                            <div class="goal-progress-ring" style="--pct:{{ $pct }}; --color:{{ $isCompleted ? 'var(--success)' : ($isBehind ? 'var(--danger)' : $color) }}">
                                <svg viewBox="0 0 36 36" style="width:72px; height:72px">
                                    <path class="ring-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="var(--border)" stroke-width="3"/>
                                    <path class="ring-fg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="{{ $isCompleted ? 'var(--success)' : ($isBehind ? 'var(--danger)' : $color) }}"
                                          stroke-width="3" stroke-dasharray="{{ $pct }}, 100"/>
                                </svg>
                                <span class="progress-text">{{ $pct }}%</span>
                            </div>

                            <div style="flex:1">
                                <h5 class="mb-1" style="font-size:15px; font-weight:600">
                                    <i class="{{ $icon }} me-1" style="color:{{ $color }}"></i>
                                    {{ locale_name($goal) }}
                                </h5>
                                <div style="font-size:13px; color:var(--text-muted)">
                                    @if($isCompleted)
                                        <x-status-badge domain="goal" status="completed" set="bi" />
                                    @elseif($goal->status === \App\Enums\GoalStatus::Cancelled)
                                        <x-status-badge domain="goal" status="cancelled" set="bi" />
                                    @else
                                        @if($remaining !== null)
                                            <span>{{ $remaining }} {{ __('goal.days_remaining') }}</span>
                                            @if($isBehind)
                                                <span class="ms-2" style="color:var(--danger)"><i class="bi bi-exclamation-triangle-fill"></i> {{ __('goal.behind') }}</span>
                                            @endif
                                        @endif
                                    @endif
                                </div>

                                <div class="d-flex justify-content-between mt-2" style="font-size:13px">
                                    <span>{{ number_format($goal->current_amount, 0) }}</span>
                                    <span class="fw-bold">{{ number_format($goal->target_amount, 0) }}</span>
                                </div>

                                @if(!$isCompleted && $goal->status !== \App\Enums\GoalStatus::Cancelled)
                                    <div style="font-size:12px; color:var(--text-muted); margin-top:4px">
                                        <i class="bi bi-calendar me-1"></i>{{ __('goal.monthly_target') }}: <strong>{{ number_format($goal->monthly_target, 0) }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="goal-actions">
                                @if($tab === 'trashed')
                                    @if($canRestore)
                                        <form action="{{ route('goal.restore', $goal->id) }}" method="POST" style="display:inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="action-btn" title="{{ __('general.restore') }}">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($canForceDelete)
                                        <button type="button" class="action-btn" title="{{ __('general.force_delete') }}" style="color:var(--danger)" @click="confirmForceDelete('goal', {{ $goal->id }})">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    @endif
                                @else
                                    @if($canUpdate)
                                        <a href="{{ route('goal.edit', $goal) }}" class="action-btn" title="{{ __('general.edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <button type="button" class="action-btn" title="{{ __('general.delete') }}" @click="confirmDelete('goal', {{ $goal->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <x-pagination-info :items="$goals" />
            <div>
                {{ $goals->appends(request()->except('page'))->links() }}
            </div>
        </div>
    @else
        <x-empty-state
            icon="bi-flag"
            :title="$tab === 'trashed' ? __('goal.trashed_empty_title') : __('goal.no_goals')"
            :message="$tab === 'trashed' ? __('goal.trashed_empty_message') : __('goal.create_first_goal')"
            :action="$tab === 'trashed' ? route('goal.index') : ($canCreate ? route('goal.create') : '#')"
            :actionText="$tab === 'trashed' ? __('general.active') : __('goal.add')"
        />
    @endif

</x-app-layout>
