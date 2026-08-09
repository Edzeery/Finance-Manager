# تقرير التدقيق الشامل — Finance Manager
**تاريخ:** 2026-07-20  
**النطاق:** Phase 1 — تشخيص فقط (Audit-Only)  
**المنهجية:** قراءة فعلية للكود المصدري + أوامر تجريبية (`php artisan event:list`, `php artisan test`, `composer show`)

> ⚠️ **ملاحظة تحديث (2026-08-08):** هذا التقرير تجاوزه `AUDIT_2026-08-08.md` وترميمه. البنود التالية **حُلّت**:
> - **[PAY-05]** webhook log لكل البوابات → تم تنفيذ تسجيل DB لكل الـ 5 بوابات غير Chargily في `PaymentWebhookController` (حالة `received → processed/failed`).
> - **[PAY-04]** idempotency Chargily-only → تكرار نفس الحدث `(checkout_id, event_type)` يُحدّث السجل الحالي بدل إنشاء نسخة.
> - **[F-11]/[EVT]** تكرار `EventServiceProvider`/`AppServiceProvider` → التحقيق الجديد أثبت أن `EventServiceProvider` كان **كوداً ميتاً** (غير مسجّل) فحُذف؛ `CreateAdminNotification` أُصلح بثلاث دوال `handle*` قابلة للاكتشاف التلقائي.
> - **[SUB-05]/[SUB-07] و yearly pricing** → قرار منتج (2026-08-08): التسعير السنوي المستقل لكل عملة عبر `plan_prices` **مقصود** ويُلغي قرار الصيغة الحصرية. انظر `SUBSCRIPTIONS.md`.

---

## 1. البنية العامة والتهيئة (Foundation)

### [F-01] إصدارات Laravel/PHP مطابقة
- **الحالة:** ✅ سليم
- **الدليل:** `composer.json:26` — `"laravel/framework": "13.15.*"`, `composer.json:12` — `"php": "^8.3"`
- **الوصف:** `composer show` يؤكد Laravel 13.15.0 مثبت. جميع الحزم محدّثة ولا توجد حزم متوقفة.

### [F-02] ملفات البيئة
- **الحالة:** ✅ سليم
- **الدليل:** `.env.example` (142 سطر)، `.env.testing.example` (29 سطر) موجودان
- **الوصف:** `.env` موجود (محظور في Git). `.env.example` يحتوي على مفاتيح إعداد لجميع البوابات.

### [F-03] تعارض default قاعدة البيانات
- **الحالة:** ⚠️ غير متسق
- **الدليل:** `config/database.php:20` — الافتراضي `sqlite`، `.env.example:20` — `DB_CONNECTION=mysql`
- **الوصف:** بدون `.env`، يحاول المشروع الاتصال بـ SQLite بدلاً من MySQL. قد يسبب تشويشاً للمطورين الجدد.

### [F-04] مسار Windows مُ硬编码 في database config
- **الحالة:** ❌ خطير
- **الدليل:** `config/database.php:66` — `'C:\\laragon\\bin\\mysql\\...'`
- **الوصف:** مسار Laragon خاص بنظام Windows هذا. سيء على CI/CD أو أي بيئة غير Laragon.

### [F-05] SESSION_SECURE_COOKIE يكسر التطوير المحلي
- **الحالة:** ❌ خطير
- **الدليل:** `config/session.php:172` — `SESSION_SECURE_COOKIE=true`، `.env.example:32` — `true`
- **الوصف:** المتصفحات ترفض الكوكيز غير الآمنة عبر HTTP. التطوير المحلي على `localhost` بدون HTTPS لن يعمل مع هذا الإعداد.

### [F-06] تعارضات config متعددة
- **الحالة:** ⚠️ غير متسق
- **الدليل:**
  - `config/payment.php:4` — الافتراضي `chargily`، `.env.example:60` — `PAYMENT_GATEWAY=cash`
  - `config/finance.php:30` — `EXPIRY_REMINDER_DAYS=3`، `.env.example:140` — `5`
  - `config/finance.php:31` — `GRACE_PERIOD_DAYS=3`، `.env.example:141` — `7`
- **الوصف:** القيم الافتراضية في Config تختلف عن `.env.example`. بدون `.env`، القيم المطبّقة مختلفة عمّا هو متوقع.

### [F-07] مفاتيح env ناقصة في .env.example
- **الحالة:** ⚠️ غير متسق
- **الدليل:**
  - `APP_REGISTRATION_ENABLED` — مستخدم في `config/app.php:87`
  - `TRIAL_DAYS` — مستخدم في `config/finance.php:32`
  - `NOEST_WEBHOOK_SECRET` — مستخدم في `config/payment.php:55`
  - `WISE_MANUAL_ACCOUNT_EMAIL` — مستخدم في `config/payment.php:58`
  - `WISE_MANUAL_ACCOUNT_HOLDER` — مستخدم في `config/payment.php:59`
  - `INVITATION_EXPIRY_DAYS` — مستخدم في `config/invitation.php:12`
  - `RATE_LIMIT_*` (جميعها) — مستخدمة في `config/finance.php:43-60`
