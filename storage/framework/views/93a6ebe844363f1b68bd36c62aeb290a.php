<?php if (! $__env->hasRenderedOnce('5e4db4b5-e734-47c4-bc2d-781509ba7365')): $__env->markAsRenderedOnce('5e4db4b5-e734-47c4-bc2d-781509ba7365'); ?>
<div id="pageLoaderOverlay" class="page-loader-overlay">
    <div class="page-loader-spinner-wrap">
        <div class="page-loader-spinner"></div>
        <span class="page-loader-text"><?php echo e(__('general.loading') ?? 'Loading...'); ?></span>
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
    var overlay = document.getElementById('pageLoaderOverlay');
    var bar = document.getElementById('pageLoaderBar');
    var progress = bar.querySelector('.page-loader-progress');
    var timer = null;
    var hideTimer = null;

    function show() {
        overlay.classList.add('active');
        overlay.classList.remove('done');
        bar.classList.add('active');
        bar.classList.remove('done');
        progress.style.width = '0%';
        clearTimeout(timer);
        clearTimeout(hideTimer);
    }

    function hide() {
        bar.classList.add('done');
        bar.classList.remove('active');
        overlay.classList.add('done');
        overlay.classList.remove('active');

        hideTimer = setTimeout(function() {
            bar.classList.remove('done');
            overlay.classList.remove('done');
            progress.style.width = '0%';
        }, 400);
    }

    document.addEventListener('livewire:navigate', show);
    document.addEventListener('livewire:navigated', hide);
    window.addEventListener('beforeunload', show);
    window.addEventListener('load', hide);
})();
</script>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\components\page-loader.blade.php ENDPATH**/ ?>