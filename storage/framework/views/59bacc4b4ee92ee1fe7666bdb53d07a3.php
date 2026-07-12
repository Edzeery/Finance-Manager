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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('general.account')); ?> <?php echo e(__('general.settings')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('general.account')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('profile.page_description')); ?> <?php $__env->endSlot(); ?>

    <?php
        $recentNotifications = \App\Models\Notification::where('user_id', $user->id)
            ->latest()->take(10)->get();
        $unreadCount = \App\Models\Notification::where('user_id', $user->id)
            ->where('is_read', false)->count();
    ?>

    <div class="profile-grid" x-data="{ tab: 'profile' }">
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar">
                    <div class="avatar-circle"><?php echo e($initials); ?></div>
                </div>
                <h4 class="profile-name mt-3"><?php echo e($user->name); ?></h4>
                <p class="profile-email"><?php echo e($user->email); ?></p>
                <nav class="profile-nav mt-3">
                    <button @click="tab = 'profile'" :class="{ 'active': tab === 'profile' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-person"></i>
                        <span><?php echo e(__('profile.tab_profile_info')); ?></span>
                    </button>
                    <button @click="tab = 'preferences'" :class="{ 'active': tab === 'preferences' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-sliders2"></i>
                        <span><?php echo e(__('settings.preferences')); ?></span>
                    </button>
                    <button @click="tab = 'security'" :class="{ 'active': tab === 'security' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-shield-lock"></i>
                        <span><?php echo e(__('settings.security')); ?></span>
                    </button>
                    <button @click="tab = 'notifications'" :class="{ 'active': tab === 'notifications' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-bell"></i>
                        <span><?php echo e(__('profile.notifications')); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                            <span class="badge bg-danger ms-auto" style="font-size:10px;"><?php echo e($unreadCount); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>
                    <a href="<?php echo e(route('account.settings.developer')); ?>" wire:navigate class="profile-nav-item">
                        <i class="bi bi-code-slash"></i>
                        <span><?php echo e(__('developer.api_tokens')); ?></span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="profile-main">
            
            <div x-show="tab === 'profile'" x-transition:enter.duration.200ms>
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

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-76513711-0', $__key);

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
            </div>

            
            <div x-show="tab === 'preferences'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-sliders2" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('settings.preferences')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('settings.preferences_desc')); ?></p>
                        </div>
                    </div>

                    <form method="POST" action="<?php echo e(route('account.settings.update')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('settings.language')); ?></label>
                                <select name="language" class="form-custom">
                                    <option value="ar" <?php echo e($user->locale === 'ar' ? 'selected' : ''); ?>><?php echo e(__('general.ar')); ?></option>
                                    <option value="fr" <?php echo e($user->locale === 'fr' ? 'selected' : ''); ?>><?php echo e(__('general.fr')); ?></option>
                                    <option value="en" <?php echo e($user->locale === 'en' ? 'selected' : ''); ?>><?php echo e(__('general.en')); ?></option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('settings.currency')); ?></label>
                                <select name="currency" class="form-custom">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Helpers\CurrencyHelper::availableCurrencies() ?: [['code' => 'DZD', 'name' => 'Algerian Dinar'], ['code' => 'USD', 'name' => 'US Dollar'], ['code' => 'EUR', 'name' => 'Euro']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cur['code']); ?>" <?php echo e($user->currency === $cur['code'] ? 'selected' : ''); ?>><?php echo e($cur['name']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('settings.timezone')); ?></label>
                                <select name="timezone" class="form-custom">
                                    <?php
                                        $timezones = [
                                            'Africa/Algiers' => 'Africa/Algiers (UTC+1)',
                                            'Africa/Cairo' => 'Africa/Cairo (UTC+2)',
                                            'Asia/Dubai' => 'Asia/Dubai (UTC+4)',
                                            'Europe/Paris' => 'Europe/Paris (UTC+1)',
                                            'Europe/London' => 'Europe/London (UTC+0)',
                                            'America/New_York' => 'America/New_York (UTC-5)',
                                            'America/Chicago' => 'America/Chicago (UTC-6)',
                                            'America/Denver' => 'America/Denver (UTC-7)',
                                            'America/Los_Angeles' => 'America/Los_Angeles (UTC-8)',
                                        ];
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $timezones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>" <?php echo e($user->timezone === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('settings.theme')); ?></label>
                                <div class="d-flex gap-2">
                                    <button type="button" @click="$store.theme.set('light')" class="btn <?php echo e(session('theme', 'light') === 'light' ? 'btn-accent' : 'btn-outline-secondary'); ?> btn-custom flex-fill">
                                        <i class="bi bi-sun-fill me-1"></i><?php echo e(__('settings.light')); ?>

                                    </button>
                                    <button type="button" @click="$store.theme.set('dark')" class="btn <?php echo e(session('theme', 'light') === 'dark' ? 'btn-accent' : 'btn-outline-secondary'); ?> btn-custom flex-fill">
                                        <i class="bi bi-moon-fill me-1"></i><?php echo e(__('settings.dark')); ?>

                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-accent btn-custom">
                            <i class="bi bi-check-lg me-1"></i><?php echo e(__('general.save')); ?>

                        </button>
                    </form>
                </div>
            </div>

            
            <div x-show="tab === 'security'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-shield-lock" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('general.update_password')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('profile.password_help')); ?></p>
                        </div>
                    </div>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('profile.update-password-form', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-76513711-1', $__key);

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

                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-shield-check" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('settings.two_factor')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('messages.add_2fa_security')); ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size:14px;font-weight:500;"><?php echo e(__('general.status')); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->hasTwoFactorEnabled()): ?>
                                <span class="badge-success"><?php echo e(__('general.enabled')); ?></span>
                            <?php else: ?>
                                <span class="badge-muted"><?php echo e(__('general.disabled')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <a href="<?php echo e(route('two-factor.setup')); ?>" class="btn btn-accent btn-sm">
                            <i class="bi bi-shield-plus me-1"></i>
                            <span><?php echo e(__('general.manage')); ?></span>
                        </a>
                    </div>
                </div>

                <div class="settings-card" style="border-color:rgba(239,68,68,0.2);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(239,68,68,0.1);flex-shrink:0;">
                            <i class="bi bi-exclamation-triangle" style="color:var(--danger);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;color:var(--danger);"><?php echo e(__('general.danger_zone')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('profile.delete_account_help')); ?></p>
                        </div>
                    </div>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('profile.delete-user-form', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-76513711-2', $__key);

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
            </div>

            
            <div x-show="tab === 'notifications'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-bell" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('profile.notifications')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('profile.notifications_help')); ?></p>
                        </div>
                    </div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentNotifications->count()): ?>
    <div class="notifications-list">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $notifIcon = match($notification->type) {
                    'budget_exceeded', 'budget_nearing_limit' => 'bi-exclamation-triangle text-danger',
                    'debt_reminder' => 'bi-credit-card-2-front text-warning',
                    'goal_achieved', 'goal_milestone' => 'bi-flag text-success',
                    'goal_deadline' => 'bi-clock text-info',
                    'zakat_reminder' => 'bi-heart text-primary',
                    'role_changed' => 'bi-shield-check text-warning',
                    default => 'bi-info-circle text-info',
                };
            ?>
            <div class="notification-item <?php echo e($notification->is_read ? '' : 'notification-unread'); ?>">
                <div class="notification-icon">
                    <i class="bi <?php echo e($notifIcon); ?>"></i>
                </div>
                <div class="notification-body">
                    <p class="notification-title"><?php echo e($notification->{'title_' . app()->getLocale()}); ?></p>
                    <p class="notification-message"><?php echo e($notification->{'message_' . app()->getLocale()}); ?></p>
                    <span class="notification-time"><?php echo e($notification->created_at->diffForHumans()); ?></span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$notification->is_read): ?>
                    <span class="notification-dot"></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-bell-slash" style="font-size:2rem;color:var(--text-muted);"></i>
                            <p class="text-muted mt-2 mb-0"><?php echo e(__('profile.no_notifications')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\account\settings.blade.php ENDPATH**/ ?>