- **الوصف:** مفاتيح env مستخدمة في الكود لكنها غير موثقة في `.env.example`.

### [F-08] Sentry logging channel خاطئ
- **الحالة:** ⚠️ جزئي
- **الدليل:** `config/logging.php:188-196` — يستخدم `StreamHandler` يكتب لملف `sentry.json` بدلاً من handler Sentry الرسمي
- **الوصف:** integration مع `sentry/sentry-laravel` يحتاج `SentryHandler` لا `StreamHandler`.

### [F-09] OTP_LIFETIME = 0 (أبدي)
- **الحالة:** ❌ خطير أمنياً
- **الدليل:** `config/google2fa.php:17` — `'lifetime' => env('OTP_LIFETIME', 0)`
- **الوصف:** رموز 2FA لا تنتهي أبداً. إذا تم اختراق الجلسة، الرمز صالح للأبد.

### [F-10] config/statuses.php — cancelled vs canceled
- **الحالة:** ⚠️ غير متسق
- **الدليل:** `config/statuses.php:38-39` — `'cancelled'` = `danger`، `'canceled'` = `gray`
- **الوصف:** نفس الحالة بهجتين مختلفتين. قد يسبب تشويشاً بصرياً.

### [F-11] EventServiceProvider يكرر AppServiceProvider
- **الحالة:** ❌ خطير
- **الدليل:** `app/Providers/EventServiceProvider.php` يكرر تسجيلات `app/Providers/AppServiceProvider.php`
- **الوصف:** `php artisan event:list` يؤكد التكرار — كل Event يُطلق معالجه مرتين (مرة عبر AppServiceProvider ومرة عبر EventServiceProvider). يسبب تنفيذ منطق مزدوج.
- **الأثر المحتمل:** `SendPaymentReceipt`، `ActivateWorkspace`، `CompleteOnboarding`، `CreateAdminNotification` كلها تُنفَّذ مرتين لكل `PaymentCompleted`.

### [F-12] مسارات تطبيق helpers
- **الحالة:** ✅ سليم
- **الدليل:** `composer.json:56` — `"app/Helpers/helpers.php"` في autoload files
- **الوصف:** يحتوي `currency_format()` و `locale_name()`.

### [F-13] Service Providers
- **الحالة:** ✅ سليم
- **الدليل:** 8 Service Providers: `AppServiceProvider`، `BindingServiceProvider`، `GatewayServiceProvider`، `PolicyServiceProvider`، `RateLimiterServiceProvider`، `SettingsServiceProvider`، `EventServiceProvider`، `ModelEventServiceProvider`، `VoltServiceProvider`
- **الوصف:** `BindingServiceProvider` يربط 7 repository interfaces + 4 service interfaces. `GatewayServiceProvider` يسجل 11 بوابة.

---

## 2. البنية متعددة المستأجرين (Multi-Tenancy)

### [MT-01] آلية العزل الرئيسية
- **الحالة:** ✅ سليم
- **الدليل:** `app/Models/Scopes/WorkspaceScope.php:14-36` — global scope يصفّر حسب `config('app.current_workspace')`
- **الوصف:** `SetWorkspace` middleware (`app/Http/Middleware/SetWorkspace.php:11-45`) يضبط config في كل طلب. `BelongsToWorkspace` trait يضبط `workspace_id` تلقائياً عند الإنشاء.

### [MT-02] عزل كامل لـ 19 نموذج مالي
- **الحالة:** ✅ سليم
- **الدليل:** كل النماذج المالية (Income, Expense, Budget, Debt, Asset, FinancialGoal, ZakatRecord, ZakatAsset, Invoice, Payment, PaymentVerification, Subscription, Notification, IncomeCategory, ExpenseCategory, ActivityLog, UserSetting, BudgetCategory, DebtPayment) تستخدم `BelongsToWorkspace` + `WorkspaceScope`.

### [MT-03] نماذج بدون عزل (مقبولة)
- **الحالة:** ✅ سليم
- **الدليل:** `User`, `Role`, `Permission`, `SubscriptionPlan`, `PlanFeature`, `PlanPrice`, `PaymentMethod`, `PaymentGateway`, `Setting`, `Workspace`
- **الوصف:** هذه نماذج عالمية (ليست خاصة بمستأجر) — العزوم صحيح.

### [MT-04] Invitation model ينقصها العزل
- **الحالة:** ⚠️ جزئي
- **الدليل:** `app/Models/Invitation.php` — لها `workspace_id` في fillable لكن لا تستخدم `BelongsToWorkspace` ولا `WorkspaceScope`
- **الوصف:** الموديل لا يُلقّح تلقائياً ولا يُصفّى. لكن الـ Controllers تُعاملها بشكل يدوي.

### [MT-05] ApiUsageLog ينقصها العزل
- **الحالة:** ⚠️ جزئي
- **الدليل:** `app/Models/ApiUsageLog.php:17,37` — لها `workspace_id` لكن لا تستخدم `BelongsToWorkspace` ولا `WorkspaceScope`
- **الوصف:** سجل تتبع API فقط (يُكتب فقط لا يُقرأ من سياق المستأجر). الخطورة منخفضة.

