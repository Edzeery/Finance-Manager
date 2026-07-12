# Database Schema

> **آخر تحديث:** 2026-07-09

---

## Overview

14 migration files, 49 tables. SQLite for testing, MySQL for production.

## Migrations

| # | File | Tables Created/Modified |
|---|---|---|
| 1 | `0001_01_01_000001_create_core_tables` | users, password_reset_tokens, sessions, cache, cache_locks, jobs, job_batches, failed_jobs, personal_access_tokens, settings |
| 2 | `0001_01_01_000002_create_financial_tables` | workspaces, user_workspace, income_categories, expense_categories, incomes, expenses, debts, debt_payments, assets, financial_goals, budgets, budget_categories, zakat_records, zakat_assets, activity_logs, notifications, user_settings — ALTER users (adds current_workspace_id) |
| 3 | `0001_01_01_000003_create_rbac_tables` | roles, permissions, permission_role, role_user, workspace_role_user |
| 4 | `0001_01_01_000004_create_billing_tables` | subscription_plans, subscriptions, coupons, payments, payment_verifications, invoices, invoice_sequences, tax_rates, payment_webhook_logs, payment_methods — ALTER users (adds pending_plan_id) |
| 5 | `0001_01_01_000005_add_fulltext_indexes` | ALTER — FULLTEXT indexes on incomes, expenses, debts, assets, budgets, financial_goals (MySQL only) |
| 6 | `2026_07_05_000001_create_payment_gateways_table` | payment_gateways |
| 7 | `2026_07_06_000001_create_workspace_invitations_table` | workspace_invitations |
| 8 | `2026_07_06_101815_restructure_payment_gateways_table` | ALTER payment_gateways — drops credentials, is_active, is_public; adds fields JSON |
| 9 | `2026_07_07_000001_create_fee_tax_pivot_tables` | payment_method_tax_rate, coupon_payment_method |
| 10 | `2026_07_07_000002_add_gateway_fee_fields` | ALTER payments + invoices — adds gateway_fee, tax_added, tax_disclosed, proration_credit |
| 11 | `2026_07_07_000003_create_plan_features_and_prices_tables` | plan_features, plan_plan_feature, plan_prices |
| 12 | `2026_07_07_000004_add_plan_price_to_subscriptions` | ALTER subscriptions — adds plan_price_amount |
| 13 | `2026_07_08_103956_add_payment_id_to_invoices` | ALTER invoices — adds payment_id FK |
| 14 | `2026_07_09_175155_add_uuid_and_update_status_in_payments` | ALTER payments — adds uuid (unique), migrates status values to PaymentStatus enum |

## Core Tables

### Users & Auth
- **users** — id, name, email, password, theme, locale, timezone, 2fa columns, current_workspace_id, pending_plan_id, timestamps, soft deletes
- **personal_access_tokens** — Sanctum token management
- **sessions** — User session storage

### Workspaces (Multi-Tenancy)
- **workspaces** — id, name, slug, type, description, currency, timezone, is_active, trial_ends_at, timestamps, soft deletes
- **user_workspace** — Pivot: user_id, workspace_id (both FK cascade on delete)
- **workspace_invitations** — email, workspace_id, inviter_id, role, token, status, expires_at, timestamps

### RBAC
- **roles** — name, slug, description, guard_name, level (platform/workspace), is_system, sort_order
- **permissions** — name, slug, description, guard_name, module
- **permission_role**, **role_user**, **workspace_role_user** — Pivot tables (custom, not Spatie)

### Subscriptions & Billing
- **subscription_plans** — id, name, slug, monthly_price, yearly_discount_percent, currency, is_free, is_active, is_public, max_users, max_workspaces, limits (JSON), sort_order, button_text, button_link
- **plan_features** — Dictionary table of feature definitions (slug, name_en, name_ar, name_fr, type, icon, sort_order, is_core)
- **plan_plan_feature** — Pivot linking plans to features
- **plan_prices** — plan_id, billing_period, currency, price, is_active
- **subscriptions** — user_id, workspace_id, subscription_plan_id, status, starts_at, ends_at, trial_ends_at, canceled_at, grace_ends_at, payment_method, auto_renew, plan_price_amount, billing_period — SoftDeletes
- **coupons** — code, type (percentage/fixed), value, max_uses, used_count, min_amount, starts_at, expires_at, is_active
- **coupon_payment_method** — Pivot linking coupons to specific payment methods
- **invoices** — workspace_id, subscription_id, user_id, coupon_id, payment_id, number, status, subtotal, discount, gateway_fee, tax_added, tax_disclosed, proration_credit, total, currency, billing_period, period_start, period_end, paid_at, due_at — SoftDeletes
- **invoice_sequences** — prefix, last_number — Auto-incrementing invoice number tracker
- **payments** — uuid (unique), workspace_id, subscription_id, user_id, coupon_id, amount, method, status (PaymentStatus enum), gateway_fee, tax_added, tax_disclosed, original_amount, discount_amount, reference, transaction_id, chargily_checkout_id, gateway_reference, gateway_payload, webhook_payload, payment_method_type, notes, metadata, paid_at, failed_at, canceled_at, webhook_processed_at — SoftDeletes
- **payment_verifications** — payment_id, verified_by, status, transaction_reference, admin_notes, receipt_path, verified_at
- **payment_webhook_logs** — gateway, event_type, checkout_id, payment_id, payload, status, notes
- **payment_methods** — key, name, description, icon, type, is_active, is_public, sort_order, supported_currencies, credentials (encrypted)
- **payment_gateways** — key, name, category, icon, description, supported_currencies, sandbox, webhook, sort_order, fields (JSON), metadata (JSON)
- **payment_method_tax_rate** — Pivot linking payment methods to tax rates with charge_type (gateway_fee/tax_added/tax_disclosed), unique constraint
- **tax_rates** — id, country, name, slug, rate, type (percentage/fixed), is_active, region

