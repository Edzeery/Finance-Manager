<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('settings.activity_log')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('settings.activity_log')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('settings.activity_log_desc') ?? __('general.recent_activity')); ?> <?php $__env->endSlot(); ?>

    <?php
        $subjectLabels = [
            'App\Models\Income' => __('income.title'),
            'App\Models\Expense' => __('expense.title'),
            'App\Models\Debt' => __('debt.title'),
            'App\Models\Asset' => __('asset.title'),
            'App\Models\Budget' => __('budget.title'),
            'App\Models\FinancialGoal' => __('goal.title'),
            'App\Models\ZakatRecord' => __('zakat.title'),
            'App\Models\IncomeCategory' => __('income.categories'),
            'App\Models\ExpenseCategory' => __('expense.categories'),
            'App\Models\User' => __('general.user'),
            'App\Models\Workspace' => __('settings.workspace'),
            'App\Models\Subscription' => __('settings.subscription'),
            'App\Models\Invoice' => __('invoice.title'),
            'App\Models\Payment' => __('payment.title'),
        ];
        $actionConfigs = [
            'created' => ['icon' => 'bi-plus-circle-fill', 'color' => 'var(--success)', 'bg' => 'var(--success-light)'],
            'updated' => ['icon' => 'bi-pencil-fill', 'color' => 'var(--info)', 'bg' => 'var(--info-light)'],
            'deleted' => ['icon' => 'bi-trash-fill', 'color' => 'var(--danger)', 'bg' => 'var(--danger-light)'],
            'restored' => ['icon' => 'bi-arrow-counterclockwise', 'color' => 'var(--warning)', 'bg' => 'var(--warning-light)'],
        ];
    ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <?php if (isset($component)) { $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-filter','data' => ['name' => 'action','options' => [
                'created' => __('general.activity_created'),
                'updated' => __('general.activity_updated'),
                'deleted' => __('general.activity_deleted'),
                'restored' => __('general.activity_restored'),
            ],'placeholder' => ''.e(__('general.all_actions')).'','minWidth' => '130px','onchange' => 'this.form.submit()','class' => 'form-custom','style' => 'padding:6px 12px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'action','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                'created' => __('general.activity_created'),
                'updated' => __('general.activity_updated'),
                'deleted' => __('general.activity_deleted'),
                'restored' => __('general.activity_restored'),
            ]),'placeholder' => ''.e(__('general.all_actions')).'','min-width' => '130px','onchange' => 'this.form.submit()','class' => 'form-custom','style' => 'padding:6px 12px']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $attributes = $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $component = $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['name' => 'search','value' => request('search'),'size' => 'sm','placeholder' => ''.e(__('general.search')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('search')),'size' => 'sm','placeholder' => ''.e(__('general.search')).'']); ?>
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

            <input type="date" name="date_from" class="form-custom" style="width:auto;padding:6px 12px;font-size:13px" value="<?php echo e(request('date_from')); ?>" onchange="this.form.submit()">
            <input type="date" name="date_to" class="form-custom" style="width:auto;padding:6px 12px;font-size:13px" value="<?php echo e(request('date_to')); ?>" onchange="this.form.submit()">

            <?php if (isset($component)) { $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clear-filters','data' => ['filters' => ['action','search','date_from','date_to']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clear-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['action','search','date_from','date_to'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113)): ?>
<?php $attributes = $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113; ?>
<?php unset($__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113)): ?>
<?php $component = $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113; ?>
<?php unset($__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113); ?>
<?php endif; ?>
        </form>

        <div class="d-flex gap-2 align-items-center">
            <?php if (isset($component)) { $__componentOriginal350cc130478c4b4aced77f6fd760100d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal350cc130478c4b4aced77f6fd760100d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page','data' => ['current' => request('per_page', 15),'preserve' => ['action','search','date_from','date_to']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('per_page', 15)),'preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['action','search','date_from','date_to'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal350cc130478c4b4aced77f6fd760100d)): ?>
