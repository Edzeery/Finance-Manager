# تقرير تقييم الجاهزية الشامل — Finance Manager

> **⚠️ هذا التقرير قديم — تم استبداله بـ `PROJECT_FULL_REAUDIT_REPORT_2026-07-09.md`**  
> **تاريخ الفحص الأصلي:** 2026-07-09  
> **المنهجية الأصلية:** فحص كامل للكود المصدري والمستندات وقاعدة البيانات — بدون أي تعديل  
> **سبب الإحلال:** تم تطبيق إصلاحات AUD-1→AUD-4 بعد هذا التقرير، والتقرير الجديد يعتمد على حالة الكود الحالية بعد الإصلاحات. بعض المشاكل المذكورة هنا قد تكون أُصلحت جزئياً أو كلياً.  
> **ملاحظة:** هذا التقرير التشخيصي الأصلي لا يزال متاحاً للأرشفة فقط — لا يُعتمد عليه كمرجع للوضع الحالي.

---

## 1. ملخص تنفيذي

| المحور | الحالة | تبرير سطر واحد |
|--------|:------:|-----------------|
| **مطابقة الكود مع التوثيق** | 🔴 | 8 تناقضات مؤكدة بين التوثيق والكود، بينها أخطاء خطيرة في PROJECT_STATUS_REPORT.md |
| **هيكلة قاعدة البيانات** | 🟡 | 14 ترحيلاً فعلياً (لا 79)؛ FK cascade-only؛ لا توجد مشاكل هيكلية خطيرة |
| **البنيان المعماري** | 🔴 | 9 استعلامات في جدول payments تستخدم قيماً نصية قديمة بعد إعادة هيكلة PaymentStatus؛ حميعها معطّلة |
| **جودة واكتمال الاختبارات** | 🟡 | 96 ملف اختبار لكن بدون تغطية للتدفقات الحرجة الجديدة (الدفع، الاشتراكات، RBAC) |
| **جاهزية الإنتاج** | 🔴 | ForceTwoFactor معطّل رغم ادعاء PROJECT_STATUS_REPORT أنه مفعّل؛ ثغرات متعددة في تكوين البيئة |

---

## 2. جدول تناقضات التوثيق مقابل الكود الفعلي

