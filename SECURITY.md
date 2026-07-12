# Security & RBAC — Finance Manager

> آخر تحديث: 2026-07-07

---

## Authentication

- **Session-based** (web) with `auth` middleware
- **Token-based** (API) with Sanctum + ability scoping
- **2FA** via Google2FA (pragmarx) — intended for `super_admin`, `deputy_super_admin`, `workspace_admin`
- ⚠️ `ForceTwoFactor` middleware (`app/Http/Middleware/ForceTwoFactor.php:15-23`) is **currently inert** — `handle()` has all logic commented out. 2FA setup works but is **not enforced** for any role.
- **2FA secret** encrypted at rest (`encrypted` cast on `google2fa_secret`)
- **Email verification** required for workspace access

### API Token Abilities

Defined in `config/api-abilities.php` (32 abilities):

`income:read`, `income:write`, `expense:read`, `expense:write`, `debt:read`, `debt:write`, `asset:read`, `asset:write`, `budget:read`, `budget:write`, `goal:read`, `goal:write`, `zakat:read`, `zakat:write`, `report:read`, `report:write`, `export:data`, `workspace:read`, `workspace:write`, `user:read`, `user:write`, `notification:read`, `notification:write`, `dashboard:read`, `transaction:read`, `income-categories:read`, `income-categories:write`, `expense-categories:read`, `expense-categories:write`, `subscription:read`, `subscription:write`, `*` (full access)

## Authorization (RBAC)

Dual RBAC system: **Platform Roles** + **Workspace Roles**.

### Permission Verification

```php
// PLATFORM: Check platform permission
$user->hasPermission('tenant.create', 'platform');

// WORKSPACE: Check workspace permission
$user->hasPermission('income.create', 'workspace');

// ANY (checks both contexts):
$user->hasPermission('income.view'); // default context = 'any'

// Direct platform check:
$user->hasPlatformPermission('platform-notification.view');

// Direct workspace check:
$user->workspaceHasPermission('income.create');
```

### Platform Roles (7)

| Role | Slug | Access |
|------|------|--------|
| Super Admin | `super_admin` | Full platform control |
| Deputy Super Admin | `deputy_super_admin` | All except destructive |
| Platform Manager | `platform_manager` | Tenants, subscriptions, payments |
| Billing Manager | `billing_manager` | Invoices, payments, refunds, coupons |
| Support Team | `support_team` | Tickets, read-only customer data |
| Technical Team | `technical_team` | Backups, monitoring, logs, queue |
| QA Team | `qa_team` | Test environment, read-only |

### Workspace Roles (6)

| Role | Slug | Access |
|------|------|--------|
| Admin (Owner) | `workspace_admin` | Full workspace control |
| Deputy Admin | `workspace_deputy_admin` | All except destructive |
| Finance Manager | `workspace_finance_manager` | Daily ops, team, reports |
| Accountant | `workspace_accountant` | Full financial read/write |
| Editor | `workspace_editor` | Create/edit own records |
| Viewer | `workspace_viewer` | Read-only |

### Naming Convention

```
{module}.{action}
Actions: create | view | update | delete | restore | archive | approve | export | import | assign | manage
```

### Permission Modules

**Platform:** `tenant`, `platform-user`, `platform-role`, `subscription`, `payment`, `invoice`, `coupon`, `plan`, `platform-setting`, `backup`, `audit`, `ticket`, `monitor`, `api`, `system`, `platform-dashboard`, `platform-notification`, `billing`

**Workspace:** `income`, `expense`, `debt`, `asset`, `budget`, `goal`, `zakat`, `category`, `dashboard`, `report`, `transaction`, `export`, `workspace-setting`, `workspace-user`, `workspace-role`, `notification`, `activity-log`, `billing`, `workspace-billing`, `payment`

### Platform Permission Matrix

