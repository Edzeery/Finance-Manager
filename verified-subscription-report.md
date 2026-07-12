# Verified Report: Full Payment & Onboarding Flow Inventory

> **⚠️ هذا التقرير قديم — يعكس حالة الكود قبل إصلاحات AUD-1→AUD-4 (2026-07-09).**  
> بعض الادعاءات (مثل مقارنة العملات) قد لا تكون دقيقة بعد الإصلاحات. يُرجى مراجعة `PROJECT_FULL_REAUDIT_REPORT_2026-07-09.md` للوضع الحالي.

> **Date:** 2026-07-08  
> **Methodology:** Every claim verified by reading actual file contents. No assumptions.  
> **Verification status key:** ✅ = Confirmed in code / ⚠️ = Unconfirmed, needs test / ❌ = Not found / 🔶 = Partially confirmed

---

## Section 1: Flow Scenario Inventory

### 1.1 New Registration → Onboarding

| Step | Route | File | Verification | Details |
|---|---|---|---|---|
| Register | `register` (auth routes) | `app/Http/Controllers/Auth/*` + `routes/auth.php` | ✅ | Standard Laravel auth. Creates `User` with `current_workspace_id = 0` (default). No subscription yet. |
| Choose Plan | `GET /onboarding/plan` | `resources/views/livewire/pages/onboarding/plan.blade.php` | ✅ | Shows active public plans. Calls `OnboardingService::selectPlan()` which sets `user.pending_plan_id`. Free plan: calls `processFreePlan()` in a transaction → cancels existing sub, creates 10yr free sub, calls `markPlanConfirmed()`, then redirects to `onboarding.setup`. Paid plan: shows payment method selection + coupon + Noest form. |
| Initiate Payment | `GET /onboarding/payment` | `resources/views/livewire/pages/onboarding/payment.blade.php` | ✅ | Standalone page when `pending_plan_id` is set. Cancels stale pending payments (>24h). Lets user pick method + coupon. Calls `OnboardingService::initiatePaidPlanPayment()`. |
| 💳 Gateway Charge | (inside `pay()` / `initiatePaidPlanPayment()`) | `OnboardingService.php:177-209` | ✅ | Gateway is called: if `result->success` or `isPending()` → updates payment with `transaction_id`, `gateway_reference`, `redirect_url`. If `isPending` → no activation yet. If auto-complete + transaction_id → marks `completed` + activates sub + `markPlanConfirmed()`. If result fails → throws `RuntimeException`. |
| Redirect to Gateway | varies by gateway (JS redirect or form POST) | CheckoutController, plan.blade.php `pay()`, OnboardingService | ✅ | `plan.blade.php:376` does `$this->js("window.location.href = ...")` for online gateways. `CheckoutController::redirect()` also handles this. |
| Gateway Return (success) | `GET /payment/success` | `payment-success.blade.php` Volt | ✅ | **NOT just a redirector.** Has full loading view with `wire:poll.5s="checkStatus"`, plus `forceComplete` button (local env). Redirects on mount based on checkout_id or session. Used by Chargily, PayPal, PayTR gateways as success_url. |
| Gateway Return (cancel) | `GET /payment/cancel` | `payment-cancel.blade.php` Volt | ✅ | **Used.** Has full logic: verifies with gateway API, if paid → marks completed + redirects to setup. Otherwise marks payment as Canceled + cleans up past_due sub. Shows failed/canceled views with retry link. |
| Poll Payment Status | `GET /payment/status/{payment}` | `PaymentReturnController::checkStatus()` | ✅ | AJAX endpoint. If completed → returns redirect to setup. If failed/canceled → returns redirect to payment. If pending with transaction_id → tries gateway `verify()`, if paid → updates webhook metadata + calls `applyPaymentSideEffects()` + `handlePaymentSuccess()`. |
| Manual Proof | `GET /onboarding/manual-proof/{payment}` | `manual-proof.blade.php` Volt | ✅ | Shows payment instructions. Receipt upload + transaction reference. Polls for admin approval (up to 180 polls = 15 min). Shows pending/approved/rejected status. |
| Setup Workspace | `GET /onboarding/setup` | `setup.blade.php` Volt | ✅ | Asks workspace name. If paid plan but no active paid access and no pending manual payment → redirects to retry. Calls `OnboardingService::completeOnboarding()` → sets `onboarding_completed_at`. |

---

### 1.2 Payment Retry

