<?php

use App\Enums\PaymentStatus;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\Payments\Noest\NoestErrorHandler;
use App\Services\Payments\Noest\NoestService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<div class="onboarding-wrapper ">
    <div class="onboarding-header">
        <div class="auth-logo">
            <div class="logo-icon">FM</div>
            <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
            <span class="logo-sub"><?php echo e(__('onboarding.choose_plan')); ?></span>
        </div>
        <p class="onboarding-desc"><?php echo e(__('onboarding.plan_description')); ?></p>
    </div>

    <div class="billing-toggle">
        <span class="billing-label <?php echo e($billingPeriod === 'monthly' ? 'active' : ''); ?>"><?php echo e(__('onboarding.monthly')); ?></span>
        <button type="button" class="toggle-switch <?php echo e($billingPeriod === 'yearly' ? 'active' : ''); ?>"
            wire:click="toggleBilling" role="switch">
            <span class="toggle-knob <?php echo e($billingPeriod === 'yearly' ? 'on' : ''); ?>"></span>
        </button>
        <span class="billing-label <?php echo e($billingPeriod === 'yearly' ? 'active' : ''); ?>"><?php echo e(__('onboarding.yearly')); ?></span>
    </div>

    <div class="plan-grid">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $isPopular = $index === 1;
            $isFree = $plan['is_free'] ?? false;
            $monthlyPrice = $plan['monthly_price'] ?? 0;
            $yearlyPrice = $plan['yearly_price'] ?? 0;
            $displayPrice = $billingPeriod === 'yearly' ? $yearlyPrice : $monthlyPrice;
            $savings = $this->yearlySavings($plan);
            $savingsPercent = $this->yearlySavingsPercent($plan);
            $features = $plan['_features'] ?? [];
            $planPrices = $plan['_prices'] ?? [];
        ?>
        <div class="plan-card <?php echo e($selectedPlanId === $plan['id'] ? 'selected' : ''); ?> <?php echo e($isPopular ? 'popular' : ''); ?>"
            wire:click="selectPlan(<?php echo e($plan['id']); ?>)" role="button" tabindex="0"
            wire:key="plan-<?php echo e($plan['id']); ?>"
            x-data="{ showAll: false }">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPopular): ?>
                <div class="plan-badge"><?php echo e(__('onboarding.popular')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="plan-card-content">
                <div class="plan-name"><?php echo e($plan['name']); ?></div>

                <div class="plan-price">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFree): ?>
                        <span class="price-amount"><?php echo e(__('onboarding.free')); ?></span>
                    <?php else: ?>
                        <?php
                            $currentPrice = $billingPeriod === 'yearly'
                                ? $yearlyPrice
                                : (collect($planPrices)->firstWhere('billing_period', 'monthly')['price'] ?? $monthlyPrice);
                            $originalPrice = null;
                        ?>
                        <span class="price-amount"><?php echo e($this->displayPrice((float) $currentPrice)); ?></span>
                        <span class="price-period"><?php echo e($billingPeriod === 'yearly' ? __('onboarding.per_year') : __('onboarding.per_month')); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($originalPrice && $originalPrice > $currentPrice): ?>
                            <span class="price-original"><?php echo e($this->displayPrice((float) $originalPrice)); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($billingPeriod === 'yearly' && $savingsPercent): ?>
                    <div class="plan-savings">
                        <?php echo e(__('onboarding.save_percent', ['percent' => $savingsPercent])); ?>

                        <span class="plan-savings-amount"><?php echo e($this->displayPrice($savings)); ?></span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan['description'] ?? null): ?>
                    <p class="plan-desc"><?php echo e($plan['description']); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <ul class="plan-features">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($features)): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i >= 5): ?>
                                <li x-show="showAll" x-cloak>
                            <?php else: ?>
                                <li>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feature['icon']): ?>
                                    <i class="<?php echo e($feature['icon']); ?>"></i>
                                <?php else: ?>
                                    <i class="bi bi-check-circle-fill"></i>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php
                                    $nameKey = 'name_' . app()->getLocale();
                                    $name = $feature[$nameKey] ?? $feature['name_en'];
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feature['type'] === 'boolean'): ?>
                                    <?php echo e($name); ?>

                                <?php elseif($feature['value']): ?>
                                    <?php echo e($feature['value']); ?> <?php echo e($name); ?>

                                <?php else: ?>
                                    <?php echo e($name); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($features) > 5): ?>
                            <li class="plan-features-toggle" style="list-style:none;text-align:center;padding:4px 0 0">
                                <button type="button" @click.stop="showAll = !showAll" style="background:none;border:none;color:var(--accent);font-size:12px;cursor:pointer;padding:4px 0">
                                    <span x-show="!showAll"><?php echo e(__('onboarding.show_more')); ?> (<?php echo e(count($features) - 5); ?>) <i class="bi bi-chevron-down"></i></span>
                                    <span x-show="showAll" x-cloak><?php echo e(__('onboarding.show_less')); ?> <i class="bi bi-chevron-up"></i></span>
                                </button>
                            </li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (is_array($plan['features'] ?? null) ? $plan['features'] : []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><i class="bi bi-check-circle-fill"></i> <?php echo e($feature); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>

            <div class="plan-select-indicator">
                <div class="radio-circle <?php echo e($selectedPlanId === $plan['id'] ? 'checked' : ''); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedPlanId === $plan['id']): ?>
                        <i class="bi bi-check-lg"></i>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['selectedPlanId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="onboarding-error"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

   <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedPlan && !($selectedPlan['is_free'] ?? false)): ?>
        <div class="payment-section">
            <div class="payment-section-header">
                <i class="bi bi-credit-card-2-front"></i>
                <span><?php echo e(__('onboarding.select_payment')); ?></span>
            </div>
            <div style="font-size:12px;color:var(--text-muted,#888);margin-bottom:10px;display:flex;align-items:center;gap:6px">
                <i class="bi bi-info-circle"></i>
                <?php echo e(__('onboarding.payment_methods_for_currency', ['currency' => \App\Services\CurrencyHelper::symbol(session('currency', auth()->user()?->currency ?? config('finance.currency', 'DZD')))])); ?>

            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
                <div class="alert alert-danger py-2 small"><?php echo e($errorMessage); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="payment-methods">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="payment-method-card <?php echo e($paymentMethod === $method['id'] ? 'selected' : ''); ?>"
                        wire:click="setPaymentMethod('<?php echo e($method['id']); ?>')" role="button" tabindex="0">
                        <div class="method-radio">
                            <div class="method-radio-circle <?php echo e($paymentMethod === $method['id'] ? 'checked' : ''); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod === $method['id']): ?>
                                    <i class="bi bi-check-lg"></i>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <i class="method-icon <?php echo e($method['icon']); ?>"></i>
                        <span class="method-name"><?php echo e($method['name']); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['paymentMethod'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod && App\Services\OnboardingService::isManual($paymentMethod)): ?>
                <div class="alert alert-info py-2 small mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle me-1"></i><?php echo e(__('onboarding.manual_confirm_hint')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod === 'noest'): ?>
                <div class="noest-form mb-3">
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

            <div class="coupon-section">
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

            <?php $_feeBrk = $this->feeBreakdown; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod && $_feeBrk): ?>
                <div class="price-breakdown mb-3">
                    <div class="price-row original">
                        <span><?php echo e(__('onboarding.plan_price')); ?></span>
                        <span><?php echo e($this->displayPrice((float) $_feeBrk['original_usd'])); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($_feeBrk['discount_usd'] ?? 0) > 0): ?>
                    <div class="price-row discount">
                        <span><?php echo e(__('onboarding.coupon_discount')); ?></span>
                        <span>-<?php echo e($this->displayPrice((float) $_feeBrk['discount_usd'])); ?></span>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($_feeBrk['gateway_fee_usd'] ?? 0) > 0): ?>
                    <div class="price-row fee">
                        <span><?php echo e(__('onboarding.gateway_fee')); ?></span>
                        <span>+<?php echo e($this->displayPrice((float) $_feeBrk['gateway_fee_usd'])); ?></span>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($_feeBrk['tax_added_usd'] ?? 0) > 0): ?>
                    <div class="price-row fee">
                        <span><?php echo e(__('onboarding.tax_added')); ?></span>
                        <span>+<?php echo e($this->displayPrice((float) $_feeBrk['tax_added_usd'])); ?></span>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($_feeBrk['tax_disclosed_usd'] ?? 0) > 0): ?>
                    <div class="price-row">
                        <span><?php echo e(__('onboarding.tax_disclosed')); ?></span>
                        <span><?php echo e($this->displayPrice((float) $_feeBrk['tax_disclosed_usd'])); ?></span>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="price-divider"></div>
                    <div class="price-row total <?php echo e(($_feeBrk['total_usd'] ?? 0) <= 0 ? 'free' : ''); ?>">
                        <span><?php echo e(__('onboarding.total')); ?></span>
                        <span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($_feeBrk['total_usd'] ?? 0) <= 0): ?>
                                <?php echo e(__('onboarding.free')); ?>

                            <?php else: ?>
                                <?php echo e($this->displayPrice((float) $_feeBrk['total_usd'])); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <button type="button" class="btn btn-accent btn-custom w-100 pay-btn"
                wire:click="pay" wire:loading.attr="disabled" wire:target="pay"
                <?php if($isProcessing): echo 'disabled'; endif; ?>>
                <span wire:loading.remove wire:target="pay"><?php echo e(__('onboarding.pay_now')); ?></span>
                <span wire:loading wire:target="pay"><?php echo e(__('onboarding.processing_payment')); ?></span>
            </button>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($redirectUrl): ?>
                <div class="text-center mt-3">
                    <p class="small text-muted"><?php echo e(__('onboarding.redirecting')); ?></p>
                    <a href="<?php echo e($redirectUrl); ?>" class="btn btn-accent btn-sm" target="_blank" rel="noopener">
                        <?php echo e(__('onboarding.proceed_to_payment')); ?>

                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php elseif($selectedPlan && ($selectedPlan['is_free'] ?? false)): ?>
        <button type="button" class="btn btn-accent btn-custom w-100 proceed-btn"
            wire:click="proceed" wire:loading.attr="disabled" wire:target="proceed">
            <?php echo e(__('onboarding.continue')); ?>

        </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="onboarding-footer">
        <a href="<?php echo e(route('logout')); ?>"
            @click="$event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right me-1"></i><?php echo e(__('onboarding.sign_out')); ?>

        </a>
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
    </div>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire/pages/onboarding/plan.blade.php ENDPATH**/ ?>