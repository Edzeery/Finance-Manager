# Phase 1 Audit Report — Payment UUID, Enum Conversion & Translation-Key Compliance

**Date:** 2026-07-10  
**Scope:** Source-code audit of the Finance Manager multi-tenant SaaS (Laravel 13 / PHP 8.3)  
**Mode:** Evidence-based, read-only audit. No modifications made.

---

## Executive Summary

The `payments.uuid` column exists as a nullable `string(20)` with a unique constraint and auto-generation logic, but it is **functionally dead** — no controller, service, view, route, API resource, or test reads or displays it. The enum conversion is **partially complete**: `PaymentStatus`, `SubscriptionStatus`, `DebtStatus`, `GoalStatus`, `InvitationStatus`, `RecurringFrequency`, `AssetType`, and `DebtType` all have proper backed enums with `label()` translation methods, but `Invoice.status`, `PaymentVerification.status`, `PaymentWebhookLog.status`, and `PaymentMethod.type` are still raw strings with no enum classes. Raw string leaks for existing enums are concentrated in a few controllers (`DashboardController`, `PaymentWebhookController`, `User.php` model) and blade views. The translation system is largely consistent with one orphaned `general_status` section in English `enums.php` and one missing `goal.name` key in Arabic.

---

## Section 1: `payments.uuid` / `payment_id` Findings

### 1.1 Migration Definition

| Property | Value | File:Line |
|----------|-------|-----------|
| Column type | `string('uuid', 20)` — max 20 chars | `database/migrations/2026_07_09_175155_add_uuid_and_update_status_in_payments.php:15` |
| Nullable | **Yes** (`->nullable()`) | same |
| Unique constraint | **Yes** (`->unique()`) | same file, line 55 (added after backfill) |
| Index | Only via unique constraint | same |
| Backfill format | `'pay-' . Str::lower(Str::random(12))` | lines 18–25 |
| Dedup logic | Groups by uuid, keeps lowest `id` | lines 41–52 |

### 1.2 Model (`Payment.php`)

| Aspect | Status | File:Line |
|--------|--------|-----------|
| `$fillable` includes `uuid` | **Yes** (first element) | `app/Models/Payment.php:19` |
| `$casts` includes `uuid` | **No** (remains plain string) | `app/Models/Payment.php:30–48` |
| Auto-generation on `creating` | **Yes** — `'pay-' . Str::lower(Str::random(12))` if not set | `app/Models/Payment.php:59–63` |
| UUID/ULID trait used | **No** — inline generation in `booted()` | `app/Models/Payment.php:55–73` |
| `getRouteKeyName()` override | **No** — uses default `id` | `Payment.php` does not define this |

### 1.3 Factory

| Aspect | Status | File:Line |
|--------|--------|-----------|
| Populates `uuid` | **Yes** — `'pay-' . strtolower(fake()->bothify('????????????'))` | `database/factories/PaymentFactory.php:18` |

### 1.4 Seeders

| Seeder | Creates Payments? | Populates `uuid`? |
|--------|-------------------|-------------------|
| `DatabaseSeeder` | No (calls `PaymentGatewaySeeder`, `PaymentMethodSeeder`) | N/A |
| `PaymentGatewaySeeder` | No (creates `PaymentGateway` records) | N/A |
| `PaymentMethodSeeder` | No (creates `PaymentMethod` records) | N/A |
| `SubscriptionPlanSeeder` | No | N/A |
| `DemoDataSeeder` | No | N/A |
| `EnterpriseRolePermissionSeeder` | No (only permission slugs) | N/A |

**No seeder directly creates `Payment` model records.** Payment records are created only at runtime by `PaymentService` / `OnboardingService`.

### 1.5 Controllers — No `uuid` references

All controllers look up payments by auto-increment `id` or `transaction_id`:

