# Finance Manager — Multi-Tenant SaaS

> تطبيق إدارة مالية شخصية ومحاسبة بنظام SAAS متعدد المستأجرين (Multi-Tenant) مبني على Laravel 13.  
> يدعم العربية (RTL)، الفرنسية، والإنجليزية.

**Status:** 🟢 Tests passing — 642/642 (11 skipped Chargily webhook tests)

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
| `SECURITY.md` | الأمان والصلاحيات: RBAC الكامل (13 دور)، 2FA، Policy، Rate Limiting |
| `TESTING.md` | الاختبارات: النتائج، التوزيع، حسابات الاختبار |
| `DATABASE_SCHEMA.md` | هيكل قاعدة البيانات: 49 جدول، Migration sequence |
| `INVITATION_SYSTEM.md` | نظام دعوات العملاء |

## Features

- **Financial Management**: Income, Expense, Debt, Asset, Budget, Goals, Zakat
- **Multi-Tenant**: Workspace isolation, RBAC (7 Platform + 6 Workspace roles)
- **Billing**: 4 plans, subscription lifecycle, proration, coupons, tax rates
- **Payments**: 13 gateways (Online + Manual + Courier + Cash)
- **Security**: 2FA, Sanctum API tokens, rate limiting, security headers
- **Localization**: Arabic (RTL), French, English — 31 domain files each
- **UI/UX**: Responsive, dark/light theme, command palette, RTL support
- **API**: RESTful API with 14 resource controllers, Sanctum auth

## Production Checklist

- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] Database: MySQL 8, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`
- [ ] `SESSION_SECURE_COOKIE=true`, configure HTTPS
- [ ] All gateway credentials set in `.env`
- [ ] `php artisan config:cache && route:cache && view:cache`
- [ ] Queue worker via supervisor, scheduler in crontab
- [ ] Composer: `--optimize-autoloader --no-dev`
- [ ] Vite: `npm run build`
