<?php

use App\Models\Coupon;
use App\Models\TaxRate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

?>

<div x-data="{
    tab: localStorage.getItem('sa_tab_coupons') || '<?php echo e($activeTab); ?>',
    init() {
        $wire.set('activeTab', this.tab);
        this.$watch('tab', val => {
            localStorage.setItem('sa_tab_coupons', val);
            $wire.set('activeTab', val);
        });
    }
}" @toast.window="window.Toast[$event.detail.type]($event.detail.message)">
    <div class="d-flex gap-2 mb-4 border-bottom pb-2">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->canViewCoupons()): ?>
            <button @click="tab = 'coupons'"
                :class="{ 'active-tab': tab === 'coupons' }" class="btn btn-sm px-3 d-flex gap-2"
                style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
                <i class="bi bi-tags me-1"></i><?php echo e(__('super-admin.coupons')); ?>

            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->canViewTaxRates()): ?>
            <button @click="tab = 'taxRates'"
                :class="{ 'active-tab': tab === 'taxRates' }" class="btn btn-sm px-3 d-flex gap-2"
                style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
                <i class="bi bi-percent me-1"></i><?php echo e(__('super-admin.tax_rates')); ?>

            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div x-show="tab === 'coupons'" x-transition:enter.duration.200ms>
        <div class="data-grid">
            <div class="data-grid-toolbar">
                <div class="data-grid-toolbar-left">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['wireModel' => 'couponSearch','icon' => 'bi-search','debounce' => '300ms','placeholder' => ''.e(__('super-admin.search_coupon')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-model' => 'couponSearch','icon' => 'bi-search','debounce' => '300ms','placeholder' => ''.e(__('super-admin.search_coupon')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $attributes = $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $component = $__componentOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>

                        
                        <div class="filter-tabs d-flex" style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['' => __('general.all'), 'active' => __('general.active'), 'inactive' => __('general.inactive')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" wire:click="$set('couponStatusFilter', '<?php echo e($val); ?>')"
                                    style="padding:5px 12px;font-size:12px;font-weight:500;border:none;background:transparent;color:var(--text-muted);cursor:pointer;transition:all 0.15s"
                                    style="<?php echo \Illuminate\Support\Arr::toCssStyles(['background:var(--accent) !important;color:#0F172A !important' => $couponStatusFilter === $val]) ?>">
                                    <?php echo e($label); ?>

                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($couponSearch || $couponStatusFilter): ?>
                            <button type="button" class="btn"
                                style="padding:7px 10px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer"
                                wire:click="resetCouponFilters">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="data-grid-toolbar-right">
                    <div class="d-flex align-items-center gap-2">
                        <span
                            style="font-size:12px;color:var(--text-muted);white-space:nowrap"><?php echo e(__('general.per_page')); ?>:</span>
                        <select wire:model.live="couponPerPage" class="form-control grid-filter-xs"
                            style="width:auto;min-width:60px;padding:6px 8px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [10, 15, 25, 50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>"><?php echo e($val); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->canCreateCoupon()): ?>
                            <a href="<?php echo e(route('super.admin.coupons.create')); ?>" class="btn"
                                style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer"
                                wire:navigate>
                                <i class="bi bi-plus-lg"></i><?php echo e(__('super-admin.create_coupon')); ?>

                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="data-grid-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->coupons->count()): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><?php echo e(__('super-admin.coupon_code')); ?></th>
                                <th><?php echo e(__('super-admin.coupon_type')); ?></th>
                                <th><?php echo e(__('super-admin.coupon_value')); ?></th>
                                <th><?php echo e(__('super-admin.coupon_uses')); ?></th>
                                <th><?php echo e(__('super-admin.linked_gateways') ?? 'البوابات'); ?></th>
                                <th><?php echo e(__('super-admin.coupon_expires')); ?></th>
                                <th><?php echo e(__('general.status')); ?></th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><code
                                            style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px;font-weight:600"><?php echo e($coupon->code); ?></code>
                                    </td>
                                    <td><span
                                            style="font-size:12px;color:var(--text-secondary)"><?php echo e($coupon->type === 'percentage' ? __('general.percentage') : __('general.fixed')); ?></span>
                                    </td>
                                    <td><strong><?php echo e($coupon->type === 'percentage' ? $coupon->value . '%' : config('finance.currency_symbol') . ' ' . number_format($coupon->value, 2)); ?></strong>
                                    </td>
                                    <td><span
                                            style="font-size:13px"><?php echo e($coupon->used_count); ?><?php echo e($coupon->max_uses ? ' / ' . $coupon->max_uses : ''); ?></span>
                                    </td>
                                    <td>
                                        <?php $_gws = $coupon->paymentMethods ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($_gws->isNotEmpty()): ?>
                                            <span style="font-size:11px;color:var(--text-secondary);white-space:normal;word-break:break-word">
                                                <?php echo e($_gws->pluck('name')->join('، ')); ?>

                                            </span>
                                        <?php else: ?>
                                            <span style="font-size:11px;color:var(--text-muted)"><?php echo e(__('general.all')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="cell-muted">
                                        <?php echo e($coupon->expires_at ? $coupon->expires_at->format('Y/m/d') : '—'); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coupon->isValid()): ?>
                                            <span class="badge"
                                                style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('general.active')); ?></span>
                                        <?php else: ?>
                                            <span class="badge"
                                                style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('general.inactive')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="col-actions">
                                        <div class="cell-actions">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->canUpdateCoupon()): ?>
                                                <a href="<?php echo e(route('super.admin.coupons.edit', $coupon)); ?>"
                                                    class="btn"
                                                    style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s"
                                                    title="<?php echo e(__('general.edit')); ?>" wire:navigate>
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->canDeleteCoupon()): ?>
                                                <button type="button" class="btn"
                                                    style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s"
                                                    title="<?php echo e(__('general.delete')); ?>"
                                                    wire:click="confirmDeleteCoupon(<?php echo e($coupon->id); ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                    <div class="data-grid-footer">
                        <span><?php echo e(__('general.showing')); ?>

                            <?php echo e($this->coupons->firstItem()); ?>&ndash;<?php echo e($this->coupons->lastItem()); ?>

                            <?php echo e(__('general.of')); ?> <?php echo e($this->coupons->total()); ?></span>
                        <div><?php echo e($this->coupons->links()); ?></div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i
                                class="bi bi-ticket-perforated"></i></div>
                        <h4><?php echo e(__('general.no_data')); ?></h4>
                        <p><?php echo e(__('super-admin.no_coupons')); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div x-show="tab === 'taxRates'" x-cloak x-transition:enter.duration.200ms>
        <div class="data-grid">
            <div class="data-grid-toolbar">
                <div class="data-grid-toolbar-left">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['wireModel' => 'taxSearch','placeholder' => ''.e(__('super-admin.search_tax_rate')).'...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-model' => 'taxSearch','placeholder' => ''.e(__('super-admin.search_tax_rate')).'...']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $attributes = $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $component = $__componentOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>

                        
                        <div class="filter-tabs d-flex" style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['' => __('general.all'), 'active' => __('general.active'), 'inactive' => __('general.inactive')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" wire:click="$set('taxStatusFilter', '<?php echo e($val); ?>')"
                                    style="padding:5px 12px;font-size:12px;font-weight:500;border:none;background:transparent;color:var(--text-muted);cursor:pointer;transition:all 0.15s"
                                    style="<?php echo \Illuminate\Support\Arr::toCssStyles(['background:var(--accent) !important;color:#0F172A !important' => $taxStatusFilter === $val]) ?>">
                                    <?php echo e($label); ?>

                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taxSearch || $taxStatusFilter): ?>
                            <button type="button" class="btn"
                                style="padding:7px 10px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer"
                                wire:click="resetTaxFilters">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="data-grid-toolbar-right">
                    <div class="d-flex align-items-center gap-2">
                        <span
                            style="font-size:12px;color:var(--text-muted);white-space:nowrap"><?php echo e(__('general.per_page')); ?>:</span>
                        <select wire:model.live="taxPerPage" class="form-control grid-filter-xs"
                            style="width:auto;min-width:60px;padding:6px 8px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [10, 15, 25, 50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>"><?php echo e($val); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->canCreateTaxRate()): ?>
                            <a href="<?php echo e(route('super.admin.tax-rates.create')); ?>" class="btn"
                                style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer"
                                wire:navigate>
                                <i class="bi bi-plus-lg"></i><?php echo e(__('super-admin.create_tax_rate')); ?>

                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="data-grid-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->taxRates->count()): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><?php echo e(__('super-admin.tax_rate_name')); ?></th>
                                <th><?php echo e(__('super-admin.tax_rate_slug')); ?></th>
                                <th><?php echo e(__('super-admin.tax_rate_value')); ?></th>
                                <th><?php echo e(__('super-admin.tax_rate_type')); ?></th>
                                <th><?php echo e(__('super-admin.tax_rate_country')); ?></th>
                                <th><?php echo e(__('super-admin.tax_rate_region')); ?></th>
                                <th><?php echo e(__('super-admin.linked_gateways') ?? 'البوابات'); ?></th>
                                <th><?php echo e(__('general.status')); ?></th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->taxRates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taxRate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($taxRate->name); ?></td>
                                    <td><code
                                            style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px"><?php echo e($taxRate->slug ?? '—'); ?></code>
                                    </td>
                                    <td><strong><?php echo e($taxRate->type === 'percentage' ? $taxRate->rate . '%' : config('finance.currency_symbol') . ' ' . number_format($taxRate->rate, 2)); ?></strong>
                                    </td>
                                    <td><span
                                            style="font-size:12px;color:var(--text-secondary)"><?php echo e($taxRate->type === 'percentage' ? __('general.percentage') : __('general.fixed')); ?></span>
                                    </td>
                                    <td class="cell-muted">
                                        <?php echo e($taxRate->country ? strtoupper($taxRate->country) : '—'); ?></td>
                                    <td class="cell-muted"><?php echo e($taxRate->region ?? '—'); ?></td>
                                    <td>
                                        <?php $_tr_gws = $taxRate->paymentMethods ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($_tr_gws->isNotEmpty()): ?>
                                            <span style="font-size:11px;color:var(--text-secondary);white-space:normal;word-break:break-word">
                                                <?php echo e($_tr_gws->map(fn($pm) => $pm->name . ' (' . $pm->pivot->charge_type . ')')->join('، ')); ?>

                                            </span>
                                        <?php else: ?>
                                            <span style="font-size:11px;color:var(--text-muted)"><?php echo e(__('general.all')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taxRate->is_active): ?>
                                            <span class="badge"
                                                style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('general.active')); ?></span>
                                        <?php else: ?>
                                            <span class="badge"
                                                style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('general.inactive')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="col-actions">
                                        <div class="cell-actions">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->canUpdateTaxRate()): ?>
                                                <a href="<?php echo e(route('super.admin.tax-rates.edit', $taxRate)); ?>"
                                                    class="btn"
                                                    style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s"
                                                    title="<?php echo e(__('general.edit')); ?>" wire:navigate>
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->canDeleteTaxRate()): ?>
                                                <button type="button" class="btn"
                                                    style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s"
                                                    title="<?php echo e(__('general.delete')); ?>"
                                                    wire:click="confirmDeleteTaxRate(<?php echo e($taxRate->id); ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                    <div class="data-grid-footer">
                        <span><?php echo e(__('general.showing')); ?>

                            <?php echo e($this->taxRates->firstItem()); ?>&ndash;<?php echo e($this->taxRates->lastItem()); ?>

                            <?php echo e(__('general.of')); ?> <?php echo e($this->taxRates->total()); ?></span>
                        <div><?php echo e($this->taxRates->links()); ?></div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i
                                class="bi bi-percent"></i></div>
                        <h4><?php echo e(__('general.no_data')); ?></h4>
                        <p><?php echo e(__('super-admin.no_tax_rates')); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="confirmDeleteCouponModal" tabindex="-1" data-bs-backdrop="static" x-data
        @confirm-delete-coupon.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-trash me-2"
                            style="color:var(--danger)"></i><?php echo e(__('general.confirm')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--danger);margin-bottom:12px"><i
                            class="bi bi-exclamation-triangle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px">
                        <?php echo e(__('super-admin.confirm_delete_coupon')); ?></p>
                </div>
                <div class="modal-footer"
                    style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn"
                        style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)"
                        data-bs-dismiss="modal"><?php echo e(__('general.cancel')); ?></button>
                    <button type="button" class="btn"
                        style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none"
                        wire:click="deleteCoupon" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteCoupon"><?php echo e(__('general.delete')); ?></span>
                        <span wire:loading wire:target="deleteCoupon"><span class="spinner-border spinner-border-sm"
                                role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="confirmDeleteTaxRateModal" tabindex="-1" data-bs-backdrop="static" x-data
        @confirm-delete-tax-rate.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-trash me-2"
                            style="color:var(--danger)"></i><?php echo e(__('general.confirm')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--danger);margin-bottom:12px"><i
                            class="bi bi-exclamation-triangle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px">
                        <?php echo e(__('super-admin.confirm_delete_tax_rate')); ?></p>
                </div>
                <div class="modal-footer"
                    style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn"
                        style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)"
                        data-bs-dismiss="modal"><?php echo e(__('general.cancel')); ?></button>
                    <button type="button" class="btn"
                        style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none"
                        wire:click="deleteTaxRate" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteTaxRate"><?php echo e(__('general.delete')); ?></span>
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
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire/pages/super-admin/coupons-and-tax-rates.blade.php ENDPATH**/ ?>