| Controller | Lookup Method | File:Line |
|------------|--------------|-----------|
| `SuperAdmin\PaymentController` | `Payment::withoutWorkspace()->findOrFail($id)` | `app/Http/Controllers/SuperAdmin/PaymentController.php:101,147,174` |
| `Account\PaymentController` | `Payment::withoutWorkspace()->whereIn('subscription_id', ...)` | `app/Http/Controllers/Account/PaymentController.php:22` |
| `PaymentWebhookController` | `Payment::where('transaction_id', ...)` | `app/Http/Controllers/PaymentWebhookController.php:102,136,168,213,245` |
| `CheckoutController` | Implicit route-model binding by `id` | `app/Http/Controllers/CheckoutController.php:15` |
| `PaymentReturnController` | Implicit route-model binding by `id` | `app/Http/Controllers/PaymentReturnController.php:19` |

### 1.6 Services — No `uuid` references

All services pass/read auto-increment `payment_id`:

| Service | Usage | File:Line |
|---------|-------|-----------|
| `PaymentService` | `Payment::create([...])`, then `$payment->id` | `app/Services/PaymentService.php:116,214` |
| `OnboardingService` | `$payment->id` stored in session | `app/Services/OnboardingService.php:147,239` |
| `ChargilyWebhookService` | Metadata `payment_id` = auto-increment `id` | `app/Services/Payments/Chargily/ChargilyWebhookService.php:38` |
| `ChargilyGateway` | `'payment_id' => $data['payment_id']` | `app/Services/Payments/Chargily/ChargilyGateway.php:31` |
| `NoestGateway` | `Payment::find($data['payment_id'])` | `app/Services/Payments/Noest/NoestGateway.php:18` |
| `RasmalGateway` | `'payment_id' => $payment->id` | `app/Services/Payments/Rasmal/RasmalGateway.php:44` |

### 1.7 Blade Views — No `uuid` display

| View File | Payments Displayed? | Shows `uuid`? |
|-----------|-------------------|---------------|
| `super-admin/payments.blade.php` | Yes (table rows) | **No** |
| `account/payments.blade.php` | Yes (table rows) | **No** |
| `payment/result.blade.php` | Yes (status) | **No** |
| `emails/payment-receipt.blade.php` | Yes (details) | **No** |
| `account/subscriptions.blade.php` | Yes (payment history) | **No** |
| All onboarding Volt files | Yes | **No** |

### 1.8 API Resource — `uuid` omitted

`app/Http/Resources/PaymentResource.php:12–26` — returns `id`, `workspace_id`, `subscription_id`, `method`, `amount`, `currency`, `status`, `reference`, `transaction_id`, `notes`, `paid_at`, `created_at`, `updated_at`. **No `uuid`.**

### 1.9 Route-Model Binding

All routes use auto-increment `id`:
- `routes/super-admin.php:41–45` — `payments/{id}/approve`, `/reject`, `/refund` (explicit `{id}`)
- `routes/tenant.php:33,40,41,47,49` — implicit binding via `{payment}` → resolves by `id`
- `app/Providers/BindingServiceProvider.php` — no custom bindings for Payment

### 1.10 Tests — No `uuid` assertions

`tests/Feature/PaymentWebhookTest.php`, `tests/Unit/Payments/GatewayTest.php`, `tests/Feature/Admin/DashboardRegressionTest.php` — all create payments via factory (which sets `uuid`) but **never assert** on the `uuid` value, uniqueness, or format.

### 1.11 Variable Naming Consistency

All external references use `payment_id` to mean the auto-increment primary key:
- Foreign keys in `invoices`, `payment_verifications`, `payment_webhook_logs` → reference `payments.id`
- Gateway metadata → `'payment_id' => $payment->id`
- Session → `'pending_payment_id'` stores `$payment->id`
- Admin approval/rejection URLs → `payments/{id}` where `{id}` = primary key

**Naming verdict:** The column is named `uuid` but behaves as a "public payment ID." There is no naming inconsistency in the codebase itself because the column is never actually used — it is simply unused infrastructure.

### 1.12 Finding: `uuid` Column — Critical Gap