### [MT-06] super admin يرى كل البيانات
- **الحالة:** ✅ مقصود
- **الدليل:** `WorkspaceScope.php:18-19` — يعود فارغاً عندما `config('app.current_workspace')` = null. `SetWorkspace.php:41` — لا يضبط workspace للـ super admin.
- **الوصف:** هذا سلوك مقصود —_super admin يرى كل البيانات عبر كل العملاء.

### [MT-07] withoutWorkspace() استخدام صحيح
- **الحالة:** ✅ سليم
- **الدليل:** لا يوجد أي controller موجّه للمستأجر يستخدم `withoutWorkspace()`. الاستخدام مقتصر على super admin, services, jobs, commands.

---

## 3. RBAC والصلاحيات

### [RBAC-01] نظام مخصص (ليس Spatie)
- **الحالة:** ✅ سليم
- **الدليل:** `app/Models/Role.php`، `app/Models/Permission.php`، `config/roles.php`
- **الوصف:** نظام ثلاثي الطبقات: platform roles + workspace roles + permissions. لا توجد حزمة خارجية.

### [RBAC-02] super admin bypass عبر Gate::before
- **الحالة:** ✅ سليم
- **الدليل:** `app/Providers/PolicyServiceProvider.php:34-36` — `Gate::before` يُعيد `true` لكل policies للمستخدم `super_admin`.
- **الوصف:** مع `WorkspaceScope` الذي يعود فارغاً للـ super admin، هناك двойная حماية.

### [RBAC-03] فجوة صلاحيات في category routes
- **الحالة:** ⚠️ متوسط
- **الدليل:** `routes/tenant.php:109,136` — `Route::resource('categories', ...)` بدون per-action middleware
- **الوصف:** يرث فقط `permission:income.view` من المجموعة الأب. مستخدم مع `income.view` فقط يمكنه إنشاء/تعديل/حذف التصنيفات.

### [RBAC-04] API routes بدون per-action permission middleware
- **الحالة:** ⚠️ متوسط
- **الدليل:** `routes/api.php:39-46` — `apiResource` لجميع النماذج المالية بدون `permission:` middleware
- **الوصف:** يعتمد فقط على `auth:sanctum` + `ability` + `workspace` + controller-level authorization (إن وُجد).

### [RBAC-05] Policy لا تتحقق من workspace_id
- **الحالة:** ⚠️ متوسط
- **الدليل:** `app/Policies/IncomePolicy.php:10-13` — يتحقق من `user_id` فقط لا `workspace_id`
- **الوصف:** إذا تجاوز Scope، السياسات وحدها لا تمنع عرض بيانات مستأجر آخر. الاعتماد على `WorkspaceScope` كخط دفاع واحد.

### [RBAC-06] workspace create/store بدون permission middleware
- **الحالة:** ⚠️ منخفض
- **الدليل:** `routes/tenant.php:269-272` — بدون `permission:` middleware ولا `throttle`
- **الوصف:** يعتمد على `WorkspacePolicy::create()` فقط.

### [RBAC-07] workspace switch بدون throttle
- **الحالة:** ⚠️ منخفض
- **الدليل:** `routes/tenant.php:346-347` — بدون `permission:` أو `throttle` middleware

---

## 4. الاشتراكات والفوترة

### [SUB-01] بنية الخدمات
- **الحالة:** ✅ سليم
- **الدليل:** 6 خدمات منفصلة: `SubscriptionService`, `SubscriptionActivationService`, `SubscriptionProrationService`, `SubscriptionCancellationService`, `SubscriptionPaymentService`, `PaymentService`, `OnboardingService`
- **الوصف:** فصل جيد للمسؤوليات.

### [SUB-02] حماية الدفعات المعلقة
- **الحالة:** ✅ سليم
- **الدليل:** `app/Services/SubscriptionService.php:70-76` — `hasPendingPayment()` يتحقق قبل تغيير الخطة
- **الوصف:** يُستدعى في `changePlan()` و `processFreePlan()` و `processTrialPlan()`.

### [SUB-03] فساد — `pendingSubscription()` يستخدم enum خاطئ
- **الحالة:** ❌ خطير
- **الدليل:** `app/Models/User.php:223` — يقارن `PaymentStatus::CheckoutPending` بدلاً من `SubscriptionStatus`
- **الوصف:** حالة الاشتراك المعلّق هي `past_due` في `SubscriptionStatus`، لا في `PaymentStatus`. المقارنة لن تنجح أبداً.

### [SUB-04] فساد — `resumeSubscription` لا يُعيد الحالة
- **الحالة:** ❌ خطير
- **الدليل:** `app/Http/Controllers/Account/SubscriptionController.php:128-130` — يُعيّن `canceled_at = null` لكن لا يُعيد `status` إلى `Active`
- **الوصف:** الاشتراك يبقى `Canceled` مع `grace_ends_at` في المستقبل. بعد انتهاء فترة السماح، يموت.

