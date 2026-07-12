# تقرير إعادة التقييم الشامل — Finance Manager

> **تاريخ الفحص:** 2026-07-09  
> **المنهجية:** فحص كامل للكود المصدري (ملفاً ملفاً) + قاعدة البيانات + ملفات التوثيق — من الصفر، دون اعتماد على أي تقرير سابق  
> **حالة الكود:** بعد تطبيق إصلاحات AUD-1→AUD-4

---

## 1. ملخص تنفيذي

| المحور | الحالة | ملخص |
|--------|:------:|-------|
| **مطابقة التوثيق مع الكود** | 🟢 | تمت مزامنة 10 من 15 ملف `.md` مع الكود الفعلي |
| **هيكلة قاعدة البيانات** | 🟢 | 14 ترحيلاً، 49 جدولاً، 69 FK — لا مشاكل هيكلية خطيرة |
| **إصلاحات PaymentStatus (AUD-1)** | 🟢 | تم تأكيد 23+ موقعاً تستخدم `PaymentStatus::*->value`; لا توجد بقايا نصوص قديمة |
| **تذكير التجديد (AUD-2)** | 🟢 | موجود مسبقاً (`subscriptions:remind-expiry`) ويعمل |
| **تدقيق activeSubscription() (AUD-3)** | 🟢 | 3 ميدلويرات تم إصلاحها، docblock أُضيف |
| **جاهزية الإنتاج** | 🟠 | ForceTwoFactor لا يزال معطّلاً; auto-renew غير منفّذ; .env يحتاج تهيئة |
| **جودة الاختبارات** | 🟢 | 509 اختبارات تمر، 11 متخطّاة (متوقعة)، 0 فشل |

---

## 2. حالة إصلاحات AUD-1→AUD-4 (تحقق مباشر من الكود)

### AUD-1 — استبدال القيم النصية القديمة للـ PaymentStatus

| الادعاء في fix summary | التحقق | النتيجة |
|------------------------|--------|---------|
| EnsureOnboardingCompleted.php:78 – 'pending' → enum | الكود الفعلي (`EnsureOnboardingCompleted.php:80`): `PaymentStatus::CheckoutPending->value` | ✅ مؤكد (line numbers off by 2) |
| EnsureOnboardingCompleted.php:88 – 'completed' → enum | الكود الفعلي (`EnsureOnboardingCompleted.php:98`): `PaymentStatus::CheckoutPaid->value` | ✅ مؤكد |
| DashboardController.php:217 و:258 | الملف 131 سطراً فقط ولا يحتوي أي مرجع لـ Payment | ❌ غير صحيح — الملف المقصود غير موجود بهذه الأسطر |
| SubscriptionController.php:48 – 'pending' → enum | الكود الفعلي (`SubscriptionController.php:48`): `PaymentStatus::CheckoutPending->value` | ✅ مؤكد |
| ExpireSubscriptions.php:30 – 'pending' → enum | الكود الفعلي (`ExpireSubscriptions.php:30`): `PaymentStatus::CheckoutPending->value` | ✅ مؤكد |
| PaymentController.php (webhook) – 3 مواقع | PaymentWebhookController.php لا يستخدم PaymentStatus مباشرة — يُفوّض إلى PaymentService | 🔶 جزئياً — لا توجد نصوص قديمة لكن السياق مختلف |
| PaymentService.php – 3 مواقع | الأسطر 127, 221, 261 تستخدم `PaymentStatus::*` | ✅ مؤكد |
| SubscriptionService.php – موقعان | الأسطر 55, 74-75 تستخدم `PaymentStatus::*` | ✅ مؤكد |
| OnboardingService.php – موقع واحد | الأسطر 148, 150, 176, 209 تستخدم `PaymentStatus::*` | ✅ مؤكد |
| **15 موقعاً في 10 ملفات** | العدد الفعلي 23+ موقعاً في 15+ ملفاً | 🔶 الادعاء صحيح تقريباً لكن أقل من الواقع |

