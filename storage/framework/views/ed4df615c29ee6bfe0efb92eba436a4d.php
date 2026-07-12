<?php $__env->startSection('title', __('workspace.invitation_title') . ' - ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="padding:2rem;">
    <div class="card shadow-sm" style="max-width:480px;width:100%;border-radius:16px;border:1px solid var(--border);">
        <div class="card-body p-4" style="text-align:center;">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width:64px;height:64px;background:rgba(21,183,108,0.1);">
                    <i class="bi bi-mailbox2" style="font-size:28px;color:var(--accent);"></i>
                </div>
                <h4 class="mb-2" style="font-weight:600;"><?php echo e(__('workspace.invitation_title')); ?></h4>
                <p class="text-muted mb-0" style="font-size:14px;">
                    <?php echo e(__('workspace.invitation_description')); ?>

                </p>
            </div>

            <div class="mb-4 p-3 rounded-3"
                 style="background:var(--bg);border:1px solid var(--border);text-align:start;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex-shrink:0;">
                        <?php echo e(strtoupper(substr($invitation->workspace->name, 0, 1))); ?>

                    </div>
                    <div>
                        <div style="font-weight:600;font-size:15px;"><?php echo e($invitation->workspace->name); ?></div>
                        <div style="font-size:12px;color:var(--text-muted);">
                            <?php echo e(__('workspace.invited_by')); ?> <?php echo e($invitation->inviter->name); ?>

                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2" style="font-size:13px;color:var(--text-muted);">
                    <i class="bi bi-shield-check"></i>
                    <span><?php echo e(__('workspace.role')); ?>: <strong><?php echo e(__('workspace.role_' . $invitation->role)); ?></strong></span>
                </div>
                <div class="d-flex align-items-center gap-2 mt-1" style="font-size:13px;color:var(--text-muted);">
                    <i class="bi bi-clock"></i>
                    <span><?php echo e(__('workspace.invitation_expires', ['date' => $invitation->expires_at->format('Y-m-d')])); ?></span>
                </div>
            </div>

            <div class="d-grid gap-2">
                <form action="<?php echo e(route('invitations.do-accept', $invitation)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-accent btn-custom w-100" style="padding:12px;font-size:15px;">
                        <i class="bi bi-check-lg me-1"></i><?php echo e(__('workspace.accept_invitation')); ?>

                    </button>
                </form>
                <form action="<?php echo e(route('invitations.do-decline', $invitation)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn w-100"
                            style="padding:12px;font-size:15px;background:transparent;border:1px solid var(--border);color:var(--text-muted);"
                            onclick="return confirm('<?php echo e(__('workspace.decline_confirm')); ?>')">
                        <?php echo e(__('workspace.decline_invitation')); ?>

                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\invitations\accept.blade.php ENDPATH**/ ?>