<div id="toast-container" class="toast-container" x-data="toastManager()">
    <?php echo e($slot); ?>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
function toastManager() {
    return {
        toasts: [],
        soundEnabled: true,
        init() {
            window.Toast = {
                success: (message, title = null) => this.add('success', message, title),
                error: (message, title = null) => this.add('error', message, title),
                warning: (message, title = null) => this.add('warning', message, title),
                info: (message, title = null) => this.add('info', message, title),
            };
            this.loadFromSession();
        },
        loadFromSession() {
            <?php if(session('success')): ?>
            this.add('success', <?php echo json_encode(session('success'), 15, 512) ?>, <?php echo json_encode(__('general.success'), 15, 512) ?>);
            <?php endif; ?>
            <?php if(session('error')): ?>
            this.add('error', <?php echo json_encode(session('error'), 15, 512) ?>, <?php echo json_encode(__('general.error'), 15, 512) ?>);
            <?php endif; ?>
            <?php if(session('warning')): ?>
            this.add('warning', <?php echo json_encode(session('warning'), 15, 512) ?>, <?php echo json_encode(__('general.warning'), 15, 512) ?>);
            <?php endif; ?>
            <?php if(session('info')): ?>
            this.add('info', <?php echo json_encode(session('info'), 15, 512) ?>, <?php echo json_encode(__('general.info'), 15, 512) ?>);
            <?php endif; ?>
        },
        add(type, message, title = null) {
            const id = Date.now() + Math.random();
            const toast = { id, type, message, title, progress: 100 };
            this.toasts.push(toast);

            // تشغيل صوت إذا كان مفعلاً
            this.playSound(type);

            this.$nextTick(() => {
                this.animateProgress(id);
            });
        },
        playSound(type) {
            // يمكن إضافة أصوات فريدة لكل نوع
            const soundMap = {
                success: 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAAB9AAACABAAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj==',
                error: 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAAB9AAACABAAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBg==',
                warning: 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAAB9AAACABAAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBg==',
                info: 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAAB9AAACABAAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBg==',
            };

            if (this.soundEnabled && soundMap[type]) {
                try {
                    const audio = new Audio(soundMap[type]);
                    audio.volume = 0.3;
                    audio.play().catch(() => {});
                } catch (e) {}
            }
        },
        animateProgress(id) {
            const duration = 5000;
            const start = Date.now();
            const animate = () => {
                const toast = this.toasts.find(t => t.id === id);
                if (!toast) return;
                const elapsed = Date.now() - start;
                toast.progress = Math.max(0, 100 - (elapsed / duration) * 100);
                if (toast.progress > 0) {
                    requestAnimationFrame(animate);
                } else {
                    this.remove(id);
                }
            };
            requestAnimationFrame(animate);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },
        getIcon(type) {
            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-x-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill',
            };
            return icons[type] || 'bi-info-circle-fill';
        },
        getColor(type) {
            const colors = {
                success: 'var(--success)',
                error: 'var(--danger)',
                warning: 'var(--warning)',
                info: 'var(--info)',
            };
            return colors[type] || 'var(--info)';
        },
        getBg(type) {
            const bgs = {
                success: 'rgba(34,197,94,0.12)',
                error: 'rgba(239,68,68,0.12)',
                warning: 'rgba(245,158,11,0.12)',
                info: 'rgba(59,130,246,0.12)',
            };
            return bgs[type] || 'rgba(59,130,246,0.12)';
        },
        getBorderColor(type) {
            const colors = {
                success: 'var(--success)',
                error: 'var(--danger)',
                warning: 'var(--warning)',
                info: 'var(--info)',
            };
            return colors[type] || 'var(--info)';
        },
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/toast/container.blade.php ENDPATH**/ ?>