<?php $attributes = $__attributesOriginal350cc130478c4b4aced77f6fd760100d; ?>
<?php unset($__attributesOriginal350cc130478c4b4aced77f6fd760100d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal350cc130478c4b4aced77f6fd760100d)): ?>
<?php $component = $__componentOriginal350cc130478c4b4aced77f6fd760100d; ?>
<?php unset($__componentOriginal350cc130478c4b4aced77f6fd760100d); ?>
<?php endif; ?>
        </div>
    </div>

    <div class="section-card">
        <div class="section-card-header">
            <h5><i class="bi bi-clock-history"></i><?php echo e(__('settings.activity_log')); ?></h5>
        </div>
        <div class="section-card-body p-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logs->count()): ?>
                <div class="activity-feed" style="padding:16px 24px">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $actionConfig = $actionConfigs[$log->action] ?? ['icon' => 'bi-circle-fill', 'color' => 'var(--text-muted)', 'bg' => 'var(--border)'];
                            $subjectLabel = $subjectLabels[$log->subject_type] ?? class_basename($log->subject_type);
                            $hasProps = $log->properties && (is_array($log->properties) || is_object($log->properties));
                            $logId = 'log-' . $log->id;
                        ?>
                        <div class="timeline-item" x-data="{ expanded: false }">
                            <div class="timeline-marker">
                                <div class="timeline-dot" style="background:<?php echo e($actionConfig['bg']); ?>;color:<?php echo e($actionConfig['color']); ?>">
                                    <i class="bi <?php echo e($actionConfig['icon']); ?>"></i>
                                </div>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                            <span class="badge" style="font-size:10px;background:<?php echo e($actionConfig['bg']); ?>;color:<?php echo e($actionConfig['color']); ?>;padding:2px 10px;border-radius:6px;font-weight:600"><?php echo e($log->action); ?></span>
                                            <span class="badge timeline-subject" style="font-size:9px;background:var(--bg-subtle);color:var(--text-muted);padding:2px 8px;border-radius:4px;font-weight:500"><?php echo e($subjectLabel); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->subject_id): ?>
                                                <span style="font-size:11px;color:var(--text-muted)">#<?php echo e($log->subject_id); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div style="font-size:13px;color:var(--text)"><?php echo e($log->description ?: '—'); ?></div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">
                                            <i class="bi bi-person"></i> <?php echo e($log->user?->name ?? __('general.deleted_user')); ?>

                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1" style="flex-shrink:0">
                                        <span class="timeline-meta"><?php echo e($log->created_at->diffForHumans()); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasProps): ?>
                                            <button type="button" @click="expanded = !expanded" class="btn" style="padding:2px 6px;font-size:10px;border-radius:4px;border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer;line-height:1">
                                                <i class="bi" :class="expanded ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasProps): ?>
                                    <div x-show="expanded" x-collapse>
                                        <pre style="font-size:11px;color:var(--text-muted);background:var(--bg-subtle);padding:8px 12px;border-radius:6px;margin-top:6px;overflow-x:auto;max-width:100%"><?php echo e(json_encode($log->properties, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)); ?></pre>
                                    </div>
                                    <div x-show="!expanded">
                                        <?php
                                            $propsStr = json_encode($log->properties, JSON_UNESCAPED_UNICODE);
                                        ?>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;cursor:pointer" @click="expanded = true" title="<?php echo e(__('general.click_to_expand')); ?>">
                                            <?php echo e(\Illuminate\Support\Str::limit($propsStr, 80)); ?>

                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-3" style="border-top:1px solid var(--border-light)">
                    <?php if (isset($component)) { $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination-info','data' => ['items' => $logs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination-info'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($logs)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105)): ?>
<?php $attributes = $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105; ?>
<?php unset($__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105)): ?>
<?php $component = $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105; ?>
<?php unset($__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105); ?>
<?php endif; ?>
                    <div>
                        <?php echo e($logs->appends(request()->except('page'))->links()); ?>

                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-clock-history"></i></div>
                    <h4><?php echo e(__('general.no_data')); ?></h4>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\activity_logs\index.blade.php ENDPATH**/ ?>