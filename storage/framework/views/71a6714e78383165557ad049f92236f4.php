<?php if (isset($component)) { $__componentOriginalaa758e6a82983efcbf593f765e026bd9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa758e6a82983efcbf593f765e026bd9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => $__env->getContainer()->make(Illuminate\View\Factory::class)->make('mail::message'),'data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mail::message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
# <?php echo new \Illuminate\Support\EncodedHtmlString(__('emails.payment_receipt_subject')); ?>


<?php echo new \Illuminate\Support\EncodedHtmlString(__('emails.hello')); ?>


<?php echo new \Illuminate\Support\EncodedHtmlString(__('emails.payment_receipt_line')); ?>


<?php if (isset($component)) { $__componentOriginal91214b38020aa1d764d4a21e693f703c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91214b38020aa1d764d4a21e693f703c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => $__env->getContainer()->make(Illuminate\View\Factory::class)->make('mail::panel'),'data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mail::panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
- **<?php echo new \Illuminate\Support\EncodedHtmlString(__('emails.amount')); ?>:** <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($payment->amount, 2)); ?> <?php echo new \Illuminate\Support\EncodedHtmlString($payment->currency); ?>

- **<?php echo new \Illuminate\Support\EncodedHtmlString(__('emails.method')); ?>:** <?php echo new \Illuminate\Support\EncodedHtmlString(__('onboarding.method_' . $payment->method)); ?>

- **<?php echo new \Illuminate\Support\EncodedHtmlString(__('emails.reference')); ?>:** <?php echo new \Illuminate\Support\EncodedHtmlString($payment->reference ?? __('emails.na')); ?>

- **<?php echo new \Illuminate\Support\EncodedHtmlString(__('super-admin.payment_id')); ?>:** <?php echo new \Illuminate\Support\EncodedHtmlString($payment->uuid ?? __('emails.na')); ?>

- **<?php echo new \Illuminate\Support\EncodedHtmlString(__('emails.date')); ?>:** <?php echo new \Illuminate\Support\EncodedHtmlString($payment->paid_at?->format('Y-m-d H:i') ?? $payment->created_at->format('Y-m-d H:i')); ?>

- **<?php echo new \Illuminate\Support\EncodedHtmlString(__('emails.status')); ?>:** <?php echo new \Illuminate\Support\EncodedHtmlString($payment->status->label()); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91214b38020aa1d764d4a21e693f703c)): ?>
<?php $attributes = $__attributesOriginal91214b38020aa1d764d4a21e693f703c; ?>
<?php unset($__attributesOriginal91214b38020aa1d764d4a21e693f703c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91214b38020aa1d764d4a21e693f703c)): ?>
<?php $component = $__componentOriginal91214b38020aa1d764d4a21e693f703c; ?>
<?php unset($__componentOriginal91214b38020aa1d764d4a21e693f703c); ?>
<?php endif; ?>

<?php echo new \Illuminate\Support\EncodedHtmlString(__('emails.payment_receipt_line')); ?>


<?php echo new \Illuminate\Support\EncodedHtmlString(__('emails.regards')); ?>

<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa758e6a82983efcbf593f765e026bd9)): ?>
<?php $attributes = $__attributesOriginalaa758e6a82983efcbf593f765e026bd9; ?>
<?php unset($__attributesOriginalaa758e6a82983efcbf593f765e026bd9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa758e6a82983efcbf593f765e026bd9)): ?>
<?php $component = $__componentOriginalaa758e6a82983efcbf593f765e026bd9; ?>
<?php unset($__componentOriginalaa758e6a82983efcbf593f765e026bd9); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/emails/payment-receipt.blade.php ENDPATH**/ ?>