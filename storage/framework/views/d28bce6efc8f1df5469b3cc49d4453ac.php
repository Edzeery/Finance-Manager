<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true"
     data-confirm-text="<?php echo e(__('general.confirm')); ?>"
     data-delete-text="<?php echo e(__('messages.confirm_delete')); ?>"
     data-bulk-delete-text="<?php echo e(__('messages.confirm_bulk_delete')); ?>"
     data-force-delete-text="<?php echo e(__('messages.confirm_force_delete')); ?>"
     data-bulk-force-delete-text="<?php echo e(__('messages.confirm_bulk_force_delete')); ?>"
     data-password-required="<?php echo e(__('general.password_required')); ?>"
     data-password-incorrect="<?php echo e(__('general.incorrect_password')); ?>">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-custom">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="<?php echo e(__('general.close')); ?>"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <div class="confirm-modal-icon mx-auto" id="confirmModalIcon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="fw-semibold mb-2" id="confirmModalTitle"><?php echo e(__('general.confirm')); ?></h5>
                <p class="mb-0" id="confirmModalBody"><?php echo e(__('messages.confirm_delete')); ?></p>
                <div id="confirmModalPasswordWrap" class="mt-3 text-start" style="display:none">
                    <label class="form-label-custom"><?php echo e(__('general.password')); ?></label>
                    <input type="password" id="confirmModalPassword" class="form-custom" placeholder="<?php echo e(__('general.enter_password')); ?>" autocomplete="off">
                    <div id="confirmModalPasswordError" class="text-danger small mt-1" style="display:none"><?php echo e(__('general.incorrect_password')); ?></div>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-custom px-4" data-bs-dismiss="modal">
                    <?php echo e(__('general.cancel')); ?>

                </button>
                <button type="button" id="confirmModalAction" class="btn btn-danger btn-custom px-4" data-default-text="<?php echo e(__('general.delete')); ?>" data-force-delete-text="<?php echo e(__('general.force_delete')); ?>">
                    <?php echo e(__('general.delete')); ?>

                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\components\confirm-modal\index.blade.php ENDPATH**/ ?>