### [SUB-05] فساد — `canDowngrade()` تتجاهل billing period للخطة المستهدفة
- **الحالة:** ⚠️ متوسط
- **الدليل:** `app/Services/SubscriptionService.php:137` — يستخدم `$targetPlan->monthly_price` دائماً حتى لو كان المستخدم ينتقل لـ yearly
- **الوصف:** مقارنة السعر خاطئة — الخطة المستهدفة تُقارَن بسعرها الشهري دائماً.

### [SUB-06] فساد — `sendPlanChangeEmail` تقارن بالسعر الشهري فقط
- **الحالة:** ⚠️ منخفض
- **الدليل:** `app/Services/SubscriptionService.php:482-483` — تقارن `monthly_price` للخطتين مهما كان billing period
- **الوصف:** مشترك سنوي قد يتلقى بريد "ترقية" حتى لو انخفض سعره السنوي.

### [SUB-07] فساد — التجديد التلقائي غير مجدول
- **الحالة:** ❌ خطير
- **الدليل:** `RenewSubscriptions` command موجود (`app/Console/Commands/RenewSubscriptions.php`) لكنه غير مُضاف لـ Scheduler في `app/Console/Kernel.php`
- **الوصف:** الاشتراكات ذات التجديد التلقائي لن تُجّد تلقائياً.

### [SUB-08] فساد — تجديد Pending لا يمدد تاريخ الانتهاء
- **الحالة:** ❌ خطير
- **الدليل:** `app/Console/Commands/RenewSubscriptions.php:144-146` — يُعيد `'renewed'` لكن لا يُحدّث `ends_at`
- **الوصف:** الاشتراك سينتهي والتجديد معلّق في البوابة.

### [SUB-09] تكرار منطق التحقق من الكوبون
- **الحالة:** ⚠️ متوسط
- **الدليل:** `SubscriptionService::validateColon()` (سطر 35-68) و `PaymentService::resolveCoupon()` (سطر 140-168)
- **الوصف:** منطق متطابق في مكانين مختلفين — maintenance risk.

### [SUB-10] تكرار حساب السعر
- **الحالة:** ⚠️ منخفض
- **الدليل:** الصيغة `$billingPeriod === 'yearly' ? $plan->yearly_price : $plan->monthly_price` مكررة في 6+ أماكن
- **الوصف:** `SubscriptionPlan::getPrice()` موجود لكن نادراً ما يُستخدم.

### [SUB-11] Canceled و Cancelled معاً في Enum
- **الحالة:** ⚠️ منخفض
- **الدليل:** `app/Enums/SubscriptionStatus.php:10-11`
- **الوصف:** نفس المفهوم بهجتين مختلفتين. قد يسبب خطأ في المقارنة.

### [SUB-12] trial expiry لا يدخل grace period
- **الحالة:** ⚠️ منخفض
- **الدليل:** `app/Console/Commands/ExpireSubscriptions.php:49-58` — يُعيّن `Expired` بدون grace period
- **الوصف:** الاشتراكات المدفوعة تدخل grace، التجريبية لا تدخل. هذا قرار تصميم لكنه غير متسق.

---

## 5. نظام الدفع

### [PAY-01] بوابات الدفع المسجلة
- **الحالة:** ✅ 11 بوابة مسجلة
- **الدليل:** `GatewayServiceProvider.php:57-73`
- **الوصف:**

| بوابة | مسجلة | كلاس | Webhook | توقيع | حالة |
|-------|:-----:|:----:|:-------:|:-----:|:----:|
| Chargily | ✅ | ✅ | ✅ | ✅ HMAC-SHA256 | مفعّلة بالكامل |
| BaridiMob | ✅ | ✅ | ❌ | — | Offline/يدوي |
| PayPal | ✅ | ✅ | ✅ | ⚠️ HMAC مخصص | مفعّلة (توقيع غير معياري) |
| RedotPay | ✅ | ✅ | ❌ | — | Offline/محفظة |
| Stripe | ✅ | ✅ | ✅ | ✅ Stripe v1 | مفعّلة بالكامل |
| Wise API | ✅ | ✅ | ✅ | ⚠️ HMAC مخصص | مفعّلة (توقيع غير معياري) |
| Wise Manual | ✅ | ✅ | ❌ | — | Offline |
| Payoneer | ✅ | ✅ | ✅ | ⚠️ HMAC مخصص | مفعّلة (توقيع غير معياري) |
| Cash | ✅ | ❌ | ❌ | — | Offline |
| Delivery | ✅ | ❌ | ❌ | — | Offline |
| Noest | ✅ | ✅ | ✅ | ✅ HMAC-SHA256 | مفعّلة بالكامل |

### [PAY-02] PaymentTransitionValidator — كود ميّت
- **الحالة:** ❌ خطير
- **الدليل:** `app/Services/Payments/PaymentTransitionValidator.php` — معرّف لكن لا يُستدعى في أي مكان
- **الوصف:** جميع الانتقالات تتم عبر `->update()` مباشرة في 11+ مكان بدون تحقق من صحة الانتقال.

