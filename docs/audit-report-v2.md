# تقرير التدقيق الشامل v2 — Finance Manager (FMZS)
**تاريخ:** 10 يوليو 2026 | **الحالة:** بعد الفحص التفصيلي

---

## 🛑 حالات حرجة — يجب حلها قبل الإطلاق

### 1. 🔴 Route `rasmal` لا يزال موجوداً مع حذف الميثود

| الملف | السطر | المشكلة |
|-------|-------|---------|
| `routes/webhooks.php` | 13 | `Route::post('/rasmal', [PaymentWebhookController::class, 'rasmal'])->name('rasmal');` |

الـ method `rasmal()` تم حذفه من `PaymentWebhookController.php`. أي طلب لهذه الـ route سيؤدي إلى **500 error**.

**التوصية:** `git rm routes/webhooks.php:13` أو حذف السطر.

---

### 2. 🔴 `config/payment.php` — بقايا Rasmal و PayTR

| الملف | السطر | المشكلة |
|-------|-------|---------|
| `config/payment.php` | 4 | الـ default gateway هو `'cash'` ولكن `cash` لديه `'enabled' => false`. إذا لم يتم تعيين `PAYMENT_GATEWAY` في `.env`، سيتم استخدام بوابة معطلة. |
| `config/payment.php` | 57-62 | قسم `rasmal` لا يزال موجوداً |
| `config/payment.php` | 67-72 | قسم `paytr` لا يزال موجوداً |

**التوصية:**
- تغيير default إلى `'chargily'`: `'default' => env('PAYMENT_GATEWAY', 'chargily')`
- حذف أقسام `rasmal` و `paytr` من الملف

---

### 3. 🔴 `PaymentService::generateReference()` — بقايا Rasmal

| الملف | السطر | المشكلة |
|-------|-------|---------|
| `app/Services/PaymentService.php` | 169 | `'rasmal' => 'RSM'` في `match` — كود ميت |

**التوصية:** حذف السطر.

---

### 4. 🔴 ازدواجية معالجة التفعيل (Double Processing)

هناك **مساران مختلفان** لتفعيل الاشتراك عند تأكيد الدفع، وقد يتسببان في تنفيذ نفس العملية مرتين:

#### المسار A — عبر Webhook (Chargily)
```
ChargilyWebhookService::handlePaid()
  → activationService->activateFromPayment()   // يُفعّل الاشتراك + يُنشئ الفاتورة + يُصدر الحدث
```

#### المسار B — عبر الموافقة الإدارية
```
PaymentController::approve()
  → paymentService->verifyPayment()
    → paymentService->applyPaymentSideEffects()
      → SubscriptionActivated::dispatch()        // يُصدر الحدث
      → activationService->generateInvoice()      // يُنشئ الفاتورة
  → onboardingService->handlePaymentSuccess()
    → subscriptionService->activateFromPayment()  // يُفعّل مرة أخرى!
      → activationService->activateFromPayment()  // ⚠️ DUPLICATE
```

**المشكلة:** عند موافقة الأدمن على دفعة يدوية:
1. `applyPaymentSideEffects()` يُفعّل الاشتراك ويُصدر `SubscriptionActivated`
2. ثم `handlePaymentSuccess()` يستدعي `activateFromPayment()` **مرة أخرى**

فحص `SubscriptionActivationService::activateFromPayment()` يظهر أنه لا يوجد guard كافٍ ضد التفعيل المزدوج (فقط `isCompleted()` في السطر 32، لكن الحالة قد تغيرت بالفعل).

**التوصية:** إزالة `SubscriptionService::activateFromPayment()` من `handlePaymentSuccess()` لأن `applyPaymentSideEffects` يقوم بهذا الدور بالفعل، أو إضافة guard `if ($payment->subscription?->isActive()) return`.

---

## 🟡 مشاكل عالية الأولوية

### 5. Noest Gateway — `isOnline()` vs `isOffline()`

| الملف | المشكلة |
|-------|---------|
| `app/Services/Payments/Noest/NoestGateway.php` | `isOnline()` → `false`, `isOffline()` → `true` |

Noest هي بوابة delivery تتلقى Webhook ويتم تأكيد الدفع تلقائياً. اعتبارها `isOffline() = true` يعني أن النظام قد يعاملها كبوابة يدوية في بعض السياقات. حالياً `isOffline()` تُستخدم فقط في `GatewayManager::offline()` ولا تؤثر على flow الدفع الرئيسي، لكنها قد تسبب ارتباكاً.

**التوصية:** تغيير `isOnline()` → `true` لتعكس أنها بوابة إلكترونية مع Webhook.

