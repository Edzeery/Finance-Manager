<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class EnterpriseRolePermissionSeeder extends Seeder
{
    private function permissionIds(array $slugs): array
    {
        return Permission::whereIn('slug', $slugs)->pluck('id')->toArray();
    }

    public function run(): void
    {
        $this->createPlatformPermissions();
        $this->createWorkspacePermissions();
        $this->createPlatformRoles();
        $this->createWorkspaceRoles();
    }

    // ========================================================================
    //  PERMISSIONS
    // ========================================================================

    private function createPlatformPermissions(): void
    {
        $permissions = [
            // Tenant Management
            ['name' => 'View Tenants', 'slug' => 'tenant.view', 'module' => 'tenant'],
            ['name' => 'Create Tenants', 'slug' => 'tenant.create', 'module' => 'tenant'],
            ['name' => 'Update Tenants', 'slug' => 'tenant.update', 'module' => 'tenant'],
            ['name' => 'Delete Tenants', 'slug' => 'tenant.delete', 'module' => 'tenant'],
            ['name' => 'Suspend Tenants', 'slug' => 'tenant.suspend', 'module' => 'tenant'],
            ['name' => 'Restore Tenants', 'slug' => 'tenant.restore', 'module' => 'tenant'],
            ['name' => 'Export Tenants', 'slug' => 'tenant.export', 'module' => 'tenant'],
            ['name' => 'Tenant Billing', 'slug' => 'tenant.billing', 'module' => 'tenant'],

            // Platform Users
            ['name' => 'View Platform Users', 'slug' => 'platform-user.view', 'module' => 'platform-user'],
            ['name' => 'Create Platform Users', 'slug' => 'platform-user.create', 'module' => 'platform-user'],
            ['name' => 'Update Platform Users', 'slug' => 'platform-user.update', 'module' => 'platform-user'],
            ['name' => 'Delete Platform Users', 'slug' => 'platform-user.delete', 'module' => 'platform-user'],
            ['name' => 'Manage Platform User Roles', 'slug' => 'platform-user.role', 'module' => 'platform-user'],

            // Platform Roles
            ['name' => 'View Platform Roles', 'slug' => 'platform-role.view', 'module' => 'platform-role'],
            ['name' => 'Create Platform Roles', 'slug' => 'platform-role.create', 'module' => 'platform-role'],
            ['name' => 'Update Platform Roles', 'slug' => 'platform-role.update', 'module' => 'platform-role'],
            ['name' => 'Delete Platform Roles', 'slug' => 'platform-role.delete', 'module' => 'platform-role'],

            // Subscriptions
            ['name' => 'View Subscriptions', 'slug' => 'subscription.view', 'module' => 'subscription'],
            ['name' => 'Create Subscriptions', 'slug' => 'subscription.create', 'module' => 'subscription'],
            ['name' => 'Update Subscriptions', 'slug' => 'subscription.update', 'module' => 'subscription'],
            ['name' => 'Cancel Subscriptions', 'slug' => 'subscription.cancel', 'module' => 'subscription'],
            ['name' => 'Refund Subscriptions', 'slug' => 'subscription.refund', 'module' => 'subscription'],

            // Payments
            ['name' => 'View Payments', 'slug' => 'payment.view', 'module' => 'payment'],
            ['name' => 'Create Payments', 'slug' => 'payment.create', 'module' => 'payment'],
            ['name' => 'Refund Payments', 'slug' => 'payment.refund', 'module' => 'payment'],
            ['name' => 'Export Payments', 'slug' => 'payment.export', 'module' => 'payment'],
            ['name' => 'Verify Payments', 'slug' => 'payment.verify', 'module' => 'payment'],
            ['name' => 'View Raw Payment Details', 'slug' => 'payment.view_raw', 'module' => 'payment'],

            // Invoices
            ['name' => 'View Invoices', 'slug' => 'invoice.view', 'module' => 'invoice'],
            ['name' => 'Create Invoices', 'slug' => 'invoice.create', 'module' => 'invoice'],
            ['name' => 'Send Invoices', 'slug' => 'invoice.send', 'module' => 'invoice'],
            ['name' => 'Cancel Invoices', 'slug' => 'invoice.cancel', 'module' => 'invoice'],

            // Coupons
            ['name' => 'View Coupons', 'slug' => 'coupon.view', 'module' => 'coupon'],
            ['name' => 'Create Coupons', 'slug' => 'coupon.create', 'module' => 'coupon'],
            ['name' => 'Update Coupons', 'slug' => 'coupon.update', 'module' => 'coupon'],
            ['name' => 'Delete Coupons', 'slug' => 'coupon.delete', 'module' => 'coupon'],

            // Tax Rates
            ['name' => 'View Tax Rates', 'slug' => 'tax-rate.view', 'module' => 'tax-rate'],
            ['name' => 'Create Tax Rates', 'slug' => 'tax-rate.create', 'module' => 'tax-rate'],
            ['name' => 'Update Tax Rates', 'slug' => 'tax-rate.update', 'module' => 'tax-rate'],
            ['name' => 'Delete Tax Rates', 'slug' => 'tax-rate.delete', 'module' => 'tax-rate'],

            // Payment Methods
            ['name' => 'View Payment Methods', 'slug' => 'payment-method.view', 'module' => 'payment-method'],
            ['name' => 'Create Payment Methods', 'slug' => 'payment-method.create', 'module' => 'payment-method'],
            ['name' => 'Update Payment Methods', 'slug' => 'payment-method.update', 'module' => 'payment-method'],
            ['name' => 'Delete Payment Methods', 'slug' => 'payment-method.delete', 'module' => 'payment-method'],

            // Platform Settings
            ['name' => 'General Settings', 'slug' => 'platform-setting.general', 'module' => 'platform-setting'],
            ['name' => 'Security Settings', 'slug' => 'platform-setting.security', 'module' => 'platform-setting'],
            ['name' => 'Email Settings', 'slug' => 'platform-setting.email', 'module' => 'platform-setting'],
            ['name' => 'Payment Gateway Settings', 'slug' => 'platform-setting.payment', 'module' => 'platform-setting'],
            ['name' => 'Localization Settings', 'slug' => 'platform-setting.localization', 'module' => 'platform-setting'],

            // Backups
            ['name' => 'View Backups', 'slug' => 'backup.view', 'module' => 'backup'],
            ['name' => 'Create Backups', 'slug' => 'backup.create', 'module' => 'backup'],
            ['name' => 'Restore Backups', 'slug' => 'backup.restore', 'module' => 'backup'],
            ['name' => 'Delete Backups', 'slug' => 'backup.delete', 'module' => 'backup'],

            // Audit
            ['name' => 'View Audit Logs', 'slug' => 'audit.view', 'module' => 'audit'],
            ['name' => 'Export Audit Logs', 'slug' => 'audit.export', 'module' => 'audit'],
            ['name' => 'Delete Audit Logs', 'slug' => 'audit.delete', 'module' => 'audit'],

            // Support Tickets
            ['name' => 'View Tickets', 'slug' => 'ticket.view', 'module' => 'ticket'],
            ['name' => 'Create Tickets', 'slug' => 'ticket.create', 'module' => 'ticket'],
            ['name' => 'Reply to Tickets', 'slug' => 'ticket.reply', 'module' => 'ticket'],
            ['name' => 'Assign Tickets', 'slug' => 'ticket.assign', 'module' => 'ticket'],
            ['name' => 'Close Tickets', 'slug' => 'ticket.close', 'module' => 'ticket'],

            // System Monitoring
            ['name' => 'View Monitoring', 'slug' => 'monitor.view', 'module' => 'monitor'],
            ['name' => 'Manage Alerts', 'slug' => 'monitor.alert', 'module' => 'monitor'],
            ['name' => 'Manage Monitoring', 'slug' => 'monitor.manage', 'module' => 'monitor'],

            // API Management
            ['name' => 'View API Settings', 'slug' => 'api.view', 'module' => 'api'],
            ['name' => 'Manage API', 'slug' => 'api.manage', 'module' => 'api'],
            ['name' => 'Regenerate API Keys', 'slug' => 'api.key-regenerate', 'module' => 'api'],

            // System
            ['name' => 'Maintenance Mode', 'slug' => 'system.maintenance', 'module' => 'system'],
            ['name' => 'Clear Cache', 'slug' => 'system.cache-clear', 'module' => 'system'],
            ['name' => 'View Logs', 'slug' => 'system.log-view', 'module' => 'system'],
            ['name' => 'Manage Queue', 'slug' => 'system.queue-manage', 'module' => 'system'],
            ['name' => 'View System Info', 'slug' => 'system.info.view', 'module' => 'system'],

            // Subscription Plans
            ['name' => 'View Plans', 'slug' => 'plan.view', 'module' => 'plan'],
            ['name' => 'Create Plans', 'slug' => 'plan.create', 'module' => 'plan'],
            ['name' => 'Update Plans', 'slug' => 'plan.update', 'module' => 'plan'],
            ['name' => 'Delete Plans', 'slug' => 'plan.delete', 'module' => 'plan'],

            // Plan Features
            ['name' => 'View Features', 'slug' => 'feature.view', 'module' => 'feature'],
            ['name' => 'Create Features', 'slug' => 'feature.create', 'module' => 'feature'],
            ['name' => 'Update Features', 'slug' => 'feature.update', 'module' => 'feature'],
            ['name' => 'Delete Features', 'slug' => 'feature.delete', 'module' => 'feature'],

            // Plan Prices
            ['name' => 'View Prices', 'slug' => 'price.view', 'module' => 'price'],
            ['name' => 'Create Prices', 'slug' => 'price.create', 'module' => 'price'],
            ['name' => 'Update Prices', 'slug' => 'price.update', 'module' => 'price'],
            ['name' => 'Delete Prices', 'slug' => 'price.delete', 'module' => 'price'],

            // Platform Dashboard
            ['name' => 'View Platform Dashboard', 'slug' => 'platform-dashboard.view', 'module' => 'platform-dashboard'],

            // Platform Notifications
            ['name' => 'View Platform Notifications', 'slug' => 'platform-notification.view', 'module' => 'notification'],
            ['name' => 'Manage Platform Notifications', 'slug' => 'platform-notification.manage', 'module' => 'notification'],

            // Workspace (platform-level operations on workspaces)
            ['name' => 'View Workspace', 'slug' => 'workspace.view', 'module' => 'tenant'],
            ['name' => 'Delete Workspace', 'slug' => 'workspace.delete', 'module' => 'tenant'],
            ['name' => 'Transfer Workspace', 'slug' => 'workspace.transfer', 'module' => 'tenant'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }
    }

    private function createWorkspacePermissions(): void
    {
        $permissions = [
            // Income
            ['name' => 'View Income', 'slug' => 'income.view', 'module' => 'income'],
            ['name' => 'Create Income', 'slug' => 'income.create', 'module' => 'income'],
            ['name' => 'Update Income', 'slug' => 'income.update', 'module' => 'income'],
            ['name' => 'Delete Income', 'slug' => 'income.delete', 'module' => 'income'],
            ['name' => 'Restore Income', 'slug' => 'income.restore', 'module' => 'income'],
            ['name' => 'Force Delete Income', 'slug' => 'income.force-delete', 'module' => 'income'],
            ['name' => 'Archive Income', 'slug' => 'income.archive', 'module' => 'income'],
            ['name' => 'Approve Income', 'slug' => 'income.approve', 'module' => 'income'],
            ['name' => 'Export Income', 'slug' => 'income.export', 'module' => 'income'],
            ['name' => 'Import Income', 'slug' => 'income.import', 'module' => 'income'],

            // Expense
            ['name' => 'View Expense', 'slug' => 'expense.view', 'module' => 'expense'],
            ['name' => 'Create Expense', 'slug' => 'expense.create', 'module' => 'expense'],
            ['name' => 'Update Expense', 'slug' => 'expense.update', 'module' => 'expense'],
            ['name' => 'Delete Expense', 'slug' => 'expense.delete', 'module' => 'expense'],
            ['name' => 'Restore Expense', 'slug' => 'expense.restore', 'module' => 'expense'],
            ['name' => 'Force Delete Expense', 'slug' => 'expense.force-delete', 'module' => 'expense'],
            ['name' => 'Archive Expense', 'slug' => 'expense.archive', 'module' => 'expense'],
            ['name' => 'Approve Expense', 'slug' => 'expense.approve', 'module' => 'expense'],
            ['name' => 'Export Expense', 'slug' => 'expense.export', 'module' => 'expense'],
            ['name' => 'Import Expense', 'slug' => 'expense.import', 'module' => 'expense'],

            // Debt
            ['name' => 'View Debt', 'slug' => 'debt.view', 'module' => 'debt'],
            ['name' => 'Create Debt', 'slug' => 'debt.create', 'module' => 'debt'],
            ['name' => 'Update Debt', 'slug' => 'debt.update', 'module' => 'debt'],
            ['name' => 'Delete Debt', 'slug' => 'debt.delete', 'module' => 'debt'],
            ['name' => 'Restore Debt', 'slug' => 'debt.restore', 'module' => 'debt'],
            ['name' => 'Force Delete Debt', 'slug' => 'debt.force-delete', 'module' => 'debt'],
            ['name' => 'Approve Debt', 'slug' => 'debt.approve', 'module' => 'debt'],
            ['name' => 'Export Debt', 'slug' => 'debt.export', 'module' => 'debt'],

            // Asset
            ['name' => 'View Asset', 'slug' => 'asset.view', 'module' => 'asset'],
            ['name' => 'Create Asset', 'slug' => 'asset.create', 'module' => 'asset'],
            ['name' => 'Update Asset', 'slug' => 'asset.update', 'module' => 'asset'],
            ['name' => 'Delete Asset', 'slug' => 'asset.delete', 'module' => 'asset'],
            ['name' => 'Restore Asset', 'slug' => 'asset.restore', 'module' => 'asset'],
            ['name' => 'Force Delete Asset', 'slug' => 'asset.force-delete', 'module' => 'asset'],
            ['name' => 'Export Asset', 'slug' => 'asset.export', 'module' => 'asset'],

            // Budget
            ['name' => 'View Budget', 'slug' => 'budget.view', 'module' => 'budget'],
            ['name' => 'Create Budget', 'slug' => 'budget.create', 'module' => 'budget'],
            ['name' => 'Update Budget', 'slug' => 'budget.update', 'module' => 'budget'],
            ['name' => 'Delete Budget', 'slug' => 'budget.delete', 'module' => 'budget'],
            ['name' => 'Restore Budget', 'slug' => 'budget.restore', 'module' => 'budget'],
            ['name' => 'Force Delete Budget', 'slug' => 'budget.force-delete', 'module' => 'budget'],
            ['name' => 'Approve Budget', 'slug' => 'budget.approve', 'module' => 'budget'],
            ['name' => 'Export Budget', 'slug' => 'budget.export', 'module' => 'budget'],

            // Goal
            ['name' => 'View Goal', 'slug' => 'goal.view', 'module' => 'goal'],
            ['name' => 'Create Goal', 'slug' => 'goal.create', 'module' => 'goal'],
            ['name' => 'Update Goal', 'slug' => 'goal.update', 'module' => 'goal'],
            ['name' => 'Delete Goal', 'slug' => 'goal.delete', 'module' => 'goal'],
            ['name' => 'Restore Goal', 'slug' => 'goal.restore', 'module' => 'goal'],
            ['name' => 'Force Delete Goal', 'slug' => 'goal.force-delete', 'module' => 'goal'],
            ['name' => 'Export Goal', 'slug' => 'goal.export', 'module' => 'goal'],

            // Zakat
            ['name' => 'View Zakat', 'slug' => 'zakat.view', 'module' => 'zakat'],
            ['name' => 'Create Zakat', 'slug' => 'zakat.create', 'module' => 'zakat'],
            ['name' => 'Update Zakat', 'slug' => 'zakat.update', 'module' => 'zakat'],
            ['name' => 'Delete Zakat', 'slug' => 'zakat.delete', 'module' => 'zakat'],
            ['name' => 'Restore Zakat', 'slug' => 'zakat.restore', 'module' => 'zakat'],
            ['name' => 'Force Delete Zakat', 'slug' => 'zakat.force-delete', 'module' => 'zakat'],
            ['name' => 'Export Zakat', 'slug' => 'zakat.export', 'module' => 'zakat'],

            // Income Categories
            ['name' => 'View Income Categories', 'slug' => 'income-categories.view', 'module' => 'category'],
            ['name' => 'Create Income Categories', 'slug' => 'income-categories.create', 'module' => 'category'],
            ['name' => 'Update Income Categories', 'slug' => 'income-categories.update', 'module' => 'category'],
            ['name' => 'Delete Income Categories', 'slug' => 'income-categories.delete', 'module' => 'category'],

            // Expense Categories
            ['name' => 'View Expense Categories', 'slug' => 'expense-categories.view', 'module' => 'category'],
            ['name' => 'Create Expense Categories', 'slug' => 'expense-categories.create', 'module' => 'category'],
            ['name' => 'Update Expense Categories', 'slug' => 'expense-categories.update', 'module' => 'category'],
            ['name' => 'Delete Expense Categories', 'slug' => 'expense-categories.delete', 'module' => 'category'],

            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard'],

            // Reports
            ['name' => 'View Reports', 'slug' => 'report.view', 'module' => 'report'],
            ['name' => 'Create Reports', 'slug' => 'report.create', 'module' => 'report'],
            ['name' => 'Export Reports', 'slug' => 'report.export', 'module' => 'report'],
            ['name' => 'Schedule Reports', 'slug' => 'report.schedule', 'module' => 'report'],

            // Transactions
            ['name' => 'View Transactions', 'slug' => 'transaction.view', 'module' => 'transaction'],
            ['name' => 'Export Transactions', 'slug' => 'transaction.export', 'module' => 'transaction'],

            // Export
            ['name' => 'Export Data', 'slug' => 'export.data', 'module' => 'export'],
            ['name' => 'Export PDF', 'slug' => 'export.pdf', 'module' => 'export'],
            ['name' => 'Export Excel', 'slug' => 'export.excel', 'module' => 'export'],

            // Workspace Settings
            ['name' => 'View Workspace Settings', 'slug' => 'workspace-setting.view', 'module' => 'workspace-setting'],
            ['name' => 'Update Workspace Settings', 'slug' => 'workspace-setting.update', 'module' => 'workspace-setting'],
            ['name' => 'Delete Workspace', 'slug' => 'workspace-setting.delete', 'module' => 'workspace-setting'],

            // Workspace Users
            ['name' => 'View Workspace Users', 'slug' => 'workspace-user.view', 'module' => 'workspace-user'],
            ['name' => 'Invite Workspace Users', 'slug' => 'workspace-user.invite', 'module' => 'workspace-user'],
            ['name' => 'Update Workspace Users', 'slug' => 'workspace-user.update', 'module' => 'workspace-user'],
            ['name' => 'Remove Workspace Users', 'slug' => 'workspace-user.remove', 'module' => 'workspace-user'],
            ['name' => 'Change User Roles', 'slug' => 'workspace-user.role', 'module' => 'workspace-user'],

            // Workspace Roles
            ['name' => 'View Workspace Roles', 'slug' => 'workspace-role.view', 'module' => 'workspace-role'],
            ['name' => 'Assign Workspace Roles', 'slug' => 'workspace-role.assign', 'module' => 'workspace-role'],

            // Notifications
            ['name' => 'View Notifications', 'slug' => 'notification.view', 'module' => 'notification'],
            ['name' => 'Create Notifications', 'slug' => 'notification.create', 'module' => 'notification'],
            ['name' => 'Delete Notifications', 'slug' => 'notification.delete', 'module' => 'notification'],

            // Activity Log
            ['name' => 'View Activity Log', 'slug' => 'activity-log.view', 'module' => 'activity-log'],
            ['name' => 'Export Activity Log', 'slug' => 'activity-log.export', 'module' => 'activity-log'],

            // Workspace Billing
            ['name' => 'View Workspace Billing', 'slug' => 'billing.view', 'module' => 'billing'],
            ['name' => 'Manage Workspace Billing', 'slug' => 'billing.manage', 'module' => 'billing'],
            ['name' => 'View Workspace Invoices', 'slug' => 'workspace-invoice.view', 'module' => 'workspace-billing'],
            ['name' => 'Manage Workspace Invoices', 'slug' => 'workspace-invoice.manage', 'module' => 'workspace-billing'],
            ['name' => 'View Payments', 'slug' => 'payment.view', 'module' => 'payment'],
            ['name' => 'Approve Payments', 'slug' => 'payment.approve', 'module' => 'payment'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }
    }

    // ========================================================================
    //  ROLES
    // ========================================================================

    private function createPlatformRoles(): void
    {
        $allSlugs = Permission::pluck('id');

        $billingIds = Permission::whereIn('module', [
            'subscription', 'payment', 'invoice', 'coupon',
            'tax-rate', 'feature', 'price',
        ])->pluck('id');

        $ticketIds = Permission::whereIn('module', ['ticket'])->pluck('id');

        $backupIds = Permission::whereIn('module', ['backup'])->pluck('id');
        $monitorIds = Permission::whereIn('module', ['monitor'])->pluck('id');
        $apiIds = Permission::whereIn('module', ['api'])->pluck('id');
        $systemIds = Permission::whereIn('module', ['system'])->pluck('id');



        // Super Admin — ALL platform permissions
        $superAdmin = Role::updateOrCreate(
            ['slug' => 'super_admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full platform control — no restrictions',
                'guard_name' => 'web',
                'level' => 'platform',
                'is_system' => true,
                'sort_order' => 1,
            ]
        );
        $superAdmin->permissions()->sync(array_merge($allSlugs->toArray(), $this->permissionIds([
            'platform-notification.view', 'platform-notification.manage',
        ])));

        // Deputy Super Admin — all except security-critical
        $deputy = Role::updateOrCreate(
            ['slug' => 'deputy_super_admin'],
            [
                'name' => 'Deputy Super Admin',
                'description' => 'Everything except security settings, audit deletion, and super admin management',
                'guard_name' => 'web',
                'level' => 'platform',
                'is_system' => true,
                'sort_order' => 2,
            ]
        );

        $excludedSlugs = [
            'platform-setting.security', 'system.maintenance',
            'audit.delete', 'platform-user.delete',
            'platform-role.delete', 'backup.restore',
        ];
        $excludedIds = Permission::whereIn('slug', $excludedSlugs)->pluck('id');
        $deputy->permissions()->syncWithoutDetaching(
            $allSlugs->diff($excludedIds)->toArray()
        );
        $deputy->permissions()->syncWithoutDetaching($this->permissionIds([
            'platform-notification.view', 'platform-notification.manage',
        ]));

        // Platform Manager
        $platformMgr = Role::updateOrCreate(
            ['slug' => 'platform_manager'],
            [
                'name' => 'Platform Manager',
                'description' => 'Manages tenants, subscriptions, payments, support team, reports',
                'guard_name' => 'web',
                'level' => 'platform',
                'is_system' => true,
                'sort_order' => 3,
            ]
        );
        $platformMgr->permissions()->syncWithoutDetaching(
            Permission::whereIn('module', [
                'tenant', 'platform-dashboard',
            ])->pluck('id')->toArray()
        );
        $platformMgr->permissions()->syncWithoutDetaching($this->permissionIds([
            'subscription.view', 'subscription.create', 'subscription.update',
            'payment.view', 'payment.export',
            'invoice.view',
            'platform-setting.general', 'platform-setting.localization',
            'ticket.view', 'ticket.reply', 'ticket.assign', 'ticket.close',
            'audit.view', 'audit.export',
            'platform-user.view',
            'platform-role.view',
            'platform-notification.view', 'platform-notification.manage',
        ]));

        // Billing Manager
        $billingMgr = Role::updateOrCreate(
            ['slug' => 'billing_manager'],
            [
                'name' => 'Billing Manager',
                'description' => 'Manages invoices, payments, refunds, subscriptions, coupons',
                'guard_name' => 'web',
                'level' => 'platform',
                'is_system' => true,
                'sort_order' => 4,
            ]
        );
        $billingMgr->permissions()->syncWithoutDetaching($billingIds->toArray());
        $billingMgr->permissions()->syncWithoutDetaching($this->permissionIds([
            'tenant.view', 'tenant.billing',
            'platform-setting.payment',
        ]));

        // Support Team
        $support = Role::updateOrCreate(
            ['slug' => 'support_team'],
            [
                'name' => 'Support Team',
                'description' => 'Customer support, tickets, customer communication',
                'guard_name' => 'web',
                'level' => 'platform',
                'is_system' => true,
                'sort_order' => 5,
            ]
        );
        $support->permissions()->syncWithoutDetaching($ticketIds->toArray());
        $support->permissions()->syncWithoutDetaching($this->permissionIds([
            'tenant.view', 'platform-user.view',
        ]));

        // Technical Team
        $technical = Role::updateOrCreate(
            ['slug' => 'technical_team'],
            [
                'name' => 'Technical Team',
                'description' => 'Servers, monitoring, backups, logs, infrastructure',
                'guard_name' => 'web',
                'level' => 'platform',
                'is_system' => true,
                'sort_order' => 6,
            ]
        );
        $technical->permissions()->syncWithoutDetaching($backupIds->toArray());
        $technical->permissions()->syncWithoutDetaching($monitorIds->toArray());
        $technical->permissions()->syncWithoutDetaching($apiIds->toArray());
        $technical->permissions()->syncWithoutDetaching($systemIds->toArray());
        $technical->permissions()->syncWithoutDetaching($this->permissionIds([
            'audit.view',
        ]));

        // QA Team
        $qa = Role::updateOrCreate(
            ['slug' => 'qa_team'],
            [
                'name' => 'QA & Testing Team',
                'description' => 'Testing, staging environment, quality assurance',
                'guard_name' => 'web',
                'level' => 'platform',
                'is_system' => true,
                'sort_order' => 7,
            ]
        );
        $qa->permissions()->syncWithoutDetaching($this->permissionIds([
            'monitor.view', 'system.cache-clear', 'system.log-view',
        ]));
    }

    private function createWorkspaceRoles(): void
    {
        $workspaceModules = [
            'income', 'expense', 'debt', 'asset', 'budget', 'goal', 'zakat',
            'category', 'dashboard', 'report', 'transaction', 'export',
            'workspace-setting', 'workspace-user', 'workspace-role',
            'notification', 'activity-log', 'billing', 'workspace-billing', 'payment',
            'subscription',
        ];

        // ALL workspace permissions — dynamic, future-proof
        $allWorkspaceIds = Permission::whereIn('module', $workspaceModules)->pluck('id');

        // View-only subset
        $allView = Permission::whereIn('module', [
            'income', 'expense', 'debt', 'asset', 'budget', 'goal', 'zakat',
        ])->where('slug', 'LIKE', '%.view')->pluck('id');

        // Write permissions for financial modules (used by Finance Manager)
        $allWrite = Permission::whereIn('module', [
            'income', 'expense', 'debt', 'asset', 'budget', 'goal', 'zakat',
        ])->where('slug', 'NOT LIKE', '%.view')->pluck('id');

        // Category permissions
        $categoryAll = Permission::whereIn('slug', [
            'income-categories.view', 'income-categories.create',
            'income-categories.update', 'income-categories.delete',
            'expense-categories.view', 'expense-categories.create',
            'expense-categories.update', 'expense-categories.delete',
        ])->pluck('id');
        $categoryView = Permission::whereIn('slug', [
            'income-categories.view', 'expense-categories.view',
        ])->pluck('id');

        // Destructive slugs — excluded from Deputy
        $destructiveSlugs = [
            'income.delete', 'income.force-delete',
            'expense.delete', 'expense.force-delete',
            'debt.delete', 'debt.force-delete',
            'asset.delete', 'asset.force-delete',
            'budget.delete', 'budget.force-delete',
            'goal.delete', 'goal.force-delete',
            'zakat.delete', 'zakat.force-delete',
            'income-categories.delete', 'expense-categories.delete',
            'workspace-setting.delete',
        ];

        // Admin (Owner) — ALL workspace permissions
        $admin = Role::updateOrCreate(
            ['slug' => 'workspace_admin'],
            [
                'name' => 'Admin (Owner)',
                'description' => 'Full workspace control, billing, users, roles, all modules',
                'guard_name' => 'web',
                'level' => 'workspace',
                'is_system' => true,
                'sort_order' => 1,
            ]
        );
        $admin->permissions()->sync($allWorkspaceIds->toArray());

        // Deputy Admin — ALL except destructive
        $excludedIds = Permission::whereIn('slug', $destructiveSlugs)->pluck('id');
        $deputy = Role::updateOrCreate(
            ['slug' => 'workspace_deputy_admin'],
            [
                'name' => 'Deputy Admin',
                'description' => 'All except: permanent delete and workspace deletion',
                'guard_name' => 'web',
                'level' => 'workspace',
                'is_system' => true,
                'sort_order' => 2,
            ]
        );
        $deputy->permissions()->sync(
            $allWorkspaceIds->diff($excludedIds)->values()->toArray()
        );

        // Finance Manager
        $financeMgr = Role::updateOrCreate(
            ['slug' => 'workspace_finance_manager'],
            [
                'name' => 'Finance Manager',
                'description' => 'Income, expenses, assets, debts, financial reports',
                'guard_name' => 'web',
                'level' => 'workspace',
                'is_system' => true,
                'sort_order' => 3,
            ]
        );
        $financeMgr->permissions()->syncWithoutDetaching($allWrite->toArray());
        $financeMgr->permissions()->syncWithoutDetaching($categoryView->toArray());
        $financeMgr->permissions()->syncWithoutDetaching($this->permissionIds([
            'dashboard.view',
            'report.view', 'report.create', 'report.export',
            'transaction.view', 'transaction.export',
            'export.data', 'export.pdf', 'export.excel',
            'notification.view',
            'income.archive', 'expense.archive',
        ]));

        // Accountant
        $accountant = Role::updateOrCreate(
            ['slug' => 'workspace_accountant'],
            [
                'name' => 'Accountant',
                'description' => 'Accounting operations — full financial read/write, reconcile, export',
                'guard_name' => 'web',
                'level' => 'workspace',
                'is_system' => true,
                'sort_order' => 4,
            ]
        );
        $accountant->permissions()->syncWithoutDetaching(
            Permission::whereIn('module', ['income', 'expense', 'debt', 'asset', 'budget', 'goal', 'zakat'])
                ->where('slug', 'NOT LIKE', '%.delete')
                ->where('slug', 'NOT LIKE', '%.restore')
                ->pluck('id')
                ->toArray()
        );
        $accountant->permissions()->syncWithoutDetaching($categoryView->toArray());
        $accountant->permissions()->syncWithoutDetaching($this->permissionIds([
            'dashboard.view',
            'report.view', 'report.create', 'report.export',
            'transaction.view', 'transaction.export',
            'export.data', 'export.pdf', 'export.excel',
            'notification.view',
            'income.archive', 'expense.archive',
        ]));

        // Editor
        $editor = Role::updateOrCreate(
            ['slug' => 'workspace_editor'],
            [
                'name' => 'Editor',
                'description' => 'Create and update records, view shared data — no delete, no approve',
                'guard_name' => 'web',
                'level' => 'workspace',
                'is_system' => true,
                'sort_order' => 5,
            ]
        );
        $editor->permissions()->syncWithoutDetaching($allView->toArray());
        $editor->permissions()->syncWithoutDetaching($this->permissionIds([
            'income.create', 'income.update',
            'expense.create', 'expense.update',
            'debt.create', 'debt.update',
            'asset.create', 'asset.update',
            'budget.create', 'budget.update',
            'goal.create', 'goal.update',
            'zakat.create', 'zakat.update',
            'income-categories.view', 'expense-categories.view',
            'dashboard.view',
            'notification.view',
        ]));

        // Viewer
        $viewer = Role::updateOrCreate(
            ['slug' => 'workspace_viewer'],
            [
                'name' => 'Viewer',
                'description' => 'Read-only access, run reports — no create, update, or delete',
                'guard_name' => 'web',
                'level' => 'workspace',
                'is_system' => true,
                'sort_order' => 6,
            ]
        );
        $viewer->permissions()->syncWithoutDetaching($allView->toArray());
        $viewer->permissions()->syncWithoutDetaching($categoryView->toArray());
        $viewer->permissions()->syncWithoutDetaching($this->permissionIds([
            'dashboard.view',
            'report.view', 'report.export',
            'transaction.view',
            'notification.view',
        ]));
    }
}
