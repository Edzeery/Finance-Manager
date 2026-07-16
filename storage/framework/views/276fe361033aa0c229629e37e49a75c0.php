<?php if (isset($component)) { $__componentOriginal11b520df80702cb1ab8718e178b6ffa6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6 = $attributes; } ?>
<?php $component = App\View\Components\SuperAdminLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('super-admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SuperAdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('super-admin.users')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('super-admin.users')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('super-admin.users_desc')); ?> <?php $__env->endSlot(); ?>

    <?php $showSubTabs = request('status') !== 'trashed'; ?>


    <?php if (isset($component)) { $__componentOriginal526982350b860bbb0ef3834fb35dd9e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal526982350b860bbb0ef3834fb35dd9e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-tabs','data' => ['tabs' => [
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-people'],
        'active' => ['label' => __('general.active'), 'count' => $countActive, 'icon' => 'bi-check-circle'],
        'online' => [
            'label' => __('general.online'),
            'count' => $countOnline,
            'icon' => 'bi-wifi',
            'color' => '#16a34a',
        ],
        'inactive' => ['label' => __('general.inactive'), 'count' => $countInactive, 'icon' => 'bi-x-circle'],
        'suspended' => ['label' => __('general.suspended'), 'count' => $countSuspended, 'icon' => 'bi-pause-circle'],
        'banned' => ['label' => __('general.banned'), 'count' => $countBanned, 'icon' => 'bi-slash-circle'],
        'trashed' => ['label' => __('general.trash'), 'count' => $countTrashed, 'icon' => 'bi-trash'],
    ],'current' => ''.e(request('status', 'all')).'','keyParam' => 'status','defaultKey' => 'all','preserve' => ['search', 'per_page'],'subParam' => ''.e($showSubTabs ? 'super_admin' : '').'','subCurrent' => ''.e($showSubTabs ? request('super_admin', '') : '').'','subTabs' => $showSubTabs
            ? [
                '' => ['label' => __('general.all')],
                'yes' => ['label' => __('super-admin.super_admin')],
                'no' => ['label' => __('super-admin.users')],
            ]
            : []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tabs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-people'],
        'active' => ['label' => __('general.active'), 'count' => $countActive, 'icon' => 'bi-check-circle'],
        'online' => [
            'label' => __('general.online'),
            'count' => $countOnline,
            'icon' => 'bi-wifi',
            'color' => '#16a34a',
        ],
        'inactive' => ['label' => __('general.inactive'), 'count' => $countInactive, 'icon' => 'bi-x-circle'],
        'suspended' => ['label' => __('general.suspended'), 'count' => $countSuspended, 'icon' => 'bi-pause-circle'],
        'banned' => ['label' => __('general.banned'), 'count' => $countBanned, 'icon' => 'bi-slash-circle'],
        'trashed' => ['label' => __('general.trash'), 'count' => $countTrashed, 'icon' => 'bi-trash'],
    ]),'current' => ''.e(request('status', 'all')).'','keyParam' => 'status','defaultKey' => 'all','preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search', 'per_page']),'subParam' => ''.e($showSubTabs ? 'super_admin' : '').'','subCurrent' => ''.e($showSubTabs ? request('super_admin', '') : '').'','subTabs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showSubTabs
            ? [
                '' => ['label' => __('general.all')],
                'yes' => ['label' => __('super-admin.super_admin')],
                'no' => ['label' => __('super-admin.users')],
            ]
            : [])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal526982350b860bbb0ef3834fb35dd9e5)): ?>
