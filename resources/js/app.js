import './bootstrap';
import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';
import ApexCharts from 'apexcharts';

window.bootstrap = bootstrap;
window.Chart = Chart;
window.ApexCharts = ApexCharts;

// ===== Alpine Components & Stores =====
document.addEventListener('alpine:init', () => {
    Alpine.data('profileDropdown', function () {
        return {
            open: false,
            toggle() {
                this.open = !this.open;
            },
            close() {
                this.open = false;
            },
        }
    });

    Alpine.data('dateFilterBar', function (initialPeriod, initialStartDate, initialEndDate) {
        return {
            period: initialPeriod || 'all_time',
            startDate: initialStartDate || '',
            endDate: initialEndDate || '',
            init() {
                if (this.period === 'custom' && !this.startDate) {
                    var end = new Date();
                    var start = new Date();
                    start.setMonth(start.getMonth() - 1);
                    this.startDate = start.toISOString().split('T')[0];
                    this.endDate = end.toISOString().split('T')[0];
                }
            },
            setCustom() {
                this.period = 'custom';
                if (!this.startDate) {
                    var end = new Date();
                    var start = new Date();
                    start.setMonth(start.getMonth() - 1);
                    this.startDate = start.toISOString().split('T')[0];
                    this.endDate = end.toISOString().split('T')[0];
                }
            }
        }
    });

    Alpine.data('superAdminChart', function (chartId, chartType, options) {
        return {
            chart: null,
            init() {
                var opts = Object.assign({
                    chart: { id: chartId, type: chartType, toolbar: { show: false }, fontFamily: 'inherit' },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    grid: { borderColor: 'var(--border)', strokeDashArray: 4 },
                    tooltip: { theme: 'dark' },
                    xaxis: { labels: { style: { colors: 'var(--text-muted)', fontSize: '11px' } } },
                    yaxis: { labels: { style: { colors: 'var(--text-muted)', fontSize: '11px' } } },
                    legend: { position: 'top', labels: { colors: 'var(--text-muted)' } },
                }, options);
                var el = this.$el;
                this.$nextTick(function () {
                    this.chart = new ApexCharts(el, opts);
                    this.chart.render();
                });
            },
            destroy() {
                if (this.chart) this.chart.destroy();
            },
        }
    });

    Alpine.data('commandPalette', function () {
        return {
            open: false,
            searchQuery: '',
            selectedIndex: 0,
            items: [],
            get filteredItems() {
                if (!this.searchQuery) return this.items;
                var q = this.searchQuery.toLowerCase();
                return this.items.filter(function (i) {
                    return i.title.toLowerCase().includes(q) ||
                           i.description.toLowerCase().includes(q);
                });
            },
            init() {
                var self = this;
                this.$watch('open', function (val) {
                    if (val) {
                        self.$nextTick(function () {
                            if (self.$refs.searchInput) {
                                self.searchQuery = '';
                                self.selectedIndex = 0;
                                self.$refs.searchInput.focus();
                            }
                        });
                    }
                });
                if (!window._cmdPaletteListeners) {
                    window.addEventListener('toggle-cmd-palette', function () {
                        self.open = !self.open;
                    });
                    document.addEventListener('keydown', function (e) {
                        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                            e.preventDefault();
                            self.open = !self.open;
                        }
                        if (e.key === 'Escape') {
                            self.open = false;
                        }
                    });
                    window._cmdPaletteListeners = true;
                }
            },
            executeCommand(index) {
                var item = this.filteredItems[index];
                if (item) {
                    if (typeof Livewire !== 'undefined' && Livewire.navigate) {
                        Livewire.navigate(item.url);
                    } else {
                        window.location.href = item.url;
                    }
                    this.open = false;
                }
            }
        }
    });

    Alpine.data('appLayout', function () {
        return {
            collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            init() {
                var self = this;
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                    try { new bootstrap.Tooltip(el); } catch(e) {}
                });
            },
            toggleSidebar() {
                this.collapsed = !this.collapsed;
                localStorage.setItem('sidebarCollapsed', this.collapsed);
            }
        }
    });

    Alpine.data('superAdminLayout', function () {
        return {
            sidebarCollapsed: localStorage.getItem('sa_sidebar_collapsed') === 'true',
            toggleSidebar() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sa_sidebar_collapsed', this.sidebarCollapsed);
            }
        }
    });

    Alpine.store('theme', {
        mode: document.documentElement.getAttribute('data-theme') || 'light',

        set(mode) {
            this.mode = mode;
            document.documentElement.setAttribute('data-theme', mode);
            document.querySelectorAll('[data-theme-toggle] i').forEach(el => {
                el.className = 'bi ' + (mode === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill');
            });
            const url = document.querySelector('meta[name="theme-switch-url"]')?.content;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            if (url && csrf) {
                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ theme: mode }),
                });
            }
        },

        toggle() {
            this.set(this.mode === 'dark' ? 'light' : 'dark');
        }
    });

    Alpine.store('bulkSelect', {
        items: [],

        toggle(id) {
            const idx = this.items.indexOf(id);
            if (idx > -1) this.items.splice(idx, 1);
            else this.items.push(id);
        },

        selectAll(ids) {
            this.items = [...ids];
        },

        clear() {
            this.items = [];
        },

        isSelected(id) {
            return this.items.includes(id);
        },

        get count() {
            return this.items.length;
        }
    });
});

