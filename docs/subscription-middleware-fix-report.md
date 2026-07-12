# Subscription Middleware & Payment Status Overhaul — Complete Report

> **⚠️ هذا التقرير قديم — يعكس مرحلة سابقة من الإصلاحات. التحقيقات والتغييرات اللاحقة موثّقة في `docs/audit-report-v2.md` و `docs/audit-report.md`.**
>
> **تحديث 12 يوليو 2026:** تم إعادة هيكلة `subscription_plans` بالكامل (إزالة `limits` JSON، إضافة `yearly_discount_percent` كعمود، نقل القيم إلى `plan_plan_feature` pivot). راجع `docs/test_manual.md` المُحدَّث.

## Overview

This document catalogs all changes made to fix subscription expiry/middleware issues and simplify the payment status system. The root cause was `User::activeSubscription()` returning a free-plan subscription (`ends_at=null`) even after the user had canceled their paid plan — bypassing middleware checks.

---

## Phase 1: Middleware & Subscription Fixes

### 1. Route exceptions for gateways

**Files:**
- `app/Http/Middleware/CheckActiveSubscription.php` — `except` array
- `app/Http/Middleware/CheckSubscriptionStatus.php` — `alwaysAllowed` array

**Change:** Added gateway return/back URLs so webhooks and redirects aren't blocked:
- `chargily.back`, `paypal.back`, `paytr.back`, `rasmal.back`

### 2. Grace-period check order

**File:** `app/Http/Middleware/CheckSubscriptionStatus.php:72`

**Change:** In `computeStatus()`, moved the grace-period check (`$sub->isOnGrace()`) **above** the `isActive()` check. Previously `isActive()` returned `true` during grace (since `ends_at` is still in the future), causing the middleware to return `'active'` instead of `'grace'`.

### 3. `User::activeSubscription()` — root cause fix

**File:** `app/Models/User.php:activeSubscription()`

**Change:** The method was returning a free plan (`ends_at=null`) whenever it existed, even if the user had a recent canceled/expired subscription. Now it:
- Filters out `ends_at`-past subscriptions for `active`/`trialing` statuses
- Only returns free plan (`ends_at=null`) when the user has **no** canceled/expired subscription without grace remaining
- This is the fix that finally blocks users from navigating after paid plan cancel

### 4. Subscription cancellation service

**Files:**
- `app/Services/SubscriptionCancellationService.php`
- `app/Services/SubscriptionService.php` (`cancelSubscription()`)

**Changes:**
- Added refund logic for `immediate` type via `GatewayManager::refund($payment)`
- `immediate` type now returns early (skips grace period entry)
- `cancelCurrentSubscription()` now calls `enterGracePeriod()` for non-immediate cancels

### 5. Cancel route moved

**File:** `routes/tenant.php`

**Change:**
- **Before:** `Route::prefix('workspace')->name('settings.workspace.')->post('/cancel', …)->name('cancel')`
- **After:** `Route::prefix('account')->name('account.')->post('/subscriptions/cancel', …)->name('subscriptions.cancel')`

### 6. Views updated

**Files:**
- `resources/views/settings/_subscription.blade.php`
- `resources/views/settings/index.blade.php`
- `resources/views/account/subscriptions.blade.php`

**Change:** All `route('settings.workspace.cancel')` → `route('account.subscriptions.cancel')`

### 7. Layout banner logic

**File:** `resources/views/layouts/app.blade.php:33-55`

**Change:** Banner now only shows when `activeSubscription()` returns null **AND** there exists an expired/canceled subscription where `grace_ends_at` is null or past. Previously it showed even with an active subscription.

### 8. Renew button added

**File:** `resources/views/layouts/app.blade.php:49-51`

**Change:** Added `<a href="{{ route('account.subscriptions') }}">` link beside the expired banner text.

---

## Phase 2: PaymentStatus Enum Simplification

### Before: 21 values
The old `PaymentStatus` enum had 21 cases covering checkout states, subscription states (active, trialing, past_due), and business-specific states (manual_pending, manual_approved, etc.).

### After: 5 values
Only checkout-level gateway states remain; subscription-level and manual states are inferred from the `subscriptions.status` column and `payment_verifications.status` columns.

