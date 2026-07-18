<?php
    $languages = ['php', 'curl', 'python', 'javascript', 'ruby'];
    $langNames = [
        'php' => __('api-docs.php'),
        'curl' => __('api-docs.curl'),
        'python' => __('api-docs.python'),
        'javascript' => __('api-docs.javascript'),
        'ruby' => __('api-docs.ruby'),
    ];
?>

<div class="code-block">
    <div class="code-tabs" role="tablist">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($examples[$lang])): ?>
                <button type="button" class="code-tab <?php echo e($loop->first ? 'active' : ''); ?>" data-lang="<?php echo e($lang); ?>"><?php echo e($langNames[$lang]); ?></button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($examples[$lang])): ?>
            <div class="code-pre" data-lang="<?php echo e($lang); ?>" dir="ltr" style="<?php echo e($loop->first ? 'display:block' : 'display:none'); ?>">
                <div class="code-block-wrapper">
                    <button type="button" class="copy-btn"><?php echo e(__('api-docs.copy_code')); ?></button>
                    <pre><code><?php echo e($examples[$lang]); ?></code></pre>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($response)): ?>
        <details style="margin-top:0.5rem">
            <summary style="cursor:pointer;font-size:0.8125rem;color:var(--text-muted,#6b7280);padding:0.25rem 0">
                <?php echo e(__('api-docs.response_example')); ?>

            </summary>
            <div class="code-block-wrapper" style="margin-top:0.5rem" dir="ltr">
                <pre><code><?php echo e($response); ?></code></pre>
            </div>
        </details>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\api-docs\_code_block.blade.php ENDPATH**/ ?>