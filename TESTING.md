# Testing — Finance Manager

> آخر تحديث: 2026-07-20

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
**Last run:** 2026-07-20

```
Tests:    11 skipped, 0 failed, 642 passed (1693 assertions)
Duration: ~91s
```

### Results Breakdown

**642 passed** — after comprehensive 2026-07-20 audit fixes (PaymentStatus enum comparison, API Resource wrapping, test corrections, Blade business logic, config fixes, security hardening).  
**11 skipped** — Chargily webhook tests requiring real HTTP (ngrok). All skips are expected and documented.

### Previous Results

| Date | Passed | Failed | Skipped | Notes |
|------|--------|--------|---------|-------|
| 2026-07-20 | 642 | 0 | 11 | After comprehensive audit fixes |
| 2026-07-11 | 509 | 0 | 11 | After AUD-1→4 fixes |
| 2026-07-07 | 637 | 12 | 11 | Before PaymentRetryPageTest fix |

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