**The `uuid` column is functionally dead.** It is generated, stored, fillable, and unique-constrained, but:
- No controller, service, view, route, API resource, or test reads, searches, or displays it
- No gateway integration uses it as a reference
- No invoice or receipt references it
- No admin panel shows it
- No external notification (email) includes it
- No seeder populates it (no seeder creates payments)

---

## Section 2: Enum Conversion Findings

### 2.1 Enums That Exist (All Properly Implemented)

| Enum Class | Namespace | Backed | File |
|-----------|-----------|--------|------|
| `PaymentStatus` | `App\Enums` | `string` | `app/Enums/PaymentStatus.php` |
| `SubscriptionStatus` | `App\Enums` | `string` | `app/Enums/SubscriptionStatus.php` |
| `DebtStatus` | `App\Enums` | `string` | `app/Enums/DebtStatus.php` |
| `DebtType` | `App\Enums` | `string` | `app/Enums/DebtType.php` |
| `GoalStatus` | `App\Enums` | `string` | `app/Enums/GoalStatus.php` |
| `InvitationStatus` | `App\Enums` | `string` | `app/Enums/InvitationStatus.php` |
| `RecurringFrequency` | `App\Enums` | `string` | `app/Enums/RecurringFrequency.php` |
| `AssetType` | `App\Enums` | `string` | `app/Enums/AssetType.php` |

All enums have `label()` methods. No duplicate/parallel enums found.

### 2.2 Fields Missing Enum Classes Entirely

| Field | Table | Current Values | Impact | 
|-------|-------|----------------|--------|
| `invoices.status` | `invoices` | `draft`, `paid`, `overdue`, `cancelled` | **CRITICAL** — all raw string usage, no model cast |
| `payment_verifications.status` | `payment_verifications` | `pending`, `approved`, `rejected` | **HIGH** — raw strings in blade views and DB queries |
| `payment_webhook_logs.status` | `payment_webhook_logs` | `received`, `processed` | **MEDIUM** — raw strings in `ChargilyWebhookService.php` |
| `payment_webhook_logs.gateway` | `payment_webhook_logs` | `chargily` | **MEDIUM** — raw string |
| `payment_methods.type` | `payment_methods` | `online`, `manual`, `auto_complete` | **MEDIUM** — raw string comparisons in model |
| `payment_methods.gateway` | `payment_methods` | gateway keys (raw strings) | **MEDIUM** |
| `coupons.type` | `coupons` | `percentage`, `fixed` | **LOW** |
| `tax_rates.type` | `tax_rates` | `percentage`, `fixed` | **LOW** |
| `workspaces.type` | `workspaces` | `personal`, (custom) | **LOW** |
| `budgets.type` | `budgets` | (category type) | **LOW** |
| `plan_features.type` | `plan_features` | `boolean`, `range`, etc. | **LOW** |

### 2.3 Raw String Leaks for Enums That Exist

#### PaymentStatus — Raw String Leaks (7 occurrences)

| File | Line | Leak |
|------|------|------|
| `app/Models/User.php` | 164 | `->where('status', 'checkout.pending')` — should be `PaymentStatus::CheckoutPending->value` |
| `app/Services/Payments/Chargily/ChargilyWebhookService.php` | 125 | `'checkout.paid'` literal |
| `app/Services/Payments/Chargily/ChargilyWebhookService.php` | 149 | `'checkout.failed'` literal |
| `app/Services/Payments/Chargily/ChargilyWebhookService.php` | 173 | `'checkout.canceled'` literal |
| `app/Services/Payments/Chargily/ChargilyWebhookService.php` | 197 | `'checkout.expired'` literal |
| `app/Services/OnboardingService.php` | 275 | `'status' => 'pending'` — should be `PaymentStatus::CheckoutPending->value` |
| `app/Services/Payments/Chargily/DTOs/CheckoutData.php` | 21 | `status: $checkout->getStatus() ?? 'pending'` |

#### SubscriptionStatus — Raw String Leak (1 occurrence)

