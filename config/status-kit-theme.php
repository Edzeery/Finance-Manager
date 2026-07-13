<?php

/**
 * إعدادات "الفريموورك" المسؤول عن شكل الألوان/البادج.
 *
 * الحزمة تدعم فريمووركين:
 * - bootstrap : يستعمل كلاسات Bootstrap 5.1+ الجاهزة (text-bg-*) المبنية على variant
 * - tailwind  : يستعمل كلاسات light/dark الموجودة يدويًا داخل كل حالة في config/statuses.php
 *
 * غيّر 'default_framework' حسب مشروعك، أو مرّر framework صراحة عند الاستدعاء:
 *   Status::for('payment','paid')->color(framework: 'tailwind')
 */

return [

    'default_framework' => 'bootstrap', // bootstrap | tailwind

    // ==========================================================
    // خريطة variant → كلاس Bootstrap 5 (badge/text/bg موحّد بكلاس واحد)
    // متوفرة افتراضيًا من Bootstrap 5.1+ بلا أي CSS إضافي.
    // ==========================================================
    'bootstrap_variants' => [
        'success' => 'text-bg-success',
        'warning' => 'text-bg-warning',
        'danger'  => 'text-bg-danger',
        'info'    => 'text-bg-info',
        'gray'    => 'text-bg-secondary',
    ],

    // ==========================================================
    // الكلاسات الأساسية لعنصر البادج (badge()) حسب الفريموورك
    // ==========================================================
    'badge_base' => [
        'bootstrap' => 'badge d-inline-flex align-items-center gap-1',
        'tailwind'  => 'status-badge inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium',
    ],

];
