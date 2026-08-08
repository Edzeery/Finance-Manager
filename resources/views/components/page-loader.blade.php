@once
<div id="pageLoaderOverlay" class="page-loader-overlay">
    <div class="page-loader-spinner-wrap">
        <div class="page-loader-spinner"></div>
        <span class="page-loader-text">{{ __('general.loading') ?? 'Loading...' }}</span>
    </div>
</div>
<div id="pageLoaderBar" class="page-loader-bar">
    <div class="page-loader-progress"></div>
</div>

<style>
.page-loader-bar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    z-index: 10001;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.15s ease;
}
.page-loader-bar.active {
    opacity: 1;
}
.page-loader-progress {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, var(--accent), #34d399);
    border-radius: 0 2px 2px 0;
    transition: width 0.3s ease;
}
.page-loader-bar.active .page-loader-progress {
    animation: pageLoaderIndeterminate 1.8s ease-in-out infinite;
}
.page-loader-bar.done .page-loader-progress {
    width: 100% !important;
    transition: width 0.2s ease;
}

.page-loader-overlay {
    position: fixed;
    inset: 0;
    z-index: 10000;
    background: var(--bg, #F8FAFC);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.page-loader-overlay.active {
    opacity: 1;
    pointer-events: all;
}
.page-loader-overlay.done {
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease 0.1s;
}
.page-loader-spinner-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}
.page-loader-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid var(--border, #e2e8f0);
    border-top-color: var(--accent, #15B76C);
    border-radius: 50%;
    animation: pageLoaderSpin 0.8s linear infinite;
}
.page-loader-text {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-muted, #94A3B8);
    letter-spacing: 0.3px;
}

@keyframes pageLoaderSpin {
    to { transform: rotate(360deg); }
}
@keyframes pageLoaderIndeterminate {
    0% { width: 0%; margin-left: 0; }
    50% { width: 60%; margin-left: 20%; }
    100% { width: 0%; margin-left: 100%; }
}
</style>

<script>
(function() {
    if (window.__pageLoaderInitialized) {
        return;
    }
    window.__pageLoaderInitialized = true;

    var MIN_DISPLAY = 350;
    var SETTLE = 150;
    var showTime = 0;
    var hideTimer = null;

    function elements() {
        var overlay = document.getElementById('pageLoaderOverlay');
        var bar = document.getElementById('pageLoaderBar');
        var progress = bar ? bar.querySelector('.page-loader-progress') : null;
        return { overlay: overlay, bar: bar, progress: progress };
    }

    function show() {
        var el = elements();
        if (!el.overlay || !el.bar) {
            return;
        }
        el.overlay.classList.add('active');
        el.overlay.classList.remove('done');
        el.bar.classList.add('active');
        el.bar.classList.remove('done');
        if (el.progress) {
            el.progress.style.width = '0%';
        }
        clearTimeout(hideTimer);
        showTime = Date.now();
    }

    function hide() {
        var el = elements();
        if (!el.overlay || !el.bar) {
            return;
        }
        var remaining = MIN_DISPLAY - (Date.now() - showTime);
        if (remaining < 0) {
            remaining = 0;
        }
        clearTimeout(hideTimer);
        hideTimer = setTimeout(function() {
            el.bar.classList.add('done');
            el.bar.classList.remove('active');
            el.overlay.classList.add('done');
            el.overlay.classList.remove('active');

            setTimeout(function() {
                if (el.bar) {
                    el.bar.classList.remove('done');
                }
                if (el.overlay) {
                    el.overlay.classList.remove('done');
                }
                if (el.progress) {
                    el.progress.style.width = '0%';
                }
            }, 400);
        }, remaining + SETTLE);
    }

    document.addEventListener('livewire:navigate', show);
    document.addEventListener('livewire:navigated', hide);
    window.addEventListener('beforeunload', show);
    window.addEventListener('load', hide);
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            hide();
        }
    });
})();
</script>
@endonce