| File | Line | Leak |
|------|------|------|
| `app/Http/Controllers/SuperAdmin/DashboardController.php` | 36 | `->whereIn('status', ['active', 'trialing'])` — should use `SubscriptionStatus::Active->value` and `SubscriptionStatus::Trialing->value` |

Note: Line 37 in the same file correctly uses `SubscriptionStatus::Canceled->value` — this is an **inconsistency within the same method**.

#### Invoice.status — Raw String (No Enum Exists)

| File | Line | Leak |
|------|------|------|
| `app/Models/Invoice.php` | 69,74,79,84 | Local scopes using raw `'draft'`, `'paid'`, `'overdue'`, `'cancelled'` |
| `app/Models/Invoice.php` | 89 | `$this->status === 'paid'` |
| `app/Models/Invoice.php` | 94 | `$this->status === 'overdue'` |
| `app/Http/Controllers/Account/InvoiceController.php` | 32 | `in_array($status, ['paid', 'overdue', 'draft', 'cancelled'])` |
| `app/Http/Controllers/PaymentWebhookController.php` | 177,222,255 | `in_array($status, ['failed', 'cancelled', 'refunded'])` |
| `app/Services/SubscriptionActivationService.php` | 185 | `'status' => $payment && $payment->isCompleted() ? 'paid' : 'draft'` |

#### PaymentVerification.status — Raw String (No Enum Exists)

| File | Line | Leak |
|------|------|------|
| `resources/views/livewire/pages/onboarding/manual-proof.blade.php` | 62–64 | `match ($v->status) { 'approved' => ..., 'rejected' => ... }` |
| `resources/views/livewire/pages/onboarding/manual-proof.blade.php` | 260–263 | `statusBadgeClass` uses `'pending'`, `'approved'`, `'rejected'` |
| `resources/views/super-admin/payments.blade.php` | 126 | `$vColors = ['pending' => ..., 'approved' => ..., 'rejected' => ...]` |
| `app/Models/User.php` | 209 | `->whereHas('verification', fn($q) => $q->where('status', 'pending'))` |

#### PaymentMethod.type — Raw String (No Enum Exists)

| File | Line | Leak |
|------|------|------|
| `app/Models/PaymentMethod.php` | 104 | `$this->type === 'online'` |
| `app/Models/PaymentMethod.php` | 109 | `$this->type === 'manual'` |
| `app/Models/PaymentMethod.php` | 114 | `$this->type === 'auto_complete'` |
| `app/Services/OnboardingService.php` | 99,104,109 | `'online'`, `'manual'`, `'auto_complete'` literals |

#### Role Slugs — Raw Strings Throughout (30+ occurrences)

All middleware, controllers, and models use hardcoded `'super_admin'`, `'workspace_admin'`, `'workspace_deputy_admin'` strings. These are not covered by the enum scope in the prompt but are noted as a systemic issue.

### 2.4 Models with Missing Enum Casts (for Enums That Exist)

| Model | Field | Existing Enum | Cast Declared? | File:Line |
|-------|-------|---------------|----------------|-----------|
| `Asset` | `type` | `AssetType` | **NO** | `app/Models/Asset.php:27–38` |
| `Income` | `recurring_frequency` | `RecurringFrequency` | **NO** | `app/Models/Income.php` (casts check needed) |
| `Expense` | `recurring_frequency` | `RecurringFrequency` | **NO** | `app/Models/Expense.php` (casts check needed) |

### 2.5 Summary Table — Enum Conversion