// ===== Global Helpers =====

window.toggleTheme = function () {
    if (window.Alpine && Alpine.store('theme')) {
        Alpine.store('theme').toggle();
        return;
    }
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    const url = document.querySelector('meta[name="theme-switch-url"]')?.content;
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!url || !csrfMeta) return;
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfMeta.content },
        body: JSON.stringify({ theme: next }),
    }).finally(function () {
        html.setAttribute('data-theme', next);
        document.querySelectorAll('[data-theme-toggle] i').forEach(function (el) {
            el.className = 'bi ' + (next === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill');
        });
    });
};

window.toggleMobileSearch = function () {
    const bar = document.getElementById('mobileSearchBar');
    const input = document.getElementById('mobileSearchInput');
    if (bar) {
        bar.classList.toggle('show');
        if (bar.classList.contains('show')) {
            setTimeout(() => input?.focus(), 100);
        }
    }
};

window.toggleSidebarMobile = function () {
    document.body.classList.toggle('sidebar-open');
};

window.closeSidebarMobile = function () {
    document.body.classList.remove('sidebar-open');
};

window.formatCurrency = function(value) {
    var locale = document.documentElement.lang || 'en';
    var symbol = '';
    var meta = document.querySelector('meta[name="currency-symbol"]');
    if (meta) symbol = meta.content;
    return new Intl.NumberFormat(
        locale === 'ar' ? 'ar-EG' : locale === 'fr' ? 'fr-FR' : 'en-US',
        { minimumFractionDigits: 2, maximumFractionDigits: 2 }
    ).format(value) + ' ' + symbol;
};

window.showEmptyChart = function(containerId, message) {
    var container = document.getElementById(containerId);
    if (!container) return;
    if (container.querySelector('.empty-state')) return;
    var canvas = container.querySelector('canvas');
    if (canvas) canvas.style.display = 'none';
    var el = document.createElement('div');
    el.className = 'empty-state';
    el.style.cssText = 'display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:260px;gap:12px';
    el.innerHTML = '<i class="bi bi-bar-chart" style="font-size:32px;color:var(--text-muted);opacity:0.4"></i><p style="color:var(--text-muted);font-size:14px;margin:0">' + message + '</p>';
    container.appendChild(el);
};

window.destroyExistingCharts = function() {
    ['incomeExpenseChart', 'expenseCategoriesChart', 'cashFlowChart', 'financialGrowthChart'].forEach(function(id) {
        var c = document.getElementById(id);
        if (c) { var ch = Chart.getChart(c); if (ch) ch.destroy(); }
    });
};

