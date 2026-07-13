<?php

return [
    'payment' => [
        'paid' => 'مدفوع', 'pending' => 'قيد الانتظار', 'failed' => 'فشلت العملية',
        'refunded' => 'مسترجع', 'canceled' => 'ملغى',
    ],
    'subscription' => [
        'active' => 'نشط', 'pending' => 'قيد الانتظار', 'expired' => 'منتهي',
        'canceled' => 'ملغى', 'suspended' => 'موقوف',
    ],
    'user' => [
        'active' => 'نشط', 'pending' => 'قيد الانتظار', 'suspended' => 'موقوف',
        'banned' => 'محظور', 'email_unverified' => 'بريد غير مفعّل',
    ],
    'stores' => [
        'active' => 'نشط', 'pending' => 'قيد المراجعة', 'suspended' => 'موقوف',
        'closed' => 'مغلق', 'draft' => 'مسودة', 'blocked' => 'محظور',
        'approved' => 'معتمد', 'rejected' => 'مرفوض',
    ],
    'order' => [
        'pending' => 'قيد الانتظار', 'processing' => 'قيد المعالجة', 'completed' => 'مكتمل',
        'canceled' => 'ملغى', 'refunded' => 'مسترجع',
    ],
    'product' => [
        'active' => 'متوفر', 'inactive' => 'غير مفعّل', 'out_of_stock' => 'نفدت الكمية',
        'discontinued' => 'متوقف',
    ],
    'role' => [
        'super_admin' => 'مشرف عام', 'admin' => 'مشرف', 'support_agent' => 'وكيل دعم',
        'tech_support' => 'دعم تقني', 'merchant' => 'تاجر', 'user' => 'مستخدم',
        'owner' => 'مالك', 'manager' => 'مدير', 'staff' => 'موظف',
    ],
    'general' => [
        'info' => 'معلومة', 'success' => 'نجاح', 'warning' => 'تحذير',
        'danger' => 'خطر', 'gray' => 'غير محدد',
    ],
];