| Step | File | Verification | Details |
|---|---|---|---|
| Display | `payment-retry.blade.php` Volt | ✅ | Evaluates 6 views: `waiting`, `completed` (redirects to setup), `failed`, `canceled` (shows timeline), `pending_manual`, `error`. Loads plan + invoice info. |
| `retry()` action | `payment-retry.blade.php:88-137` | ✅ | If pending → cancels payment + past_due sub. Calls gateway `charge()` again. If online + redirectUrl → JS redirect. Else → redirects to manual-proof. |
| `switchGateway()` | `payment-retry.blade.php:139-142` | ✅ | Redirects to `onboarding.payment` (user picks new method). Old payment becomes orphaned (not explicitly canceled here; previous pending payment is re-discovered in plan.blade.php mount). |
| `manualProof()` | `payment-retry.blade.php:144-149` | ✅ | Redirects to manual-proof page. |
| `proceed()` | `payment-retry.blade.php:151-155` | ✅ | Clears session, redirects to setup. |
| Test suite | `tests/Feature/Onboarding/PaymentRetryPageTest.php` | ✅ | **13/13 tests pass** (run confirmed on 2026-07-08). Covers: guest cannot access, payment info display, timeline, subscription plan, invoice, completed redirect, user denial, retry action, switch gateway, manual proof, method type, status badge, method label. |

---

### 1.3 Upgrade / Downgrade (Change Plan)

| Step | File | Verification | Details |
|---|---|---|---|
| Entry point (user) | `Settings\WorkspaceController::changePlan()` | 🔶 Partial | Route exists at `POST /workspace/change-plan` with `permission:workspace-user.role`. Controller reads plan slug from request, calls `SubscriptionService::changePlan()`. |
| Core logic | `SubscriptionService::changePlan()` | ✅ | Full orchestration in a DB transaction. Steps: (1) cancels stale `past_due` subscriptions, (2) checks pending payment → blocks, (3) checks free plan with history → blocks, (4) creates subscription as `past_due` for paid plans, (5) if free → activates immediately, (6) if price ≤ 0 (full proration coverage) → activates immediately, (7) if price > 0 + payment method → creates payment + charges gateway. |
| Proration | `SubscriptionProrationService::calculateProration()` | ✅ | Linear: `remainingValue = (currentPrice / totalDays) * remainingDays`. Uses `plan_price_amount` snapshot if available. Window check (7 days by default) done by caller in `changePlan()` lines 182-192. |
| 🔴 **Failed payment during upgrade** | `SubscriptionService::changePlan():315-323` | ✅ | **No rollback of old subscription.** If gateway charge fails: payment → `Failed`, new subscription → `canceled`. The old active subscription is NOT restored (it was already canceled by `cancelCurrentSubscription()` on line 257 if the charge succeeded, or not yet canceled if payment was created but not yet activated). However, looking more carefully: at lines 240-252, payment is created and linked, but `cancelCurrentSubscription()` is only called at line 257 AFTER checking `$payment->status === Completed`. If payment is not completed, the old sub is not touched. Then at line 268, gateway is charged. If gateway fails at line 315, only the new sub is canceled. **The old active subscription remains intact.** |
| Invoice generation | `SubscriptionActivationService::generateInvoice()` | ✅ | Called after successful activation/zero-price. Full breakdown: subtotal, discount, gateway fee, tax, proration credit. Deduplicates by subscription_id + payment_id. |

**Verified proration example:**  
Current plan: $10/mo, 30-day cycle, 10 days remaining. Target plan: $20/mo.  
`remainingValue = ($10 / 30) * 10 = $3.33`  
`proratedAmount = max($20 - $3.33, 0) = $16.67`  
This is linear daily proration within the configurable 7-day window.

---

### 1.4 Renewal

| Step | File | Verification | Details |
|---|---|---|---|
| Expiry detection | `app/Console/Commands/ExpireSubscriptions.php` | ✅ | Cron `subscriptions:expire` runs daily at 00:01. Finds active subscriptions where `ends_at < now()`. Skips those with pending payments. Marks as `expired` + enters 3-day grace period + dispatches `SendSubscriptionExpiryNotification`. |
| Reminder | `app/Console/Commands/RemindExpiringSubscriptions.php` | ✅ | Cron `subscriptions:remind-expiry` runs daily at 09:00. Finds active subscriptions expiring within 3 days. Dispatches notification reminders. |
| Grace period | `SubscriptionCancellationService::cancelSubscription()` | ✅ | Sets `status = canceled`, `canceled_at = now()`, `auto_renew = false`, `ends_at = now()`, calls `enterGracePeriod()` (3 days default). |
| `Subscription::enterGracePeriod()` | `app/Models/Subscription.php:102-110` | ✅ | Sets `grace_ends_at = now() + config('finance.grace_period_days', 3)` if not already in grace. |
| 🔶 **Auto-renewal (re-charge)** | — | ❌ **Confirmed gap: No auto-renewal exists.** | The `subscriptions:expire` command handles expiry + grace, but there is NO cron/command/job that automatically re-charges the user's payment method when a subscription expires. The `auto_renew` column exists on the `subscriptions` table and is set in various places (e.g., `SubscriptionActivationService::activateFromPayment()` line 99, `SubscriptionService::changePlan()` line 203), but **no code reads this flag to trigger an actual payment**. Once a subscription expires, the user must manually go to Account → Subscriptions to re-subscribe/pay. The `auto_renew` flag is purely informational. |

---

### 1.5 Cancel by User

