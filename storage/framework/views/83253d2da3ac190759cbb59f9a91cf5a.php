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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('dashboard.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('dashboard.title')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__("filters.{$period}")); ?>: <strong><?php echo e(config('finance.currency_symbol')); ?> <?php echo e(number_format($kpi->netBalance, 2)); ?></strong> <?php $__env->endSlot(); ?>

    <?php if (isset($component)) { $__componentOriginal45820a29a8741c05f6b6338dfa1de322 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45820a29a8741c05f6b6338dfa1de322 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.date-filter-bar','data' => ['periods' => $periods,'currentPeriod' => $period,'startDate' => $startDate,'endDate' => $endDate,'preserve' => []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('date-filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['periods' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($periods),'currentPeriod' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($period),'startDate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($startDate),'endDate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($endDate),'preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45820a29a8741c05f6b6338dfa1de322)): ?>
<?php $attributes = $__attributesOriginal45820a29a8741c05f6b6338dfa1de322; ?>
<?php unset($__attributesOriginal45820a29a8741c05f6b6338dfa1de322); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45820a29a8741c05f6b6338dfa1de322)): ?>
<?php $component = $__componentOriginal45820a29a8741c05f6b6338dfa1de322; ?>
<?php unset($__componentOriginal45820a29a8741c05f6b6338dfa1de322); ?>
<?php endif; ?>

    <div class="row g-3 mb-4 animate-fade-in">
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(34,197,94,0.12); color: var(--success)">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="kpi-label">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($period === 'all_time'): ?>
                        <?php echo e(__('dashboard.total_income')); ?>

                    <?php elseif($period === 'this_month'): ?>
                        <?php echo e(__('dashboard.all_time')); ?> — <?php echo e(__('dashboard.total_income')); ?>

                    <?php else: ?>
                        <?php echo e(__('filters.filtered_by')); ?>: <?php echo e(__("filters.{$period}")); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="kpi-value"><?php echo e(number_format($kpi->totalIncome, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></div>
                <div class="kpi-trend up">
                    <i class="bi bi-calendar3"></i>
                    <?php echo e(__('dashboard.all_time')); ?>: <?php echo e(number_format($kpi->totalIncomeAllTime, 2)); ?>

                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(239,68,68,0.12); color: var(--danger)">
                    <i class="bi bi-cart"></i>
                </div>
                <div class="kpi-label"><?php echo e(__('dashboard.total_expense')); ?></div>
                <div class="kpi-value"><?php echo e(number_format($kpi->totalExpense, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></div>
                <div class="kpi-trend <?php echo e($kpi->expenseChange <= 0 ? 'up' : 'down'); ?>">
                    <i class="bi <?php echo e($kpi->expenseChange <= 0 ? 'bi-arrow-down' : 'bi-arrow-up'); ?>"></i>
                    <?php echo e(__('dashboard.all_time')); ?>: <?php echo e(number_format($kpi->totalExpenseAllTime, 2)); ?>

                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(59,130,246,0.12); color: var(--info)">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="kpi-label"><?php echo e(__('dashboard.net_balance')); ?> (<?php echo e(__("filters.{$period}")); ?>)</div>
                <div class="kpi-value <?php echo e($kpi->netBalance >= 0 ? '' : 'text-danger'); ?>"><?php echo e(number_format($kpi->netBalance, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></div>
                <div class="kpi-trend <?php echo e($kpi->netBalance >= 0 ? 'up' : 'down'); ?>">
                    <i class="bi <?php echo e($kpi->netBalance >= 0 ? 'bi-arrow-up' : 'bi-arrow-down'); ?>"></i>
                    <?php echo e(__('dashboard.total_savings')); ?>: <?php echo e(number_format($kpi->totalSavings, 2)); ?>

                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(139,92,246,0.12); color:#8B5CF6">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <div class="kpi-label"><?php echo e(__('dashboard.total_savings')); ?> (<?php echo e(__('dashboard.all_time')); ?>)</div>
                <div class="kpi-value <?php echo e($kpi->totalSavings >= 0 ? '' : 'text-danger'); ?>">
                    <?php echo e(number_format($kpi->totalSavings, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?>

                </div>
                <div class="kpi-trend up">
                    <i class="bi bi-clock-history"></i>
                    <?php echo e(__('filters.all_time')); ?>

                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(34,197,94,0.12); color: var(--success)">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
                <div class="kpi-label"><?php echo e(__('dashboard.total_assets')); ?> (<?php echo e(__('dashboard.all_time')); ?>)</div>
                <div class="kpi-value"><?php echo e(number_format($kpi->totalAssets, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></div>
                <div class="kpi-trend up"><?php echo e(__('dashboard.all_time')); ?></div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(245,158,11,0.12); color: var(--warning)">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                <div class="kpi-label"><?php echo e(__('dashboard.total_debts')); ?> (<?php echo e(__('dashboard.all_time')); ?>)</div>
                <div class="kpi-value"><?php echo e(number_format($kpi->totalDebts, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></div>
                <div class="kpi-trend down">
                    <i class="bi bi-exclamation-circle"></i>
                    <?php echo e($kpi->overdueDebts); ?> <?php echo e(__('dashboard.overdue_debts')); ?>

                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-bar-chart-fill" style="color:var(--success)"></i>
                        <span><?php echo e(__('dashboard.income_vs_expenses')); ?></span>
                    </h5>
                    <span style="font-size:12px;color:var(--text-muted)"><?php echo e(__('dashboard.monthly_summary')); ?></span>
                </div>
                <div style="min-height:300px" id="incomeExpenseContainer">
                    <canvas id="incomeExpenseChart" height="280"
                        data-labels='<?php echo json_encode($incomeExpense['labels'] ?? [], 15, 512) ?>'
                        data-income='<?php echo json_encode($incomeExpense['incomeData'] ?? [], 15, 512) ?>'
                        data-expense='<?php echo json_encode($incomeExpense['expenseData'] ?? [], 15, 512) ?>'></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-pie-chart-fill" style="color:var(--danger)"></i>
                        <span><?php echo e(__('dashboard.expense_categories')); ?></span>
                    </h5>
                    <span style="font-size:12px;color:var(--text-muted)"><?php echo e(__('dashboard.this_month')); ?></span>
                </div>
                <div style="min-height:300px" id="expenseCategoriesContainer">
                    <canvas id="expenseCategoriesChart" height="280"
                        data-labels='<?php echo json_encode($expenseCategories['labels'] ?? [], 15, 512) ?>'
                        data-values='<?php echo json_encode($expenseCategories['data'] ?? [], 15, 512) ?>'
                        data-colors='<?php echo json_encode($expenseCategories['colors'] ?? [], 15, 512) ?>'></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up-arrow" style="color:var(--info)"></i>
                        <span><?php echo e(__('dashboard.monthly_cash_flow')); ?></span>
                    </h5>
                    <span style="font-size:12px;color:var(--text-muted)"><?php echo e(__('dashboard.all_time')); ?></span>
                </div>
                <div style="min-height:300px" id="cashFlowContainer">
                    <canvas id="cashFlowChart" height="280"
                        data-labels='<?php echo json_encode($incomeExpense['labels'] ?? [], 15, 512) ?>'
                        data-income='<?php echo json_encode($incomeExpense['incomeData'] ?? [], 15, 512) ?>'
                        data-expense='<?php echo json_encode($incomeExpense['expenseData'] ?? [], 15, 512) ?>'></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-currency-exchange" style="color:var(--accent)"></i>
                        <span><?php echo e(__('dashboard.financial_growth')); ?></span>
                    </h5>
                    <span style="font-size:12px;color:var(--text-muted)">6 <?php echo e(__('general.monthly')); ?></span>
                </div>
                <div style="min-height:300px" id="growthContainer">
                    <canvas id="financialGrowthChart" height="280"
                        data-labels='<?php echo json_encode($growth['labels'] ?? [], 15, 512) ?>'
                        data-values='<?php echo json_encode($growth['data'] ?? [], 15, 512) ?>'></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($goals->isNotEmpty()): ?>
        <div class="col-xl-4 col-12">
            <div class="dashboard-chart-card h-100">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-trophy" style="color:var(--accent)"></i>
                        <span><?php echo e(__('dashboard.goals_progress')); ?></span>
                    </h5>
                    <a href="<?php echo e(route('goal.index')); ?>" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none"><?php echo e(__('dashboard.view_all')); ?></a>
                </div>
                <div style="padding:20px">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $goals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $goal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:13px; font-weight:500"><?php echo e($goal->{'name_' . app()->getLocale()}); ?></span>
                            <span style="font-size:12px; color:var(--text-muted)"><?php echo e($goal->progress); ?>%</span>
                        </div>
                        <div class="progress" style="height:6px; border-radius:3px; background:var(--border)">
                            <div class="progress-bar" role="progressbar" style="width:<?php echo e($goal->progress); ?>%; border-radius:3px; background:<?php echo e($goal->color ?: 'var(--accent)'); ?>" aria-valuenow="<?php echo e($goal->progress); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:2px">
                            <?php echo e(number_format($goal->current_amount, 0)); ?> / <?php echo e(number_format($goal->target_amount, 0)); ?> <?php echo e(config('finance.currency_symbol')); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($goal->days_remaining !== null): ?>
                                · <?php echo e($goal->days_remaining); ?> <?php echo e(__('general.days_left')); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($budgetAlerts->isNotEmpty()): ?>
        <div class="col-xl-4 col-12">
            <div class="dashboard-chart-card h-100">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle" style="color:var(--warning)"></i>
                        <span><?php echo e(__('dashboard.budget_alerts')); ?></span>
                    </h5>
                    <a href="<?php echo e(route('budget.index')); ?>" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none"><?php echo e(__('dashboard.view_all')); ?></a>
                </div>
                <div style="padding:20px">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $budgetAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="flex-shrink-0" style="width:36px; height:36px; border-radius:8px; background:rgba(245,158,11,0.12); display:flex; align-items:center; justify-content:center; color:var(--warning)">
                            <i class="bi bi-cash"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width:0">
                            <div style="font-size:13px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis"><?php echo e($budget->{'name_' . app()->getLocale()}); ?></div>
                            <div style="font-size:11px; color:var(--text-muted)">
                                <?php echo e(number_format($budget->totalSpent, 0)); ?> / <?php echo e(number_format($budget->total_amount, 0)); ?> <?php echo e(config('finance.currency_symbol')); ?>

                            </div>
                            <div class="progress" style="height:4px; border-radius:2px; background:var(--border); margin-top:4px">
                                <div class="progress-bar bg-warning" role="progressbar" style="width:<?php echo e(min(100, $budget->adherence_rate)); ?>%; border-radius:2px" aria-valuenow="<?php echo e($budget->adherence_rate); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <span style="font-size:12px; font-weight:600; color:var(--warning)">+<?php echo e(round($budget->adherence_rate - 100)); ?>%</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debtReminders->isNotEmpty()): ?>
        <div class="col-xl-4 col-12">
            <div class="dashboard-chart-card h-100">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-bell" style="color:var(--info)"></i>
                        <span><?php echo e(__('dashboard.debt_reminders')); ?></span>
                    </h5>
                    <a href="<?php echo e(route('debt.index')); ?>" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none"><?php echo e(__('dashboard.view_all')); ?></a>
                </div>
                <div style="padding:20px">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $debtReminders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $daysLeft = now()->startOfDay()->diffInDays($debt->due_date, false);
                    ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="flex-shrink-0" style="width:36px; height:36px; border-radius:8px; background:<?php echo e($daysLeft <= 0 ? 'rgba(239,68,68,0.12)' : 'rgba(59,130,246,0.12)'); ?>; display:flex; align-items:center; justify-content:center; color:<?php echo e($daysLeft <= 0 ? 'var(--danger)' : 'var(--info)'); ?>">
                            <i class="bi bi-<?php echo e($debt->type === \App\Enums\DebtType::Owed ? 'person-up' : 'person-down'); ?>"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width:0">
                            <div style="font-size:13px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis"><?php echo e($debt->counterparty_name); ?></div>
                            <div style="font-size:11px; color:var(--text-muted)">
                                <?php echo e(number_format($debt->remaining_amount, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?>

                                · <?php echo e($debt->type === \App\Enums\DebtType::Owed ? __('general.you_owe') : __('general.owed_to_you')); ?>

                            </div>
                        </div>
                        <span style="font-size:12px; font-weight:500; white-space:nowrap; color:<?php echo e($daysLeft <= 0 ? 'var(--danger)' : ($daysLeft <= 7 ? 'var(--warning)' : 'var(--text-muted)')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($daysLeft <= 0): ?>
                                <?php echo e(abs($daysLeft)); ?>d overdue
                            <?php else: ?>
                                <?php echo e($daysLeft); ?>d
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history" style="color:var(--accent)"></i>
                        <span><?php echo e(__('dashboard.recent_transactions')); ?></span>
                    </h5>
                    <a href="<?php echo e(route('transactions.index')); ?>" wire:navigate style="font-size:13px;color:var(--accent);text-decoration:none"><?php echo e(__('dashboard.view_all')); ?></a>
                </div>
                <div style="padding:0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <thead>
                            <tr>
                                <th><?php echo e(__('general.date')); ?></th>
                                <th><?php echo e(__('general.description')); ?></th>
                                <th><?php echo e(__('general.category')); ?></th>
                                <th class="text-end"><?php echo e(__('general.amount')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td style="white-space:nowrap"><?php echo e($txn['date']->format('Y/m/d')); ?></td>
                                    <td><?php echo e($txn['description'] ?: '—'); ?></td>
                                    <td><?php echo e($txn['category']); ?></td>
                                    <td text-start fw-bold style="color:<?php echo e($txn['type'] === 'income' ? 'var(--success)' : 'var(--danger)'); ?>">
                                        <?php echo e($txn['type'] === 'income' ? '+' : '-'); ?><?php echo e(number_format($txn['amount'], 2)); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size:24px; display:block; margin-bottom:8px"></i>
                                        <?php echo e(__('general.no_data')); ?>

                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    var chartBaseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                align: 'end',
                labels: { padding: 16, usePointStyle: true, pointStyle: 'circle', font: { size: 12, weight: '500' }, color: '#94A3B8' }
            },
            tooltip: {
                backgroundColor: 'rgba(15,23,42,0.94)',
                titleColor: '#F8FAFC',
                bodyColor: '#CBD5E1',
                padding: 12,
                cornerRadius: 10,
                titleFont: { size: 13, weight: '600' },
                bodyFont: { size: 12 },
                boxPadding: 6,
                usePointStyle: true,
                callbacks: {
                    label: function(ctx) { return ctx.dataset.label + ': ' + formatCurrency(ctx.parsed.y); }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(148,163,184,0.08)', drawBorder: false },
                ticks: {
                    font: { size: 11 },
                    color: '#64748B',
                    padding: 8,
                    callback: function(v) {
                        if (v >= 1e6) return (v / 1e6).toFixed(1) + 'M';
                        if (v >= 1e3) return (v / 1e3).toFixed(0) + 'K';
                        return v.toLocaleString();
                    }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 }, color: '#64748B', maxRotation: 30 }
            }
        },
        interaction: { intersect: false, mode: 'index' }
    };

    function getChartData(id) {
        var el = document.getElementById(id);
        if (!el) return null;
        var data = {};
        ['labels', 'income', 'expense', 'values', 'colors'].forEach(function(key) {
            if (el.dataset[key]) {
                try { data[key] = JSON.parse(el.dataset[key]); } catch(e) { data[key] = []; }
            }
        });
        return data;
    }

    function initDashboardCharts() {
        if (!document.getElementById('incomeExpenseChart')) return;
        try {
            destroyExistingCharts();

            var ieData = getChartData('incomeExpenseChart');
            var incomeExpenseLabels = ieData.labels || [];
            var incomeData = ieData.income || [];
            var expenseData = ieData.expense || [];

            var expCatData = getChartData('expenseCategoriesChart');
            var expenseCatLabels = expCatData.labels || [];
            var expenseCatValues = expCatData.values || [];
            var expenseCatColors = expCatData.colors || [];

            var growthChartData = getChartData('financialGrowthChart');
            var growthLabels = growthChartData.labels || [];
            var growthValues = growthChartData.values || [];

            var cssVar = function(name) { return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || '#64748B'; };
            var success = cssVar('--success') || '#22C55E';
            var danger = cssVar('--danger') || '#EF4444';

            if (incomeExpenseLabels.length) {
                new Chart(document.getElementById('incomeExpenseChart'), {
                    type: 'bar',
                    data: {
                        labels: incomeExpenseLabels,
                        datasets: [{
                            label: '<?php echo e(__("dashboard.total_income")); ?>',
                            data: incomeData,
                            backgroundColor: success + 'E6',
                            borderColor: success,
                            borderWidth: { top: 0, right: 0, bottom: 0, left: 0 },
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.5,
                            categoryPercentage: 0.7
                        }, {
                            label: '<?php echo e(__("dashboard.total_expense")); ?>',
                            data: expenseData,
                            backgroundColor: danger + 'E6',
                            borderColor: danger,
                            borderWidth: { top: 0, right: 0, bottom: 0, left: 0 },
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.5,
                            categoryPercentage: 0.7
                        }]
                    },
                    options: {...chartBaseOptions}
                });
            } else {
                showEmptyChart('incomeExpenseContainer', '<?php echo e(__("dashboard.no_chart_data")); ?>');
            }

            if (expenseCatLabels.length) {
                new Chart(document.getElementById('expenseCategoriesChart'), {
                    type: 'doughnut',
                    data: {
                        labels: expenseCatLabels,
                        datasets: [{
                            data: expenseCatValues,
                            backgroundColor: expenseCatColors,
                            borderWidth: 2,
                            borderColor: 'var(--card-bg)',
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        ...chartBaseOptions,
                        cutout: '72%',
                        plugins: {
                            ...chartBaseOptions.plugins,
                            legend: { ...chartBaseOptions.plugins.legend, position: 'bottom' }
                        }
                    }
                });
            } else {
                showEmptyChart('expenseCategoriesContainer', '<?php echo e(__("dashboard.no_chart_data")); ?>');
            }

            var cfData = getChartData('cashFlowChart');
            if ((cfData.labels || []).length) {
                new Chart(document.getElementById('cashFlowChart'), {
                    type: 'line',
                    data: {
                        labels: cfData.labels || incomeExpenseLabels,
                        datasets: [{
                            label: '<?php echo e(__("dashboard.total_income")); ?>',
                            data: cfData.income || incomeData,
                            borderColor: success,
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: success,
                            pointHoverBorderWidth: 3,
                            borderWidth: 2.5,
                            borderCapStyle: 'round',
                            borderJoinStyle: 'round'
                        }, {
                            label: '<?php echo e(__("dashboard.total_expense")); ?>',
                            data: cfData.expense || expenseData,
                            borderColor: danger,
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: danger,
                            pointHoverBorderWidth: 3,
                            borderWidth: 2.5,
                            borderCapStyle: 'round',
                            borderJoinStyle: 'round'
                        }]
                    },
                    options: {...chartBaseOptions}
                });
            } else {
                showEmptyChart('cashFlowContainer', '<?php echo e(__("dashboard.no_chart_data")); ?>');
            }

            if (growthLabels.length) {
                var lastVal = growthValues[growthValues.length - 1];
                var growthColor = parseFloat(lastVal) >= 0 ? success : danger;

                new Chart(document.getElementById('financialGrowthChart'), {
                    type: 'line',
                    data: {
                        labels: growthLabels,
                        datasets: [{
                            label: '<?php echo e(__("dashboard.net_balance")); ?>',
                            data: growthValues,
                            borderColor: growthColor,
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: growthColor,
                            pointHoverBorderWidth: 3,
                            borderWidth: 3,
                            borderCapStyle: 'round',
                            borderJoinStyle: 'round'
                        }]
                    },
                    options: {
                        ...chartBaseOptions,
                        scales: {
                            ...chartBaseOptions.scales,
                            y: { ...chartBaseOptions.scales.y, beginAtZero: false }
                        }
                    }
                });
            } else {
                showEmptyChart('growthContainer', '<?php echo e(__("dashboard.no_chart_data")); ?>');
            }
        } catch (e) {
            console.warn('Chart init error:', e);
        }
    }

    if (!window._dashboardNavListener) {
        document.addEventListener('livewire:navigated', initDashboardCharts);
        window._dashboardNavListener = true;
    }
    initDashboardCharts();
    </script>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/dashboard/index.blade.php ENDPATH**/ ?>