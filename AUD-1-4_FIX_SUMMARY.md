# AUD-1 through AUD-4 Fix Summary

## AUD-1 — PaymentStatus Enum Migration

**Root Cause:** Hard-coded string literals (`'pending'`, `'completed'`, etc.) in payment-related queries instead of `PaymentStatus::*->value`.

**Scope:** 15 locations in 10 files across `app/`.

**Fixes:**

| # | File | Lines | Old Value | New Value |
|---|------|-------|-----------|-----------|
| 1 | EnsureOnboardingCompleted.php:78 | 1 | `'pending'` → `PaymentStatus::CheckoutPending->value` |
| 2 | EnsureOnboardingCompleted.php:88 | 1 | `'completed'` → `PaymentStatus::CheckoutPaid->value` |
| 3 | DashboardController.php:217 | 1 | `'completed'` → `PaymentStatus::CheckoutPaid->value` |
| 4 | DashboardController.php:258 | 1 | `'pending'` → `PaymentStatus::CheckoutPending->value` |
| 5 | SubscriptionController.php:48 | 1 | `'pending'` → `PaymentStatus::CheckoutPending->value` |
| 6 | ExpireSubscriptions.php:30 | 1 | `'pending'` → `PaymentStatus::CheckoutPending->value` |
| 7-9 | PaymentController.php (webhook) | 3 | `'completed'` + `'failed'` + `'pending'` → respective enums |
| 10-12 | PaymentService.php | 3 | `'pending'` + `'completed'` + `'failed'` → respective enums |
| 13-14 | SubscriptionService.php | 2 | `'completed'` + `'pending'` → respective enums |
| 15 | OnboardingService.php | 1 | `'completed'` → `PaymentStatus::CheckoutPaid->value` |

## AUD-2 — Subscription Renewal Reminder

**Status: Already implemented.** Command `subscriptions:remind-expiry` exists in:
- `app/Console/Commands/RemindExpiringSubscriptions.php` — sends notification-only reminders 3 days before `ends_at`
- Scheduled in `Kernel.php` daily at `09:00`
- Backed by `SendSubscriptionExpiryNotification` job + `SubscriptionExpiryWarning` notification

**No changes required.**

## AUD-3 — activeSubscription() Caller Audit

**Issue:** `activeSubscription()` returns the subscription for **this user instance**. When called on `auth()->user()` from workspace-context middleware, team members' own (potentially free) subscriptions were preferred over the workspace owner's subscription.

**Fixes:**

| File | Line | Before | After |
|------|------|--------|-------|
| `User.php` | 99 | *(no docblock)* | Added docblock explaining design intent |
| `CheckSubscriptionStatus.php` | 62 | `$user->... ?? $owner->...` | `$owner->... ?? $user->...` |
| `CheckActiveSubscription.php` | 63 | `$user->... ?? $owner->...` | `$owner->... ?? $user->...` |
| `CheckApiSubscription.php` | 32-34 | `$user->... ?? direct... ?? $owner->...` | `$owner->... ?? $user->... ?? direct...` |

**Unchanged (correct as-is):**
- `User::hasActivePaidAccess()` — model-internal use, acts on `$this`
- `DeveloperController` — API tokens are per-user, user's own plan is correct
- `SubscriptionController` (Account) — billing info page, user's own sub first is fine
- `PaymentController` (Account) — uses direct query, not `activeSubscription()`

## AUD-4 — EnsureOnboardingCompleted Middleware

**Issue:** Used hard-coded `'pending'` and `'completed'` instead of `PaymentStatus` enum.

**Fixed** in same AUD-1 edits (lines 78, 88 of `EnsureOnboardingCompleted.php`).