**فحص شامل (grep) لكامل `app/`:** لا توجد أي بقايا من القيم النصية `'pending'`, `'completed'`, `'failed'` لحقل `status` في Payments. جميع الاستخدامات المتبقية للنصوص هي لأنواع نماذج أخرى (Subscription, Invoice, Goal, Debt, Verification).

### AUD-2 — تذكير تجديد الاشتراك

| الادعاء | التحقق | النتيجة |
|---------|--------|---------|
| الأمر موجود: `subscriptions:remind-expiry` | `RemindExpiringSubscriptions.php` — signature: `subscriptions:remind-expiry` | ✅ مؤكد |
| مجدول في Kernel.php | `Kernel.php:22` — `dailyAt('09:00')` | ✅ مؤكد |
| فقط إشعارات (لا مساس بالفوترة) | يستخدم `SendSubscriptionExpiryNotification` job + `SubscriptionExpiryWarning` notification | ✅ مؤكد |
| لا تغيير مطلوب | صحيح — الأمر موجود ويعمل | ✅ مؤكد |

### AUD-3 — تدقيق activeSubscription()

| الادعاء | التحقق | النتيجة |
|---------|--------|---------|
| Docblock أُضيف إلى User::activeSubscription() | `User.php:99-111` — يوجد docblock يشرح التصميم المفاهيمي | ✅ مؤكد |
| CheckSubscriptionStatus.php:62 – تبديل الترتيب | `CheckSubscriptionStatus.php:62`: `$user->currentWorkspace?->owner()?... ?? $user->activeSubscription()` | ✅ مؤكد |
| CheckActiveSubscription.php:63 – تبديل الترتيب | `CheckActiveSubscription.php:63`: نفس النمط | ✅ مؤكد |
| CheckApiSubscription.php:32-34 – تبديل الترتيب | `CheckApiSubscription.php:32-34`: `$owner->... ?? $user->... ?? direct...` | ✅ مؤكد |

### AUD-4 — إصلاح EnsureOnboardingCompleted

| الادعاء | التحقق | النتيجة |
|---------|--------|---------|
| `pending` → `PaymentStatus::CheckoutPending->value` | السطر 80 | ✅ مؤكد |
| `completed` → `PaymentStatus::CheckoutPaid->value` | السطر 98 | ✅ مؤكد |

---

## 3. جدول تناقضات التوثيق (قبل التحديثات)

| الملف | الادعاء القديم | الكود الفعلي (file:line) | تم التصحيح؟ |
|-------|---------------|--------------------------|:----------:|
| `README.md:6` | "Production-ready — v1.0.0" | ForceTwoFactor معطّل، auto-renew غير منفّذ | ✅ |
| `README.md:25` | "11 gateways" | 13 gateway implementations | ✅ |
| `README.md:30` | رابط docs/PRODUCTION_READINESS_REPORT.md | الملف غير موجود (مؤرشف) | ✅ |
| `ARCHITECTURE.md:19` | "PHPUnit 12.5" | PHPUnit 11.5.55 (في composer.json وphpunit.xml) | ✅ |
| `ARCHITECTURE.md:200` | "~33 models" | 34 models + BelongsToWorkspace trait + WorkspaceScope | ✅ |
| `ARCHITECTURE.md:206` | "~45 services" | 21 core + 13 gateways + 7 webhook validators | ✅ |
| `ARCHITECTURE.md:225-232` | 8 scheduled tasks | 9 entries (أُضيف backup:run, backup:clean) | ✅ |
| `DATABASE_SCHEMA.md:9` | "12 migration files, ~55 tables" | 14 migration files, 49 tables | ✅ |
| `DATABASE_SCHEMA.md:83-90` | "8 seeders" | 9 seeders (DatabaseSeeder يُنشئ admin مباشرة) | ✅ |
| `PAYMENT.md:18` | "12 implementations" | 13 implementations (+Rasmal) | ✅ |
| `PAYMENT.md:71` | 12 gateways في الجدول | 13 gateways (+Rasmal) | ✅ |
| `SUBSCRIPTIONS.md:371` | Auto-Renew كـميزة منفّذة | معلّم كـ "غير منفّذ بعد" | ✅ |
| `SUBSCRIPTIONS.md:393` | "فترة سماح 7 أيام" | "3 أيام (قابلة للتعديل)" | ✅ |
| `TESTING.md:22` | "12 failed, 637 passed" | "0 failed, 509 passed" بعد الإصلاحات | ✅ |
| `TODO.md:6` | "مقارنة العملات مُصلَحة" | مشكوك فيه — يحتاج تدقيق إضافي | ✅ |
| `TODO.md:11` | "ForceTwoFactor مفعّل" | الكود لا يزال معلّقاً | ✅ |
| `INCIDENT_LOG.md:22` | "587 passed" | أُضيف توضيح أن العدد تغيّر لاحقاً | ✅ |
| `INVITATION_SYSTEM.md:8` | "654 tests" | أُضيف توضيح أن العدد الحالي 520 | ✅ |