| الملف | الادعاء في التوثيق | الكود الفعلي (file:line) | الخلاصة |
|-------|-------------------|--------------------------|---------|
| `PAYMENT.md:57` | 12 بوابة دفع | `GatewayServiceProvider.php:63-77` يُسجّل 13 بوابة (بما فيها Rasmal) | ❌ نقص: Rasmal غير مذكور |
| `ARCHITECTURE.md:16` | 12 بوابة دفع | `GatewayServiceProvider.php:63-77` يُسجّل 13 | ❌ نقص العدد |
| `PROJECT_STATUS_REPORT.md:163` | 0 unit — 0 feature — لا يوجد مجلد tests/features/ | يوجد 96 ملف اختبار (Unit + Feature) في `tests/` | ❌ خطأ فادح |
| `PROJECT_STATUS_REPORT.md:164` | قائمة البوابات: Paymob, MyFatoorah, Tap, Telr, Hyperpay, Moyasar — ولا يذكر BaridiMob, RedotPay, Wise, Payoneer, Cash, Delivery | البوابات الفعلية مختلفة تماماً (13 بوابة) | ❌ الادعاء غير صحيح بالكامل |
| `PROJECT_STATUS_REPORT.md:89` | Filament v3 | لا يوجد أي استخدام لـ Filament في composer.json أو الكود | ❌ غير موجود |
| `PROJECT_STATUS_REPORT.md:159` | إجمالي الـ Migrations: 79 | 14 ملف ترحيل فقط في `database/migrations/` | ❌ خطأ عددي فادح |
| `PROJECT_STATUS_REPORT.md:57` و `TODO.md:11` | ForceTwoFactor middleware مُفعّل الآن (`handle()` فُك تعليقه) | `ForceTwoFactor.php:15-23` — `handle()` بالكامل مُعلّق (commenté) | ❌ غير صحيح — لا يزال معطلاً |
| `SECURITY.md:12` | ForceTwoFactor middleware "currently inert" — `handle()` has all logic commented out | `ForceTwoFactor.php:15-23` — مؤكد: كل المنطق مُعلّق | ✅ صحيح |
| `verified-subscription-report.md:382-383` | Currency comparison bug في `SubscriptionActivationService.php:35` لم يُصلح | `SubscriptionActivationService.php:36-38` — الكود يستخدم `CurrencyHelper::convert()` حالياً | 🔶 التقرير قديم — الإصلاح موجود حالياً |
| `README.md:6` | "✅ Production-ready — v1.0.0" | ForceTwoFactor مُعطّل، 9 استعلامات payments مكسورة بعد refactoring، Auto-renew cron مفقود | ❌ غير جاهز للإنتاج |
| `PROJECT_STATUS_REPORT.md:174` | 12 بوابة معرفة، نقص Webhooks لـ 2 بوابة | `routes/webhooks.php` — 7 مسارات webhook معرفة (chargily, paypal, stripe, wise, payoneer, noest, rasmal) | ⚠️ العدد مختلف |
| `ARCHITECTURE.md:48` | يذكر "Subscription → Invoice → Payment → Verify" | مسار التدفق الفعلي يتضمن `PaymentVerification` و `PaymentWebhookLog` و `ActivateSubscription` ككيان منفصل | ⚠️ التبسيط مقبول لكنه يغفل خطوة webhook |
| `SUBSCRIPTIONS.md:84` | فترة السماح 3 أيام (قابلة للتعديل عبر `GRACE_PERIOD_DAYS`) | `.env:49` — `GRACE_PERIOD_DAYS=7` (القيمة الافتراضية في الكود `3` لكن `.env` يُغيّرها إلى `7`) | ⚠️ قيمة الانتاج الفعلية 7، التوثيق يقول 3 |
| `SUBSCRIPTIONS.md:27` | جدول `payments`: العمود `reference` | `Payment.php:18-28` — `$fillable` لا يحتوي على `reference`، بل `gateway_reference` | ⚠️ اختلاف بسيط في اسم العمود |
| `TESTING.md:62` | 11 skipped tests (Chargily webhook + DebugMode) | عدد الملفات المُتخطّاة الفعلية: 3 (ChargilyPaymentMethodTest كلها + DebugModeSafetyTest 1 + PaymentWebhookTest 5) | ✅ دقيق |
| `DATABASE_SCHEMA.md:53` | جدول `invoices` يحتوي على `tax` | `2026_07_07_000002_add_gateway_fee_fields.php` يُضيف `tax_added`, `tax_disclosed`, `proration_credit` — لا يوجد عمود `tax` وحيد | ⚠️ تبسيط غير دقيق |

---

## 3. جدول مشاكل قاعدة البيانات الفعلية

> ملاحظة: تم فحص 14 ملف ترحيل. التقييم مبني على الكود الفعلي وليس التوثيق.

| الجدول / العمود | نوع المشكلة | الوصف | مستوى الخطورة |
|-----------------|-------------|-------|:--------------:|
| `payments.status` | **كود معطل (9 مواقع)** | بعد refactoring PaymentStatus إلى `checkout.*`، لا تزال 9 استعلامات في `app/` تبحث عن `'pending'`, `'completed'` — هذه الاستعلامات لا تجد نتائج أبداً | 🔴 حرج |
| `workspaces.settings` | **JSON بدون schema** | `workspaces.settings` (JSON column) بدون تحقق من النوع أو validation — قد يسبب أخطاء وقت التشغيل | 🟡 متوسط |
| `budget_carried_over` | **غير مكتمل** | `budget_carried_over` ليس جدولاً أو عموداً في أي migration — الكود لا يطبق هذا المفهوم بعد | 🟢 منخفض |
| `payments` | **قيم enum متعددة المصادر** | `status` يُكتب من 3 مسارات مختلفة (Webhook، Admin Verification، Gateway Return) بدون نظام مركزي لتسجيل التغيير | 🟡 متوسط |
| `payment_webhook_logs.checkout_id` | **إضافة حديثة** | `checkout_id` أُضيف بعد الإنشاء الأولي (index ليس unique) — لا يوجد قيد uniqueness على `(checkout_id, event_type)` لكن الكود يتحقق منه يدوياً | 🟢 منخفض |
| `subscriptions.status` | **قيم غير مقيدة** | العمود `status` من نوع string بدون `CHECK` constraint — قد يُكتب بقيمة غير متوقعة | 🟡 متوسط |
| `payment_gateways.credentials` | **تشفير إداري** | يُخزّن كمشفر (`HasGatewaySettings` trait) — جيد، لكن لا يوجد fallback إلى `.env` موثّق | 🟢 منخفض |