### [PAY-03] منطق أعمال في Blade/Livewire views
- **الحالة:** ❌ خطير
- **الدليل:**
  - `resources/views/livewire/pages/onboarding/manual-proof.blade.php:82,137,218`
  - `resources/views/livewire/pages/onboarding/payment-status.blade.php:93,190`
  - `resources/views/livewire/pages/onboarding/payment-result.blade.php:189,209,284`
- **الوصف:** `->update(['status' => ...])` مباشرة في القوالب — انتهاك لمعمارية Service Layer. لا يوجد تحقق من صحة الانتقال ولا تنفيذ side effects.

### [PAY-04] webhook idempotency فقط لـ Chargily
- **الحالة:** ⚠️ متوسط
- **الدليل:** فقط `ChargilyWebhookService` يتحقق من `alreadyProcessed()`. باقي البوابات لا تتحقق.
- **الوصف:** إعادة إرسال webhook لنفس الدفع قد تُعالج مرتين لـ Stripe/PayPal/Wise/Payoneer.

### [PAY-05] لا يوجد webhook log لكل البوابات
- **الحالة:** ⚠️ متوسط
- **الدليل:** `PaymentWebhookController.php:186` — يسجّل في Laravel Log فقط، لا في `PaymentWebhookLog` model. فقط Chargily ينشئ `PaymentWebhookLog`.
- **الوصف:** لا يمكن تتبع أو عرض webhook events للبوابات الأخرى.

### [PAY-06] PayPal signature validation غير معياري
- **الحالة:** ⚠️ متوسط
- **الدليل:** `PayPalSignatureValidator.php:20-28` — يستخدم HMAC بدلاً من PayPal certificate chain verification
- **الوصف:** يعمل لكنه لا يتبع معايير PayPal الرسمية.

### [PAY-07] مصادر بيانات الاعتماد
- **الحالة:** ✅ سليم
- **الدليل:** `HasGatewaySettings` trait (`app/Services/Payments/Concerns/HasGatewaySettings.php:11-22`)
- **الوصف:** DB أولاً → `.env` كـ fallback. بيانات الاعتماد مشفّرة في DB.

### [PAY-08] Manual Transfer flow
- **الحالة:** ✅ سليم
- **الدليل:**
  - رفع الإثبات: `OnboardingService::submitManualPaymentProof()` — `app/Services/OnboardingService.php:351-366`
  - موافقة إدارية: `SuperAdmin\PaymentController::approve()` — `:116-158`
  - تفعيل الاشتراك: `PaymentService::verifyPayment()` → `applyPaymentSideEffects()` — `:225-292`
- **الوصف:** يفعّل الاشتراك فعلياً بعد الموافقة.

### [PAY-09] تثبيت الحالة PaymentStatus enum
- **الحالة:** ✅ سليم
- **الدليل:** `app/Models/Payment.php:40` — `PaymentStatus::class` cast
- **الوصف:** جميع حالات الدفع تستخدم Enum. `SubscriptionStatus` يستخدم أيضاً Enum (لكن به Canceled/Cancelled مزدوج).

---

## 6. وحدة الزكاة

### [ZAK-01] بنية الوحدة
- **الحالة:** ✅ سليم
- **الدليل:**
  - `app/Services/ZakatCalculationService.php` — محرك الحساب (211 سطر)
  - `app/Services/GoldPriceService.php` — جلب الأسعار (140 سطر)
  - `app/Http/Controllers/Zakat/ZakatController.php` — controller (324 سطر)
  - `app/Models/ZakatRecord.php` + `ZakatAsset.php` — النماذج
  - `config/zakat.php` — الإعدادات (35 سطر)
  - 4 views: calculator, history, report, _nav

### [ZAK-02] صحة الحساب الشرعية
- **الحالة:** ✅ صحيح
- **الدليل:** `ZakatCalculationService.php`
  - نسبة 2.5% ✅ (سطر 12)
  - Nisab = min(gold, silver) ✅ (سطر 78-80)
  - خصم الديون قبل الحساب ✅ (سطر 56-58)
  - المستحققات لا تُحتسب في zakatable ✅ (سطر 52-54)
  - العقارات غير zakatable افتراضياً ✅ (سطر 50-54)
  - يدعم 6 عيارات ذهب ✅ (config سطر 11-18)

### [ZAK-03] GoldPriceService — manual override يعمل
- **الحالة:** ✅ مُصحّح
- **الدليل:** `GoldPriceService.php:29-37,76-81` — يقرأ من `Setting::get()` بدلاً من `config()`
- **الوصف:** تم إصلاحه في هذه الجلسة.

### [ZAK-04] اكتمال الوحدة
- **الحالة:** ✅ مكتمل تقريباً
- **الدليل:** calculator UI مكتمل (603 سطر)، حساب مكتمل، حفظ سجلات، تاريخ، تقرير، جلب أسعار تلقائي، يدوي override
- **الوصف:**
  - ✅ Calculator UI + multi-karat gold
  - ✅ Auto-fetch prices (API + manual override)
  - ✅ Save records + assets
  - ✅ History (searchable, filterable, paginated)
  - ✅ Report (full breakdown)
  - ⚠️ Export — زر موجود لكن لا يوجد controller action مخصص
  - ❌ Delete records — `SoftDeletes` موجود لكن لا UI ولا route