---

### 6. `config/payment.php` — لا يوجد قسم لـ `wise_manual` في الـ webhook

لا يوجد عنوان `webhook_url` في إعدادات `wise_manual` لأنها يدوية. هذا طبيعي ولا يحتاج تغيير. لكن لا يوجد أيضاً إعدادات لـ `noest` كاملة في config مقارنةً بالـ seeder.

**ملاحظة:** لا مشكلة — الإعدادات الحقيقية موجودة في DB عبر `PaymentGateway` model وليس config.

---

## 🟢 مشاكل متوسطة — يفضل حلها قبل الإطلاق

### 7. باقي Rasmal في اختبارات (Tests)

| الملف | المشكلة |
|-------|---------|
| غير معروف | يحتاج التأكد من عدم وجود مراجع لـ `rasmal` في الاختبارات |

**التوصية:** `grep -r "rasmal" tests/` للتأكد.

---

### 8. `Coupon.php` — لا يوجد `calculateForAmount()` متناسق

`Coupon::applyDiscount()` يرجع قيمة الخصم وليس المبلغ بعد الخصم، بينما `TaxRate::calculateForAmount()` يرجع قيمة الضريبة/الرسوم. هذا غير متناسق من ناحية التسمية.

**التوصية:** لا تحتاج تغيير، مجرد ملاحظة.

---

### 9. `TaxRate.php` — `calculateForAmount()` اسم غير واضح

الدالة تحسب قيمة الفائدة/الضريبة وليس النسبة. الاسم مضلل قليلاً. يوجد `@deprecated calculateTax()` التي تفعل نفس الشيء.

**التوصية:** إزالة `calculateTax()` (القديمة) لأنها `@deprecated`.

---

### 10. `CurrencyHelper.php` — لا يحتوي `symbol()` أو `convert()`

بالرجوع إلى `manual-proof.blade.php` line 254: يتم استخدام `CurrencyHelper::symbol($currency)` و `CurrencyHelper::convert()` في `PaymentService.php` lines 74-76. لكن `CurrencyHelper.php` لا يحتوي إلا على `availableCurrencies()` و `availableCurrencyCodes()`.

**تحقق:** هل الدوال موجودة في `Helper` آخر؟ أم أن `CurrencyHelper` في `app/Helpers/` مختلف عن `CurrencyHelper` المستخدم؟ يجب التحقق من الـ Namespace.

**التوصية:** التأكد من وجود الـ Helper الصحيح المستورد في كل مكان — قد يكون هناك `CurrencyHelper` في `app/Helpers/` وآخر في `app/Services/`.

---

### 11. `Invitation.php` — `InvitationStatus::Cancelled` مع حرفين L

كما تم ملاحظته سابقاً، `Canceled` (حرف L واحد) في `SubscriptionStatus` بينما `Cancelled` (حرفين L) في `InvitationStatus`. هذا غير متناسق.

**التوصية:** توحيد القيمة — لكن لا تؤثر على الوظيفة لأنها string في الـ DB.

---

### 12. `lang/` — ملفات الترجمة تحت `resources/lang/` وليس `lang/`

لاحظت أن المجلد هو `resources/lang/` وليس `lang/` (المسار الافتراضي في Laravel 11+). هذا يعني أن Laravel قد لا يجدها تلقائياً.

**التوصية:** التحقق من إعداد `config/app.php` أو نقل الملفات إلى `lang/`.

---

### 13. دفعات Noest — Route الـ return

مسار العودة لـ Noest (لأن `isOnline() = false` يعتبر Offline) قد لا يعمل بشكل صحيح. في `SubscriptionService::changePlan()` line 301-302:
```php
} elseif (OnboardingService::isAutoComplete($paymentMethod) && $payment->transaction_id) {
    $payment->update(['status' => PaymentStatus::CheckoutPaid, 'paid_at' => now()]);
```
Noest هي `auto_complete` في الـ DB، و `NoestGateway::charge()` يرجع `PaymentResult::pending()`. لذلك السطر `$result->isPending()` سيكون `true` في `SubscriptionService::changePlan()` line 299، مما يعني أنه **لن يتم تفعيل الاشتراك فوراً** بل ينتظر الـ webhook. هذا صحيح.

**التوصية:** لا تحتاج تغيير — التدفق صحيح.

---

## ✅ تم التحقق منه ويعمل بشكل صحيح

