<?php

use App\Models\Coupon;
use App\Models\TaxRate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.super-admin.app')] class extends Component {
    use WithPagination;

    public string $activeTab = 'coupons';

    public string $couponSearch = '';
    public string $couponStatusFilter = '';
    public int $couponPerPage = 15;

    public string $taxSearch = '';
    public string $taxStatusFilter = '';
    public int $taxPerPage = 15;

    public ?int $deletingCouponId = null;
    public ?int $deletingTaxRateId = null;

    public function getCouponsProperty()
    {
        return Coupon::query()
            ->with('paymentMethods:id,key,name')
            ->when($this->couponSearch, fn($q) => $q->where('code', 'like', "%{$this->couponSearch}%"))
            ->when($this->couponStatusFilter === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->couponStatusFilter === 'inactive', fn($q) => $q->where('is_active', false))
            ->latest()
            ->paginate($this->couponPerPage);
    }

    public function getTaxRatesProperty()
    {
        return TaxRate::query()
            ->with('paymentMethods:id,key,name')
            ->when(
                $this->taxSearch,
                fn($q) => $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->taxSearch}%")->orWhere('country', 'like', "%{$this->taxSearch}%");
                }),
            )
            ->when($this->taxStatusFilter === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->taxStatusFilter === 'inactive', fn($q) => $q->where('is_active', false))
            ->latest()
            ->paginate($this->taxPerPage);
    }

    public function confirmDeleteCoupon(int $id): void
    {
        $this->deletingCouponId = $id;
        $this->dispatch('confirm-delete-coupon');
    }

    public function confirmDeleteTaxRate(int $id): void
    {
        $this->deletingTaxRateId = $id;
        $this->dispatch('confirm-delete-tax-rate');
    }

    public function deleteCoupon(): void
    {
        if (!$this->deletingCouponId) {
            return;
        }
        Coupon::findOrFail($this->deletingCouponId)->delete();
        $this->deletingCouponId = null;
        $this->dispatch('toast', type: 'success', message: __('super-admin.coupon_deleted'));
    }

    public function deleteTaxRate(): void
    {
        if (!$this->deletingTaxRateId) {
            return;
        }
        TaxRate::findOrFail($this->deletingTaxRateId)->delete();
        $this->deletingTaxRateId = null;
        $this->dispatch('toast', type: 'success', message: __('super-admin.tax_rate_deleted'));
    }

    public function mount(): void
    {
        if (!$this->canViewCoupons() && $this->canViewTaxRates()) {
            $this->activeTab = 'taxRates';
        }
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function resetCouponFilters(): void
    {
        $this->couponSearch = '';
        $this->couponStatusFilter = '';
        $this->resetPage();
    }

    public function resetTaxFilters(): void
    {
        $this->taxSearch = '';
        $this->taxStatusFilter = '';
        $this->resetPage();
    }

    public function canViewCoupons(): bool
    {
        return auth()->user()->hasPlatformPermission('coupon.view');
    }

    public function canCreateCoupon(): bool
    {
        return auth()->user()->hasPlatformPermission('coupon.create');
    }

    public function canUpdateCoupon(): bool
    {
        return auth()->user()->hasPlatformPermission('coupon.update');
    }

    public function canDeleteCoupon(): bool
    {
        return auth()->user()->hasPlatformPermission('coupon.delete');
    }

    public function canViewTaxRates(): bool
    {
        return auth()->user()->hasPlatformPermission('tax-rate.view') || auth()->user()->hasPlatformPermission('platform-setting.general');
    }

    public function canCreateTaxRate(): bool
    {
        return auth()->user()->hasPlatformPermission('tax-rate.create') || auth()->user()->hasPlatformPermission('platform-setting.general');
    }

    public function canUpdateTaxRate(): bool
    {
        return auth()->user()->hasPlatformPermission('tax-rate.update') || auth()->user()->hasPlatformPermission('platform-setting.general');
    }

    public function canDeleteTaxRate(): bool
    {
        return auth()->user()->hasPlatformPermission('tax-rate.delete') || auth()->user()->hasPlatformPermission('platform-setting.general');
    }

    public function updatingCouponSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCouponStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCouponPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingTaxSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTaxStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTaxPerPage(): void
    {
        $this->resetPage();
    }
}; ?>