### [ZAK-05] أخطاء محتملة في الحساب
- **الحالة:** ⚠️ جزئي
- **الدليل:** `ZakatCalculationService.php:78-80` — عندما لا يوجد ذهب، Nisab يعتمد على الفضة فقط. بعض العلماء يقولون Nisab = الأعلى بين الذهب والفضة.
- **الوصف:** هذا خلاف فقهي. الموقع يعتمد على `min(gold, silver)` وهو مقبول عند جمهور العلماء.

---

## 7. الأمان

### [SEC-01] API Resources موجودة لكن لا تُستخدم
- **الحالة:** ❌ خطير
- **الدليل:** 16 API Resource معرّفة في `app/Http/Resources/` لكن جميع API controllers تُعيد Models مباشرة عبر `response()->json($model)`
- **الوصف:** `UserResource`، `PaymentResource`، `SubscriptionResource`، إلخ — كلها موجودة لكن `AuthController::login()` يُعيد `$user` مباشرة، `AssetController` يُعيد `$asset` مباشرة، إلخ.
- **الأثر:** تسريب كل الحقول غير المخفية (timestamps, pivot data, internal fields) كـ JSON.

### [SEC-02] PII غير مشفّر في قاعدة البيانات
- **الحالة:** ⚠️ متوسط
- **الدليل:**
  - `Payment.gateway_payload` — قد يحتوي على توقيعات بوابة (`app/Models/Payment.php:46`)
  - `Payment.webhook_payload` — بيانات webhook خام (`app/Models/Payment.php:47`)
  - `LoginAttempt.email` — بريد في نص واضح (`app/Models/LoginAttempt.php:14`)
  - `Invitation.email` — بريد مُدعى في نص واضح (`app/Models/Invitation.php:21`)
  - `Debt.counterparty_name` — اسم طرف ثالث (`app/Models/Debt.php:25`)
- **الوصف:** هذه الحقول لا تشفّر رغم أنها بيانات حساسة.

### [SEC-03] تشفير سليم في أماكن أخرى
- **الحالة:** ✅ سليم
- **الدليل:**
  - `User.google2fa_secret` — `encrypted` ✅
  - `User.two_factor_recovery_codes` — `encrypted:array` ✅
  - `Asset.account_number` — `encrypted` ✅
  - `Setting::setSecret()` — `Crypt::encryptString` ✅
  - `PaymentMethod` credentials — `encrypt()` helper ✅

### [SEC-04] theme switch بدون auth
- **الحالة:** ⚠️ منخفض
- **الدليل:** `routes/web.php:19-20` — `POST /theme/switch` — لا يوجد `auth` middleware
- **الوصف:** أي زائر غير مصادق عليه يمكنه تبديل السمة. خطورة منخفضة.

### [SEC-05] currency switch بدون auth
- **الحالة:** ⚠️ منخفض
- **الدليل:** `routes/web.php:22` — `POST /currency/{currency}` — لا يوجد `auth` middleware
- **الوصف:** أي زائر يمكنه تغيير العملة في الجلسة.

### [SEC-06] 2FA disable بدون permission
- **الحالة:** ⚠️ منخفض
- **الدليل:** `routes/super-admin.php:212-213` — `PUT /super-admin/settings/2fa/disable` — بدون `permission:` middleware
- **الوصف:** أي super admin يمكنه تعطيل 2FA للمنصة بأكملها.

### [SEC-07] لا توجد أسرار مُ硬coded في الكود
- **الحالة:** ✅ سليم
- **الدليل:** `grep` لـ `test_[a-zA-Z0-9]{20,}` و `sk_live` و `pk_live` في `app/` — نتائج صفرية
- **الوصف:** كل الأسرار تأتي من `.env`. مفاتيح Chargily في `.env.example` هي test keys فقط.

### [SEC-08] Validation مكتملة في FormRequests
- **الحالة:** ✅ سليم
- **الدليل:** 39 FormRequest class موجودة تغطي جميع عمليات CRUD + auth + subscriptions
- **الوصف:** كلها ترث من `ApiRequest` base. تحقق كافٍ في كل مكان.

---

## 8. الأحداث والمستمعون

### [EVT-01] تكرار تسجيل Events
- **الحالة:** ❌ خطير
- **الدليل:** `app/Providers/EventServiceProvider.php` + `app/Providers/AppServiceProvider.php`
- **الوصف:** كلاهما يسجل نفس المستمعين: `PaymentCompleted` → 4 مستمعين، `SubscriptionActivated` → 1، `Registered` → 1. `php artisan event:list` يؤكد التكرار.

### [EVT-02] PaymentCompleted — تأثير التكرار
- **الحالة:** ❌ خطير
- **الدليل:** `PaymentCompleted` event → `SendPaymentReceipt` + `ActivateWorkspace` + `CompleteOnboarding` + `CreateAdminNotification` — كلها تُنفَّذ مرتين
- **الوصف:** `PaymentWebhookController::handleApproved()` يستدعي `handlePaymentSuccess()` ثم يطلق `PaymentCompleted` → `CompleteOnboarding` listener يستدعي `markPlanConfirmed()` مرة ثانية.

