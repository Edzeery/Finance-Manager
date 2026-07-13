<?php


return [

    // ===== Subscriptions =====
    'subscription' => [
        'active'    => [
            'filament' => 'success',
            'light' => 'text-green-600 bg-green-100',
            'dark' => 'text-green-400 bg-green-950',
            'hex' => '#16a34a',
            'icon' => 'heroicon-o-check-circle'
        ],
        'pending'   => ['filament' => 'warning', 'light' => 'text-yellow-600 bg-yellow-100', 'dark' => 'text-yellow-400 bg-yellow-950', 'hex' => '#facc15', 'icon' => 'heroicon-o-clock'],
        // 'expired'   => ['filament' => Color::Gray, 'light' => 'text-gray-600 bg-gray-100', 'dark' => 'text-gray-400 bg-gray-900', 'hex' => '#9ca3af', 'icon' => 'heroicon-o-x-circle'],
        'canceled'  => ['filament' => 'danger', 'light' => 'text-red-600 bg-red-100', 'dark' => 'text-red-400 bg-red-950', 'hex' => '#dc2626', 'icon' => 'heroicon-o-x-mark'],
        'suspended' => ['filament' => 'danger', 'light' => 'text-red-600 bg-red-100', 'dark' => 'text-red-400 bg-red-950', 'hex' => '#dc2626', 'icon' => 'heroicon-o-exclamation-circle'],
    ],

    // ===== Payments =====
    'payment' => [
        'paid'     => ['filament' => 'success', 'light' => 'text-green-600 bg-green-100', 'dark' => 'text-green-400 bg-green-950', 'hex' => '#16a34a', 'icon' => 'heroicon-o-currency-dollar'],
        'pending'  => ['filament' => 'warning', 'light' => 'text-yellow-600 bg-yellow-100', 'dark' => 'text-yellow-400 bg-yellow-950', 'hex' => '#facc15', 'icon' => 'heroicon-o-clock'],
        'failed'   => ['filament' => 'danger', 'light' => 'text-red-600 bg-red-100', 'dark' => 'text-red-400 bg-red-950', 'hex' => '#dc2626', 'icon' => 'heroicon-o-x-circle'],
        'refunded' => ['filament' => 'info', 'light' => 'text-blue-600 bg-blue-100', 'dark' => 'text-blue-400 bg-blue-950', 'hex' => '#2563eb', 'icon' => 'heroicon-o-arrow-path'],
        // 'canceled' => ['filament' => Color::Gray, 'light' => 'text-gray-600 bg-gray-100', 'dark' => 'text-gray-400 bg-gray-900', 'hex' => '#9ca3af', 'icon' => 'heroicon-o-x-mark'],
    ],

    // ===== Users =====
    'user' => [
        'active'            => ['filament' => 'success', 'light' => 'text-green-600 bg-green-100', 'dark' => 'text-green-400 bg-green-950', 'hex' => '#16a34a', 'icon' => 'heroicon-o-check-circle'],
        'pending'           => ['filament' => 'warning', 'light' => 'text-yellow-600 bg-yellow-100', 'dark' => 'text-yellow-400 bg-yellow-950', 'hex' => '#facc15', 'icon' => 'heroicon-o-clock'],
        'suspended'         => ['filament' => 'danger', 'light' => 'text-red-600 bg-red-100', 'dark' => 'text-red-400 bg-red-950', 'hex' => '#dc2626', 'icon' => 'heroicon-o-exclamation-circle'],
        'banned'            => ['filament' => 'danger', 'light' => 'text-red-700 bg-red-200', 'dark' => 'text-red-500 bg-red-950', 'hex' => '#b91c1c', 'icon' => 'heroicon-o-x-circle'],
        // 'email_unverified'  => ['filament' => Color::Gray, 'light' => 'text-gray-600 bg-gray-100', 'dark' => 'text-gray-400 bg-gray-900', 'hex' => '#9ca3af', 'icon' => 'heroicon-o-envelope'],
    ],

    // ===== Stores =====
    'stores' => [
        'active'    => ['filament' => 'success', 'light' => 'text-green-600 bg-green-100', 'dark' => 'text-green-400 bg-green-950', 'hex' => '#16a34a', 'icon' => 'heroicon-o-check-circle'],
        'pending'   => ['filament' => 'warning', 'light' => 'text-yellow-600 bg-yellow-100', 'dark' => 'text-yellow-400 bg-yellow-950', 'hex' => '#facc15', 'icon' => 'heroicon-o-clock'],
        'suspended' => ['filament' => 'danger', 'light' => 'text-red-600 bg-red-100', 'dark' => 'text-red-400 bg-red-950', 'hex' => '#dc2626', 'icon' => 'heroicon-o-exclamation-circle'],
        // 'closed'    => ['filament' => Color::Gray, 'light' => 'text-gray-600 bg-gray-100', 'dark' => 'text-gray-400 bg-gray-900', 'hex' => '#9ca3af', 'icon' => 'heroicon-o-lock-closed'],
        'draft'    => ['filament' => 'info', 'light' => 'text-info-600 bg-info-100', 'dark' => 'text-info-400 bg-info-900', 'hex' => '#2563eb', 'icon' => 'heroicon-o-lock-open'],
        'blocked'    => ['filament' => 'danger', 'light' => 'text-red-600 bg-red-100', 'dark' => 'text-red-400 bg-red-900', 'hex' => '#dc2626', 'icon' => 'heroicon-o-x-mark'],
        'approved'    => ['filament' => 'success', 'light' => 'text-green-600 bg-green-100', 'dark' => 'text-green-400 bg-green-900', 'hex' => '#16a34a', 'icon' => 'heroicon-o-check'],
        'rejected'    => ['filament' => 'danger', 'light' => 'text-red-600 bg-red-100', 'dark' => 'text-red-400 bg-red-900', 'hex' => '#dc2626', 'icon' => 'heroicon-o-x-circle'],
    ],

    // ===== Orders =====
    'order' => [
        'pending'     => ['filament' => 'warning', 'light' => 'text-yellow-600 bg-yellow-100', 'dark' => 'text-yellow-400 bg-yellow-950', 'hex' => '#facc15', 'icon' => 'heroicon-o-clock'],
        'processing'  => ['filament' => 'info', 'light' => 'text-blue-600 bg-blue-100', 'dark' => 'text-blue-400 bg-blue-950', 'hex' => '#2563eb', 'icon' => 'heroicon-o-cog'],
        'completed'   => ['filament' => 'success', 'light' => 'text-green-600 bg-green-100', 'dark' => 'text-green-400 bg-green-950', 'hex' => '#16a34a', 'icon' => 'heroicon-o-check-circle'],
        'canceled'    => ['filament' => 'danger', 'light' => 'text-red-600 bg-red-100', 'dark' => 'text-red-400 bg-red-950', 'hex' => '#dc2626', 'icon' => 'heroicon-o-x-mark'],
        'refunded'    => ['filament' => 'info', 'light' => 'text-blue-600 bg-blue-100', 'dark' => 'text-blue-400 bg-blue-950', 'hex' => '#2563eb', 'icon' => 'heroicon-o-arrow-path'],
    ],

    // ===== Products =====
    'product' => [
        'active'        => ['filament' => 'success', 'light' => 'text-green-600 bg-green-100', 'dark' => 'text-green-400 bg-green-950', 'hex' => '#16a34a', 'icon' => 'heroicon-o-check-circle'],
        // 'inactive'      => ['filament' => Color::Gray, 'light' => 'text-gray-600 bg-gray-100', 'dark' => 'text-gray-400 bg-gray-900', 'hex' => '#9ca3af', 'icon' => 'heroicon-o-x-circle'],
        'out_of_stock'  => ['filament' => 'warning', 'light' => 'text-yellow-600 bg-yellow-100', 'dark' => 'text-yellow-400 bg-yellow-950', 'hex' => '#facc15', 'icon' => 'heroicon-o-exclamation-triangle'],
        'discontinued'  => ['filament' => 'danger', 'light' => 'text-red-600 bg-red-100', 'dark' => 'text-red-400 bg-red-950', 'hex' => '#dc2626', 'icon' => 'heroicon-o-x-mark'],
    ],

    // ===== General / Others =====
    'general' => [
        'info'    => ['filament' => 'info', 'light' => 'text-blue-600 bg-blue-100', 'dark' => 'text-blue-400 bg-blue-950', 'hex' => '#2563eb', 'icon' => 'heroicon-o-information-circle'],
        'success' => ['filament' => 'success', 'light' => 'text-green-600 bg-green-100', 'dark' => 'text-green-400 bg-green-950', 'hex' => '#16a34a', 'icon' => 'heroicon-o-check-circle'],
        'warning' => ['filament' => 'warning', 'light' => 'text-yellow-600 bg-yellow-100', 'dark' => 'text-yellow-400 bg-yellow-950', 'hex' => '#facc15', 'icon' => 'heroicon-o-exclamation-triangle'],
        'danger'  => ['filament' => 'danger', 'light' => 'text-red-600 bg-red-100', 'dark' => 'text-red-400 bg-red-950', 'hex' => '#dc2626', 'icon' => 'heroicon-o-x-circle'],
        // 'gray'    => ['filament' => Color::Gray, 'light' => 'text-gray-600 bg-gray-100', 'dark' => 'text-gray-400 bg-gray-900', 'hex' => '#9ca3af', 'icon' => 'heroicon-o-minus-circle'],
    ],


];
