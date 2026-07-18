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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('developer.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('developer.api')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('developer.page_description')); ?> <?php $__env->endSlot(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('api_token')): ?>
        <?php $tokenVal = session('api_token'); ?>
        <div id="new-token-value" data-token="<?php echo e($tokenVal); ?>" style="display:none"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row g-4">

        
        <div class="col-lg-8">

            
            <div class="settings-section">
                <div class="settings-card" style="overflow:hidden">
                    <div style="background:linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #000));margin:-1.25rem -1.25rem 1.25rem -1.25rem;padding:1.5rem 1.75rem;position:relative">
                        <div style="position:absolute;top:0;right:0;width:200px;height:200px;background:rgba(255,255,255,0.05);border-radius:50%;transform:translate(50%,-50%)"></div>
                        <div style="position:absolute;bottom:0;left:0;width:150px;height:150px;background:rgba(255,255,255,0.03);border-radius:50%;transform:translate(-30%,30%)"></div>
                        <div class="d-flex align-items-center justify-content-between gap-3" style="position:relative;z-index:1">
                            <div>
                                <div style="font-size:13px;color:rgba(255,255,255,0.7);margin-bottom:4px"><?php echo e(__('developer.api_tokens')); ?></div>
                                <h3 style="font-weight:700;color:#fff;margin-bottom:0"><?php echo e(__('developer.api')); ?> <span style="font-weight:400;font-size:14px;color:rgba(255,255,255,0.6)"><?php echo e(__('developer.api_tokens_desc')); ?></span></h3>
                            </div>
                            <button class="btn btn-custom flex-shrink-0" style="background:rgba(255,255,255,0.2);color:#fff;border:none" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                                <i class="bi bi-plus-lg me-1"></i><?php echo e(__('developer.create_token')); ?>

                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div style="text-align:center;padding:12px">
                                <div style="font-size:28px;font-weight:700;color:var(--text-color)"><?php echo e($stats['total']); ?></div>
                                <div style="font-size:12px;color:var(--text-muted)"><?php echo e(__('general.total')); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div style="text-align:center;padding:12px">
                                <div style="font-size:28px;font-weight:700;color:var(--success)"><?php echo e($stats['active']); ?></div>
                                <div style="font-size:12px;color:var(--text-muted)"><?php echo e(__('general.active')); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div style="text-align:center;padding:12px">
                                <div style="font-size:28px;font-weight:700;color:var(--danger)"><?php echo e($stats['expired']); ?></div>
                                <div style="font-size:12px;color:var(--text-muted)"><?php echo e(__('general.expired')); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div style="text-align:center;padding:12px">
                                <div style="font-size:28px;font-weight:700;color:var(--text-muted)"><?php echo e($stats['never_used']); ?></div>
                                <div style="font-size:12px;color:var(--text-muted)"><?php echo e(__('developer.never_used')); ?></div>
                            </div>
                        </div>
                    </div>

                    
                    <div style="margin-top:8px;padding:10px 16px;background:var(--bg-subtle);border-radius:8px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-bar-chart-fill" style="color:var(--accent);font-size:14px"></i>
                            <span style="font-size:13px;font-weight:500"><?php echo e(__('developer.token_usage')); ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width:300px">
                            <div class="progress flex-grow-1" style="height:6px;border-radius:3px;background:var(--border)">
                                <?php $usagePercent = $stats['total'] > 0 ? min(100, round(($stats['total'] / max(1, $stats['token_limit'])) * 100)) : 0; ?>
                                <div class="progress-bar" role="progressbar"
                                    style="width:<?php echo e($usagePercent); ?>%;border-radius:3px;background:<?php echo e($usagePercent >= 90 ? 'var(--danger)' : ($usagePercent >= 70 ? 'var(--warning)' : 'var(--accent))')); ?>"></div>
                            </div>
                            <span style="font-size:12px;color:var(--text-muted);white-space:nowrap"><?php echo e($stats['total']); ?> / <?php echo e($stats['token_limit']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quotaLimits['minute'] > 0 || $quotaLimits['hour'] > 0 || $quotaLimits['day'] > 0): ?>
            <div class="settings-section">
                <div class="settings-card">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-speedometer2" style="color:var(--accent)"></i>
                        <span><?php echo e(__('developer.api_quota')); ?></span>
                    </h5>
                    <div class="row g-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['minute' => __('developer.per_minute'), 'hour' => __('developer.per_hour'), 'day' => __('developer.per_day')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $limit = $quotaLimits[$period];
                                $used = $quotaUsage[$period];
                                $remaining = max(0, $limit - $used);
                                $percent = $limit > 0 ? min(100, round(($used / $limit) * 100)) : 0;
                                $barColor = $percent >= 90 ? 'var(--danger)' : ($percent >= 70 ? 'var(--warning)' : 'var(--accent)');
                                $reset = \Carbon\Carbon::createFromTimestamp($quotaReset[$period]);
                            ?>
                            <div class="col-md-4">
                                <div style="border:1px solid var(--border);border-radius:10px;padding:14px;height:100%">
                                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px"><?php echo e($label); ?></div>
                                    <div class="d-flex align-items-baseline gap-1 mb-2">
                                        <span style="font-size:24px;font-weight:700;color:var(--text-color)"><?php echo e($used); ?></span>
                                        <span style="font-size:13px;color:var(--text-muted)">/ <?php echo e($limit); ?></span>
                                    </div>
                                    <div class="progress" style="height:5px;border-radius:3px;background:var(--border);margin-bottom:6px">
                                        <div class="progress-bar" role="progressbar" style="width:<?php echo e($percent); ?>%;border-radius:3px;background:<?php echo e($barColor); ?>"></div>
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);display:flex;justify-content:space-between">
                                        <span><?php echo e($remaining); ?> <?php echo e(__('developer.remaining')); ?></span>
                                        <span><i class="bi bi-arrow-clockwise me-1"></i><?php echo e($reset->diffForHumans()); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="settings-section">
                <div class="settings-card">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-key-fill" style="color:var(--accent)"></i>
                                <span><?php echo e(__('developer.api_tokens')); ?></span>
                            </h5>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tokens->isNotEmpty()): ?>
                            <div class="d-flex gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tokens->count() > 1): ?>
                                    <form action="<?php echo e(route('account.settings.developer.revoke-all')); ?>" method="POST" id="revoke-all-form">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="showConfirmModal('<?php echo e(__('developer.revoke_all')); ?>', '<?php echo e(__('developer.revoke_all_confirm')); ?>', (confirmed) => { if (confirmed) document.getElementById('revoke-all-form').submit(); }, '<?php echo e(__('developer.revoke_all')); ?>', 'btn-danger')">
                                            <i class="bi bi-trash me-1"></i><?php echo e(__('developer.revoke_all')); ?>

                                        </button>
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <button class="btn btn-accent btn-custom btn-sm" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                                    <i class="bi bi-plus-lg me-1"></i><?php echo e(__('developer.create_token')); ?>

                                </button>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tokens->isEmpty()): ?>
                        <div class="text-center py-5">
                            <div style="width:72px;height:72px;border-radius:50%;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                                <i class="bi bi-key" style="font-size:2rem;color:var(--text-muted);opacity:0.4"></i>
                            </div>
                            <p class="fw-medium mb-1"><?php echo e(__('developer.no_tokens')); ?></p>
                            <p class="text-muted small mb-3"><?php echo e(__('developer.no_tokens_desc')); ?></p>
                            <button class="btn btn-accent btn-custom" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                                <i class="bi bi-plus-lg me-1"></i><?php echo e(__('developer.create_token')); ?>

                            </button>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isExpired = $token['expires_at'] && now()->gt($token['expires_at']);
                                    $expiresSoon = $token['expires_at'] && !$isExpired && now()->diffInDays($token['expires_at']) <= 7;
                                    $isDeactivated = !$isExpired && $token['deactivated_at'];
                                    $isActive = !$isExpired && !$isDeactivated;
                                    $expiryPercent = $token['expires_at'] ? min(100, round((now()->diffInDays($token['expires_at'], false) / max(1, $token['created_at']->diffInDays($token['expires_at']))) * 100)) : null;
                                    $abilityCount = count($token['abilities']);
                                    $displayAbilities = $abilityCount <= 3 ? $token['abilities'] : array_slice($token['abilities'], 0, 3);
                                    $maskedToken = $token['plaintext_token'] ? (substr($token['plaintext_token'], 0, 8) . '****' . substr($token['plaintext_token'], -6)) : null;
                                    $statusColor = $isExpired ? 'var(--danger-border, #fecaca)' : ($isDeactivated ? 'var(--warning-border, #fde68a)' : 'var(--border)');
                                ?>
                                <div class="token-card" style="border:1px solid <?php echo e($statusColor); ?>;border-radius:10px;padding:16px;transition:all 0.2s;background:var(--card-bg)">
                                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                                        <div class="d-flex align-items-start gap-3 flex-grow-1">
                                            <div style="width:40px;height:40px;border-radius:8px;background:<?php echo e($isExpired ? 'var(--danger-light, #fef2f2)' : ($isDeactivated ? 'var(--warning-light, #fef3c7)' : 'var(--accent-light, rgba(21,183,108,0.12))')); ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                                <i class="bi bi-key-fill" style="color:<?php echo e($isExpired ? 'var(--danger)' : ($isDeactivated ? 'var(--warning)' : 'var(--accent)')); ?>;font-size:1.1rem"></i>
                                            </div>
                                            <div class="flex-grow-1" style="min-width:0">
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span id="token-name-<?php echo e($token['id']); ?>" class="fw-semibold" style="font-size:14px"><?php echo e($token['name']); ?></span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isExpired): ?>
                                                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'expired','set' => 'bi','size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'expired','set' => 'bi','size' => 'xs']); ?>
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
                                                    <?php elseif($isDeactivated): ?>
                                                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'inactive','set' => 'bi','size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'inactive','set' => 'bi','size' => 'xs']); ?>
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
                                                    <?php elseif($expiresSoon): ?>
                                                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'warning','set' => 'bi','size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'warning','set' => 'bi','size' => 'xs']); ?>
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
                                                <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size:12px;color:var(--text-muted);margin-top:4px">
                                                    <span><i class="bi bi-calendar3 me-1"></i><?php echo e($token['created_at']->format('M d, Y')); ?></span>
                                                    <span><i class="bi bi-clock-history me-1"></i><?php echo e($token['last_used_at'] ? $token['last_used_at']->diffForHumans() : __('developer.never_used')); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($token['expires_at']): ?>
                                                    <span class="<?php echo e($isExpired ? 'text-danger' : ''); ?>"><i class="bi bi-hourglass-split me-1"></i><?php echo e($token['expires_at']->format('M d, Y')); ?></span>
                                                <?php else: ?>
                                                    <span><i class="bi bi-infinity me-1"></i><?php echo e(__('developer.never_expires')); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <span><i class="bi bi-graph-up me-1"></i><?php echo e($token['usage_7d']); ?> <?php echo e(__('developer.requests_7d')); ?></span>
                                            </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($token['expires_at'] && !$isExpired && $expiryPercent !== null): ?>
                                                    <div style="margin-top:8px;max-width:240px">
                                                        <div style="display:flex;justify-content:space-between;font-size:10px;color:var(--text-muted);margin-bottom:2px">
                                                            <span><?php echo e(now()->diffInDays($token['expires_at'])); ?> <?php echo e(__('general.days_left')); ?></span>
                                                        </div>
                                                        <div class="progress" style="height:3px;border-radius:2px;background:var(--border)">
                                                            <div class="progress-bar" role="progressbar" style="width:<?php echo e($expiryPercent); ?>%;border-radius:2px;background:<?php echo e($expiresSoon ? 'var(--warning)' : 'var(--accent)'); ?>"></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($maskedToken): ?>
                                                    <div style="margin-top:6px;display:flex;align-items:center;gap:6px">
                                                        <code style="font-size:11px;background:var(--bg-subtle);border:1px solid var(--border);border-radius:4px;padding:2px 8px;color:var(--text-muted);letter-spacing:0.5px;direction:ltr;display:inline-block"><?php echo e($maskedToken); ?></code>
                                                        <span style="font-size:10px;color:var(--text-muted)"><i class="bi bi-lock-fill me-1"></i><?php echo e(__('developer.token_masked')); ?></span>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <div style="margin-top:6px;display:flex;align-items:center;gap:4px;flex-wrap:wrap">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $displayAbilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="badge" style="background:var(--bg-subtle);color:var(--text-color);font-size:10px;padding:2px 8px;border-radius:20px;font-weight:500;border:1px solid var(--border)"><?php echo e($a); ?></span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($abilityCount > 3): ?>
                                                        <span class="badge" style="background:var(--bg-subtle);color:var(--text-muted);font-size:10px;padding:2px 8px;border-radius:20px;font-weight:500;border:1px solid var(--border)">+<?php echo e($abilityCount - 3); ?> <?php echo e(__('general.more')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-shrink-0">
                                            <button type="button" class="btn btn-sm btn-outline-accent" title="<?php echo e(__('developer.details')); ?>"
                                                @click="showTokenFullDetails(<?php echo e($token['id']); ?>, '<?php echo e($token['name']); ?>', '<?php echo e($token['created_at']->format('M d, Y H:i')); ?>', '<?php echo e($token['last_used_at'] ? $token['last_used_at']->diffForHumans() : __('developer.never_used')); ?>', <?php echo e(json_encode($token['abilities'])); ?>, '<?php echo e($token['expires_at'] ? $token['expires_at']->format('M d, Y') : __('developer.never_expires')); ?>', <?php echo e($token['deactivated_at'] ? 'true' : 'false'); ?>)">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActive): ?>
                                                <button type="button" class="btn btn-sm btn-outline-accent" title="<?php echo e(__('developer.view_token')); ?>"
                                                    @click="showTokenFullDetails(<?php echo e($token['id']); ?>, '<?php echo e($token['name']); ?>', '<?php echo e($token['created_at']->format('M d, Y H:i')); ?>', '<?php echo e($token['last_used_at'] ? $token['last_used_at']->diffForHumans() : __('developer.never_used')); ?>', <?php echo e(json_encode($token['abilities'])); ?>, '<?php echo e($token['expires_at'] ? $token['expires_at']->format('M d, Y') : __('developer.never_expires')); ?>', <?php echo e($token['deactivated_at'] ? 'true' : 'false'); ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-accent" title="<?php echo e(__('developer.rename')); ?>"
                                                    @click="showRenameModal(<?php echo e($token['id']); ?>, '<?php echo e($token['name']); ?>')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="<?php echo e(route('account.settings.developer.regenerate', $token['id'])); ?>" method="POST" id="regenerate-form-<?php echo e($token['id']); ?>" style="display:inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="button" class="btn btn-sm btn-outline-warning" title="<?php echo e(__('developer.regenerate')); ?>"
                                                        @click="showConfirmModal('<?php echo e(__('developer.regenerate')); ?>', '<?php echo e(__('developer.regenerate_confirm')); ?>', (confirmed) => { if (confirmed) document.getElementById('regenerate-form-<?php echo e($token['id']); ?>').submit(); }, '<?php echo e(__('developer.regenerate')); ?>', 'btn-warning')">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                </form>
                                                <form action="<?php echo e(route('account.settings.developer.deactivate', $token['id'])); ?>" method="POST" id="deactivate-form-<?php echo e($token['id']); ?>" style="display:inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="button" class="btn btn-sm btn-outline-warning" title="<?php echo e(__('developer.deactivate')); ?>"
                                                        @click="showConfirmModal('<?php echo e(__('developer.deactivate')); ?>', '<?php echo e(__('developer.deactivate_confirm')); ?>', (confirmed) => { if (confirmed) document.getElementById('deactivate-form-<?php echo e($token['id']); ?>').submit(); }, '<?php echo e(__('developer.deactivate')); ?>', 'btn-warning')">
                                                        <i class="bi bi-pause-fill"></i>
                                                    </button>
                                                </form>
                                            <?php elseif($isDeactivated): ?>
                                                <form action="<?php echo e(route('account.settings.developer.activate', $token['id'])); ?>" method="POST" id="activate-form-<?php echo e($token['id']); ?>" style="display:inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="button" class="btn btn-sm btn-outline-success" title="<?php echo e(__('developer.activate')); ?>"
                                                        @click="showConfirmModal('<?php echo e(__('developer.activate')); ?>', '<?php echo e(__('developer.activate_confirm')); ?>', (confirmed) => { if (confirmed) document.getElementById('activate-form-<?php echo e($token['id']); ?>').submit(); }, '<?php echo e(__('developer.activate')); ?>', 'btn-success')">
                                                        <i class="bi bi-play-fill"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <form action="<?php echo e(route('account.settings.developer.revoke', $token['id'])); ?>" method="POST" id="revoke-form-<?php echo e($token['id']); ?>" style="display:inline">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="<?php echo e(__('developer.revoke')); ?>"
                                                    @click="showConfirmModal('<?php echo e(__('developer.revoke')); ?>', '<?php echo e(__('developer.revoke_confirm')); ?>', (confirmed) => { if (confirmed) document.getElementById('revoke-form-<?php echo e($token['id']); ?>').submit(); }, '<?php echo e(__('developer.revoke')); ?>', 'btn-danger')">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="settings-section" style="position:sticky;top:24px">

                
                <div class="settings-card mb-3">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-terminal" style="color:var(--accent)"></i>
                        <span><?php echo e(__('general.quick_test')); ?></span>
                    </h5>
                    <p class="section-desc mb-2"><?php echo e(__('developer.quick_test_desc')); ?></p>
                    <div style="background:var(--bg-subtle);border:1px solid var(--border);border-radius:8px;padding:12px;font-size:12px;font-family:monospace;direction:ltr;text-align:left;overflow-x:auto;white-space:nowrap">
                        <div style="color:var(--text-muted);margin-bottom:4px"># <?php echo e(__('general.curl_example')); ?></div>
                        <div style="color:var(--accent)">curl -X GET "<?php echo e(url('/api/workspace')); ?>" \</div>
                        <div style="color:var(--accent);padding-inline-start:20px">-H "Authorization: Bearer <span style="color:var(--warning)"><?php echo e(__('general.your_token')); ?></span>" \</div>
                        <div style="color:var(--accent);padding-inline-start:20px">-H "Accept: application/json"</div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tokens->isNotEmpty()): ?>
                        <div class="mt-2 d-flex gap-1">
                            <button class="btn btn-sm btn-outline-accent flex-fill" @click="testApiToken()">
                                <i class="bi bi-play-fill me-1"></i><?php echo e(__('general.test_connection')); ?>

                            </button>
                        </div>
                        <div id="apiTestResult" class="mt-2 d-none"></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="settings-card mb-3">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-link-45deg" style="color:var(--accent)"></i>
                        <span><?php echo e(__('developer.api_base_url')); ?></span>
                    </h5>
                    <div class="mt-2">
                        <div class="d-flex align-items-center gap-1" style="background:var(--bg-subtle);border:1px solid var(--border);border-radius:6px;padding:8px 12px">
                            <code style="font-size:13px;flex:1;background:none;border:none;padding:0" class="copy-target"><?php echo e(url('/api')); ?></code>
                            <button class="btn btn-sm btn-link text-muted p-0" @click="copyToClipboard(this, '<?php echo e(url('/api')); ?>')" title="<?php echo e(__('general.copy')); ?>">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                </div>

                
                <div class="settings-card mb-3">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-shield-check" style="color:var(--accent)"></i>
                        <span><?php echo e(__('developer.auth_endpoints')); ?></span>
                    </h5>
                    <div class="mt-2 d-flex flex-column gap-1">
                        <div class="endpoint-row" style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:6px;background:var(--bg-subtle);border:1px solid var(--border)">
                            <span class="badge" style="background:var(--success);color:#fff;font-size:9px;padding:2px 6px;border-radius:4px;font-weight:600;text-transform:uppercase;flex-shrink:0">POST</span>
                            <code style="font-size:11px;background:none;border:none;padding:0;flex:1">/api/auth/login</code>
                        </div>
                        <div class="endpoint-row" style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:6px;background:var(--bg-subtle);border:1px solid var(--border)">
                            <span class="badge" style="background:var(--accent);color:#fff;font-size:9px;padding:2px 6px;border-radius:4px;font-weight:600;text-transform:uppercase;flex-shrink:0">POST</span>
                            <code style="font-size:11px;background:none;border:none;padding:0;flex:1">/api/auth/register</code>
                        </div>
                        <div class="endpoint-row" style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:6px;background:var(--bg-subtle);border:1px solid var(--border)">
                            <span class="badge" style="background:var(--danger);color:#fff;font-size:9px;padding:2px 6px;border-radius:4px;font-weight:600;text-transform:uppercase;flex-shrink:0">POST</span>
                            <code style="font-size:11px;background:none;border:none;padding:0;flex:1">/api/auth/logout</code>
                        </div>
                    </div>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($usageHistory->isNotEmpty()): ?>
                <div class="settings-card mb-3">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-bar-chart-steps" style="color:var(--accent)"></i>
                        <span><?php echo e(__('developer.usage_history')); ?></span>
                    </h5>
                    <p class="section-desc mb-2"><?php echo e(__('developer.usage_history_desc', ['total' => $totalRequests])); ?></p>
                    <div class="d-flex flex-column gap-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $usageHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $maxCount = $usageHistory->max();
                                $barWidth = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                            ?>
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:11px;color:var(--text-muted);width:40px;flex-shrink:0"><?php echo e(\Carbon\Carbon::parse($date)->format('D')); ?></span>
                                <div class="progress flex-grow-1" style="height:6px;border-radius:3px;background:var(--border)">
                                    <div class="progress-bar" role="progressbar" style="width:<?php echo e($barWidth); ?>%;border-radius:3px;background:var(--accent)"></div>
                                </div>
                                <span style="font-size:11px;color:var(--text-color);font-weight:600;width:30px;text-align:right"><?php echo e($count); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="settings-card">
                    <h5 class="section-title d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-book" style="color:var(--accent)"></i>
                        <span><?php echo e(__('developer.api_documentation_link')); ?></span>
                    </h5>
                    <p class="section-desc mt-2 mb-3"><?php echo e(__('developer.api_documentation_desc')); ?></p>
                    <a href="<?php echo e(route('api.documentation')); ?>" class="btn btn-accent btn-custom w-100" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i><?php echo e(__('general.api_documentation')); ?>

                    </a>
                </div>

            </div>
        </div>
    </div>

    
    <div class="modal fade" id="createTokenModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="POST" action="<?php echo e(route('account.settings.developer.store')); ?>" class="modal-content">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('developer.create_token')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-custom"><?php echo e(__('developer.token_name')); ?></label>
                        <input type="text" name="name" class="form-custom" placeholder="<?php echo e(__('developer.token_name_placeholder')); ?>" required maxlength="255" autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom"><?php echo e(__('developer.expires_at')); ?></label>
                        <p class="text-muted small mb-1"><?php echo e(__('developer.expires_at_optional')); ?></p>
                        <input type="date" name="expires_at" class="form-custom" min="<?php echo e(now()->addDay()->format('Y-m-d')); ?>" style="max-width:260px">
                    </div>

                    <div class="mb-2">
                        <label class="form-label-custom"><?php echo e(__('developer.select_abilities')); ?></label>
                        <p class="text-muted small mb-2"><?php echo e(__('developer.select_abilities_desc')); ?></p>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-sm btn-outline-accent" @click="document.querySelectorAll('.ability-check').forEach(c=>c.checked=true)"><?php echo e(__('developer.select_all')); ?></button>
                            <button type="button" class="btn btn-sm btn-outline-accent" @click="document.querySelectorAll('.ability-check').forEach(c=>c.checked=false)"><?php echo e(__('developer.deselect_all')); ?></button>
                        </div>
                        <div style="max-height:360px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:8px">
                            <?php
                                $grouped = [];
                                foreach ($abilities as $slug => $desc) {
                                    $group = explode(':', $slug)[0] ?? $slug;
                                    $grouped[$group][$slug] = $desc;
                                }
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupAbilities): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-2">
                                    <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);margin-bottom:4px;padding-bottom:4px;border-bottom:1px solid var(--border)">
                                        <?php echo e(ucfirst(str_replace('-', ' ', $group))); ?>

                                    </h6>
                                    <div class="row g-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groupAbilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-6">
                                                <label class="d-flex align-items-center gap-2 p-2 rounded" style="border:1px solid var(--border);cursor:pointer;transition:all 0.15s" title="<?php echo e($desc); ?>" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor=''">
                                                    <input type="checkbox" name="abilities[]" value="<?php echo e($slug); ?>" class="ability-check" checked>
                                                    <span class="small fw-medium"><?php echo e($slug); ?></span>
                                                    <i class="bi bi-info-circle text-muted ms-auto flex-shrink-0" style="font-size:0.7rem" title="<?php echo e($desc); ?>"></i>
                                                </label>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-accent" data-bs-dismiss="modal"><?php echo e(__('developer.cancel')); ?></button>
                    <button type="submit" class="btn btn-accent btn-custom"><?php echo e(__('developer.generate')); ?></button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="modal fade" id="tokenCreatedModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0" style="padding-bottom:0">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'success','set' => 'bi','class' => 'text-success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'success','set' => 'bi','class' => 'text-success']); ?>
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
                        <span><?php echo e(__('developer.token_generated')); ?></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" @click="setTimeout(() => location.reload(), 100)"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="alert" style="background:var(--danger-light, #fef2f2);border:1px solid var(--danger-border, #fecaca);border-radius:8px;padding:12px;margin-bottom:16px">
                        <div class="d-flex align-items-start gap-2">
                            <?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'danger','set' => 'bi','class' => 'flex-shrink-0','style' => 'margin-top:2px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'danger','set' => 'bi','class' => 'flex-shrink-0','style' => 'margin-top:2px']); ?>
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
                            <div>
                                <p class="fw-semibold mb-1" style="font-size:13px;color:var(--danger)"><?php echo e(__('developer.token_one_time')); ?></p>
                                <p class="mb-0 small text-muted"><?php echo e(__('developer.token_generated_desc')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label-custom"><?php echo e(__('developer.new_token_created')); ?></label>
                        <div class="d-flex align-items-center gap-1" style="background:var(--bg-subtle);border:2px dashed var(--accent);border-radius:8px;padding:10px 12px">
                            <code id="tokenValueDisplay" style="font-size:12px;word-break:break-all;flex:1;background:none;border:none;padding:0" class="copy-target"></code>
                            <button class="btn btn-accent btn-custom btn-sm flex-shrink-0" @click="copyTokenValue()" id="copyTokenBtn">
                                <i class="bi bi-clipboard me-1"></i><?php echo e(__('developer.copy_token')); ?>

                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-2">
                    <button type="button" class="btn btn-accent btn-custom px-4" data-bs-dismiss="modal" @click="setTimeout(() => location.reload(), 100)">
                        <i class="bi bi-check-lg me-1"></i><?php echo e(__('general.got_it')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="renameTokenModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form method="POST" class="modal-content" id="renameTokenForm">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('developer.rename_token')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label class="form-label-custom"><?php echo e(__('developer.token_name')); ?></label>
                        <input type="text" name="name" id="renameTokenInput" class="form-custom" required maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-accent" data-bs-dismiss="modal"><?php echo e(__('developer.cancel')); ?></button>
                    <button type="submit" class="btn btn-accent btn-custom"><?php echo e(__('general.save')); ?></button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="modal fade" id="tokenDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width:520px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tokenDetailsModalTitle"><?php echo e(__('developer.token_name')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="tokenDetailsBody" style="display:flex;flex-direction:column;gap:10px"></div>

                    
                    <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-key-fill" style="color:var(--accent);font-size:14px"></i>
                            <span style="font-size:13px;font-weight:600"><?php echo e(__('developer.view_token_title')); ?></span>
                        </div>
                        <p class="small text-muted mb-2" id="detailsViewTokenDesc"><?php echo e(__('developer.view_token_desc')); ?></p>
                        <div>
                            <?php if (isset($component)) { $__componentOriginalb37ff04c7d1d761340845e7d275eabcc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.password-input','data' => ['id' => 'detailsViewTokenPassword','placeholder' => ''.e(__('developer.enter_password')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('password-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'detailsViewTokenPassword','placeholder' => ''.e(__('developer.enter_password')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $attributes = $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $component = $__componentOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>
                        </div>
                        <div id="detailsViewTokenError" class="text-danger small mt-1 d-none"></div>
                        <button class="btn btn-accent btn-custom btn-sm w-100 mt-2" @click="confirmDetailsViewToken()" id="detailsViewTokenBtn">
                            <span id="detailsViewTokenBtnText"><?php echo e(__('developer.view_token')); ?></span>
                            <div class="spinner-border spinner-border-sm d-none" id="detailsViewTokenSpinner"></div>
                        </button>
                        <div id="detailsTokenReveal" class="d-none mt-2">
                            <div class="d-flex align-items-center gap-1" style="background:var(--bg-subtle);border:2px dashed var(--accent);border-radius:8px;padding:10px 12px">
                                <code id="detailsTokenFullDisplay" style="font-size:12px;word-break:break-all;flex:1;background:none;border:none;padding:0" class="copy-target"></code>
                                <button class="btn btn-accent btn-custom btn-sm flex-shrink-0" @click="copyDetailsToken()" id="copyDetailsTokenBtn">
                                    <i class="bi bi-clipboard me-1"></i><?php echo e(__('developer.copy_token')); ?>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-accent btn-custom" data-bs-dismiss="modal"><?php echo e(__('general.close')); ?></button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    let currentTokenValue = '';
    let detailsTokenTargetId = null;

    function showTokenCreated(tokenVal) {
        currentTokenValue = tokenVal;
        document.getElementById('tokenValueDisplay').textContent = tokenVal;
        new bootstrap.Modal(document.getElementById('tokenCreatedModal')).show();
    }

    function copyTokenValue() {
        const btn = document.getElementById('copyTokenBtn');
        navigator.clipboard.writeText(currentTokenValue).then(() => {
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i><?php echo e(__('general.copied')); ?>';
            btn.className = 'btn btn-success btn-custom btn-sm flex-shrink-0';
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-clipboard me-1"></i><?php echo e(__('developer.copy_token')); ?>';
                btn.className = 'btn btn-accent btn-custom btn-sm flex-shrink-0';
            }, 2500);
        });
    }

    function copyToClipboard(btn, text) {
        navigator.clipboard.writeText(text).then(() => {
            const icon = btn.querySelector('i');
            icon.className = 'bi bi-check-lg';
            setTimeout(() => { icon.className = 'bi bi-clipboard'; }, 2000);
        });
    }

    function copyDetailsToken() {
        const btn = document.getElementById('copyDetailsTokenBtn');
        navigator.clipboard.writeText(currentTokenValue).then(() => {
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i><?php echo e(__('general.copied')); ?>';
            btn.className = 'btn btn-success btn-custom btn-sm flex-shrink-0';
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-clipboard me-1"></i><?php echo e(__('developer.copy_token')); ?>';
                btn.className = 'btn btn-accent btn-custom btn-sm flex-shrink-0';
            }, 2500);
        });
    }

    function showTokenFullDetails(id, name, createdAt, lastUsed, abilities, expiresAt, isDeactivated) {
        detailsTokenTargetId = id;
        const titleEl = document.getElementById('tokenDetailsModalTitle');
        const bodyEl = document.getElementById('tokenDetailsBody');
        const revealEl = document.getElementById('detailsTokenReveal');
        const passwordInput = document.getElementById('detailsViewTokenPassword');
        const errorEl = document.getElementById('detailsViewTokenError');

        revealEl.classList.add('d-none');
        passwordInput.value = '';
        errorEl.classList.add('d-none');
        document.getElementById('detailsViewTokenBtnText').classList.remove('d-none');
        document.getElementById('detailsViewTokenSpinner').classList.add('d-none');
        document.getElementById('detailsViewTokenBtn').disabled = false;

        if (titleEl) titleEl.textContent = name;
        if (bodyEl) {
            const abilitiesHtml = abilities.map(a =>
                '<span class="badge" style="background:var(--bg-subtle);color:var(--text-color);font-size:11px;padding:3px 10px;border-radius:20px;font-weight:500;border:1px solid var(--border)">' + a + '</span>'
            ).join(' ');
            const statusBadge = isDeactivated
                ? '<span class="badge" style="background:var(--warning-light, #fef3c7);color:var(--warning);font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600"><?php echo e(__('developer.deactivated')); ?></span>'
                : '<span class="badge" style="background:var(--accent-light, rgba(21,183,108,0.12));color:var(--accent);font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600"><?php echo e(__('general.active')); ?></span>';

            bodyEl.innerHTML = `
                <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-muted)"><?php echo e(__('developer.token_name')); ?></span>
                    <span style="font-size:13px;font-weight:600">${name}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-muted)"><?php echo e(__('developer.status')); ?></span>
                    <span style="font-size:13px">${statusBadge}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-muted)"><?php echo e(__('developer.created')); ?></span>
                    <span style="font-size:13px">${createdAt}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-muted)"><?php echo e(__('developer.expires')); ?></span>
                    <span style="font-size:13px">${expiresAt}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-muted)"><?php echo e(__('developer.last_used')); ?></span>
                    <span style="font-size:13px">${lastUsed}</span>
                </div>
                <div class="py-2 px-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border)">
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px"><?php echo e(__('developer.abilities')); ?> (${abilities.length})</div>
                    <div style="display:flex;flex-wrap:wrap;gap:4px">${abilitiesHtml}</div>
                </div>
            `;
        }

        document.getElementById('detailsViewTokenDesc').textContent = isDeactivated
            ? '<?php echo e(__('developer.token_deactivated_desc')); ?>'
            : '<?php echo e(__('developer.view_token_desc')); ?>';
        if (isDeactivated) {
            document.getElementById('detailsViewTokenPassword').disabled = true;
            document.getElementById('detailsViewTokenBtn').disabled = true;
        } else {
            document.getElementById('detailsViewTokenPassword').disabled = false;
            document.getElementById('detailsViewTokenBtn').disabled = false;
        }

        new bootstrap.Modal(document.getElementById('tokenDetailsModal')).show();
    }

    function confirmDetailsViewToken() {
        const password = document.getElementById('detailsViewTokenPassword').value;
        if (!password) {
            showDetailsError('<?php echo e(__('validation.required', ['attribute' => __('developer.password')])); ?>');
            return;
        }

        const btn = document.getElementById('detailsViewTokenBtn');
        const btnText = document.getElementById('detailsViewTokenBtnText');
        const spinner = document.getElementById('detailsViewTokenSpinner');
        const errorEl = document.getElementById('detailsViewTokenError');
        const revealEl = document.getElementById('detailsTokenReveal');

        btn.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');
        errorEl.classList.add('d-none');
        revealEl.classList.add('d-none');

        fetch('<?php echo e(route('account.settings.developer.show', '__ID__')); ?>'.replace('__ID__', detailsTokenTargetId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ password: password })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');

            if (data.token) {
                currentTokenValue = data.token;
                document.getElementById('detailsTokenFullDisplay').textContent = data.token;
                revealEl.classList.remove('d-none');
            } else {
                showDetailsError(data.message || '<?php echo e(__('developer.invalid_password')); ?>');
            }
        })
        .catch(e => {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');
            showDetailsError('<?php echo e(__('general.connection_failed')); ?>');
        });
    }

    function showDetailsError(msg) {
        const errorEl = document.getElementById('detailsViewTokenError');
        errorEl.textContent = msg;
        errorEl.classList.remove('d-none');
    }

    function showRenameModal(id, currentName) {
        const form = document.getElementById('renameTokenForm');
        form.action = '<?php echo e(route('account.settings.developer.update', '__ID__')); ?>'.replace('__ID__', id);
        document.getElementById('renameTokenInput').value = currentName;
        new bootstrap.Modal(document.getElementById('renameTokenModal')).show();
    }

    function testApiToken() {
        const resultEl = document.getElementById('apiTestResult');
        resultEl.className = 'mt-2';
        resultEl.innerHTML = '<div class="d-flex align-items-center gap-2 text-muted small"><div class="spinner-border spinner-border-sm"></div> <?php echo e(__('general.testing')); ?></div>';
        resultEl.classList.remove('d-none');

        fetch('<?php echo e(url('/api/workspace')); ?>', {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const isOk = data && !data.message && data.data;
            resultEl.innerHTML = isOk
                ? '<div class="d-flex align-items-center gap-2 small" style="color:var(--success)"><i class="bi bi-check-circle-fill"></i> <?php echo e(__('general.connection_ok')); ?></div>'
                : '<div class="d-flex align-items-center gap-2 small" style="color:var(--danger)"><i class="bi bi-x-circle-fill"></i> ' + (data.message || '<?php echo e(__('general.connection_failed')); ?>') + '</div>';
        })
        .catch(e => {
            resultEl.innerHTML = '<div class="d-flex align-items-center gap-2 small" style="color:var(--danger)"><i class="bi bi-x-circle-fill"></i> <?php echo e(__('general.connection_failed')); ?></div>';
        });
    }

    function initDeveloperPage() {
        var el = document.getElementById('new-token-value');
        if (el && el.dataset.token) {
            setTimeout(function() { showTokenCreated(el.dataset.token); }, 300);
        }
    }
    if (!window._developerNavListener) {
        document.addEventListener('livewire:navigated', initDeveloperPage);
        window._developerNavListener = true;
    }
    </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\settings\developer.blade.php ENDPATH**/ ?>