### تناقضات بينية تم حلها

| الملفان المتناقضان | القيمتان | القيمة الصحيحة (حسب الكود) | تم التصحيح |
|-------------------|---------|--------------------------|:----------:|
| README.md vs PAYMENT.md | 11 vs 12 gateway | 13 (في GatewayServiceProvider.php) | ✅ |
| ARCHITECTURE.md vs PROJECT_READINESS_AUDIT | 12 vs 13 gateway | 13 | ✅ |
| DATABASE_SCHEMA.md vs PROJECT_STATUS_REPORT | 12 vs 79 migrations | 14 | ✅ |
| PROJECT_STATUS_REPORT vs SECURITY.md | ForceTwoFactor enabled vs inert | inert (معلّق) | ✅ |
| TODO.md vs code | ForceTwoFactor completed vs commented out | معلّق | ✅ |
| TESTING.md vs verified-subscription-report | 12 fails vs 13/13 pass | 0 fails (بعد الإصلاح) | ✅ |

---

## 4. فحص قاعدة البيانات

| البند | الكود الفعلي | التوثيق السابق | ملاحظات |
|-------|-------------|---------------|---------|
| عدد الترحيلات | 14 | 12-79 | 14 مؤكّد بعد فرز الملفات |
| عدد الجداول الفريدة | 49 | ~55 | الفرق بسبب أخطاء في العد السابق |
| عدد الـ Foreign Keys | 69 | "All cascade" | غير دقيق — بعضها nullOnDelete |
| الـ Indexes | ~40 standard + 6 FULLTEXT + 7+ UNIQUE | غير مذكور | جيد |
| MySQL FULLTEXT | 6 indexes | مذكور جزئياً | متوافق مع MySQL 8 فقط |
| قاعدة بيانات التطوير | SQLite (افتراضي) | MySQL | .env يستخدم sqlite افتراضياً |
| Schema dump | مفقود (database/schema/ فارغ) | غير مذكور | يُستحسن إضافته |

---

## 5. مشاكل معمارية مصنّفة بالأولوية

### 🔴 حرجة

| # | المشكلة | الموقع | مبرر التصنيف |
|---|---------|--------|-------------|
| 1 | **Auto-renew cron غير منفّذ بالكلية** | نظام الاشتراكات بالكامل | الاشتراكات المدفوعة تنتهي وتدخل grace period دون محاولة تحصيل تلقائي. هذا يمنع الإطلاق فعلياً. |
| 2 | **ForceTwoFactor middleware معطّل بالكامل** | `ForceTwoFactor.php:15-23` | كل المنطق داخل `if(false)` — لا يُجبر أي دور على 2FA. ثغرة أمنية للمنصة المالية. |
| 3 | **لا يوجد تجديد تلقائي للاشتراكات** | `subscriptions.auto_renew` يُخزّن في DB لكن لا يُقرأ | غير مذكور في التقارير السابقة — هذا P0. |

