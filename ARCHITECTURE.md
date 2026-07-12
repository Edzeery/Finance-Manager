# Architecture — Finance Manager (Multi-Tenant SaaS)

> آخر تحديث: 2026-07-11

---

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Framework** | Laravel | ^13.8 |
| **Language** | PHP | ^8.3 |
| **Database** | MySQL (prod) / SQLite (dev/test) | 8.0+ |
| **Frontend** | Bootstrap + Livewire + Alpine.js + Chart.js + Vite | 5.3 / 3.6 / - / 4.5 / 8 |
| **Auth** | Laravel Auth + Sanctum + Google2FA | 4.3 |
| **Payments** | Chargily Pay SDK + custom gateways (13 total) | 2.0 |
| **Backup** | Spatie Laravel Backup | 10.3 |
| **QR Code** | Bacon QR Code | 3.1 |
| **Testing** | PHPUnit (via Pest) | 11.5.55 |
| **Dev Tools** | Debugbar, Laravel Pint, Laravel Pail, Collision | — |
| **Excel** | Maatwebsite/Laravel-Excel (dev only) | 3.1 |

## Architecture Overview

**Finance Manager** is a Laravel 13 monolith with Livewire 3 + Volt for interactivity. Multi-tenant finance tracking SaaS with subscription billing, workspace isolation, RBAC, payment gateway integration, and a REST API.

Monolithic web application with service layer, repository pattern, and middleware-driven authorization. No microservices, no message bus, no CQRS/Event Sourcing.

### Domain Architecture

```
┌──────────────────────────────────────────────────────────┐
│                   FINANCE MANAGER SAAS                     │
├──────────────────────────────────────────────────────────┤
│  ┌────────────────────────────┐  ┌─────────────────────┐  │
│  │     PLATFORM DOMAIN        │  │    TENANT DOMAIN      │  │
│  │    (SuperAdmin Panel)      │  │  (Workspace-scoped)   │  │
│  │                            │  │                       │  │
│  │ users → platform roles     │  │ income, expense, debt │  │
│  │ roles → permissions        │  │ asset, budget, goal   │  │
│  │ workspaces (read)          │  │ zakat, categories     │  │
│  │ subscriptions (manage)     │  │ reports, search       │  │
│  │ payments, invoices         │  │ workspace settings    │  │
│  │ coupons, tax rates         │  │ members, notifications│  │
│  │ backup, settings           │  └─────────────────────┘  │
│  └────────────────────────────┘                           │
│  ┌──────────────────────────────────────────────────────┐ │
│  │              BILLING / SUBSCRIPTION                   │ │
│  │  Plan → Subscription → Invoice → Payment → Verify    │ │
│  │  Coupon (discount)  TaxRate (per-country)  Gateways  │ │
│  └──────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────┘
```

### Layers

| Layer | Location | Role |
|-------|----------|------|
| Presentation | `resources/views/`, `routes/` | Blade + Volt components, route definitions |
| HTTP | `app/Http/Controllers/`, `app/Http/Middleware/` | Request handling, authz, validation |
| Application | `app/Services/`, `app/Jobs/` | Business logic orchestration |
| Domain | `app/Models/`, `app/Enums/`, `app/DTOs/` | Core entities and value objects |
| Persistence | `app/Repositories/`, `database/migrations/` | Data access and schema |
| Infrastructure | `app/Providers/`, `config/`, `bootstrap/` | DI binding, configuration |

### Multi-Tenant Isolation

1. **`WorkspaceScope`** — Global scope on all financial models auto-filters by `workspace_id`
2. **`BelongsToWorkspace` trait** — Auto-sets `workspace_id` on create
3. **`SetWorkspace` middleware** — Resolves workspace from session/header into `config('app.current_workspace')`
4. **`EnsureWorkspaceSelected` middleware** — Redirects if no active workspace
5. **Category sharing** — Categories can be global (`workspace_id = null`) or workspace-specific
6. **Super admin bypass** — Scope returns early when no workspace selected

## Middleware Stack