<?php $attributes = $__attributesOriginal526982350b860bbb0ef3834fb35dd9e5; ?>
<?php unset($__attributesOriginal526982350b860bbb0ef3834fb35dd9e5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal526982350b860bbb0ef3834fb35dd9e5)): ?>
<?php $component = $__componentOriginal526982350b860bbb0ef3834fb35dd9e5; ?>
<?php unset($__componentOriginal526982350b860bbb0ef3834fb35dd9e5); ?>
<?php endif; ?>

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="<?php echo e(route('super.admin.users.index')); ?>"
                    class="d-flex flex-wrap align-items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['name' => 'search','placeholder' => ''.e(__('general.search')).'...','value' => ''.e(request('search')).'','minWidth' => '200px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','placeholder' => ''.e(__('general.search')).'...','value' => ''.e(request('search')).'','min-width' => '200px']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $attributes = $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $component = $__componentOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('status') && request('status') !== 'all'): ?>
                        <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSubTabs && request('super_admin')): ?>
                        <input type="hidden" name="super_admin" value="<?php echo e(request('super_admin')); ?>">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button type="submit" class="btn"
                        style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer"><?php echo e(__('general.filter')); ?></button>
                    <?php if (isset($component)) { $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clear-filters','data' => ['filters' => ['search', 'status', 'super_admin'],'route' => route('super.admin.users.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clear-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search', 'status', 'super_admin']),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('super.admin.users.index'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113)): ?>
<?php $attributes = $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113; ?>
<?php unset($__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113)): ?>
<?php $component = $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113; ?>
<?php unset($__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113); ?>
<?php endif; ?>
                </form>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('status') === 'trashed'): ?>
                    <button id="bulk-restore-btn" class="btn bulk-btn" style="display:none"
                        onclick="confirmBulkRestore()">
                        <i class="bi bi-arrow-counterclockwise"></i> <?php echo e(__('general.restore')); ?>

                    </button>
                <?php else: ?>
                    <button id="bulk-delete-btn" class="btn bulk-btn" style="display:none"
                        onclick="confirmBulkDelete()">
                        <i class="bi bi-trash"></i> <?php echo e(__('general.delete')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="data-grid-toolbar-right">
                <span id="bulk-count" class="bulk-count" style="display:none"></span>
                <?php if (isset($component)) { $__componentOriginal350cc130478c4b4aced77f6fd760100d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal350cc130478c4b4aced77f6fd760100d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page','data' => ['current' => (int) request('per_page', 15),'route' => route('super.admin.users.index'),'preserve' => ['search', 'status', 'super_admin'],'options' => [10, 15, 25, 50]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) request('per_page', 15)),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('super.admin.users.index')),'preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search', 'status', 'super_admin']),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([10, 15, 25, 50])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal350cc130478c4b4aced77f6fd760100d)): ?>
<?php $attributes = $__attributesOriginal350cc130478c4b4aced77f6fd760100d; ?>
<?php unset($__attributesOriginal350cc130478c4b4aced77f6fd760100d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal350cc130478c4b4aced77f6fd760100d)): ?>
<?php $component = $__componentOriginal350cc130478c4b4aced77f6fd760100d; ?>
<?php unset($__componentOriginal350cc130478c4b4aced77f6fd760100d); ?>
<?php endif; ?>
            </div>
        </div>

        <div class="data-grid-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->count()): ?>
                <form id="bulk-delete-form" action="<?php echo e(route('super.admin.users.bulk-delete')); ?>" method="POST"
                    class="d-none"><?php echo csrf_field(); ?><input type="hidden" name="user_ids" id="bulk-delete-ids"></form>
                <form id="bulk-restore-form" action="<?php echo e(route('super.admin.users.bulk-restore')); ?>" method="POST"
                    class="d-none"><?php echo csrf_field(); ?><input type="hidden" name="user_ids" id="bulk-restore-ids"></form>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-checkbox"><input type="checkbox" class="select-all"
                                    style="accent-color:var(--accent)"></th>
                            <th><?php echo e(__('general.name')); ?></th>
                            <th><?php echo e(__('general.email')); ?></th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('status') !== 'trashed'): ?>
                                <th><?php echo e(__('super-admin.workspaces')); ?></th>
                                <th><?php echo e(__('general.status')); ?></th>
                                <th><?php echo e(__('super-admin.super_admin')); ?></th>
                                <th><?php echo e(__('super-admin.roles')); ?></th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <th><?php echo e(request('status') === 'trashed' ? __('general.deleted') : __('general.member_since')); ?>

                            </th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $sr = $user->statusRecord; ?>
                            <tr>
                                <td class="col-checkbox"><input type="checkbox" class="select-item"
                                        value="<?php echo e($user->id); ?>" style="accent-color:var(--accent)"></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar" style="position:relative">
                                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                            <span class="online-dot"
                                                style="background:<?php echo e($sr && $sr->online_status->value === 'online' ? '#16a34a' : '#9ca3af'); ?>"></span>
                                        </div>
                                        <span class="fw-500"><?php echo e($user->name); ?></span>
                                    </div>
                                </td>
                                <td class="cell-muted"><?php echo e($user->email); ?>

                                    <?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'email_verification','status' => $user->email_verified_at ? 'email_verified' : 'email_unverified','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'email_verification','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->email_verified_at ? 'email_verified' : 'email_unverified'),'set' => 'bi']); ?>
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
                                </td>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('status') !== 'trashed'): ?>
                                    <td><span class="badge-count"><?php echo e($user->workspaces->count()); ?></span></td>

                                    <td>
                                        <?php if (isset($component)) { $__componentOriginal2efc67f06a125e15ab104a0993e8a8ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2efc67f06a125e15ab104a0993e8a8ab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-select','data' => ['domain' => 'user','name' => 'status','selected' => $user->status,'set' => 'bi','size' => 'sm','dataUserId' => ''.e($user->id).'','onchange' => 'confirmChangeStatus(this)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'user','name' => 'status','selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->status),'set' => 'bi','size' => 'sm','data-user-id' => ''.e($user->id).'','onchange' => 'confirmChangeStatus(this)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2efc67f06a125e15ab104a0993e8a8ab)): ?>
