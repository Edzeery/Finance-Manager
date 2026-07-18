<?php

use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

?>

<div x-data="{
    tab: localStorage.getItem('sa_tab_payment_methods') || '<?php echo e($activeTab); ?>',
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
            <i class="bi bi-credit-card-2-front me-1"></i><?php echo e(__('super-admin.payment_methods')); ?>

        </button>
        <button @click="tab = 'gateways'" :class="{ 'active-tab': tab === 'gateways' }" class="btn btn-sm px-3 d-flex gap-2" style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
            <i class="bi bi-diagram-3 me-1"></i><?php echo e(__('super-admin.gateway_structures')); ?>

        </button>
    </div>

    <div x-show="tab === 'methods'" x-transition:enter.duration.200ms>
        <div class="data-grid">
            <div class="data-grid-toolbar">
                <div class="data-grid-toolbar-left">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['wireModel' => 'search','placeholder' => ''.e(__('super-admin.search_payment_method')).'...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-model' => 'search','placeholder' => ''.e(__('super-admin.search_payment_method')).'...']); ?>
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
                        <select wire:model.live="typeFilter" class="form-control grid-filter-sm" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                            <option value=""><?php echo e(__('general.all_types')); ?></option>
                            <option value="online"><?php echo e(__('super-admin.online')); ?></option>
                            <option value="manual"><?php echo e(__('super-admin.manual')); ?></option>
                            <option value="auto_complete"><?php echo e(__('super-admin.auto_complete')); ?></option>
                        </select>

                        
                        <div class="filter-tabs d-flex" style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['' => __('general.all'), 'active' => __('general.active'), 'inactive' => __('general.inactive')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" wire:click="$set('statusFilter', '<?php echo e($val); ?>')"
                                    style="padding:5px 12px;font-size:12px;font-weight:500;border:none;background:transparent;color:var(--text-muted);cursor:pointer;transition:all 0.15s"
                                    style="<?php echo \Illuminate\Support\Arr::toCssStyles(['background:var(--accent) !important;color:#0F172A !important' => $statusFilter === $val]) ?>">
                                    <?php echo e($label); ?>

                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $typeFilter || $statusFilter): ?>
                            <button type="button" class="btn" style="padding:7px 10px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer" wire:click="resetFilters">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="data-grid-toolbar-right">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:12px;color:var(--text-muted);white-space:nowrap"><?php echo e(__('general.per_page')); ?>:</span>
                        <select wire:model.live="perPage" class="form-control grid-filter-xs" style="width:auto;min-width:60px;padding:6px 8px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [10, 15, 25, 50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>"><?php echo e($val); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <a href="<?php echo e(route('super.admin.payment-methods.create')); ?>" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                            <i class="bi bi-plus-lg"></i><?php echo e(__('super-admin.create_payment_method')); ?>

                        </a>
                    </div>
                </div>
            </div>

            <div class="data-grid-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->paymentMethods->count()): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><?php echo e(__('super-admin.payment_method_icon')); ?></th>
                                <th><?php echo e(__('super-admin.payment_method_key')); ?></th>
                                <th><?php echo e(__('super-admin.payment_method_name')); ?></th>
                                <th><?php echo e(__('super-admin.payment_method_type')); ?></th>
                                <th><?php echo e(__('general.order')); ?></th>
                                <th><?php echo e(__('super-admin.public')); ?></th>
                                <th><?php echo e(__('general.status')); ?></th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span style="width:34px;height:34px;border-radius:8px;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;font-size:16px">
                                            <i class="bi <?php echo e($method->icon ?: 'bi-credit-card'); ?>"></i>
                                        </span>
                                    </td>
                                    <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px"><?php echo e($method->key); ?></code></td>
                                    <td>
                                        <span style="font-weight:500"><?php echo e($method->name); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($method->description): ?>
                                            <div style="font-size:12px;color:var(--text-muted)"><?php echo e($method->description); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($method->isOnline()): ?>
                                            <span class="badge" style="font-size:10px;background:var(--info-light);color:var(--info);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('super-admin.online')); ?></span>
                                        <?php elseif($method->isManual()): ?>
                                            <span class="badge" style="font-size:10px;background:var(--warning-light);color:var(--warning);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('super-admin.manual')); ?></span>
                                        <?php else: ?>
                                            <span class="badge" style="font-size:10px;background:var(--accent-light);color:var(--accent);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('super-admin.auto_complete')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td><?php echo e($method->sort_order); ?></td>
                                    <td>
                                        <button type="button" wire:click="togglePublic(<?php echo e($method->id); ?>)" class="btn btn-sm p-0 border-0 bg-transparent" style="transition:all 0.15s;cursor:pointer" aria-label="Toggle public">
                                            <i class="bi <?php echo e($method->is_public ? 'bi-toggle2-on' : 'bi-toggle2-off'); ?>" style="font-size:20px;color:<?php echo e($method->is_public ? 'var(--success)' : 'var(--text-muted)'); ?>;pointer-events:none;transition:color 0.15s"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" wire:click="toggleStatus(<?php echo e($method->id); ?>)" class="btn btn-sm p-0 border-0 bg-transparent" style="transition:all 0.15s;cursor:pointer" aria-label="Toggle status">
                                            <i class="bi <?php echo e($method->is_active ? 'bi-toggle2-on' : 'bi-toggle2-off'); ?>" style="font-size:20px;color:<?php echo e($method->is_active ? 'var(--success)' : 'var(--text-muted)'); ?>;pointer-events:none;transition:color 0.15s"></i>
                                        </button>
                                    </td>
                                    <td class="col-actions">
                                        <div class="cell-actions">
                                            <a href="<?php echo e(route('super.admin.payment-methods.edit', $method)); ?>" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="<?php echo e(__('general.edit')); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="<?php echo e(__('general.delete')); ?>" wire:click="confirmDeleteMethod(<?php echo e($method->id); ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                    <div class="data-grid-footer">
                        <span><?php echo e(__('general.showing')); ?> <?php echo e($this->paymentMethods->firstItem()); ?>&ndash;<?php echo e($this->paymentMethods->lastItem()); ?> <?php echo e(__('general.of')); ?> <?php echo e($this->paymentMethods->total()); ?></span>
                        <div><?php echo e($this->paymentMethods->links()); ?></div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-credit-card-2-front"></i></div>
                        <h4><?php echo e(__('general.no_data')); ?></h4>
                        <p><?php echo e(__('super-admin.no_payment_methods')); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <div x-show="tab === 'gateways'" x-cloak x-transition:enter.duration.200ms>
        <div class="data-grid">
            <div class="data-grid-toolbar">
                <div class="data-grid-toolbar-left"></div>
                <div class="data-grid-toolbar-right">
                    <a href="<?php echo e(route('super.admin.gateways.create')); ?>" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                        <i class="bi bi-plus-lg"></i><?php echo e(__('super-admin.create_gateway_structure')); ?>

                    </a>
                </div>
            </div>

            <div class="data-grid-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->gateways)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><?php echo e(__('super-admin.payment_method_icon')); ?></th>
                                <th><?php echo e(__('super-admin.payment_method_key')); ?></th>
                                <th><?php echo e(__('super-admin.payment_method_name')); ?></th>
                                <th><?php echo e(__('super-admin.category')); ?></th>
                                <th><?php echo e(__('super-admin.field_count')); ?></th>
                                <th><?php echo e(__('general.order')); ?></th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span style="width:34px;height:34px;border-radius:8px;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;font-size:16px">
                                            <i class="bi <?php echo e($gateway->icon ?: 'bi-diagram-3'); ?>"></i>
                                        </span>
                                    </td>
                                    <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px"><?php echo e($gateway->key); ?></code></td>
                                    <td>
                                        <span style="font-weight:500"><?php echo e($gateway->name); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gateway->description): ?>
                                            <div style="font-size:12px;color:var(--text-muted)"><?php echo e($gateway->description); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td><span class="badge" style="font-size:10px;background:var(--info-light);color:var(--info);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e($gateway->category); ?></span></td>
                                    <td><?php echo e(count($gateway->fields ?? [])); ?></td>
                                    <td><?php echo e($gateway->sort_order); ?></td>
                                    <td class="col-actions">
                                        <div class="cell-actions">
                                            <a href="<?php echo e(route('super.admin.gateways.edit', $gateway)); ?>" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="<?php echo e(__('general.edit')); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="<?php echo e(__('general.delete')); ?>" wire:click="confirmDeleteGateway(<?php echo e($gateway->id); ?>, '<?php echo e($gateway->key); ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-diagram-3"></i></div>
                        <h4><?php echo e(__('general.no_data')); ?></h4>
                        <p><?php echo e(__('super-admin.no_gateway_structures')); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="confirmDeleteMethodModal" tabindex="-1" data-bs-backdrop="static" x-data @confirm-delete-method.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-trash me-2" style="color:var(--danger)"></i><?php echo e(__('general.confirm')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--danger);margin-bottom:12px"><i class="bi bi-exclamation-triangle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px"><?php echo e(__('super-admin.confirm_delete_payment_method')); ?></p>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal"><?php echo e(__('general.cancel')); ?></button>
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none" wire:click="deleteMethod" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteMethod"><?php echo e(__('general.delete')); ?></span>
                        <span wire:loading wire:target="deleteMethod"><span class="spinner-border spinner-border-sm" role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="confirmDeleteGatewayModal" tabindex="-1" data-bs-backdrop="static" x-data @confirm-delete-gateway.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-trash me-2" style="color:var(--danger)"></i><?php echo e(__('general.confirm')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--danger);margin-bottom:12px"><i class="bi bi-exclamation-triangle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px"><?php echo e(__('super-admin.confirm_delete_gateway')); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($deletingGatewayKey): ?>
                        <p style="font-size:13px;color:var(--text-muted)">(<?php echo e($deletingGatewayKey); ?>)</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal"><?php echo e(__('general.cancel')); ?></button>
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none" wire:click="deleteGateway" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteGateway"><?php echo e(__('general.delete')); ?></span>
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
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\pages\super-admin\payment-methods.blade.php ENDPATH**/ ?>