**File:** `app/Enums/PaymentStatus.php`
```php
case CheckoutPending  = 'checkout.pending';
case CheckoutPaid     = 'checkout.paid';
case CheckoutFailed   = 'checkout.failed';
case CheckoutCanceled = 'checkout.canceled';
case CheckoutExpired  = 'checkout.expired';
```

### Files updated with new enum values

| File | Changes |
|------|---------|
| `app/Models/Payment.php` | `$fillable` (added `uuid`), `isPending()`, `isPaid()`, `isCompleted()`, `isFailed()`, `scopePending`, `scopeTerminal`, `booted` creating/updating events |
| `app/Services/PaymentService.php` | All status references updated |
| `app/Services/SubscriptionActivationService.php` | Status references updated |
| `app/Services/OnboardingService.php` | Status references updated |
| `app/Services/SubscriptionService.php` | Status references updated |
| `app/Services/Payments/Chargily/ChargilyWebhookService.php` | Status references updated |
| `app/Services/Payments/PaymentTransitionValidator.php` | Transition map and target checks updated |
| `app/Http/Controllers/PaymentReturnController.php` | Status checks updated |
| `app/Console/Commands/CheckNoestDeliveries.php` | Status references updated |
| `resources/views/livewire/pages/onboarding/payment.blade.php` | Canceled → CheckoutCanceled |
| `resources/views/livewire/pages/onboarding/payment-retry.blade.php` | All status refs + `statusBadgeClass()` |
| `resources/views/livewire/pages/onboarding/payment-result.blade.php` | All status refs + `statusBadgeClass()` |
| `resources/views/livewire/pages/onboarding/manual-proof.blade.php` | All status refs |
| `tests/Feature/PaymentWebhookTest.php` | Assertions + factory status |
| `tests/Feature/Admin/DashboardRegressionTest.php` | Factory status |
| `tests/Feature/Onboarding/PaymentRetryPageTest.php` | Payment update call |
| `tests/Feature/Payments/ChargilyPaymentMethodTest.php` | Assertions + factory status |
| `tests/Feature/Payments/NoestPaymentMethodTest.php` | Factory status |
| `tests/Feature/Payments/PaymentFlowTest.php` | Assertions |
| `database/factories/PaymentFactory.php` | Uses PaymentStatus enum cases |

---

## Phase 3: Migration — UUID & Status Migration

**File:** `database/migrations/2026_07_09_175155_add_uuid_and_update_status_in_payments.php`

**Operations:**
1. Adds `uuid` column (`VARCHAR(20)`, nullable) after `id`
2. Populates existing rows with `pay-xxxxxxxxxxxx` format UUIDs
3. Maps old status strings → new `checkout.*` values:
   - `pending` → `checkout.pending`
   - `completed`, `paid` → `checkout.paid`
   - `failed` → `checkout.failed`
   - `canceled`, `cancelled` → `checkout.canceled`
   - `expired` → `checkout.expired`
4. Deduplicates payments by UUID (keeps oldest)
5. Adds unique index on `uuid`

**Payment Model auto-generation:** `app/Models/Payment.php:booted()` — auto-generates `pay-{12 chars}` when `uuid` is null on creation.

---

## Test Results

- **PaymentFlowTest** — 10/10 passed
- **PaymentWebhookTest** — 17 passed, 7 skipped (Chargily requires ngrok)
- **NoestPaymentMethodTest** — 11/11 passed
- **ChargilyPaymentMethodTest** — 0 passed (all skipped, requires ngrok)
- **PaymentRetryPageTest** — 13/13 passed
- **DashboardRegressionTest** — 2/2 passed
- **Total suite:** ~462 passed, 11 skipped (pre-existing failures unrelated)

---

## Key Architecture Decisions

1. **`uuid` nullable**: Kept nullable in the DB to avoid migration issues; the model's `creating` event ensures it's always populated in application code.
2. **Unique index on `uuid`**: Prevents accidental duplicate payments; MySQL permits multiple NULLs in unique indexes.
3. **`isCompleted()` = `isPaid()`**: `isCompleted()` is retained as an alias for backward compatibility.
4. **`isFailure()` includes CheckoutCanceled and CheckoutExpired**: Failed/canceled/expired are all terminal failure states from the checkout perspective.
