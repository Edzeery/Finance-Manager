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
        $recentNotifications = \App\Models\Notification::where('user_id', $user->id)->latest()->take(10)->get();
        $unreadCount = \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count();
    ?>

    <div class="profile-grid" x-data="{ tab: 'profile' }">
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar mb-3">
                    <div class="avatar-circle"><?php echo e($initials); ?></div>
                    <div class="avatar-online"></div>
                </div>
                <h4 class="profile-name mt-3"><?php echo e($user->name); ?></h4>
                <p class="profile-email"><?php echo e($user->email); ?></p>
                <span class="profile-joined"><?php echo e(__('profile.member_since')); ?> <?php echo e($user->created_at->translatedFormat('F Y')); ?></span>
                <nav class="profile-nav mt-3">
                    <button @click="tab = 'profile'" :class="{ 'active': tab === 'profile' }" class="profile-tab-btn">
                        <i class="bi bi-person"></i>
                        <span><?php echo e(__('profile.tab_profile_info')); ?></span>
                    </button>
                    <button @click="tab = 'preferences'" :class="{ 'active': tab === 'preferences' }" class="profile-tab-btn">
                        <i class="bi bi-sliders2"></i>
                        <span><?php echo e(__('settings.preferences')); ?></span>
                    </button>
                    <button @click="tab = 'security'" :class="{ 'active': tab === 'security' }" class="profile-tab-btn">
                        <i class="bi bi-shield-lock"></i>
                        <span><?php echo e(__('settings.security')); ?></span>
                    </button>
                    <button @click="tab = 'notifications'" :class="{ 'active': tab === 'notifications' }" class="profile-tab-btn">
                        <i class="bi bi-bell"></i>
                        <span><?php echo e(__('profile.notifications')); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'pending','set' => 'bi','format' => 'dot','size' => 'xs','class' => 'ms-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'pending','set' => 'bi','format' => 'dot','size' => 'xs','class' => 'ms-auto']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>
                    <a href="<?php echo e(route('account.settings.developer')); ?>" wire:navigate class="profile-tab-btn">
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
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-person" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('profile.account_info')); ?>

                            </h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                <?php echo e(__('profile.account_info_help')); ?></p>
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
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-sliders2" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('settings.preferences')); ?>

                            </h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                <?php echo e(__('settings.preferences_desc')); ?></p>
                        </div>
                    </div>

                    <form method="POST" action="<?php echo e(route('account.settings.update')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('settings.language')); ?></label>
                                <select name="language" class="form-custom">
                                    <option value="ar" <?php echo e($user->locale === 'ar' ? 'selected' : ''); ?>>
                                        <?php echo e(__('general.ar')); ?></option>
                                    <option value="fr" <?php echo e($user->locale === 'fr' ? 'selected' : ''); ?>>
                                        <?php echo e(__('general.fr')); ?></option>
                                    <option value="en" <?php echo e($user->locale === 'en' ? 'selected' : ''); ?>>
                                        <?php echo e(__('general.en')); ?></option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('settings.currency')); ?></label>
                                <select name="currency" class="form-custom">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Helpers\CurrencyHelper::availableCurrencies() ?: [['code' => 'DZD', 'name' => 'Algerian Dinar'], ['code' => 'USD', 'name' => 'US Dollar'], ['code' => 'EUR', 'name' => 'Euro']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cur['code']); ?>"
                                            <?php echo e($user->currency === $cur['code'] ? 'selected' : ''); ?>>
                                            <?php echo e($cur['name']); ?></option>
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
                                        <option value="<?php echo e($value); ?>"
                                            <?php echo e($user->timezone === $value ? 'selected' : ''); ?>><?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('settings.theme')); ?></label>
                                <div class="d-flex gap-2">
                                    <button type="button" @click="$store.theme.set('light')"
                                        class="btn <?php echo e(session('theme', 'light') === 'light' ? 'btn-accent' : 'btn-outline-secondary'); ?> btn-custom flex-fill">
                                        <i class="bi bi-sun-fill me-1"></i><?php echo e(__('settings.light')); ?>

                                    </button>
                                    <button type="button" @click="$store.theme.set('dark')"
                                        class="btn <?php echo e(session('theme', 'light') === 'dark' ? 'btn-accent' : 'btn-outline-secondary'); ?> btn-custom flex-fill">
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
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-shield-lock" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                                <?php echo e(__('general.update_password')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                <?php echo e(__('profile.password_help')); ?></p>
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
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-shield-check" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                                <?php echo e(__('settings.two_factor')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                <?php echo e(__('messages.add_2fa_security')); ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size:14px;font-weight:500;"><?php echo e(__('general.status')); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->hasTwoFactorEnabled()): ?>
                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'yes','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'yes','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                            <?php else: ?>
                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'no','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'no','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <a href="<?php echo e(route('two-factor.setup')); ?>" class="btn btn-accent btn-sm">
                            <i class="bi bi-shield-plus me-1"></i>
                            <span><?php echo e(__('general.manage')); ?></span>
                        </a>
                    </div>
                </div>

                
                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width:36px;height:36px;background:rgba(59,130,246,0.1);flex-shrink:0;">
                                <i class="bi bi-pc-display" style="color:#3b82f6;font-size:16px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                                    <?php echo e(__('settings.active_sessions')); ?></h5>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                    <?php echo e(__('settings.active_sessions_help')); ?></p>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sessions->count() > 1): ?>
                            <form method="POST" action="<?php echo e(route('account.settings.sessions.revoke-all')); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm"
                                    style="background:rgba(239,68,68,0.1);color:var(--danger);border:1px solid rgba(239,68,68,0.2);"
                                    onclick="return confirm('<?php echo e(__('settings.confirm_revoke_all')); ?>')">
                                    <i class="bi bi-box-arrow-right me-1"></i><?php echo e(__('settings.revoke_all_others')); ?>

                                </button>
                            </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="sessions-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div
                                class="session-item d-flex align-items-center justify-content-between py-3 <?php echo e($loop->first ? '' : 'border-top'); ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                                        style="width:40px;height:40px;background:<?php echo e($session->is_current ? 'rgba(21,183,108,0.1)' : 'var(--bg-secondary)'); ?>;flex-shrink:0;">
                                        <i class="bi <?php echo e($session->device === 'phone' ? 'bi-phone' : ($session->device === 'tablet' ? 'bi-tablet' : 'bi-pc-display')); ?>"
                                            style="color:<?php echo e($session->is_current ? 'var(--accent)' : 'var(--text-muted)'); ?>;font-size:18px;"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span style="font-weight:500;font-size:14px;"><?php echo e($session->browser); ?> on
                                                <?php echo e($session->os); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($session->is_current): ?>
                                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'active','set' => 'bi','size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'active','set' => 'bi','size' => 'xs']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div style="font-size:12px;color:var(--text-muted);">
                                            <i class="bi bi-globe me-1"></i><?php echo e($session->ip_address); ?>

                                            &middot;
                                            <i
                                                class="bi bi-clock me-1"></i><?php echo e(\Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans()); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($session->login_at): ?>
                                                &middot;
                                                <i
                                                    class="bi bi-box-arrow-in-right me-1"></i><?php echo e($session->login_at->diffForHumans()); ?>

                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$session->is_current): ?>
                                    <form method="POST"
                                        action="<?php echo e(route('account.settings.sessions.revoke', $session->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm"
                                            style="background:transparent;color:var(--text-muted);border:1px solid var(--border-color);"
                                            title="<?php echo e(__('settings.revoke_session')); ?>">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-3">
                                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-pc-display','title' => __('settings.no_sessions')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-pc-display','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.no_sessions'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(168,85,247,0.1);flex-shrink:0;">
                            <i class="bi bi-clock-history" style="color:#a855f7;font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                                <?php echo e(__('settings.login_history')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                <?php echo e(__('settings.login_history_help')); ?></p>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loginHistory->count()): ?>
                        <div class="table-responsive">
                            <table class="data-table" style="margin:0;">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('general.status')); ?></th>
                                        <th><?php echo e(__('general.ip_address')); ?></th>
                                        <th><?php echo e(__('general.device')); ?></th>
                                        <th><?php echo e(__('general.user_agent')); ?></th>
                                        <th><?php echo e(__('general.os')); ?></th>
                                        <th><?php echo e(__('general.date')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $loginHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attempt->status === 'success'): ?>
                                                    <?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'success','set' => 'bi','class' => 'me-1 text-success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'success','set' => 'bi','class' => 'me-1 text-success']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
                                                    <span style="color:var(--accent);font-weight:500;"><?php echo e(__('general.success')); ?></span>
                                                <?php else: ?>
                                                    <?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'failed','set' => 'bi','class' => 'me-1 text-danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'failed','set' => 'bi','class' => 'me-1 text-danger']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
                                                    <span style="color:var(--danger);font-weight:500;"><?php echo e(__('general.failed')); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attempt->suspicious): ?>
                                                    <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'suspended','set' => 'bi','size' => 'xs','class' => 'ms-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'suspended','set' => 'bi','size' => 'xs','class' => 'ms-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td style="font-family:monospace;font-size:13px;">
                                                <?php echo e($attempt->ip_address); ?></td>
                                            <td>

                                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => ''.e($attempt->device === 'phone' ? 'phone'
                                                    : ($attempt->device === 'tablet' ? 'tablet' : 'pc')).'','set' => 'bi','size' => 'xs','class' => 'ms-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => ''.e($attempt->device === 'phone' ? 'phone'
                                                    : ($attempt->device === 'tablet' ? 'tablet' : 'pc')).'','set' => 'bi','size' => 'xs','class' => 'ms-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                                                <i
                                                    class="bi bi-<?php echo e($attempt->device === 'phone' ? 'phone' : ($attempt->device === 'tablet' ? 'tablet' : 'pc-display')); ?>

                                                     ms-1"></i><?php echo e(__('general.' . $attempt->device)); ?>

                                            </td>
                                            <td><?php echo e($attempt->browser); ?></td>
                                            <td><?php echo e($attempt->os); ?></td>
                                            <td style="font-size:13px;color:var(--text-muted);">
                                                <?php echo e($attempt->created_at->diffForHumans()); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-clock-history','title' => __('settings.no_login_history')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-clock-history','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.no_login_history'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="settings-card" style="border-color:rgba(239,68,68,0.2);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(239,68,68,0.1);flex-shrink:0;">
                            <i class="bi bi-exclamation-triangle" style="color:var(--danger);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;color:var(--danger);">
                                <?php echo e(__('general.danger_zone')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                <?php echo e(__('profile.delete_account_help')); ?></p>
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
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-bell" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                                <?php echo e(__('profile.notifications')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                <?php echo e(__('profile.notifications_help')); ?></p>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentNotifications->count()): ?>
                        <div class="notifications-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $notifIcon = match ($notification->type) {
                                        'budget_exceeded',
                                        'budget_nearing_limit'
                                            => 'bi-exclamation-triangle text-danger',
                                        'debt_reminder' => 'bi-credit-card-2-front text-warning',
                                        'goal_achieved', 'goal_milestone' => 'bi-flag text-success',
                                        'goal_deadline' => 'bi-clock text-info',
                                        'zakat_reminder' => 'bi-heart text-primary',
                                        'role_changed' => 'bi-shield-check text-warning',
                                        default => 'bi-info-circle text-info',
                                    };
                                ?>
                                <div
                                    class="notification-item <?php echo e($notification->is_read ? '' : 'notification-unread'); ?>">
                                    <div class="notification-icon">
                                        <i class="bi <?php echo e($notifIcon); ?>"></i>
                                    </div>
                                    <div class="notification-body">
                                        <p class="notification-title">
                                            <?php echo e($notification->{'title_' . app()->getLocale()}); ?></p>
                                        <p class="notification-message">
                                            <?php echo e($notification->{'message_' . app()->getLocale()}); ?></p>
                                        <span
                                            class="notification-time"><?php echo e($notification->created_at->diffForHumans()); ?></span>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$notification->is_read): ?>
                                        <span class="notification-dot"></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-bell-slash','title' => __('profile.no_notifications')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-bell-slash','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('profile.no_notifications'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
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