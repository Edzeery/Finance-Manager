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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('super-admin.test_checklist')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('super-admin.test_checklist')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('super-admin.test_checklist_desc')); ?> <?php $__env->endSlot(); ?>

    <?php
        $overallPercent = $stats['total'] > 0 ? round((($stats['passed'] + $stats['failed']) / $stats['total']) * 100) : 0;
    ?>

    <div class="test-checklist-page" x-data="checklistApp()">
        <div class="checklist-stats-grid">
            <div class="stat-card stat-total">
                <div class="stat-value" x-text="stats.total"></div>
                <div class="stat-label"><?php echo e(__('super-admin.test_total')); ?></div>
            </div>
            <div class="stat-card stat-passed">
                <div class="stat-value" x-text="stats.passed"></div>
                <div class="stat-label"><?php echo e(__('super-admin.test_passed')); ?></div>
            </div>
            <div class="stat-card stat-failed">
                <div class="stat-value" x-text="stats.failed"></div>
                <div class="stat-label"><?php echo e(__('super-admin.test_failed')); ?></div>
            </div>
            <div class="stat-card stat-skipped">
                <div class="stat-value" x-text="stats.skipped"></div>
                <div class="stat-label"><?php echo e(__('super-admin.test_skipped')); ?></div>
            </div>
            <div class="stat-card stat-pending">
                <div class="stat-value" x-text="stats.pending"></div>
                <div class="stat-label"><?php echo e(__('super-admin.test_pending')); ?></div>
            </div>
            <div class="stat-card stat-overall">
                <div class="stat-value" x-text="progressPercent() + '%'"></div>
                <div class="stat-label"><?php echo e(__('super-admin.test_overall')); ?></div>
                <div class="progress-bar" style="margin-top:0.5rem;height:6px">
                    <div class="progress-fill" :style="'width:' + progressPercent() + '%;background:' + progressColor()"></div>
                </div>
            </div>
        </div>

        <div class="checklist-actions">
            <button class="btn btn-outline" @click="expandAll()">
                <i class="bi bi-arrows-expand"></i> <?php echo e(__('super-admin.test_expand_all')); ?>

            </button>
            <button class="btn btn-outline" @click="collapseAll()">
                <i class="bi bi-arrows-collapse"></i> <?php echo e(__('super-admin.test_collapse_all')); ?>

            </button>
            <button class="btn btn-outline-danger" @click="confirmReset">
                <i class="bi bi-arrow-counterclockwise"></i> <?php echo e(__('super-admin.test_reset')); ?>

            </button>
            <form method="POST" action="<?php echo e(route('super.admin.test-checklist.export-markdown')); ?>" style="display:inline">
                <?php echo csrf_field(); ?>
                <button class="btn btn-outline" type="submit">
                    <i class="bi bi-filetype-md"></i> <?php echo e(__('super-admin.test_export_md')); ?>

                </button>
            </form>
            <form method="POST" action="<?php echo e(route('super.admin.test-checklist.import-markdown')); ?>" style="display:inline" onsubmit="return confirm('<?php echo e(__('super-admin.test_import_confirm')); ?>')">
                <?php echo csrf_field(); ?>
                <button class="btn btn-outline" type="submit">
                    <i class="bi bi-filetype-md"></i> <?php echo e(__('super-admin.test_import_md')); ?>

                </button>
            </form>
        </div>

        <form id="reset-form" method="POST" action="<?php echo e(route('super.admin.test-checklist.reset')); ?>" style="display:none">
            <?php echo csrf_field(); ?>
        </form>

        <div class="checklist-tabs">
            <template x-for="tab in tabs" :key="tab.id">
                <button class="tab-btn" :class="{ active: activeTab === tab.id }" @click="activeTab = tab.id">
                    <span class="tab-label" x-text="__(tab.label)"></span>
                    <span class="tab-count" x-text="'(' + (tabStats[tab.id]?.passed || 0) + '/' + (tabStats[tab.id]?.total || 0) + ')'"></span>
                </button>
            </template>
        </div>

        <template x-for="tab in tabs" :key="tab.id">
            <div x-show="activeTab === tab.id" class="tab-panel">
                <template x-for="categoryName in tab.categories" :key="categoryName">
                    <template x-if="itemsByCategory[categoryName] && itemsByCategory[categoryName].length > 0">
                        <div class="checklist-category">
                            <button class="category-header" @click="toggleCategory(categoryName)" type="button">
                                <div class="category-info">
                                    <span class="category-name" x-text="categoryName"></span>
                                    <span class="category-count" x-text="'(' + catStats(categoryName).done + '/' + catStats(categoryName).total + ')'"></span>
                                </div>
                                <div class="category-meta">
                                    <div class="category-progress">
                                        <div class="progress-bar" style="height:6px">
                                            <div class="progress-fill" :style="'width:' + catStats(categoryName).percent + '%'"></div>
                                        </div>
                                    </div>
                                    <div class="category-status-tags">
                                        <span class="badge badge-success" x-show="catStats(categoryName).passed > 0" x-text="catStats(categoryName).passed + ' \u2713'"></span>
                                        <span class="badge badge-danger" x-show="catStats(categoryName).failed > 0" x-text="catStats(categoryName).failed + ' \u2717'"></span>
                                    </div>
                                    <i class="bi bi-chevron-down chevron" :class="openCategories.includes(categoryName) ? 'rotated' : ''"></i>
                                </div>
                            </button>
                            <div class="category-body" x-show="openCategories.includes(categoryName)" x-collapse>
                                <template x-for="item in itemsByCategory[categoryName]" :key="item.id">
                                    <div class="checklist-item">
                                        <div class="item-main">
                                            <div class="item-status-icons">
                                                <button class="status-btn status-pass" :class="{ active: item.status === 'passed' }"
                                                        @click="setStatus(item.id, 'passed')" title="<?php echo e(__('super-admin.test_mark_passed')); ?>">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </button>
                                                <button class="status-btn status-fail" :class="{ active: item.status === 'failed' }"
                                                        @click="setStatus(item.id, 'failed')" title="<?php echo e(__('super-admin.test_mark_failed')); ?>">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </button>
                                                <button class="status-btn status-skip" :class="{ active: item.status === 'skipped' }"
                                                        @click="setStatus(item.id, 'skipped')" title="<?php echo e(__('super-admin.test_mark_skipped')); ?>">
                                                    <i class="bi bi-dash-circle-fill"></i>
                                                </button>
                                                <button class="status-btn status-pending" :class="{ active: item.status === 'pending' }"
                                                        @click="setStatus(item.id, 'pending')" title="<?php echo e(__('super-admin.test_mark_pending')); ?>">
                                                    <i class="bi bi-circle"></i>
                                                </button>
                                            </div>
                                            <div class="item-content">
                                                <span class="item-description" x-text="item.description"></span>
                                                <span class="item-tester text-muted small" x-show="item.tested_by">
                                                    <i class="bi bi-person"></i> <span x-text="item.tested_by"></span>
                                                    <span x-show="item.tested_at" x-text="'\u00b7 ' + item.tested_at"></span>
                                                </span>
                                            </div>
                                            <div class="item-notes-toggle">
                                                <button class="btn btn-sm btn-ghost" @click="toggleDetails(item.id)" title="<?php echo e(__('super-admin.test_details')); ?>" x-show="item.details">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                                <button class="btn btn-sm btn-ghost" @click="focusNotes(categoryName, item.id)" title="<?php echo e(__('general.notes')); ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="item-details" x-show="showDetails === item.id" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                            <div class="details-text" x-text="item.details"></div>
                                        </div>
                                        <div class="item-notes" x-show="item.notes || editingNotes === item.id">
                                            <div class="notes-row">
                                                <input type="text" x-model="item.notes" :id="'notes-' + item.id"
                                                       class="form-control form-control-sm notes-input"
                                                       placeholder="<?php echo e(__('super-admin.test_notes_placeholder')); ?>"
                                                       @change.debounce="updateNotes(item.id, item.notes)"
                                                       @focus="editingNotes = item.id"
                                                       @blur="editingNotes = null">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
        </template>
    </div>

    <script>
        window.checklistApp = function () {
            return {
                activeTab: 'admin',
                openCategories: [],
                editingNotes: null,
                showDetails: null,
                tabs: <?php echo json_encode($tabs); ?>,
                tabStats: <?php echo json_encode($tabStats); ?>,
                stats: <?php echo json_encode($stats); ?>,
                itemsByCategory: {},

                init() {
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $catItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    this.itemsByCategory['<?php echo e($category); ?>'] = <?php echo json_encode($catItems->map(fn($i) => [
                        'id' => $i->id,
                        'item_key' => $i->item_key,
                        'category' => $i->category,
                        'description' => $i->description,
                        'details' => $i->details,
                        'status' => $i->status,
                        'notes' => $i->notes,
                        'tested_by' => $i->tester?->name,
                        'tested_at' => $i->tested_at?->diffForHumans(),
                    ])->values()); ?>;
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                },

                __(key) {
                    const labels = {
                        <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        '<?php echo e($tab['label']); ?>': '<?php echo e(__($tab['label'])); ?>',
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    };
                    return labels[key] || key;
                },

                catStats(category) {
                    const items = this.itemsByCategory[category] || [];
                    const total = items.length;
                    const passed = items.filter(i => i.status === 'passed').length;
                    const failed = items.filter(i => i.status === 'failed').length;
                    const done = passed + failed;
                    return { total, passed, failed, done, percent: total > 0 ? Math.round((done / total) * 100) : 0 };
                },

                recomputeStats() {
                    let total = 0, passed = 0, failed = 0, skipped = 0;
                    Object.values(this.itemsByCategory).forEach(cat => {
                        cat.forEach(item => {
                            total++;
                            if (item.status === 'passed') passed++;
                            else if (item.status === 'failed') failed++;
                            else if (item.status === 'skipped') skipped++;
                        });
                    });
                    this.stats = { total, passed, failed, skipped, pending: total - passed - failed - skipped };
                    this.tabs.forEach(tab => {
                        let t = 0, p = 0, f = 0;
                        tab.categories.forEach(cat => {
                            const items = this.itemsByCategory[cat] || [];
                            t += items.length;
                            p += items.filter(i => i.status === 'passed').length;
                            f += items.filter(i => i.status === 'failed').length;
                        });
                        this.tabStats[tab.id] = { total: t, passed: p, failed: f, done: p + f, percent: t > 0 ? Math.round(((p + f) / t) * 100) : 0 };
                    });
                },

                progressPercent() {
                    const t = this.stats.total;
                    return t === 0 ? 0 : Math.round(((this.stats.passed + this.stats.failed) / t) * 100);
                },

                progressColor() {
                    const p = this.progressPercent();
                    return p >= 90 ? '#22c55e' : p >= 70 ? '#eab308' : '#ef4444';
                },

                async setStatus(itemId, newStatus) {
                    let foundItem = null;
                    for (const [, items] of Object.entries(this.itemsByCategory)) {
                        const idx = items.findIndex(i => i.id === itemId);
                        if (idx !== -1) { foundItem = items[idx]; break; }
                    }
                    if (!foundItem) return;
                    const oldStatus = foundItem.status;
                    foundItem.status = newStatus;
                    try {
                        const url = '<?php echo e(route("super.admin.test-checklist.update", 0)); ?>'.replace('0', itemId);
                        const resp = await fetch(url, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
                            body: JSON.stringify({ status: newStatus }),
                        });
                        if (!resp.ok) throw new Error('Failed');
                        const data = await resp.json();
                        if (data.item) {
                            foundItem.status = data.item.status;
                            foundItem.tested_by = data.item.tested_by;
                            foundItem.tested_at = data.item.tested_at;
                        }
                        this.recomputeStats();
                    } catch (e) { foundItem.status = oldStatus; }
                },

                toggleDetails(id) {
                    this.showDetails = this.showDetails === id ? null : id;
                },

                async updateNotes(itemId, notes) {
                    try {
                        const url = '<?php echo e(route("super.admin.test-checklist.notes", 0)); ?>'.replace('0', itemId);
                        await fetch(url, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
                            body: JSON.stringify({ notes }),
                        });
                    } catch (e) {}
                },

                focusNotes(category, itemId) {
                    const el = document.getElementById('notes-' + itemId);
                    if (el) el.focus();
                },

                toggleCategory(name) {
                    const idx = this.openCategories.indexOf(name);
                    if (idx === -1) { this.openCategories.push(name); }
                    else { this.openCategories.splice(idx, 1); }
                },

                expandAll() {
                    this.tabs.forEach(tab => {
                        tab.categories.forEach(cat => {
                            if (this.itemsByCategory[cat]?.length && !this.openCategories.includes(cat)) {
                                this.openCategories.push(cat);
                            }
                        });
                    });
                },

                collapseAll() { this.openCategories = []; },

                confirmReset() {
                    if (confirm('<?php echo e(__('super-admin.test_reset_confirm')); ?>')) {
                        document.getElementById('reset-form').submit();
                    }
                },
            };
        };
    </script>

    <?php $__env->startPush('styles'); ?>
    <style>
        .test-checklist-page { max-width: 960px; margin: 0 auto; }
        .checklist-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card { background: var(--card-bg, #fff); border: 1px solid var(--border-color, #e5e7eb); border-radius: 0.75rem; padding: 1rem; text-align: center; }
        .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1.2; }
        .stat-label { font-size: 0.8rem; color: var(--text-muted, #6b7280); margin-top: 0.25rem; }
        .stat-total .stat-value { color: var(--primary, #6366f1); }
        .stat-passed .stat-value { color: #22c55e; }
        .stat-failed .stat-value { color: #ef4444; }
        .stat-skipped .stat-value { color: #f59e0b; }
        .stat-pending .stat-value { color: #9ca3af; }
        .stat-overall .stat-value { color: var(--text, #111827); }
        .checklist-actions { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .checklist-tabs { display: flex; gap: 0.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; border-bottom: 2px solid var(--border-color, #e5e7eb); padding-bottom: 0; }
        .tab-btn { background: none; border: none; padding: 0.6rem 1rem; cursor: pointer; font-size: 0.85rem; color: var(--text-muted, #6b7280); border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.4rem; }
        .tab-btn:hover { color: var(--text, #111827); }
        .tab-btn.active { color: var(--primary, #6366f1); border-bottom-color: var(--primary, #6366f1); font-weight: 600; }
        .tab-label { font-weight: 500; }
        .tab-count { font-size: 0.75rem; opacity: 0.7; }
        .tab-panel { min-height: 200px; }
        .checklist-category { margin-bottom: 0.75rem; border: 1px solid var(--border-color, #e5e7eb); border-radius: 0.75rem; overflow: hidden; background: var(--card-bg, #fff); }
        .category-header { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 0.75rem 1rem; background: var(--bg-subtle, #f9fafb); border: none; cursor: pointer; font-size: 0.95rem; }
        .category-header:hover { background: var(--bg-hover, #f3f4f6); }
        .category-info { display: flex; align-items: center; gap: 0.75rem; }
        .category-name { font-weight: 600; }
        .category-count { font-size: 0.8rem; color: var(--text-muted, #6b7280); }
        .category-meta { display: flex; align-items: center; gap: 0.75rem; }
        .category-progress { width: 100px; }
        .category-progress .progress-bar { height: 6px; }
        .progress-bar { width: 100%; background: var(--border-color, #e5e7eb); border-radius: 999px; overflow: hidden; }
        .progress-fill { height: 100%; background: #6366f1; border-radius: 999px; transition: width 0.3s; }
        .category-status-tags { display: flex; gap: 0.25rem; }
        .badge { font-size: 0.7rem; padding: 0.15rem 0.4rem; border-radius: 0.25rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .chevron { transition: transform 0.2s; }
        .chevron.rotated { transform: rotate(180deg); }
        .category-body { border-top: 1px solid var(--border-color, #e5e7eb); }
        .checklist-item { padding: 0.5rem 1rem; border-bottom: 1px solid var(--border-color, #e5e7eb); }
        .checklist-item:last-child { border-bottom: none; }
        .item-main { display: flex; align-items: center; gap: 0.75rem; }
        .item-status-icons { display: flex; gap: 0.15rem; flex-shrink: 0; }
        .status-btn { background: none; border: 1px solid transparent; border-radius: 0.25rem; padding: 0.2rem 0.3rem; cursor: pointer; opacity: 0.35; transition: all 0.15s; font-size: 1rem; line-height: 1; }
        .status-btn:hover { opacity: 0.7; }
        .status-btn.active { opacity: 1; }
        .status-pass.active { color: #22c55e; }
        .status-fail.active { color: #ef4444; }
        .status-skip.active { color: #f59e0b; }
        .status-pending.active { color: #9ca3af; }
        .item-content { flex: 1; display: flex; flex-direction: column; }
        .item-description { font-size: 0.875rem; }
        .item-tester { font-size: 0.75rem; margin-top: 0.1rem; }
        .btn-ghost { background: none; border: none; color: var(--text-muted, #6b7280); cursor: pointer; padding: 0.25rem; }
        .btn-ghost:hover { color: var(--text, #111827); }
        .item-details { padding: 0.25rem 0 0.5rem 2.5rem; }
        .details-text { font-size: 0.8rem; color: var(--text-muted, #6b7280); line-height: 1.5; background: var(--bg-subtle, #f9fafb); padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color, #e5e7eb); white-space: pre-wrap; }
        .item-notes { padding: 0.25rem 0 0.5rem 2.5rem; }
        .notes-input { font-size: 0.8rem; }
        .btn-outline { background: none; border: 1px solid var(--border-color, #e5e7eb); border-radius: 0.5rem; padding: 0.4rem 0.75rem; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem; }
        .btn-outline:hover { background: var(--bg-hover, #f3f4f6); }
        .btn-outline-danger { background: none; border: 1px solid #fca5a5; border-radius: 0.5rem; padding: 0.4rem 0.75rem; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem; color: #dc2626; }
        .btn-outline-danger:hover { background: #fee2e2; }
        .text-muted { color: var(--text-muted, #6b7280); }
        .small { font-size: 0.75rem; }
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\test-checklist.blade.php ENDPATH**/ ?>