### [EVT-03] أحداث بدون مستمعين
- **الحالة:** ⚠️ متوسط
- **الدليل:**
  - `PaymentFailed` — مُطلق في `ChargilyWebhookService:159,183,207` — بدون listener
  - `InvitationCreated` — مُطلق في `WorkspaceInvitationService:59` — بدون listener
  - `InvitationAccepted` — مُطلق في `WorkspaceInvitationService:104` — بدون listener
  - `InvitationDeclined` — مُطلق في `WorkspaceInvitationService:125` — بدون listener
  - `InvitationExpired` — مُطلق في `WorkspaceInvitationService:192,222` — بدون listener
- **الوصف:** هذه الأحداث كود ميّت — لا تفعل شيئاً عند إطلاقها.

### [EVT-04] ModelEventServiceProvider — ActivityLog
- **الحالة:** ✅ سليم
- **الدليل:** `app/Providers/ModelEventServiceProvider.php` — 9 نماذج تشحن `LogActivity` job على created/updated/deleted/restored
- **الوصف:** `DashboardCacheObserver` على 6 نماذج مالية.

### [EVT-05] Duplicate onboarding completion
- **الحالة:** ⚠️ متوسط
- **الدليل:** `PaymentWebhookController::handleApproved()` (سطر 60) → `handlePaymentSuccess()` + `PaymentCompleted` event → `CompleteOnboarding` listener
- **الوصف:** `markPlanConfirmed()` يُستدعى مرتين في كل مسار نجاح دفع.

---

## 9. الاختبارات

### [TST-01] اختبارات الوحدة — نجاح كامل
- **الحالة:** ✅ سليم
- **الدليل:** `php artisan test --testsuite=Unit` — 47 class تمر بنجاح
- **الوصف:** 21 ملف اختبار وحدة يغطي: enums, models, helpers, gateways, settings, activity logs, services.

### [TST-02] اختبارات الميزات — معطّلة
- **الحالة:** ❌ خطير
- **الدليل:** `php artisan test` — Fatal error في `app/Notifications/WorkspaceInvitation.php:49`
- **الوصف:** `WorkspaceInvitation::preferredLocale()` signature لا يتوافق مع `HasLocalePreference` interface في Laravel 13. الخطأ يحطم أي اختبار يرسل دعوة.

### [TST-03] اختبارات فاشلة قبل الخطأ
- **الحالة:** ⚠️ متوسط
- **الدليل:** 4 اختبارات تفشل قبل الخطأ:
  - `Admin\UserControllerTest` — toggle status, destroy user
  - `Api\SubscriptionApiTest` — list plans
  - `Api\WorkspaceApiTest` — create workspace
  - `Auth\AuthenticationTest` — login screen, navigation menu

### [TST-04] فجوات التغطية — الزكاة
- **الحالة:** ❌ خطير
- **الدليل:** لا يوجد `ZakatCalculationServiceTest` — المحرك الحسابي الرئيسي بدون اختبارات
- **الوصف:** حساسية الحسابات المالية تتطلب تغطية اختبارية عالية.

### [TST-05] فجوات التغطية — areas أخرى
- **الحالة:** ⚠️ متوسط
- **الدليل:** لا اختبارات لـ: CurrencyHelper, GoldPriceService, ReportController, rate limiting, file uploads
- **الوصف:** مناطق مهمة بدون تغطية.

### [TST-06] اختبار zakat authorization يفشل بصمت
- **الحالة:** ❌ خطير
- **الدليل:** `tests/Feature/Zakat/ZakatControllerTest.php:126` — `assertOk()` بدلاً من `assertForbidden()`
- **الوصف:** الاختبار يتحقق أن المستخدم **يستطيع** رؤية سجل زكاة مستخدم آخر — عكس القصد.

---

## 10. الواجهة والتدويل (i18n / RTL)

### [I18N-01] اللغات المتاحة
- **الحالة:** ✅ 3 لغات مكتملة
- **الدليل:** `resources/lang/{ar,en,fr}/` — كل لغة بها 43 ملف
- **الوصف:** ~3399 مفتاح عربي، ~3399 إنجليزي، ~3399 فرنسي. التغطية متساوية تقريباً.

### [I18N-02] RTL — الدعم الأساسي
- **الحالة:** ✅ سليم
- **الدليل:** `resources/views/layouts/app.blade.php:3` — `dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}"`
- **الوصف:** Bootstrap يدعم RTL. خطوط Inter + Tajawal مُحمّلة.

### [I18N-03] RTL — مشاكل CSS
- **الحالة:** ⚠️ جزئي
- **الدليل:** ~10+ مواقع تستخدم خصائص CSS فيزيائية (لا منطقية):
  - `components/api-endpoint.blade.php:5` — `text-align:right`
  - `livewire/pages/onboarding/payment-result.blade.php:449,595` — `margin-right`, `text-align:right`
  - `super-admin/users.blade.php:424,476` — `margin-right`, `text-align:right`
  - `settings/developer.blade.php:361` — `text-align:right`