### تفاصيل الاستعلامات المعطلة بعد Refactoring PaymentStatus

| الموقع (file:line) | القيمة القديمة | القيمة الجديدة المطلوبة | الأثر |
|--------------------|---------------|-------------------------|-------|
| `ExpireSubscriptions.php:29` | `'pending'` | `PaymentStatus::CheckoutPending->value` | اشتراكات لا تدخل grace period |
| `User.php:147` | `'pending'` | `PaymentStatus::CheckoutPending->value` | `hasPendingManualPayment()` لا تعمل أبداً |
| `SubscriptionCancellationService.php:49` | `'completed'` | `PaymentStatus::CheckoutPaid->value` | إلغاء الاشتراك لا يجد دفعة مكتملة للاسترداد |
| `SubscriptionService.php:55` | `'pending'` | `PaymentStatus::CheckoutPending->value` | `hasPendingPayment()` لا تجد مدفوعات معلقة أبداً |
| `SubscriptionService.php:74` | `'pending'` | `PaymentStatus::CheckoutPaid->value` | لا يُلغى الدفعات المعلقة القديمة |
| `OnboardingService.php:148` | `'pending'` | `PaymentStatus::CheckoutPending->value` | لا تُلغى الدفعات المعلقة القديمة عند تبديل البوابة |
| `EnsureOnboardingCompleted.php:79` | `'pending'` | `PaymentStatus::CheckoutPending->value` | Middleware لا يوجه المستخدم لصفحة الدفع المعلق |
| `EnsureOnboardingCompleted.php:97` | `'completed'` | `PaymentStatus::CheckoutPaid->value` | Middleware لا يوجه المستخدم المكتمل إلى onboarding.setup |
| `SubscriptionController.php:47` | `'pending'` | `PaymentStatus::CheckoutPending->value` | صفحة حسابات المستخدم لا تظهر الدفعة المعلقة |

---

## 4. قائمة المشاكل المعمارية مرتبة حسب الخطورة

### 🔴 حرج (يمنع الإطلاق)

| # | المشكلة | الموقع | الأثر الفعلي |
|---|---------|--------|-------------|
| AUD-1 | **9 استعلامات payments مكسورة بعد refactoring PaymentStatus** | انظر الجدول أعلاه (8 ملفات) | النظام لا يجد المدفوعات حسب الحالة — المدفوعات المعلقة غير مكتشفة، الاشتراكات لا تدخل grace period، الإلغاء لا يعمل، الـ middleware لا يوجه بشكل صحيح |
| AUD-2 | **Auto-renew cron للاشتراكات المدفوعة مفقود بالكامل** | لا يوجد ملف — لا `app/Console/Commands/` ولا `app/Jobs/` | الاشتراكات المدفوعة لا تتجدد تلقائياً. خسارة إيراد متكرر. |
| AUD-3 | **`User::activeSubscription()` يتجاوز حدود workspace** | `User.php:95-101` يعمل بـ `withoutWorkspace()` — 8 مستدعين | عند تبديل المساحة، تظهر بيانات اشتراك المساحة السابقة في المساحة الحالية (تسريب بيانات بين المساحات) |
| AUD-4 | **`EnsureOnboardingCompleted.php` لا يزال يستخدم `'pending'` و `'completed'`** | `EnsureOnboardingCompleted.php:79,97` | توجيه الـ middleware بعد الدفع معطل — المستخدمون قد يعلقون في حلقة توجيه لا نهائية |

### 🟠 عالية