<div x-data="{
    tab: localStorage.getItem('sa_tab_coupons') || '{{ $activeTab }}',
    init() {
        $wire.set('activeTab', this.tab);
        this.$watch('tab', val => {
            localStorage.setItem('sa_tab_coupons', val);
            $wire.set('activeTab', val);
        });
    }
}" @toast.window="window.Toast[$event.detail.type]($event.detail.message)">
    <div class="d-flex gap-2 mb-4 border-bottom pb-2">
        @if ($this->canViewCoupons())
            <button @click="tab = 'coupons'"
                :class="{ 'active-tab': tab === 'coupons' }" class="btn btn-sm px-3 d-flex gap-2"
                style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
                <i class="bi bi-tags me-1"></i>{{ __('super-admin.coupons') }}
            </button>
        @endif
        @if ($this->canViewTaxRates())
            <button @click="tab = 'taxRates'"
                :class="{ 'active-tab': tab === 'taxRates' }" class="btn btn-sm px-3 d-flex gap-2"
                style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
                <i class="bi bi-percent me-1"></i>{{ __('super-admin.tax_rates') }}
            </button>
        @endif
    </div>

    {{-- === COUPONS TAB === --}}
    <div x-show="tab === 'coupons'" x-transition:enter.duration.200ms>
        <div class="data-grid">
            <div class="data-grid-toolbar">
                <div class="data-grid-toolbar-left">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <x-search-filter wire-model="couponSearch" icon="bi-search" debounce="300ms"
                            placeholder="{{ __('super-admin.search_coupon') }}" />

                        {{-- Filter tabs --}}
                        <div class="filter-tabs d-flex" style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            @foreach (['' => __('general.all'), 'active' => __('general.active'), 'inactive' => __('general.inactive')] as $val => $label)
                                <button type="button" wire:click="$set('couponStatusFilter', '{{ $val }}')"
                                    style="padding:5px 12px;font-size:12px;font-weight:500;border:none;background:transparent;color:var(--text-muted);cursor:pointer;transition:all 0.15s"
                                    @style(['background:var(--accent) !important;color:#0F172A !important' => $couponStatusFilter === $val])>
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        @if ($couponSearch || $couponStatusFilter)
                            <button type="button" class="btn"
                                style="padding:7px 10px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer"
                                wire:click="resetCouponFilters">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="data-grid-toolbar-right">
                    <div class="d-flex align-items-center gap-2">
                        <span
                            style="font-size:12px;color:var(--text-muted);white-space:nowrap">{{ __('general.per_page') }}:</span>
                        <select wire:model.live="couponPerPage" class="form-control grid-filter-xs"
                            style="width:auto;min-width:60px;padding:6px 8px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                            @foreach ([10, 15, 25, 50] as $val)
                                <option value="{{ $val }}">{{ $val }}</option>
                            @endforeach
                        </select>
                        @if ($this->canCreateCoupon())
                            <a href="{{ route('super.admin.coupons.create') }}" class="btn"
                                style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer"
                                wire:navigate>
                                <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_coupon') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="data-grid-body">
                @if ($this->coupons->count())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('super-admin.coupon_code') }}</th>
                                <th>{{ __('super-admin.coupon_type') }}</th>
                                <th>{{ __('super-admin.coupon_value') }}</th>
                                <th>{{ __('super-admin.coupon_uses') }}</th>
                                <th>{{ __('super-admin.linked_gateways') ?? 'البوابات' }}</th>
                                <th>{{ __('super-admin.coupon_expires') }}</th>
                                <th>{{ __('general.status') }}</th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->coupons as $coupon)
                                <tr>
                                    <td><code
                                            style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px;font-weight:600">{{ $coupon->code }}</code>
                                    </td>
                                    <td><span
                                            style="font-size:12px;color:var(--text-secondary)">{{ $coupon->type === 'percentage' ? __('general.percentage') : __('general.fixed') }}</span>
                                    </td>
                                    <td><strong>{{ $coupon->type === 'percentage' ? $coupon->value . '%' : config('finance.currency_symbol') . ' ' . number_format($coupon->value, 2) }}</strong>
                                    </td>
                                    <td><span
                                            style="font-size:13px">{{ $coupon->used_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}</span>
                                    </td>
                                    <td>
                                        @php $_gws = $coupon->paymentMethods @endphp
                                        @if($_gws->isNotEmpty())
                                            <span style="font-size:11px;color:var(--text-secondary);white-space:normal;word-break:break-word">
                                                {{ $_gws->pluck('name')->join('، ') }}
                                            </span>
                                        @else
                                            <span style="font-size:11px;color:var(--text-muted)">{{ __('general.all') }}</span>
                                        @endif
                                    </td>
                                    <td class="cell-muted">
                                        {{ $coupon->expires_at ? $coupon->expires_at->format('Y/m/d') : '—' }}</td>
                                    <td>
                                        @if ($coupon->isValid())
                                            <span class="badge"
                                                style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.active') }}</span>
                                        @else
                                            <span class="badge"
                                                style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="col-actions">
                                        <div class="cell-actions">
                                            @if ($this->canUpdateCoupon())
                                                <a href="{{ route('super.admin.coupons.edit', $coupon) }}"
                                                    class="btn"
                                                    style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s"
                                                    title="{{ __('general.edit') }}" wire:navigate>
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            @if ($this->canDeleteCoupon())
                                                <button type="button" class="btn"
                                                    style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s"
                                                    title="{{ __('general.delete') }}"
                                                    wire:click="confirmDeleteCoupon({{ $coupon->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="data-grid-footer">
                        <span>{{ __('general.showing') }}
                            {{ $this->coupons->firstItem() }}&ndash;{{ $this->coupons->lastItem() }}
                            {{ __('general.of') }} {{ $this->coupons->total() }}</span>
                        <div>{{ $this->coupons->links() }}</div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i
                                class="bi bi-ticket-perforated"></i></div>
                        <h4>{{ __('general.no_data') }}</h4>
                        <p>{{ __('super-admin.no_coupons') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- === TAX RATES TAB === --}}
    <div x-show="tab === 'taxRates'" x-cloak x-transition:enter.duration.200ms>
        <div class="data-grid">
            <div class="data-grid-toolbar">
                <div class="data-grid-toolbar-left">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <x-search-filter wire-model="taxSearch" placeholder="{{ __('super-admin.search_tax_rate') }}..." />

                        {{-- Filter tabs --}}
                        <div class="filter-tabs d-flex" style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            @foreach (['' => __('general.all'), 'active' => __('general.active'), 'inactive' => __('general.inactive')] as $val => $label)
                                <button type="button" wire:click="$set('taxStatusFilter', '{{ $val }}')"
                                    style="padding:5px 12px;font-size:12px;font-weight:500;border:none;background:transparent;color:var(--text-muted);cursor:pointer;transition:all 0.15s"
                                    @style(['background:var(--accent) !important;color:#0F172A !important' => $taxStatusFilter === $val])>
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        @if ($taxSearch || $taxStatusFilter)
                            <button type="button" class="btn"
                                style="padding:7px 10px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer"
                                wire:click="resetTaxFilters">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="data-grid-toolbar-right">
                    <div class="d-flex align-items-center gap-2">
                        <span
                            style="font-size:12px;color:var(--text-muted);white-space:nowrap">{{ __('general.per_page') }}:</span>
                        <select wire:model.live="taxPerPage" class="form-control grid-filter-xs"
                            style="width:auto;min-width:60px;padding:6px 8px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                            @foreach ([10, 15, 25, 50] as $val)
                                <option value="{{ $val }}">{{ $val }}</option>
                            @endforeach
                        </select>
                        @if ($this->canCreateTaxRate())
                            <a href="{{ route('super.admin.tax-rates.create') }}" class="btn"
                                style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer"
                                wire:navigate>
                                <i class="bi bi-plus-lg"></i>{{ __('super-admin.create_tax_rate') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="data-grid-body">
                @if ($this->taxRates->count())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('super-admin.tax_rate_name') }}</th>
                                <th>{{ __('super-admin.tax_rate_slug') }}</th>
                                <th>{{ __('super-admin.tax_rate_value') }}</th>
                                <th>{{ __('super-admin.tax_rate_type') }}</th>
                                <th>{{ __('super-admin.tax_rate_country') }}</th>
                                <th>{{ __('super-admin.tax_rate_region') }}</th>
                                <th>{{ __('super-admin.linked_gateways') ?? 'البوابات' }}</th>
                                <th>{{ __('general.status') }}</th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->taxRates as $taxRate)
                                <tr>
                                    <td>{{ $taxRate->name }}</td>
                                    <td><code
                                            style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px">{{ $taxRate->slug ?? '—' }}</code>
                                    </td>
                                    <td><strong>{{ $taxRate->type === 'percentage' ? $taxRate->rate . '%' : config('finance.currency_symbol') . ' ' . number_format($taxRate->rate, 2) }}</strong>
                                    </td>
                                    <td><span
                                            style="font-size:12px;color:var(--text-secondary)">{{ $taxRate->type === 'percentage' ? __('general.percentage') : __('general.fixed') }}</span>
                                    </td>
                                    <td class="cell-muted">
                                        {{ $taxRate->country ? strtoupper($taxRate->country) : '—' }}</td>
                                    <td class="cell-muted">{{ $taxRate->region ?? '—' }}</td>
                                    <td>
                                        @php $_tr_gws = $taxRate->paymentMethods @endphp
                                        @if($_tr_gws->isNotEmpty())
                                            <span style="font-size:11px;color:var(--text-secondary);white-space:normal;word-break:break-word">
                                                {{ $_tr_gws->map(fn($pm) => $pm->name . ' (' . $pm->pivot->charge_type . ')')->join('، ') }}
                                            </span>
                                        @else
                                            <span style="font-size:11px;color:var(--text-muted)">{{ __('general.all') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($taxRate->is_active)
                                            <span class="badge"
                                                style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.active') }}</span>
                                        @else
                                            <span class="badge"
                                                style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600">{{ __('general.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="col-actions">
                                        <div class="cell-actions">
                                            @if ($this->canUpdateTaxRate())
                                                <a href="{{ route('super.admin.tax-rates.edit', $taxRate) }}"
                                                    class="btn"
                                                    style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s"
                                                    title="{{ __('general.edit') }}" wire:navigate>
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            @if ($this->canDeleteTaxRate())
                                                <button type="button" class="btn"
                                                    style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s"
                                                    title="{{ __('general.delete') }}"
                                                    wire:click="confirmDeleteTaxRate({{ $taxRate->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="data-grid-footer">
                        <span>{{ __('general.showing') }}
                            {{ $this->taxRates->firstItem() }}&ndash;{{ $this->taxRates->lastItem() }}
                            {{ __('general.of') }} {{ $this->taxRates->total() }}</span>
                        <div>{{ $this->taxRates->links() }}</div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i
                                class="bi bi-percent"></i></div>
                        <h4>{{ __('general.no_data') }}</h4>
                        <p>{{ __('super-admin.no_tax_rates') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Coupon Confirmation Modal --}}
    <div class="modal fade" id="confirmDeleteCouponModal" tabindex="-1" data-bs-backdrop="static" x-data
        @confirm-delete-coupon.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-trash me-2"
                            style="color:var(--danger)"></i>{{ __('general.confirm') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--danger);margin-bottom:12px"><i
                            class="bi bi-exclamation-triangle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px">
                        {{ __('super-admin.confirm_delete_coupon') }}</p>
                </div>
                <div class="modal-footer"
                    style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn"
                        style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)"
                        data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                    <button type="button" class="btn"
                        style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none"
                        wire:click="deleteCoupon" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteCoupon">{{ __('general.delete') }}</span>
                        <span wire:loading wire:target="deleteCoupon"><span class="spinner-border spinner-border-sm"
                                role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Tax Rate Confirmation Modal --}}
    <div class="modal fade" id="confirmDeleteTaxRateModal" tabindex="-1" data-bs-backdrop="static" x-data
        @confirm-delete-tax-rate.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-trash me-2"
                            style="color:var(--danger)"></i>{{ __('general.confirm') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--danger);margin-bottom:12px"><i
                            class="bi bi-exclamation-triangle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px">
                        {{ __('super-admin.confirm_delete_tax_rate') }}</p>
                </div>
                <div class="modal-footer"
                    style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn"
                        style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)"
                        data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                    <button type="button" class="btn"
                        style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none"
                        wire:click="deleteTaxRate" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteTaxRate">{{ __('general.delete') }}</span>
                        <span wire:loading wire:target="deleteTaxRate"><span class="spinner-border spinner-border-sm"
                                role="status"></span></span>
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