- **الوصف:** هذه في صفحات مساعدة (super-admin, developer settings) لا في التدفق الأساسي للمستخدم.

### [I18N-04] Zakat translations مكتملة
- **الحالة:** ✅ سليم
- **الدليل:** `resources/lang/{ar,en,fr}/zakat.php` — 79 مفتاح في كل لغة

---

## 11. جاهزية الإنتاج

### [PRD-01] APP_DEBUG = false في .env.example
- **الحالة:** ✅ سليم
- **الدليل:** `.env.example:4` — `APP_DEBUG=false`

### [PRD-02] Rate Limiting شامل
- **الحالة:** ✅ سليم
- **الدليل:** 16 rate limiter معرّف في `RateLimiterServiceProvider`. `throttle:web-crud` على 15+ route، `throttle:api` على API، `throttle:webhook` على webhooks، `throttle:login` و `throttle:register` على auth.
- **الوصف:** حتى Zakat routes ترث `throttle:web-crud`.

### [PRD-03] Backup configuration
- **الحالة:** ⚠️ جزئي
- **الدليل:** `config/backup.php` — Spatie Backup مُعد بالكامل. قاعدة بيانات + ملفات.
- **الوصف:** التخزين محلي فقط (`local` disk). لا يوجد S3 أو cloud. في production، فقدان الخادم = فقدان النسخ الاحتياطية.

### [PRD-04] Sentry integration
- **الحالة:** ✅ سليم
- **الدليل:** `config/sentry.php` — DSN من env، `send_default_pii = false`
- **الوصف:** لكن `sql_bindings = true` قد يسجّل بيانات حساسة في breadcrumbs.

### [PRD-05] Logging structure
- **الحالة:** ✅ سليم
- **الدليل:** قنوات مخصصة: `payments`، `subscriptions`، `auth`، `queue`، `sentry` — كلها بتنسيق JSON

### [PRD-06] Hardcoded Windows path
- **الحالة:** ❌ خطير
- **الدليل:** `config/database.php:66` — `'C:\\laragon\\bin\\mysql\\...'`
- **الوصف:** سيء على CI/CD، Docker، أو أي بيئة غير Laragon.

---

## ملخص تنفيذي

| الوحدة | نسبة الاكتمال | التقييم |
|--------|:-----------:|:-------:|
| البنية العامة (Foundation) | 85% | ⚠️ تعارضات config + مسار Windows |
| متعدد المستأجرين (Multi-Tenancy) | 95% | ✅ قوي مع فجوات بسيطة |
| RBAC والصلاحيات | 88% | ⚠️ فجوات في category routes + API |
| الاشتراكات والفوترة | 70% | ❌ أخطاء في renewal + resume |
| نظام الدفع | 75% | ❌ PaymentTransitionValidator ميّت + منطق في views |
| وحدة الزكاة | 92% | ✅ شبه مكتمل |
| الأمان | 80% | ⚠️ API Resources غير مستخدمة + PII غير مشفّر |
| الأحداث والمستمعون | 60% | ❌ تكرار + أحداث ميّتة |
| الاختبارات | 40% | ❌ معطّلة جزئياً + فجوات كبيرة |
| التدويل (i18n/RTL) | 90% | ✅ مكتمل مع مشاكل RTL بسيطة |
| جاهزية الإنتاج | 82% | ⚠️ backup محلي فقط + session secure |

---

## قائمة "غير قابل للتحقق"

1. **تشغيل الاختبارات بشكل كامل** — معطّل بسبب خطأ `WorkspaceInvitation::preferredLocale()` type error. لا يمكن التحقق من اختبارات Feature.
2. **عملية التجديد التلقائي فعلياً** — `RenewSubscriptions` command غير مجدول في Scheduler، لا يمكن التحقق من سلوكه التلقائي.
3. **سلامة توقيعات PayPal/Wise/Payoneer webhooks** — التوقيعات مخصصة (HMAC) وليست المعايير الرسمية. لا يمكن التحقق من صحتها بدون بيئة اختبار حقيقية لكل بوابة.
4. **سلوك super admin بدون workspace** — `SetWorkspace` middleware لا يضبط config عندما لا يوجد workspace. `DataController::import` قد ي crash.

---

## تعارضات الوثائق

1. **`.env.example` vs `config/*.php`** — قيم افتراضية مختلفة لـ: DB connection, default payment gateway, expiry reminder days, grace period days (详见 F-06).
2. **`config/payment.php` gateway defaults** — الافتراضي `chargily` لكن `.env.example` يقول `cash`. بعض البوابات hardcoded `false` في config رغم وجود env key (详见 F-06).
3. **`config/database.php` default connection** — `sqlite` في config لكن `mysql` في `.env.example` (详见 F-03).
4. **`config/auth-home.php` vs `config/roles.php`** — `auth-home.php` يذكر `deputy_super_admin` لكن `roles.php` لا يتضمن هذا الدور (详见 config audit).
5. **`config/finance.php` Sentry channel** — يصف `StreamHandler` لكن `sentry/sentry-laravel` يحتاج handler مختلف (详见 F-08).
