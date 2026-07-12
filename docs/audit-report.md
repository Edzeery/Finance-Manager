# تقرير التدقيق الشامل للمشروع
## Finance Manager (FMZS)

**تاريخ التدقيق:** 10 يوليو 2026  
**الإصدار:** 1.0  
**الحالة:** تم إصلاح بعض المشاكل - لا يزال هناك ما يحتاج مراجعة

---

## فهرس المحتويات

1. [المشاكل الحرجة - حرجة (CRITICAL)](#1-مشاكل-حرجة-critical)
2. [مشاكل عالية الخطورة (HIGH)](#2-مشاكل-عالية-high)
3. [مشاكل متوسطة (MEDIUM)](#3-مشاكل-متوسطة-medium)
4. [مشاكل منخفضة / ملاحظات (LOW)](#4-ملاحظات-منخفضة-low)
5. [تناقضات في البوابات وطرق الدفع](#5-تناقضات-في-البوابات-وطرق-الدفع)
6. [الثغرات الأمنية](#6-ثغرات-أمنية)
7. [ملخص عام](#7-ملخص-عام)

---

## 1. مشاكل حرجة (CRITICAL)

### 1.1 كلاس RasmalGateway و PayTRGateway غير موجودين مع بقاء References

| الملف | السطر | المشكلة |
|-------|-------|---------|
| `app/Providers/GatewayServiceProvider.php` | (تم الإصلاح) | كانت تشير إلى `RasmalGateway`, `RasmalService` و `PayTRGateway` والملفات غير موجودة |
| `database/seeders/PaymentMethodSeeder.php` | (تم الإصلاح) | كانت تحتوي على بيانات `rasmal` و `paytr` |
| `database/seeders/PaymentGatewaySeeder.php` | (تم الإصلاح) | كانت تحتوي على بيانات `rasmal` و `paytr` |
| `app/Services/Webhooks/RasmalSignatureValidator.php` | (تم حذف الملف) | كان معتمداً على كلاس غير موجود |

**الملفات المتبقية على القرص (غير مستخدمة ولكنها موجودة كمجرد ملفات):**
- `app/Services/Payments/PayTRGateway.php` - كلاس كامل لا يستخدمه أي شيء (تم إزالة الـ registration)

**التوصية:** حذف `app/Services/Payments/PayTRGateway.php` لتنظيف المشروع.

---

### 1.2 مقارنة Enum بـ String (تم الإصلاح - 9 مواقع)

تم إصلاح 9 مواقع كان يتم فيها مقارنة `$model->status === SomeEnum::Case->value` مما يؤدي إلى `false` دائماً:

| الملف | السطر | الحالة |
|-------|-------|--------|
| `app/Services/PaymentService.php` | 239, 265 | ✅ تم |
| `app/Services/SubscriptionService.php` | 365 | ✅ تم |
| `app/Services/SubscriptionActivationService.php` | 60 | ✅ تم |
| `app/Services/Payments/Chargily/ChargilyWebhookService.php` | 117, 208 | ✅ تم |
| `resources/views/livewire/pages/onboarding/payment-retry.blade.php` | 95 | ✅ تم |
| `resources/views/livewire/pages/onboarding/payment-result.blade.php` | 221, 293 | ✅ تم |

---

### 1.3 Enum في String Interpolation والـ match (تم الإصلاح - 6 مواقع)

| الملف | السطر | المشكلة |
|-------|-------|---------|
| `resources/views/livewire/pages/onboarding/payment-result.blade.php` | 576-580 | `match($v->status)` يقارن enum مع string |
| `resources/views/livewire/pages/onboarding/manual-proof.blade.php` | 356 | `"{$v->status}"` في string interpolation + `statusBadgeClass($v->status)` بدلاً من `.value` |
| `app/Exports/GoalExport.php` | 34 | `__("goal.{$goal->status}")` |
| `app/Exports/DebtExport.php` | 35 | `__("debt.{$debt->status}")` |
| `app/Exports/AssetExport.php` | 27 | `__("asset.{$asset->type}")` |
| `app/Services/SearchService.php` | 117 | `__("asset.{$a->type}")` |

---

### 1.4 `'bank'` في Asset Seeder بدلاً من `'bank_account'` (تم الإصلاح)

| الملف | السطر | المشكلة |
|-------|-------|--------|
| `database/seeders/DemoDataSeeder.php` | 229, 230 | كانت القيمة `'bank'` بينما `AssetType` لا يحتوي على `bank` بل `bank_account` |

---

## 2. مشاكل عالية (HIGH)

### 2.1 Routes محذوفة ولكن قد تبقى مراجع

تم حذف الروoutes التالية:
- `routes/webhooks.php`: `POST /rasmal` ✅ تم
- `routes/tenant.php`: `GET /payment/rasmal/result/{payment}` ✅ تم
- `routes/tenant.php`: `GET /payment/paytr/result/{payment}` ✅ تم

**الحالة:** ✅ تم الإصلاح

---

### 2.2 `__("general.{$invoice->status}")` في الـ Blade (تم الإصلاح)

تم إصلاح 4 ملفات كانت تستخدم `__("general.{$invoice->status}")` أو `$invoice->status` كمفتاح array:

| الملف | الحالة |
|-------|--------|
| `resources/views/account/invoices-index.blade.php` | ✅ تم |
| `resources/views/account/invoices-show.blade.php` | ✅ تم |
| `resources/views/account/invoices-pdf.blade.php` | ✅ تم |
| `resources/views/super-admin/invoices.blade.php` | ✅ تم |
| `resources/views/super-admin/invoice-show.blade.php` | ✅ تم |
| `resources/views/super-admin/subscription-show.blade.php` | ✅ تم |
| `resources/views/super-admin/payments.blade.php` | ✅ تم |

---

### 2.3 كلمة مرور ثابتة في Seeder

| الملف | السطر | المشكلة |
|-------|-------|--------|
| `database/seeders/DemoDataSeeder.php` | 43, 259 | كلمة المرور `password` مثبتة في الكود ويتم طباعتها للمطور |

**التوصية:** استخدام متغير بيئة في `.env` مثل `DEMO_PASSWORD` بدلاً من الثابت.

---

### 2.4 `APP_DEBUG=false` ولكن `APP_ENV=local`

| الملف | المشكلة |
|-------|---------|
| `.env` | `APP_ENV=local` مع `APP_DEBUG=false` - هذا غير معتاد. في بيئة `local` يفضل `APP_DEBUG=true`، أو تغيير `APP_ENV=production` |

**التوصية:** ضبط `APP_DEBUG=true` للتطوير المحلي، أو وضع `APP_ENV=production` إذا كنت تختبر مثل الإنتاج.

---

## 3. مشاكل متوسطة (MEDIUM)

### 3.1 تكرار استخدام Enum Values في الـ Models

| الملف | السطر | المشكلة |
|-------|-------|--------|
| `app/Models/Payment.php` | 70-71 | يستخدم `PaymentStatus::CheckoutPending->value` و `PaymentStatus::CheckoutCanceled->value` في `where()` - هذا يعمل لأن `where()` يحتاج قيمة string للـ DB. لكن يمكن تبسيطه. **ملاحظة:** هذا ليس خطأ لأنه يتم استخدام القيم في Query Builder وليس مقارنة objects. |
| `app/Models/User.php` | 117-209 | 11 موقعاً يستخدم `->value` داخل Query Builder scopes. هذا صحيح ومتعمد. |

**التوصية:** توحيد الأسلوب - استخدام `->value` في Query Builder ومقارنة object مباشرة في المنطق.

---

### 3.2 `$payment->status->value === PaymentStatus::CheckoutCanceled->value` (مقارنة مزدوجة)

| الملف | السطر |
|-------|-------|
| `resources/views/livewire/pages/onboarding/payment-result.blade.php` | 177 |

هذا السطر يقارن قيمة الـ enum مع `->value` في كلا الجانبين. يعمل ولكنه غير ضروري - الأفضل:
```php
$payment->status === PaymentStatus::CheckoutCanceled
```

**التوصية:** تبسيط المقارنة.

---

### 3.3 `SubscriptionStatus::PastDue` في Query Builder vs Object Comparison

بعض الأماكن تستخدم `SubscriptionStatus::PastDue` مباشرة (دون `->value`) في `where()`:

| الملف | السطر |
|-------|-------|
| `app/Services/SubscriptionService.php` | 68 |

في Laravel 11+، `where('status', SubscriptionStatus::PastDue)` يعمل تلقائياً لأن الـ Query Builder يستدعي `toString()` أو يستخرج القيمة من الإينم. لكنه يعتمد على الإصدار. الأفضل استخدام `->value` للوضوح.

**التوصية:** توحيد كل `where()` على استخدام `->value`.

---

### 3.4 عدم وجود Routes مسجلة لـ `delivery` و `cash` في GatewayManager

| البوابة | مسجلة في `GatewayServiceProvider`؟ | هل لها Route؟ |
|---------|-----------------------------------|---------------|
| `chargily` | ✅ | ✅ عبر webhook |
| `baridimob` | ✅ | ❌ (manual) |
| `paypal` | ✅ | ✅ عبر webhook |
| `stripe` | ✅ | ✅ عبر webhook |
| `wise` | ✅ | ✅ عبر webhook |
| `wise_manual` | ✅ | ❌ (manual) |
| `payoneer` | ✅ | ✅ عبر webhook |
| `noest` | ✅ | ✅ عبر webhook |
| `cash` | ✅ | ❌ (غير مسجلة) |
| `delivery` | ✅ | ❌ (غير مسجلة) |
| `redotpay` | ✅ | ❌ (manual) |

**ملاحظة:** `cash` و `delivery` بوابات مسجلة ولكن ليس لها Route للـ checkout في `tenant.php`. هذا قد يكون مقصوداً إذا كانت تُعالج محلياً.

---

### 3.5 تأكيد وجود `GoalStatus::values()` بينما الـ `values()` ليست @override

| الملف | السطر |
|-------|-------|
| `app/Enums/GoalStatus.php` | 12 |

يحتوي على `public static function values()` بينما الدالة `values()` هي دالة مدمجة في PHP 8.1+ لـ `UnitEnum` والتي ترجع الـ cases. هنا يريد المطور `array_column(self::cases(), 'value')` وهو نفس `array_map` مع `->value`. الأفضل تسميتها `backedValues()`.

**التوصية:** إعادة تسمية الدالة إلى `backedValues()` أو `rawValues()`.

---

## 4. ملاحظات منخفضة (LOW)

### 4.1 أخطاء إملائية واتفاقية

| الموقع | المشكلة |
|--------|---------|
| `Canceled` vs `Cancelled` | في `SubscriptionStatus` يستخدم `Canceled` (حرف واحد L)، بينما في `GoalStatus` يستخدم `Cancelled` (حرفين L). هذا غير متناسق. |
| `InvoiceStatus::Cancelled` | يستخدم `Cancelled` بحرفين L |
| مزج الإنجليزية/الفرنسية/العربية | بعض التعليقات بالفرنسية، التوثيق غير منتظم |

---

### 4.2 لا يوجد `phpstan` أو `psalm` أو `pint` مكون

| الملف | المشكلة |
|-------|---------|
| `.phpstan.neon` أو `phpstan.neon.dist` | غير موجود |
| `phpstan` في composer.json | غير موجود في `require-dev` |
| `php-cs-fixer` | غير موجود |

**التوصية:** إضافة PHPStan للمشروع لضمان اكتشاف أخطاء الـ types مسبقاً.

---

### 4.3 مكتبة `sentry/sentry-laravel` في `require` عام

| الحزمة | النوع |
|--------|-------|
| `sentry/sentry-laravel` | `require` (غير dev) |

هذا يعني أنه حتى في بيئة `local` سيتم تحميل Sentry. الأفضل نقله إلى `require-dev`.

---

### 4.4 Gap في الترقيم بين الـ Migrations

| الملف | التسلسل |
|-------|---------|
| `2026_07_07_000001_...` | ✅ |
| `2026_07_07_000003_...` | ⚠️ قفز `000002` |
| `2026_07_07_000004_...` | ⚠️ |

**التوصية:** هذا ليس خطأ في Laravel، لكنه غير منظم. ينصح باستخدام `artisan make:migration` دائماً لضمان التسلسل الصحيح.

---

### 4.5 تحذيرات `Log::warning` و `Log::error` غير المعالجة

يوجد في `PaymentWebhookController.php` تحذيرات مسجلة لـ webhooks:
- `Chargily webhook: missing signature header`
- `Chargily webhook: invalid signature`
- `PayPal/Stripe/Wise/Payoneer webhook: Payment not found`

هذه طبيعية أثناء التطوير ولكن يجب مراقبتها في الإنتاج.

---

## 5. تناقضات في البوابات وطرق الدفع

### 5.1 البوابات المطلوبة حسب المستخدم

| البوابة | النوع | API | الحالة في المشروع |
|---------|-------|-----|-------------------|
| **Chargily** (بطاقة الذهبية + CIB) | Online | API موجود | ✅ مسجلة ومفعّلة |
| **Stripe** (مؤجل) | Online | API موجود | ✅ مسجلة ولكن غير مفعّلة |
| **PayPal** | Online | API موجود | ✅ مسجلة ولكن غير مفعّلة |
| **Noest (نواست)** | Delivery | API موجود | ✅ مسجلة ولكن غير مفعّلة |
| **BaridiMob** | Manual | رفع وصل | ✅ مسجلة ومفعّلة |
| **RedotPay** | Manual | رفع صورة | ✅ مسجلة ومفعّلة |
| **Wise Transfer** | Manual | رفع وصل | ✅ مسجلة ولكن غير مفعّلة |

### 5.2 الدفع اليدوي (Manual Proof)

صفحة `resources/views/livewire/pages/onboarding/manual-proof.blade.php` تستخدم لـ:
- **BaridiMob** - رفع وصل كصورة أو PDF + رقم المعاملة
- **RedotPay** - رفع صورة فقط + رقم المعاملة
- **Wise Transfer** - رفع وصل (مثل BaridiMob)

**ملاحظات:**
- `BaridiMobGateway.php` موجود ✅
- `RedotPayGateway.php` موجود ✅
- `WiseManualGateway.php` موجود ✅

---

## 6. ثغرات أمنية

### 6.1 معلومات حساسة في الـ Log

| الموقع | المشكلة |
|--------|---------|
| `storage/logs/laravel-2026-07-10.log` | يحتوي على Stack traces كاملة بما في ذلك مسارات الملفات المحلية (`C:\laragon\www\...`) |

**التوصية:** عدم مشاركة الـ logs مع أي شخص. أيضاً في بيئة الإنتاج يجب وضع `LOG_LEVEL=warning` أو `error`.

### 6.2 No SQL Injection

✅ جميع الاستعلامات الخام تستخدم parameterized queries (مثل `whereRaw('LOWER(email) = ?', [$email])`).

### 6.3 XSS

✅ لا يوجد استخدام لـ `{!! !!}` غير مراقب مع بيانات المستخدم.

---

## 7. ملخص عام

### تم إصلاحه بالفعل:
| المشكلة | العدد |
|---------|-------|
| مقارنة Enum بـ String (تم التحويل إلى مقارنة objects) | 9 مواقع |
| Enum في string interpolation (تم التحويل إلى `->label()`) | 6 مواقع |
| `'bank'` بدلاً من `'bank_account'` في seeder | 2 موقعين |
| إزالة Rasmal بالكامل | 15+ موقع |
| إزالة PayTR | 5 مواقع |
| إصلاح route rasmal/paytr | 3 مواقع |

### لا يزال قائماً (مقترحات للتحسين):
| المشكلة | الأولوية |
|---------|----------|
| PayTRGateway.php ملف متروك على القرص | منخفضة |
| كلمة مرور ثابتة في seeder | متوسطة |
| عدم تناسق `Canceled` vs `Cancelled` | منخفضة |
| عدم وجود PHPStan/Psalm للتأكد من types | متوسطة |
| عدم تناسق استخدام `->value` في `where()` | منخفضة |

### صحة البوابات:
- ✅ **Chargily**: كاملة (API + Webhook + Gateway)
- ✅ **Stripe**: كاملة (API + Webhook + Gateway)
- ✅ **PayPal**: كاملة (API + Webhook + Gateway)
- ✅ **Noest**: كاملة (API + Webhook + Gateway)
- ✅ **BaridiMob**: كاملة (Manual Proof + Gateway)
- ✅ **RedotPay**: كاملة (Manual Proof + Gateway)
- ✅ **Wise Transfer (wise_manual)**: كاملة (Manual Proof + Gateway)
- ⚠️ **Wise (wise - online)**: موجودة وغير مفعّلة - غير مطلوبة حسب المستخدم
- ⚠️ **Payoneer**: موجودة وغير مفعّلة - غير مطلوبة حسب المستخدم
- ⚠️ **Cash**: موجودة وغير مفعّلة - غير مطلوبة حسب المستخدم
- ⚠️ **Delivery**: موجودة وغير مفعّلة - تستخدم Noest بدلاً منها

### التوصيات النهائية:
1. حذف `app/Services/Payments/PayTRGateway.php` (ملف متروك)
2. إضافة `phpstan` للمشروع لضمان الـ type safety
3. استخدام متغير بيئة لكلمة المرور في DemoDataSeeder
4. توحيد `Canceled` vs `Cancelled` عبر جميع الـ Enums
5. نقل `sentry/sentry-laravel` إلى `require-dev`

---

### ✅ تحديث 12 يوليو 2026 — تغييرات بنية الخطط

تم تنفيذ تغييرات جوهرية على `subscription_plans` بعد كتابة هذا التقرير:
- **حذف** `monthly_price`, `currency`, `max_users`, `max_workspaces`, `limits` (JSON) من الجدول.
- **إعادة** `yearly_discount_percent` كعمود مستقل.
- جميع القيم الحَدّية (`transactions_per_month`, `api_requests_*`, `max_api_tokens`) في `plan_plan_feature` pivot.
- `WelcomeEmail` يُرسل بعد تفعيل البريد (ليس عند التسجيل).
- راجع `docs/test_manual.md` المحدَّث و `docs/audit-report-v2.md` للتفاصيل الكاملة.
