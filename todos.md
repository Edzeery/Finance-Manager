# TODOs — تنظيف جداول الخطط (Subscription Plans)

## Phase 1 — إزالة تكرار التسعير ✅
- [x] إضافة getPrice() / getFeatureValue() + accessors
- [x] تعديل Controller / Resource / Seeders
- [x] تعديل Onboarding blades + Super-admin form
- [x] Migration: حذف `monthly_price`, `yearly_discount_percent`, `currency`

## Phase 2 — إزالة max_users / max_workspaces ✅
- [x] Accessors تقرأ من plan_features (users/workspaces)
- [x] Controller / Seeder
- [x] Super-admin form
- [x] Migration: حذف `max_users`, `max_workspaces`

## Phase 3 — استبدال limits JSON بـ plan_features ✅
- [x] إضافة `getFeatureValue()` في SubscriptionPlan model
- [x] Accessors (max_users / max_workspaces) ← plan_features
- [x] إزالة `limits` من `$fillable` و `casts`
- [x] ApiQuotaService / SubscriptionService / DeveloperController ← `getFeatureValue()`
- [x] إضافة plan_features جديدة للـ 5 limits (transactions_per_month, api_requests_*, max_api_tokens)
- [x] SubscriptionPlanResource ← إزالة `limits`
- [x] SubscriptionPlanController ← إزالة `limits` validation
- [x] Super-admin form ← إزالة limits textarea
- [x] Migration: نقل limits → plan_plan_feature + حذف `limits` column
- [x] Seeder: دمج limits في plan_features
- [x] إعادة عمود `yearly_discount_percent` مستقل + حقل في الفورم
- [x] مزامنة resources/lang/