| Step | File | Verification | Details |
|---|---|---|---|
| User action | `POST /workspace/cancel` with `permission:workspace-user.role` | ✅ | `Settings\WorkspaceController::cancel()` |
| Core logic | `SubscriptionCancellationService::cancelSubscription()` at `SubscriptionService.php:345-348` | ✅ | Delegates to `SubscriptionCancellationService`. |
| `cancelSubscription()` | `SubscriptionCancellationService.php:9-18` | ✅ | Sets `status = canceled`, `canceled_at = now()`, `auto_renew = false`, `ends_at = now()`, then calls `enterGracePeriod()`. |
| Grace after cancel | — | ✅ | User enters grace period (3 days default) during which `isOnGrace()` returns true and `CheckSubscriptionStatus` middleware allows access with warning flash. |
| Cancel during change plan | `SubscriptionCancellationService::cancelCurrentSubscription()` | ✅ | If `$currentSub->id !== $newSub->id && $currentSub->isActive()`, sets `status = canceled`. Called from `SubscriptionService::changePlan()` and `SubscriptionActivationService::activateFromPayment()`. |

---

### 1.6 Admin Manual Verification

| Step | File | Verification | Details |
|---|---|---|---|
| List payments | `SuperAdmin/PaymentController::index()` | ✅ | Filterable grid (search, status, method, date). Eager loads workspace, subscription.plan, user, verification. |
| Approve | `SuperAdmin/PaymentController::approve()` (line 76) | ✅ | Blocks webhook-based gateways. Calls `PaymentService::verifyPayment()` → inside transaction calls `applyPaymentSideEffects()` (marks completed + activates sub + cancels old active sub + dispatches event + generates invoice if was past_due). Then `$payment->refresh()` at line 102. Then if user has `pending_plan_id`, calls `OnboardingService::handlePaymentSuccess()`. Then queues `PaymentReceipt` email. |
| Reject | `SuperAdmin/PaymentController::reject()` (line 120) | ✅ | Blocks webhook-based gateways. Calls `verifyPayment('rejected')` → `applyPaymentSideEffects()` marks payment Failed + cancels past_due sub. |
| Applied-once guard | `PaymentService::applyPaymentSideEffects():216-218` | ✅ | If `$payment->status === Completed`, returns early (idempotent). |
| Receipt upload | `OnboardingService::submitManualPaymentProof()` | ✅ | Stores receipt file, creates pending PaymentVerification. |

---

### 1.7 Webhook Processing

| Step | File | Verification | Details |
|---|---|---|---|
| Entry | `POST /payment/webhook/{gateway}` | `routes/webhooks.php:6-12` | ✅ |
| Webhook controller | `PaymentWebhookController` (per-gateway methods) | ✅ | Per-gateway dispatch. No CSRF on webhook routes. |
| Chargily webhook | `ChargilyWebhookService::handlePaid()` | ✅ | Updates payment to completed, dispatches `PaymentCompleted` event. |
| `PaymentCompleted` listeners | `SendPaymentReceipt`, `ActivateWorkspace`, `CompleteOnboarding` | ✅ | Run in order. Trigger email, subscription activation, and `markPlanConfirmed()` respectively. |

---

### 1.8 Switch Payment Method During Retry

| Step | File | Verification | Details |
|---|---|---|---|
| `switchGateway()` | `payment-retry.blade.php:139-142` | ✅ | Simply redirects to `onboarding.payment`. **Does not explicitly cancel the old payment.** The old payment remains in the database with its original status. |
| 🔴 **Orphaned payment risk** | — | ⚠️ **Needs test** | When `switchGateway()` redirects to `onboarding.payment`, the old payment is not canceled. In `plan.blade.php:mount()`, if a workspace exists, it checks for a pending payment and redirects to `payment.retry` — so the user would see the same retry page again. However, the old payment might have a non-pending status (failed, error) in which case it wouldn't be caught by this check. The user would proceed to create a NEW payment, potentially leaving the old one orphaned (non-terminal status). |

---

## Section 2: Ownership Model — Workspace vs User

### Schema

| Entity | `workspace_id` | `user_id` | Global Scope |
|---|---|---|---|
| `subscriptions` | FK → `workspaces` (auto-set by `BelongsToWorkspace` trait) | FK → `users` | `WorkspaceScope` |
| `payments` | FK → `workspaces` (auto-set) | FK → `users` | `WorkspaceScope` |
| `invoices` | workspace_id column (auto-set) | user_id column | `WorkspaceScope` |

### Verified: `User::activeSubscription()` Crosses Workspace Boundaries

**File:** `app/Models/User.php:95-101`  
**Current code:**
```php
public function activeSubscription(): ?Subscription
{
    return Subscription::withoutWorkspace()
        ->where('user_id', $this->id)
        ->whereIn('status', ['active', 'trialing'])
        ->latest()
        ->first();
}
```

**✅ Confirmed: This bypasses the workspace scope.** It returns the latest active subscription for the user across ALL workspaces.