### Global Stack
```
EncryptCookies → StartSession → ShareErrorsFromSession → VerifyCsrfToken
→ SubstituteBindings → PreventBackHistory → SetWorkspaceLocale
→ SetLocale → SetTheme → SecurityHeaders → SetWorkspace → EnsureOnboardingCompleted
```

### Route Middleware
| Alias | Class | Purpose |
|-------|-------|---------|
| `auth` | Authenticate | Session auth |
| `verified` | EnsureEmailIsVerified | Email verification |
| `super-admin` | SuperAdmin | `hasRole('super_admin')` |
| `force-2fa` | ForceTwoFactor | 2FA for admin roles |
| `onboarding` | EnsureOnboardingCompleted | Block if pending |
| `workspace` | ApiWorkspace | API workspace resolution |
| `set.workspace` | SetWorkspace | Session workspace ID |
| `permission` | CheckPermission | Generic permission |
| `platform.role` | HasPlatformRole | Platform role check |
| `platform.permission` | HasPlatformPermission | Platform perm check |
| `workspace.role` | HasWorkspaceRole | Workspace role check |
| `workspace.permission` | HasWorkspacePermission | Workspace perm check |
| `api.ability` | CheckApiAbility | Sanctum token abilities |
| `throttle` | ThrottleRequests | Rate limiting |

## Route Structure

| File | Prefix | Middleware | Routes |
|------|--------|------------|--------|
| `web.php` | `/` | `web` | Landing, locale, theme, profile, search, reports, notifications |
| `auth.php` | — | `web` | Login, register, password, 2FA |
| `tenant.php` | — | `web, auth, verified, onboarding` | All workspace CRUD, payment resume/retry/result/checkout |
| `super-admin.php` | `/super-admin` | `web, auth, super-admin, force-2fa` | Dashboard, users, roles, plans, subs, payments, invoices, coupons |
| `api.php` | `/api/v1` | `api, auth:sanctum, abilities` | 14 resource controllers + auth |
| `webhooks.php` | `/payment/webhook` | `throttle:webhook` | Chargily, PayPal, Stripe, Wise, Payoneer, Rasmal, Noest |
| `console.php` | — | — | Command registration, health check, onboarding cleanup |

## Service Layer

| Service | Responsibility |
|---------|---------------|
| `DashboardService` | Cached KPI data |
| `ChartDataService` | Cached chart data |
| `SearchService` | Cross-entity fulltext search (6 types) |
| `ReportService` | Monthly/yearly financial reports |
| `ActivityLogService` | Sensitive data filtering |
| `NotificationService` | 8 notification types |
| `OnboardingService` | Plan selection + payment |
| `SubscriptionService` | Plan/subscription changes, proration, coupon |
| `WorkspaceService` | Workspace CRUD + members |
| `WorkspaceInvitationService` | Invitation lifecycle |
| `PaymentService` | Payment creation + verification |
| `RedirectService` | Role-based post-auth routing |
| `TwoFactorAuthenticationService` | Google2FA + audit |
| `ZakatCalculationService` | Islamic alms calculation |
| `InvoiceNumberGenerator` | Auto-incrementing invoice numbers |
| `NoestService` | Noest API client |

## Repository Pattern

```
Contracts/Repositories/
├── IncomeRepositoryInterface    → IncomeRepository
├── ExpenseRepositoryInterface   → ExpenseRepository
├── DebtRepositoryInterface      → DebtRepository
├── AssetRepositoryInterface     → AssetRepository
├── BudgetRepositoryInterface    → BudgetRepository
├── GoalRepositoryInterface      → GoalRepository
├── ZakatRepositoryInterface     → ZakatRepository
├── CrudRepositoryInterface      → CrudRepository (generic)
└── BaseRepositoryInterface      → BaseRepository
```

## Key Design Patterns

| Pattern | Usage | Location |
|---------|-------|----------|
| **Strategy** | Payment gateways | 13 implementations |
| **Repository** | Data access abstraction | 9 repositories |
| **Observer** | Cache + Activity logging | `DashboardCacheObserver` |
| **Global Scope** | Multi-tenant isolation | `WorkspaceScope` on 14+ models |
| **DTO** | Type-safe data transfer | `KpiData`, `ChartData`, `SearchResult` |
| **Singleton** | Gateway registry + services | `GatewayManager` |
| **Queue** | Async logging | `LogActivity` job |

