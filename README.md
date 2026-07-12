# Finance Manager — Multi-Tenant SaaS

> تطبيق إدارة مالية شخصية ومحاسبة بنظام SAAS متعدد المستأجرين (Multi-Tenant) مبني على Laravel 13.  
> يدعم العربية (RTL)، الفرنسية، والإنجليزية.

**Status:** 🟡 In active development — approaching production readiness

---

## Quick Start

```bash
cp .env.example .env
composer install && npm install && npm run build
php artisan key:generate && php artisan migrate:fresh --seed
php artisan serve
php artisan test
```

## Documentation

| File | المحتوى بالعربية |
|------|-----------------|
| `ARCHITECTURE.md` | معمارية المشروع، التقنيات، الـ Routes، الـ Services، الـ Patterns |
| `PAYMENT.md` | نظام الدفع: البوابات (13)، التدفق، Chargily، الـ Webhooks |
| `SUBSCRIPTIONS.md` | دورة الاشتراك الكاملة: براتا، كوبونات، فواتير، ترقية/تخفيض/تجديد |
| `SECURITY.md` | الأمان والصلاحيات: RBAC الكامل (14 دور)، 2FA، Policy، Rate Limiting |
| `TESTING.md` | الاختبارات: النتائج، التوزيع، حسابات الاختبار، الفجوات |
| `TODO.md` | المهام المتبقية |
| *(مؤرشف)* | تقرير الجاهزية للإطلاق (تم أرشفته بعد التحديث) |

## Features

- **Financial Management**: Income, Expense, Debt, Asset, Budget, Goals, Zakat
- **Multi-Tenant**: Workspace isolation, RBAC (7 Platform + 6 Workspace roles)
- **Billing**: 4 plans, subscription lifecycle, proration, coupons, tax rates
- **Payments**: 13 gateways (Online + Manual + Courier + Cash)
- **Security**: 2FA, Sanctum API tokens, rate limiting, security headers
- **Localization**: Arabic (RTL), French, English — 31 domain files each
- **UI/UX**: Responsive, dark/light theme, command palette, RTL support

## Production Checklist

- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] Database: MySQL 8, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`
- [ ] `SESSION_SECURE_COOKIE=true`, configure HTTPS
- [ ] All gateway credentials set in `.env`
- [ ] `php artisan config:cache && route:cache && view:cache`
- [ ] Queue worker via supervisor, scheduler in crontab
- [ ] Composer: `--optimize-autoloader --no-dev`
- [ ] Vite: `npm run build`
- [ ] 2FA forced for all admin accounts (ForceTwoFactor middleware currently inert)
