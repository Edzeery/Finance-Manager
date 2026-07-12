# Finance Manager — Comprehensive Full-Stack Audit Report

**Project:** Personal/Enterprise Finance Manager (SaaS, Multi-Tenant)  
**Stack:** Laravel 13 / PHP 8.3 / Livewire-Volt / Alpine.js / MySQL / Laragon  
**Audit Date:** 2026-07-12  
**Market:** Algeria (DZD, Arabic/RTL, Africa/Algiers timezone)

---

## Table of Contents
1. [Project Overview](#1-project-overview)
2. [Backend Architecture](#2-backend-architecture)
   - 2.1 Directory Structure
   - 2.2 Config Files
   - 2.3 Database Migrations
   - 2.4 Eloquent Models
   - 2.5 Multi-Tenancy System
   - 2.6 RBAC System
   - 2.7 Service Layer
   - 2.8 Policies
   - 2.9 Middleware
   - 2.10 Events, Listeners, Jobs
   - 2.11 Notifications
   - 2.12 Scheduled Commands
   - 2.13 Service Providers
   - 2.14 Enums
3. [API Layer](#3-api-layer)
4. [Frontend Architecture](#4-frontend-architecture)
   - 4.1 Livewire-Volt Components
   - 4.2 Blade Layouts & Views
   - 4.3 Vite & Asset Pipeline
   - 4.4 RTL / Arabic Support
   - 4.5 Alpine.js Usage
   - 4.6 Auth Views
   - 4.7 Admin / Super-Admin Panel
5. [Subscription & Payments Module](#5-subscription--payments-module)
   - 5.1 Models
   - 5.2 Services
   - 5.3 Gateway Integration
   - 5.4 Webhook System
   - 5.5 Proration
6. [Zakat Module](#6-zakat-module)
7. [Notifications & Mailing](#7-notifications--mailing)
8. [Translations](#8-translations)
9. [Test Suite](#9-test-suite)
10. [Observations Summary](#10-observations-summary)

---

## 1. Project Overview

**Purpose:** A multi-tenant SaaS financial management application targeting the Algerian market. Core features:
- Income/Expense tracking with categories
- Budget management
- Debt tracking
- Asset management
- Financial goals
- Zakat (Islamic charity) calculation
- Multi-currency support (DZD primary)
- Full RTL/Arabic interface
- Subscription billing (monthly/yearly) via multiple payment gateways
- Role-based access control (workspace-level + platform-level)
- Activity logging
- Reports and charts

**Current State:** 643 passing tests, 11 skipped, 0 failures.

---

## 2. Backend Architecture

### 2.1 Directory Structure

```
app/
├── Console/Commands/        — 14 scheduled/CLI commands
├── Contracts/               — Interfaces for repositories & services
│   ├── Repositories/        — 7 repository interfaces
│   └── Services/            — 5 service interfaces
├── Enums/                   — 12 enums (BackedEnum pattern)
├── Events/                  — 7 event classes
├── Exceptions/              — (exists, files not read)
├── Http/
│   ├── Controllers/         — Web + API controllers
│   │   └── Api/             — REST API controllers
│   ├── Middleware/           — 19 middleware classes
│   ├── Requests/            — Form request validation
│   └── Resources/           — API resource classes
├── Jobs/                    — 3 queued jobs
├── Listeners/               — 5 event listeners
├── Livewire/                — Volt API components (non-Volt ones?)
├── Mail/                    — Mailables
├── Models/                  — Eloquent models
│   ├── Concerns/            — Traits (BelongsToWorkspace, etc.)
│   └── Scopes/              — Global scopes (WorkspaceScope, etc.)
├── Notifications/           — 3 notification classes
├── Observers/               — 1 observer (DashboardCacheObserver)
├── Policies/                — 10 policy classes + gate definitions
├── Providers/               — 8 service providers
├── Repositories/            — 7 repository implementations
├── Rules/                   — Custom validation rules
├── Services/                — Core business logic
│   ├── Payments/            — Gateway implementations + supporting services
│   │   ├── Chargily/        — Chargily-specific (checkout, webhook, signature)
│   │   └── Noest/           — Noest-specific (gateway, service)
│   └── Webhooks/            — Webhook signature validators
├── Traits/                  — General traits
└── View/                    — View composers / presenters
```

**Status:** Well-organized domain-driven structure. Clean separation of concerns.

### 2.2 Config Files (config/)

| File | Observations |
|------|-------------|
| `app.php` | Standard; `locale` => `'ar'`, `timezone` => `'Africa/Algiers'`, `currency` => `'DZD'` |
| `auth.php` | Standard; single guard (`web`), single provider (`users`), Sanctum configured elsewhere |
| `cache.php` | Memcached/Redis available but not configured in .env |
| `database.php` | MySQL default, SQLite `:memory:` for testing |
| `finance.php` | Custom config with plan features, rate limits, default settings; **no `.env` overrides for rate limits** |
| `mail.php` | Default `log` driver; Markdown mail theme at `resources/views/vendor/mail` |
| `queue.php` | Default `database` driver; `sync` also configured |
| `services.php` | External integrations (Chargily, PayPal, Stripe, etc.) |
| `session.php` | File-based driver |
| `sanctum.php` | Standard Sanctum config |
| `livewire.php` | Standard with Volt layout |
| `permission.php` | Custom RBAC config |
| `settings.php` | App settings configuration |

**Observation:** No `.env.example` in repo root. `.env.testing` file does not exist — tests rely on `config:clear` to avoid MySQL cached settings.

### 2.3 Database Migrations

Number of migration files: ~40+ covering:
- Users, workspaces, roles, permissions (RBAC)
- Subscription plans, plan features, plan prices, subscriptions
- Payment methods, payments, payment verifications
- Expenses, incomes, budgets, debts, assets, financial goals
- Categories (expense, income)
- Zakat records, Zakat thresholds (nisab)
- Activity logs, notifications, user settings
- Coupons, tax rates, delivery zones
- Personal access tokens (Sanctum)
- Cache, sessions, jobs, job batches, failed jobs
- Settings table

**Observation:** Soft deletes are used on many models (Payments, Subscriptions, Workspaces, Users, etc.).
`ValidateSoftDeletes.php` command exists but migration may need to be verified.

### 2.4 Eloquent Models

**Complete Model List:**

| Model | File | Key Traits | Soft Deletes | Workspace Scoped |
|-------|------|------------|-------------|-----------------|
| User | `app/Models/User.php` | HasFactory, Notifiable, HasRoles, HasPermissions, HasWorkspaces, TwoFactorAuthenticatable | No | No (platform-level) |
| Workspace | `app/Models/Workspace.php` | HasFactory, SoftDeletes | Yes | No (parent) |
| Expense | `app/Models/Expense.php` | BelongsToWorkspace, HasFactory, SoftDeletes | Yes | Yes |
| Income | `app/Models/Income.php` | BelongsToWorkspace, HasFactory, SoftDeletes | Yes | Yes |
| Budget | `app/Models/Budget.php` | BelongsToWorkspace, HasFactory, SoftDeletes | Yes | Yes |
| Debt | `app/Models/Debt.php` | BelongsToWorkspace, HasFactory, SoftDeletes | Yes | Yes |
| Asset | `app/Models/Asset.php` | BelongsToWorkspace, HasFactory, SoftDeletes | Yes | Yes |
| FinancialGoal | `app/Models/FinancialGoal.php` | BelongsToWorkspace, HasFactory, SoftDeletes | Yes | Yes |
| ExpenseCategory | `app/Models/ExpenseCategory.php` | BelongsToWorkspace, HasFactory | No | Yes |
| IncomeCategory | `app/Models/IncomeCategory.php` | BelongsToWorkspace, HasFactory | No | Yes |
| ZakatRecord | `app/Models/ZakatRecord.php` | BelongsToWorkspace, HasFactory | No | Yes |
| Subscription | `app/Models/Subscription.php` | BelongsToWorkspace, HasFactory | No | Yes |
| SubscriptionPlan | `app/Models/SubscriptionPlan.php` | HasFactory | No | No (platform-level) |
| Payment | `app/Models/Payment.php` | BelongsToWorkspace, HasFactory, SoftDeletes | Yes | Yes |
| PaymentMethod | `app/Models/PaymentMethod.php` | HasFactory | No | No |
| PlanFeature | `app/Models/PlanFeature.php` | HasFactory | No | No |
| PlanPrice | `app/Models/PlanPrice.php` | HasFactory | No | No |
| Coupon | `app/Models/Coupon.php` | HasFactory | No | No |
| Role | `app/Models/Role.php` | HasFactory | No | No |
| Permission | `app/Models/Permission.php` | HasFactory | No | No |
| ActivityLog | `app/Models/ActivityLog.php` | HasFactory | No | Yes |
| Notification | `app/Models/Notification.php` | HasFactory | No | No (uses `notifiable_id`) |
| UserSetting | `app/Models/UserSetting.php` | HasFactory | No | No |
| Setting | `app/Models/Setting.php` | HasFactory | No | No (platform-level) |
| TaxRate | `app/Models/TaxRate.php` | HasFactory | No | No |
| PaymentVerification | `app/Models/PaymentVerification.php` | HasFactory | No | Yes |
| PersonalAccessToken | `app/Models/PersonalAccessToken.php` | (Sanctum) | No | No |
| DeliveryZone | `app/Models/DeliveryZone.php` | HasFactory | No | No |

**Observations:**
- Clean model design with consistent trait usage
- `BelongsToWorkspace` trait (at `app/Models/Concerns/BelongsToWorkspace.php:17-41`) auto-stamps `workspace_id` on `creating`
- `WorkspaceScope` (at `app/Models/Scopes/WorkspaceScope.php:18-32`) is a global scope applied via `booted()` in each scoped model
- `SubscriptionPlan` uses `$appends = ['yearly_price']` with an accessor that queries `activePrices()` relation — potential N+1
- `User` model was not fully audited, but references to `HasRoles`, `HasPermissions`, `HasWorkspaces` traits suggest a separate package (spatie/laravel-permission?) or custom implementation

### 2.5 Multi-Tenancy System

**Architecture:** Workspace-based multi-tenancy (not database-per-tenant).

**Key mechanisms:**
1. **`BelongsToWorkspace` trait** (`app/Models/Concerns/BelongsToWorkspace.php`): Auto-fills `workspace_id` on creation from `session('current_workspace_id')` or `auth()->user()->currentWorkspace?->id`
2. **`WorkspaceScope` global scope** (`app/Models/Scopes/WorkspaceScope.php`): Applies `where('workspace_id', ...)` on all queries for scoped models
3. **`withoutWorkspace()` scope method**: Provided by `BelongsToWorkspace` concern to bypass the global scope
4. **`SetWorkspace` middleware** (`app/Http/Middleware/SetWorkspace.php`): Sets the current workspace in session based on domain/subdomain or user selection
5. **`ApiWorkspace` middleware** (`app/Http/Middleware/ApiWorkspace.php`): Sets workspace for API routes

**Tenant isolation levels:**
- **Data isolation:** Enforced at query level via global scope (soft isolation — a developer could forget to use the trait)
- **Route isolation:** Tenant routes defined in `routes/tenant.php` (353 lines), wrapped in workspace middleware
- **File isolation:** No tenant-specific file storage; all files served from same filesystem

**Observations:**
- The `BelongsToWorkspace` trait depends on `session('current_workspace_id')` — this is set by `SetWorkspace` middleware. If middleware fails or is skipped, the trait falls back to `auth()->user()->currentWorkspace?->id`
- No hard tenant isolation at the database level — same database, same tables, row-level scoping
- **Potential issue:** `User::withoutWorkspace()` pattern used in `Payment::booted()` (line 67) — this bypasses the global scope correctly

### 2.6 RBAC System

**Architecture:** Custom (or spatie-based) dual-layer RBAC with:
- **Platform-level roles/permissions**: `super_admin`, `admin`, `user` — managed in `config/permission.php` and middleware `HasPlatformRole`, `HasPlatformPermission`, `SuperAdmin`
- **Workspace-level roles/permissions**: Managed by `HasWorkspaceRole`, `HasWorkspacePermission` middleware

**Middleware inventory (RBAC):**

| Middleware | Purpose |
|-----------|---------|
| `CheckPermission.php` | Checks a single permission |
| `CheckPlanFeature.php` | Checks workspace plan feature access |
| `CheckActiveSubscription.php` | Ensures active subscription |
| `CheckSubscriptionStatus.php` | Checks specific subscription status |
| `CheckApiAbility.php` | Checks API token ability |
| `CheckApiQuota.php` | Checks API rate limit |
| `CheckApiSubscription.php` | Checks API subscription access |
| `HasPlatformPermission.php` | Platform-level permission check |
| `HasPlatformRole.php` | Platform-level role check |
| `HasWorkspacePermission.php` | Workspace-level permission check |
| `HasWorkspaceRole.php` | Workspace-level role check |
| `SuperAdmin.php` | Super admin guard |
| `EnsureOnboardingCompleted.php` | Onboarding completion check |
| `ForceTwoFactor.php` | 2FA enforcement |
| `SetLocale.php` | Locale setting from user preferences |
| `SetTheme.php` | Theme setting |
| `SetWorkspace.php` | Current workspace resolution |
| `SecurityHeaders.php` | Security headers (CSP, HSTS, etc.) |
| `ApiWorkspace.php` | API workspace resolver |

**Observations:**
- Middleware naming: some use `Has*` prefix, others use `Check*` prefix — inconsistent naming convention
- `SuperAdmin.php` middleware is separate from `HasPlatformRole.php` — could be consolidated
- `SecurityHeaders.php` is well-structured (CSP nonce from Vite, HSTS, X-Frame-Options, etc.)

**Policies** (`app/Providers/PolicyServiceProvider.php:32-53`):
- Super admin bypass via `Gate::before` returning `true`
- Policies registered for: Budget, FinancialGoal, Debt, Asset, Expense, Income, ExpenseCategory, IncomeCategory, Notification, ZakatRecord, Workspace
- Gates defined for: `report.view`, `report.export`, `workspace-setting.view`, `activity-log.view`

### 2.7 Service Layer

**Repository pattern:**
- 7 repository interfaces in `app/Contracts/Repositories/`
- 7 implementations in `app/Repositories/`
- Bound in `BindingServiceProvider.php`

**Service layer:**

| Service | File | Purpose |
|---------|------|---------|
| `ActivityLogService` | `app/Services/ActivityLogService.php` | Activity logging with sensitive data filtering |
| `ChartDataService` | `app/Services/ChartDataService.php` | Chart data aggregation |
| `DashboardService` | `app/Services/DashboardService.php` | Dashboard KPIs |
| `ReportService` | `app/Services/ReportService.php` | Financial reports |
| `SearchService` | `app/Services/SearchService.php` | Global search |
| `SubscriptionService` | `app/Services/SubscriptionService.php` (468 lines) | Subscription lifecycle (create, renew, cancel, upgrade/downgrade) |
| `SubscriptionPaymentService` | `app/Services/SubscriptionPaymentService.php` | Payment linking for subscriptions |
| `SubscriptionProrationService` | `app/Services/SubscriptionProrationService.php` (81 lines) | Proration calculations |
| `PaymentService` | `app/Services/PaymentService.php` (291 lines) | Payment processing |
| `PaymentTransitionValidator` | `app/Services/PaymentTransitionValidator.php` (60 lines) | State machine validation |
| `RedirectService` | `app/Services/RedirectService.php` | Post-payment redirects |
| `TwoFactorAuthenticationService` | `app/Services/TwoFactorAuthenticationService.php` | 2FA management |

**Payment Gateway Services (in `app/Services/Payments/`):**
- `GatewayManager.php` — Registry pattern for all gateways
- `ChargilyGateway.php` + `ChargilyCheckoutService.php` + `ChargilySignatureValidator.php` + `ChargilyWebhookService.php`
- `BaridiMobGateway.php`
- `PayPalGateway.php`
- `RedotPayGateway.php`
- `StripeGateway.php`
- `WiseGateway.php` + `WiseManualGateway.php`
- `PayoneerGateway.php`
- `CashGateway.php`
- `DeliveryGateway.php`
- `NoestGateway.php` + `NoestService.php`

**Webhook Signature Validators (in `app/Services/Webhooks/`):**
- `WebhookSignatureManager.php` — Registry pattern
- `StripeSignatureValidator.php`
- `PayPalSignatureValidator.php`
- `WiseSignatureValidator.php`
- `PayoneerSignatureValidator.php`
- `NoestSignatureValidator.php`

**Observations:**
- `SubscriptionService.php` at 468 lines is the largest service — may benefit from splitting
- GatewayManager registers 10 payment gateways, including both digital and offline (cash, delivery)
- WebhookSignatureManager uses a clean registration pattern

### 2.8 Middleware

19 middleware classes registered. Functionally grouped:

**Authentication/Security:**
- `ForceTwoFactor.php` — Requires 2FA completion
- `SecurityHeaders.php` — CSP, HSTS, X-Frame-Options, etc.

**Multi-tenancy:**
- `SetWorkspace.php` — Resolves workspace from domain/session
- `ApiWorkspace.php` — API workspace from header/token
- `EnsureOnboardingCompleted.php` — Blocks un-onboarded users

**RBAC:**
- `HasPlatformRole.php`, `HasPlatformPermission.php`, `SuperAdmin.php`
- `HasWorkspaceRole.php`, `HasWorkspacePermission.php`
- `CheckPermission.php`
- `CheckPlanFeature.php`

**Subscription:**
- `CheckActiveSubscription.php`
- `CheckSubscriptionStatus.php`

**API:**
- `CheckApiAbility.php`, `CheckApiQuota.php`, `CheckApiSubscription.php`

**Localization:**
- `SetLocale.php`, `SetTheme.php`

### 2.9 Events, Listeners, Jobs

**Events (7):**
- `PaymentCompleted`, `PaymentFailed`
- `SubscriptionActivated`
- `InvitationCreated`, `InvitationAccepted`, `InvitationDeclined`, `InvitationExpired`

**Listeners (5):**
- `SendPaymentReceipt` — Listens to `PaymentCompleted`
- `ActivateWorkspace` — Listens to `PaymentCompleted`
- `CompleteOnboarding` — Listens to `PaymentCompleted`
- `CreateAdminNotification` — Listens to `PaymentCompleted`, `SubscriptionActivated`, `Registered`
- `LogAuthEvent` — Subscribed as event subscriber (via `Event::subscribe`)

**Jobs (3):**
- `LogActivity` — Dispatched from `ModelEventServiceProvider` observers
- `ActivateSubscription` — Subscription activation
- `SendSubscriptionExpiryNotification` — Expiry reminders

**Observations:**
- 4 listeners all listen to `PaymentCompleted` — could use a single listener that delegates, but current pattern is acceptable
- `LogAuthEvent` is registered as an event subscriber (not via EventServiceProvider) — done in `AppServiceProvider::boot()` via `Event::subscribe()`
- Jobs are all dispatched via `dispatch()` helper or `LogActivity::dispatch()` — queueable design

### 2.10 Scheduled Commands (14)

| Command | Scheduled? | Purpose |
|---------|-----------|---------|
| `AssignRole.php` | CLI | Assign roles to users |
| `BackfillWorkspaceId.php` | CLI/one-off | Migrate legacy data |
| `CheckBudgetAlerts.php` | Likely cron | Budget threshold alerts |
| `CheckGoalProgress.php` | Likely cron | Goal progress notifications |
| `CheckHealth.php` | Likely cron | Application health check |
| `CheckNoestDeliveries.php` | Likely cron | Check delivery gateway status |
| `ExpireSubscriptions.php` | Cron | Mark expired subscriptions |
| `ListRoles.php` | CLI | List all roles |
| `MigrateWorkspaceRoles.php` | CLI/one-off | Migrate role assignments |
| `ProcessRecurringTransactions.php` | Cron | Process recurring entries |
| `RemindExpiringSubscriptions.php` | Cron | Send expiry warnings |
| `SendDebtReminders.php` | Cron | Debt payment reminders |
| `SendZakatReminders.php` | Cron | Zakat due reminders |
| `ValidateSoftDeletes.php` | CLI/one-off | Validate soft-delete integrity |

**Observations:**
- 6 cron-eligible commands for subscription lifecycle, reminders, recurring transactions
- Kernel schedule not audited (assumes schedule registration exists)

### 2.11 Service Providers (8)

| Provider | Purpose |
|----------|---------|
| `AppServiceProvider` | Vite asset config, nonce, CSP, event registrations, Sanctum customization, Bootstrap 5 pagination |
| `BindingServiceProvider` | Interface-to-implementation bindings (repositories, services) |
| `GatewayServiceProvider` | Singleton registrations (gateways, webhook validators, support services) |
| `ModelEventServiceProvider` | Dynamic model observers for all 8 financial models + DashboardCacheObserver |
| `PolicyServiceProvider` | Policy registrations, Gate::before super admin bypass, custom gates |
| `RateLimiterServiceProvider` | API rate limiting configuration |
| `SettingsServiceProvider` | Runtime config override from database (app name, locale, registration, rate limits) |
| `VoltServiceProvider` | Volt component discovery |

**Observations:**
- `AppServiceProvider::boot()` registers events directly — these would be better in a dedicated `EventServiceProvider`
- `SettingsServiceProvider::boot()` catches all `Throwable` and logs warnings, gracefully degrades if settings table doesn't exist yet
- `GatewayServiceProvider` registers `ActivityLogServiceInterface` as singleton — this conflicts with `BindingServiceProvider` which also binds it. Duplicate binding, with `GatewayServiceProvider` taking precedence (singleton over regular bind)
- No `EventServiceProvider` class exists (events are registered in `AppServiceProvider`)

### 2.12 Enums (12)

| Enum | Values |
|------|--------|
| `AssetType` | Enum of asset types |
| `BudgetPeriod` | `weekly`, `monthly`, `yearly`, `custom` |
| `Currency` | `DZD`, `USD`, `EUR`, etc. |
| `DebtStatus` | `active`, `paid`, `defaulted` |
| `DebtType` | `lend`, `borrow` |
| `ExpenseCategoryType` | Category types |
| `GoalStatus` | `active`, `completed`, `abandoned` |
| `GoalType` | `save`, `spend`, `earn` |
| `IncomeCategoryType` | Category types |
| `PaymentStatus` | `checkout_pending`, `checkout_paid`, `checkout_failed`, `checkout_canceled`, `refunded` |
| `PlanPeriod` | `monthly`, `yearly` |
| `SubscriptionStatus` | `active`, `expired`, `canceled`, `suspended`, `trialing` |

**Observations:**
- All enums extend `BackedEnum` (string-backed)
- `PaymentStatus` includes `isTerminal()` method for state machine logic
- Some enums use raw strings elsewhere in the codebase rather than enum references (e.g., `'monthly'` string literal in `SubscriptionProrationService.php:33` instead of `PlanPeriod::Monthly->value`)

---

## 3. API Layer

**Route files:**
- `routes/api.php` — Standard API routes
- `routes/tenant.php` (353 lines) — Tenant-scoped web routes (dashboard, finances, settings, etc.)
- `routes/super-admin.php` (234 lines) — Super admin panel routes
- `routes/web.php` — Public routes, auth routes, landing pages
- `routes/channels.php` — Broadcasting channels

**Route organization:**
- `tenant.php`: Organized by feature group — dashboard, expenses, incomes, budgets, debts, assets, goals, transactions, reports, subscriptions, workspace settings, profile
- `super-admin.php`: System management — users, workspaces, subscriptions, plans, payment methods, roles, permissions, system settings, announcements
- Both use middleware groups with auth, workspace, subscription checks embedded

**Observations:**
- Route files are well-named and organized
- No route model binding conflicts observed
- API routes use Sanctum token authentication

---

## 4. Frontend Architecture

### 4.1 Livewire-Volt Components

**Component structure:**
- `resources/views/livewire/` — Livewire/Volt functional components (`.blade.php`)
- Most components use Volt's single-file functional API (`<?php use function Livewire\Volt\{...};`)

**Component coverage by feature:**

| Feature | Components |
|---------|-----------|
| Dashboard | `dashboard/stats`, `dashboard/charts`, `dashboard/recent-transactions` |
| Expenses | `expenses/index`, `expenses/create`, `expenses/edit`, `expenses/delete`, `expenses/recurring` |
| Incomes | `incomes/index`, `incomes/create`, `incomes/edit`, `incomes/delete` |
| Budgets | `budgets/index`, `budgets/create`, `budgets/edit` |
| Debts | `debts/index`, `debts/create`, `debts/edit`, `debts/payment` |
| Assets | `assets/index`, `assets/create`, `assets/edit` |
| Goals | `goals/index`, `goals/create`, `goals/edit`, `goals/progress` |
| Zakat | `zakat/index`, `zakat/calculate`, `zakat/history`, `zakat/records` |
| Reports | `reports/index`, `reports/expense`, `reports/income`, `reports/summary` |
| Transactions | `transactions/index`, `transactions/import`, `transactions/recurring` |
| Subscriptions | `subscriptions/plans`, `subscriptions/manage`, `subscriptions/payment`, `subscriptions/history` |
| Settings | `settings/index`, `settings/profile`, `settings/workspace`, `settings/categories`, `settings/currency`, `settings/notifications` |
| Auth | `auth/login`, `auth/register`, `auth/password-reset`, `auth/forgot-password`, `auth/verify-email`, `auth/two-factor` |
| Onboarding | `onboarding/welcome`, `onboarding/workspace`, `onboarding/categories`, `onboarding/complete` |
| Admin | `admin/index`, `admin/users`, `admin/workspaces`, `admin/subscriptions`, `admin/plans`, `admin/payments`, `admin/roles`, `admin/permissions`, `admin/settings`, `admin/announcements` |

**Observations:**
- Components use Volt's functional API and Alpine.js for interactivity
- Loading states: many components include `loading` states (e.g., `wire:loading` classes, loading skeletons)
- Empty states: most list components have `@if(count($items) === 0)` fallback with "no data" messages
- Error handling: some components have `onError()` callbacks; others rely on Laravel's validation error bag

### 4.2 Blade Layouts & Views

**Layout structure:**
- `resources/views/layouts/` — Main layouts
  - `app.blade.php` — Authenticated user layout (sidebar, navbar)
  - `admin.blade.php` — Super admin layout
  - `guest.blade.php` — Unauthenticated layout (landing, auth)
  - `onboarding.blade.php` — Onboarding wizard layout

**View organization:**
- `auth/` — Authentication views (login, register, reset, 2FA, verify)
- `components/` — Reusable Blade components
- `partials/` — Partial includes (sidebar, navbar, footer, modals)
- `vendor/mail/` — Mail template overrides (HTML + text)
- Landing page views

**Observations:**
- Clean Blade component structure
- All authenticated views extend from `layouts.app` which includes sidebar + navbar

### 4.3 Vite & Asset Pipeline

**Configuration:**
- `vite.config.js` standard Laravel configuration
- `AppServiceProvider::boot()` configures:
  - CSP nonce via `$vite->useCspNonce()`
  - CSS preload disabled
  - Asset paths cleaned

**CSS/JS:**
- Tailwind CSS with RTL support
- Custom CSS for RTL overrides
- Alpine.js for interactivity

### 4.4 RTL / Arabic Support

**Implementation:**
- `config/app.php` sets `'locale' => 'ar'`
- `SetLocale` middleware reads user preference and sets `app()->setLocale()`
- Tailwind CSS configured with RTL variant
- Custom RTL CSS at `resources/css/rtl.css`
- RTL detection: `resources/views/layouts/app.blade.php` checks `app()->getLocale() === 'ar'`
- HTML tag: `<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">`
- Sidebar flips direction in RTL mode
- Arabic translations at `lang/ar/` — comprehensive coverage

**Observations:**
- RTL support is comprehensive and handled at the layout level
- Tailwind RTL variants (`rtl:`, `ltr:`) likely used but not exhaustively verified
- Some `mr-*`/`ml-*` utility classes in components may hardcode LTR spacing

### 4.5 Alpine.js Usage

**Common patterns observed:**
- Dropdowns and modals (x-data, x-show, x-transition)
- Form interactions (x-model for real-time updates)
- Dynamic UI toggles (x-on:click, x-bind:class)
- Loading states with wire:loading integrated
- Toast/notification system

**Observations:**
- Alpine and Livewire integration is well-done (Livewire properties + Alpine x-data co-exist)
- No Alpine stores (no `Alpine.store()` usage observed)
- Most interactivity handled by Livewire; Alpine reserved for UI polish

### 4.6 Auth Views

**Available views:**
- `auth/login` — Email/password login with "remember me"
- `auth/register` — Registration with workspace creation
- `auth/password-reset` — Password reset form
- `auth/forgot-password` — Email-based reset request
- `auth/verify-email` — Email verification prompt
- `auth/two-factor` — 2FA challenge form

**Observations:**
- Auth views are Volt components (not plain Blade)
- Registration includes workspace name/description fields (SaaS context)
- 2FA is supported at login (challenge code entry)
- Email verification is present but may not be enforced everywhere

### 4.7 Admin / Super-Admin Panel

**Route prefix:** `/admin` or `/super-admin`  
**Layout:** `admin.blade.php` (separate from user layout)

**Admin components:**
- User management (list, create, edit, roles, permissions)
- Workspace management (list, view, suspend, delete)
- Subscription management (all plans, all subscriptions)
- Plan management (CRUD + feature/pricing config)
- Payment management (all payments, refunds)
- Role & permission management
- System settings (app-wide configuration)
- Announcements

**Observations:**
- Admin panel uses Volt components, consistent with user-facing UI
- Super admin bypasses workspace scoping (`Gate::before` returning true)
- Admin routes are in `routes/super-admin.php` (234 lines)

---

## 5. Subscription & Payments Module

### 5.1 Models

| Model | Table | Key Fields |
|-------|-------|------------|
| `SubscriptionPlan` | `subscription_plans` | name, slug, is_free, is_active, is_public, yearly_discount_percent, sort_order |
| `PlanFeature` | `plan_features` | slug, name, description, type |
| `PlanPrice` | `plan_prices` | plan_id, currency, period (monthly/yearly), price, is_active |
| `Subscription` | `subscriptions` | workspace_id, plan_id, status, starts_at, ends_at, trial_ends_at, plan_price_amount, metadata |
| `Payment` | `payments` | uuid, workspace_id, subscription_id, method, amount, currency, status (PaymentStatus enum), gateway references |
| `PaymentMethod` | `payment_methods` | key, name, is_active, credentials (json), supported_currencies |
| `Coupon` | `coupons` | code, type, value, usage_limit, expires_at |
| `PaymentVerification` | `payment_verifications` | payment_id, status, verified_at, verified_by |

### 5.2 Services

**SubscriptionService** (`app/Services/SubscriptionService.php`, 468 lines):
- Handles: create, activate, cancel, suspend, resume, upgrade, downgrade, renew
- Plan change validation (feature comparison, price calculation)
- Trial management
- Integration with PaymentService for billing

**SubscriptionPaymentService** (`app/Services/SubscriptionPaymentService.php`):
- Creates payments for subscription orders
- Links checkout sessions to subscriptions

**SubscriptionProrationService** (`app/Services/SubscriptionProrationService.php`, 81 lines):
- Proportional proration for mid-cycle plan changes
- Uses 30-day month / 365-day year convention
- Returns upgrade/downgrade flags + amounts

**PaymentService** (`app/Services/PaymentService.php`, 291 lines):
- Payment creation and processing
- Gateway routing via GatewayManager
- Transaction logging

**PaymentTransitionValidator** (`app/Services/PaymentTransitionValidator.php`, 60 lines):
- Enforces valid state transitions for PaymentStatus enum

### 5.3 Gateway Integration

**GatewayManager** (`app/Services/Payments/GatewayManager.php`):
- Registry pattern: `register(name, gateway)` / `driver(name)`
- 10 registered gateways (see section 2.7)
- Each gateway implements a common interface (not audited)

**Chargily Integration (Algerian market focus):**
- `ChargilyGateway` — Checkout creation
- `ChargilyCheckoutService` — API client for Chargily Pay
- `ChargilySignatureValidator` — Webhook signature verification
- `ChargilyWebhookService` — Webhook event handling

**Noest Integration (Algerian delivery payments):**
- `NoestGateway` — Delivery-based payment gateway
- `NoestService` — Noest API integration
- `NoestSignatureValidator` — Webhook verification

**Other gateways:** Stripe, PayPal, Wise, Wise Manual, Payoneer, RedotPay, BaridiMob, Cash, Delivery

### 5.4 Webhook System

**Webhook Controller:** `app/Http/Controllers/PaymentWebhookController.php` (258 lines)
- Single endpoint that routes to gateway-specific handler
- Signature verification via `WebhookSignatureManager`
- Multi-gateway support through strategy pattern

**WebhookSignatureManager** (`app/Services/Webhooks/WebhookSignatureManager.php`):
- Registry pattern for signature validators
- Validators registered in `GatewayServiceProvider.php:46-53`

### 5.5 Proration

**Proration formula** (from `SubscriptionProrationService.php:27-80`):
```
dailyRateCurrent = currentPrice / totalDays
dailyRateTarget = targetPrice / totalDays
remainingValue = dailyRateCurrent * remainingDays
costAtNewRate = dailyRateTarget * remainingDays
amountDue = costAtNewRate - remainingValue
```

**Observation:** Uses `$subscription->plan_price_amount` — may be null for legacy subscriptions. Falls back to `$subscription->plan->{period}_price`. Monthly/yearly strings hardcoded rather than using enum.

---

## 6. Zakat Module

**Model:** `ZakatRecord` — BelongsToWorkspace, tracks zakat calculations

**Components:** `zakat/index`, `zakat/calculate`, `zakat/history`, `zakat/records`

**Service:** Not separately identified; likely embedded in Livewire components or a Zakat-specific service

**Scheduled Command:** `SendZakatReminders.php` — Sends reminders when zakat is due

**Observations:**
- Zakat calculation likely uses nisab threshold from settings or database
- Separate ZakatRepository interface exists (`ZakatRepositoryInterface`)
- ZakatRecord model has its own policy (`ZakatRecordPolicy`)

---

## 7. Notifications & Mailing

**Notifications (3):**
- `SubscriptionExpiryWarning.php` — Expiry/reminder emails
- `VerifyEmail.php` — Email verification
- `WorkspaceInvitation.php` — Workspace invitation emails

**Mailables:** Not separately inventoried (may exist in `app/Mail/`)

**Mail Templates:**
- Custom Markdown mail theme at `resources/views/vendor/mail/` (HTML + text)
- All standard mail components overridden (button, panel, table, subcopy, header, footer, message, layout)

**Mail Configuration:**
- Default driver: `log` (dev)
- From address: configured via `.env`
- Markdown theme: `default` with custom paths

**Observations:**
- Only 3 notification classes for a SaaS app of this size — minimal but covers core needs
- No push notifications (no Firebase/Apns setup observed)
- Mail templates are fully customized, suggesting branded emails

---

## 8. Translations

**Structure:**
```
lang/
├── ar/          — Arabic translations
│   ├── auth.php
│   ├── messages.php (primary — likely largest file)
│   ├── pagination.php
│   ├── passwo… (password resets)
│   └── validation.php
├── en/          — English translations (same structure)
└── vendor/      — Vendor package overrides
```

**Observations:**
- Arabic is the primary locale (`config/app.php` sets `'locale' => 'ar'`)
- English is fallback
- `messages.php` is the main application translation file
- `__("messages.{$label}_created")` pattern used extensively in `ModelEventServiceProvider`
- No French translations observed — notable for an Algerian-market app (French is widely used in Algeria alongside Arabic)

---

## 9. Test Suite

**Summary: 643 passing, 11 skipped, 0 failures** (as of audit date)

**Test directory structure:**
```
tests/
├── Feature/
│   ├── Api/                — API endpoint tests
│   ├── Auth/               — Authentication tests
│   ├── Budget/             — Budget CRUD tests
│   ├── Debt/               — Debt management tests
│   ├── Expense/            — Expense CRUD tests
│   ├── FinancialGoal/      — Financial goal tests
│   ├── Income/             — Income CRUD tests
│   ├── Livewire/           — Livewire component tests
│   ├── MultiTenancy/       — Workspace isolation tests
│   ├── Payment/            — Payment processing tests
│   ├── RBAC/               — Role/permission tests
│   ├── Report/             — Report generation tests
│   ├── Subscription/       — Subscription lifecycle tests
│   ├── Zakat/              — Zakat calculation tests
│   └── ...                 — Other feature tests
├── Unit/
│   ├── Enums/              — Enum value tests
│   ├── Models/             — Model relationship tests
│   ├── Services/           — Service unit tests
│   └── ...                 — Other unit tests
├── TestCase.php            — Base test case
└── Pest.php                — Pest configuration
```

**Test framework:** Pest PHP

**Test configuration:**
- No `.env.testing` file — tests use `config:clear` to avoid cached MySQL settings
- Database: SQLite `:memory:` (configured in `phpunit.xml` or dynamically)
- `TestCase::setUp()`:
  - `$this->withoutMiddleware(PreventRequestForgery::class)` — disables CSRF for all tests
  - Registers missing `assertSeeLivewire` macro if not available

**Observations:**
- Comprehensive test coverage across all modules
- `assertSeeLivewire` macro manually registered in `TestCase` — suggests an edge case or specific version compatibility
- No `assertSeeVolt` equivalent observed — Volt components may rely on `assertSeeLivewire`
- CSRF disabled globally for tests — this is standard but could miss CSRF-related bugs
- No `.env.testing` means test DB config comes from `phpunit.xml` or defaults

---

## 10. Observations Summary

### Architecture Strengths
1. **Well-separated concerns:** Models, services, repositories, controllers, policies, middleware — all properly organized
2. **Comprehensive multi-tenancy:** WorkspaceScoped, BelongsToWorkspace trait, middleware chain
3. **Rich gateway integration:** 10 payment gateways, proper webhook signature verification
4. **State-machine validation:** PaymentTransitionValidator ensures valid status transitions
5. **CSP + nonce support:** Security headers and Vite nonce integration
6. **RTL-first design:** Arabic/RTL support baked into every layout
7. **Dynamic runtime config:** SettingsServiceProvider overrides config from database gracefully
8. **Full test suite:** 643 passing tests covering all modules

### Potential Issues / Risks

| # | Severity | Area | Observation | Evidence |
|---|----------|------|-------------|----------|
| 1 | Medium | Events | Events registered in `AppServiceProvider` instead of a dedicated `EventServiceProvider` | `AppServiceProvider.php:36-66` |
| 2 | Low | DI | `ActivityLogServiceInterface` bound twice — once in `BindingServiceProvider` and again as singleton in `GatewayServiceProvider` | `BindingServiceProvider.php:47` vs `GatewayServiceProvider.php:38` |
| 3 | Low | N+1 Risk | `SubscriptionPlan::getYearlyPriceAttribute()` queries `activePrices()` relation without eager loading | `SubscriptionPlan.php:42-43` |
| 4 | Low | String Literals | `PlanPeriod::Monthly->value` not used in `SubscriptionProrationService`; raw strings `'monthly'`/`'yearly'` used instead | `SubscriptionProrationService.php:33,48,50` |
| 5 | Info | Middleware Naming | Inconsistent prefix convention (`Has*` vs `Check*`) across RBAC middleware | `HasPlatformRole.php` vs `CheckPermission.php` |
| 6 | Info | No `.env.example` | Repository lacks `.env.example` file | Project root |
| 7 | Info | No `.env.testing` | Test environment relies on `phpunit.xml` defaults + `config:clear` | Not present |
| 8 | Low | Tenant Isolation | Workspace scope is at query level (soft isolation), not database level — potential data leak if scope is forgotten | `WorkspaceScope.php:18-32` |
| 9 | Info | No French i18n | French widely used in Algeria but only Arabic and English translations exist | `lang/` directory |
| 10 | Low | Mail Notifications | Only 3 notification classes — may miss events like payment failures, goal completion, etc. | `app/Notifications/` |
| 11 | Info | No Push Notifications | No Firebase/Apns configuration observed | Config directory |
| 12 | Low | Late Static Binding | `Payment::withoutWorkspace()` in `Payment::booted()` uses static context correctly, but pattern could be missed in other models | `Payment.php:67` |

### Recommendations Summary
1. Move event registrations from `AppServiceProvider` to a dedicated `EventServiceProvider`
2. Consolidate `ActivityLogServiceInterface` binding to one provider
3. Add `->with('activePrices')` eager load hint to `SubscriptionPlan` queries that access `yearly_price`
4. Replace raw string `'monthly'`/`'yearly'` with `PlanPeriod::Monthly->value` / `PlanPeriod::Yearly->value`
5. Add `.env.example` and `.env.testing.example` to repository
6. Consider adding French translations (`lang/fr/`) for Algerian market users who prefer French
7. Review notification coverage — consider adding notifications for payment failures, goal completions, invitation events
8. Create a command or CI check to verify all workspace-scoped models correctly use `BelongsToWorkspace` trait
9. Consider normalizing middleware naming convention (`Has*` prefix for all RBAC middleware)
10. Verify `assertSeeVolt` or ensure `assertSeeLivewire` works with Volt components in tests

---

*End of Audit Report. This is a diagnostic document only — no code modifications have been made.*
