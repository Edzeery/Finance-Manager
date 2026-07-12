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