| # | المشكلة | الموقع | الأثر الفعلي |
|---|---------|--------|-------------|
| AUD-5 | **ForceTwoFactor middleware مُعطّل رغم ادعاء PROJECT_STATUS_REPORT.md أنه مفعّل** | `ForceTwoFactor.php:15-23` — المنطق كامل مُعلّق | الإدارة العليا (super_admin, deputy_super_admin, workspace_admin) غير مجبرة على إعداد 2FA — ثغرة أمنية |
| AUD-6 | **التبديل بين البوابات (switchGateway) يُنشئ دفعات يتيمة** | `payment-retry.blade.php:139-142` | الدفعة القديمة تبقى بحالة غير نهائية — قد تتراكم الدفعات غير المكتملة |
| AUD-7 | **`payment/result` يعتمد على session (`session('payment_gateway')`)** | قيد المكون Volt لصفحة payment-result | إذا انتهت الجلسة (session expired)، تفشل الصفحة في تحديد البوابة المناسبة |
| AUD-8 | **`LOG_LEVEL=warning` في `.env`** | `.env:21` | يمنع تسجيل `info`-level logs — قد يخفي معلومات تشخيصية مهمة أثناء الإنتاج |

### 🟡 متوسطة

| # | المشكلة | الموقع | الأثر الفعلي |
|---|---------|--------|-------------|
| AUD-9 | **`APP_URL=https://finance-manager.com`** | `.env:5` | عنوان نطاق افتراضي — يجب تغييره إلى النطاق الفعلي قبل الإطلاق |
| AUD-10 | **`CHARGILY_WEBHOOK_URL=https://apoplectoid-supervastly-dinah.ngrok-free.dev/...`** | `.env:91` | رابط ngrok تجريبي منتهي — يجب استبداله برابط الإنتاج الفعلي |
| AUD-11 | **`BACKUP_MAIL_TO=admin@example.com`** | `.env:136` | بريد إلكتروني افتراضي — لن تصل إشعارات النسخ الاحتياطي |
| AUD-12 | **`SESSION_LIFETIME=120` دون idle timeout** | `.env:32` | لا يوجد middleware لإلغاء الجلسة عند الخمول — خطر أمني |
| AUD-13 | **مقارنة عملات قد تخطئ مع عملات أصغر عددياً من USD** | `SubscriptionActivationService.php:36-38` — رغم أن الإصلاح أُضيف (`CurrencyHelper::convert`) يستخدم `$payment->currency !== $plan->currency` كشرط | إذا لم تكن العملتان مختلفتين حرفياً لكن القيم غير قابلة للمقارنة، قد يفشل التحقق |
| AUD-14 | **لا يوجد توثيق OpenAPI/Swagger للـ API** | لا يوجد ملف — لا `routes/api.php` documentation | لا يمكن توليد client SDK تلقائياً — يزيد وقت التكامل مع العملاء |
| AUD-15 | **تقارير التوثيق المتناقضة:** `PROJECT_STATUS_REPORT.md` يحتوي على 6+ أخطاء واقعية | `PROJECT_STATUS_REPORT.md` كلها | يُضلل المطورين الجدد ويجعل التوثيق غير موثوق |

### 🟢 منخفضة

| # | المشكلة | الموقع | الأثر الفعلي |
|---|---------|--------|-------------|
| AUD-16 | **`resources/views/payment/result.blade.php` غير مستخدم** | `resources/views/payment/result.blade.php` — لا يوجد controller أو route يعيده | كود ميت — يسبب ارتباكاً |
| AUD-17 | **متوسط عدد الـ Models في التوثيق غير دقيق** | `ARCHITECTURE.md:200` يقول ~33 Models — الفعلي 35 | اختلاف طفيف |
| AUD-18 | **عدد الخدمات في التوثيق غير دقيق** | `ARCHITECTURE.md:206` يقول ~45 Services — الفعلي 58 | نقص 13 خدمة |
| AUD-20 | **`stripe-webhook` endpoint مُعرّف لكنه لم يُختبر ضد خادم حقيقي** | `PaymentWebhookController::stripe()` موجود — `StripeSignatureValidator` موجود في `WebhookSignatureManager` | قد يعمل وقد لا يعمل — لم يُختبر |
| AUD-21 | **إعدادات CORS افتراضية** | `config/cors.php` — إعدادات Laravel الافتراضية | قد تسبب مشاكل عبر النطاقات (CORS) في الإنتاج |

---

## 5. نقاط غير مؤكدة تتطلب تحققاً بشرياً إضافياً