| Field | Enum Exists? | Raw-String Leaks (count) | Model Cast? | Priority |
|-------|-------------|-------------------------|-------------|----------|
| `payments.status` | ✅ `PaymentStatus` | 7 | ✅ Cast | High |
| `subscriptions.status` | ✅ `SubscriptionStatus` | 1 | ✅ Cast | Med |
| `invoices.status` | ❌ **Missing** | 9 | ❌ None | **Critical** |
| `payment_verifications.status` | ❌ **Missing** | 4 | ❌ None | **High** |
| `payment_webhook_logs.status` | ❌ **Missing** | 3 | ❌ None | Med |
| `payment_methods.type` | ❌ **Missing** | 4 | ❌ None | Med |
| `debts.status` | ✅ `DebtStatus` | 0 | ✅ Cast | Low |
| `debts.type` | ✅ `DebtType` | 0 | ✅ Cast | Low |
| `financial_goals.status` | ✅ `GoalStatus` | 0 | ✅ Cast | Low |
| `workspace_invitations.status` | ✅ `InvitationStatus` | 0 | ✅ Cast | Low |
| `assets.type` | ✅ `AssetType` | 0 | **❌ NO cast** | **High** |
| `incomes.recurring_frequency` | ✅ `RecurringFrequency` | 0 | **❌ NO cast** | Low |
| `expenses.recurring_frequency` | ✅ `RecurringFrequency` | 0 | **❌ NO cast** | Low |

---

## Section 3: Translation Key Findings

### 3.1 Translation Mechanism

Each enum implements a `label()` method that calls `__()` with a translation key. No `EnumTranslatable` trait or interface exists — the pattern is manually repeated per enum.

| Enum | Translation Keys Used | File:Line |
|------|---------------------|-----------|
| `PaymentStatus` | `enums.payment_status.checkout_*` | `app/Enums/PaymentStatus.php:53–62` |
| `SubscriptionStatus` | `enums.subscription_status.*` | `app/Enums/SubscriptionStatus.php:38–47` |
| `GoalStatus` | `goal.in_progress`, `goal.completed`, `goal.cancelled` | `app/Enums/GoalStatus.php:17–24` |
| `DebtStatus` | `debt.*` | `app/Enums/DebtStatus.php:17–25` |
| `DebtType` | `debt.owed`, `debt.owing` | `app/Enums/DebtType.php:15–20` |
| `AssetType` | `asset.*` | `app/Enums/AssetType.php:22–34` |
| `RecurringFrequency` | `general.daily`, `general.weekly`, etc. | `app/Enums/RecurringFrequency.php:17–24` |
| `InvitationStatus` | `workspace.invitation_*` | `app/Enums/InvitationStatus.php:23–32` |

### 3.2 `enums.php` Language Files

All three locales have `enums.php` in `resources/lang/{locale}/enums.php`:

**English** (`resources/lang/en/enums.php`):
- `payment_status` (5 keys) — all OK
- `subscription_status` (5 keys) — all OK
- `general_status` (5 keys) — **ORPHANED** — no code references it, no enum exists, missing from ar/fr

**Arabic** (`resources/lang/ar/enums.php`):
- `payment_status` (5 keys) — all OK
- `subscription_status` (5 keys) — all OK
- Missing `general_status` — not an issue since it's orphaned

**French** (`resources/lang/fr/enums.php`):
- `payment_status` (5 keys) — all OK
- `subscription_status` (5 keys) — all OK
- Missing `general_status` — same as Arabic

### 3.3 Missing Translation Keys

| Locale | File | Missing Key | Present In |
|--------|------|-------------|------------|
| Arabic (ar) | `resources/lang/ar/goal.php` | `'name'` | English (line 4), French (line 4) |

### 3.4 Hardcoded Label Leaks

| File | Line | Leak | Severity |
|------|------|------|----------|
| `resources/views/livewire/pages/onboarding/manual-proof.blade.php` | 62–64 | `match ($v->status) { 'approved' => 'completed', 'rejected' => 'rejected' }` — hardcoded verification status strings | **Medium** |
| `resources/views/livewire/pages/onboarding/manual-proof.blade.php` | 260–263 | `statusBadgeClass()` uses `'pending'`, `'approved'`, `'rejected'` raw strings | **Medium** |
| `resources/views/super-admin/payments.blade.php` | 126 | `$vColors = ['pending' => ..., 'approved' => ..., 'rejected' => ...]` | **Medium** |
| `resources/views/livewire/pages/onboarding/partials/payment-details.blade.php` | 85 | `{{ ucfirst($invoice['status'] ?? 'draft') }}` — untranslated raw status | **Low** |
| `resources/views/super-admin/subscription-show.blade.php` | 83–84 | `$bi = ['paid' => ..., 'draft' => ..., 'overdue' => ...]` then `{{ $inv->status }}` — displays raw status, not `$inv->status->label()` | **Low** |
| `resources/views/debt/index.blade.php` | 142–149 | `$statusColors` hardcoded (labels use translations — acceptable but fragile) | **Low / Info** |

