<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<div class="auth-card animate-fade-in" wire:poll.3s="checkVerification">

    <div class="auth-logo">
        <div class="logo-icon">
            <i class="bi bi-envelope-check" style="font-size:1.4rem;"></i>
        </div>
        <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
        <span class="logo-sub"><?php echo e(__('general.verify_email')); ?></span>
    </div>

    <p class="text-muted text-center mb-4" style="font-size:14px; line-height:1.6">
        <?php echo e(__('general.verify_email_desc')); ?>

    </p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status') == 'verification-link-sent'): ?>
        <div class="alert-success-custom mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo e(__('general.verification_link_sent')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="d-flex gap-3 mt-2">
        <button wire:click="sendVerification" class="btn btn-accent btn-custom flex-grow-1">
            <div wire:loading wire:target="sendVerification" class="spinner-border spinner-border-sm me-2" role="status"></div>
            <i class="bi bi-envelope me-2" wire:loading.remove wire:target="sendVerification"></i>
            <?php echo e(__('general.resend_verification_email')); ?>

        </button>
        <button wire:click="logout" class="btn btn-outline-secondary btn-custom btn-icon" title="<?php echo e(__('general.logout')); ?>">
            <i class="bi bi-box-arrow-right"></i>
        </button>
    </div>

</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire/pages/auth/verify-email.blade.php ENDPATH**/ ?>