## SOLID Compliance

| Principle | Assessment |
|-----------|-----------|
| **S**ingle Responsibility | Mostly violated. `SubscriptionService` handles proration, change plan, validation, cancellation |
| **O**pen/Closed | Gateway/Validator registration patterns are good |
| **L**iskov Substitution | `PaymentGateway` interface properly followed |
| **I**nterface Segregation | Repository interfaces specific per domain |
| **D**ependency Inversion | Repositories use interfaces via DI. Services use constructor injection. |

## Coupling & Cohesion

- **High cohesion** within payment services (`app/Services/Payments/Chargily/`)
- **Low cohesion** in `SubscriptionService` (4+ responsibilities)
- **Tight coupling**: controllers call `Model::query()` directly instead of repositories
- API controllers use resources (good separation)

## Folder Organization

```
app/
├── Console/Commands/       # 14 artisan commands
├── Contracts/              # 16 interfaces
├── DTOs/                   # 3 DTOs
├── Enums/                  # 7 enums
├── Events/                 # 7 events
├── Exceptions/             # 4 custom exceptions
├── Exports/                # 8 Laravel Excel exports
├── Http/
│   ├── Controllers/        # 66 controllers + 2 Base
│   ├── Middleware/         # 17 middleware
│   ├── Requests/           # ~34 form requests
│   └── Resources/          # 16 API resources
├── Imports/                # ExpenseImport + IncomeImport
├── Jobs/                   # 3 jobs
├── Listeners/              # 4 listeners
├── Livewire/               # Volt components in resources/
├── Mail/                   # 4 mailables
├── Models/                 # 34 models + 1 trait + 1 scope
├── Notifications/          # 2 notifications
├── Observers/              # DashboardCacheObserver
├── Policies/               # 11 policies
├── Providers/              # 8 service providers
├── Repositories/           # 9 repositories
├── Services/               # 21 core + 13 gateways + 7 webhook validators
├── Support/                # DatabaseHelper
└── View/Components/        # 4 layout components
```

## System Flow

### User Registration
```
Register → Create User → Provision Personal Workspace
→ Assign workspace_admin role → Send WelcomeEmail (queued)
→ Redirect to email verification → Login page
→ Email verified → OnboardingMiddleware checks pending_plan
→ Redirect to /onboarding/plan
```

### Daily Scheduled Tasks
| Command | Frequency | Purpose |
|---------|-----------|---------|
| `finance:process-recurring` | daily | Create next recurring income/expense |
| `finance:check-budget-alerts` | daily | Budget limit notifications |
| `finance:check-goal-progress` | daily | Milestone + deadline checks |
| `finance:send-debt-reminders` | daily | Overdue/due debt notifications |
| `finance:send-zakat-reminders` | weekly | Zakat reminders |
| `subscriptions:expire` | daily | Expire past-due subscriptions |
| `subscriptions:remind-expiry` | daily | Remind nearing expiry |
| `subscriptions:remind-expiry` | daily | Remind expiring subscriptions |
| `noest:check-deliveries` | hourly | Poll Noest delivery status |
| `backup:run --only-db` | daily | Database backup |
| `backup:clean` | daily | Backup rotation cleanup |

## Localization

| Locale | Code | Direction | Font |
|--------|------|-----------|------|
| العربية | `ar` | RTL | Tajawal |
| Français | `fr` | LTR | Inter |
| English | `en` | LTR | Inter |

31 domain files each (190+ keys), organized by domain: income, expense, debt, asset, budget, goal, zakat, report, settings, onboarding, payment, notification, etc.

## Queue & Jobs

| Job | Queue | Description | Trigger |
|-----|-------|-------------|---------|
| `LogActivity` | `default` | Async activity logging | Model events |
| `ActivateSubscription` | `default` | Activate subscription | Payment completed |
| `SendSubscriptionExpiryNotification` | `default` | Expiry notifications | Cron |

## Weaknesses

