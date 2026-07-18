<?php

use App\Models\Payment;
use App\Services\Payments\Noest\NoestService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<?php
$sl = [
    "upload" => __("super-admin.noest_status_pending"),
    "customer_validation" => __("super-admin.noest_status_validated"),
    "validation_collect_colis" => __("super-admin.noest_status_collected"),
    "fdr_activated" => __("super-admin.noest_status_out_for_delivery"),
    "livre" => __("super-admin.noest_status_delivered"),
    "mise_a_jour" => __("super-admin.noest_status_attempt"),
    "return_asked_by_customer" => __("super-admin.noest_status_return_requested"),
    "return_dispatched_to_partenaire" => __("super-admin.noest_status_return_shipped"),
    "return_recu" => __("super-admin.noest_status_return_received"),
    "colis_suspendu" => __("super-admin.noest_status_suspended"),
    "prepa_expedition" => __("super-admin.noest_status_preparing"),
    "attente_expedition" => __("super-admin.noest_status_waiting_shipment"),
    "verssement_admin_cust" => __("super-admin.noest_status_paid"),
];
$ss = [
    "upload" => "background:var(--warning-light);color:var(--warning)",
    "customer_validation" => "background:var(--info-light);color:var(--info)",
    "validation_collect_colis" => "background:rgba(20,184,166,0.12);color:#0D9488",
    "fdr_activated" => "background:var(--warning-light);color:var(--warning)",
    "livre" => "background:var(--success-light);color:var(--success)",
    "mise_a_jour" => "background:rgba(249,115,22,0.12);color:#EA580C",
    "return_asked_by_customer" => "background:rgba(168,85,247,0.12);color:#9333EA",
    "return_dispatched_to_partenaire" => "background:rgba(99,102,241,0.12);color:#4F46E5",
    "return_recu" => "background:var(--border);color:var(--text-muted)",
    "colis_suspendu" => "background:var(--danger-light);color:var(--danger)",
    "prepa_expedition" => "background:var(--bg-subtle);color:var(--text)",
    "attente_expedition" => "background:var(--bg-subtle);color:var(--text)",
    "verssement_admin_cust" => "background:rgba(5,150,105,0.12);color:#059669",
];
?>

    <?php
        $allTracking = array_map(fn($o) => $o["tracking"], $this->paginatedOrders["items"] ?? []);
    ?>
    <div class="data-grid" x-data="noestOrders()"
         data-tracking-numbers="<?php echo e(json_encode($allTracking)); ?>"
         data-copied-message="<?php echo e(__('super-admin.noest_copied')); ?>">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <div class="d-flex flex-wrap align-items-center gap-2">
                <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['wireModel' => 'search','placeholder' => ''.e(__('general.search')).'...','minWidth' => '200px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-model' => 'search','placeholder' => ''.e(__('general.search')).'...','min-width' => '200px']); ?>
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

                    <select wire:model.live="statusFilter" class="form-control" style="width:auto;min-width:120px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                        <option value=""><?php echo e(__("general.all_status")); ?></option>
                        <option value="upload"><?php echo e(__("super-admin.noest_status_pending")); ?></option>
                        <option value="customer_validation"><?php echo e(__("super-admin.noest_status_validated")); ?></option>
                        <option value="validation_collect_colis"><?php echo e(__("super-admin.noest_status_collected")); ?></option>
                        <option value="fdr_activated"><?php echo e(__("super-admin.noest_status_out_for_delivery")); ?></option>
                        <option value="livre"><?php echo e(__("super-admin.noest_status_delivered")); ?></option>
                        <option value="mise_a_jour"><?php echo e(__("super-admin.noest_status_attempt")); ?></option>
                        <option value="return_asked_by_customer"><?php echo e(__("super-admin.noest_status_return_requested")); ?></option>
                        <option value="return_dispatched_to_partenaire"><?php echo e(__("super-admin.noest_status_return_shipped")); ?></option>
                        <option value="return_recu"><?php echo e(__("super-admin.noest_status_return_received")); ?></option>
                        <option value="colis_suspendu"><?php echo e(__("super-admin.noest_status_suspended")); ?></option>
                        <option value="prepa_expedition"><?php echo e(__("super-admin.noest_status_preparing")); ?></option>
                        <option value="attente_expedition"><?php echo e(__("super-admin.noest_status_waiting_shipment")); ?></option>
                        <option value="verssement_admin_cust"><?php echo e(__("super-admin.noest_status_paid")); ?></option>
                    </select>
                    <select wire:model.live="wilayaFilter" class="form-control" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                        <option value=""><?php echo e(__("super-admin.noest_all_wilayas")); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $wilayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wilaya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($wilaya['code'] ?? $wilaya['id']); ?>"><?php echo e($wilaya['nom'] ?? $wilaya['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                    <button type="button" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer" wire:click="exportCsv">
                        <i class="bi bi-download me-1"></i><?php echo e(__("super-admin.noest_export_csv")); ?>

                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedOrders)): ?>
                        <button type="button" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--sa-indigo-light);color:var(--sa-indigo);font-weight:600;border:none;cursor:pointer" wire:click="bulkValidate">
                            <i class="bi bi-check2-all me-1"></i><?php echo e(__("super-admin.noest_bulk_validate")); ?> (<?php echo e(count($selectedOrders)); ?>)
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $statusFilter || $wilayaFilter): ?>
                        <button type="button" class="btn" style="padding:7px 10px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer" wire:click="$set('search','');$set('statusFilter','');$set('wilayaFilter','')">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div class="data-grid-toolbar-right">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:12px;color:var(--text-muted);white-space:nowrap"><?php echo e(__("general.per_page")); ?>:</span>
                    <select wire:model.live="perPage" class="form-control" style="width:auto;min-width:60px;padding:6px 8px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [10, 15, 25, 50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>"><?php echo e($val); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="data-grid-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loading): ?>
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </div>
                    <h4><?php echo e(__("general.loading")); ?></h4>
                </div>
            <?php elseif(!count($this->paginatedOrders["items"])): ?>
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h4><?php echo e(__("super-admin.noest_no_orders")); ?></h4>
                    <p><?php echo e(__("super-admin.noest_no_orders_desc")); ?></p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-checkbox">
                                <input type="checkbox" class="form-check-input" style="accent-color:var(--accent)" x-on:change="toggleSelectAll($event.target.checked)" :checked="selectedOrders.length === <?php echo e(count($this->paginatedOrders["items"])); ?> && <?php echo e(count($this->paginatedOrders["items"])); ?> > 0">
                            </th>
                            <th><a href="#" wire:click.prevent="sortBy('tracking')" class="d-flex align-items-center gap-1" style="color:inherit;text-decoration:none"><?php echo e(__("super-admin.noest_tracking")); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === "tracking"): ?> <i class="bi bi-arrow-<?php echo e($sortDirection === "asc" ? "up" : "down"); ?>"></i> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></a></th>
                            <th><a href="#" wire:click.prevent="sortBy('reference')" class="d-flex align-items-center gap-1" style="color:inherit;text-decoration:none"><?php echo e(__("super-admin.noest_reference")); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === "reference"): ?> <i class="bi bi-arrow-<?php echo e($sortDirection === "asc" ? "up" : "down"); ?>"></i> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></a></th>
                            <th><?php echo e(__("super-admin.noest_client")); ?></th>
                            <th><?php echo e(__("general.phone")); ?></th>
                            <th><?php echo e(__("super-admin.noest_product")); ?></th>
                            <th><a href="#" wire:click.prevent="sortBy('montant')" class="d-flex align-items-center gap-1" style="color:inherit;text-decoration:none"><?php echo e(__("super-admin.noest_amount")); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === "montant"): ?> <i class="bi bi-arrow-<?php echo e($sortDirection === "asc" ? "up" : "down"); ?>"></i> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></a></th>
                            <th><?php echo e(__("super-admin.noest_wilaya")); ?></th>
                            <th><a href="#" wire:click.prevent="sortBy('status')" class="d-flex align-items-center gap-1" style="color:inherit;text-decoration:none"><?php echo e(__("super-admin.noest_status")); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === "status"): ?> <i class="bi bi-arrow-<?php echo e($sortDirection === "asc" ? "up" : "down"); ?>"></i> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></a></th>
                            <th><a href="#" wire:click.prevent="sortBy('created_at')" class="d-flex align-items-center gap-1" style="color:inherit;text-decoration:none"><?php echo e(__("super-admin.noest_created_at")); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === "created_at"): ?> <i class="bi bi-arrow-<?php echo e($sortDirection === "asc" ? "up" : "down"); ?>"></i> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></a></th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->paginatedOrders["items"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="col-checkbox">
                                    <input type="checkbox" class="form-check-input" style="accent-color:var(--accent)" x-model="selectedOrders" value="<?php echo e($order["tracking"]); ?>">
                                </td>
                                <td>
                                    <a href="#" x-on:click.prevent="copyToClipboard('<?php echo e($order["tracking"]); ?>')" class="d-flex align-items-center gap-1" style="text-decoration:none" title="<?php echo e(__("super-admin.noest_click_to_copy")); ?>">
                                        <code style="font-size:12px;background:var(--bg-subtle);padding:2px 6px;border-radius:4px;color:var(--sa-indigo)"><?php echo e($order["tracking"]); ?></code>
                                        <i class="bi bi-clipboard" style="font-size:11px;color:var(--text-muted)"></i>
                                    </a>
                                </td>
                                <td class="cell-muted" style="font-size:12px"><?php echo e($order["reference"]); ?></td>
                                <td><?php echo e($order["client"]); ?></td>
                                <td style="font-size:12px"><?php echo e($order["phone"]); ?><?php echo e($order["phone_2"] ? " / " . $order["phone_2"] : ""); ?></td>
                                <td style="font-size:12px;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?php echo e($order["produit"]); ?>"><?php echo e($order["produit"]); ?></td>
                                <td><strong><?php echo e(number_format($order["montant"], 2)); ?></strong> <span style="font-size:11px;color:var(--text-muted)">DZD</span></td>
                                <td style="font-size:12px"><?php echo e($order["wilaya_name"]); ?></td>
                                <td><span class="badge" style="font-size:10px;padding:3px 10px;border-radius:6px;font-weight:600;<?php echo e($ss[$order["status"]] ?? "background:var(--border);color:var(--text-muted)"); ?>"><?php echo e($sl[$order["status"]] ?? $order["status"]); ?></span></td>
                                <td class="cell-muted" style="font-size:12px"><?php echo e($order["created_at"]); ?></td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;transition:all 0.15s" title="<?php echo e(__("super-admin.noest_edit_title")); ?>" wire:click="editOrder('<?php echo e($order["tracking"]); ?>')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--success);font-size:13px;transition:all 0.15s" title="<?php echo e(__("super-admin.noest_validate")); ?>" wire:click="confirmValidate('<?php echo e($order["tracking"]); ?>')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--info);font-size:13px;transition:all 0.15s" title="<?php echo e(__("super-admin.noest_download_pdf")); ?>" wire:click="downloadLabel('<?php echo e($order["tracking"]); ?>')">
                                            <i class="bi bi-filetype-pdf"></i>
                                        </button>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="<?php echo e(__("general.delete")); ?>" wire:click="confirmDelete('<?php echo e($order["tracking"]); ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loading && count($this->paginatedOrders["items"])): ?>
            <div class="data-grid-footer">
                <span style="font-size:13px;color:var(--text-muted)"><?php echo e(__("general.showing")); ?> <?php echo e($this->paginatedOrders["from"]); ?>&ndash;<?php echo e($this->paginatedOrders["to"]); ?> <?php echo e(__("general.of")); ?> <?php echo e($this->paginatedOrders["total"]); ?></span>
                <div class="d-flex align-items-center gap-1">
                    <?php $cur = $this->paginatedOrders["page"]; $lst = $this->paginatedOrders["lastPage"]; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cur > 1): ?>
                        <button type="button" class="btn" style="padding:5px 10px;font-size:12px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer" wire:click="$set('page',1)"><i class="bi bi-chevron-double-left"></i></button>
                        <button type="button" class="btn" style="padding:5px 10px;font-size:12px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer" wire:click="$set('page',<?php echo e($cur - 1); ?>)"><i class="bi bi-chevron-left"></i></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($p = max(1, $cur - 2); $p <= min($lst, $cur + 2); $p++): ?>
                        <button type="button" class="btn" style="padding:5px 10px;font-size:12px;border-radius:var(--radius-xs);border:<?php echo e($p === $cur ? "none" : "1px solid var(--border)"); ?>;background:<?php echo e($p === $cur ? "var(--accent)" : "transparent"); ?>;color:<?php echo e($p === $cur ? "#0F172A" : "var(--text)"); ?>;font-weight:<?php echo e($p === $cur ? "600" : "400"); ?>;cursor:pointer" wire:click="$set('page',<?php echo e($p); ?>)"><?php echo e($p); ?></button>
                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cur < $lst): ?>
                        <button type="button" class="btn" style="padding:5px 10px;font-size:12px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer" wire:click="$set('page',<?php echo e($cur + 1); ?>)"><i class="bi bi-chevron-right"></i></button>
                        <button type="button" class="btn" style="padding:5px 10px;font-size:12px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer" wire:click="$set('page',<?php echo e($lst); ?>)"><i class="bi bi-chevron-double-right"></i></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="modal fade" id="editOrderModal" tabindex="-1" data-bs-backdrop="static" x-data <?php echo $__env->yieldSection(); ?>-edit-modal.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-pencil-square me-2" style="color:var(--accent)"></i><?php echo e(__("super-admin.noest_edit_title")); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size:12px"></button>
                </div>
                <div class="modal-body" style="padding:20px">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingTracking && $editData): ?>
                        <?php $isShipped = in_array($editData["status"] ?? "", ["fdr_activated","livre","mise_a_jour"]); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isShipped): ?>
                            <div class="alert alert-warning" style="padding:10px 14px;font-size:12px;border-radius:var(--radius-sm);margin-bottom:16px">
                                <i class="bi bi-info-circle-fill me-1"></i><?php echo e(__("super-admin.noest_edit_shipped_warning")); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px"><?php echo e(__("super-admin.noest_tracking")); ?></label>
                                <input type="text" class="form-control" value="<?php echo e($editingTracking); ?>" disabled style="font-size:13px;background:var(--bg-subtle)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px"><?php echo e(__("super-admin.noest_client")); ?></label>
                                <input type="text" class="form-control" wire:model="editData.client" style="font-size:13px">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px"><?php echo e(__("general.phone")); ?></label>
                                <input type="text" class="form-control" wire:model="editData.phone" style="font-size:13px">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px"><?php echo e(__("general.address")); ?></label>
                                <input type="text" class="form-control" wire:model="editData.adresse" style="font-size:13px">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px"><?php echo e(__("super-admin.noest_amount")); ?></label>
                                <input type="number" step="0.01" class="form-control" wire:model="editData.montant" style="font-size:13px">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px"><?php echo e(__("super-admin.noest_delivery_mode")); ?></label>
                                <select class="form-control" wire:model="editData.type_id" style="font-size:13px">
                                    <option value="1"><?php echo e(__("super-admin.noest_type_delivery")); ?></option>
                                    <option value="2"><?php echo e(__("super-admin.noest_type_exchange")); ?></option>
                                    <option value="3"><?php echo e(__("super-admin.noest_type_retrait")); ?></option>
                                </select>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isShipped): ?>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px"><?php echo e(__("super-admin.noest_wilaya_commune")); ?></label>
                                <select class="form-control" wire:model="editData.commune" style="font-size:13px">
                                    <option value=""><?php echo e(__("super-admin.noest_select_commune")); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $communes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($c["commune"] ?? $c); ?>"><?php echo e($c["commune"] ?? $c); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px"><?php echo e(__("super-admin.noest_stop_desk")); ?></label>
                                <div class="d-flex align-items-center gap-2" style="margin-top:6px">
                                    <x-toggle-switch wire:model="editData.stop_desk" id="stopDeskToggle" :checked="$editData['stop_desk'] ?? false" description="<?php echo e(__("super-admin.noest_stop_desk_toggle")); ?>" />
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editData["stop_desk"] ?? false): ?>
                                <div style="margin-top:8px">
                                    <label style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:2px"><?php echo e(__("super-admin.noest_station_code")); ?></label>
                                    <input type="text" class="form-control" wire:model="editData.station_code" style="font-size:13px" placeholder="<?php echo e(__("super-admin.noest_select_station")); ?>">
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12px;font-weight:500;color:var(--text-muted);margin-bottom:4px"><?php echo e(__("super-admin.noest_payment")); ?></label>
                                <select class="form-control" wire:model="editData.remboursement" style="font-size:13px">
                                    <option value="0"><?php echo e(__("super-admin.noest_payment_cod")); ?></option>
                                    <option value="1"><?php echo e(__("super-admin.noest_payment_refund")); ?></option>
                                    <option value="2"><?php echo e(__("super-admin.noest_payment_cod_collect")); ?></option>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal"><?php echo e(__("general.cancel")); ?></button>
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none" wire:click="saveEdit" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveEdit"><?php echo e(__("general.save")); ?></span>
                        <span wire:loading wire:target="saveEdit"><span class="spinner-border spinner-border-sm" role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="validateOrderModal" tabindex="-1" data-bs-backdrop="static" x-data <?php echo $__env->yieldSection(); ?>-validate-modal.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-check2-circle me-2" style="color:var(--success)"></i><?php echo e(__("super-admin.noest_validate_title")); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--success);margin-bottom:12px"><i class="bi bi-question-circle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px"><?php echo e(__("super-admin.noest_validate_warning")); ?></p>
                    <p style="font-size:12px;color:var(--text-muted)"><?php echo e($validatingTracking ? __("super-admin.noest_validate_confirm",["tracking"=>$validatingTracking]) : ""); ?></p>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal"><?php echo e(__("general.cancel")); ?></button>
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--success);color:white;font-weight:600;border:none" wire:click="doValidate" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="doValidate"><?php echo e(__("super-admin.noest_validate")); ?></span>
                        <span wire:loading wire:target="doValidate"><span class="spinner-border spinner-border-sm" role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteOrderModal" tabindex="-1" data-bs-backdrop="static" x-data <?php echo $__env->yieldSection(); ?>-delete-modal.window="new bootstrap.Modal($el).show()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-trash me-2" style="color:var(--danger)"></i><?php echo e(__("super-admin.noest_delete_title")); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <div style="font-size:48px;color:var(--danger);margin-bottom:12px"><i class="bi bi-exclamation-triangle"></i></div>
                    <p style="font-size:14px;color:var(--text);margin-bottom:4px"><?php echo e(__("super-admin.noest_delete_warning")); ?></p>
                    <p style="font-size:12px;color:var(--text-muted)"><?php echo e($deletingTracking ? __("super-admin.noest_delete_confirm",["tracking"=>$deletingTracking]) : ""); ?></p>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px;justify-content:center">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal"><?php echo e(__("general.cancel")); ?></button>
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none" wire:click="doDelete" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="doDelete"><?php echo e(__("general.delete")); ?></span>
                        <span wire:loading wire:target="doDelete"><span class="spinner-border spinner-border-sm" role="status"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ show: false, type: 'success', message: '', timer: null }"
         x-on:noest-toast.window="type = $event.detail.type; message = $event.detail.message; show = true; clearTimeout(timer); timer = setTimeout(() => show = false, 4000)"
         x-show="show" x-transition.duration.300ms x-cloak
         style="position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;max-width:400px">
        <div :class="'alert alert-' + type" style="padding:12px 16px;border-radius:var(--radius-sm);box-shadow:0 8px 30px rgba(0,0,0,0.12);display:flex;align-items:center;gap:8px;margin-bottom:0" role="alert">
            <i :class="'bi bi-' + (type === 'success' ? 'check-circle-fill' : type === 'error' ? 'exclamation-circle-fill' : 'info-circle-fill')"></i>
            <span x-text="message" style="font-size:13px;flex:1"></span>
            <button type="button" class="btn-close" @click="show = false" style="font-size:10px"></button>
        </div>
    </div>