### Finance
- **incomes** — workspace_id, user_id, category_id, amount, description, date, is_recurring, recurring_frequency, recurring_end_date, is_archived, receipt_path, notes — SoftDeletes + FULLTEXT
- **expenses** — workspace_id, user_id, category_id, amount, description, date, is_recurring, recurring_frequency, recurring_end_date, is_archived, receipt_path, notes — SoftDeletes + FULLTEXT
- **budgets** — workspace_id, user_id, name_ar, name_fr, name_en, type, total_amount, start_date, end_date, is_active, notes — SoftDeletes
- **budget_categories** — workspace_id, budget_id, expense_category_id, allocated_amount, spent_amount
- **debts** — workspace_id, user_id, type (owed/owing), counterparty_name, total_amount, paid_amount, due_date, status, description, reminder_date, notes — SoftDeletes + FULLTEXT
- **debt_payments** — workspace_id, debt_id, amount, payment_date, notes
- **assets** — workspace_id, user_id, type, name, description, quantity, unit_price, total_value, currency, bank_name (encrypted), account_number (encrypted), is_liquid, is_zakatable, notes — SoftDeletes + FULLTEXT
- **financial_goals** — workspace_id, user_id, name_ar, name_fr, name_en, target_amount, current_amount, target_date, status (GoalStatus), icon, color, completed_at, notes — SoftDeletes + FULLTEXT
- **zakat_records** — workspace_id, user_id, calculation_date, hijri_year, nisab_gold, nisab_silver, ...(detailed asset breakdown), total_wealth, total_zakatable, exceeds_nisab, zakat_amount, notes — SoftDeletes
- **zakat_assets** — workspace_id, zakat_record_id, asset_id, type, name, value, is_zakatable, zakatable_value, notes

### Other
- **activity_logs** — user_id, workspace_id, action, subject_type, subject_id, description, properties (JSON), ip_address, user_agent
- **notifications** — user_id, workspace_id, type, title_ar, title_fr, title_en, message_ar, message_fr, message_en, data (JSON), is_read, read_at
- **user_settings** — user_id, workspace_id, key, value

## Seeds

`database/seeders/DatabaseSeeder.php` calls (9 seeders):
1. `CategorySeeder` — Default income/expense categories
2. `CurrencySeeder` — Supported currencies
3. `EnterpriseRolePermissionSeeder` — 7 platform roles + 6 workspace roles with full permission matrices
4. `SubscriptionPlanSeeder` — 4 plans (personal, business, professional, enterprise)
5. `PaymentGatewaySeeder` — Gateway configuration
6. `PaymentMethodSeeder` — Payment method definitions
7. `MigrateToWorkspacesSeeder` — Data migration helper
8. `DemoDataSeeder` — Demo user (demo@example.com) + sample data
9. *(DatabaseSeeder.php also creates admin user directly)*

## Foreign Keys

Mixed: some tables use cascadeOnDelete (user_workspace, RBAC pivots, owned-child tables), others use `nullOnDelete` (workspace-scoped tables, billing). Financial records within workspaces nullify workspace_id on delete to preserve history. See full FK inventory in the re-audit report.

## Concerns

1. **Repeating expenses/incomes** — `is_recurring` boolean + `recurring_frequency` string stores interval but no scheduler auto-creates them
2. **Polymorphic references** in activity_logs (`subject_type/subject_id`) — no foreign keys
3. **UUID column** in payments was added late (14th migration) — ideally it would have been from creation
4. **No schema dump** in `database/schema/` — would be useful for CI/version control