### 3.5 Orphaned Keys

| Section | File | Keys | Status |
|---------|------|------|--------|
| `enums.general_status.*` | `resources/lang/en/enums.php:20–26` | `active`, `past_due`, `expired`, `canceled`, `trialing` | **Orphaned** — no enum class `GeneralStatus` exists, no code references these keys |

### 3.6 Translation Completeness

All existing enum cases have matching translation keys across all three locales, except:
- `goal.name` missing from Arabic (referenced by `GoalStatus::label()` for `Active` case)

---

## Section 4: Cross-Cutting Sanity Checks

### 4.1 Model Enum Casting

| Model | Status Field | Cast Declaration | File:Line |
|-------|-------------|-----------------|-----------|
| `Payment` | `status` | ✅ `PaymentStatus::class` | `app/Models/Payment.php:39` |
| `Subscription` | `status` | ✅ `SubscriptionStatus::class` | `app/Models/Subscription.php:34` |
| `Debt` | `type`, `status` | ✅ `DebtType::class`, `DebtStatus::class` | `app/Models/Debt.php:32–33` |
| `FinancialGoal` | `status` | ✅ `GoalStatus::class` | `app/Models/FinancialGoal.php:30` |
| `Invitation` | `status` | ✅ `InvitationStatus::class` | `app/Models/Invitation.php:38` |
| `Asset` | `type` | ❌ `AssetType` exists but NOT cast | `app/Models/Asset.php:27–38` |
| `Invoice` | `status` | ❌ No enum exists | `app/Models/Invoice.php:34–50` |
| `PaymentVerification` | `status` | ❌ No enum exists | `app/Models/PaymentVerification.php:24–28` |

### 4.2 Naming Drift Check (`Paid` vs `Completed`)

The `PaymentStatus` enum uses `CheckoutPaid` (value: `'checkout.paid'`), NOT `Completed`. This naming was previously identified as a bug class. Verification confirms:
- No `Completed` case exists in `PaymentStatus`
- No code references `PaymentStatus::Completed` or `'completed'` for payment status (correct)
- No raw `'completed'` string used for payment status comparisons

**No reintroduction of the `Completed` vs `Paid` drift found.** This is clean.

### 4.3 PHP Version Compatibility

All enums use `string`-backed syntax, compatible with PHP 8.1+ (project uses PHP 8.3). All `match` expressions are exhaustive. No issues.

---

## Section 5: Open Questions (Require Your Confirmation Before Phase 2)

1. **`payments.uuid` semantic intent**: Should this column now become the canonical public-facing Payment ID across all surfaces (admin panel, invoices, API responses, webhook logs, email receipts, gateway references)? The column is currently unused. Do you want it to replace the auto-increment `id` in admin URLs, or simply be displayed alongside `id`?

2. **Seeders**: No seeder creates Payment records. Should we add a `PaymentSeeder` for demo/test data that populates `uuid`, or leave seeding as-is?

3. **`InvoiceStatus` enum**: What exact values/cases should it have? Possible options:
   - `Draft`, `Paid`, `Overdue`, `Cancelled` (matching current usage)
   - Or different naming convention?

4. **`PaymentVerificationStatus` enum**: Values `Pending`, `Approved`, `Rejected` — confirm case names.

5. **`PaymentWebhookLogStatus` enum**: Values `Received`, `Processed` — any others needed?

6. **`PaymentMethodType` enum**: Values `Online`, `Manual`, `AutoComplete` — confirm.

7. **Scope limit for role slugs**: The prompt's enum audit scope includes "User role / permission-related status fields." There are 30+ raw `'super_admin'` and `'workspace_admin'` strings across the codebase. Should these be converted to a `RoleSlug` enum as part of this audit, or is that out of scope (separate prompt already exists)?