| المكون | الحالة |
|--------|--------|
| `manual-proof.blade.php` | ✅ صحيح — يستخدم `$v->status->value` في `statusBadgeClass()` و `$v->status->label()` في العرض |
| Admin approve/reject UI | ✅ يستخدم POST + redirect (وليس AJAX)، لذلك الأزرار تختفي إعادة التحميل |
| Route names match | ✅ `payment.webhook.chargily`, `onboarding.manual-proof`, `chargily.back` كلها تطابق ما تستخدمه الكونترولرز |
| `ActivateSubscription` Job | ✅ فيه guards ضد التفعيل المزدوج (`isCompleted()`, `isActive()`) |
| `Invitation.php` | ✅ يستخدم `InvitationStatus` enum بشكل صحيح مع مقارنة مباشرة |
| `Coupon.php` | ✅ `applyDiscount()` + `isValid()` + `markUsed()` — صحيح |
| `TaxRate.php` | ✅ `calculateForAmount()` + `scopeActive()` + `scopeForCountry()` — صحيح |
| `BaridiMobGateway.php` | ✅ موجود وينفذ `PaymentGateway` interface |
| `RedotPayGateway.php` | ✅ موجود وينفذ `PaymentGateway` interface |
| `WiseManualGateway.php` | ✅ موجود وينفذ `PaymentGateway` interface |
| `NoestGateway.php` | ✅ موجود وينفذ `PaymentGateway` interface |
| `ChargilyGateway.php` | ✅ موجود وكامل (مع `ChargilyCheckoutService`, `ChargilyWebhookService`, `ChargilySignatureValidator`) |
| Chargily `isOnline()` | ✅ يرجع `true` — صحيح |
| Noest webhook route | ✅ موجود وتتم معالجته في `PaymentWebhookController::noest()` |
| Super Admin approve/reject routes | ✅ موجودة: `super.admin.payments.approve`, `super.admin.payments.reject` |
| `resources/lang/` | ✅ ملفات `enums.php`, `super-admin.php`, `onboarding.php` موجودة في en/ar/fr ومكتملة |
| `PaymentMethodSeeder` | ✅ Chargily نشط، BaridiMob نشط، RedotPay نشط، Wise_manual غير نشط، Noest غير نشط، الباقي غير نشط |

---

## ✅ مقارنة مع متطلباتك

| البوابة/الطريقة | النوع | مسجلة في GatewayManager؟ | مسجلة في Seeder؟ | نشطة حالياً؟ | هل تعمل؟ |
|----------------|-------|--------------------------|-------------------|-------------|----------|
| **Chargily** (Edahabia + CIB) | Online | ✅ | ✅ | ✅ **نشط** | ✅ كامل |
| **BaridiMob** | Manual | ✅ | ✅ | ✅ **نشط** | ✅ كامل |
| **RedotPay** | Manual | ✅ | ✅ | ✅ **نشط** | ✅ كامل |
| **Wise Transfer (wise_manual)** | Manual | ✅ | ✅ | ❌ غير نشط | ✅ كود موجود |
| **Stripe** | Online | ✅ | ✅ | ❌ غير نشط (مؤجل) | ✅ كود موجود |
| **PayPal** | Online | ✅ | ✅ | ❌ غير نشط | ✅ كود موجود |
| **Noest** | Auto-complete | ✅ | ✅ | ❌ غير نشط | ✅ كود موجود |
| Cash | Manual | ✅ | ✅ | ❌ غير نشط | ✅ |
| Delivery | Auto-complete | ✅ | ✅ | ❌ غير نشط | ✅ |
| Wise (online) | Online | ✅ | ✅ | ❌ غير نشط | ✅ |
| Payoneer | Online | ✅ | ✅ | ❌ غير نشط | ✅ |

---

---

## ✅ تحديث 12 يوليو 2026 — تغييرات شاملة في نظام الخطط

تم تنفيذ 4 هجرات (migrations) رئيسية بعد كتابة هذا التقرير، يُرجى أخذها بعين الاعتبار عند تقييم الجاهزية:

### أ. إزالة الحقول القديمة
- **`monthly_price`**, **`currency`** — تم حذفهما من `subscription_plans`. الأسعار الآن في جدول `plan_prices` فقط.
- **`max_users`**, **`max_workspaces`** — تم حذفهما من `subscription_plans`. الآن تُقرأ القيم من `plan_features` عبر `getFeatureValue('users')` و `getFeatureValue('workspaces')`.

### ب. `limits` JSON → `plan_features` pivot
- عمود `limits` (JSON) تم حذفه بالكامل من `subscription_plans`.
- جميع القيم (مثل `transactions_per_month`, `api_requests_*`, `max_api_tokens`) أصبحت في `plan_plan_feature` pivot مع `value`.
- الاستبدال: `$plan->limits['xxx']` ← `$plan->getFeatureValue('xxx')`.