window.showToast = function(type, message, title = null) {
    if (window.Toast && typeof window.Toast[type] === 'function') {
        window.Toast[type](message, title);
        return;
    }
    const icons = { success: 'bi-check-circle', error: 'bi-x-circle', warning: 'bi-exclamation-triangle', info: 'bi-info-circle' };
    const colors = { success: 'var(--success)', error: 'var(--danger)', warning: 'var(--warning)', info: 'var(--info)' };
    const isRtl = document.documentElement.dir === 'rtl';
    const toast = document.createElement('div');
    toast.className = 'toast-fallback';
    toast.style.cssText = `position:fixed;top:20px;inset-inline-end:20px;z-index:10000;background:white;color:#0F172A;padding:14px 20px;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,0.15);display:flex;align-items:center;gap:12px;font-size:14px;max-width:400px;transform:translateX(${isRtl ? '-120%' : '120%'});transition:transform 0.3s ease;border-inline-start:4px solid ${colors[type] || colors.info}`;
    toast.innerHTML = `<i class="bi ${icons[type] || icons.info}" style="color:${colors[type] || colors.info};font-size:20px;flex-shrink:0"></i><div style="flex:1">${message || ''}</div>`;
    document.body.appendChild(toast);
    requestAnimationFrame(() => { toast.style.transform = 'translateX(0)'; });
    const exitDir = isRtl ? '-120%' : '120%';
    setTimeout(() => {
        toast.style.transform = `translateX(${exitDir})`;
        setTimeout(() => toast.remove(), 300);
    }, 5000);
};

window.handleAjaxError = function(error, fallbackMessage = 'An unexpected error occurred') {
    console.error('AJAX Error:', error);
    const msg = error?.response?.data?.message || error?.message || fallbackMessage;
    if (window.showToast) {
        window.showToast('error', msg);
    } else {
        alert(msg);
    }
};



// ===== Confirm Modal (Event Delegation) =====

let confirmModalCallback = null;
let confirmModalPasswordMode = false;

function pwKeyHandler(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const actionBtn = document.getElementById('confirmModalAction');
        if (actionBtn) actionBtn.click();
    }
}

window.showConfirmModal = function(title, message, callback, btnText = null, btnClass = 'btn-danger', requirePassword = false) {
    const modalEl = document.getElementById('confirmModal');
    if (!modalEl) {
        if (callback) { const c = confirm(message); callback(c); }
        return;
    }
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const iconEl = document.getElementById('confirmModalIcon');
    const titleEl = document.getElementById('confirmModalTitle');
    const bodyEl = document.getElementById('confirmModalBody');
    const actionBtn = document.getElementById('confirmModalAction');
    const pwWrap = document.getElementById('confirmModalPasswordWrap');
    const pwInput = document.getElementById('confirmModalPassword');
    const pwError = document.getElementById('confirmModalPasswordError');
    if (!actionBtn) return;
    confirmModalPasswordMode = requirePassword;
    if (titleEl) titleEl.textContent = title;
    if (bodyEl) bodyEl.textContent = message;
    actionBtn.textContent = btnText || actionBtn.getAttribute('data-default-text') || 'Delete';
    actionBtn.className = 'btn btn-custom px-4 ' + (btnClass || 'btn-danger');
    if (iconEl) {
        iconEl.className = 'confirm-modal-icon mx-auto';
        const icon = iconEl.querySelector('i');
        if (icon) {
            if (btnClass?.includes('btn-danger')) {
                icon.className = 'bi bi-exclamation-triangle-fill';
            } else if (btnClass?.includes('btn-accent') || btnClass?.includes('btn-primary')) {
                icon.className = 'bi bi-shield-check';
            } else {
                icon.className = 'bi bi-info-circle-fill';
            }
        }
    }
    if (pwWrap && pwInput && pwError) {
        pwWrap.style.display = requirePassword ? 'block' : 'none';
        if (requirePassword) {
            pwInput.value = '';
            pwError.style.display = 'none';
            pwInput.removeEventListener('keydown', pwKeyHandler);
            pwInput.addEventListener('keydown', pwKeyHandler);
            setTimeout(() => pwInput.focus(), 200);
        }
    }
    confirmModalCallback = (confirmed, password) => {
        modal.hide();
        if (callback) callback(confirmed, password);
        confirmModalCallback = null;
    };
    modal.show();
};