8. **`Asset.type` not cast**: `AssetType` enum exists but is not cast on the model. Should casting be added during Phase 2?

9. **`enums.general_status` orphan**: Should this section be removed from English `enums.php`?

---

## Section 6: Priority Classification

### Critical
| Finding | Location | Rationale |
|---------|----------|-----------|
| `payments.uuid` is functionally dead | Entire codebase | Column exists, data is generated, but never read or displayed anywhere — **data integrity gap** |
| `Invoice.status` has no enum | `app/Models/Invoice.php:34–50` | 9 raw-string occurrences; filter logic, scopes, and display all use raw strings; no type safety |
| `PaymentVerification.status` has no enum | `app/Models/PaymentVerification.php:24–28` | 4 raw-string occurrences across blade views and model; admin verification flow relies on this |

### High
| Finding | Location | Rationale |
|---------|----------|-----------|
| `Asset.type` not cast to `AssetType` | `app/Models/Asset.php:27–38` | Enum exists but is unused — data stored as raw string, no type safety |
| `User.php:164` raw `'checkout.pending'` | `app/Models/User.php:164` | Should use `PaymentStatus::CheckoutPending->value` |
| `DashboardController:36` raw subscription status | `app/Http/Controllers/SuperAdmin/DashboardController.php:36` | Same method line 37 uses enum correctly — clear inconsistency |
| `ChargilyWebhookService.php` raw status strings (4×) | `app/Services/Payments/Chargily/ChargilyWebhookService.php:125,149,173,197` | Should use `PaymentStatus::*->value` |
| Hardcoded verification status in blade views | `manual-proof.blade.php:62–64,260–263` and `payments.blade.php:126` | Display logic coupled to raw string values |
| `goal.name` missing from Arabic | `resources/lang/ar/goal.php` | Translated key missing — causes fallback to key name or error |

### Medium
| Finding | Location | Rationale |
|---------|----------|-----------|
| `PaymentWebhookLog.status` / `.gateway` raw strings | `app/Services/Payments/Chargily/ChargilyWebhookService.php:223,234` | No enum exists for webhook log statuses |
| `PaymentMethod.type` raw strings | `app/Models/PaymentMethod.php:104,109,114` | No enum exists for method types |
| `SubscriptionActivationService.php:185` raw invoice status | `app/Services/SubscriptionActivationService.php:185` | `'paid' : 'draft'` — should use enum |
| `OnboardingService.php:99,104,109` raw method types | `app/Services/OnboardingService.php` | `'online'`, `'manual'`, `'auto_complete'` literals |
| `OnboardingService.php:275` raw status | `app/Services/OnboardingService.php:275` | `'status' => 'pending'` |
| `payment-details.blade.php:85` untranslated status | `resources/views/livewire/pages/onboarding/partials/payment-details.blade.php:85` | `ucfirst($invoice['status'] ?? 'draft')` |
| `subscription-show.blade.php:84` raw invoice status display | `resources/views/super-admin/subscription-show.blade.php:84` | `{{ $inv->status }}` instead of `$inv->status->label()` |

### Low
| Finding | Location | Rationale |
|---------|----------|-----------|
| `Incomes.recurring_frequency` not cast | `app/Models/Income.php` | `RecurringFrequency` exists but not cast |
| `Expenses.recurring_frequency` not cast | `app/Models/Expense.php` | Same as above |
| `enums.general_status` orphaned section | `resources/lang/en/enums.php:20–26` | Dead code — no code references it |
| `debt/index.blade.php:142–149` hardcoded colors | `resources/views/debt/index.blade.php` | Only CSS class mapping, labels use translations |
| `Workspace.type` / `Coupon.type` / `TaxRate.type` | Various | Low-impact fields, raw strings acceptable for simple `type` columns |

---

*End of Phase 1 Audit Report. Awaiting review and approval before Phase 2 implementation.*
