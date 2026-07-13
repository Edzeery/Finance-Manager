<?php

return [
    'payment' => [
        'paid' => 'Paid', 'pending' => 'Pending', 'failed' => 'Failed',
        'refunded' => 'Refunded', 'canceled' => 'Canceled',
    ],
    'subscription' => [
        'active' => 'Active', 'pending' => 'Pending', 'expired' => 'Expired',
        'canceled' => 'Canceled', 'suspended' => 'Suspended',
    ],
    'user' => [
        'active' => 'Active', 'pending' => 'Pending', 'suspended' => 'Suspended',
        'banned' => 'Banned', 'email_unverified' => 'Email Unverified',
    ],
    'stores' => [
        'active' => 'Active', 'pending' => 'Under Review', 'suspended' => 'Suspended',
        'closed' => 'Closed', 'draft' => 'Draft', 'blocked' => 'Blocked',
        'approved' => 'Approved', 'rejected' => 'Rejected',
    ],
    'order' => [
        'pending' => 'Pending', 'processing' => 'Processing', 'completed' => 'Completed',
        'canceled' => 'Canceled', 'refunded' => 'Refunded',
    ],
    'product' => [
        'active' => 'Available', 'inactive' => 'Inactive', 'out_of_stock' => 'Out of Stock',
        'discontinued' => 'Discontinued',
    ],
    'role' => [
        'super_admin' => 'Super Admin', 'admin' => 'Admin', 'support_agent' => 'Support Agent',
        'tech_support' => 'Tech Support', 'merchant' => 'Merchant', 'user' => 'User',
        'owner' => 'Owner', 'manager' => 'Manager', 'staff' => 'Staff',
    ],
    'general' => [
        'info' => 'Info', 'success' => 'Success', 'warning' => 'Warning',
        'danger' => 'Danger', 'gray' => 'Unspecified',
    ],
];