// Event delegation for confirm modal action button
document.addEventListener('click', function(e) {
    const actionBtn = e.target.closest('#confirmModalAction');
    if (!actionBtn) return;
    if (confirmModalPasswordMode) {
        var pwInput = document.getElementById('confirmModalPassword');
        var pwError = document.getElementById('confirmModalPasswordError');
        var password = pwInput ? pwInput.value : '';
        if (!password) {
            var modalEl2 = document.getElementById('confirmModal');
            var pwRequired = modalEl2 ? modalEl2.getAttribute('data-password-required') : '';
            if (pwError) { pwError.textContent = pwRequired || 'Password required'; pwError.style.display = 'block'; }
            return;
        }
        const verifyUrl = document.querySelector('meta[name="password-verify-url"]')?.content;
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!verifyUrl || !csrfMeta) {
            if (confirmModalCallback) confirmModalCallback(true, password);
            return;
        }
        actionBtn.disabled = true;
        actionBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        fetch(verifyUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfMeta.content },
            body: JSON.stringify({ password }),
        }).then(r => r.json()).then(data => {
            if (data.valid) {
                if (pwError) pwError.style.display = 'none';
                if (confirmModalCallback) confirmModalCallback(true, password);
            } else {
                if (pwError) {
                    var modalEl3 = document.getElementById('confirmModal');
                    var pwIncorrect = modalEl3 ? modalEl3.getAttribute('data-password-incorrect') : '';
                    pwError.textContent = pwIncorrect || 'Incorrect password';
                    pwError.style.display = 'block';
                }
                pwInput?.select();
            }
        }).catch(() => {
            if (confirmModalCallback) confirmModalCallback(true, password);
        }).finally(() => {
            actionBtn.disabled = false;
            actionBtn.textContent = actionBtn.getAttribute('data-default-text') || 'Delete';
        });
    } else {
        if (confirmModalCallback) confirmModalCallback(true);
    }
});

// Clear callback when modal is hidden
document.addEventListener('hidden.bs.modal', function(e) {
    if (e.target.id === 'confirmModal') {
        var pwWrap = document.getElementById('confirmModalPasswordWrap');
        if (pwWrap) pwWrap.style.display = 'none';
        if (confirmModalCallback) confirmModalCallback(false);
    }
});

// ===== Bulk Select Helpers =====

// Event delegation: any .select-item checkbox change updates the bulk bar
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('select-item')) {
        updateBulkBar();
    }
});

window.toggleSelectAll = function(master) {
    document.querySelectorAll('.select-item').forEach(cb => cb.checked = master.checked);
    updateBulkBar();
};

window.updateBulkBar = function() {
    const checked = document.querySelectorAll('.select-item:checked');
    const bar = document.getElementById('bulkBar');
    const countEl = document.getElementById('selectedCount');
    if (!bar || !countEl) return;
    if (checked.length > 0) {
        bar.style.display = 'flex';
        countEl.textContent = checked.length;
    } else {
        bar.style.display = 'none';
    }
};

window.submitBulk = function(actionUrl) {
    const form = document.createElement('form');
    form.method = 'POST'; form.action = actionUrl; form.style.display = 'none';
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) { const inp = document.createElement('input'); inp.type = 'hidden'; inp.name = '_token'; inp.value = meta.content; form.appendChild(inp); }
    document.querySelectorAll('.select-item:checked').forEach(function(cb) {
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
        form.appendChild(inp);
    });
    document.body.appendChild(form); form.submit();
};

window.submitBulkRestore = function(route) {
    submitBulk(route);
};

window.confirmDelete = function(resource, id) {
    const modalEl = document.getElementById('confirmModal');
    const formEl = document.getElementById('delete-form-' + resource + '-' + id);
    var action = formEl ? formEl.getAttribute('action') : '';
    showConfirmModal(
        modalEl?.getAttribute('data-confirm-text') || 'Confirm',
        modalEl?.getAttribute('data-delete-text') || 'Are you sure?',
        function(confirmed) {
            if (confirmed && action) {
                var f = document.createElement('form');
                f.method = 'POST'; f.action = action; f.style.display = 'none';
                var meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) { var inp = document.createElement('input'); inp.type = 'hidden'; inp.name = '_token'; inp.value = meta.content; f.appendChild(inp); }
                var m = document.createElement('input'); m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE'; f.appendChild(m);
                document.body.appendChild(f); f.submit();
            }
        },
        'Delete',
        'btn-danger'
    );
};

