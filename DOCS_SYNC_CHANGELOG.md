# DOCS_SYNC_CHANGELOG — مزامنة التوثيق مع الكود الفعلي

> **تاريخ المزامنة:** 2026-07-09  
> **المرجع:** الكود المصدري في `app/`, `config/`, `database/` — تم فحصه ملفاً ملفاً  
> **أداة المزامنة:** فحص يدوي عبر قراءة الكود الفعلي ومقارنته مع كل ادعاء في ملفات `.md`

---

## 1. README.md

| التغيير | السطر | قبلاً | بعداً | المرجع في الكود |
|---------|:-----:|-------|-------|:--------------:|
| تحديث وصف الجاهزية | 6 | "Production-ready — v1.0.0" | تنويه بأن ForceTwoFactor معطّل وauto-renew غير منفّذ | ForceTwoFactor.php:15-23 |
| إزالة ادعاء 11 بوابة | 25 | "11 gateways" | "13 gateways (Chargily + Moyasar + Paymob + MyFatoorah + Telr + Hyperpay + Tap, 6 more)" | GatewayServiceProvider.php |
| إزالة رابط READINESS | 30 | رابط docs/PRODUCTION_READINESS_REPORT.md | رابط docs/index.md | الملف المؤرشف |

---

## 2. ARCHITECTURE.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| تصحيح PHPUnit | 19 | PHPUnit 12.5 | PHPUnit 11.5.55 | composer.json, phpunit.xml |
| تصحيح عدد الموديلات | 200 | ~33 models | 34 + 1 trait + 1 scope | `app/Models/` glob |
| تصحيح عدد الـ Services | 206 | ~45 services | 21 core + 13 gateways + 7 webhook validators | `app/Services/` |
| إضافة backup إلى المجدول | 225-232 | 8 tasks | 9 tasks (+backup:run, +backup:clean) | Kernel.php |
| إزالة Filament claims | 68-70 | إشارات إلى Filament v3 | حذف كامل | — |
| تصحيح مقدمة Routes | 210 | "~250 lines" | "250 lines between 3 route files (web.php:38, tenant.php:164, auth.php:79)" | routes/ |
| إزالة Filament Admin Panel | 230-240 | فقرة عن Filament | حذف كامل | — |

---

## 3. DATABASE_SCHEMA.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| تصحيح عدد الترحيلات | 9 | "12 migration files, ~55 tables" | "14 migration files, 49 tables" | `database/migrations/` |
| تصحيح الـ seeders | 83-90 | "8 seeders" | "9 seeders" | `database/seeders/` |
| إضافة تفاصيل FULLTEXT | 27-29 | "some indexes" | تفصيل 6 FULLTEXT + UNIQUE indexes | migrations |
| تحديث جدول subscriptions | — | auto_renew غير مذكور | إضافة auto_renew boolean | migration files |
| إضافة جدول failed_jobs | — | غير مذكور | إضافة | migration files |

---

## 4. PAYMENT.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| تصحيح عدد البوابات | 18 | "12 implementations" | "13 implementations" | GatewayServiceProvider.php |
| إضافة Rasmal للجدول | 71 | 12 rows | 13 rows (+Rasmal) | GatewayServiceProvider.php |

---

## 5. SUBSCRIPTIONS.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| تنبيه Auto-Renew غير منفّذ | 371 | Auto-Renew كـ feature منفّذة | تنبيه واضح بأنه غير منفّذ + تصميم To-Be | لا يوجد Artisan command يقرأ auto_renew |
| تصحيح فترة السماح | 393 | "7 أيام" | "3 أيام (قابلة للتعديل عبر GRACE_PERIOD_DAYS في .env)" | config/finance.php: grace_period_days => 3 (env override=7) |
| تصحيح مصير expired | 398 | "الانتقال إلى الخطة المجانية" | "لا انتقال تلقائي إلى الخطة المجانية — المساحة تصبح بدون خطة" | SubscriptionService.php |

---

## 6. TESTING.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| تحديث تعداد الاختبارات الكلي | 8 | "660 tests" | "520 tests (509 pass + 11 skip)" | `phpunit` actual output |
| تحديث التفصيل | 13 | 660 total | 520 total (مطابقة للتفصيل الذي كان صحيحاً) | phpunit output |
| تصحيح الخلاصة | 22 | "12 failed, 637 passed" | "0 failed, 509 passed" | phpunit actual run |

---

## 7. TODO.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| تحديث بند ForceTwoFactor | 11 | "مفعّل على جميع الأدوار" | "الكلاس موجود لكن الكود معلّق — لم يُفعّل بعد" | ForceTwoFactor.php:15-23 |
| إضافة بند مقارنة العملات | 6 | "مُصلَحة" | إضافة ❓ علامة استفهام + إشارة إلى عدم التأكّد | يحتاج تدقيق إضافي |
| إزالة بند Repositories Claim | 58 | "All controllers use repositories" | "Most controllers bypass repositories — use Model::query() directly" | Controllers audit |
| إزالة دليل الصيانة المكرر | 157-200 | إشارات إلى دليل صيانة غير موجود | حذف | — |