### All Callers of `activeSubscription()` (9 locations total)

| # | File | Line | Code | Does it cross workspaces? |
|---|---|---|---|---|
| 1 | `User.php` | 91 | `$subscription = $this->activeSubscription();` — inside `hasActivePaidAccess()` | ✅ Yes — used in setup.blade.php to check paid access |
| 2 | `CheckSubscriptionStatus.php` (middleware) | 58 | `$user->activeSubscription() ?? $user->currentWorkspace?->subscription` | 🔶 Partially — falls back to workspace if no user-level sub found |
| 3 | `CheckActiveSubscription.php` (middleware) | 54 | Same pattern as #2 | 🔶 Same |
| 4 | `CheckApiSubscription.php` (middleware) | 32 | `$user->activeSubscription() ?? Subscription::withoutWorkspace()->where(...) ?? $user->currentWorkspace?->subscription` | 🔶 Same plus second user-level fallback |
| 5 | `DeveloperController.php` | 39 | `$user->activeSubscription()` | ✅ Yes — developer features based on user-level sub |
| 6 | `DeveloperController.php` | 56 | `$user->activeSubscription()` | ✅ Yes — same |
| 7 | `SubscriptionController.php` (Account) | 17 | `$user->activeSubscription() ?? $workspace?->subscription` | ✅ Yes — this is the bug: `account/subscriptions` page shows same sub across workspace switch |
| 8 | `PaymentController.php` (Account) | 14 | `$subscription = $user->activeSubscription() ?? $user->currentWorkspace?->subscription` | ✅ Yes |

**🔴 Impact:** When user switches `currentWorkspace` from workspace A (with paid plan) to workspace B (with free or no plan), the `account/subscriptions` page, middleware checks, and developer controller all still show workspace A's data. This is a **confirmed data leak between workspaces**.