1. **Repository bypass**: Many controllers use `Model::query()` directly
2. **God services**: `SubscriptionService` (450+ lines) handles too many concerns
3. **Mixed concerns in middleware**: `EnsureOnboardingCompleted` contains Livewire route resolution logic
4. **No application events**: Only 3 domain events. Many state changes occur without events.
5. **No strict types**: Many files don't declare `declare(strict_types=1)`
6. **Empty `register()` in AppServiceProvider**: Doesn't register any services
7. **Recurring expenses/incomes**: Tracked via string field only — no scheduler for auto-creation
8. **2FA not enforced**: Fields exist but no middleware requires it for non-admin roles
9. **No OpenAPI/Swagger docs**: API has no machine-readable documentation
10. **Rate limiting limited**: Only login is throttled; most API/workspace routes lack limits
11. **No idle session timeout**: No middleware for automatic logout on inactivity
12. **No file virus scanning**: File uploads lack antivirus integration

## Key Files Reference

### Routes
| File | Purpose |
|------|---------|
| `routes/web.php` | ~250 lines — landing, locale, theme, profile, search, reports, notifications |
| `routes/auth.php` | Login, register, password reset, 2FA, email verification |
| `routes/tenant.php` | All workspace-scoped CRUD + payment resume/return/retry/checkout (auth + verified + onboarding middleware) |
| `routes/super-admin.php` | Super admin dashboard, users, roles, plans, subs, payments |
| `routes/api.php` | ~200 lines — 14 resource controllers + auth (`/api/v1`) |
| `routes/webhooks.php` | Chargily, PayPal, Stripe, Wise, Payoneer, Rasmal, Noest (`throttle:webhook`) |
| `routes/console.php` | Command registration |

### Key Controllers
| Area | Files |
|------|-------|
| Auth | Fortify-backed (2 controllers in `Auth/`: VerifyEmail, Logout) |
| Web CRUD | ExpenseController, IncomeController, DebtController, GoalController, AssetController, ZakatRecordController |
| Payments | `PaymentController`, `PaymentWebhookController`, `CheckoutController` |
| Super Admin | `app/Http/Controllers/SuperAdmin/` — User, Plan, Feature, Permission, Settings, Payment, Role |
| API | `app/Http/Controllers/Api/` — 14 resource controllers + Auth, Dashboard, User |
| Invitations | `WorkspaceInvitationController` — accept, decline, cancel, resend |

### Key Models
| Model | File |
|-------|------|
| User | HasApiTokens, HasFactory, Notifiable, 2FA |
| Workspace | Multi-tenant isolation pivot |
| Invitation | Token-based workspace invite with expiry |
| Subscription | Custom billing model (no Cashier) |
| PaymentMethod | Gateway configuration model |
| Expense, Income, Debt, Goal, Asset, ZakatRecord, ZakatAsset, Budget | Finance domain models |

### Key Services
`SubscriptionService` (~360 lines), `PaymentService`, `WorkspaceInvitationService`, `OnboardingService`, `WorkspaceService`, `DashboardService`, `SearchService`, `ReportService`, `ActivityLogService`, `NotificationService`, `GatewayManager`, `NoestService`

### Middleware (17 total)
`SecurityHeaders`, `SuperAdmin`, `CheckPermission`, `CheckActiveSubscription`, `CheckSubscriptionStatus`, `EnsureOnboardingCompleted`, `SetLocale`, `SetTheme`, `SetWorkspace`, `ForceTwoFactor`, `CheckApiAbility`, `CheckApiSubscription`, `ApiWorkspace`, `HasPlatformRole`, `HasPlatformPermission`, `HasWorkspaceRole`, `HasWorkspacePermission`

### Config
`config/payment.php` (gateway keys/URLs), `config/fortify.php` (auth features), `config/api-abilities.php` (Sanctum token abilities), `config/invitation.php` (expiry/rate limits), `config/cors.php`, `config/session.php`

### Providers (8)
`GatewayServiceProvider`, `BindingServiceProvider`, `SettingsServiceProvider`, `RateLimiterServiceProvider`, `ModelEventServiceProvider`, `VoltServiceProvider`, `PolicyServiceProvider`, `AppServiceProvider`