---

## 8. SECURITY.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| تصحيح حالة ForceTwoFactor | 110 | "مفعَّل" | "موجود في الكود لكنه مُعلّق (if(false)) — غير مفعَّل" | ForceTwoFactor.php:15-23 |
| إزالة ادعاء Filament | 8 | "Filament v3" | إزالة كاملة | — |

---

## 9. INCIDENT_LOG.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| توضيح تغيّر عدد الاختبارات | 22 | "587 passed" | إضافة ملاحظة أن العدد اختلف لاحقاً (660→520) | phpunit actual |

---

## 10. INVITATION_SYSTEM.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| توضيح عدد الاختبارات | 8 | "654 tests" | إضافة ملاحظة أن العدد الحالي 520 بعد الإصلاح | phpunit actual |

---

## 11. AUD-1-4_FIX_SUMMARY.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| إضافة تصحيح DashboardController | — | غير مذكور | إضافة Footnote يوضح أن DashboardController.php:217,258 غير صحيح (الملف أقصر) | DashboardController.php |
| إضافة حاشية العدد الفعلي | 110+ | "15 sites in 10 files" | ⚠️ ملاحظة أن العدد الفعلي 23+ موقعاً | grep audit |
| إضافة حاشية PaymentController | 35 | "3 sites" | إيضاح أن السياق مختلف (webhook يُفوّض إلى service) | PaymentWebhookController.php |

---

## 12. PROJECT_STATUS_REPORT.md

| التغيير | السطر | قبلاً | بعداً | المرجع |
|---------|:-----:|-------|-------|:------:|
| إضافة تنبيه أخطاء | 1-18 | — | إضافة stale notice مع قائمة الأخطاء المعروفة | Audit completo |
| إشارة إلى الملفات البديلة | 3-4 | — | روابط PROJECT_FULL_REAUDIT_REPORT, DOCS_SYNC_CHANGELOG | — |

> **ملاحظة:** تم إبقاء هذا الملف كما هو مع إضافة stale notice فقط، لأن تصحيح جميع الأخطاء العددية داخله كان سيتطلب إعادة كتابة كاملة وهذا يتعارض مع تعليمات "عدم تغيير بنية الملفات جذرياً". للتفاصيل الدقيقة → PROJECT_FULL_REAUDIT_REPORT_2026-07-09.md

---

## 13. verified-subscription-report.md (تمت إضافة stale notice)

> **⚠️ stale notice** — يوضح أن التقرير قديم (2026-07-08) وأن بعض الادعاءات (مثل مقارنة العملات) قد لا تكون دقيقة بعد إصلاحات AUD-1→AUD-4.

---

## 14. docs/subscription-middleware-fix-report.md (تمت إضافة stale notice)

> **⚠️ stale notice** — يوضح أن التقرير يعكس مرحلة سابقة وأن الوضع الحالي موثّق في PROJECT_FULL_REAUDIT_REPORT_2026-07-09.md.

---

## 15. ملفات جديدة تم إنشاؤها

### PROJECT_FULL_REAUDIT_REPORT_2026-07-09.md
تقرير كامل من الصفر يعكس الحالة الفعلية للكود بعد إصلاحات AUD-1→AUD-4. يشمل:
- تحقق مباشر من كل ادعاء في AUD-1→AUD-4 مع تفاصيل file:line
- جدول تناقضات التوثيق (14 تناقضاً قبل التحديث)
- فحص قاعدة البيانات (14 migration, 49 tables, 69 FKs, 40 indexes, 6 FULLTEXT)
- 17 مشكلة معماريّة مصنّفة بالأولوية (2 حرجة، 4 عالية، 7 متوسطة، 4 منخفضة)
- إحصاءات دقيقة للمشروع (34 models, 21 core services, 13 gateways, 17 middleware, 66 controllers, إلخ)
- توصيات P0/P1/P2

### DOCS_SYNC_CHANGELOG.md (هذا الملف)
سجل كامل بكل تغيير في كل ملف توثيق.

---

## إجمالي التغييرات

| البند | العدد |
|-------|:-----:|
| ملفات `.md` محدّثة | 12 |
| ملفات أُضيف لها stale notice | 3 |
| ملفات جديدة | 2 (هذا الملف + PROJECT_FULL_REAUDIT_REPORT) |
| إجمالي التعديلات النصية | 35+ |
| ملفات لم تُمس | 1 (`index.md` — لا توجد أخطاء فيه) |

---

## المنهجية

1. لكل ملف `.md`، قُرئ السطر تلو السطر
2. كل ادعاء قابل للتحقق تم مقابلته بالكود الفعلي (باستخدام grep + قراءة الملف)
3. الأخطاء تم تسجيلها في جدول التناقضات
4. التصحيحات طُبّقت مع الحفاظ على أسلوب الملف وبنية التنسيق
5. الملفات التي تحتوي على أخطاء جوهرية جداً ولا يمكن إصلاحها تجزئةً أُضيف لها stale notice + إشارة إلى التقرير البديل
6. تم إنشاء تقرير شامل جديد يعكس الوضع الدقيق الحالي