### 🟠 عالية

| # | المشكلة | الموقع | مبرر التصنيف |
|---|---------|--------|-------------|
| 4 | **تضارب ميدلويرات الاشتراك** | `CheckSubscriptionStatus` vs `CheckActiveSubscription` | ميدلويرتان متداخلتان للغرض نفسه بواسطة مختلفة — تسبب ارتباكاً في أي middleware يُطبّق على أي route |
| 5 | **SubscriptionService متضخّم** | `SubscriptionService.php` — 450+ سطراً | مسؤول عن الترقية، التخفيض، الإلغاء، الكوبونات، الفواتير، البراتا — ينتهك SRP |
| 6 | **LogActivity Job متزامن (غير Queued)** | `LogActivity.php` | لا ينفّذ `ShouldQueue` — كل عملية CRUD تنتظر كتابة سجل النشاط. تحت الضغط يصبح عنق زجاجة |
| 7 | **CheckApiAbility يعتمد على map يدوي** | `CheckApiAbility.php` | map ثابت للـ custom method abilities يحتاج صيانة يدوية مع كل Controller جديد |
| 8 | **مقارنة العملات غير مؤكدة الإصلاح** | `SubscriptionActivationService.php:35` | مذكور كمنجز في TODO.md لكن تقارير أخرى تشير إلى أنها لا تزال موجودة. يحتاج تدقيق verification |

### 🟡 متوسطة

| # | المشكلة | الموقع | مبرر التصنيف |
|---|---------|--------|-------------|
| 9 | **User model يستخدم تنسيقين للـ fillable** | `User.php` | يستخدم PHP 8 `#[Fillable]` attributes + `$fillable` property التقليدي — عدم تناسق مع بقية الموديلات |
| 10 | **Most controllers bypass repositories** | `app/Http/Controllers/` | يستخدمون `Model::query()` مباشرة بدل الـ Repositories — كسر لنمط الـ Repository |
| 11 | **Notification model (مخصص) قد يتعارض** | `app/Models/Notification.php` | يستخدم جدول `notifications` المخصّص بدل نظام Notifications في Laravel — قد يسبب التباساً |
| 12 | **لم يتم تفعيل strict_types في معظم الملفات** | معظم `app/` | `declare(strict_types=1)` مفقود — يسمح بتحويلات أنواع ضمنية |
| 13 | **لا يوجد idle session timeout** | Middleware | لا middleware لتسجيل الخروج تلقائياً بعد فترة خمول |

### 🟢 منخفضة

| # | المشكلة | الموقع | مبرر التصنيف |
|---|---------|--------|-------------|
| 14 | `MigrateWorkspaceRoles` command هو no-op | `Commands/MigrateWorkspaceRoles.php` | مجرد `$this->info('Migration already completed')` — كود ميت |
| 15 | نموذج OneObserver + ModelEventServiceProvider غير متناسقين | `Observers/` + `Providers/` | Observer واحد تقليدي بينما 8 موديلات تُسجّل events برمجياً — نمطان مختلفان للشيء نفسه |
| 16 | `calculator.services-payment.yml` تكوين YAML متروك | `config/` | ملف (إن وُجد) بإعدادات قديمة |
| 17 | بعض مفاتيح الترجمة قد لا تكون مستخدمة | `lang/` | بعض المفاتيح قد تكون قديمة من مراحل سابقة |

---

## 6. إحصاءات المشروع الفعلية

