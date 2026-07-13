<?php

/**
 * ملف موحّد لكل حالات النظام (ألوان + hex + أيقونة عامة + variant).
 *
 * كل حالة تحتوي:
 * - variant : اسم عام محايد (success|warning|danger|info|gray) قابل للربط لاحقًا بأي مكتبة UI
 * - light   : كلاسات Tailwind للوضع الفاتح
 * - dark    : كلاسات Tailwind للوضع الداكن (بادئة dark: على كل الكلاسات)
 * - hex     : اللون الأساسي كـ hex (مفيد للرسوم البيانية / SVG / Inline style)
 * - icon    : مفتاح عام يُترجم عبر config/icons.php حسب المجموعة (fa/bi/ion/heroicon) وقت العرض
 *
 * التسميات النصية (Labels) موجودة في lang/{ar,en,fr}/statuses.php
 * ويُستدعى المفتاح بصيغة: "{domain}.{status}" مثل: payment.paid
 */

return [

    'payment' => [
        'paid'      => ['variant' => 'success', 'light' => 'text-green-700 bg-green-100',  'dark' => 'dark:text-green-300 dark:bg-green-900/40',  'hex' => '#16a34a', 'icon' => 'paid'],
        'pending'   => ['variant' => 'warning', 'light' => 'text-yellow-700 bg-yellow-100', 'dark' => 'dark:text-yellow-300 dark:bg-yellow-900/40', 'hex' => '#facc15', 'icon' => 'pending'],
        'failed'    => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',       'dark' => 'dark:text-red-300 dark:bg-red-900/40',      'hex' => '#dc2626', 'icon' => 'failed'],
        'refunded'  => ['variant' => 'info',    'light' => 'text-blue-700 bg-blue-100',     'dark' => 'dark:text-blue-300 dark:bg-blue-900/40',    'hex' => '#2563eb', 'icon' => 'refunded'],
        'canceled'  => ['variant' => 'gray',    'light' => 'text-gray-700 bg-gray-100',     'dark' => 'dark:text-gray-300 dark:bg-gray-800',       'hex' => '#9ca3af', 'icon' => 'canceled'],
    ],

    'subscription' => [
        'active'    => ['variant' => 'success', 'light' => 'text-green-700 bg-green-100',  'dark' => 'dark:text-green-300 dark:bg-green-900/40',  'hex' => '#16a34a', 'icon' => 'active'],
        'pending'   => ['variant' => 'warning', 'light' => 'text-yellow-700 bg-yellow-100', 'dark' => 'dark:text-yellow-300 dark:bg-yellow-900/40', 'hex' => '#facc15', 'icon' => 'pending'],
        'expired'   => ['variant' => 'gray',    'light' => 'text-gray-700 bg-gray-100',     'dark' => 'dark:text-gray-300 dark:bg-gray-800',       'hex' => '#9ca3af', 'icon' => 'expired'],
        'canceled'  => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',       'dark' => 'dark:text-red-300 dark:bg-red-900/40',      'hex' => '#dc2626', 'icon' => 'canceled'],
        'suspended' => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',       'dark' => 'dark:text-red-300 dark:bg-red-900/40',      'hex' => '#dc2626', 'icon' => 'suspended'],
    ],

    'user' => [
        'active'           => ['variant' => 'success', 'light' => 'text-green-700 bg-green-100',  'dark' => 'dark:text-green-300 dark:bg-green-900/40',  'hex' => '#16a34a', 'icon' => 'active'],
        'pending'          => ['variant' => 'warning', 'light' => 'text-yellow-700 bg-yellow-100', 'dark' => 'dark:text-yellow-300 dark:bg-yellow-900/40', 'hex' => '#facc15', 'icon' => 'pending'],
        'suspended'        => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',       'dark' => 'dark:text-red-300 dark:bg-red-900/40',      'hex' => '#dc2626', 'icon' => 'suspended'],
        'banned'           => ['variant' => 'danger',  'light' => 'text-red-800 bg-red-200',       'dark' => 'dark:text-red-400 dark:bg-red-950',         'hex' => '#b91c1c', 'icon' => 'banned'],
        'email_unverified' => ['variant' => 'gray',    'light' => 'text-gray-700 bg-gray-100',     'dark' => 'dark:text-gray-300 dark:bg-gray-800',       'hex' => '#9ca3af', 'icon' => 'email_unverified'],
    ],

    'stores' => [
        'active'    => ['variant' => 'success', 'light' => 'text-green-700 bg-green-100',  'dark' => 'dark:text-green-300 dark:bg-green-900/40',  'hex' => '#16a34a', 'icon' => 'active'],
        'pending'   => ['variant' => 'warning', 'light' => 'text-yellow-700 bg-yellow-100', 'dark' => 'dark:text-yellow-300 dark:bg-yellow-900/40', 'hex' => '#facc15', 'icon' => 'pending'],
        'suspended' => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',       'dark' => 'dark:text-red-300 dark:bg-red-900/40',      'hex' => '#dc2626', 'icon' => 'suspended'],
        'closed'    => ['variant' => 'gray',    'light' => 'text-gray-700 bg-gray-100',     'dark' => 'dark:text-gray-300 dark:bg-gray-800',       'hex' => '#9ca3af', 'icon' => 'closed'],
        'draft'     => ['variant' => 'info',    'light' => 'text-blue-700 bg-blue-100',     'dark' => 'dark:text-blue-300 dark:bg-blue-900/40',    'hex' => '#2563eb', 'icon' => 'draft'],
        'blocked'   => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',       'dark' => 'dark:text-red-300 dark:bg-red-900/40',      'hex' => '#dc2626', 'icon' => 'blocked'],
        'approved'  => ['variant' => 'success', 'light' => 'text-green-700 bg-green-100',  'dark' => 'dark:text-green-300 dark:bg-green-900/40',  'hex' => '#16a34a', 'icon' => 'approved'],
        'rejected'  => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',       'dark' => 'dark:text-red-300 dark:bg-red-900/40',      'hex' => '#dc2626', 'icon' => 'rejected'],
    ],

    'order' => [
        'pending'    => ['variant' => 'warning', 'light' => 'text-yellow-700 bg-yellow-100', 'dark' => 'dark:text-yellow-300 dark:bg-yellow-900/40', 'hex' => '#facc15', 'icon' => 'pending'],
        'processing' => ['variant' => 'info',    'light' => 'text-blue-700 bg-blue-100',     'dark' => 'dark:text-blue-300 dark:bg-blue-900/40',    'hex' => '#2563eb', 'icon' => 'processing'],
        'completed'  => ['variant' => 'success', 'light' => 'text-green-700 bg-green-100',  'dark' => 'dark:text-green-300 dark:bg-green-900/40',  'hex' => '#16a34a', 'icon' => 'completed'],
        'canceled'   => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',       'dark' => 'dark:text-red-300 dark:bg-red-900/40',      'hex' => '#dc2626', 'icon' => 'canceled'],
        'refunded'   => ['variant' => 'info',    'light' => 'text-blue-700 bg-blue-100',     'dark' => 'dark:text-blue-300 dark:bg-blue-900/40',    'hex' => '#2563eb', 'icon' => 'refunded'],
    ],

    'product' => [
        'active'       => ['variant' => 'success', 'light' => 'text-green-700 bg-green-100',  'dark' => 'dark:text-green-300 dark:bg-green-900/40',  'hex' => '#16a34a', 'icon' => 'active'],
        'inactive'     => ['variant' => 'gray',    'light' => 'text-gray-700 bg-gray-100',     'dark' => 'dark:text-gray-300 dark:bg-gray-800',       'hex' => '#9ca3af', 'icon' => 'inactive'],
        'out_of_stock' => ['variant' => 'warning', 'light' => 'text-yellow-700 bg-yellow-100', 'dark' => 'dark:text-yellow-300 dark:bg-yellow-900/40', 'hex' => '#facc15', 'icon' => 'out_of_stock'],
        'discontinued' => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',       'dark' => 'dark:text-red-300 dark:bg-red-900/40',      'hex' => '#dc2626', 'icon' => 'discontinued'],
    ],

    // نطاق الأدوار (roles.php سابقًا) — أصبح نطاقًا ضمن نفس البنية الموحدة
    'role' => [
        'super_admin'   => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',       'dark' => 'dark:text-red-400 dark:bg-red-950',      'hex' => '#991b1b', 'icon' => 'shield-exclamation'],
        'admin'         => ['variant' => 'warning', 'light' => 'text-yellow-700 bg-yellow-100', 'dark' => 'dark:text-yellow-400 dark:bg-yellow-950', 'hex' => '#f59e0b', 'icon' => 'shield-check'],
        'support_agent' => ['variant' => 'info',    'light' => 'text-blue-700 bg-blue-100',     'dark' => 'dark:text-blue-400 dark:bg-blue-950',     'hex' => '#2563eb', 'icon' => 'chat-bubble'],
        'tech_support'  => ['variant' => 'gray',    'light' => 'text-gray-600 bg-gray-100',     'dark' => 'dark:text-gray-400 dark:bg-gray-900',     'hex' => '#6b7280', 'icon' => 'wrench-screwdriver'],
        'merchant'      => ['variant' => 'success', 'light' => 'text-green-700 bg-green-100',   'dark' => 'dark:text-green-400 dark:bg-green-950',   'hex' => '#16a34a', 'icon' => 'building-storefront'],
        'user'          => ['variant' => 'info',    'light' => 'text-blue-600 bg-blue-100',     'dark' => 'dark:text-blue-400 dark:bg-blue-950',     'hex' => '#3b82f6', 'icon' => 'user-circle'],
        'owner'         => ['variant' => 'success', 'light' => 'text-green-700 bg-green-100',   'dark' => 'dark:text-green-400 dark:bg-green-950',   'hex' => '#16a34a', 'icon' => 'shield-check'],
        'manager'       => ['variant' => 'warning', 'light' => 'text-yellow-700 bg-yellow-100', 'dark' => 'dark:text-yellow-400 dark:bg-yellow-950', 'hex' => '#ca8a04', 'icon' => 'clipboard-check'],
        'staff'         => ['variant' => 'gray',    'light' => 'text-gray-600 bg-gray-100',     'dark' => 'dark:text-gray-400 dark:bg-gray-900',     'hex' => '#9ca3af', 'icon' => 'user'],
    ],

    'general' => [
        'info'    => ['variant' => 'info',    'light' => 'text-blue-700 bg-blue-100',   'dark' => 'dark:text-blue-300 dark:bg-blue-900/40',   'hex' => '#2563eb', 'icon' => 'info'],
        'success' => ['variant' => 'success', 'light' => 'text-green-700 bg-green-100', 'dark' => 'dark:text-green-300 dark:bg-green-900/40', 'hex' => '#16a34a', 'icon' => 'success'],
        'warning' => ['variant' => 'warning', 'light' => 'text-yellow-700 bg-yellow-100','dark' => 'dark:text-yellow-300 dark:bg-yellow-900/40','hex' => '#facc15', 'icon' => 'warning'],
        'danger'  => ['variant' => 'danger',  'light' => 'text-red-700 bg-red-100',     'dark' => 'dark:text-red-300 dark:bg-red-900/40',     'hex' => '#dc2626', 'icon' => 'failed'],
        'gray'    => ['variant' => 'gray',    'light' => 'text-gray-700 bg-gray-100',   'dark' => 'dark:text-gray-300 dark:bg-gray-800',      'hex' => '#9ca3af', 'icon' => 'default'],
    ],

    

];