</div>

    <?php $__env->startPush("scripts"); ?>
    <script>
        function initNoestOrders() {
            if (!window._noestToastListener) {
                Livewire.on("noest-toast", (data) => {
                    window.dispatchEvent(new CustomEvent("noest-toast", { detail: data }));
                });
                window._noestToastListener = true;
            }
            if (!window._noestCsvListener) {
                Livewire.on("noest-download-csv", (data) => {
                    const a = document.createElement("a");
                    a.href = "data:text/csv;base64," + data.content;
                    a.download = data.filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                });
                window._noestCsvListener = true;
            }
        }
        initNoestOrders();

        function noestOrders() {
            return {
                selectedOrders: [],
                toggleSelectAll(checked) {
                    var el = this.$el.closest('[data-tracking-numbers]');
                    this.selectedOrders = checked && el ? JSON.parse(el.dataset.trackingNumbers) : [];
                },
                copyToClipboard(text) {
                    var el = this.$el.closest('[data-tracking-numbers]');
                    var msg = el ? el.dataset.copiedMessage : 'Copied';
                    navigator.clipboard.writeText(text).then(() => {
                        window.dispatchEvent(new CustomEvent("noest-toast", {
                            detail: { type: "success", message: msg }
                        }));
                    });
                }
            };
        }
    </script>
    <?php $__env->stopPush(); ?><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\pages\super-admin\noest-orders.blade.php ENDPATH**/ ?>