<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true"
     data-confirm-text="{{ __('general.confirm') }}"
     data-delete-text="{{ __('messages.confirm_delete') }}"
     data-bulk-delete-text="{{ __('messages.confirm_bulk_delete') }}"
     data-force-delete-text="{{ __('messages.confirm_force_delete') }}"
     data-bulk-force-delete-text="{{ __('messages.confirm_bulk_force_delete') }}"
     data-password-required="{{ __('general.password_required') }}"
     data-password-incorrect="{{ __('general.incorrect_password') }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-custom">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="{{ __('general.close') }}"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <div class="confirm-modal-icon mx-auto" id="confirmModalIcon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="fw-semibold mb-2" id="confirmModalTitle">{{ __('general.confirm') }}</h5>
                <p class="mb-0" id="confirmModalBody">{{ __('messages.confirm_delete') }}</p>
                <div id="confirmModalPasswordWrap" class="mt-3 text-start" style="display:none">
                    <label class="form-label-custom">{{ __('general.password') }}</label>
                    <input type="password" id="confirmModalPassword" class="form-custom" placeholder="{{ __('general.enter_password') }}" autocomplete="off">
                    <div id="confirmModalPasswordError" class="text-danger small mt-1" style="display:none">{{ __('general.incorrect_password') }}</div>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-custom px-4" data-bs-dismiss="modal">
                    {{ __('general.cancel') }}
                </button>
                <button type="button" id="confirmModalAction" class="btn btn-danger btn-custom px-4" data-default-text="{{ __('general.delete') }}" data-force-delete-text="{{ __('general.force_delete') }}">
                    {{ __('general.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>
