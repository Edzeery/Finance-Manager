@php
    $user = auth()->user();
@endphp

{{-- Invite Member --}}
@can('inviteMembers', $workspace)
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
                <x-status-select
                    domain="workspace_role"
                    name="role"
                    size="md"
                    set="bi"
                />
            </div>
            <div class="col-md-3">
                <x-button submit block icon="bi bi-person-plus">{{ __('workspace.invite') }}</x-button>
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
@endcan

{{-- Pending Invitations --}}
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
                                    @can('inviteMembers', $workspace)
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
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Members List --}}
<div class="settings-card mb-4">
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
                                @if(($isOwner || $user->workspaceHasPermission('workspace-user.role')) && $memberWsRole !== 'workspace_admin')
                                    <form action="{{ route('settings.workspace.members.change-role', $member) }}" method="POST" class="d-flex align-items-center gap-1">
                                        @csrf @method('PUT')
                                        <x-status-select
                                            domain="workspace_role"
                                            name="role"
                                            :selected="$memberWsRole"
                                            size="sm"
                                            set="bi"
                                            x-on:change="$el.closest('form').submit()"
                                        />
                                    </form>
                                @else
                                    <x-status-badge domain="workspace_role" :status="$memberWsRole" set="bi" size="xs" />
                                @endif
                            </td>
                            <td class="cell-muted">{{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('Y/m/d') : '—' }}</td>
                            @if($isOwner || $user->workspaceHasPermission('workspace-user.role') || $user->workspaceHasPermission('workspace-user.remove'))
                                <td class="text-center">
                                    @if($memberWsRole !== 'workspace_admin' && ($isOwner || $user->workspaceHasPermission('workspace-user.remove')))
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

{{-- Transfer Ownership --}}
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
                <x-button variant="danger" block
                        @click="$event.preventDefault();showConfirmModal('{{ __('general.confirm') }}','{{ __('workspace.transfer_confirm') }}',function(c){if(c){$el.closest('form').submit();}},'{{ __('workspace.transfer') }}','btn-danger')" icon="bi bi-arrow-left-right">{{ __('workspace.transfer') }}</x-button>
            </div>
        </form>
    </div>
@endif
