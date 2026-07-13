<?php if (isset($component)) { $__componentOriginal11b520df80702cb1ab8718e178b6ffa6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6 = $attributes; } ?>
<?php $component = App\View\Components\SuperAdminLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('super-admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SuperAdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('super-admin.settings')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('super-admin.settings')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('super-admin.settings_desc')); ?> <?php $__env->endSlot(); ?>

    <div x-data="settingsTabs()" x-init="initTabs()">
        <div class="row g-4">
            
            <div class="col-12 col-md-3">
                <div class="settings-tabs-sidebar">
                    <nav class="nav flex-column nav-pills settings-nav">
                        <button @click="switchTab('general')" :class="{ active: tab === 'general' }" class="settings-nav-link">
                            <i class="bi bi-sliders me-2"></i><?php echo e(__('super-admin.tab_general')); ?>

                        </button>
                        <button @click="switchTab('system')" :class="{ active: tab === 'system' }" class="settings-nav-link">
                            <i class="bi bi-gear-wide-connected me-2"></i><?php echo e(__('super-admin.tab_environment')); ?>

                        </button>
                        <button @click="switchTab('security')" :class="{ active: tab === 'security' }" class="settings-nav-link">
                            <i class="bi bi-shield-check me-2"></i><?php echo e(__('super-admin.tab_security')); ?>

                        </button>
                        <button @click="switchTab('payments')" :class="{ active: tab === 'payments' }" class="settings-nav-link">
                            <i class="bi bi-credit-card-2-front me-2"></i><?php echo e(__('super-admin.tab_payments')); ?>

                        </button>
                        <button @click="switchTab('exchangeRates')" :class="{ active: tab === 'exchangeRates' }" class="settings-nav-link">
                            <i class="bi bi-currency-exchange me-2"></i><?php echo e(__('super-admin.tab_exchange_rates')); ?>

                        </button>
                        <button @click="switchTab('rateLimits')" :class="{ active: tab === 'rateLimits' }" class="settings-nav-link">
                            <i class="bi bi-speedometer2 me-2"></i><?php echo e(__('super-admin.tab_rate_limits')); ?>

                        </button>
                        <button @click="switchTab('currencies')" :class="{ active: tab === 'currencies' }" class="settings-nav-link">
                            <i class="bi bi-currency-exchange me-2"></i><?php echo e(__('super-admin.tab_currencies')); ?>

                        </button>
                    </nav>
                </div>
            </div>

            
            <div class="col-12 col-md-9">
                
                <div x-show="tab === 'general'" x-cloak x-transition:enter.duration.200ms>
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            <div class="section-card">
                                <div class="section-card-header">
                                    <h5><i class="bi bi-sliders"></i><?php echo e(__('super-admin.general_settings')); ?></h5>
                                </div>
                                <div class="section-card-body">
                                    <form method="POST" action="<?php echo e(route('super.admin.settings.update')); ?>">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <div class="form-floating-group">
                                            <input type="text" name="app_name" id="app_name" class="form-control" placeholder="<?php echo e(__('general.app_name')); ?>" value="<?php echo e($settings['app_name'] ?? config('app.name')); ?>">
                                            <label for="app_name"><?php echo e(__('general.app_name')); ?></label>
                                        </div>
                                        <div class="form-floating-group">
                                            <select name="default_locale" id="default_locale" class="form-control">
                                                <option value="en" <?php echo e(($settings['default_locale'] ?? config('app.locale')) === 'en' ? 'selected' : ''); ?>>English</option>
                                                <option value="ar" <?php echo e(($settings['default_locale'] ?? config('app.locale')) === 'ar' ? 'selected' : ''); ?>>العربية</option>
                                                <option value="fr" <?php echo e(($settings['default_locale'] ?? config('app.locale')) === 'fr' ? 'selected' : ''); ?>>Français</option>
                                            </select>
                                            <label for="default_locale"><?php echo e(__('general.language')); ?></label>
                                        </div>
                                        <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['class' => 'mb-3','name' => 'registration_enabled','value' => '1','checked' => ($settings['registration_enabled'] ?? '1') === '1','label' => ''.e(__('super-admin.registration_enabled')).'','hint' => ''.e(__('super-admin.registration_hint')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-3','name' => 'registration_enabled','value' => '1','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($settings['registration_enabled'] ?? '1') === '1'),'label' => ''.e(__('super-admin.registration_enabled')).'','hint' => ''.e(__('super-admin.registration_hint')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal319c173192d983146c5bd67854bb9452)): ?>
<?php $attributes = $__attributesOriginal319c173192d983146c5bd67854bb9452; ?>
<?php unset($__attributesOriginal319c173192d983146c5bd67854bb9452); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal319c173192d983146c5bd67854bb9452)): ?>
<?php $component = $__componentOriginal319c173192d983146c5bd67854bb9452; ?>
<?php unset($__componentOriginal319c173192d983146c5bd67854bb9452); ?>
<?php endif; ?>
                                        <button type="submit" class="btn" style="padding:8px 18px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                                            <i class="bi bi-check2"></i><?php echo e(__('general.save')); ?>

                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="section-card">
                                <div class="section-card-header">
                                    <h5><i class="bi bi-info-circle"></i><?php echo e(__('super-admin.system_info')); ?></h5>
                                </div>
                                <?php
                                    $healthBadgeBg = $systemInfo['system_health'] === 'healthy' ? 'var(--success-light)' : 'var(--danger-light)';
                                    $healthBadgeColor = $systemInfo['system_health'] === 'healthy' ? 'var(--success)' : 'var(--danger)';
                                ?>
                                <div class="section-card-body p-0">
                                    <div style="padding:4px 0">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                                            ['label' => __('super-admin.php_version'), 'value' => $systemInfo['php_version'], 'type' => 'code'],
                                            ['label' => __('super-admin.laravel_version'), 'value' => $systemInfo['laravel_version'], 'type' => 'code'],
                                            ['label' => __('super-admin.system_health'), 'value' => $systemInfo['system_health'] === 'healthy' ? __('general.healthy') : __('general.degraded'), 'type' => 'badge', 'bg' => $healthBadgeBg, 'color' => $healthBadgeColor],
                                            ['label' => __('super-admin.timezone'), 'value' => $systemInfo['timezone'], 'type' => 'code'],
                                            ['label' => __('super-admin.locale'), 'value' => $systemInfo['locale'], 'type' => 'code'],
                                            ['label' => __('super-admin.session_same_site'), 'value' => $systemInfo['session_same_site'] ?? 'lax', 'type' => 'code'],
                                            ['label' => __('super-admin.cache_driver'), 'value' => $systemInfo['cache_driver'], 'type' => 'code'],
                                            ['label' => __('super-admin.queue_driver'), 'value' => $systemInfo['queue_driver'], 'type' => 'code'],
                                            ['label' => __('super-admin.log_channel'), 'value' => $systemInfo['log_channel'], 'type' => 'code'],
                                            ['label' => __('super-admin.app_url'), 'value' => $systemInfo['app_url'], 'type' => 'code'],
                                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="d-flex align-items-center px-4 py-2" style="border-bottom:1px solid var(--border-light)">
                                                <span style="width:150px;font-size:12.5px;color:var(--text-muted);flex-shrink:0"><?php echo e($row['label']); ?></span>
                                                <span style="font-size:13px;color:var(--text)">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($row['type'] ?? '') === 'badge'): ?>
                                                        <span class="badge" style="font-size:10px;background:<?php echo e($row['bg']); ?>;color:<?php echo e($row['color']); ?>;padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e($row['value']); ?></span>
                                                    <?php else: ?>
                                                        <code><?php echo e($row['value']); ?></code>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </span>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div x-show="tab === 'system'" x-cloak x-transition:enter.duration.200ms>
                    <div class="section-card mb-4">
                        <div class="section-card-header">
                            <h5><i class="bi bi-gear-wide-connected"></i><?php echo e(__('super-admin.system_settings')); ?></h5>
                        </div>
                        <div class="section-card-body">
                            <div class="settings-section-desc"><?php echo e(__('super-admin.system_settings_desc')); ?></div>
                            <form method="POST" action="<?php echo e(route('super.admin.settings.system.update')); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-floating-group">
                                            <select name="app_env" class="form-control">
                                                <option value="production" <?php echo e(($systemSettings['app_env'] ?? 'local') === 'production' ? 'selected' : ''); ?>><?php echo e(__('super-admin.production')); ?></option>
                                                <option value="local" <?php echo e(($systemSettings['app_env'] ?? 'local') === 'local' ? 'selected' : ''); ?>>Local</option>
                                                <option value="testing" <?php echo e(($systemSettings['app_env'] ?? 'local') === 'testing' ? 'selected' : ''); ?>>Testing</option>
                                                <option value="staging" <?php echo e(($systemSettings['app_env'] ?? 'local') === 'staging' ? 'selected' : ''); ?>>Staging</option>
                                            </select>
                                            <label><?php echo e(__('super-admin.app_env')); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating-group">
                                            <input type="text" class="form-control" value="<?php echo e(($systemSettings['app_debug'] ?? 'false') === 'true' ? __('general.enabled') : __('general.disabled')); ?>" readonly disabled>
                                            <label><?php echo e(__('super-admin.app_debug')); ?></label>
                                            <div class="form-hint"><i class="bi bi-lock"></i> <?php echo e(__('super-admin.app_debug_readonly')); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating-group">
                                            <input type="url" name="app_url" class="form-control" placeholder=" " value="<?php echo e($systemSettings['app_url'] ?? config('app.url')); ?>">
                                            <label><?php echo e(__('super-admin.app_url')); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating-group">
                                            <select name="log_level" class="form-control">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['debug','info','notice','warning','error','critical','alert','emergency']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($level); ?>" <?php echo e(($systemSettings['log_level'] ?? 'warning') === $level ? 'selected' : ''); ?>><?php echo e(ucfirst($level)); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                            <label><?php echo e(__('super-admin.log_level')); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating-group">
                                            <select name="log_channel" class="form-control">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['single','daily','slack','syslog','errorlog','stack']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($channel); ?>" <?php echo e(($systemSettings['log_channel'] ?? 'daily') === $channel ? 'selected' : ''); ?>><?php echo e(ucfirst($channel)); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                            <label><?php echo e(__('super-admin.log_channel')); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating-group">
                                            <select name="session_encrypt" class="form-control">
                                                <option value="false" <?php echo e(($systemSettings['session_encrypt'] ?? 'false') === 'false' ? 'selected' : ''); ?>><?php echo e(__('general.disabled')); ?></option>
                                                <option value="true" <?php echo e(($systemSettings['session_encrypt'] ?? 'false') === 'true' ? 'selected' : ''); ?>><?php echo e(__('general.enabled')); ?></option>
                                            </select>
                                            <label><?php echo e(__('super-admin.session_encrypt')); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating-group">
                                            <select name="session_secure_cookie" class="form-control">
                                                <option value="false" <?php echo e(($systemSettings['session_secure_cookie'] ?? 'false') === 'false' ? 'selected' : ''); ?>><?php echo e(__('general.disabled')); ?></option>
                                                <option value="true" <?php echo e(($systemSettings['session_secure_cookie'] ?? 'false') === 'true' ? 'selected' : ''); ?>><?php echo e(__('general.enabled')); ?></option>
                                            </select>
                                            <label><?php echo e(__('super-admin.session_secure_cookie')); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating-group">
                                            <select name="session_same_site" class="form-control">
                                                <option value="lax" <?php echo e(($systemSettings['session_same_site'] ?? 'lax') === 'lax' ? 'selected' : ''); ?>>Lax</option>
                                                <option value="strict" <?php echo e(($systemSettings['session_same_site'] ?? 'lax') === 'strict' ? 'selected' : ''); ?>>Strict</option>
                                                <option value="none" <?php echo e(($systemSettings['session_same_site'] ?? 'lax') === 'none' ? 'selected' : ''); ?>>None</option>
                                            </select>
                                            <label><?php echo e(__('super-admin.session_same_site')); ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-accent btn-custom">
                                        <i class="bi bi-check2"></i><?php echo e(__('general.save')); ?>

                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                
                <div x-show="tab === 'security'" x-cloak x-transition:enter.duration.200ms>
                    <div class="section-card mb-4">
                        <div class="section-card-header">
                            <h5><i class="bi bi-shield-check"></i><?php echo e(__('settings.two_factor')); ?></h5>
                        </div>
                        <div class="section-card-body">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($twoFactorEnabled): ?>
                                <div class="d-flex align-items-center gap-2 mb-3" style="font-size:13px;background:var(--success-light);color:var(--success);border-radius:var(--radius-sm);padding:10px 14px">
                                    <i class="bi bi-check-circle-fill"></i><?php echo e(__('messages.two_factor_enabled')); ?>

                                </div>
                                <form method="POST" action="<?php echo e(route('super.admin.settings.2fa.disable')); ?>">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                    <button type="button" class="btn" style="padding:8px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--danger);background:transparent;color:var(--danger);font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:6px" onclick="confirmDisable2fa(this)">
                                        <i class="bi bi-shield-slash"></i><?php echo e(__('general.disable')); ?>

                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="d-flex align-items-center gap-2 mb-3" style="font-size:13px;background:var(--warning-light);color:var(--warning);border-radius:var(--radius-sm);padding:10px 14px">
                                    <i class="bi bi-exclamation-triangle-fill"></i><?php echo e(__('auth.2fa_not_enabled')); ?>

                                </div>
                                <a href="<?php echo e(route('two-factor.setup')); ?>" class="btn" style="padding:8px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                                    <i class="bi bi-shield-plus"></i><?php echo e(__('general.setup_2fa')); ?>

                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div x-show="tab === 'payments'" x-cloak x-transition:enter.duration.200ms>
                    <div class="section-card mb-4">
                        <div class="section-card-header">
                            <h5><i class="bi bi-credit-card-2-front"></i><?php echo e(__('super-admin.payment_gateways')); ?></h5>
                        </div>
                        <div class="section-card-body">
                            <div class="settings-section-desc"><?php echo e(__('super-admin.payment_gateways_desc')); ?></div>
                            <div class="p-4 text-center">
                                <i class="bi bi-credit-card-2-front" style="font-size:2.5rem;color:var(--text-muted);"></i>
                                <p class="text-muted mt-2 mb-3"><?php echo e(__('super-admin.credentials_managed_in_methods')); ?></p>
                                <a href="<?php echo e(route('super.admin.payment-methods.index')); ?>" class="btn btn-accent btn-custom">
                                    <i class="bi bi-gear me-1"></i><?php echo e(__('super-admin.manage_payment_methods')); ?>

                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div x-show="tab === 'exchangeRates'" x-cloak x-transition:enter.duration.200ms>
                    <?php
                        $exchangeRates = json_decode(\App\Models\Setting::get('exchange_rates', '{}'), true) + ['USD' => 1, 'DZD' => 250, 'EUR' => 0.877, 'GBP' => 0.75, 'USDT' => 1];
                        $baseCurrency = config('finance.base_currency', 'USD');
                    ?>
                    <div class="section-card mb-4">
                        <div class="section-card-header">
                            <h5><i class="bi bi-currency-exchange"></i><?php echo e(__('super-admin.exchange_rates')); ?></h5>
                        </div>
                        <div class="section-card-body">
                            <div class="settings-section-desc"><?php echo e(__('super-admin.exchange_rates_desc')); ?></div>
                            <form method="POST" action="<?php echo e(route('super.admin.settings.exchange-rates.update')); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <div class="row g-3">
                                    <?php $rateCurrencies = array_diff(\App\Helpers\CurrencyHelper::availableCurrencyCodes() ?: ['DZD', 'EUR', 'GBP', 'USDT'], [$baseCurrency]); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rateCurrencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-4">
                                            <div class="form-floating-group">
                                                <input type="number" name="rates[<?php echo e($cur); ?>]" class="form-control" placeholder=" " value="<?php echo e($exchangeRates[$cur] ?? ''); ?>" step="0.001" min="0" lang="en">
                                                <label><?php echo e($cur); ?> / <?php echo e($baseCurrency); ?></label>
                                                <div class="form-hint">1 <?php echo e($baseCurrency); ?> = ? <?php echo e($cur); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-accent btn-custom">
                                        <i class="bi bi-check2"></i><?php echo e(__('general.save')); ?>

                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                
                <div x-show="tab === 'rateLimits'" x-cloak x-transition:enter.duration.200ms>
                    <div class="section-card mb-4">
                        <div class="section-card-header">
                            <h5><i class="bi bi-speedometer2"></i><?php echo e(__('super-admin.rate_limits')); ?></h5>
                        </div>
                        <div class="section-card-body">
                            <div class="settings-section-desc"><?php echo e(__('super-admin.rate_limits_desc')); ?></div>
                            <form method="POST" action="<?php echo e(route('super.admin.settings.rate-limits.update')); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <?php
                                    $groups = [
                                        'api' => __('super-admin.rate_limit_group_api'),
                                        'web' => __('super-admin.rate_limit_group_web'),
                                        'auth' => __('super-admin.rate_limit_group_auth'),
                                        'system' => __('super-admin.rate_limit_group_system'),
                                    ];
                                    $groupMap = [
                                        'api' => ['api', 'api-workspace', 'api-sensitive', 'api-auth'],
                                        'web' => ['web', 'web-list', 'web-search', 'web-crud', 'web-delete', 'web-sensitive', 'web-proof', 'web-invite-resend'],
                                        'auth' => ['login', 'register'],
                                        'system' => ['webhook', 'super-admin-settings'],
                                    ];
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $groupLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <h6 class="mt-3 mb-2" style="font-size:13px;font-weight:600;color:var(--text)"><?php echo e($groupLabel); ?></h6>
                                    <div class="row g-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groupMap[$groupKey]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-4">
                                                <div class="form-floating-group">
                                                    <input type="number" name="rate_limits[<?php echo e($key); ?>]" class="form-control" placeholder=" " value="<?php echo e($rateLimits[$key] ?? config('finance.rate_limits.' . $key, 120)); ?>" min="1" max="10000">
                                                    <label><code><?php echo e($key); ?></code></label>
                                                    <div class="form-hint"><?php echo e(__('super-admin.rate_limit_per_minute')); ?></div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-accent btn-custom">
                                        <i class="bi bi-check2"></i><?php echo e(__('general.save')); ?>

                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div x-show="tab === 'currencies'" x-cloak x-transition:enter.duration.200ms
                     x-data="currenciesManager()" x-init="init()">
                    <div class="section-card mb-4">
                        <div class="section-card-header">
                            <h5><i class="bi bi-currency-exchange"></i><?php echo e(__('super-admin.currencies')); ?></h5>
                            <div class="section-card-actions">
                                <button @click="resetDefault()" class="btn btn-sm" style="padding:4px 10px;font-size:12px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted)">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i><?php echo e(__('super-admin.currencies_reset_default')); ?>

                                </button>
                            </div>
                        </div>
                        <div class="section-card-body">
                            <div class="settings-section-desc"><?php echo e(__('super-admin.currencies_desc')); ?></div>

                            <?php
                                $currenciesList = \App\Helpers\CurrencyHelper::availableCurrencies();
                            ?>

                            <form method="POST" action="<?php echo e(route('super.admin.settings.currencies.update')); ?>" id="currencies-form">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <input type="hidden" name="currencies" id="currencies-input" value="<?php echo e(htmlspecialchars(json_encode($currenciesList))); ?>">

                                <div class="table-responsive mt-3">
                                    <table class="data-table" id="currencies-table">
                                        <thead>
                                            <tr>
                                                <th style="width:80px"><?php echo e(__('super-admin.currency_code')); ?></th>
                                                <th><?php echo e(__('super-admin.currency_name')); ?></th>
                                                <th style="width:100px"><?php echo e(__('super-admin.currency_symbol')); ?></th>
                                                <th class="col-actions" style="width:80px"><?php echo e(__('general.actions')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(cur, index) in currencies" :key="index">
                                                <tr>
                                                    <td><code x-text="cur.code" style="font-size:13px"></code></td>
                                                    <td x-text="cur.name"></td>
                                                    <td x-text="cur.symbol" style="font-size:16px"></td>
                                                    <td class="col-actions">
                                                        <div class="cell-actions">
                                                            <button type="button" class="btn" style="width:28px;height:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:12px" title="<?php echo e(__('general.edit')); ?>" @click="editCurrency(index)">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button type="button" class="btn" style="width:28px;height:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:12px" title="<?php echo e(__('general.delete')); ?>" @click="removeCurrency(index)">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                <button type="button" class="btn btn-accent btn-custom mt-3" @click="addCurrency()">
                                    <i class="bi bi-plus-lg me-1"></i><?php echo e(__('super-admin.add_currency')); ?>

                                </button>

                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-accent btn-custom">
                                        <i class="bi bi-check2"></i><?php echo e(__('general.save')); ?>

                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    
                    <div class="modal-overlay" x-show="showModal" x-cloak x-transition.opacity
                         @click.self="showModal = false" style="position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem">
                        <div class="modal-content" style="background:var(--bg);border-radius:var(--radius-md);padding:1.5rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,0.2)" @click.stop>
                            <h5 style="margin:0 0 1rem;font-size:15px;font-weight:600">
                                <span x-text="editingIndex !== null ? '<?php echo e(__('super-admin.edit_currency')); ?>' : '<?php echo e(__('super-admin.add_currency')); ?>'"></span>
                            </h5>

                            <div class="mb-3">
                                <label class="form-label-custom"><?php echo e(__('super-admin.currency_code')); ?> <span class="text-danger">*</span></label>
                                <input type="text" x-model="form.code" maxlength="3" class="form-control" placeholder="USD" style="text-transform:uppercase">
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom"><?php echo e(__('super-admin.currency_name')); ?> <span class="text-danger">*</span></label>
                                <input type="text" x-model="form.name" class="form-control" placeholder="US Dollar">
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom"><?php echo e(__('super-admin.currency_symbol')); ?> <span class="text-danger">*</span></label>
                                <input type="text" x-model="form.symbol" class="form-control" placeholder="$" style="max-width:100px">
                            </div>

                            <div class="d-flex gap-2 justify-content-end mt-3">
                                <button type="button" class="btn btn-custom" style="padding:6px 16px;border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px" @click="showModal = false">
                                    <?php echo e(__('general.cancel')); ?>

                                </button>
                                <button type="button" class="btn btn-accent btn-custom" @click="confirmCurrency()">
                                    <?php echo e(__('general.confirm')); ?>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function settingsTabs() {
                return {
                    tab: localStorage.getItem('sa_tab_settings') || 'general',
                    initTabs() {
                        this.$watch('tab', val => localStorage.setItem('sa_tab_settings', val));
                    },
                    switchTab(name) {
                        this.tab = name;
                    }
                };
            }

            function confirmDisable2fa(btn) {
                showConfirmModal(
                    '<?php echo e(__('general.confirm')); ?>',
                    '<?php echo e(__('messages.confirm_disable_2fa')); ?>?',
                    function(confirmed) { if (confirmed) { btn.closest('form').submit(); } },
                    '<?php echo e(__('general.disable')); ?>',
                    'btn-danger'
                );
            }

            function currenciesManager() {
                return {
                    currencies: [],
                    showModal: false,
                    editingIndex: null,
                    form: { code: '', name: '', symbol: '' },
                    init() {
                        const input = document.getElementById('currencies-input');
                        try {
                            this.currencies = JSON.parse(input.value || '[]');
                        } catch(e) {
                            this.currencies = [];
                        }
                        const formEl = document.getElementById('currencies-form');
                        if (formEl) {
                            formEl.addEventListener('submit', () => {
                                document.getElementById('currencies-input').value = JSON.stringify(this.currencies);
                            });
                        }
                    },
                    addCurrency() {
                        this.editingIndex = null;
                        this.form = { code: '', name: '', symbol: '' };
                        this.showModal = true;
                    },
                    editCurrency(index) {
                        this.editingIndex = index;
                        this.form = { ...this.currencies[index] };
                        this.showModal = true;
                    },
                    confirmCurrency() {
                        const code = this.form.code.toUpperCase().trim();
                        const name = this.form.name.trim();
                        const symbol = this.form.symbol.trim();
                        if (!code || !name || !symbol) return;
                        if (this.editingIndex !== null) {
                            this.currencies[this.editingIndex] = { code, name, symbol };
                        } else {
                            this.currencies.push({ code, name, symbol });
                        }
                        this.showModal = false;
                    },
                    removeCurrency(index) {
                        showConfirmModal(
                            '<?php echo e(__('general.confirm')); ?>',
                            '<?php echo e(__('super-admin.confirm_delete_currency')); ?>',
                            (confirmed) => {
                                if (confirmed) this.currencies.splice(index, 1);
                            },
                            '<?php echo e(__('general.delete')); ?>',
                            'btn-danger'
                        );
                    },
                    resetDefault() {
                        const defaults = [
                            {code:'DZD',name:'Algerian Dinar',symbol:'د.ج'},
                            {code:'USD',name:'US Dollar',symbol:'$'},
                            {code:'EUR',name:'Euro',symbol:'€'},
                            {code:'GBP',name:'British Pound',symbol:'£'},
                            {code:'USDT',name:'Tether',symbol:'₮'},
                            {code:'BTC',name:'Bitcoin',symbol:'₿'},
                            {code:'ETH',name:'Ethereum',symbol:'Ξ'},
                        ];
                        showConfirmModal(
                            '<?php echo e(__('general.confirm')); ?>',
                            '<?php echo e(__('super-admin.currencies_reset_default')); ?>?',
                            (confirmed) => {
                                if (confirmed) this.currencies = defaults;
                            },
                            '<?php echo e(__('super-admin.currencies_reset_default')); ?>',
                            'btn-warning'
                        );
                    }
                };
            }
        </script>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('styles'); ?>
        <style>
            .settings-tabs-sidebar {
                background: var(--card-bg);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 8px;
                position: sticky;
                top: 24px;
            }

            .settings-nav {
                gap: 2px;
            }

            .settings-nav-link {
                display: flex;
                align-items: center;
                width: 100%;
                padding: 10px 14px;
                font-size: 13px;
                font-weight: 500;
                color: var(--text-secondary);
                background: transparent;
                border: none;
                border-radius: var(--radius-sm);
                cursor: pointer;
                transition: all 0.15s;
                text-align: left;
            }

            .settings-nav-link:hover {
                background: var(--bg-subtle);
                color: var(--text);
            }

            .settings-nav-link.active {
                background: var(--accent);
                color: #0F172A;
                font-weight: 600;
            }

            .settings-nav-link i {
                font-size: 15px;
                width: 20px;
                text-align: center;
            }

            [dir="rtl"] .settings-nav-link {
                text-align: right;
            }

            @media (max-width: 767px) {
                .settings-tabs-sidebar {
                    position: static;
                }

                .settings-nav {
                    flex-direction: row !important;
                    overflow-x: auto;
                    gap: 4px;
                    padding-bottom: 4px;
                }

                .settings-nav-link {
                    white-space: nowrap;
                    padding: 8px 12px;
                    font-size: 12px;
                }
            }
        </style>
    <?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $attributes = $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $component = $__componentOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\Finance-Manager\resources\views/super-admin/settings.blade.php ENDPATH**/ ?>