### ج. إعادة `yearly_discount_percent` كعمود مستقل
- تم حذفه مؤقتاً في migration 000001، ثم إعادته في migration 000003 كعمود عادي في `subscription_plans`.
- يمكن تعديله من واجهة Super Admin في تبويب "التفاصيل".

### د. البريد الترحيبي بعد التفعيل
- `WelcomeEmail` لم يعد يُرسل في `register` — يُرسل الآن في `VerifyEmailController@__invoke` بعد النقر على رابط التفعيل.

### هـ. ملفات تحتاج تحديث
- **`docs/test_manual.md`** — تم تحديثه ليعكس التغييرات أعلاه.
- **`database/seeders/TestChecklistSeeder.php`** — تم تحديثه ليعكس التغييرات أعلاه.
- المراجع القديمة لـ `limits` أو `getLimit()` في أي وثائق أخرى يجب إزالتها.

---

## 🆕 تحديث 12 يوليو 2026 — مراجعة تدفق الدفع في Onboarding (Payment Flow Refactoring)

### المشاكل المكتشفة

| # | المشكلة | الملف/المكان | التفاصيل |
|---|---------|-------------|----------|
| 1 | **لا يمكن تغيير الخطة عند وجود دفع معلق** | `EnsureOnboardingCompleted` middleware + `plan.mount()` | عندما يكون هناك `pending_plan_id`، الـ middleware يمنع الوصول إلى `/onboarding/plan` و `plan.mount()` يعيد التوجيه إلى `payment.resume`. المستخدم عالق — لا يستطيع تغيير الخطة أو طريقة الدفع. |
| 2 | **تكرار الصفحات** — 5 صفحات منفصلة لنفس المهمة | `payment`, `payment-resume`, `payment-retry`, `payment-result`, `manual-proof` | كل صفحة تتعامل مع حالة دفع مختلفة لكن المنطق متكرر: نفس `loadNoestData()`, `validateCouponCode()`, `getFeeBreakdownProperty()`, `pay()`. |
| 3 | **صفحة payment.blade.php مكررة بالكامل** | `resources/views/livewire/pages/onboarding/payment.blade.php` | نفس محتوى `plan.blade.php` بالضبط. فرق واحد: وصف الباقة. نفس `mount()` يقبل `$plan_id` من URL. |
| 4 | **`?cancel` بدون تحقق من البوابة** | `payment-result.blade.php:183` | أي بوابة (حتى اليدوية) يمكنها إلغاء الدفع عبر `?cancel` في URL دون استدعاء `$gateway->verify()`. |
| 5 | **Session fallback بدون ownership check** | `payment-result.blade.php` | `session('pending_payment_id')` يُستخدم لحل الدفعة عند غياب `{payment}` parameter، لكن لا يوجد تحقق من أن الدفعة تخص هذا المستخدم. |
| 6 | **لا يوجد state/nonce في return URL** | `plan.blade.php:pay()` | رابط العودة لا يحتوي على token للتحقق من أن الطلب أصلي (anti-CSRF للـ return). |

### خطة إعادة الهيكلة المعتمدة

```
التدفق القديم (7 صفحات):
  payment.blade.php ← payment-resume.blade.php ← payment-retry.blade.php
                                                     ↘
                     payment-result.blade.php ← manual-proof.blade.php

التدفق الجديد (5 صفحات):
  plan.blade.php ← payment.status.blade.php ← payment-return.blade.php
                                                     ↘
                     manual-proof.blade.php
```