| # | النقطة | السبب | ما الذي يجب التحقق منه |
|---|--------|-------|----------------------|
| UNC-1 | **HalalInvest gateway لم يُفحص** | ذكر `ARCHITECTURE.md` لا يذكره — `PAYMENT.md` يذكر 12 بوابة فقط — لكن الكود يُسجل 13 | هل هناك بوابة HalalInvest يجب إضافتها أو إزالتها؟ |
| UNC-2 | **هل يعمل `WebhookSignatureManager` فعلياً مع Stripe/PayPal/Wise؟** | الـ validators مُسجّلة لكن لا يوجد اختبارات تكامل تثبت عملها مع خوادم حقيقية | هل التوقيعات (signatures) صحيحة؟ هل تحتاج الـ endpoints إلى معالجة Payload مختلفة؟ |
| UNC-3 | **هل `QUEUE_CONNECTION=database` مع `sync` فشل في الاختبارات؟** | `.env:42` يقول `database` لكن إعدادات التطوير قد تستخدم `sync` | هل الاختبارات الحالية تعمل مع queue غير متزامن؟ هل تُرسَل رسائل البريد الإلكتروني في الخلفية؟ |
| UNC-4 | **هل تم التحقق من كفاءة الـ N+1 queries؟** | `ARCHITECTURE.md:261` يذكر أنه لم يُفحص | هل Dashboard/Filters تعاني من N+1 تحت الحمل؟ (يتطلب Debugbar أو Telescope) |
| UNC-5 | **هل الـ seeder `DemoDataSeeder` يعمل مع بنية `payment_gateways` الجديدة؟** | `Migration 2026_07_06_101815` أعاد هيكلة الجدول — هل الـ seeder متوافق؟ | تشغيل `migrate:fresh --seed` والتأكد من عدم وجود أخطاء |
| UNC-6 | **هل يوجد تعارض بين `APP_DEBUG=false` و `github_token` في Sentry؟** | `.env:4` = `false` — ولكن `SENTRY_LARAVEL_DSN` فارغ | هل إعدادات الخطأ كافية للإنتاج؟ |
| UNC-7 | **هل خطة Enterprise (`is_public=false`) تعمل بشكل صحيح في Onboarding؟** | `subscription_plans` — Enterprise غير عام (is_public=false) | هل يمكن للمستخدم المخصص (Custom) الاشتراك في خطة Enterprise؟ هل هناك واجهة إدارية لتعيينها؟ |
| UNC-8 | **هل تدعم `app/Http/Controllers/Api/` جميع 14 وحدة مذكورة؟** | `ARCHITECTURE.md:111` يقول 14 resource controllers | هل جميع وحدات API مكتملة (CRUD لكل منها)؟ |
| UNC-9 | **هل ملفات الترجمة (31 domain files) محدّثة بالكامل؟** | `ARCHITECTURE.md:242` يقول 31 domain files | هل جميع المفاتيح الجديدة (PaymentStatus enum, subscription states) مُترجَمة؟ |
| UNC-10 | **هل قاعدة TestAccounts في TESTING.md متطابقة مع الـ seeder؟** | `TESTING.md:96-108` يذكر `demo@example.com` و `admin@example.com` | هل كلمات المرور والنطاقات صحيحة؟ |

---

## ملاحظات ختامية

- **أخطر مشكلة مكتشفة:** 9 استعلامات معطلة في جدول `payments` بعد refactoring PaymentStatus إلى `checkout.*`. هذه الاستعلامات ستعيد 0 نتيجة دائماً، مما يعطل تدفقات الدفع والاشتراك والـ middleware بالكامل.
- **أخطر تناقض في التوثيق:** `PROJECT_STATUS_REPORT.md` يحتوي على 6+ أخطاء واقعية (عدد الاختبارات، عدد الترحيلات، قائمة البوابات، Filament، حالة ForceTwoFactor) — لا يمكن الوثوق بهذا الملف كمرجع.
- **التوصية الأولى:** إصلاح الـ 9 استعلامات payments المكسورة قبل أي شيء آخر (استخدام ثوابت `PaymentStatus::Checkout*->value` بدلاً من النصوص).
- **التوصية الثانية:** تصحيح أو إزالة `PROJECT_STATUS_REPORT.md` لأنه يضلل القارئ بشكل خطير.
- **التوصية الثالثة:** تنفيذ آلية Auto-renew cron للاشتراكات المدفوعة قبل الإطلاق.

---

*التقرير تشخيصي بحت — لا يحتوي على اقتراحات حلول أو خطوات إصلاح.*  
*تاريخ الفحص: 2026-07-09*
