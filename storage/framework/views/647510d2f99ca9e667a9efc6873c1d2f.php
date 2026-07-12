<?php

use App\Enums\PaymentStatus;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\SubscriptionPlan;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\Payments\Noest\NoestErrorHandler;
use App\Services\Payments\Noest\NoestService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<div class="auth-card animate-fade-in" x-data="noestForm()">
    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
        <span class="logo-sub"><?php echo e(__('onboarding.payment')); ?></span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
        <?php
            $usdPrice = $billingPeriod === 'yearly' ? $plan['yearly_price'] : $plan['monthly_price'];
            $userCurrency = auth()->user()?->currency ?? config('finance.currency', 'DZD');
            $converted = CurrencyHelper::fromUsd($usdPrice, $userCurrency);
        ?>
        <div class="text-center mb-3">
            <h5><?php echo e($plan['name']); ?></h5>
            <p class="h3 mb-2">
                <?php echo e(number_format($converted, 2)); ?>

                <small><?php echo e(CurrencyHelper::symbol($userCurrency)); ?></small>
            </p>
        </div>
        <?php $_features = $plan['_features'] ?? []; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($_features)): ?>
            <div class="mb-4" x-data="{ showAll: false }">
                <div class="plan-features" style="border:1px solid var(--border);border-radius:8px;padding:12px 16px;">
                    <div style="font-weight:600;font-size:13px;margin-bottom:8px;color:var(--text-muted)"><?php echo e(__('onboarding.what_included')); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $_features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $feat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i >= 5): ?>
                            <div x-show="showAll" x-cloak style="border-bottom:1px solid var(--border);padding:6px 0">
                                <span style="font-size:13px">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feat['icon']): ?>
                                        <i class="<?php echo e($feat['icon']); ?>" style="margin-inline-end:6px;color:var(--accent)"></i>
                                    <?php else: ?>
                                        <i class="bi bi-check-circle-fill" style="margin-inline-end:6px;color:var(--accent);font-size:12px"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php
                                        $_nameKey = 'name_' . app()->getLocale();
                                        $_name = $feat[$_nameKey] ?? $feat['name_en'];
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feat['type'] === 'boolean'): ?>
                                        <?php echo e($_name); ?>

                                    <?php elseif($feat['value']): ?>
                                        <?php echo e($feat['value']); ?> <?php echo e($_name); ?>

                                    <?php else: ?>
                                        <?php echo e($_name); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <div style="border-bottom:1px solid var(--border);padding:6px 0">
                                <span style="font-size:13px">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feat['icon']): ?>
                                        <i class="<?php echo e($feat['icon']); ?>" style="margin-inline-end:6px;color:var(--accent)"></i>
                                    <?php else: ?>
                                        <i class="bi bi-check-circle-fill" style="margin-inline-end:6px;color:var(--accent);font-size:12px"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php
                                        $_nameKey = 'name_' . app()->getLocale();
                                        $_name = $feat[$_nameKey] ?? $feat['name_en'];
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feat['type'] === 'boolean'): ?>
                                        <?php echo e($_name); ?>

                                    <?php elseif($feat['value']): ?>
                                        <?php echo e($feat['value']); ?> <?php echo e($_name); ?>

                                    <?php else: ?>
                                        <?php echo e($_name); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($_features) > 5): ?>
                        <button type="button" @click="showAll = !showAll" style="background:none;border:none;color:var(--accent);font-size:12px;padding:8px 0 0;cursor:pointer;width:100%;text-align:center">
                            <span x-show="!showAll"><?php echo e(__('onboarding.show_more')); ?> (<?php echo e(count($_features) - 5); ?>) <i class="bi bi-chevron-down"></i></span>
                            <span x-show="showAll" x-cloak><?php echo e(__('onboarding.show_less')); ?> <i class="bi bi-chevron-up"></i></span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
        <div class="alert alert-danger py-2 small" style="white-space:pre-wrap;"><?php echo e($errorMessage); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mb-4">
        <label class="form-label-custom"><?php echo e(__('onboarding.payment_method')); ?></label>
        <div style="font-size:12px;color:var(--text-muted,#888);margin-bottom:8px;display:flex;align-items:center;gap:6px">
            <i class="bi bi-info-circle"></i>
            <?php echo e(__('onboarding.payment_methods_for_currency', ['currency' => \App\Services\CurrencyHelper::symbol(session('currency', auth()->user()?->currency ?? config('finance.currency', 'DZD')))])); ?>

        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="paymentMethod"
                    id="method-<?php echo e($method['id']); ?>" value="<?php echo e($method['id']); ?>"
                    wire:model="paymentMethod">
                <label class="form-check-label d-flex align-items-center gap-2" for="method-<?php echo e($method['id']); ?>">
                    <i class="<?php echo e($method['icon']); ?>"></i>
                    <?php echo e($method['name']); ?>

                </label>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentMethod'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod === 'noest'): ?>
        <div class="noest-form mb-4">
            <div class="noest-form-header">
                <i class="bi bi-truck"></i>
                <span><?php echo e(__('onboarding.noest_delivery_info')); ?></span>
            </div>

            <div class="form-floating-group mb-3">
                <input type="text" id="noest_client" class="form-control" wire:model="noestClient" placeholder=" " <?php if($isProcessing): echo 'disabled'; endif; ?>>
                <label for="noest_client"><?php echo e(__('onboarding.noest_client')); ?> <span class="text-danger">*</span></label>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['noestClient'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <div class="form-floating-group">
                        <input type="text" id="noest_phone" class="form-control" wire:model="noestPhone" placeholder=" " <?php if($isProcessing): echo 'disabled'; endif; ?>>
                        <label for="noest_phone"><?php echo e(__('onboarding.noest_phone')); ?> <span class="text-danger">*</span></label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['noestPhone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating-group">
                        <input type="text" id="noest_phone_2" class="form-control" wire:model="noestPhone2" placeholder=" " <?php if($isProcessing): echo 'disabled'; endif; ?>>
                        <label for="noest_phone_2"><?php echo e(__('onboarding.noest_phone_2')); ?></label>
                    </div>
                </div>
            </div>

            <div class="form-floating-group mb-3">
                <input type="text" id="noest_adresse" class="form-control" wire:model="noestAdresse" placeholder=" " <?php if($isProcessing): echo 'disabled'; endif; ?>>
                <label for="noest_adresse"><?php echo e(__('onboarding.noest_adresse')); ?> <span class="text-danger">*</span></label>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['noestAdresse'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php
                $_wilayaItems = array_values(array_map(fn($w) => [
                    'code' => (string) ($w['code'] ?? $w['id'] ?? ''),
                    'nom'  => $w['nom'] ?? $w['name'] ?? '',
                ], $this->noestWilayas));
            ?>
            <div class="form-floating-group mb-3">
                <div class="noest-search-group"
                     x-data="{ search: '', items: [] }"
                     x-init="items = JSON.parse($el.dataset.items)"
                     data-items="<?php echo e(json_encode($_wilayaItems)); ?>">
                    <div class="form-floating">
                        <input type="text" x-model="search" class="form-control" id="noest_wilaya_search" placeholder="<?php echo e(__('onboarding.noest_search_wilaya')); ?>" autocomplete="off" <?php if($isProcessing): ?> disabled <?php endif; ?>>
                        <label for="noest_wilaya_search"><?php echo e(__('onboarding.noest_wilaya')); ?> <span class="text-danger">*</span></label>
                    </div>
                    <select wire:model.live="noestWilaya" class="form-select" <?php if($isProcessing): ?> disabled <?php endif; ?>>
                        <option value="">-- <?php echo e(__('onboarding.noest_select_wilaya')); ?> --</option>
                        <template x-for="item in items.filter(i => !search || i.nom.toLowerCase().includes(search.toLowerCase()) || i.code.includes(search))" :key="item.code">
                            <option :value="item.code" x-text="item.code + ' - ' + item.nom"></option>
                        </template>
                    </select>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['noestWilaya'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php
                $_deskItems = $this->noestDesksForWilaya();
                $_deskItems = array_values(array_map(fn($d) => [
                    'code' => (string) ($d['code'] ?? $d['id'] ?? ''),
                    'nom'  => $d['nom'] ?? $d['name'] ?? $d['desk_name'] ?? '',
                ], $_deskItems));
            ?>
            <div class="form-floating-group mb-3" wire:key="noest-desk-wrapper-<?php echo e($noestWilaya ?: 'none'); ?>">
                <div class="noest-search-group"
                     x-data="{ search: '', items: [] }"
                     x-init="items = JSON.parse($el.dataset.items)"
                     data-items="<?php echo e(json_encode($_deskItems)); ?>">
                    <div class="form-floating">
                        <input type="text" x-model="search" class="form-control" id="noest_desk_search" placeholder="<?php echo e(__('onboarding.noest_search_desk')); ?>" autocomplete="off" <?php if(!$noestWilaya || $isProcessing): ?> disabled <?php endif; ?>>
                        <label for="noest_desk_search"><?php echo e(__('onboarding.noest_stop_desk')); ?></label>
                    </div>
                    <select wire:model.live="noestDeskId" class="form-select" <?php if(!$noestWilaya || $isProcessing): ?> disabled <?php endif; ?>>
                        <option value="">-- <?php echo e(__('onboarding.noest_select_desk')); ?> --</option>
                        <template x-for="item in items.filter(i => !search || i.nom.toLowerCase().includes(search.toLowerCase()) || i.code.includes(search))" :key="item.code">
                            <option :value="item.code" x-text="item.code + ' - ' + item.nom"></option>
                        </template>
                    </select>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($noestWilaya && !count($_deskItems)): ?>
                    <div class="noest-no-desks"><?php echo e(__('onboarding.noest_no_desks_for_wilaya')); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod === 'chargily'): ?>
        <div class="alert alert-info py-2 mb-0 d-flex align-items-center gap-2 small">
            <i class="bi bi-info-circle me-1"></i><?php echo e(__('onboarding.chargily_auto_hint')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mb-4">
        <label for="couponCode" class="form-label-custom"><?php echo e(__('onboarding.coupon')); ?></label>
        <div class="coupon-input-wrapper">
            <input type="text" id="couponCode" class="form-custom coupon-input"
                wire:model.live.debounce.300ms="couponCode"
                placeholder="<?php echo e(__('onboarding.coupon_placeholder')); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($couponCode && !$this->couponValidation): ?>
                <span class="coupon-spinner"><i class="bi bi-arrow-repeat"></i></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->couponValidation): ?>
                <span class="coupon-status <?php echo e($this->couponValidation['valid'] ? 'valid' : 'invalid'); ?>">
                    <i class="bi <?php echo e($this->couponValidation['valid'] ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'); ?>"></i>
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->couponValidation): ?>
            <div class="coupon-message <?php echo e($this->couponValidation['valid'] ? 'text-success' : 'text-danger'); ?>">
                <?php echo e($this->couponValidation['message']); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php $fees = $this->feeBreakdown; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod && $fees): ?>
        <div class="price-breakdown mb-3">
            <div class="price-row original">
                <span><?php echo e(__('onboarding.plan_price')); ?></span>
                <span><?php echo e($this->displayPrice((float) $fees['original_usd'])); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($fees['discount_usd'] ?? 0) > 0): ?>
            <div class="price-row discount">
                <span><?php echo e(__('onboarding.coupon_discount')); ?></span>
                <span>-<?php echo e($this->displayPrice((float) $fees['discount_usd'])); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($fees['gateway_fee_usd'] ?? 0) > 0): ?>
            <div class="price-row fee">
                <span><?php echo e(__('onboarding.gateway_fee')); ?></span>
                <span>+<?php echo e($this->displayPrice((float) $fees['gateway_fee_usd'])); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($fees['tax_added_usd'] ?? 0) > 0): ?>
            <div class="price-row fee">
                <span><?php echo e(__('onboarding.tax_added')); ?></span>
                <span>+<?php echo e($this->displayPrice((float) $fees['tax_added_usd'])); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($fees['tax_disclosed_usd'] ?? 0) > 0): ?>
            <div class="price-row">
                <span><?php echo e(__('onboarding.tax_disclosed')); ?></span>
                <span><?php echo e($this->displayPrice((float) $fees['tax_disclosed_usd'])); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="price-divider"></div>
            <div class="price-row total <?php echo e(($fees['total_usd'] ?? 0) <= 0 ? 'free' : ''); ?>">
                <span><?php echo e(__('onboarding.total')); ?></span>
                <span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($fees['total_usd'] ?? 0) <= 0): ?>
                        <?php echo e(__('onboarding.free')); ?>

                    <?php else: ?>
                        <?php echo e($this->displayPrice((float) $fees['total_usd'])); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <button type="button" class="btn btn-accent btn-custom w-100" wire:click="pay"
        wire:loading.attr="disabled" wire:target="pay" <?php if($isProcessing): echo 'disabled'; endif; ?>>
        <div wire:loading wire:target="pay" class="spinner-border spinner-border-sm me-2" role="status"></div>
        <span wire:loading.remove wire:target="pay"><?php echo e(__('onboarding.pay_now')); ?></span>
        <span wire:loading wire:target="pay"><?php echo e(__('onboarding.processing_payment')); ?></span>
    </button>

    <div class="auth-footer mt-3">
        <a href="<?php echo e(route('onboarding.plan')); ?>" wire:navigate><?php echo e(__('onboarding.back_to_plans')); ?></a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($redirectUrl): ?>
        <div class="text-center mt-3">
            <p class="small text-muted"><?php echo e(__('onboarding.redirecting')); ?></p>
            <a href="<?php echo e($redirectUrl); ?>" class="btn btn-accent btn-sm">
                <?php echo e(__('onboarding.proceed_to_payment')); ?>

            </a>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire/pages/onboarding/payment.blade.php ENDPATH**/ ?>