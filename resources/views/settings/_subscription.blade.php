<div class="settings-card">
    <div class="d-flex align-items-center justify-content-between gap-2" style="margin-bottom:16px">
        <h5 class="section-title mb-0"><i class="bi bi-credit-card text-accent"></i>{{ __('settings.subscription') }}</h5>
        @if($subscription)
            <x-status-badge domain="subscription" :status="$subscription->status->value" set="bi" />
        @endif
    </div>

    @if($subscription && $subscription->plan)
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h6 style="font-weight:600;margin-bottom:2px">{{ $subscription->plan->name }}</h6>
                <p class="plan-card-muted" style="margin:0;font-size:13px">
                    @if($subscription->plan->isFree())
                        {{ __('settings.free_plan') }}
                    @else
                        ${{ $subscription->plan->monthly_price }}/{{ __('general.month') }}
                        @if($subscription->plan->yearly_price > 0)
                            &middot; ${{ $subscription->plan->yearly_price }}/{{ __('general.year') }}
                        @endif
                    @endif
                </p>
            </div>
            <div class="text-end">
                <x-status-badge domain="subscription" :status="$subscription->status->value" set="bi" />
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="text-muted-sm" style="font-size:11px">{{ __('settings.users_usage') }}</div>
                <div style="font-weight:600;font-size:13px">
                    {{ $workspace->userCount() }} / {{ $workspace->userLimit() }}
                    <span class="text-muted-sm" style="font-size:11px">{{ __('general.users') }}</span>
                </div>
            </div>
            <div class="col-6">
                <div class="text-muted-sm" style="font-size:11px">{{ __('settings.days_remaining') }}</div>
                <div style="font-weight:600;font-size:13px">{{ $subscription->daysRemaining() }} {{ __('general.days_left') }}</div>
            </div>
        </div>

        <a href="{{ route('account.subscriptions') }}" class="btn btn-accent btn-custom btn-sm w-100 mb-2">
            <i class="bi bi-credit-card me-1"></i>{{ __('settings.subscriptions') }}
        </a>

        @if(auth()->user()->isWorkspaceOwner($workspace) && !$subscription->plan->isFree() && $subscription->isActive() && !$subscription->canceled_at)
            <button type="button" class="btn btn-sm btn-outline-danger btn-custom w-100" @click="confirmCancelSubscription()">
                <i class="bi bi-x-circle me-1"></i>{{ __('settings.cancel_subscription') }}
            </button>
        @elseif($subscription->canceled_at)
            <span class="text-muted-sm" style="font-size:13px;display:block;text-align:center;padding:8px 0">
                <i class="bi bi-info-circle me-1"></i>{{ __('settings.cancel_scheduled') }}
            </span>
        @endif
    @else
        <p class="text-muted mb-3" style="font-size:13px">{{ __('settings.no_subscription') }}</p>
        <a href="{{ route('account.subscriptions') }}" class="btn btn-accent btn-custom btn-sm w-100">
            <i class="bi bi-credit-card me-1"></i>{{ __('settings.subscriptions') }}
        </a>
    @endif
</div>

@push('scripts')
<script>
function confirmCancelSubscription() {
    showConfirmModal(
        '{{ __('general.confirm') }}',
        '{{ __('settings.cancel_confirm') }}',
        (confirmed) => {
            if (confirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('account.subscriptions.cancel') }}';
                form.innerHTML = '@csrf';
                document.body.appendChild(form);
                form.submit();
            }
        },
        '{{ __('settings.cancel_subscription') }}',
        'btn-danger'
    );
}
</script>
@endpush
