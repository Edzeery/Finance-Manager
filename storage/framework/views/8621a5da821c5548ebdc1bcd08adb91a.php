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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('profile.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('general.profile')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('profile.page_description')); ?> <?php $__env->endSlot(); ?>

    <?php
        $user = Auth::user();
        $initials = implode('', array_map(fn($n) => $n[0] ?? '', array_filter(explode(' ', $user->name))));
        $incomeCount = \App\Models\Income::where('user_id', $user->id)->count();
        $expenseCount = \App\Models\Expense::where('user_id', $user->id)->count();
        $goalCount = \App\Models\FinancialGoal::where('user_id', $user->id)->count();
        $debtCount = \App\Models\Debt::where('user_id', $user->id)->count();
        $assetCount = \App\Models\Asset::where('user_id', $user->id)->count();
        $budgetCount = \App\Models\Budget::where('user_id', $user->id)->count();
    ?>

    <div class="profile-grid">
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar">
                    <div class="avatar-circle"><?php echo e($initials); ?></div>
                    <div class="avatar-online"></div>
                </div>
                <h4 class="profile-name mt-4"><?php echo e($user->name); ?></h4>
                <p class="profile-email"><?php echo e($user->email); ?></p>
                <span class="profile-joined"><?php echo e(__('profile.member_since')); ?> <?php echo e($user->created_at->translatedFormat('F Y')); ?></span>
                <a href="<?php echo e(route('account.settings')); ?>" class="profile-settings-btn">
                    <i class="bi bi-gear me-1"></i><?php echo e(__('general.settings')); ?>

                </a>
            </div>

            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-link-45deg"></i>
                    <span><?php echo e(__('profile.quick_links')); ?></span>
                </div>
                <nav class="profile-nav">
                    <a href="<?php echo e(route('dashboard')); ?>" wire:navigate class="profile-nav-item">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span><?php echo e(__('general.dashboard')); ?></span>
                    </a>
                    <a href="<?php echo e(route('income.index')); ?>" wire:navigate class="profile-nav-item">
                        <i class="bi bi-cash-stack"></i>
                        <span><?php echo e(__('general.income')); ?></span>
                    </a>
                    <a href="<?php echo e(route('expense.index')); ?>" wire:navigate class="profile-nav-item">
                        <i class="bi bi-cart"></i>
                        <span><?php echo e(__('general.expense')); ?></span>
                    </a>
                    <a href="<?php echo e(route('report.index')); ?>" wire:navigate class="profile-nav-item">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i>
                        <span><?php echo e(__('general.report')); ?></span>
                    </a>
                    <a href="<?php echo e(route('settings.index')); ?>" wire:navigate class="profile-nav-item">
                        <i class="bi bi-gear-fill"></i>
                        <span><?php echo e(__('general.settings')); ?></span>
                    </a>
                </nav>
            </div>

            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-clock-history"></i>
                    <span><?php echo e(__('profile.recent_activity')); ?></span>
                    <a href="<?php echo e(route('activity.logs')); ?>" wire:navigate class="profile-card-link"><?php echo e(__('profile.view_all')); ?></a>
                </div>
                <div class="profile-activity">
                    <?php
                        $recentLogs = \App\Models\ActivityLog::where('user_id', $user->id)
                            ->latest()
                            ->take(5)
                            ->get();
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentLogs->count()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $actionIcon = match($log->action) {
                                    'created' => 'bi-plus-circle text-success',
                                    'updated' => 'bi-pencil text-info',
                                    'deleted' => 'bi-trash text-danger',
                                    'restored' => 'bi-arrow-counterclockwise text-warning',
                                    default => 'bi-circle text-muted',
                                };
                            ?>
                            <div class="profile-activity-item">
                                <i class="bi <?php echo e($actionIcon); ?>"></i>
                                <div>
                                    <p><?php echo e($log->description ?: __('general.unknown')); ?></p>
                                    <span><?php echo e($log->created_at->diffForHumans()); ?></span>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="profile-activity-empty">
                            <i class="bi bi-clock-history"></i>
                            <p><?php echo e(__('profile.no_activity')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="profile-main">
            <div class="profile-stats mb-4" role="list">
                <div class="stat-card stat-income" role="listitem">
                    <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                    <div class="stat-body">
                        <span class="stat-label"><?php echo e(__('profile.stats_income')); ?></span>
                        <span class="stat-value"><?php echo e($incomeCount); ?></span>
                    </div>
                </div>
                <div class="stat-card stat-expense" role="listitem">
                    <div class="stat-icon"><i class="bi bi-cart"></i></div>
                    <div class="stat-body">
                        <span class="stat-label"><?php echo e(__('profile.stats_expense')); ?></span>
                        <span class="stat-value"><?php echo e($expenseCount); ?></span>
                    </div>
                </div>
                <div class="stat-card stat-goal" role="listitem">
                    <div class="stat-icon"><i class="bi bi-flag"></i></div>
                    <div class="stat-body">
                        <span class="stat-label"><?php echo e(__('profile.stats_goals')); ?></span>
                        <span class="stat-value"><?php echo e($goalCount); ?></span>
                    </div>
                </div>
                <div class="stat-card stat-debt" role="listitem">
                    <div class="stat-icon"><i class="bi bi-credit-card-2-front"></i></div>
                    <div class="stat-body">
                        <span class="stat-label"><?php echo e(__('profile.stats_debts')); ?></span>
                        <span class="stat-value"><?php echo e($debtCount); ?></span>
                    </div>
                </div>
                <div class="stat-card stat-asset" role="listitem">
                    <div class="stat-icon"><i class="bi bi-pie-chart"></i></div>
                    <div class="stat-body">
                        <span class="stat-label"><?php echo e(__('profile.stats_assets')); ?></span>
                        <span class="stat-value"><?php echo e($assetCount); ?></span>
                    </div>
                </div>
                <div class="stat-card stat-budget" role="listitem">
                    <div class="stat-icon"><i class="bi bi-calculator"></i></div>
                    <div class="stat-body">
                        <span class="stat-label"><?php echo e(__('profile.stats_budgets')); ?></span>
                        <span class="stat-value"><?php echo e($budgetCount); ?></span>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                        <i class="bi bi-person" style="color:var(--accent);font-size:16px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('profile.account_info')); ?></h5>
                        <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('profile.account_info_help')); ?></p>
                    </div>
                </div>
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('profile.update-profile-information-form', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3627825604-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>

            <div class="text-center mt-4">
                <a href="<?php echo e(route('account.settings')); ?>" class="btn btn-accent btn-custom">
                    <i class="bi bi-gear me-1"></i><?php echo e(__('settings.preferences')); ?>

                </a>
            </div>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/profile.blade.php ENDPATH**/ ?>