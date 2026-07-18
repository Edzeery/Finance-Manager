{{-- resources\views\settings\index.blade.php --}}
<x-app-layout>
    <x-slot:title>{{ __('settings.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('settings.title') }}</x-slot>
    <x-slot:page-description>{{ __('settings.workspace_desc') }}</x-slot>

    <div class="profile-grid" x-data="{ tab: 'general' }">
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-building"></i>
                    <span>{{ $workspace?->name ?? __('workspace.title') }}</span>
                </div>
                <nav class="profile-nav">
                    <button @click="tab = 'general'" :class="{ 'active': tab === 'general' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-sliders"></i>
                        <span>{{ __('settings.general') }}</span>
                    </button>
                    <button @click="tab = 'members'" :class="{ 'active': tab === 'members' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-people"></i>
                        <span>{{ __('workspace.members') }}</span>
                        <span class="badge bg-secondary ms-auto" style="font-size:10px;">{{ $members->count() }}</span>
                    </button>
                    <button @click="tab = 'roles'" :class="{ 'active': tab === 'roles' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-shield"></i>
                        <span>{{ __('workspace.roles') }}</span>
                    </button>
                    <button @click="tab = 'billing'" :class="{ 'active': tab === 'billing' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-credit-card"></i>
                        <span>{{ __('settings.subscription') }}</span>
                    </button>
                </nav>
            </div>
        </div>

        <div class="profile-main">
            {{-- General Tab --}}
            <div x-show="tab === 'general'" x-transition:enter.duration.200ms>
                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-building" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('settings.workspace_info') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('settings.workspace_desc') }}</p>
                        </div>
                    </div>
                    <form action="{{ route('settings.workspace.update') }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('settings.workspace_name') }}</label>
                                <input type="text" name="name" class="form-custom" value="{{ $workspace->name ?? '' }}" required maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('settings.workspace_type') }}</label>
                                <input type="text" class="form-custom" value="{{ ucfirst($workspace->type ?? 'personal') }}" disabled style="opacity:0.7">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-accent btn-custom">{{ __('settings.save') }}</button>
                    </form>
                </div>

                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-person-gear" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('settings.owner_info') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('settings.owner_info_desc') }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex-shrink:0;">
                            {{ strtoupper(substr($workspaceOwner?->name ?? '-', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;">{{ $workspaceOwner?->name }}</div>
                            <div style="font-size:12px;color:var(--text-muted);">{{ $workspaceOwner?->email }}</div>
                        </div>
                        <x-status-badge domain="general" status="paid" set="bi" size="xs" class="ms-auto" />
                    </div>
                </div>
            </div>

            {{-- Members Tab --}}
            <div x-show="tab === 'members'" x-cloak x-transition:enter.duration.200ms>
                @if($isOwner)
                    <div class="settings-card mb-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                                <i class="bi bi-person-plus" style="color:var(--accent);font-size:16px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('workspace.invite_member') }}</h5>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('workspace.invite_desc') }}</p>
                            </div>
                        </div>
                        <form action="{{ route('settings.workspace.members.invite') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-5">
                                <input type="email" name="email" class="form-custom" value="{{ old('email') }}" required placeholder="user@example.com">
                            </div>
                            <div class="col-md-4">
                                <select name="role" class="form-custom">
                                    @foreach($roles as $val => $label)
                                        @if($val !== 'workspace_admin')
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-accent btn-custom w-100">
                                    <i class="bi bi-person-plus me-1"></i>{{ __('workspace.invite') }}
                                </button>
                            </div>
                        </form>
                        @if($userLimit > 0)
                            <div class="mt-3 d-flex align-items-center gap-2" style="font-size:12px;color:var(--text-muted);">
                                <i class="bi bi-people"></i>
                                <span>{{ $userCount }} / {{ $userLimit }} {{ __('general.users') }}</span>
                                @if(!$workspace->canAddUser())
                                    <x-status-badge domain="general" status="danger" set="bi" size="xs" class="ms-1" />
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                @if($pendingInvitations->isNotEmpty())
                    <div class="settings-card mb-4" style="border-color:rgba(245,158,11,0.25);">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(245,158,11,0.1);flex-shrink:0;">
                                <i class="bi bi-envelope-paper" style="color:rgb(245,158,11);font-size:16px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('workspace.pending_invitations') }}</h5>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('workspace.pending_invitations_desc') }}</p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="data-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.email') }}</th>
                                        <th>{{ __('workspace.role') }}</th>
                                        <th>{{ __('workspace.invited_by') }}</th>
                                        <th>{{ __('workspace.invited_date') }}</th>
                                        <th>{{ __('workspace.expiration') }}</th>
                                        <th class="text-center">{{ __('general.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingInvitations as $invitation)
                                        <tr>
                                            <td class="cell-muted">{{ $invitation->email }}</td>
                                            <td>
                                                <x-status-badge domain="general" status="pending" set="bi" size="xs" />
                                            </td>
                                            <td class="cell-muted">{{ $invitation->inviter->name ?? '—' }}</td>
                                            <td class="cell-muted">{{ $invitation->created_at->format('Y/m/d') }}</td>
                                            <td class="cell-muted">
                                                @if($invitation->expires_at->isFuture())
                                                    {{ $invitation->expires_at->diffForHumans() }}
                                                @else
                                                    <span style="color:var(--danger);">{{ __('workspace.expired') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <form action="{{ route('invitations.resend', $invitation) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="action-btn" title="{{ __('workspace.resend') }}">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('invitations.cancel', $invitation) }}" method="POST" style="display:inline;">
                                                        @csrf @method('DELETE')
                                                        <button type="button" class="action-btn" style="color:var(--danger);" title="{{ __('workspace.cancel') }}"
                                                                @click="showConfirmModal('{{ __('general.confirm') }}','{{ __('workspace.cancel_confirm') }}',function(c){if(c){$el.closest('td').querySelector('form').submit();}},'{{ __('workspace.cancel') }}','btn-danger')">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-people" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('workspace.members') }} ({{ $members->count() }})</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('workspace.members_desc') }}</p>
                        </div>
                    </div>
                    @if($members->count())
                        <div class="table-responsive">
                            <table class="data-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.name') }}</th>
                                        <th>{{ __('general.email') }}</th>
                                        <th>{{ __('workspace.role') }}</th>
                                        <th>{{ __('workspace.joined') }}</th>
                                        @if($isOwner)
                                            <th class="text-center">{{ __('general.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($members as $member)
                                        @php $memberWsRole = $member->workspaceRole($workspace); @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div style="width:32px;height:32px;border-radius:8px;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                                    </div>
                                                    <span style="font-weight:500">{{ $member->name }}</span>
                                                    @if($memberWsRole === 'workspace_admin')
                                                        <x-status-badge domain="general" status="verified" set="bi" size="xs" />
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="cell-muted">{{ $member->email }}</td>
                                            <td>
                                                @if($isOwner && $memberWsRole !== 'workspace_admin')
                                                    <form action="{{ route('settings.workspace.members.change-role', $member) }}" method="POST" class="d-flex align-items-center gap-1">
                                                        @csrf @method('PUT')
                                                        <select name="role" class="form-custom filter-fw-sm" style="width:auto;padding:4px 8px;font-size:12px" @change="$el.form.submit()">
                                                            @foreach($roles as $val => $label)
                                                                @if($val !== 'workspace_admin')
                                                                    <option value="{{ $val }}" {{ $memberWsRole === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                @else
                                                    <x-status-badge domain="general" status="inactive" set="bi" size="xs" />
                                                @endif
                                            </td>
                                            <td class="cell-muted">{{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('Y/m/d') : '—' }}</td>
                                            @if($isOwner)
                                                <td class="text-center">
                                                    @if($memberWsRole !== 'workspace_admin')
                                                        <button type="button" class="action-btn" title="{{ __('workspace.remove') }}"
                                                                 @click="showConfirmModal('{{ __('general.confirm') }}','{{ __('messages.confirm_delete') }}',function(c){if(c){$el.closest('tr').querySelector('.remove-form').submit();}},'{{ __('workspace.remove') }}','btn-danger')">
                                                            <i class="bi bi-person-x"></i>
                                                        </button>
                                                        <form action="{{ route('settings.workspace.members.remove', $member) }}" method="POST" class="remove-form" style="display:none">@csrf @method('DELETE')</form>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <x-empty-state icon="bi bi-people" :title="__('workspace.no_members')" />
                        </div>
                    @endif
                </div>

                @if($isOwner && $nonAdminMembers->count() > 0)
                    <div class="settings-card" style="border-color:rgba(239,68,68,0.2)">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(239,68,68,0.1);flex-shrink:0;">
                                <i class="bi bi-arrow-left-right" style="color:var(--danger);font-size:16px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight:600;font-size:15px;color:var(--danger);">{{ __('workspace.transfer_ownership') }}</h5>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('workspace.transfer_desc') }}</p>
                            </div>
                        </div>
                        <form action="{{ route('settings.workspace.members.transfer') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-8">
                                <select name="user_id" class="form-custom" required>
                                    <option value="">{{ __('workspace.select_member') }}</option>
                                    @foreach($nonAdminMembers as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-danger btn-custom w-100"
                                        @click="$event.preventDefault();showConfirmModal('{{ __('general.confirm') }}','{{ __('workspace.transfer_confirm') }}',function(c){if(c){$el.closest('form').submit();}},'{{ __('workspace.transfer') }}','btn-danger')">
                                    <i class="bi bi-arrow-left-right me-1"></i>{{ __('workspace.transfer') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Roles Tab --}}
            <div x-show="tab === 'roles'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-shield" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('workspace.roles') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('workspace.roles_desc') }}</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        @foreach($roles as $roleKey => $roleLabel)
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3 p-3 rounded" style="border:1px solid var(--border);background:var(--bg-subtle);">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                                        <i class="bi bi-shield-check" style="color:var(--accent);font-size:15px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:14px;">{{ $roleLabel }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-muted mt-3 mb-0" style="font-size:12px;">
                        <i class="bi bi-info-circle me-1"></i>{{ __('workspace.roles_manage_desc') }}
                        <a href="{{ route('settings.workspace.roles.index') }}" wire:navigate style="color:var(--accent);text-decoration:none;font-weight:500;">{{ __('workspace.view_roles') }}</a>
                    </p>
                </div>
            </div>

            {{-- Billing Tab --}}
            <div x-show="tab === 'billing'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                                <i class="bi bi-credit-card" style="color:var(--accent);font-size:16px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('settings.subscription') }}</h5>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('settings.subscriptions_desc') }}</p>
                            </div>
                        </div>
                        @if($subscription)
                            <x-status-badge domain="subscription" :status="$subscription->status->value" set="bi" />
                        @endif
                    </div>

                    @if($subscription && $subscription->plan)
                        <div class="d-flex justify-content-between align-items-start mb-3 p-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border);">
                            <div>
                                <h6 style="font-weight:600;margin-bottom:2px;font-size:15px;">{{ $subscription->plan->name }}</h6>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
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
                                @if($subscription->isActive())
                                    <span style="color:var(--success);font-weight:600;font-size:14px;">&#9679; {{ __('settings.active_plan') }}</span>
                                @elseif($subscription->status === \App\Enums\SubscriptionStatus::Canceled)
                                    <span style="color:var(--danger);font-weight:600;font-size:14px;">&#9679; {{ __('settings.canceled_plan') }}</span>
                                @elseif($subscription->isExpired())
                                    <span style="color:var(--danger);font-weight:600;font-size:14px;">&#9679; {{ __('settings.expired_plan') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <div class="text-muted-sm" style="font-size:12px;">{{ __('settings.users_usage') }}</div>
                                <div style="font-weight:600;font-size:15px;">{{ $userCount }} / {{ $userLimit }} <span class="text-muted-sm" style="font-size:12px;">{{ __('general.users') }}</span></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted-sm" style="font-size:12px;">{{ __('settings.days_remaining') }}</div>
                                <div style="font-weight:600;font-size:15px;">{{ $subscription->daysRemaining() }} <span class="text-muted-sm" style="font-size:12px;">{{ __('general.days_left') }}</span></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted-sm" style="font-size:12px;">{{ __('settings.plan_status') }}</div>
                                <div style="font-weight:600;font-size:15px;"><x-status-badge domain="subscription" :status="$subscription->status->value" set="bi" /></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3" style="border-top:1px solid var(--border);">
                            <a href="{{ route('account.subscriptions') }}" class="btn btn-accent btn-custom">
                                <i class="bi bi-credit-card me-1"></i>{{ __('settings.manage_subscription') }}
                            </a>
                            @if(auth()->user()->isWorkspaceOwner($workspace) && !$subscription->plan->isFree() && $subscription->isActive() && !$subscription->canceled_at)
                                <button type="button" class="btn btn-outline-danger btn-custom" @click="confirmCancelSubscription()">
                                    <i class="bi bi-x-circle me-1"></i>{{ __('settings.cancel_subscription') }}
                                </button>
                            @elseif($subscription->canceled_at)
                                <span class="text-muted-sm" style="font-size:13px">
                                    <i class="bi bi-info-circle me-1"></i>{{ __('settings.cancel_scheduled') }}
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-credit-card-2-front" style="font-size:40px;color:var(--text-muted);opacity:0.4;"></i>
                            <p class="text-muted mt-2 mb-3">{{ __('settings.no_subscription') }}</p>
                            <a href="{{ route('account.subscriptions') }}" class="btn btn-accent btn-custom">
                                <i class="bi bi-credit-card me-1"></i>{{ __('settings.subscriptions') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
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
</x-app-layout>