window.confirmForceDelete = function(resource, id) {
    const modalEl = document.getElementById('confirmModal');
    const formEl = document.getElementById('force-delete-form-' + resource + '-' + id);
    var action = formEl ? formEl.getAttribute('action') : '';
    showConfirmModal(
        modalEl?.getAttribute('data-confirm-text') || 'Confirm',
        modalEl?.getAttribute('data-force-delete-text') || 'Permanently delete?',
        function(confirmed) {
            if (confirmed && action) {
                var f = document.createElement('form');
                f.method = 'POST'; f.action = action; f.style.display = 'none';
                var meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) { var inp = document.createElement('input'); inp.type = 'hidden'; inp.name = '_token'; inp.value = meta.content; f.appendChild(inp); }
                var m = document.createElement('input'); m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE'; f.appendChild(m);
                document.body.appendChild(f); f.submit();
            }
        },
        document.getElementById('confirmModalAction')?.getAttribute('data-force-delete-text') || 'Force Delete',
        'btn-danger'
    );
};

window.confirmBulkDelete = function(resource) {
    const modalEl = document.getElementById('confirmModal');
    const checked = document.querySelectorAll('.select-item:checked');
    if (checked.length === 0) return;
    const formEl = document.getElementById('bulkForm');
    var action = formEl ? formEl.getAttribute('data-bulk-delete-route') : '';
    showConfirmModal(
        modalEl?.getAttribute('data-confirm-text') || 'Confirm',
        (modalEl?.getAttribute('data-bulk-delete-text') || 'Delete selected?').replace(':count', checked.length),
        function(confirmed) {
            if (confirmed && action) {
                var f = document.createElement('form');
                f.method = 'POST'; f.action = action; f.style.display = 'none';
                var meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) { var inp = document.createElement('input'); inp.type = 'hidden'; inp.name = '_token'; inp.value = meta.content; f.appendChild(inp); }
                checked.forEach(function(cb) {
                    var inp = document.createElement('input');
                    inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
                    f.appendChild(inp);
                });
                document.body.appendChild(f); f.submit();
            }
        },
        'Delete',
        'btn-danger'
    );
};

window.confirmBulkForceDelete = function() {
    const modalEl = document.getElementById('confirmModal');
    const checked = document.querySelectorAll('.select-item:checked');
    if (checked.length === 0) return;
    const formEl = document.getElementById('bulkForm');
    var action = formEl ? formEl.getAttribute('data-bulk-force-delete-route') : '';
    showConfirmModal(
        modalEl?.getAttribute('data-confirm-text') || 'Confirm',
        (modalEl?.getAttribute('data-bulk-force-delete-text') || 'Permanently delete selected?').replace(':count', checked.length),
        function(confirmed) {
            if (confirmed && action) {
                var f = document.createElement('form');
                f.method = 'POST'; f.action = action; f.style.display = 'none';
                var meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) { var inp = document.createElement('input'); inp.type = 'hidden'; inp.name = '_token'; inp.value = meta.content; f.appendChild(inp); }
                checked.forEach(function(cb) {
                    var inp = document.createElement('input');
                    inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
                    f.appendChild(inp);
                });
                document.body.appendChild(f); f.submit();
            }
        },
        document.getElementById('confirmModalAction')?.getAttribute('data-force-delete-text') || 'Force Delete',
        'btn-danger'
    );
};

// ===== Livewire Navigation: Tooltips only =====
document.addEventListener('livewire:navigated', function() {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        try { new bootstrap.Tooltip(el); } catch(e) {}
    });
});

// ===== Unhandled Rejection Handler =====
window.addEventListener('unhandledrejection', (event) => {
    if (event.reason instanceof TypeError || event.reason instanceof DOMException) {
        console.warn('Unhandled promise rejection:', event.reason);
    }
});

// ===== FAQ Accordion (event delegation — no Alpine dependency) =====
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.faq-question');
    if (btn) {
        btn.parentElement.classList.toggle('faq-open');
    }
});
