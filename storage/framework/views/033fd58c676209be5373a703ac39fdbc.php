
<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('settings.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('settings.title')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('settings.workspace_desc')); ?> <?php $__env->endSlot(); ?>

    <div class="profile-grid" x-data="{ tab: 'general' }">
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-building"></i>
                    <span><?php echo e($workspace?->name ?? __('workspace.title')); ?></span>
                </div>
                <nav class="profile-nav">
                    <button @click="tab = 'general'" :class="{ 'active': tab === 'general' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-sliders"></i>
                        <span><?php echo e(__('settings.general')); ?></span>
                    </button>
                    <button @click="tab = 'members'" :class="{ 'active': tab === 'members' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-people"></i>
                        <span><?php echo e(__('workspace.members')); ?></span>
                        <span class="badge bg-secondary ms-auto" style="font-size:10px;"><?php echo e($members->count()); ?></span>
                    </button>
                    <button @click="tab = 'roles'" :class="{ 'active': tab === 'roles' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-shield"></i>
                        <span><?php echo e(__('workspace.roles')); ?></span>
                    </button>
                    <button @click="tab = 'billing'" :class="{ 'active': tab === 'billing' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-credit-card"></i>
                        <span><?php echo e(__('settings.subscription')); ?></span>
                    </button>
                </nav>
            </div>
        </div>

        <div class="profile-main">
            
            <div x-show="tab === 'general'" x-transition:enter.duration.200ms>
                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-building" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('settings.workspace_info')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('settings.workspace_desc')); ?></p>
                        </div>
                    </div>
                    <form action="<?php echo e(route('settings.workspace.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('settings.workspace_name')); ?></label>
                                <input type="text" name="name" class="form-custom" value="<?php echo e($workspace->name ?? ''); ?>" required maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('settings.workspace_type')); ?></label>
                                <input type="text" class="form-custom" value="<?php echo e(ucfirst($workspace->type ?? 'personal')); ?>" disabled style="opacity:0.7">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-accent btn-custom"><?php echo e(__('settings.save')); ?></button>
                    </form>
                </div>

                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-person-gear" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('settings.owner_info')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('settings.owner_info_desc')); ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex-shrink:0;">
                            <?php echo e(strtoupper(substr($workspaceOwner?->name ?? '-', 0, 1))); ?>

                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;"><?php echo e($workspaceOwner?->name); ?></div>
                            <div style="font-size:12px;color:var(--text-muted);"><?php echo e($workspaceOwner?->email); ?></div>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'paid','set' => 'bi','size' => 'xs','class' => 'ms-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'paid','set' => 'bi','size' => 'xs','class' => 'ms-auto']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div x-show="tab === 'members'" x-cloak x-transition:enter.duration.200ms>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOwner): ?>
                    <div class="settings-card mb-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                                <i class="bi bi-person-plus" style="color:var(--accent);font-size:16px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('workspace.invite_member')); ?></h5>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('workspace.invite_desc')); ?></p>
                            </div>
                        </div>
                        <form action="<?php echo e(route('settings.workspace.members.invite')); ?>" method="POST" class="row g-3">
                            <?php echo csrf_field(); ?>
                            <div class="col-md-5">
                                <input type="email" name="email" class="form-custom" value="<?php echo e(old('email')); ?>" required placeholder="user@example.com">
                            </div>
                            <div class="col-md-4">
                                <select name="role" class="form-custom">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($val !== 'workspace_admin'): ?>
                                            <option value="<?php echo e($val); ?>"><?php echo e($label); ?></option>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-accent btn-custom w-100">
                                    <i class="bi bi-person-plus me-1"></i><?php echo e(__('workspace.invite')); ?>

                                </button>
                            </div>
                        </form>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($userLimit > 0): ?>
                            <div class="mt-3 d-flex align-items-center gap-2" style="font-size:12px;color:var(--text-muted);">
                                <i class="bi bi-people"></i>
                                <span><?php echo e($userCount); ?> / <?php echo e($userLimit); ?> <?php echo e(__('general.users')); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$workspace->canAddUser()): ?>
                                    <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'danger','set' => 'bi','size' => 'xs','class' => 'ms-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'danger','set' => 'bi','size' => 'xs','class' => 'ms-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingInvitations->isNotEmpty()): ?>
                    <div class="settings-card mb-4" style="border-color:rgba(245,158,11,0.25);">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(245,158,11,0.1);flex-shrink:0;">
                                <i class="bi bi-envelope-paper" style="color:rgb(245,158,11);font-size:16px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('workspace.pending_invitations')); ?></h5>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('workspace.pending_invitations_desc')); ?></p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="data-table mb-0">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('general.email')); ?></th>
                                        <th><?php echo e(__('workspace.role')); ?></th>
                                        <th><?php echo e(__('workspace.invited_by')); ?></th>
                                        <th><?php echo e(__('workspace.invited_date')); ?></th>
                                        <th><?php echo e(__('workspace.expiration')); ?></th>
                                        <th class="text-center"><?php echo e(__('general.actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pendingInvitations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invitation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="cell-muted"><?php echo e($invitation->email); ?></td>
                                            <td>
                                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'pending','set' => 'bi','size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'pending','set' => 'bi','size' => 'xs']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                                            </td>
                                            <td class="cell-muted"><?php echo e($invitation->inviter->name ?? '—'); ?></td>
                                            <td class="cell-muted"><?php echo e($invitation->created_at->format('Y/m/d')); ?></td>
                                            <td class="cell-muted">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invitation->expires_at->isFuture()): ?>
                                                    <?php echo e($invitation->expires_at->diffForHumans()); ?>

                                                <?php else: ?>
                                                    <span style="color:var(--danger);"><?php echo e(__('workspace.expired')); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <form action="<?php echo e(route('invitations.resend', $invitation)); ?>" method="POST" style="display:inline;">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="action-btn" title="<?php echo e(__('workspace.resend')); ?>">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>
                                                    </form>
                                                    <form action="<?php echo e(route('invitations.cancel', $invitation)); ?>" method="POST" style="display:inline;">
                                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                        <button type="button" class="action-btn" style="color:var(--danger);" title="<?php echo e(__('workspace.cancel')); ?>"
                                                                @click="showConfirmModal('<?php echo e(__('general.confirm')); ?>','<?php echo e(__('workspace.cancel_confirm')); ?>',function(c){if(c){$el.closest('td').querySelector('form').submit();}},'<?php echo e(__('workspace.cancel')); ?>','btn-danger')">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-people" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('workspace.members')); ?> (<?php echo e($members->count()); ?>)</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('workspace.members_desc')); ?></p>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($members->count()): ?>
                        <div class="table-responsive">
                            <table class="data-table mb-0">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('general.name')); ?></th>
                                        <th><?php echo e(__('general.email')); ?></th>
                                        <th><?php echo e(__('workspace.role')); ?></th>
                                        <th><?php echo e(__('workspace.joined')); ?></th>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOwner): ?>
                                            <th class="text-center"><?php echo e(__('general.actions')); ?></th>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $memberWsRole = $member->workspaceRole($workspace); ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div style="width:32px;height:32px;border-radius:8px;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                                                        <?php echo e(strtoupper(substr($member->name, 0, 1))); ?>

                                                    </div>
                                                    <span style="font-weight:500"><?php echo e($member->name); ?></span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($memberWsRole === 'workspace_admin'): ?>
                                                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'verified','set' => 'bi','size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'verified','set' => 'bi','size' => 'xs']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="cell-muted"><?php echo e($member->email); ?></td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOwner && $memberWsRole !== 'workspace_admin'): ?>
                                                    <form action="<?php echo e(route('settings.workspace.members.change-role', $member)); ?>" method="POST" class="d-flex align-items-center gap-1">
                                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                                        <select name="role" class="form-custom filter-fw-sm" style="width:auto;padding:4px 8px;font-size:12px" @change="$el.form.submit()">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($val !== 'workspace_admin'): ?>
                                                                    <option value="<?php echo e($val); ?>" <?php echo e($memberWsRole === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </select>
                                                    </form>
                                                <?php else: ?>
                                                    <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'inactive','set' => 'bi','size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'inactive','set' => 'bi','size' => 'xs']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td class="cell-muted"><?php echo e($member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('Y/m/d') : '—'); ?></td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOwner): ?>
                                                <td class="text-center">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($memberWsRole !== 'workspace_admin'): ?>
                                                        <button type="button" class="action-btn" title="<?php echo e(__('workspace.remove')); ?>"
                                                                 @click="showConfirmModal('<?php echo e(__('general.confirm')); ?>','<?php echo e(__('messages.confirm_delete')); ?>',function(c){if(c){$el.closest('tr').querySelector('.remove-form').submit();}},'<?php echo e(__('workspace.remove')); ?>','btn-danger')">
                                                            <i class="bi bi-person-x"></i>
                                                        </button>
                                                        <form action="<?php echo e(route('settings.workspace.members.remove', $member)); ?>" method="POST" class="remove-form" style="display:none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-people','title' => __('workspace.no_members')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-people','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('workspace.no_members'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOwner && $nonAdminMembers->count() > 0): ?>
                    <div class="settings-card" style="border-color:rgba(239,68,68,0.2)">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(239,68,68,0.1);flex-shrink:0;">
                                <i class="bi bi-arrow-left-right" style="color:var(--danger);font-size:16px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight:600;font-size:15px;color:var(--danger);"><?php echo e(__('workspace.transfer_ownership')); ?></h5>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('workspace.transfer_desc')); ?></p>
                            </div>
                        </div>
                        <form action="<?php echo e(route('settings.workspace.members.transfer')); ?>" method="POST" class="row g-3">
                            <?php echo csrf_field(); ?>
                            <div class="col-md-8">
                                <select name="user_id" class="form-custom" required>
                                    <option value=""><?php echo e(__('workspace.select_member')); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $nonAdminMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($member->id); ?>"><?php echo e($member->name); ?> (<?php echo e($member->email); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-danger btn-custom w-100"
                                        @click="$event.preventDefault();showConfirmModal('<?php echo e(__('general.confirm')); ?>','<?php echo e(__('workspace.transfer_confirm')); ?>',function(c){if(c){$el.closest('form').submit();}},'<?php echo e(__('workspace.transfer')); ?>','btn-danger')">
                                    <i class="bi bi-arrow-left-right me-1"></i><?php echo e(__('workspace.transfer')); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div x-show="tab === 'roles'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-shield" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('workspace.roles')); ?></h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('workspace.roles_desc')); ?></p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleKey => $roleLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3 p-3 rounded" style="border:1px solid var(--border);background:var(--bg-subtle);">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                                        <i class="bi bi-shield-check" style="color:var(--accent);font-size:15px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:14px;"><?php echo e($roleLabel); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="text-muted mt-3 mb-0" style="font-size:12px;">
                        <i class="bi bi-info-circle me-1"></i><?php echo e(__('workspace.roles_manage_desc')); ?>

                        <a href="<?php echo e(route('settings.workspace.roles.index')); ?>" wire:navigate style="color:var(--accent);text-decoration:none;font-weight:500;"><?php echo e(__('workspace.view_roles')); ?></a>
                    </p>
                </div>
            </div>

            
            <div x-show="tab === 'billing'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                                <i class="bi bi-credit-card" style="color:var(--accent);font-size:16px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight:600;font-size:15px;"><?php echo e(__('settings.subscription')); ?></h5>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);"><?php echo e(__('settings.subscriptions_desc')); ?></p>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription): ?>
                            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'subscription','status' => $subscription->status->value,'set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'subscription','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($subscription->status->value),'set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription && $subscription->plan): ?>
                        <div class="d-flex justify-content-between align-items-start mb-3 p-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border);">
                            <div>
                                <h6 style="font-weight:600;margin-bottom:2px;font-size:15px;"><?php echo e($subscription->plan->name); ?></h6>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->plan->isFree()): ?>
                                        <?php echo e(__('settings.free_plan')); ?>

                                    <?php else: ?>
                                        $<?php echo e($subscription->plan->monthly_price); ?>/<?php echo e(__('general.month')); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->plan->yearly_price > 0): ?>
                                            &middot; $<?php echo e($subscription->plan->yearly_price); ?>/<?php echo e(__('general.year')); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>
                            <div class="text-end">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->isActive()): ?>
                                    <span style="color:var(--success);font-weight:600;font-size:14px;">&#9679; <?php echo e(__('settings.active_plan')); ?></span>
                                <?php elseif($subscription->status === \App\Enums\SubscriptionStatus::Canceled): ?>
                                    <span style="color:var(--danger);font-weight:600;font-size:14px;">&#9679; <?php echo e(__('settings.canceled_plan')); ?></span>
                                <?php elseif($subscription->isExpired()): ?>
                                    <span style="color:var(--danger);font-weight:600;font-size:14px;">&#9679; <?php echo e(__('settings.expired_plan')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <div class="text-muted-sm" style="font-size:12px;"><?php echo e(__('settings.users_usage')); ?></div>
                                <div style="font-weight:600;font-size:15px;"><?php echo e($userCount); ?> / <?php echo e($userLimit); ?> <span class="text-muted-sm" style="font-size:12px;"><?php echo e(__('general.users')); ?></span></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted-sm" style="font-size:12px;"><?php echo e(__('settings.days_remaining')); ?></div>
                                <div style="font-weight:600;font-size:15px;"><?php echo e($subscription->daysRemaining()); ?> <span class="text-muted-sm" style="font-size:12px;"><?php echo e(__('general.days_left')); ?></span></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted-sm" style="font-size:12px;"><?php echo e(__('settings.plan_status')); ?></div>
                                <div style="font-weight:600;font-size:15px;"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'subscription','status' => $subscription->status->value,'set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'subscription','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($subscription->status->value),'set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3" style="border-top:1px solid var(--border);">
                            <a href="<?php echo e(route('account.subscriptions')); ?>" class="btn btn-accent btn-custom">
                                <i class="bi bi-credit-card me-1"></i><?php echo e(__('settings.manage_subscription')); ?>

                            </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->isWorkspaceOwner($workspace) && !$subscription->plan->isFree() && $subscription->isActive() && !$subscription->canceled_at): ?>
                                <button type="button" class="btn btn-outline-danger btn-custom" @click="confirmCancelSubscription()">
                                    <i class="bi bi-x-circle me-1"></i><?php echo e(__('settings.cancel_subscription')); ?>

                                </button>
                            <?php elseif($subscription->canceled_at): ?>
                                <span class="text-muted-sm" style="font-size:13px">
                                    <i class="bi bi-info-circle me-1"></i><?php echo e(__('settings.cancel_scheduled')); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-credit-card-2-front" style="font-size:40px;color:var(--text-muted);opacity:0.4;"></i>
                            <p class="text-muted mt-2 mb-3"><?php echo e(__('settings.no_subscription')); ?></p>
                            <a href="<?php echo e(route('account.subscriptions')); ?>" class="btn btn-accent btn-custom">
                                <i class="bi bi-credit-card me-1"></i><?php echo e(__('settings.subscriptions')); ?>

                            </a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function confirmCancelSubscription() {
        showConfirmModal(
            '<?php echo e(__('general.confirm')); ?>',
            '<?php echo e(__('settings.cancel_confirm')); ?>',
            (confirmed) => {
                if (confirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?php echo e(route('account.subscriptions.cancel')); ?>';
                    form.innerHTML = '<?php echo csrf_field(); ?>';
                    document.body.appendChild(form);
                    form.submit();
                }
            },
            '<?php echo e(__('settings.cancel_subscription')); ?>',
            'btn-danger'
        );
    }
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\settings\index.blade.php ENDPATH**/ ?>