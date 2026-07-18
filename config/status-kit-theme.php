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
        'danger' => 'text-bg-danger',
        'info' => 'text-bg-info',
        'gray' => 'text-bg-secondary',
    ],

    // ==========================================================
    // الكلاسات الأساسية لعنصر البادج (badge()) حسب الفريموورك
    // ==========================================================
    'badge_base' => [
        'bootstrap' => 'badge d-inline-flex align-items-center gap-1',
        'tailwind' => 'status-badge inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium',
    ],

    // ==========================================================
    // كلاسات <x-status-select> حسب الفريموورك
    // ==========================================================
    'select_classes' => [
        'bootstrap' => [
            'trigger'     => 'form-select form-select-sm text-start d-inline-flex align-items-center justify-content-between',
            'trigger_sm'  => 'form-select form-select-sm text-start d-inline-flex align-items-center justify-content-between form-select-sm',
            'trigger_lg'  => 'form-select text-start d-inline-flex align-items-center justify-content-between',
            'menu'        => 'list-unstyled',
            'option'      => 'rounded-2 px-2 py-1',
            'input'       => 'form-control form-control-sm',
            'check_icon'  => 'bi bi-check-lg ms-auto text-success',
            'gap_small'   => 'gap-1',
            'overflow'    => 'overflow-hidden',
            'text_truncate' => 'text-truncate',
            'text_muted'  => 'text-muted',
            'small'       => 'small',
            'ms_2'        => 'ms-2',
            'flex_grow'   => 'flex-grow-1',
            'p_1_pb_2'    => 'p-1 pb-2',
            'px_2_py_1'   => 'px-2 py-1',
        ],
        'tailwind' => [
            'trigger'     => 'w-full text-start text-sm border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5',
            'trigger_sm'  => 'w-full text-start text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-1',
            'trigger_lg'  => 'w-full text-start text-base border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-2',
            'menu'        => 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg',
            'option'      => 'rounded px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer',
            'input'       => 'w-full text-sm border border-gray-200 dark:border-gray-700 rounded px-2 py-1',
            'check_icon'  => 'bi bi-check-lg ms-auto text-green-500',
            'gap_small'   => 'gap-1',
            'overflow'    => 'overflow-hidden',
            'text_truncate' => 'truncate',
            'text_muted'  => 'text-gray-400',
            'small'       => 'text-xs',
            'ms_2'        => 'ms-2',
            'flex_grow'   => 'flex-1',
            'p_1_pb_2'    => 'p-1 pb-2',
            'px_2_py_1'   => 'px-2 py-1',
        ],
    ],

];