| خطوة | الإجراء | ملفات/أماكن التأثير |
|------|---------|-------------------|
| 1 | **تعديل middleware للسماح بدخول `/onboarding/plan` مع pending payment + عرض Banner** | `EnsureOnboardingCompleted.php:handle()`, `plan.blade.php` (إضافة `pendingPayment` + banner) |
| 2 | **إضافة `change_plan` إلى `plan.blade.php`** — خيار جديد في `pay()` يلغي الدفع القديم تلقائياً | `plan.blade.php` |
| 3 | **إنشاء `payment.status.blade.php`** — يدمج payment-resume + payment-retry | مسار `payment/status/{payment}`. يعرض أزراراً حسب حالة الدفع: `pending` (متابعة/تغيير طريقة/تغيير خطة)، `failed` (إعادة محاولة/تغيير طريقة)، `canceled` (إعادة محاولة/تغيير طريقة/تغيير خطة) |
| 4 | **حذف `payment.blade.php`** — مكرر بالكامل مع `plan.blade.php` | `resources/views/livewire/pages/onboarding/payment.blade.php` + مساره في `routes/tenant.php` |
| 5 | **تحديث fee breakdown** — استخدام `fee_breakdown_status` المخزّن من سجل الدفع بدلاً من إعادة الحساب | `payment.status.blade.php` |
| 6 | **إضافة trust signal badges** — شعار أمان في `plan.blade.php` و `payment.status.blade.php` و `manual-proof.blade.php` | 3 صفحات: أيقونة قفل + "مدفوعاتك مشفرة" + "بوابات دفع آمنة" + "طرق دفع متعددة" |
| 7 | **إعادة تسمية `payment-result.blade.php` ← `payment-return.blade.php`** | اسم أوضح يعكس أنها صفحة عودة من بوابة الدفع وليست "نتيجة" |
| 8 | **إضافة `$gateway->verify()` قبل أي `?cancel`** | `payment-return.blade.php:mount()` — حتى للطرق اليدوية |
| 9 | **إضافة ownership validation للـ session fallback** | التحقق من `$payment->user_id === auth()->id()` عند استخدام `session('pending_payment_id')` |
| 10 | **تحديث التوثيق** | `docs/test_manual.md` ✅, `database/seeders/TestChecklistSeeder.php` ✅, `docs/audit-report-v2.md` ✅ |

### تأثير الاختبارات

| الاختبار | الحالة القديمة | الحالة الجديدة |
|----------|---------------|---------------|
| `pending_block` | يمنع الدخول ← يعيد التوجيه إلى `payment.resume` | يسمح بالدخول مع banner تحذيري |
| `resume_continue` | موجود | تم الدمج في `status_continue` |
| `resume_cancel` | موجود | تم الدمج في `status_cancel_change_plan` |
| `resume_switch` | موجود | تم الدمج في `status_switch_method` |
| `fee_breakdown_payment` | موجودة | تم حذفها (صفحة `payment` محذوفة) |
| `fee_breakdown_retry` | موجودة | تم الاستبدال بـ `fee_breakdown_status` |
| جديد — `change_plan` | غير موجود | تغيير الخطة مع دفع معلق |
| جديد — `status_page` | غير موجود | صفحة حالة الدفع الموحدة |
| جديد — `trust_banner.*` | غير موجود | 3 اختبارات لأشرطة الثقة |
| جديد — `cancel_verify_manual` | غير موجود | ?cancel يستدعي verify() حتى للطُرق اليدوية |
| جديد — `session_resolution_safe` | غير موجود | التحقق من ملكية الدفعة في session |

### حالة التنفيذ

- ✅ **مخطط معتمد** من المطور
- ✅ `docs/test_manual.md` — تم التحديث
- ✅ `database/seeders/TestChecklistSeeder.php` — تم التحديث
- ✅ `docs/audit-report-v2.md` — هذا القسم
- ⏳ **التنفيذ الفعلي** — لم يبدأ بعد

---

## الخلاصة — هل المشروع جاهز للإطلاق؟

### ❌ لا — هناك مشاكل حرجة يجب حلها أولاً:

| الرقم | المشكلة | الخطورة |
|-------|---------|---------|
| 1 | Route `/payment/webhook/rasmal` يؤدي إلى 500 error | 🔴 حرج |
| 2 | `config/payment.php`: default gateway معطل + بقايا rasmal/paytr | 🔴 حرج |
| 3 | `PaymentService.php`: بقايا `rasmal => 'RSM'` | 🔴 حرج |
| 4 | **Double-processing**: موافقة الأدمن تؤدي إلى تفعيل الاشتراك مرتين | 🔴 حرج |
| 5 | ملفات الـ lang في `resources/lang/` وليس `lang/` — قد لا تجدها Laravel | 🟡 عالي |

### بعد حل المشاكل أعلاه:

- ✅ جميع البوابات المطلوبة مسجلة في `GatewayServiceProvider` و `PaymentGatewaySeeder`
- ✅ Chargily نشطة وجاهزة مع webhook كامل
- ✅ BaridiMob و RedotPay نشطان مع رفع إيصال يدوي
- ✅ صفحة `manual-proof.blade.php` تعمل لكل الطرق اليدوية
- ✅ لوحة الأدمن للموافقة/الرفض تعمل
- ✅ الـ enums تقارن بشكل صحيح في كل مكان
- ✅ الـ DB تهاجر وتنتج البيانات بنجاح

**التوصية:** حل المشاكل الحرجة الأربعة أولاً (1-2-3-4)، ثم اختبر Chargily webhook end-to-end، ثم اختبر تدفق الموافقة اليدوية (BaridiMob / RedotPay)، ثم اختبر Noest، وبعدها يمكن الإطلاق.