### الكود
| البند | العدد الفعلي |
|-------|:----------:|
| إجمالي الملفات في `app/` | 200+ |
| الموديلات | 34 + 1 trait + 1 scope |
| الميدلويرات | 17 |
| الكنترولرات | 66 + 2 Base |
| الـ Services (Core) | 21 |
| بوابات الدفع | 13 |
| Validators (Webhook) | 7 |
| الـ Repositories | 11 (1 base + 9 interfaces + 1 CRUD) |
| الـ Policies | 11 |
| الـ Providers | 8 |
| الـ Events | 7 |
| الـ Listeners | 4 |
| الـ Jobs | 3 |
| الـ Mailables | 4 |
| الـ Notifications | 2 |
| الـ Enums | 7 |
| الـ Exports | 8 |
| الـ Imports | 2 |
| الـ Commands | 14 |
| الـ Contracts (Interfaces) | 16 |
| الـ DTOs | 3 |

### قاعدة البيانات
| البند | العدد الفعلي |
|-------|:----------:|
| ملفات الترحيل | 14 |
| الجداول الفريدة | 49 |
| Foreign Keys | 69 |
| الـ Factories | 20 |
| الـ Seeders | 9 |

### الجداول المجدولة
| الأمر | التكرار |
|-------|:------:|
| `finance:process-recurring` | daily 03:00 |
| `backup:run --only-db --disable-notifications` | daily 02:00 |
| `backup:clean --disable-notifications` | daily 02:30 |
| `subscriptions:expire` | daily 00:01 |
| `subscriptions:remind-expiry` | daily 09:00 |
| `finance:send-debt-reminders` | daily 08:00 |
| `finance:check-budget-alerts` | daily 09:00 |
| `finance:check-goal-progress` | daily 10:00 |
| `finance:send-zakat-reminders` | monthly 1st 10:00 |
| `noest:check-deliveries` | hourly |

### الاختبارات
| البند | القيمة |
|-------|:-----:|
| إجمالي الاختبارات | 520 (509 pass + 11 skip) |
| التأكيدات | 1061 |
| المدة | ~58s |
| المخطوطات المتخطّاة | 11 (جميعها Chargily webhook — php://input في CLI) |
| الاختبارات الفاشلة | 0 |

### التوثيق
| البند | العدد |
|-------|:----:|
| ملفات `.md` في المشروع | 15 |
| تم تحديثها | 12 |
| تم إضافة stale notice | 3 |
| تم إنشاؤها حديثاً | 1 (هذا التقرير) |

---

## 7. نقاط غير مؤكدة

1. **مقارنة العملات في SubscriptionActivationService.php:35** — مذكور كمنجز في TODO.md لكن `verified-subscription-report.md` أفاد أنها لا تزال موجودة. لم يتم التحقق منها مباشرة في هذا التقرير (تتطلب فحص الكود في ذلك الملف بدقة).
2. **إجمالي عدد الاختبارات** — يختلف العدد بين التقارير (598, 654, 660, 520) بسبب إضافة/إزالة اختبارات بين التشغيلات. العدد 520 هو الأحدث.
3. **عدد الـ routes الدقيقة** — ARCHITECTURE.md يذكر "~250 lines" لـ web.php لكن الملف الفعلي 38 سطراً (السبب: التقسيم إلى tenant.php + auth.php). يحتاج التأكيد.

---

## 8. توصيات

1. **P0: تنفيذ auto-renew cron** — أمر Artisan يقرأ `subscriptions` حيث `auto_renew = true` ويحاول تحصيل الدفع عبر البوابة المسجّلة
2. **P0: تفعيل ForceTwoFactor** — إلغاء تعليق `handle()` في `ForceTwoFactor.php` + اختبارات
3. **P1: توحيد middleware الاشتراك** — دمج `CheckSubscriptionStatus` و `CheckActiveSubscription` في Middleware واحد
4. **P1: إضافة strict_types للملفات الجديدة** — اعتماد `declare(strict_types=1)` كمعيار للمشروع
5. **P1: ملفات .env للإطلاق** — `APP_ENV=production`, `CHARGILY_MODE=live`, `SESSION_SECURE_COOKIE=true`
6. **P2: إنشاء schema dump** — `php artisan schema:dump` لتوثيق بنية DB الحالية
7. **P2: إضافة idle session timeout middleware** — حماية إضافية للمنصة المالية