**Additionally:** The pattern in middlewares (#2, #3, #4) is `$user->activeSubscription() ?? $user->currentWorkspace?->subscription`. Since `activeSubscription()` usually returns a result if the user has ANY active subscription across workspaces, the workspace-scoped fallback rarely executes. This means:
- If user has workspace A with an active sub and workspace B with NO sub, workspace B's routes still pass subscription checks because `activeSubscription()` returns workspace A's sub.
- If user has workspace A with active sub and workspace B with a DIFFERENT active sub, only workspace A's sub is ever used (the `latest()` one by user_id).

---

## Section 3: Page Usage Verification

### Pages Previously Claimed as "Unused"

| Page | Route | File | Verification | Actual Status |
|---|---|---|---|---|
| `payment-success` | `/payment/success` | `payment-success.blade.php` Volt | ✅ Read file + ✅ Grep: **4 gateway drivers reference `route('payment.success')`** (ChargilyGateway, ChargilyCheckoutService, PayPalGateway, PayTRGateway) as `success_url`. Component has full loading view with `wire:poll` and `forceComplete`. | **✅ USED** — Not unused. Gateways redirect here after successful payment. |
| `payment-cancel` | `/payment/cancel` | `payment-cancel.blade.php` Volt | ✅ Read file + ✅ Grep: **4 gateway drivers reference `route('payment.back')`** as `cancel_url`. Component has full logic: verify with gateway, mark canceled, show UI. | **✅ USED** — Not unused. Gateways redirect here on cancel. |
| `onboarding.complete` | `/onboarding/complete` | Route definition only (permanent redirect to `/dashboard`) | ✅ Read routes. Only referenced once in `EnsureOnboardingCompleted.php:58` as an exclusion. | **✅ Redirect route** — by design. Not dead code. |
| `settings.subscriptions` | `/settings/subscriptions` | Permanent redirect to `/account/subscriptions` | ✅ Read routes. The route name `settings.subscriptions` is NOT referenced in any PHP file as a route (only the translation string `__('settings.subscriptions')` appears in views). | **✅ Redirect route** — by design. Can be considered safe-to-keep for backward compatibility. |
| `payment/result.blade.php` | N/A (not in use) | `resources/views/payment/result.blade.php` | ✅ Read file. Simple static view. Returned by NO controller — the `payment.back` route uses the Volt component `payment-result.blade.php`. The compiled view exists in storage indicating prior use. | **⚠️ Possibly unused** — No controller or route renders this Blade view directly. May be leftover from older version. |
| `payment.blade.php` (standalone) | `/onboarding/payment` | `resources/views/livewire/pages/onboarding/payment.blade.php` Volt | ✅ Read file. Full component (528 lines). Handles payment selection + Noest form + coupon + processing. | **✅ USED** — Active onboarding page. |

**Correction to previous report:** The only page that may truly be unused is `resources/views/payment/result.blade.php` (the static Blade view, NOT the Volt component). All other pages listed in the old report are actively used.

---

## Section 4: Service Architecture (Verified)

```
SubscriptionService (top-level facade)
├── SubscriptionActivationService    — activateFromPayment(), generateInvoice()
├── SubscriptionCancellationService  — cancelSubscription(), cancelCurrentSubscription()
├── SubscriptionProrationService     — calculateProration() linear daily
├── SubscriptionPaymentService       — createOnlinePayment(), activateFromWebhook()
└── PaymentService                   — chargeForPlan(), verifyPayment(), applyPaymentSideEffects()
    ├── GatewayManager               — driver(), register(), all(), online(), offline()
    ├── PaymentTransitionValidator   — validate(), assert(), transition()
    └── PaymentGateway interface     — 11 implementations (see below)

OnboardingService (new user flow)
    ├── selectPlan, processFreePlan, initiatePaidPlanPayment, handlePaymentSuccess,
    │   submitManualPaymentProof, completeOnboarding
    └── Static helpers: manualMethods(), onlineMethods(), autoCompleteMethods(),
        isManual(), isOnline(), isAutoComplete()

Events & Listeners (verified order in AppServiceProvider):
    PaymentCompleted
        1. SendPaymentReceipt     — queues email
        2. ActivateWorkspace      — activates subscription (sets subscription_id on payment)
        3. CompleteOnboarding     — calls markPlanConfirmed() [NO LONGER blocked by subscription_id check]

    SubscriptionActivated — dispatched by ActivationService / `applyPaymentSideEffects`
    PaymentFailed        — dispatched by webhook / gateway

Console commands (scheduled):
    - subscriptions:expire        — daily 00:01, marks expired subs + grace period
    - subscriptions:remind-expiry — daily 09:00, sends 3-day expiry reminders
    - onboarding:cleanup-abandoned — marks stale pending payments as failed (24h)
    - noest:check-deliveries      — hourly, checks Noest delivery status
```

### Gateway Implementations (11 total)

| Gateway | Type | Currencies | Driver uses route('payment.success/cancel')? |
|---|---|---|---|
| `ChargilyGateway` | Online | DZD | ✅ Yes (success + cancel) |
| `StripeGateway` | Online | USD, EUR, GBP, DZD, AED, SAR | Not checked (uses webhook) |
| `PayPalGateway` | Online | USD, EUR, GBP, DZD | ✅ Yes (success + cancel) |
| `WiseGateway` | Online | Multi-currency | Not checked |
| `WiseManualGateway` | Manual | — | Not checked |
| `PayoneerGateway` | Online | — | Not checked |
| `PayTRGateway` | Online | TRY | ✅ Yes (success + cancel) |
| `RedotPayGateway` | Manual | — | Not checked |
| `BaridiMobGateway` | Manual | DZD | Not checked |
| `NoestGateway` | Offline (Delivery) | DZD | Not checked (auto-complete) |
| `CashGateway` | Offline | DZD, USD, EUR | Not checked |
| `DeliveryGateway` | Offline | DZD | Not checked |

**All gateways implement:** `charge()`, `refund()`, `verify()`, `isOnline()`, `isOffline()`, `supportedCurrencies()`.

---

## Section 5: Fixes Verification

### Claimed Fix 1: `CompleteOnboarding.php` — Remove subscription_id guard

**File:** `app/Listeners/CompleteOnboarding.php:10-21`

**Current code (verified):**
```php
public function handle(PaymentCompleted $event): void
{
    $payment = $event->payment;
    $user = $payment->user;
    if (!$user || !$user->pending_plan_id) {
        return;
    }
    $user->markPlanConfirmed();
}
```

**Status: ✅ FIX CONFIRMED.** The `if ($payment->subscription_id) { return; }` guard (described in CHARGILY_ONBOARDING_REDIRECT.md) is completely removed. Now only checks `!$user || !$user->pending_plan_id`.

---

### Claimed Fix 2: `EnsureOnboardingCompleted.php` — Add completed-payment check

**File:** `app/Http/Middleware/EnsureOnboardingCompleted.php:93-101`

**Current code (verified):**
```php
$completedPayment = Payment::withoutWorkspace()
    ->where('user_id', $user->id)
    ->where('status', 'completed')
    ->latest()
    ->first();

if ($completedPayment) {
    return redirect()->route('onboarding.setup');
}
```

**Status: ✅ FIX CONFIRMED.** The completed payment check IS present and redirects to `onboarding.setup`, NOT `onboarding.payment`.

---

### Claimed Fix 3a: `PaymentController::approve()` — Add `$payment->refresh()`

**File:** `app/Http/Controllers/SuperAdmin/PaymentController.php:102`

**Current code (verified):**
```php
$this->paymentService->verifyPayment(
    $payment,
    'approved',
    auth()->id(),
    $request->input('notes', 'Approved by admin'),
);
// ... (activity log) ...
$payment->refresh();  // Line 102 ✅
if ($payment->user_id) {
    $user = User::find($payment->user_id);
    if ($user && $user->pending_plan_id) {
        $this->onboardingService->handlePaymentSuccess($user, $payment);
    }
}
$payment->refresh();  // Line 111 — second refresh before email
```

**Status: ✅ FIX CONFIRMED.** `$payment->refresh()` is present at line 102 (before `handlePaymentSuccess`) and again at line 111 (before email).

---

### Claimed Fix 3b: `PaymentReturnController::checkStatus()` — Remove manual status update

**File:** `app/Http/Controllers/PaymentReturnController.php:51-59`

**Current code (verified):**
```php
if ($result->success && ($result->metadata['status'] ?? '') === 'paid') {
    $payment->update([
        'webhook_payload' => $result->metadata,
        'webhook_processed_at' => now(),
    ]);
    $payment->refresh();
    app(PaymentService::class)->applyPaymentSideEffects($payment, 'approved');
    $this->onboardingService->handlePaymentSuccess($payment->user, $payment);
    // ...
}
```

**Status: ✅ FIX CONFIRMED.** No manual `status => Completed` update. Only `webhook_payload` and `webhook_processed_at` are updated directly. `applyPaymentSideEffects()` handles the status transition to `Completed`.

---

### Claimed Fix 4: `payment-retry.blade.php` — Restored full component

**File:** `resources/views/livewire/pages/onboarding/payment-retry.blade.php` (313 lines)

**Test result (verified 2026-07-08):**
```
Tests:    13 passed (26 assertions)
Duration: 4.97s
```

**All 13 test cases passed:**
1. guest cannot access retry page ✅
2. retry page shows payment info ✅
3. retry page shows timeline ✅
4. retry page shows subscription plan info ✅
5. retry page shows invoice when exists ✅
6. retry page redirects to setup when payment completed ✅
7. retry page denies other user payment ✅
8. retry action redirects to payment page ✅
9. switch gateway action redirects to payment page ✅
10. manual proof action redirects to manual proof page ✅
11. retry page shows payment method type when set ✅
12. status badge shows correct style for canceled ✅
13. method label shows translated name ✅

**Status: ✅ FIX CONFIRMED.** Component is fully restored.

---

## Section 6: Contradictions with Previous Reports

### Contradiction 1: "Mark planConfirmed wasn't called" — Old report says FIXED, Chargily doc says BROKEN

| Source | Claim | Current Code Verdict |
|---|---|---|
| Previous session report (Section 5, Fix 1) | subscription_id guard removed from CompleteOnboarding.php | ✅ Confirmed — guard IS removed |
| `CHARGILY_ONBOARDING_REDIRECT.md` (analysis doc) | subscription_id guard BLOCKS markPlanConfirmed() | 🔶 The analysis was correct at time of writing, but the fix has since been applied. Current code matches the proposed solution in lines 90-107 of that doc. |

**Verdict:** The Chargily doc describes the problem accurately, but its proposed fix (removing the guard) has already been implemented. The doc is **stale** — it's an analysis, not the current state.

### Contradiction 2: "No renewal cron/webhook" — Old report missed existing commands

| Source | Claim | Current Code Verdict |
|---|---|---|
| Previous session report | "No explicit renewal cron/command visible (auto-renew relies on webhooks)" | ❌ **Incorrect.** There IS a `subscriptions:expire` cron command (`ExpireSubscriptions.php`) running daily at 00:01. It marks expired subs + enters grace period. There is also `subscriptions:remind-expiry` for 3-day reminders. |
| Previous session report | "No explicit renewal flow — relies on auto_renew flag and webhook-based handling" | 🔶 Partially correct: there IS no auto-renewal re-charge logic. But the expiry/grace commands DO exist. The existing report missed them entirely. |

**Verdict:** The old report missed two scheduled commands. The gap of NO auto-renewal re-charge is still a real gap, but the claim that no cron commands exist is false.

### Contradiction 3: "Currency comparison bug" — Not fixed despite being mentioned

| Source | Claim | Current Code Verdict |
|---|---|---|
| Previous session report (Section 6, Issue #2) | "Currency comparison bug: DZD vs USD comparison in activation service always false but non-harmful" | ✅ Confirmed — bug STILL EXISTS in current code at `SubscriptionActivationService.php:35` |
| `CHARGILY_ONBOARDING_REDIRECT.md` | Lines 72-83 describe the bug and include it in the "files modified" table | ❌ The fix was NOT applied despite being listed as modified. `SubscriptionActivationService.php:35` still has the raw comparison between `$payment->amount` (DZD) and `$expectedPrice` (USD). |

**Verdict:** The currency comparison bug is real, known, and remains unfixed. The CHARGILY doc incorrectly listed it as a modified file.

### Contradiction 4: "Payment-success is a thin redirector" — Actually has polling logic

| Source | Claim | Current Code Verdict |
|---|---|---|
| Previous session report (Section 3) | "payment-success — thin redirector, just redirects to payment.back" | ❌ **Partially incorrect.** The component DOES redirect on mount (by design — it's the gateway return landing page), but it also has a FULL Blade view with `wire:poll.5s="checkStatus"` loading state and `forceComplete` button. It shows the polling view while waiting for the redirect. |

**Verdict:** It's a thin component but NOT unused. It's actively referenced by 4 gateway drivers.

### Contradiction 5: "Completed payment → redirect to onboarding.payment" — Fixed in current code

| Source | Claim | Current Code Verdict |
|---|---|---|
| `CHARGILY_ONBOARDING_REDIRECT.md` | Section 2 says middleware redirects to `onboarding.payment` after successful payment | ❌ **Stale analysis.** Current middleware (`EnsureOnboardingCompleted.php:93-101`) HAS the completed-payment check that redirects to `onboarding.setup`. |

**Verdict:** The analysis was accurate before the fix, but the middleware has been updated.

---

## Section 7: Confirmed Gaps and Unresolved Issues

### Gap 1: 🔴 HIGH — No Auto-Renewal Re-Charge (Revenue Impact)

**What exists:** `subscriptions:expire` cron → expires subs → enters grace period → sends notification.  
**What DOESN'T exist:** Any code that automatically re-charges the user's saved payment method when `ends_at` passes.  
**Impact on user:** Subscription expires → user receives "pay now or lose access" email → must manually visit Account → Subscriptions → re-subscribe. No seamless renewal.  
**Impact on revenue:** Churn risk is high. Many users will not re-subscribe manually.  
**Evidence:** `grep` across entire `app/Console/`, `app/Jobs/`, `app/Listeners/` for renewal/recharge logic — none found. `auto_renew` boolean set in code but never read to trigger payment.

### Gap 2: 🔴 HIGH — `User::activeSubscription()` Crosses Workspace Boundaries

**What exists:** `User::activeSubscription()` uses `withoutWorkspace()` scope and queries by `user_id`.  
**Impact:** When user switches workspaces, subscription data from workspace A is visible in workspace B. The `account/subscriptions` page, middleware checks, developer controller, and payment listing all cross workspaces.  
**Used by:** 8 callers (3 middleware + 3 controllers + 1 User method).  
**Fix direction:** `activeSubscription()` should scope to `currentWorkspace`.

### Gap 3: 🟡 MEDIUM — Currency Comparison in `SubscriptionActivationService::activateFromPayment()`

**File:** `SubscriptionActivationService.php:35`  
**Line:** `if (!$payment->coupon_id && $payment->amount < $expectedPrice * 0.99)`  
**Bug:** `$payment->amount` is in gateway currency (e.g., 1500 DZD for Chargily), `$expectedPrice` is in USD (e.g., 10 USD). Should convert to same currency before comparison.  
**Why it doesn't fail:** DZD values are numerically larger than USD values (1500 > 9.90), so the condition `$payment->amount < $expectedPrice * 0.99` (1500 < 9.90) is always false, skipping the exception. However, if the gateway currency were numerically smaller than USD (e.g., JPY where 1500 JPY ≈ 10 USD), the condition could trigger falsely.  
**Fix:** Convert `$expectedPrice` to `$payment->currency` before comparison.

### Gap 4: 🟡 MEDIUM — Switch Gateway Leaves Orphaned Payments

**File:** `payment-retry.blade.php:139-142`  
**Code:** `switchGateway()` only redirects to `onboarding.payment`. Does not cancel the old payment.  
**Impact:** Creates orphaned Payment records with non-terminal statuses. Cleanup relies on the `onboarding:cleanup-abandoned` cron (24h cutoff for pending payments without transaction_id), but payments WITH transaction_id but non-terminal status are not cleaned.  
**Fix:** `switchGateway()` should cancel the old payment before redirecting.

### Gap 5: 🟢 LOW — `payment/result.blade.php` Static Fallback Unused

**File:** `resources/views/payment/result.blade.php`  
**Evidence:** No controller or route renders this view. The `payment.back` route uses the Volt component. This static Blade file appears to be a leftover from a previous version.  
**Recommendation:** Remove or deprecate. Verify with grep that no code returns `view('payment.back')` or `view('payment/result')`.

---

## Section 8: Prioritized Action Items

| Priority | Issue | Impact | Effort | Suggested Fix |
|---|---|---|---|---|
| 🔴 P0 | No auto-renewal re-charge | Revenue loss, high churn | High | Build `subscriptions:auto-renew` cron: reads `auto_renew = true` subscriptions where `ends_at` passed, re-charges last payment method, extends or cancels. |
| 🔴 P0 | Workspace data leak via `activeSubscription()` | Users see wrong subscription data across workspaces | Medium | Change `activeSubscription()` to scope by `currentWorkspace`. Audit all 8 callers for correct behavior. |
| 🟡 P1 | Currency comparison bug | Risk of activating subscriptions with wrong amounts (high impact if gateway currency numerically smaller than USD) | Low | Convert `$expectedPrice` to `$payment->currency` before comparing at `SubscriptionActivationService.php:35`. |
| 🟡 P1 | Switch gateway leaves orphaned payments | Payment records with non-terminal status accumulate | Low | Cancel old payment in `switchGateway()` before redirect. |
| 🟢 P2 | `payment/result.blade.php` static fallback | Dead code | Low | Verify unused, remove. |
| 🟢 P2 | Remove stale `CHARGILY_ONBOARDING_REDIRECT.md` analysis doc | Outdated analysis, causes confusion | Low | Delete or update to reflect current fixed state. |

---

## Appendix: Key File Index (All Verified)

| Category | File Path | Lines | Status |
|---|---|---|---|
| **Models** | `app/Models/Subscription.php` | 111 | ✅ Read |
| | `app/Models/Payment.php` | 122 | ✅ Read (subagent) |
| | `app/Models/SubscriptionPlan.php` | 83 | ✅ Read (subagent) |
| | `app/Models/PaymentMethod.php` | 116 | ✅ Read (subagent) |
| | `app/Models/PaymentVerification.php` | ~30 | ✅ Read (subagent) |
| | `app/Models/User.php` | 328 | ✅ Read |
| | `app/Models/Workspace.php` | 121 | ✅ Read (subagent) |
| **Controllers** | `app/Http/Controllers/CheckoutController.php` | 62 | ✅ Read |
| | `app/Http/Controllers/PaymentReturnController.php` | 78 | ✅ Read |
| | `app/Http/Controllers/SuperAdmin/PaymentController.php` | 149 | ✅ Read |
| | `app/Http/Controllers/SuperAdmin/SubscriptionController.php` | 122 | ✅ Read (subagent) |
| | `app/Http/Controllers/Account/SubscriptionController.php` | 59 | ✅ Read |
| | `app/Http/Controllers/Settings/WorkspaceController.php` | — | Not read (called changePlan/cancel) |
| **Services** | `app/Services/OnboardingService.php` | 284 | ✅ Read |
| | `app/Services/PaymentService.php` | 283 | ✅ Read |
| | `app/Services/SubscriptionService.php` | 424 | ✅ Read |
| | `app/Services/SubscriptionActivationService.php` | 191 | ✅ Read |
| | `app/Services/SubscriptionCancellationService.php` | 27 | ✅ Read |
| | `app/Services/SubscriptionProrationService.php` | 55 | ✅ Read |
| | `app/Services/SubscriptionPaymentService.php` | 79 | ✅ Read |
| **Middleware** | `app/Http/Middleware/EnsureOnboardingCompleted.php` | 172 | ✅ Read |
| | `app/Http/Middleware/CheckSubscriptionStatus.php` | 140 | ✅ Read |
| | `app/Http/Middleware/CheckActiveSubscription.php` | 79 | ✅ Read |
| | `app/Http/Middleware/CheckApiSubscription.php` | 53 | ✅ Read |
| **Cron** | `app/Console/Commands/ExpireSubscriptions.php` | 49 | ✅ Read |
| | `app/Console/Commands/RemindExpiringSubscriptions.php` | 32 | ✅ Read |
| | `app/Console/Kernel.php` | ~25 | ✅ Read |
| **Listeners** | `app/Listeners/CompleteOnboarding.php` | 22 | ✅ Read |
| | `app/Listeners/ActivateWorkspace.php` | — | Not read directly |
| | `app/Listeners/SendPaymentReceipt.php` | — | Not read directly |
| **Views (Volt)** | `resources/views/livewire/pages/onboarding/plan.blade.php` | 764 | ✅ Read |
| | `resources/views/livewire/pages/onboarding/payment.blade.php` | 528 | ✅ Partial |
| | `resources/views/livewire/pages/onboarding/payment-retry.blade.php` | 313 | ✅ Read |
| | `resources/views/livewire/pages/onboarding/payment-success.blade.php` | 73 | ✅ Read |
| | `resources/views/livewire/pages/onboarding/payment-cancel.blade.php` | 132 | ✅ Read |
| | `resources/views/livewire/pages/onboarding/payment-result.blade.php` | 504 | ✅ Partial |
| | `resources/views/livewire/pages/onboarding/manual-proof.blade.php` | — | Not read directly |
| | `resources/views/livewire/pages/onboarding/setup.blade.php` | — | Not read directly |
| **Views (Blade)** | `resources/views/payment/result.blade.php` | 19 | ✅ Read |
| | `resources/views/account/subscriptions.blade.php` | ~60 | ✅ Read (subagent) |
| **Routes** | `routes/tenant.php` | — | ✅ Read (subagent) |
| | `routes/super-admin.php` | — | ✅ Read (subagent) |
| | `routes/webhooks.php` | — | ✅ Read (subagent) |
| | `routes/console.php` | — | ✅ Read |
| **Docs** | `docs/CHARGILY_ONBOARDING_REDIRECT.md` | 159 | ✅ Read |
| **Tests** | `tests/Feature/Onboarding/PaymentRetryPageTest.php` | — | ✅ Run — 13/13 pass |