<?php $attributes = $__attributesOriginal2efc67f06a125e15ab104a0993e8a8ab; ?>
<?php unset($__attributesOriginal2efc67f06a125e15ab104a0993e8a8ab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2efc67f06a125e15ab104a0993e8a8ab)): ?>
<?php $component = $__componentOriginal2efc67f06a125e15ab104a0993e8a8ab; ?>
<?php unset($__componentOriginal2efc67f06a125e15ab104a0993e8a8ab); ?>
<?php endif; ?>
                                        <form id="set-status-form-<?php echo e($user->id); ?>"
                                            action="<?php echo e(route('super.admin.users.set-status', $user)); ?>" method="POST"
                                            class="d-none"><?php echo csrf_field(); ?>
                                            <input type="hidden" name="status"
                                                id="set-status-value-<?php echo e($user->id); ?>">
                                        </form>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->hasRole('super_admin')): ?>
                                            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'yes','set' => 'fa','class' => 'text-lg fw-bold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'yes','set' => 'fa','class' => 'text-lg fw-bold']); ?>
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
                                        <?php else: ?>
                                            <span class="cell-muted">&mdash;</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $user->roles->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'role','status' => $role->slug,'set' => 'fa','class' => 'text-lg fw-bold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'role','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($role->slug),'set' => 'fa','class' => 'text-lg fw-bold']); ?>
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
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <span class="cell-muted">&mdash;</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->roles->count() > 2): ?>
                                                <span class="badge-more">+<?php echo e($user->roles->count() - 2); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <td class="cell-muted">
                                    <?php echo e(request('status') === 'trashed' ? $user->deleted_at->format('Y/m/d') : $user->created_at->format('Y/m/d')); ?>

                                </td>

                                <td class="col-actions">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('status') === 'trashed'): ?>
                                        <div class="cell-actions">
                                            <button type="button" class="btn btn-icon"
                                                title="<?php echo e(__('general.restore')); ?>"
                                                onclick="confirmRestoreUser(<?php echo e($user->id); ?>)">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                            <button type="button" class="btn btn-icon btn-icon-danger"
                                                title="<?php echo e(__('general.force_delete')); ?>"
                                                onclick="confirmForceDeleteUser(<?php echo e($user->id); ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="restore-user-<?php echo e($user->id); ?>"
                                                action="<?php echo e(route('super.admin.users.restore', $user->id)); ?>"
                                                method="POST" class="d-none"><?php echo csrf_field(); ?></form>
                                            <form id="force-delete-user-<?php echo e($user->id); ?>"
                                                action="<?php echo e(route('super.admin.users.force-destroy', $user->id)); ?>"
                                                method="POST" class="d-none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
                                        </div>
                                    <?php else: ?>
                                        <div class="cell-actions">
                                            <a href="<?php echo e(route('super.admin.users.edit', $user)); ?>"
                                                class="btn btn-icon" title="<?php echo e(__('super-admin.edit_user')); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-icon btn-icon-danger"
                                                title="<?php echo e(__('general.delete')); ?>"
                                                onclick="confirmDeleteUser(<?php echo e($user->id); ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-user-<?php echo e($user->id); ?>"
                                                action="<?php echo e(route('super.admin.users.destroy', $user)); ?>"
                                                method="POST" class="d-none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)">
                        <i class="bi bi-<?php echo e(request('status') === 'trashed' ? 'trash' : 'people'); ?>"></i>
                    </div>
                    <h4><?php echo e(__('general.no_data')); ?></h4>
                    <p><?php echo e(request('status') === 'trashed' ? __('messages.no_trashed') : __('messages.no_results')); ?>

                    </p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->count()): ?>
            <div class="data-grid-footer">
                <?php if (isset($component)) { $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination-info','data' => ['items' => $users]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination-info'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105)): ?>
