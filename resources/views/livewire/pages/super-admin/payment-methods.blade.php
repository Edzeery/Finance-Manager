<?php

use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.super-admin.app')] class extends Component
{
    use WithPagination;

    public string $activeTab = 'methods';
    public string $search = '';
    public string $typeFilter = '';
    public string $statusFilter = '';
    public int $perPage = 15;

    public ?int $deletingMethodId = null;
    public ?int $deletingGatewayId = null;
    public ?string $deletingGatewayKey = null;

    public function getPaymentMethodsProperty()
    {
        return PaymentMethod::query()
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('key', 'like', "%{$this->search}%");
            }))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->statusFilter === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn($q) => $q->where('is_active', false))
            ->ordered()
            ->paginate($this->perPage);
    }

    public function getGatewaysProperty()
    {
        return PaymentGateway::ordered()->get();
    }

    public function toggleStatus(int $id): void
    {
        $method = PaymentMethod::findOrFail($id);
        $method->update(['is_active' => !$method->is_active]);
        $this->dispatch('toast', type: 'success', message: __('super-admin.payment_method_updated'));
    }

    public function togglePublic(int $id): void
    {
        $method = PaymentMethod::findOrFail($id);
        $method->update(['is_public' => !$method->is_public]);
        $this->dispatch('toast', type: 'success', message: __('super-admin.payment_method_updated'));
    }

    public function confirmDeleteMethod(int $id): void
    {
        $this->deletingMethodId = $id;
        $this->dispatch('confirm-delete-method');
    }

    public function confirmDeleteGateway(int $id, string $key): void
    {
        $this->deletingGatewayId = $id;
        $this->deletingGatewayKey = $key;
        $this->dispatch('confirm-delete-gateway');
    }

    public function deleteMethod(): void
    {
        if (!$this->deletingMethodId) return;
        PaymentMethod::findOrFail($this->deletingMethodId)->delete();
        $this->deletingMethodId = null;
        $this->dispatch('toast', type: 'success', message: __('super-admin.payment_method_deleted'));
    }

    public function deleteGateway(): void
    {
        if (!$this->deletingGatewayId) return;
        PaymentGateway::findOrFail($this->deletingGatewayId)->delete();
        $this->deletingGatewayId = null;
        $this->deletingGatewayKey = null;
        $this->dispatch('toast', type: 'success', message: __('super-admin.payment_method_deleted'));
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }
}; ?>

