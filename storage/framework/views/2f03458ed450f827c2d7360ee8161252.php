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

    <div class="test-checklist-app" x-data="checklistApp()" x-init="init()">
        
        <div class="checklist-header">
            <div class="header-stats">
                <div class="header-stat" @click="filterStatus = null">
                    <span class="stat-icon stat-total"><i class="bi bi-list-check"></i></span>
                    <div class="stat-info">
                        <span class="stat-value" x-text="stats.total"></span>
                        <span class="stat-lbl"><?php echo e(__('super-admin.test_total')); ?></span>
                    </div>
                </div>
                <div class="header-stat cursor-pointer" :class="{ active: filterStatus === 'passed' }" @click="toggleFilter('passed')">
                    <span class="stat-icon stat-pass"><i class="bi bi-check-circle-fill"></i></span>
                    <div class="stat-info">
                        <span class="stat-value" x-text="stats.passed"></span>
                        <span class="stat-lbl"><?php echo e(__('super-admin.test_passed')); ?></span>
                    </div>
                </div>
                <div class="header-stat cursor-pointer" :class="{ active: filterStatus === 'failed' }" @click="toggleFilter('failed')">
                    <span class="stat-icon stat-fail"><i class="bi bi-x-circle-fill"></i></span>
                    <div class="stat-info">
                        <span class="stat-value" x-text="stats.failed"></span>
                        <span class="stat-lbl"><?php echo e(__('super-admin.test_failed')); ?></span>
                    </div>
                </div>
                <div class="header-stat cursor-pointer" :class="{ active: filterStatus === 'skipped' }" @click="toggleFilter('skipped')">
                    <span class="stat-icon stat-skip"><i class="bi bi-dash-circle-fill"></i></span>
                    <div class="stat-info">
                        <span class="stat-value" x-text="stats.skipped"></span>
                        <span class="stat-lbl"><?php echo e(__('super-admin.test_skipped')); ?></span>
                    </div>
                </div>
                <div class="header-stat cursor-pointer" :class="{ active: filterStatus === 'pending' }" @click="toggleFilter('pending')">
                    <span class="stat-icon stat-pend"><i class="bi bi-circle"></i></span>
                    <div class="stat-info">
                        <span class="stat-value" x-text="stats.pending"></span>
                        <span class="stat-lbl"><?php echo e(__('super-admin.test_pending')); ?></span>
                    </div>
                </div>
                <div class="header-progress">
                    <div class="progress-ring">
                        <svg viewBox="0 0 36 36" class="ring-svg">
                            <path class="ring-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            <path class="ring-fill" :stroke-dasharray="progressPercent() + ', 100'" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" :stroke="progressColor()"/>
                            <text x="18" y="21" class="ring-text" text-anchor="middle" x-text="progressPercent() + '%'"></text>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="checklist-toolbar">
            <div class="toolbar-search">
                <i class="bi bi-search search-icon"></i>
                <input type="text" x-model="searchQuery" @keydown.slash.prevent="$refs.searchInput.focus()"
                       x-ref="searchInput" class="search-input"
                       placeholder="<?php echo e(__('general.search')); ?>..." id="checklist-search">
                <button class="search-clear" x-show="searchQuery.length > 0" @click="searchQuery = ''; $refs.searchInput.focus()" type="button">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="toolbar-actions">
                <button class="tb-btn" @click="expandAll()" title="<?php echo e(__('super-admin.test_expand_all')); ?>">
                    <i class="bi bi-arrows-expand"></i>
                </button>
                <button class="tb-btn" @click="collapseAll()" title="<?php echo e(__('super-admin.test_collapse_all')); ?>">
                    <i class="bi bi-arrows-collapse"></i>
                </button>
                <button class="tb-btn tb-btn-danger" @click="confirmReset" title="<?php echo e(__('super-admin.test_reset')); ?>">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
                <form method="POST" action="<?php echo e(route('super.admin.test-checklist.export-markdown')); ?>" style="display:inline">
                    <?php echo csrf_field(); ?>
                    <button class="tb-btn" type="submit" title="<?php echo e(__('super-admin.test_export_md')); ?>">
                        <i class="bi bi-filetype-md"></i>
                    </button>
                </form>
                <form method="POST" action="<?php echo e(route('super.admin.test-checklist.import-markdown')); ?>" style="display:inline"
                      onsubmit="return confirm('<?php echo e(__('super-admin.test_import_confirm')); ?>')">
                    <?php echo csrf_field(); ?>
                    <button class="tb-btn" type="submit" title="<?php echo e(__('super-admin.test_import_md')); ?>">
                        <i class="bi bi-filetype-md"></i> <i class="bi bi-arrow-down-short"></i>
                    </button>
                </form>
            </div>
        </div>

        <form id="reset-form" method="POST" action="<?php echo e(route('super.admin.test-checklist.reset')); ?>" style="display:none">
            <?php echo csrf_field(); ?>
        </form>

        
        <div class="saving-indicator" x-show="saving" x-transition>
            <i class="bi bi-arrow-repeat spin"></i> <span x-text="savingText"></span>
        </div>

        
        <div class="tab-pills">
            <template x-for="tab in tabs" :key="tab.id">
                <button class="tab-pill" :class="{ active: activeTab === tab.id }" @click="activeTab = tab.id">
                    <i :class="tab.id === 'admin' ? 'bi bi-shield-check' : 'bi bi-person-check'"></i>
                    <span x-text="__(tab.label)"></span>
                    <span class="tab-pill-count" x-text="tabStats[tab.id]?.passed + '/' + tabStats[tab.id]?.total"></span>
                </button>
            </template>
        </div>

        
        <template x-for="tab in tabs" :key="tab.id">
            <div x-show="activeTab === tab.id" class="tab-content" x-transition:enter="fade-enter" x-transition:enter-start="op-0" x-transition:enter-end="op-100">
                <template x-for="categoryName in tab.categories" :key="categoryName">
                    <template x-if="itemsByCategory[categoryName] && filteredItems(categoryName).length > 0">
                        <div class="cat-card">
                            <button class="cat-header" @click="toggleCategory(categoryName)" type="button">
                                <div class="cat-header-left">
                                    <i class="bi bi-folder2-open cat-icon"></i>
                                    <div>
                                        <div class="cat-name" x-text="displayName(categoryName)"></div>
                                        <div class="cat-meta">
                                            <span x-text="filteredItems(categoryName).length"></span>
                                            <span class="cat-divider">·</span>
                                            <span :class="catStats(categoryName).passed === catStats(categoryName).total ? 'text-success' : ''">
                                                <span x-text="catStats(categoryName).passed + '/' + catStats(categoryName).total"></span> <?php echo e(__('super-admin.test_passed')); ?>

                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="cat-header-right">
                                    <div class="cat-progress-wrap">
                                        <div class="cat-progress">
                                            <div class="cat-progress-fill" :style="'width:' + catStats(categoryName).percent + '%'"></div>
                                        </div>
                                    </div>
                                    <div class="cat-badges">
                                        <span class="badge badge-p" x-show="catStats(categoryName).passed > 0" x-text="catStats(categoryName).passed"></span>
                                        <span class="badge badge-f" x-show="catStats(categoryName).failed > 0" x-text="catStats(categoryName).failed"></span>
                                    </div>
                                    <i class="bi bi-chevron-down cat-chevron" :class="openCategories.includes(categoryName) ? 'rotated' : ''"></i>
                                </div>
                            </button>
                            <div class="cat-body" x-show="openCategories.includes(categoryName)" x-collapse.duration.200ms>
                                <template x-for="item in filteredItems(categoryName)" :key="item.id">
                                    <div class="check-item" :class="'item-' + item.status">
                                        <div class="item-status">
                                            <button class="status-dot" :class="'dot-' + item.status"
                                                    @click="cycleStatus(item)" :title="'Status: ' + item.status">
                                                <template x-if="item.status === 'passed'"><i class="bi bi-check-circle-fill"></i></template>
                                                <template x-if="item.status === 'failed'"><i class="bi bi-x-circle-fill"></i></template>
                                                <template x-if="item.status === 'skipped'"><i class="bi bi-dash-circle-fill"></i></template>
                                                <template x-if="item.status === 'pending'"><i class="bi bi-circle"></i></template>
                                            </button>
                                        </div>
                                        <div class="item-body">
                                            <div class="item-desc" x-text="item.description"></div>
                                            <div class="item-actions-row">
                                                <button class="ia-btn" @click="toggleDetails(item.id)" x-show="item.details">
                                                    <i class="bi bi-info-circle"></i>
                                                    <span x-text="showDetails === item.id ? '<?php echo e(__('super-admin.test_details')); ?>' : '<?php echo e(__('super-admin.test_details')); ?>'"></span>
                                                </button>
                                                <a href="#" class="ia-btn" @click.prevent="focusNotes(item.id)">
                                                    <i class="bi bi-pencil"></i>
                                                    <span x-text="item.notes ? '<?php echo e(__('general.edit')); ?>' : '<?php echo e(__('general.add')); ?>'"></span>
                                                </a>
                                                <span class="item-tester" x-show="item.tested_by">
                                                    <i class="bi bi-person"></i>
                                                    <span x-text="item.tested_by"></span>
                                                    <span x-show="item.tested_at" x-text="'· ' + item.tested_at"></span>
                                                </span>
                                            </div>
                                            <div class="item-detail" x-show="showDetails === item.id" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                                <div class="detail-box" x-text="item.details"></div>
                                            </div>
                                            <div class="item-notes">
                                                <div class="notes-row">
                                                    <input type="text" x-model="item.notes" :id="'notes-' + item.id"
                                                           class="notes-field"
                                                           placeholder="<?php echo e(__('super-admin.test_notes_placeholder')); ?>"
                                                           @input.debounce.500ms="updateNotes(item.id, item.notes)"
                                                           @focus="editingNotes = item.id"
                                                           @blur="setTimeout(() => editingNotes = null, 200)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-actions">
                                            <button class="st-btn st-pass" :class="{ active: item.status === 'passed' }"
                                                    @click="setStatus(item.id, 'passed')" title="<?php echo e(__('super-admin.test_mark_passed')); ?>">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="st-btn st-fail" :class="{ active: item.status === 'failed' }"
                                                    @click="setStatus(item.id, 'failed')" title="<?php echo e(__('super-admin.test_mark_failed')); ?>">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                            <button class="st-btn st-skip" :class="{ active: item.status === 'skipped' }"
                                                    @click="setStatus(item.id, 'skipped')" title="<?php echo e(__('super-admin.test_mark_skipped')); ?>">
                                                <i class="bi bi-dash-lg"></i>
                                            </button>
                                            <button class="st-btn st-pend" :class="{ active: item.status === 'pending' }"
                                                    @click="setStatus(item.id, 'pending')" title="<?php echo e(__('super-admin.test_mark_pending')); ?>">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </template>
                <div class="empty-state" x-show="tab.categories.every(c => filteredItems(c).length === 0)">
                    <i class="bi bi-search"></i>
                    <p><?php echo e(__('general.no_results')); ?></p>
                </div>
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
                searchQuery: '',
                filterStatus: null,
                saving: false,
                savingText: '',
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
                    this.$watch('searchQuery', () => this.recomputeStats());
                    this.$watch('filterStatus', () => this.recomputeStats());

                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            this.searchQuery = '';
                            this.filterStatus = null;
                            document.activeElement?.blur();
                        }
                    });
                },

                filteredItems(category) {
                    const items = this.itemsByCategory[category] || [];
                    let q = this.searchQuery.toLowerCase().trim();
                    return items.filter(item => {
                        if (this.filterStatus && item.status !== this.filterStatus) return false;
                        if (!q) return true;
                        return item.description.toLowerCase().includes(q)
                            || (item.details && item.details.toLowerCase().includes(q))
                            || (item.notes && item.notes.toLowerCase().includes(q));
                    });
                },

                displayName(cat) {
                    return cat.replace(/^لوحة (الإدارة|المستخدم) \| /, '');
                },

                toggleFilter(status) {
                    this.filterStatus = this.filterStatus === status ? null : status;
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
                    const items = this.filteredItems(category);
                    const total = items.length;
                    const passed = items.filter(i => i.status === 'passed').length;
                    const failed = items.filter(i => i.status === 'failed').length;
                    const done = passed + failed;
                    return { total, passed, failed, done, percent: total > 0 ? Math.round((done / total) * 100) : 0 };
                },

                recomputeStats() {
                    let total = 0, passed = 0, failed = 0, skipped = 0;
                    this.tabs.forEach(tab => {
                        tab.categories.forEach(cat => {
                            this.filteredItems(cat).forEach(item => {
                                total++;
                                if (item.status === 'passed') passed++;
                                else if (item.status === 'failed') failed++;
                                else if (item.status === 'skipped') skipped++;
                            });
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

                showSaving(text) {
                    this.saving = true;
                    this.savingText = text;
                    clearTimeout(this._savingTimer);
                    this._savingTimer = setTimeout(() => { this.saving = false; }, 1500);
                },

                cycleStatus(item) {
                    const order = ['pending', 'passed', 'failed', 'skipped'];
                    const idx = order.indexOf(item.status);
                    const next = order[(idx + 1) % order.length];
                    this.setStatus(item.id, next);
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
                        this.showSaving('Saved');
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

                focusNotes(itemId) {
                    this.editingNotes = itemId;
                    this.$nextTick(() => {
                        const el = document.getElementById('notes-' + itemId);
                        if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                    });
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
        .test-checklist-app {
            max-width: 960px;
            margin: 0 auto;
            font-family: var(--font-en);
        }
        [dir="rtl"] .test-checklist-app {
            font-family: var(--font-ar);
        }

        .checklist-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
        }
        .header-stats {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .header-stat {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.6rem;
            border-radius: var(--radius-sm);
            transition: background var(--transition-fast);
        }
        .header-stat.active {
            background: var(--accent-light);
        }
        .header-stat.cursor-pointer { cursor: pointer; }
        .stat-icon { font-size: 1.1rem; line-height: 1; }
        .stat-total { color: var(--sa-indigo); }
        .stat-pass { color: var(--success); }
        .stat-fail { color: var(--danger); }
        .stat-skip { color: var(--warning); }
        .stat-pend { color: var(--text-muted); }
        .stat-info { display: flex; flex-direction: column; line-height: 1.2; }
        .stat-value { font-weight: 700; font-size: 1rem; }
        .stat-lbl { font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.03em; }
        .header-progress { margin-left: auto; }
        [dir="rtl"] .header-progress { margin-left: 0; margin-right: auto; }
        .progress-ring { width: 40px; height: 40px; }
        .ring-svg { width: 40px; height: 40px; }
        .ring-bg { fill: none; stroke: var(--border); stroke-width: 3; }
        .ring-fill { fill: none; stroke-width: 3; stroke-linecap: round; transition: stroke-dasharray 0.5s; }
        .ring-text { font-size: 7px; font-weight: 700; fill: var(--text); }

        .checklist-toolbar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .toolbar-search {
            position: relative;
            flex: 1;
            max-width: 320px;
        }
        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }
        [dir="rtl"] .search-icon { left: auto; right: 0.75rem; }
        .search-input {
            width: 100%;
            padding: 0.45rem 0.75rem 0.45rem 2rem;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--card-bg);
            color: var(--text);
            font-size: 0.85rem;
            outline: none;
            transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
        }
        [dir="rtl"] .search-input { padding-left: 0.75rem; padding-right: 2rem; }
        .search-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-light);
        }
        .search-clear {
            position: absolute;
            right: 0.4rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.2rem;
            font-size: 0.7rem;
        }
        [dir="rtl"] .search-clear { right: auto; left: 0.4rem; }
        .search-clear:hover { color: var(--text); }

        .toolbar-actions {
            display: flex;
            gap: 0.25rem;
        }
        .tb-btn {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0.4rem 0.6rem;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 0.85rem;
            line-height: 1;
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        .tb-btn:hover { background: var(--bg-subtle); color: var(--text); border-color: var(--text-muted); }
        .tb-btn-danger:hover { color: var(--danger); border-color: var(--danger); }

        .saving-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            color: var(--success);
            padding: 0.2rem 0.6rem;
            border-radius: var(--radius-xs);
            background: var(--success-light);
            margin-bottom: 0.5rem;
        }
        .spin { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .tab-pills {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }
        .tab-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.1rem;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all var(--transition-base);
        }
        .tab-pill:hover { border-color: var(--accent); color: var(--text); }
        .tab-pill.active {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            box-shadow: 0 2px 8px rgba(21,183,108,0.25);
        }
        .tab-pill i { font-size: 1rem; }
        .tab-pill-count {
            font-size: 0.7rem;
            opacity: 0.7;
            font-weight: 600;
        }
        .tab-pill.active .tab-pill-count { opacity: 0.9; }

        .tab-content { min-height: 300px; }

        .cat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 0.75rem;
            overflow: hidden;
            transition: box-shadow var(--transition-fast);
        }
        .cat-card:hover { box-shadow: var(--shadow-sm); }

        .cat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0.75rem 1rem;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: background var(--transition-fast);
            gap: 1rem;
        }
        .cat-header:hover { background: var(--bg-subtle); }
        .cat-header-left {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-align: left;
        }
        [dir="rtl"] .cat-header-left { text-align: right; }
        .cat-icon { color: var(--accent); font-size: 1.1rem; }
        .cat-name { font-weight: 600; font-size: 0.9rem; color: var(--text); }
        .cat-meta { font-size: 0.7rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.35rem; }
        .cat-divider { opacity: 0.4; }
        .text-success { color: var(--success) !important; }
        .cat-header-right { display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0; }
        .cat-progress-wrap { width: 100px; }
        .cat-progress {
            height: 5px;
            background: var(--border);
            border-radius: 999px;
            overflow: hidden;
        }
        .cat-progress-fill {
            height: 100%;
            background: var(--accent);
            border-radius: 999px;
            transition: width 0.3s;
        }
        .badge {
            font-size: 0.65rem;
            padding: 0.1rem 0.35rem;
            border-radius: 999px;
            font-weight: 700;
            min-width: 1.2rem;
            text-align: center;
        }
        .badge-p { background: var(--success-light); color: var(--success); }
        .badge-f { background: var(--danger-light); color: var(--danger); }
        .cat-chevron { color: var(--text-muted); font-size: 0.8rem; transition: transform var(--transition-base); }
        .cat-chevron.rotated { transform: rotate(180deg); }

        .cat-body { border-top: 1px solid var(--border-light); }

        .check-item {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            padding: 0.55rem 1rem;
            border-bottom: 1px solid var(--border-light);
            transition: background var(--transition-fast);
        }
        .check-item:last-child { border-bottom: none; }
        .check-item:hover { background: var(--bg-subtle); }
        .check-item.item-passed { border-left: 3px solid var(--success); }
        .check-item.item-failed { border-left: 3px solid var(--danger); }
        .check-item.item-skipped { border-left: 3px solid var(--warning); }
        .check-item.item-pending { border-left: 3px solid transparent; }
        [dir="rtl"] .check-item { border-left: none; }
        [dir="rtl"] .check-item.item-passed { border-right: 3px solid var(--success); }
        [dir="rtl"] .check-item.item-failed { border-right: 3px solid var(--danger); }
        [dir="rtl"] .check-item.item-skipped { border-right: 3px solid var(--warning); }
        [dir="rtl"] .check-item.item-pending { border-right: 3px solid transparent; }

        .item-status { padding-top: 0.1rem; }
        .status-dot {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.15rem;
            padding: 0;
            line-height: 1;
            transition: transform var(--transition-fast);
        }
        .status-dot:hover { transform: scale(1.15); }
        .dot-passed { color: var(--success); }
        .dot-failed { color: var(--danger); }
        .dot-skipped { color: var(--warning); }
        .dot-pending { color: var(--text-muted); }

        .item-body { flex: 1; min-width: 0; }
        .item-desc { font-size: 0.85rem; color: var(--text); line-height: 1.4; }
        .item-actions-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.2rem;
            flex-wrap: wrap;
        }
        .ia-btn {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-xs);
            color: var(--text-secondary);
            font-size: 0.78rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all var(--transition-fast);
        }
        .ia-btn:hover { background: var(--bg-subtle); color: var(--text); border-color: var(--text-muted); }
        .item-tester { font-size: 0.7rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 0.2rem; }

        .item-detail { margin-top: 0.4rem; }
        .detail-box {
            font-size: 0.78rem;
            color: var(--text-secondary);
            line-height: 1.5;
            background: var(--bg-subtle);
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
        }
        .item-notes { margin-top: 0.3rem; }
        .notes-field {
            width: 100%;
            padding: 0.3rem 0.6rem;
            border: 1px solid var(--border);
            border-radius: var(--radius-xs);
            background: var(--card-bg);
            color: var(--text);
            font-size: 0.78rem;
            outline: none;
            transition: border-color var(--transition-fast);
        }
        .notes-field:focus { border-color: var(--accent); box-shadow: 0 0 0 2px var(--accent-light); }

        .item-actions {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex-shrink: 0;
        }
        .st-btn {
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 0.35rem 0.45rem;
            cursor: pointer;
            font-size: 0.85rem;
            line-height: 1;
            border-radius: var(--radius-xs);
            color: var(--text-muted);
            transition: all var(--transition-fast);
        }
        .st-btn:hover { background: var(--bg-subtle); border-color: var(--text-muted); }
        .st-pass.active, .st-pass:hover { color: var(--success); border-color: var(--success); background: var(--success-light); }
        .st-fail.active, .st-fail:hover { color: var(--danger); border-color: var(--danger); background: var(--danger-light); }
        .st-skip.active, .st-skip:hover { color: var(--warning); border-color: var(--warning); background: var(--warning-light); }
        .st-pend.active, .st-pend:hover { color: var(--text); border-color: var(--text-muted); }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 2rem; display: block; margin-bottom: 0.5rem; }
        .empty-state p { font-size: 0.9rem; }

        @media (max-width: 640px) {
            .header-stats { gap: 0.5rem; }
            .header-stat { padding: 0.2rem 0.4rem; }
            .stat-lbl { display: none; }
            .header-progress { display: none; }
            .checklist-toolbar { flex-direction: column; align-items: stretch; }
            .toolbar-search { max-width: none; }
            .toolbar-actions { justify-content: center; }
            .tab-pills { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .tab-pill { white-space: nowrap; }
            .check-item { flex-wrap: wrap; }
            .item-actions { flex-direction: row; }
            .st-btn { padding: 0.45rem 0.55rem; font-size: 0.9rem; }
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/super-admin/test-checklist.blade.php ENDPATH**/ ?>