| Module | Slug | Super Admin | Deputy SA | Platform Mgr | Billing Mgr | Support | Technical | QA |
|--------|------|:-----------:|:----------:|:------------:|:-----------:|:-------:|:---------:|:--:|
| **Tenant** | `tenant.view` | ✅ | ✅ | ✅ | ✅ | ✅ | — | ✅ |
| | `tenant.create` | ✅ | ✅ | ✅ | — | — | — | — |
| | `tenant.update` | ✅ | ✅ | ✅ | — | — | — | — |
| | `tenant.delete` | ✅ | — | — | — | — | — | — |
| | `tenant.suspend` | ✅ | ✅ | ✅ | — | — | — | — |
| | `tenant.restore` | ✅ | ✅ | ✅ | — | — | — | — |
| **Platform Users** | `platform-user.view` | ✅ | ✅ | ✅ | — | ✅ | — | — |
| | `platform-user.create` | ✅ | ✅ | — | — | — | — | — |
| | `platform-user.update` | ✅ | ✅ | — | — | — | — | — |
| | `platform-user.delete` | ✅ | — | — | — | — | — | — |
| | `platform-user.role` | ✅ | ✅ | — | — | — | — | — |
| **Platform Roles** | `platform-role.view` | ✅ | ✅ | ✅ | — | — | — | — |
| | `platform-role.create` | ✅ | ✅ | — | — | — | — | — |
| | `platform-role.update` | ✅ | ✅ | — | — | — | — | — |
| | `platform-role.delete` | ✅ | — | — | — | — | — | — |
| **Subscriptions** | `subscription.*` | ✅ | ✅ | ✅ | ✅ | — | — | — |
| **Payments** | `payment.*` | ✅ | ✅ | ✅ | ✅ | — | — | — |
| **Invoices** | `invoice.*` | ✅ | ✅ | ✅ | ✅ | — | — | — |
| **Coupons** | `coupon.*` | ✅ | ✅ | — | ✅ | — | — | — |
| **Platform Settings** | `platform-setting.general` | ✅ | ✅ | ✅ | — | — | — | — |
| | `platform-setting.security` | ✅ | — | — | — | — | — | — |
| | `platform-setting.payment` | ✅ | ✅ | — | ✅ | — | — | — |
| | `platform-setting.localization` | ✅ | ✅ | ✅ | — | — | — | — |
| **Backup** | `backup.*` | ✅ | ✅ | ✅ | — | — | ✅ | — |
| **Audit** | `audit.view` | ✅ | ✅ | ✅ | ✅ | — | ✅ | — |
| | `audit.export` | ✅ | ✅ | ✅ | ✅ | — | — | — |
| | `audit.delete` | ✅ | — | — | — | — | — | — |
| **System** | `system.maintenance` | ✅ | — | — | — | — | ✅ | — |
| | `system.cache-clear` | ✅ | ✅ | ✅ | — | — | ✅ | ✅ |
| | `system.log-view` | ✅ | ✅ | — | — | — | ✅ | — |
| | `system.queue-manage` | ✅ | ✅ | — | — | — | ✅ | — |

### Workspace Permission Matrix

| Module | Admin | Deputy Admin | Finance Mgr | Accountant | Editor | Viewer |
|--------|:-----:|:------------:|:-----------:|:----------:|:-----:|:------:|
| **Income** — `income.view` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `income.create` | ✅ | ✅ | ✅ | ✅ | ✅ | — |
| `income.update` | ✅ | ✅ | ✅ | ✅ | ✅ | — |
| `income.delete` | ✅ | ✅ | ✅ | — | — | — |
| `income.restore` | ✅ | ✅ | — | — | — | — |
| **Expense** — Same structure | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Debt** — Same structure | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Asset** — Same structure | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Budget** — Same structure | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Goal** — Same structure | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Zakat** — Same structure | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Reports** — `report.view` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `report.create` | ✅ | ✅ | ✅ | ✅ | — | — |
| `report.export` | ✅ | ✅ | ✅ | ✅ | — | — |
| **Settings** — `workspace-setting.view` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `workspace-setting.update` | ✅ | — | — | — | — | — |
| **Users** — `workspace-user.view` | ✅ | ✅ | ✅ | ✅ | — | — |
| `workspace-user.invite` | ✅ | ✅ | — | — | — | — |
| `workspace-user.role` | ✅ | ✅ | — | — | — | — |
| **Billing** — `billing.view` | ✅ | ✅ | ✅ | — | — | — |
| `billing.manage` | ✅ | ✅ | — | — | — | — |

## Data Protection

- **WorkspaceScope**: Global scope on 14+ models prevents cross-tenant data leakage
- **Encrypted settings**: `Setting::getSecret/setSecret` with `Crypt::encryptString`
- **Encrypted asset fields**: `account_number`, `bank_name` cast as `encrypted`
- **Security headers**: `SecurityHeaders` middleware adds CSP, HSTS, X-Frame-Options

## Rate Limiting (API)

Defined in `app/Providers/RateLimiterServiceProvider.php`:

| Limiter | Rate | Applied To |
|---------|------|------------|
| `api-auth` | 5/min | Login, register |
| `api` | 120/min | General API |
| `api-workspace` | 200/min | Workspaced API |
| `api-sensitive` | 10/min | Sensitive operations |
| `super-admin-settings` | 10/min | Super admin settings |
| `webhook` | 30/min | Webhook endpoints |
| `web` | 300/min | General web routes |

## Policies

11 authorization policies using owner-based + permission-based checks:

```php
// Example: ExpensePolicy
return $expense->user_id === $user->id || $user->hasPermission('expense.view');
```

## Key Decisions

- `hasPermission()` default context is `'any'` — checks platform then workspace
- `cachedPlatformPermissions()` uses `platformRoles()` (level = 'platform')
- `currentWorkspaceRole()` returns the current workspace role
- `is_super_admin` column fully removed — use `hasRole('super_admin')`
- Context-aware middleware: `HasPlatformPermission` and `HasWorkspacePermission`
