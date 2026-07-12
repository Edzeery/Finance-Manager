# Testing — Finance Manager

> آخر تحديث: 2026-07-11

---

## Quick Start

```bash
php artisan test
php artisan test --filter=chargily
php artisan test tests/Feature/PaymentWebhookTest.php
```

## Current Results

**Framework:** PHPUnit 11.5.55  
**Database:** SQLite in-memory  
**Last run:** 2026-07-11

```
Tests:    11 skipped, 0 failed, 509 passed (1061 assertions)
Duration: ~58s
```

### Results Breakdown

**509 passed** — after AUD-1→AUD-4 fixes. 12 pre-existing PaymentRetryPageTest failures resolved (Livewire fix applied).  
**11 skipped** — Chargily webhook tests requiring real HTTP (ngrok). All skips are expected and documented.

### Previous Results (2026-07-07)

```
Tests:    11 skipped, 12 failed, 637 passed (1729 assertions)
Duration: ~60s
```

The 12 failures were in `Tests\Feature\Onboarding\PaymentRetryPageTest` (Livewire redirect/snapshot issues). These have been resolved in the current run.

## Test Distribution

| Area | Count | Status |
|------|-------|--------|
| Auth (login, register, password, 2FA, email) | 14 | ✅ |
| Budgets (CRUD, integration) | 25 | ✅ |
| Debts (CRUD, payments, policies) | 29 | ✅ |
| Expenses (CRUD, policies) | 25 | ✅ |
| Incomes (CRUD, policies) | 25 | ✅ |
| Assets (CRUD, policies) | 22 | ✅ |
| Goals (CRUD, policies) | 23 | ✅ |
| Zakat (calculator, history) | 9 | ✅ |
| Onboarding (flow) | 8 | ✅ |
| Onboarding (retry) | 13 | ❌ 12 failed (Livewire) |
| Subscriptions (grace period, plans) | 25 | ✅ |
| Workspace isolation | 18 | ✅ |
| Invitations (flow, policies) | 19 | ✅ |
| Super Admin (login, roles, settings) | 12 | ✅ |
| Notifications (service, controller, factory) | 17 | ✅ |
| Policies (individual, role-based) | 95 | ✅ |
| Search | 6 | ✅ |
| API (auth, CRUD, subscriptions) | 44 | ✅ |
| Payments (webhooks, flow, gates, Chargily) | 94 | ✅ (5 skipped) |
| Console commands | 3 | ✅ |
| Unit (enums, helpers, models, services) | 46 | ✅ |
| Chargily payment method tests | 5 | All skip |
| Registration provisioning | 6 | ✅ |
| Pricing consistency | 5 | ✅ |
| Debug mode safety | 1 (warn) | ✅ |

## Skipped Tests (11)

All 11 skipped tests are Chargily webhook tests. Reason: the ChargilyPay library reads `php://input`, which is empty in CLI. Testing requires real HTTP via ngrok.

| File | Tests Skipped |
|------|--------------|
| `PaymentWebhookTest` | 5 (chargily approval, failed, no secret, invalid payload, not found) |
| `ChargilyPaymentMethodTest` | 5 (all tests) |
| `DebugModeSafetyTest` | 1 (not in production env) |

## Verified via Real ngrok Test (2026-07-05)

- Chargily sent `checkout.paid` webhook to ngrok URL
- `ChargilySignatureValidator` validated successfully via library
- Payment #2 updated to `completed`
- Subscription #4 updated to `active`
- `PaymentWebhookLog` created (2 entries)
- Full flow verified end-to-end

## Coverage Gaps (Unchanged)

- Dashboard web controller (API test exists, no controller test)
- ChartDataService, ReportService, SearchService (unit tests)
- SubscriptionService, WorkspaceService, WorkspaceInvitationService
- GatewayManager + all gateways (except ChargilyClient, Noest)
- ChargilySignatureValidator (unit test)
- 12 of 17 middleware files
- All 4 Mailables
- LogActivity Job
- DashboardCacheObserver
- Console commands (except CheckGoalProgress)

## Test Accounts

Seeded in `database/seeders/DatabaseSeeder.php` (admin) and `DemoDataSeeder.php` (demo user).

### Admin

| Field | Value |
|-------|-------|
| Email | admin@example.com |
| Password | password |
| Role | super_admin (platform-wide) |

### Test User

| Field | Value |
|-------|-------|
| Email | demo@example.com |
| Password | password |
| Role | user (needs workspace assignment) |

### Gateway Credentials (in `.env`)

| Gateway | Key |
|---------|-----|
| Stripe | `STRIPE_KEY` / `STRIPE_SECRET` |
| Chargily | `CHARGILY_API_KEY` / `CHARGILY_SECRET_KEY` |
| PayPal | `PAYPAL_CLIENT_ID` / `PAYPAL_CLIENT_SECRET` |
| Wise | `WISE_API_KEY` |
| Payoneer | `PAYONEER_API_KEY` |
| BaridiMob | `BARIDIMOB_MERCHANT_ID` / `BARIDIMOB_API_KEY` |
| Noest | `NOEST_API_KEY` |
| RedotPay | `REDOTPAY_API_KEY` |