<?php $attributes = $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105; ?>
<?php unset($__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105)): ?>
<?php $component = $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105; ?>
<?php unset($__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105); ?>
<?php endif; ?>
                <div><?php echo e($users->appends(request()->except('page'))->links()); ?></div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function confirmChangeStatus(component) {
                var userId = component.dataset.userId;
                var hiddenInput = component.querySelector('input[type="hidden"]');
                var value = hiddenInput ? hiddenInput.value : null;
                if (!value) return;
                var statusNames = {
                    'active': '<?php echo e(__('general.active')); ?>',
                    'inactive': '<?php echo e(__('general.inactive')); ?>',
                    'pending': '<?php echo e(__('general.pending')); ?>',
                    'suspended': '<?php echo e(__('general.suspended')); ?>',
                    'banned': '<?php echo e(__('general.banned')); ?>'
                };
                showConfirmModal(
                    '<?php echo e(__('general.confirm')); ?>',
                    '<?php echo e(__('messages.confirm_change_user_status')); ?>'.replace(':status', statusNames[value] || value),
                    function(confirmed) {
                        if (confirmed) {
                            document.getElementById('set-status-value-' + userId).value = value;
                            document.getElementById('set-status-form-' + userId).submit();
                        } else {
                            location.reload();
                        }
                    },
                    statusNames[value] || value,
                    value === 'active' ? 'btn-success' : value === 'inactive' ? 'btn-secondary' : value === 'suspended' ?
                    'btn-warning' :
                    'btn-danger'
                );
            }

            function confirmDeleteUser(userId) {
                const form = document.getElementById('delete-user-' + userId);
                if (!form) return;
                showConfirmModal(
                    '<?php echo e(__('general.confirm')); ?>',
                    '<?php echo e(__('messages.confirm_delete_user')); ?>',
                    (confirmed) => {
                        if (confirmed) form.submit();
                    },
                    '<?php echo e(__('general.delete')); ?>', 'btn-danger'
                );
            }

            function confirmRestoreUser(userId) {
                const form = document.getElementById('restore-user-' + userId);
                if (!form) return;
                showConfirmModal(
                    '<?php echo e(__('general.confirm')); ?>',
                    '<?php echo e(__('messages.confirm_restore_user')); ?>',
                    (confirmed) => {
                        if (confirmed) form.submit();
                    },
                    '<?php echo e(__('general.restore')); ?>', 'btn-success'
                );
            }

            function confirmForceDeleteUser(userId) {
                const form = document.getElementById('force-delete-user-' + userId);
                if (!form) return;
                showConfirmModal(
                    '<?php echo e(__('general.confirm')); ?>',
                    '<?php echo e(__('messages.confirm_force_delete_user')); ?>',
                    (confirmed) => {
                        if (confirmed) form.submit();
                    },
                    '<?php echo e(__('general.force_delete')); ?>', 'btn-danger'
                );
            }

            function updateBulkButton() {
                const checked = document.querySelectorAll('.select-item:checked');
                const count = checked.length;
                const btn = document.getElementById('bulk-restore-btn') || document.getElementById('bulk-delete-btn');
                const countEl = document.getElementById('bulk-count');
                if (count > 0) {
                    if (btn) btn.style.display = 'inline-flex';
                    if (countEl) {
                        countEl.style.display = 'inline';
                        countEl.textContent = count + ' <?php echo e(__('general.selected')); ?>';
                    }
                } else {
                    if (btn) btn.style.display = 'none';
                    if (countEl) countEl.style.display = 'none';
                }
            }

            function clearSelection() {
                document.querySelectorAll('.select-item, .select-all').forEach(function(cb) {
                    cb.checked = false;
                });
                updateBulkButton();
            }

            function confirmBulkDelete() {
                const checked = document.querySelectorAll('.select-item:checked');
                if (!checked.length) return;
                document.getElementById('bulk-delete-ids').value = Array.from(checked).map(function(cb) {
                    return cb.value;
                }).join(',');
                showConfirmModal(
                    '<?php echo e(__('general.confirm')); ?>',
                    '<?php echo e(__('messages.confirm_bulk_delete')); ?>',
                    function(confirmed) {
                        if (confirmed) document.getElementById('bulk-delete-form').submit();
                    },
                    '<?php echo e(__('general.delete')); ?>', 'btn-danger'
                );
            }

            function confirmBulkRestore() {
                const checked = document.querySelectorAll('.select-item:checked');
                if (!checked.length) return;
                document.getElementById('bulk-restore-ids').value = Array.from(checked).map(function(cb) {
                    return cb.value;
                }).join(',');
                showConfirmModal(
                    '<?php echo e(__('general.confirm')); ?>',
                    '<?php echo e(__('messages.confirm_bulk_restore')); ?>',
                    function(confirmed) {
                        if (confirmed) document.getElementById('bulk-restore-form').submit();
                    },
                    '<?php echo e(__('general.restore')); ?>', 'btn-success'
                );
            }

            (function() {
                const selectAll = document.querySelector('.select-all');
                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        document.querySelectorAll('.select-item').forEach(function(cb) {
                            cb.checked = this.checked;
                        }, this);
                        updateBulkButton();
                    });
                }
                document.querySelectorAll('.select-item').forEach(function(cb) {
                    cb.addEventListener('change', updateBulkButton);
                });
            })();
        </script>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('styles'); ?>
        <style>
            .user-avatar {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background: var(--accent);
                color: #0F172A;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 700;
                flex-shrink: 0;
            }

            .online-dot {
                position: absolute;
                bottom: -1px;
                right: -1px;
                width: 9px;
                height: 9px;
                border-radius: 50%;
                border: 2px solid var(--bg);
            }

            .fw-500 {
                font-weight: 500;
            }

            .badge-count {
                display: inline-block;
                font-size: 11px;
                background: var(--bg-subtle);
                color: var(--text);
                padding: 2px 10px;
                border-radius: 6px;
            }

            .badge-more {
                display: inline-block;
                font-size: 10px;
                background: var(--border);
                color: var(--text-muted);
                padding: 2px 8px;
                border-radius: 4px;
            }

            .bulk-btn {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 7px 16px;
                font-size: 13px;
                font-weight: 600;
                border-radius: var(--radius-sm);
                border: 1px solid var(--border);
                background: var(--bg);
                color: var(--text);
                cursor: pointer;
                transition: all 0.15s;
                white-space: nowrap;
            }

            .bulk-btn:hover {
                background: var(--bg-subtle);
                border-color: var(--accent);
            }

            .bulk-count {
                font-size: 12px;
                color: var(--text-muted);
                white-space: nowrap;
                margin-right: 8px;
            }

            .btn-icon {
                width: 30px;
                height: 30px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: var(--radius-xs);
                border: 1px solid var(--border);
                background: transparent;
                color: var(--text-muted);
                font-size: 13px;
                text-decoration: none;
                transition: all 0.15s;
                cursor: pointer;
            }

            .btn-icon:hover {
                background: var(--bg-subtle);
                color: var(--text);
                border-color: var(--accent);
            }

            .btn-icon-danger:hover {
                background: rgba(239, 68, 68, 0.08);
                color: var(--danger);
                border-color: var(--danger);
            }

            .data-table tbody tr {
                transition: background 0.15s;
            }

            .data-table tbody tr:hover {
                background: var(--bg-subtle);
            }

            .data-table th,
            .data-table td {
                padding: 10px 12px;
            }

            .data-table .col-checkbox {
                width: 40px;
                text-align: center;
            }

            .data-table .col-actions {
                width: 80px;
                text-align: right;
            }

            .status-col {
                position: relative;
            }
        </style>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $attributes = $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $component = $__componentOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/super-admin/users.blade.php ENDPATH**/ ?>