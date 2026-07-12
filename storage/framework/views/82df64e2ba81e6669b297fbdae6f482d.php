<script>
if (!window._sidebarEscapeListener) {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
            closeSidebarMobile();
        }
    });
    window._sidebarEscapeListener = true;
}
</script>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\layouts\partials\_alpine-components.blade.php ENDPATH**/ ?>