<div x-data="{
    tab: localStorage.getItem('sa_tab_payment_methods') || '{{ $activeTab }}',
    init() {
        $wire.set('activeTab', this.tab);
        this.$watch('tab', val => {
            localStorage.setItem('sa_tab_payment_methods', val);
            $wire.set('activeTab', val);
        });
    }
}"
     @toast.window="window.Toast[$event.detail.type]($event.detail.message)">
    <div class="d-flex gap-2 mb-4 border-bottom pb-2">
        <button @click="tab = 'methods'" :class="{ 'active-tab': tab === 'methods' }" class="btn btn-sm px-3 d-flex gap-2" style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
            <i class="bi bi-credit-card-2-front me-1"></i>{{ __('super-admin.payment_methods') }}
        </button>
        <button @click="tab = 'gateways'" :class="{ 'active-tab': tab === 'gateways' }" class="btn btn-sm px-3 d-flex gap-2" style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
            <i class="bi bi-diagram-3 me-1"></i>{{ __('super-admin.gateway_structures') }}
        </button>
    </div>

    <div x-show="tab === 'methods'" x-transition:enter.duration.200ms>
        <div class="data-grid">
            <div class="data-grid-toolbar">
                <div class="data-grid-toolbar-left">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <x-search-filter wire-model="search" placeholder="{{ __('super-admin.search_payment_method') }}..." />
                        <select wire:model.live="typeFilter" class="form-control grid-filter-sm" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                            <option value="">{{ __('general.all_types') }}</option>
                            <option value="online">{{ __('super-admin.online') }}</option>
                            <option value="manual">{{ __('super-admin.manual') }}</option>
                            <option value="auto_complete">{{ __('super-admin.auto_complete') }}</option>
                        </select>

                        {{-- Status filter tabs --}}
                        <div class="filter-tabs d-flex" style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            @foreach (['' => __('general.all'), 'active' => __('general.active'), 'inactive' => __('general.inactive')] as $val => $label)
                                <button type="button" wire:click="$set('statusFilter', '{{ $val }}')"
                                    style="padding:5px 12px;font-size:12px;font-weight:500;border:none;background:transparent;color:var(--text-muted);cursor:pointer;transition:all 0.15s"
                                    @style(['background:var(--accent) !important;color:#0F172A !important' => $statusFilter === $val])>
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        @if($search || $typeFilter || $statusFilter)
                            <button type="button" class="btn" style="padding:7px 10px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer" wire:click="resetFilters">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="data-grid-toolbar-right">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:12px;color:var(--text-muted);white-space:nowrap">{{ __('general.per_page') }}:</span>
                        <select wire:model.live="perPage" class="form-control grid-filter-xs" style="width:auto;min-width:60px;padding:6px 8px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                            @foreach([10, 15, 25, 50] as $val)
                                <option value="{{ $val }}">{{ $val }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('super.admin.payment-methods.create') }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                            <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_payment_method') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="data-grid-body">
                @if($this->paymentMethods->count())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('super-admin.payment_method_icon') }}</th>
                                <th>{{ __('super-admin.payment_method_key') }}</th>
                                <th>{{ __('super-admin.payment_method_name') }}</th>
                                <th>{{ __('super-admin.payment_method_type') }}</th>
                                <th>{{ __('general.order') }}</th>
                                <th>{{ __('super-admin.public') }}</th>
                                <th>{{ __('general.status') }}</th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->paymentMethods as $method)
                                <tr>
                                    <td>
                                        <span style="width:34px;height:34px;border-radius:8px;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;font-size:16px">
                                            <i class="bi {{ $method->icon ?: 'bi-credit-card' }}"></i>
                                        </span>
                                    </td>
                                    <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $method->key }}</code></td>
                                    <td>
                                        <span style="font-weight:500">{{ $method->name }}</span>
                                        @if($method->description)
                                            <div style="font-size:12px;color:var(--text-muted)">{{ $method->description }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($method->isOnline())
                                            <span class="badge" style="font-size:10px;background:var(--info-light);color:var(--info);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('super-admin.online') }}</span>
                                        @elseif($method->isManual())
                                            <span class="badge" style="font-size:10px;background:var(--warning-light);color:var(--warning);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('super-admin.manual') }}</span>
                                        @else
                                            <span class="badge" style="font-size:10px;background:var(--accent-light);color:var(--accent);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('super-admin.auto_complete') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $method->sort_order }}</td>
                                    <td>
                                        <button type="button" wire:click="togglePublic({{ $method->id }})" class="btn btn-sm p-0 border-0 bg-transparent" style="transition:all 0.15s;cursor:pointer" aria-label="Toggle public">
                                            <i class="bi {{ $method->is_public ? 'bi-toggle2-on' : 'bi-toggle2-off' }}" style="font-size:20px;color:{{ $method->is_public ? 'var(--success)' : 'var(--text-muted)' }};pointer-events:none;transition:color 0.15s"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" wire:click="toggleStatus({{ $method->id }})" class="btn btn-sm p-0 border-0 bg-transparent" style="transition:all 0.15s;cursor:pointer" aria-label="Toggle status">
                                            <i class="bi {{ $method->is_active ? 'bi-toggle2-on' : 'bi-toggle2-off' }}" style="font-size:20px;color:{{ $method->is_active ? 'var(--success)' : 'var(--text-muted)' }};pointer-events:none;transition:color 0.15s"></i>
                                        </button>
                                    </td>
                                    <td class="col-actions">
                                        <div class="cell-actions">
                                            <a href="{{ route('super.admin.payment-methods.edit', $method) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" wire:click="confirmDeleteMethod({{ $method->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="data-grid-footer">
                        <span>{{ __('general.showing') }} {{ $this->paymentMethods->firstItem() }}&ndash;{{ $this->paymentMethods->lastItem() }} {{ __('general.of') }} {{ $this->paymentMethods->total() }}</span>
                        <div>{{ $this->paymentMethods->links() }}</div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-credit-card-2-front"></i></div>
                        <h4>{{ __('general.no_data') }}</h4>
                        <p>{{ __('super-admin.no_payment_methods') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div x-show="tab === 'gateways'" x-cloak x-transition:enter.duration.200ms>
        <div class="data-grid">
            <div class="data-grid-toolbar">
                <div class="data-grid-toolbar-left"></div>
                <div class="data-grid-toolbar-right">
                    <a href="{{ route('super.admin.gateways.create') }}" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                        <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_gateway_structure') }}
                    </a>
                </div>
            </div>

            <div class="data-grid-body">
                @if(count($this->gateways))
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('super-admin.payment_method_icon') }}</th>
                                <th>{{ __('super-admin.payment_method_key') }}</th>
                                <th>{{ __('super-admin.payment_method_name') }}</th>
                                <th>{{ __('super-admin.category') }}</th>
                                <th>{{ __('super-admin.field_count') }}</th>
                                <th>{{ __('general.order') }}</th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->gateways as $gateway)
                                <tr>
                                    <td>
                                        <span style="width:34px;height:34px;border-radius:8px;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;font-size:16px">
                                            <i class="bi {{ $gateway->icon ?: 'bi-diagram-3' }}"></i>
                                        </span>
                                    </td>
                                    <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $gateway->key }}</code></td>
                                    <td>
                                        <span style="font-weight:500">{{ $gateway->name }}</span>
                                        @if($gateway->description)
                                            <div style="font-size:12px;color:var(--text-muted)">{{ $gateway->description }}</div>
                                        @endif
                                    </td>
                                    <td><span class="badge" style="font-size:10px;background:var(--info-light);color:var(--info);padding:3px 10px;border-radius:6px;font-weight:600">{{ $gateway->category }}</span></td>
                                    <td>{{ count($gateway->fields ?? []) }}</td>
                                    <td>{{ $gateway->sort_order }}</td>
                                    <td class="col-actions">
                                        <div class="cell-actions">
                                            <a href="{{ route('super.admin.gateways.edit', $gateway) }}" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="{{ __('general.edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="{{ __('general.delete') }}" wire:click="confirmDeleteGateway({{ $gateway->id }}, '{{ $gateway->key }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-diagram-3"></i></div>
                        <h4>{{ __('general.no_data') }}</h4>
                        <p>{{ __('super-admin.no_gateway_structures') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Method Confirmation Modal --}}
    <div class="modal fade" id="confirmDeleteMethodModal" tabindex="-1" data-bs-backdrop="static" x-data @confirm-delete-method.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-trash me-2" style="color:var(--danger)"></i>{{ __('general.confirm') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--danger);margin-bottom:12px"><i class="bi bi-exclamation-triangle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px">{{ __('super-admin.confirm_delete_payment_method') }}</p>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none" wire:click="deleteMethod" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteMethod">{{ __('general.delete') }}</span>
                        <span wire:loading wire:target="deleteMethod"><span class="spinner-border spinner-border-sm" role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Gateway Confirmation Modal --}}
    <div class="modal fade" id="confirmDeleteGatewayModal" tabindex="-1" data-bs-backdrop="static" x-data @confirm-delete-gateway.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-trash me-2" style="color:var(--danger)"></i>{{ __('general.confirm') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--danger);margin-bottom:12px"><i class="bi bi-exclamation-triangle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px">{{ __('super-admin.confirm_delete_gateway') }}</p>
                    @if($deletingGatewayKey)
                        <p style="font-size:13px;color:var(--text-muted)">({{ $deletingGatewayKey }})</p>
                    @endif
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none" wire:click="deleteGateway" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteGateway">{{ __('general.delete') }}</span>
                        <span wire:loading wire:target="deleteGateway"><span class="spinner-border spinner-border-sm" role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    .active-tab {
        color: var(--accent) !important;
        border-bottom: 2px solid var(--accent) !important;
        border-radius: 0 !important;
    }
    </style>
</div>
