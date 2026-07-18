<?php

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\PaymentService;
use App\Services\Payments\GatewayManager;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

?>

<div>
    <div class="auth-card animate-fade-in">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($view === 'waiting'): ?>
            <style>
                .payment-ring { width:80px;height:80px;position:relative;margin:0 auto; }
                .payment-ring .ring { position:absolute;inset:0;border-radius:50%;border:3px solid transparent;animation:ring-spin 1.5s cubic-bezier(0.5,0,0.5,1) infinite; }
                .payment-ring .ring:nth-child(1) { border-top-color:var(--accent);animation-delay:0s; }
                .payment-ring .ring:nth-child(2) { border-right-color:var(--info);animation-delay:0.2s; }
                .payment-ring .ring:nth-child(3) { border-bottom-color:var(--success);animation-delay:0.4s; }
                .payment-ring .ring:nth-child(4) { border-left-color:var(--warning);animation-delay:0.6s; }
                @keyframes ring-spin { 0%{transform:rotate(0deg)}100%{transform:rotate(360deg)} }
                .payment-ring .check-icon { position:absolute;inset:12px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;color:var(--accent);opacity:0.7; }
                .pulse-dot { display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--accent);margin:0 3px;animation:pulse-dot 1.4s ease-in-out infinite; }
                .pulse-dot:nth-child(2) { animation-delay:0.2s; }
                .pulse-dot:nth-child(3) { animation-delay:0.4s; }
                @keyframes pulse-dot { 0%,80%,100%{opacity:0.3;transform:scale(0.8)}40%{opacity:1;transform:scale(1.2)} }
                .progress-glow { height:4px;border-radius:4px;background:var(--border);overflow:hidden;max-width:240px;margin:0 auto;position:relative; }
                .progress-glow .bar { height:100%;border-radius:4px;background:linear-gradient(90deg,var(--accent),var(--info),var(--accent));background-size:200% 100%;animation:glow-move 2s linear infinite;width:<?php echo e(min(100, (($maxPolls - $this->remainingSeconds()/5) / $maxPolls) * 100)); ?>%;transition:width 1s ease; }
                @keyframes glow-move { 0%{background-position:200% 0}100%{background-position:-200% 0} }
            </style>
            <div class="text-center">
                <div class="auth-logo">
                    <div class="logo-icon">FM</div>
                    <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
                </div>
                <div class="my-4">
                    <div class="payment-ring">
                        <div class="ring"></div>
                        <div class="ring"></div>
                        <div class="ring"></div>
                        <div class="ring"></div>
                        <div class="check-icon"><i class="bi bi-credit-card"></i></div>
                    </div>
                </div>
                <p class="fw-semibold"><?php echo e(__('onboarding.processing_payment')); ?></p>
                <p class="small text-muted"><?php echo e(__('onboarding.payment_processing_hint')); ?></p>
                <div class="mt-3">
                    <div class="progress-glow">
                        <div class="bar"></div>
                    </div>
                    <p class="small text-muted mt-2"><span class="fw-medium"><?php echo e($this->remainingSeconds()); ?>s</span> <?php echo e(__('onboarding.remaining')); ?></p>
                </div>
                <div class="mt-3">
                    <span class="pulse-dot"></span>
                    <span class="pulse-dot"></span>
                    <span class="pulse-dot"></span>
                </div>
                <div class="mt-3">
                    <button wire:click="checkStatus" class="btn btn-outline-accent btn-custom btn-sm">
                        <i class="bi bi-arrow-repeat me-1"></i><?php echo e(__('onboarding.check_status')); ?>

                    </button>
                </div>
                <div wire:poll.5s="checkStatus" class="d-none"></div>
            </div>

        
        <?php elseif($view === 'completed'): ?>
            <style>
                .success-anim { width:80px;height:80px;margin:0 auto;position:relative; }
                .success-anim .circle { width:80px;height:80px;border-radius:50%;background:var(--success);display:flex;align-items:center;justify-content:center;animation:success-pop 0.5s cubic-bezier(0.175,0.885,0.32,1.275); }
                .success-anim .circle i { font-size:2.5rem;color:#fff;animation:success-check 0.4s 0.2s both; }
                @keyframes success-pop { 0%{transform:scale(0);opacity:0}100%{transform:scale(1);opacity:1} }
                @keyframes success-check { 0%{transform:scale(0) rotate(-45deg)}100%{transform:scale(1) rotate(0deg)} }
                .redirect-timer { display:inline-block;width:12px;height:12px;border:2px solid var(--accent);border-top-color:transparent;border-radius:50%;animation:spin 0.8s linear infinite;vertical-align:middle;margin-right:6px; }
                @keyframes spin { to{transform:rotate(360deg)} }
            </style>
            <div class="text-center">
                <div class="auth-logo">
                    <div class="logo-icon">FM</div>
                    <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
                </div>
                <div class="my-4">
                    <div class="success-anim">
                        <div class="circle"><?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'success','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'success','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?></div>
                    </div>
                </div>
                <p class="fw-bold"><?php echo e(__('onboarding.payment_success')); ?></p>
                <p class="small text-muted"><?php echo e(__('onboarding.proof_approved_desc')); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($autoRedirecting): ?>
                <p class="small text-accent mt-2"><span class="redirect-timer"></span><?php echo e(__('onboarding.redirecting')); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="mt-2">
                    <button wire:click="proceed" class="btn btn-accent btn-custom">
                        <i class="bi bi-arrow-right me-1"></i><?php echo e(__('onboarding.continue')); ?>

                    </button>
                </div>
            </div>

        
        <?php elseif($view === 'failed'): ?>
            <style>
                .fail-anim { width:80px;height:80px;margin:0 auto;position:relative; }
                .fail-anim .circle { width:80px;height:80px;border-radius:50%;background:var(--danger);display:flex;align-items:center;justify-content:center;animation:fail-shake 0.5s cubic-bezier(0.36,0.07,0.19,0.97); }
                .fail-anim .circle i { font-size:2.5rem;color:#fff; }
                @keyframes fail-shake { 0%{transform:translateX(0)}15%{transform:translateX(-8px)}30%{transform:translateX(8px)}45%{transform:translateX(-5px)}60%{transform:translateX(5px)}80%{transform:translateX(-2px)}100%{transform:translateX(0)} }
            </style>
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
            </div>
            <div class="text-center mb-3">
                <div class="fail-anim">
                    <div class="circle"><?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'failed','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'failed','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?></div>
                </div>
            </div>
            <div class="alert alert-danger py-2 small"><?php echo e($error); ?></div>
            <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($autoRedirecting): ?>
            <p class="small text-accent text-center mt-2"><span class="redirect-timer"></span><?php echo e(__('onboarding.redirecting')); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="d-grid gap-2 mt-3">
                <button wire:click="retry" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-repeat me-1"></i><?php echo e(__('onboarding.retry_payment')); ?>

                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment && OnboardingService::isManual($payment->method)): ?>
                <button wire:click="manualProof" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-upload me-1"></i><?php echo e(__('onboarding.upload_manual_proof')); ?>

                </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i><?php echo e(__('onboarding.switch_gateway')); ?>

                </button>
            </div>
            <?php echo $__env->make('livewire.pages.onboarding.partials.auth-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php elseif($view === 'canceled'): ?>
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
            </div>
            <div class="text-center mb-3">
                <div class="text-warning" style="font-size:3rem"><?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'cancelled','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'cancelled','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?></div>
            </div>
            <div class="alert alert-warning py-2 small"><?php echo e($error); ?></div>
            <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($autoRedirecting): ?>
            <p class="small text-accent text-center mt-2"><span class="redirect-timer"></span><?php echo e(__('onboarding.redirecting')); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="d-grid gap-2 mt-3">
                <button wire:click="retry" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-repeat me-1"></i><?php echo e(__('onboarding.retry_payment')); ?>

                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment && OnboardingService::isManual($payment->method)): ?>
                <button wire:click="manualProof" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-upload me-1"></i><?php echo e(__('onboarding.upload_manual_proof')); ?>

                </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i><?php echo e(__('onboarding.switch_gateway')); ?>

                </button>
            </div>
            <?php echo $__env->make('livewire.pages.onboarding.partials.auth-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php elseif($view === 'pending_manual'): ?>
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
            </div>
            <div class="text-center mb-3">
                <div class="text-info" style="font-size:3rem"><i class="bi bi-shield-check"></i></div>
            </div>
            <div class="alert alert-info py-2 small d-flex align-items-center gap-2">
                <i class="bi bi-info-circle"></i><?php echo e(__('onboarding.proof_pending_review_desc')); ?>

            </div>
            <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment && $payment->verification): ?>
                <?php $v = $payment->verification; ?>
                <div class="info-section mb-3">
                    <div class="info-section-header">
                        <i class="bi bi-shield-check me-1"></i><?php echo e(__('onboarding.proof_details_title')); ?>

                    </div>
                    <div class="info-grid">
                        <div class="info-row">
                            <span class="info-label"><?php echo e(__('onboarding.transaction_reference')); ?></span>
                            <span class="info-value" style="direction:ltr;font-family:monospace;font-size:12px"><?php echo e($v->transaction_reference ?? '—'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><?php echo e(__('onboarding.verification_status')); ?></span>
                            <span class="info-value">
                                <?php
                                    $vBadge = match($v->status->value) {
                                        'pending' => ['bg' => 'bg-warning text-dark', 'label' => __('onboarding.status_pending')],
                                        'approved' => ['bg' => 'bg-success text-white', 'label' => __('onboarding.status_approved')],
                                        'rejected' => ['bg' => 'bg-danger text-white', 'label' => __('onboarding.status_rejected')],
                                        default => ['bg' => 'bg-secondary text-white', 'label' => ucfirst($v->status->value)]
                                    };
                                ?>
                                <span class="badge rounded-pill <?php echo e($vBadge['bg']); ?>"><?php echo e($vBadge['label']); ?></span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><?php echo e(__('onboarding.submitted_on')); ?></span>
                            <span class="info-value"><?php echo e($v->created_at->format('d M Y, H:i')); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($v->receipt_path): ?>
                        <div class="info-row">
                            <span class="info-label"><?php echo e(__('onboarding.receipt_preview')); ?></span>
                            <span class="info-value">
                                <button type="button" @click="Livewire.dispatch('openReceiptModal')" style="color:var(--info);font-size:12px;text-decoration:none;border:none;background:none;padding:0;cursor:pointer">
                                    <i class="bi bi-eye me-1"></i><?php echo e(__('onboarding.receipt_preview')); ?>

                                </button>
                            </span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($v->admin_notes): ?>
                        <div class="info-row">
                            <span class="info-label"><?php echo e(__('super-admin.reject_reason') ?? 'Admin Notes'); ?></span>
                            <span class="info-value" style="font-size:12px;max-width:180px;text-align:right"><?php echo e($v->admin_notes); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php echo $__env->make('livewire.pages.onboarding.partials.auth-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php elseif($view === 'error'): ?>
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
            </div>
            <div class="text-center mb-3">
                <?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'danger','set' => 'bi','style' => 'font-size:3rem']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'danger','set' => 'bi','style' => 'font-size:3rem']); ?>
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
            </div>
            <div class="alert alert-danger py-2 small"><?php echo e($error); ?></div>
            <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
            <div class="d-grid gap-2 mt-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment): ?>
                <button wire:click="retry" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-repeat me-1"></i><?php echo e(__('onboarding.retry_payment')); ?>

                </button>
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i><?php echo e(__('onboarding.switch_gateway')); ?>

                </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php echo $__env->make('livewire.pages.onboarding.partials.auth-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->receiptDataUrl): ?>
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background:var(--card-bg);border:1px solid var(--border)">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title"><?php echo e(__('onboarding.receipt_preview')); ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="<?php echo e($this->receiptDataUrl); ?>"
                         alt="<?php echo e(__('onboarding.receipt_preview')); ?>"
                         style="max-width:100%;max-height:70vh;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.1)">
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        function initPaymentResult() {
            if (!window._paymentReceiptListener) {
                Livewire.on('openReceiptModal', function () {
                    var modal = new bootstrap.Modal(document.getElementById('receiptModal'));
                    modal.show();
                });
                window._paymentReceiptListener = true;
            }
        }
        initPaymentResult();
    </script>
    <?php $__env->stopPush(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\pages\onboarding\payment-result.blade.php ENDPATH**/ ?>