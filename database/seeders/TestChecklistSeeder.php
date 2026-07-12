<?php

namespace Database\Seeders;

use App\Models\TestChecklistItem;
use Illuminate\Database\Seeder;

class TestChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $items = $this->getItems();
        $sortOrder = 0;

        foreach ($items as $item) {
            TestChecklistItem::updateOrCreate(
                ['item_key' => $item['item_key']],
                [
                    'category' => $item['category'],
                    'description' => $item['description'],
                    'details' => $item['details'] ?? null,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }

    private function getItems(): array
    {
        return [
            // 1. Environment & Config (8 items)
            ['category' => '1. البيئة والإعدادات', 'item_key' => 'environment.app_env', 'description' => 'APP_ENV=production -- APP_DEBUG=false', 'details' => 'تأكد من APP_ENV=production و APP_DEBUG=false في .env. شغّل php artisan config:cache. تحقق عبر route /api/health أو tinker.'],
            ['category' => '1. البيئة والإعدادات', 'item_key' => 'environment.session_config', 'description' => 'SESSION_DRIVER=database|redis + SESSION_SECURE_COOKIE=true', 'details' => 'تأكد من SESSION_DRIVER=database|redis و SESSION_SECURE_COOKIE=true في .env. تحقق من جدول sessions إن كنت تستخدم database driver.'],
            ['category' => '1. البيئة والإعدادات', 'item_key' => 'environment.queue_connection', 'description' => 'QUEUE_CONNECTION=database', 'details' => 'تأكد من QUEUE_CONNECTION=database في .env. افحص config/queue.php. شغّل php artisan queue:work --once لاختبار المعالجة.'],
            ['category' => '1. البيئة والإعدادات', 'item_key' => 'environment.ssl_https', 'description' => 'SSL/HTTPS مفعل', 'details' => 'افتح الموقع عبر https://. تحقق من القفل الأخضر. استخدم ssllabs.com لفحص الشهادة.'],
            ['category' => '1. البيئة والإعدادات', 'item_key' => 'environment.optimize', 'description' => 'php artisan optimize ← دون أخطاء', 'details' => 'شغّل php artisan optimize. يجب ألا يظهر خطأ. تأكد من أن cache مجلداتها قابلة للكتابة.'],
            ['category' => '1. البيئة والإعدادات', 'item_key' => 'environment.storage_writable', 'description' => 'مجلدات storage/ و bootstrap/cache/ قابلة للكتابة', 'details' => 'شغّل php artisan storage:link إن لم يكن بعد. تأكد من صلاحيات الكتابة.'],
            ['category' => '1. البيئة والإعدادات', 'item_key' => 'environment.cron_schedule', 'description' => 'CRON الجدول الزمني مفعل: * * * * * php artisan schedule:run', 'details' => 'أضف السطر إلى crontab. تأكد بتشغيل crontab -e كمستخدم www-data.'],
            ['category' => '1. البيئة والإعدادات', 'item_key' => 'environment.api_keys', 'description' => 'مفاتيح API حقيقية في .env (Chargily, Mail, Sentry...)', 'details' => 'تأكد من وجود مفاتيح حقيقية لـ: CHARGILY_API_KEY, MAIL_MAILER, MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD, SENTRY_LARAVEL_DSN.'],

            // 2. Registration & Auth (7 items)
            ['category' => '2. التسجيل والمصادقة', 'item_key' => 'auth.register', 'description' => 'تسجيل ← إشعار تأكيد ← verification.notice ← poll تلقائي ← تأكيد ← onboarding.plan + بريد ترحيبي', 'details' => 'سجّل في /register. يتم تسجيل الدخول تلقائياً. يُرسل إشعار التحقق فقط. يُوجّه إلى /verify-email الذي يستعلم كل 3 ثوانٍ عن حالة التحقق. بعد النقر على رابط التفعيل، يُكتشف تلقائياً، يُرسل WelcomeEmail (عبر VerifyEmailController@__invoke)، ويُوجّه إلى /onboarding/plan. لا تُنشأ workspace بعد.'],
            ['category' => '2. التسجيل والمصادقة', 'item_key' => 'auth.email_unverified', 'description' => 'الحساب غير مفعل ← cannot access dashboard', 'details' => 'تحقق من users.email_verified_at = null. محاولة /dashboard تعيد التوجيه إلى /verify-email. بعد التفعيل ← onboarding.plan.'],
            ['category' => '2. التسجيل والمصادقة', 'item_key' => 'auth.login_remember', 'description' => 'تسجيل الدخول + تذكرني', 'details' => 'سجّل الدخول باختيار "تذكرني". تحقق من cookie remember_web_*. أعد فتح المتصفح — يجب البقاء مسجلاً.'],
            ['category' => '2. التسجيل والمصادقة', 'item_key' => 'auth.password_reset', 'description' => 'إعادة تعيين كلمة المرور', 'details' => 'اذهب إلى /forgot-password. أدخل بريداً. استلم رابط إعادة التعيين. غيّر كلمة المرور. سجّل الدخول بالجديدة.'],
            ['category' => '2. التسجيل والمصادقة', 'item_key' => 'auth.two_factor_enable', 'description' => 'تفعيل 2FA (Google Authenticator)', 'details' => 'الإعدادات → الأمان → تفعيل 2FA. امسح QR code. أدخل الرمز. سجّل الخروج والدخول — يُطلب رمز 2FA.'],
            ['category' => '2. التسجيل والمصادقة', 'item_key' => 'auth.two_factor_wrong', 'description' => 'رمز 2FA خاطئ ← رفض', 'details' => 'بعد تفعيل 2FA، أدخل رمزاً خاطئاً — يجب رفض الدخول مع رسالة خطأ.'],
            ['category' => '2. التسجيل والمصادقة', 'item_key' => 'auth.auto_poll_verify', 'description' => 'صفحة verification.notice تكتشف التفعيل تلقائياً', 'details' => 'بعد التسجيل، تبقى صفحة /verify-email تستعلم كل 3 ثوانٍ (wire:poll.3s). عند النقر على رابط التفعيل، تكتشف الصفحة التفعيل وتُوجّه تلقائياً إلى onboarding.plan دون تحديث يدوي.'],

            // 3. Onboarding (15 items)
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.view_plans', 'description' => 'عرض الباقات (شهرية/سنوية) مع المميزات والأسعار', 'details' => 'بعد التفعيل، يُوجّه إلى /onboarding/plan. تظهر الباقات من SubscriptionPlan: النشطة والعامة. يوجد toggle شهري/سنوي. تظهر المميزات (plan_features) والأسعار (plan_prices).'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.free_plan', 'description' => 'اختيار باقة مجانية ← إنشاء workspace + اشتراك Active فوراً', 'details' => 'اختر الباقة المجانية. تُنشأ workspace عبر ensureWorkspace(). يُنشأ اشتراك Active لمدة 10 سنوات. يُستدعى markPlanConfirmed(). يُوجّه إلى /onboarding/setup.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.paid_plan', 'description' => 'اختيار باقة مدفوعة ← اختيار طريقة الدفع ← توجيه حسب الطريقة', 'details' => 'اختر باقة مدفوعة. اختر طريقة دفع (Chargily, BaridiMob, RedotPay). للتوجيه: online ← بوابة الدفع، manual ← صفحة رفع الإثبات، auto_complete ← انتظار التأكيد.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.coupon', 'description' => 'استخدام كود خصم ← تطبيق الخصم على السعر', 'details' => 'اختر باقة مدفوعة. أدخل كود خصم في حقل "كود الخصم". يُتحقق من validity (النشاط، الصلاحية، الحد الأقصى، الحد الأدنى). يظهر تحليل السعر مع الخصم.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.coupon_gateway_restrict', 'description' => 'كود خصم مربوط ببوابة غير المختارة ← رسالة خطأ', 'details' => 'أنشئ كود خصم مربوطاً بـ Chargily فقط. اختر Edahabia كبوابة. أدخل الكود — يظهر خطأ "هذا الكوبون غير صالح لطريقة الدفع المحددة". اختر Chargily — يُقبل الكود.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.payment_failure', 'description' => 'دفع فاشل ← رسالة خطأ + إعادة المحاولة', 'details' => 'استخدم بطاقة مرفوضة أو ألغِ الدفع. تظهر رسالة خطأ. يمكن إعادة اختيار طريقة الدفع. لا يُفعّل الاشتراك.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.pending_block', 'description' => 'وجود دفع معلق ← منع الدخول إلى /onboarding/plan + التوجيه إلى payment.resume (مع تفاصيل الدفع + استمرار أو تغيير طريقة الدفع)', 'details' => 'أنشئ دفعاً معلقاً (checkout.pending). اذهب إلى /onboarding/plan. يُمنع الدخول ويُوجّه إلى /payment/resume/{id}. تظهر تفاصيل الدفع فوراً (بدون spinner). يوجد 3 أزرار: متابعة الدفع، تغيير طريقة الدفع، إلغاء الدفع.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.resume_continue', 'description' => '/payment/resume ← زر "متابعة الدفع" ← يعيد التوجيه إلى بوابة الدفع', 'details' => 'في صفحة resume، اضغط "متابعة الدفع". يُعاد التوجيه إلى بوابة الدفع (مثل Chargily). لا يظهر spinner انتظار.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.resume_cancel', 'description' => '/payment/resume ← زر "إلغاء الدفع" ← إلغاء الدفع + توجيه إلى /onboarding/plan', 'details' => 'في صفحة resume، اضغط "إلغاء الدفع". يُلغى الدفع (checkout.canceled). يُوجّه إلى /onboarding/plan.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.resume_switch', 'description' => '/payment/resume ← زر "تغيير طريقة الدفع" ← توجيه إلى /onboarding/payment', 'details' => 'في صفحة resume، اضغط "تغيير طريقة الدفع". يُوجّه إلى /onboarding/payment حيث يمكن اختيار طريقة جديدة.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.fee_breakdown_plan', 'description' => 'عرض تفصيل الرسوم في /onboarding/plan عند اختيار طريقة دفع', 'details' => 'اختر باقة مدفوعة ثم طريقة دفع. يظهر تحليل السعر: سعر الخطة، الخصم (إن وجد)، رسوم البوابة، الضريبة المضافة، المجموع.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.fee_breakdown_payment', 'description' => 'عرض تفصيل الرسوم في /onboarding/payment عند اختيار طريقة دفع', 'details' => 'اذهب إلى /onboarding/payment (يوجد باقة مختارة). اختر طريقة دفع. يظهر تحليل السعر مع الرسوم.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.fee_breakdown_retry', 'description' => 'عرض تفصيل الرسوم في /payment/retry/{id} من سجل الدفع', 'details' => 'اذهب إلى /payment/retry/{id} لدفع فاشل/ملغي. يظهر تفصيل الرسوم (رسوم البوابة، الضريبة) من سجل الدفع المخزّن.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.autocomplete_fix', 'description' => 'إصلاح autocomplete في البحث: إزالة name في وضع Livewire، استخدام name="{{ $name }}" في GET forms', 'details' => 'اختبر مربع البحث في صفحة super-admin (GET form). يجب إرسال ?search=value بدلاً من ?search_search=value. اختبر البحث في صفحة Livewire — لا يظهر suggest من المتصفح.'],
            ['category' => '3. الإعداد الأولي', 'item_key' => 'onboarding.complete', 'description' => 'إكمال الإعداد ← تسمية مساحة العمل ← completeOnboarding ← Dashboard', 'details' => 'بعد الدفع الناجح/الخطة المجانية، يُوجّه إلى /onboarding/setup. أدخل اسم workspace (اختياري). يُستدعى completeOnboarding(). يصبح onboarding_completed_at غير null. يُوجّه إلى /dashboard.'],

            // 4. Payment Gateways & Methods
            // 4.1 Chargily (active, online)
            ['category' => '4.1 Chargily (Online)', 'item_key' => 'chargily.checkout', 'description' => 'إنشاء Checkout ← التوجيه إلى صفحة Chargily', 'details' => 'اختر Chargily كطريقة دفع. يُنشأ checkout عبر ChargilyGateway. يظهر سجل في payments بحالة checkout.pending مع chargily_checkout_id. يُوجّه المتصفح إلى صفحة Chargily.'],
            ['category' => '4.1 Chargily (Online)', 'item_key' => 'chargily.payment_success', 'description' => 'دفع ناجح (CIB/Edahabia) ← اشتراك Active + فاتورة Paid', 'details' => 'في صفحة Chargily، استخدم CIB أو Edahabia. بعد الدفع، يُوجّه إلى /payment/chargily/result. تتحدّث حالة payment إلى checkout.paid. يُنشأ الاشتراك والفاتورة.'],
            ['category' => '4.1 Chargily (Online)', 'item_key' => 'chargily.payment_cancel', 'description' => 'إلغاء الدفع ← رسالة الإلغاء', 'details' => 'في صفحة Chargily، اضغط إلغاء. يُعاد إلى الموقع مع رسالة. حالة payment ← checkout.canceled. الاشتراك غير مُفعّل.'],
            ['category' => '4.1 Chargily (Online)', 'item_key' => 'chargily.webhook_valid', 'description' => 'Webhook بتوقيع صحيح ← 200', 'details' => 'أرسل POST /payment/webhook/chargily بتوقيع صحيح. يُعيد 200. تتحدّث حالة الدفع.'],
            ['category' => '4.1 Chargily (Online)', 'item_key' => 'chargily.webhook_invalid', 'description' => 'Webhook بتوقيع خاطئ ← 401 | بدون توقيع ← 403', 'details' => 'أرسل webhook بتوقيع خاطئ ← 401. بدون توقيع ← 403. يُسجّل المحاولة في logs.'],

            // 4.2 BaridiMob (active, manual)
            ['category' => '4.2 BaridiMob (Manual)', 'item_key' => 'baridimob.show_instructions', 'description' => 'عرض تعليمات التحويل (RIP/IBAN)', 'details' => 'اختر BaridiMob كطريقة دفع. تظهر تعليمات التحويل: رقم الحساب (RIP/IBAN)، اسم الحساب، المبلغ.'],
            ['category' => '4.2 BaridiMob (Manual)', 'item_key' => 'baridimob.upload_proof', 'description' => 'رفع إثبات الدفع ← قيد المراجعة', 'details' => 'ارفع صورة/PDF (حد 4MB). أدخل رقم مرجعي. يُنشأ سجل في payment_verifications بحالة pending.'],
            ['category' => '4.2 BaridiMob (Manual)', 'item_key' => 'baridimob.admin_approve', 'description' => '(Admin) قبول الإثبات ← اشتراك Active', 'details' => 'ادخل إلى super-admin → المدفوعات. افحص الإثبات. اضغط قبول. تتحدّث حالة الدفع إلى paid. يُفعّل الاشتراك. تُرسل الفاتورة.'],
            ['category' => '4.2 BaridiMob (Manual)', 'item_key' => 'baridimob.admin_reject', 'description' => '(Admin) رفض الإثبات ← failed', 'details' => 'في super-admin → المدفوعات، اضغط رفض وأدخل سبب الرفض. حالة الدفع ← failed. يُشعر المستخدم.'],

            // 4.3 RedotPay (active, manual)
            ['category' => '4.3 RedotPay (Manual)', 'item_key' => 'redotpay.show_instructions', 'description' => 'عرض تعليمات التحويل (Account ID)', 'details' => 'اختر RedotPay. تظهر تعليمات التحويل إلى حساب RedotPay.'],
            ['category' => '4.3 RedotPay (Manual)', 'item_key' => 'redotpay.upload_proof', 'description' => 'رفع إثبات الدفع ← قيد المراجعة', 'details' => 'ارفع إثبات الدفع (jpg/jpeg/png/pdf). أدخل المرجع. يُنشأ verification pending.'],
            ['category' => '4.3 RedotPay (Manual)', 'item_key' => 'redotpay.admin_approve', 'description' => '(Admin) قبول الإثبات', 'details' => 'نفس آلية BaridiMob: قبول ← paid + اشتراك Active.'],
            ['category' => '4.3 RedotPay (Manual)', 'item_key' => 'redotpay.admin_reject', 'description' => '(Admin) رفض الإثبات', 'details' => 'رفض ← failed + إشعار المستخدم.'],

            // 4.4 Other gateways (inactive but implemented)
            ['category' => '4.4 بوابات أخرى (غير نشطة)', 'item_key' => 'gateways.inactive_list', 'description' => 'البوابات المنفذة حالياً غير نشطة: Stripe, PayPal, Wise, Payoneer, Noest, Cash, Delivery', 'details' => 'هذه البوابات منفذة كـ Gateways في app/Services/Payments/ لكنها غير نشطة في قاعدة البيانات (is_active=false). لتفعيلها: عدّل payment_methods.is_active = true أو استخدم PaymentGatewaySeeder.'],

            // 5. Subscriptions & Invoices — Upgrade/Downgrade (30+ items)
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.activation', 'description' => 'بعد دفع ناجح ← اشتراك Active + فاتورة Paid', 'details' => 'بعد أي دفع ناجح: تحقق من subscriptions — سجل Active مع starts_at/ends_at. تحقق من invoices — فاتورة Paid بالمبلغ والضريبة.'],
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.cancel', 'description' => 'إلغاء الاشتراك ← Canceled + بريد تأكيد الإلغاء', 'details' => 'الإعدادات → الاشتراك → إلغاء. حالة الاشتراك ← Canceled مع canceled_at. فترة السماح (grace period) سارية. يُرسل بريد SubscriptionCancelled.'],
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.cancel_email', 'description' => 'إلغاء الاشتراك ← بريد تأكيد الإلغاء (SubscriptionCancelled)', 'details' => 'ألغِ اشتراكاً. تحقق من queue: وظيفة SubscriptionCancelled تحتوي على اسم الخطة وتاريخ الانتهاء.'],
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.upgrade_email', 'description' => 'ترقية الباقة ← بريد ترقية (SubscriptionUpgraded)', 'details' => 'غيّر الباقة إلى أعلى سعراً. يُرسل بريد SubscriptionUpgraded مع اسمي الباقتين.'],
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.downgrade_email', 'description' => 'تخفيض الباقة ← بريد تخفيض (SubscriptionDowngraded)', 'details' => 'غيّر الباقة إلى أقل سعراً. يُرسل بريد SubscriptionDowngraded مع اسمي الباقتين وفترة التفعيل.'],
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.auto_renew', 'description' => 'تجديد تلقائي مع فاتورة جديدة', 'details' => 'تأكد من auto_renew=true. شغّل schedule:run. تُنشأ فاتورة جديدة. تتحدّث starts_at/ends_at.'],
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.past_due', 'description' => 'فشل تجديد ← Past Due + إشعار', 'details' => 'عطّل auto_renew. شغّل التجديد. الاشتراك ← Past Due. يُرسل إشعار للمستخدم.'],
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.change_plan', 'description' => 'تغيير الباقة مع proration', 'details' => 'الإعدادات → الاشتراك → تغيير الباقة. يُحسب proration. تُنشأ فاتورة بالمبلغ الصحيح.'],
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.expired_block', 'description' => 'منع استخدام الميزات بعد انتهاء الاشتراك', 'details' => 'اجعل الاشتراك منتهياً. حاول إنشاء معاملة — يجب المنع عبر middleware.'],
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.fee_breakdown', 'description' => 'عرض تفصيل الرسوم (Fee Breakdown) قبل تغيير الباقة', 'details' => 'اذهب إلى /account/subscriptions. اختر باقة مدفوعة. اختر طريقة دفع. يظهر تفصيل: السعر، الخصم، رسوم البوابة، الضريبة، الإجمالي. يتحدّث تلقائياً عند تغيير طريقة الدفع أو إدخال كوبون.'],
            ['category' => '5. الاشتراكات والفواتير', 'item_key' => 'subscriptions.collapsible_features', 'description' => 'عرض المميزات بشكل قابل للطي (Collapse) في بطاقات الباقات', 'details' => 'في صفحة الاشتراكات: أول 5 مميزات فقط مع زر "عرض المزيد". في landing page: أول 3 مميزات مع زر "عرض المزيد".'],

            // === 5.A الترقية (Upgrade) ===
            ['category' => '5.A الترقية', 'item_key' => 'upgrade.free_to_business', 'description' => 'ترقية من مجاني إلى Business (شهري) — دفع كامل السعر', 'details' => 'أنشئ حساباً بخطة Personal. اذهب إلى /account/subscriptions. اختر Business (شهري). اختر طريقة دفع. تحقق أن المطلوب = سعر Business كاملاً (لأن المجاني ليس له فترة متبقية). أتمم الدفع. تحقق: اشتراك Business Active مع starts_at/ends_at. payment بحالة paid. فاتورة بالمبلغ الصحيح. بريد SubscriptionUpgraded.'],
            ['category' => '5.A الترقية', 'item_key' => 'upgrade.midcycle_proration', 'description' => 'ترقية من Business إلى Professional في منتصف الدورة — proration تناسبي', 'details' => 'أنشئ اشتراك Business شهري بدأ قبل 10 أيام. اختر Professional شهري. تحقق من Fee Breakdown: يظهر مبلغ proration = (سعر Professional اليومي × 20) - (سعر Business اليومي × 20). أتمم الدفع. تحقق: ends_at الجديد = نفس ends_at القديم (محمول).'],
            ['category' => '5.A الترقية', 'item_key' => 'upgrade.monthly_to_yearly', 'description' => 'ترقية مع تغيير فترة الفوترة (شهري → سنوي) — proration بين السعرين', 'details' => 'اشتراك Business شهري. غيّر إلى Business سنوي. يُحسب proration على الفرق بين السعر الشهري المتبقي والسعر السنوي للفترة المتبقية.'],
            ['category' => '5.A الترقية', 'item_key' => 'upgrade.with_coupon', 'description' => 'ترقية مع كوبون خصم — تطبيق الخصم بعد proration', 'details' => 'Business → Professional مع كوبون خصم 20%. تحقق: الخصم يُطبق على المبلغ الإجمالي. راجع PaymentService::chargeForPlan() عند overrideAmount مع coupon.'],

            // === 5.B التخفيض (Downgrade) ===
            ['category' => '5.B التخفيض', 'item_key' => 'downgrade.full_credit', 'description' => 'تخفيض مع رصيد (السعر <= 0) — تفعيل فوري بدون دفع', 'details' => 'اشتراك Professional شهري (بدأ منذ 5 أيام). اختر Business شهري. protation: القيمة المتبقية من Professional > سعر الفترة المتبقية من Business → مبلغ سالب. الاشتراك يُفعّل فوراً (status=active). فاتورة مع proration_credit. لا حاجة لدفع. بريد SubscriptionDowngraded.'],
            ['category' => '5.B التخفيض', 'item_key' => 'downgrade.partial_payment', 'description' => 'تخفيض مع دفعة مخفضة (السعر > 0 بعد proration)', 'details' => 'اشتراك Professional سنوي (بدأ منذ شهر). اختر Business شهري. proration يعطي مبلغاً موجباً لكن أقل من سعر Business الكامل. ادفع المبلغ المخفض عبر بوابة. تحقق من الاشتراك والفاتورة.'],
            ['category' => '5.B التخفيض', 'item_key' => 'downgrade.blocked_too_many_users', 'description' => 'تخفيض محظور — مستخدمون أكثر من max_users للباقة', 'details' => 'اشتراك Professional (max_users=50). أضف 20 مستخدم. حاول التخفيض إلى Business (max_users=10). يجب ظهور خطأ "عدد المستخدمين (20) يتجاوز الحد الأقصى (10)".'],
            ['category' => '5.B التخفيض', 'item_key' => 'downgrade.blocked_small_discount', 'description' => 'تخفيض محظور — فرق سعري أقل من 10%', 'details' => 'إذا كان الفرق بين سعر الباقة الحالية والجديدة < 10%، يجب رفض التخفيض مع رسالة. النسبة قابلة للتعديل في config/finance.downgrade_min_discount_percent.'],

            // === 5.C منع الدفع المعلق ===
            ['category' => '5.C منع الدفع المعلق', 'item_key' => 'pending_block_change_plan', 'description' => 'وجود دفع معلق ← منع تغيير الباقة مع رسالة', 'details' => 'أنشئ دفعاً معلقاً (checkout.pending) لترقية لم تكتمل. حاول تغيير الباقة مرة أخرى. يجب ظهور "يوجد طلب دفع قيد الانتظار". إلغاء الدفع أولاً أو إكماله.'],

            // === 5.D استئناف (Resume) ===
            ['category' => '5.D استئناف', 'item_key' => 'resume.during_grace', 'description' => 'استئناف اشتراك ملغي ضمن فترة السماح ← Active', 'details' => 'ألغِ اشتراك Business. اذهب إلى /account/subscriptions. يجب ظهور زر "استئناف الاشتراك". اضغط. تحقق: canceled_at = null. الاشتراك عاد Active مع ends_at نفسه.'],
            ['category' => '5.D استئناف', 'item_key' => 'resume.after_grace', 'description' => 'استئناف بعد انتهاء فترة السماح ← ممنوع', 'details' => 'انتظر 3 أيام (أو عدّل ends_at/grace_ends_at يدوياً). زر الاستئناف لا يظهر أو يعيد خطأ.'],

            // === 5.E إلغاء الاشتراك ===
            ['category' => '5.E إلغاء الاشتراك', 'item_key' => 'cancel.immediate', 'description' => 'إلغاء فوري ← Canceled + ends_at=now + محاولة رد المبلغ', 'details' => 'الإعدادات → الاشتراك → إلغاء فوري. الاشتراك ← Canceled مع ends_at=now. يُحاول رد المبلغ عبر البوابة (إن كانت online). يُرسل بريد SubscriptionCancelled.'],
            ['category' => '5.E إلغاء الاشتراك', 'item_key' => 'cancel.period_end_api', 'description' => 'إلغاء بنهاية الفترة (عبر API) ← Canceled + ends_at الأصلي + Grace', 'details' => 'هذا المسار متاح عبر API فقط حالياً. الاشتراك ← Canceled مع ends_at الأصلي. فترة السماح سارية حتى ends_at.'],

            // === 5.F إلغاء الدفع ===
            ['category' => '5.F إلغاء الدفع', 'item_key' => 'cancel_payment.from_subscriptions', 'description' => 'إلغاء دفع معلق من صفحة الاشتراكات', 'details' => 'أنشئ دفعاً معلقاً. اذهب إلى /account/subscriptions. يظهر تنبيه مع زر "إلغاء الدفع". اضغط. تحقق: payment.status = checkout.canceled + canceled_at.'],
            ['category' => '5.F إلغاء الدفع', 'item_key' => 'cancel_payment.unblocks_change', 'description' => 'إلغاء دفع معلق ← إتاحة تغيير الباقة مرة أخرى', 'details' => 'بعد إلغاء الدفع المعلق، حاول تغيير الباقة — يجب أن يسمح الآن.'],

            // === 5.G تغيير طريقة الدفع ===
            ['category' => '5.G تغيير طريقة الدفع', 'item_key' => 'change_payment_method', 'description' => 'تغيير طريقة الدفع لاشتراك نشط', 'details' => 'في صفحة الاشتراكات، قسم "تغيير طريقة الدفع". اختر طريقة جديدة من dropdown. اضغط تحديث. تحقق من تحديث subscription.payment_method.'],

            // === 5.H صلاحيات تغيير الباقة ===
            ['category' => '5.H صلاحيات التغيير', 'item_key' => 'permissions.owner_can_change', 'description' => 'مالك مساحة العمل فقط — يستطيع تغيير الباقة', 'details' => 'سجّل كمالك. اذهب إلى /account/subscriptions. اختر باقة. يجب نجاح التغيير.'],
            ['category' => '5.H صلاحيات التغيير', 'item_key' => 'permissions.member_cannot_change', 'description' => 'عضو عادي — لا يستطيع تغيير الباقة', 'details' => 'ادعُ مستخدم جديد بصلاحية مشاهدة. سجّل به. اذهب إلى /account/subscriptions. يجب عدم ظهور نموذج تغيير الباقة أو منع الطلب بـ 403.'],

            // === 5.I البريد الإلكتروني ===
            ['category' => '5.I بريد التغيير', 'item_key' => 'email.upgrade_sent', 'description' => 'بريد ترقية (SubscriptionUpgraded) — يصل بعد تغيير الباقة بنجاح', 'details' => 'غيّر الباقة إلى أعلى. تحقق من queue. البريد يحتوي على اسمي الباقتين (القديمة والجديدة).'],
            ['category' => '5.I بريد التغيير', 'item_key' => 'email.downgrade_sent', 'description' => 'بريد تخفيض (SubscriptionDowngraded) — يصل بعد تخفيض الباقة', 'details' => 'غيّر الباقة إلى أقل. البريد يحتوي على اسمي الباقتين وتاريخ التفعيل.'],
            ['category' => '5.I بريد التغيير', 'item_key' => 'email.no_email_first_sub', 'description' => 'عدم إرسال بريد تغيير لأول اشتراك (ليس تغييراً)', 'details' => 'أول مرة يشتري فيها المستخدم باقة (ليس تغييراً)، لا يُرسل بريد ترقية/تخفيض.'],

            // === 5.J سيناريوهات متقدمة ===
            ['category' => '5.J سيناريوهات متقدمة', 'item_key' => 'advanced.free_to_pro', 'description' => 'ترقية من مجاني إلى Professional مباشرة — proration من 0', 'details' => 'Personal → Professional. السعر المطلوب = سعر Professional كاملاً (المجاني ليس له قيمة متبقية).'],
            ['category' => '5.J سيناريوهات متقدمة', 'item_key' => 'advanced.repeated_changes', 'description' => 'تغيير متكرر (Business → Professional → Business) — اختبار تراكم الاشتراكات', 'details' => 'غيّر من Business إلى Professional. ثم غيّر مجدداً إلى Business. تحقق من سجل الاشتراكات: الـ 3 اشتراكات مسجلة. لا اشتراكات مكررة أو معلقة.'],
            ['category' => '5.J سيناريوهات متقدمة', 'item_key' => 'advanced.cancel_stale_past_due', 'description' => 'إلغاء اشتراك Past Due تلقائياً عند تغيير الباقة', 'details' => 'ابدأ تغيير باقة ولم تكمل الدفع (يتبقى اشتراك past_due). حاول تغيير الباقة مرة أخرى. يتم إلغاء الـ past_due تلقائياً قبل إنشاء الاشتراك الجديد عبر cancelStalePendingSubscriptions().'],
            ['category' => '5.J سيناريوهات متقدمة', 'item_key' => 'advanced.zero_remaining_days', 'description' => 'تصفير proration (remaining_days <= 0) — السعر = سعر الخطة كاملاً', 'details' => 'غيّر الباقة في آخر يوم من الفترة. remaining_days = 0 → proration يتجاوز. السعر = سعر الخطة كاملاً.'],
            ['category' => '5.J سيناريوهات متقدمة', 'item_key' => 'advanced.resume_only_owner', 'description' => 'استئناف الاشتراك — مالك مساحة العمل فقط', 'details' => 'العضو العادي لا يرى زر الاستئناف أو يُمنع بـ 403. المالك فقط يستطيع الاستئناف.'],
            ['category' => '5.J سيناريوهات متقدمة', 'item_key' => 'advanced.payment_failure_rollback', 'description' => 'فشل الدفع عند تغيير الباقة ← إلغاء الاشتراك الجديد + رسالة خطأ', 'details' => 'حاول الترقية ببطاقة مرفوضة. الاشتراك الجديد يُلغى (status=canceled). لا يتأثر الاشتراك القديم. رسالة "حدث خطأ في بوابة الدفع".'],

            // 6. Asset Management (7 items)
            ['category' => '6. إدارة الأصول', 'item_key' => 'assets.create_types', 'description' => 'إضافة أصل بجميع الأنواع', 'details' => 'اذهب إلى الأصول → إضافة أصل. الأنواع الفعلية: نقدي (Cash)، بنكي (BankAccount)، CCP، ذهب (Gold)، فضة (Silver)، عقار (RealEstate)، أسهم (Stocks)، عملات رقمية (Crypto)، أخرى (Other).'],
            ['category' => '6. إدارة الأصول', 'item_key' => 'assets.crud', 'description' => 'تعديل وحذف (ناعم + نهائي)', 'details' => 'تعديل اسم/قيمة ← حفظ. حذف ناعم ← يظهر في سلة المهملات. حذف نهائي ← يُزال بالكامل. soft_deletes في DB.'],
            ['category' => '6. إدارة الأصول', 'item_key' => 'assets.restore', 'description' => 'استعادة من سلة المهملات', 'details' => 'احذف ناعم. اذهب إلى سلة المهملات. استعادة ← deleted_at = null.'],
            ['category' => '6. إدارة الأصول', 'item_key' => 'assets.filter_search', 'description' => 'تصفية حسب النوع + بحث بالاسم', 'details' => 'فلتر النوع يظهر أصول نوع واحد. بحث بالاسم يصفّي النتائج. يمكن الجمع بينهما.'],
            ['category' => '6. إدارة الأصول', 'item_key' => 'assets.bulk_actions', 'description' => 'عمليات جماعية (حذف/استعادة)', 'details' => 'اختر أصولاً باستخدام checkbox. حذف جماعي ← ناعم. استعادة جماعية من سلة المهملات.'],
            ['category' => '6. إدارة الأصول', 'item_key' => 'assets.indicators', 'description' => 'مؤشرات الأداء (الإجمالي، السائل، القابل للزكاة، العدد)', 'details' => 'في صفحة الأصول: إجمالي القيمة، الأصول السائلة (نقدي+بنكي+CCP)، الأصول القابلة للزكاة (AssetType::zakatable())، عدد الأصول.'],
            ['category' => '6. إدارة الأصول', 'item_key' => 'assets.export', 'description' => 'تصدير إلى Excel/CSV/PDF', 'details' => 'زر التصدير. جرب Excel (.xlsx), CSV (.csv), PDF (.pdf). الملف يحتوي على الأصول المعروضة.'],

            // 7. Incomes & Expenses (4 items)
            ['category' => '7. الإيرادات والمصروفات', 'item_key' => 'income.crud', 'description' => 'إضافة/تعديل/حذف إيراد مع الفئة', 'details' => 'اذهب إلى الإيرادات. أضف: المبلغ، التاريخ، الفئة (IncomeCategory)، الوصف. تعديل ← حفظ. حذف ناعم ونهائي.'],
            ['category' => '7. الإيرادات والمصروفات', 'item_key' => 'expense.crud', 'description' => 'إضافة/تعديل/حذف مصروف مع الفئة', 'details' => 'اذهب إلى المصروفات. أضف: المبلغ، التاريخ، الفئة (ExpenseCategory)، الوصف. يظهر في التقارير.'],
            ['category' => '7. الإيرادات والمصروفات', 'item_key' => 'categories.manage', 'description' => 'إدارة فئات الإيرادات والمصروفات', 'details' => 'الإعدادات → فئات الإيرادات/المصروفات. أضف/عدّل/احذف فئة. اختر عامة/خاصة.'],
            ['category' => '7. الإيرادات والمصروفات', 'item_key' => 'transactions.export', 'description' => 'تصدير المعاملات', 'details' => 'اذهب إلى المعاملات. صدّر إلى Excel/CSV/PDF. الملف يحتوي على التاريخ، المبلغ، الفئة، النوع.'],

            // 8. Budgets (4 items)
            ['category' => '8. الميزانيات', 'item_key' => 'budgets.create', 'description' => 'إنشاء ميزانية (شهر/سنة + فئة + حد)', 'details' => 'اذهب إلى الميزانيات. أنشئ: الشهر، فئة مصروف، حد الإنفاق.'],
            ['category' => '8. الميزانيات', 'item_key' => 'budgets.consumption', 'description' => 'تسجيل مصروف ← تحديث نسبة الاستهلاك', 'details' => 'أضف مصروفاً ضمن فئة الميزانية. يتحدّث spent_amount ونسبة (spent/limit*100).'],
            ['category' => '8. الميزانيات', 'item_key' => 'budgets.warning_80', 'description' => 'تحذير عند 80%', 'details' => 'اجعل الاستهلاك 80%. يظهر تحذير. يُرسل إشعار.'],
            ['category' => '8. الميزانيات', 'item_key' => 'budgets.alert_exceed', 'description' => 'إنذار عند تجاوز الحد', 'details' => 'تجاوز 100%. يظهر إنذار. يُرسل إشعار. قد يُمنع إضافة مصروفات إضافية.'],

            // 9. Debts (4 items)
            ['category' => '9. الديون', 'item_key' => 'debts.create', 'description' => 'إضافة دين مع النوع والطرف والاستحقاق', 'details' => 'اذهب إلى الديون. الأنواع الفعلية: دين عليّ (Owed/owing) — تحقق من DebtType enum. أدخل المبلغ، الطرف المقابل، تاريخ الاستحقاق.'],
            ['category' => '9. الديون', 'item_key' => 'debts.partial', 'description' => 'تسديد جزئي + تحديث المبلغ المتبقي', 'details' => 'أضف دفعة جزئية. يتحدّث المبلغ المتبقي. الحالة ← Partial.'],
            ['category' => '9. الديون', 'item_key' => 'debts.full_payment', 'description' => 'تسديد كامل ← Paid', 'details' => 'أضف دفعة بكامل المبلغ. المبلغ المتبقي = 0. الحالة ← Paid.'],
            ['category' => '9. الديون', 'item_key' => 'debts.overdue', 'description' => 'تصنيف الديون المتأخرة تلقائياً', 'details' => 'إذا مرّ تاريخ الاستحقاق ولم يُسدّد، الحالة ← Overdue. يظهر في dashboard.'],

            // 10. Zakat (6 items)
            ['category' => '10. الزكاة', 'item_key' => 'zakat.calculator_load', 'description' => 'تحميل الأصول القابلة للزكاة تلقائياً', 'details' => 'اذهب إلى الزكاة → حاسبة الزكاة. الأنواع القابلة: نقدي، بنكي، CCP، ذهب، فضة، أسهم، عملات رقمية (AssetType::zakatable()). عقار لا يظهر.'],
            ['category' => '10. الزكاة', 'item_key' => 'zakat.nisab', 'description' => 'حساب النصاب (85g ذهب / 595g فضة)', 'details' => 'أدخل سعر الذهب (عيار 24) والفضة. يُحسب النصاب. يظهر إشارة بلوغ النصاب.'],
            ['category' => '10. الزكاة', 'item_key' => 'zakat.calculate', 'description' => 'حساب الزكاة (2.5%)', 'details' => 'المجموع القابل للزكاة × 2.5%. إضافة/إزالة أصول يحدّث النتيجة.'],
            ['category' => '10. الزكاة', 'item_key' => 'zakat.save', 'description' => 'حفظ النتيجة في سجل الزكاة', 'details' => 'اضغط حفظ. يظهر السجل في سجل الزكاة.'],
            ['category' => '10. الزكاة', 'item_key' => 'zakat.report', 'description' => 'عرض تقرير زكاة محفوظ', 'details' => 'اختر عملية من السجل. تظهر التفاصيل: الأصول، الأسعار، النصاب، المبلغ.'],
            ['category' => '10. الزكاة', 'item_key' => 'zakat.print', 'description' => 'طباعة التقرير', 'details' => 'اضغط طباعة. تنسيق مناسب للطباعة. اختبر الطباعة إلى PDF.'],

            // 11. Dashboard & Reports (3 items)
            ['category' => '11. لوحة التحكم والتقارير', 'item_key' => 'dashboard.summary', 'description' => 'Dashboard يعرض ملخص الأصول + الإيرادات + المصروفات + الميزانية + آخر المعاملات + الديون القادمة', 'details' => 'اذهب إلى /dashboard. يظهر: ملخص الأصول (الإجمالي)، الإيرادات (الشهر)، المصروفات (الشهر)، حالة الميزانية، آخر 5 معاملات، الديون القادمة (30 يوماً).'],
            ['category' => '11. لوحة التحكم والتقارير', 'item_key' => 'dashboard.livewire', 'description' => 'تحديث فوري عبر Livewire polling/events', 'details' => 'أضف معاملة في نافذة. dashboard في نافذة أخرى — تتغير الأرقام دون تحديث يدوي.'],
            ['category' => '11. لوحة التحكم والتقارير', 'item_key' => 'reports.accuracy', 'description' => 'التقارير (الأصول/الإيرادات/المصروفات/التدفق النقدي) بأرقام صحيحة', 'details' => 'اذهب إلى التقارير. قارن الأرقام مع قاعدة البيانات. (الإيرادات - المصروفات) = صافي التدفق.'],

            // 12. Global Search (3 items)
            ['category' => '12. البحث العام', 'item_key' => 'search.results', 'description' => 'البحث عبر الأصول/الإيرادات/المصروفات/الديون', 'details' => 'استخدم مربع البحث العام. اكتب اسم/وصف — تظهر النتائج من جميع الفئات.'],
            ['category' => '12. البحث العام', 'item_key' => 'search.navigation', 'description' => 'النقر على نتيجة ← التوجيه للصفحة المعنية', 'details' => 'انقر على نتيجة أصل ← يُوجّه لصفحة الأصل. اختبر جميع الأنواع.'],
            ['category' => '12. البحث العام', 'item_key' => 'search.no_results', 'description' => 'بحث بدون نتائج ← رسالة', 'details' => 'اكتب نصاً عشوائياً ← "لا توجد نتائج". لا أخطاء.'],

            // 13. User Settings (5 items)
            ['category' => '13. إعدادات المستخدم', 'item_key' => 'profile.edit', 'description' => 'تعديل الملف الشخصي (الاسم، البريد، كلمة المرور)', 'details' => 'الإعدادات → الملف الشخصي. غير الاسم ← حفظ. غير البريد ← إعادة تحقق. غير كلمة المرور ← إعادة تسجيل دخول.'],
            ['category' => '13. إعدادات المستخدم', 'item_key' => 'notifications.markread_page', 'description' => 'زر "تحديد كمقروء" في صفحة الإشعارات ← redirect + flash message', 'details' => 'اذهب إلى /notifications. اضغط "تحديد كمقروء" على إشعار غير مقروء. يُعاد تحميل الصفحة مع رسالة نجاح.'],
            ['category' => '13. إعدادات المستخدم', 'item_key' => 'notifications.locale_icons', 'description' => 'صفحة الإشعارات + الإشعارات في الإعدادات: أيقونات صحيحة حسب نوع الإشعار', 'details' => 'تحقق من ظهور أيقونات مناسبة لأنواع: budget_exceeded, debt_reminder, goal_achieved, zakat_reminder, role_changed. لا تظهر كلها بأيقونة info-circle.'],
            ['category' => '13. إعدادات المستخدم', 'item_key' => 'profile.language', 'description' => 'تبديل اللغة (عربي/فرنسي/إنجليزي)', 'details' => 'الإعدادات → اللغة. اختر لغة — تتغير الواجهة فوراً.'],
            ['category' => '13. إعدادات المستخدم', 'item_key' => 'profile.theme', 'description' => 'تبديل الثيم (فاتح/داكن)', 'details' => 'الإعدادات → المظهر. بدّل الثيم — تطبيق فوري.'],
            ['category' => '13. إعدادات المستخدم', 'item_key' => 'profile.2fa', 'description' => 'تفعيل 2FA', 'details' => 'الإعدادات → الأمان. امسح QR code. أدخل الرمز للتحقق.'],
            ['category' => '13. إعدادات المستخدم', 'item_key' => 'profile.sessions', 'description' => 'عرض وقطع الجلسات النشطة', 'details' => 'الإعدادات → الأمان → الجلسات. اقطع جلسة عن بعد — تُنهى فوراً.'],

            // 14. Roles & Permissions (5 items)
            ['category' => '14. الصلاحيات والأدوار', 'item_key' => 'roles.invite', 'description' => 'دعوة مستخدم ← بريد ← قبول ← انضمام', 'details' => 'إعدادات مساحة العمل → الأعضاء. أدخل بريد مستخدم، اختر صلاحية، أرسل. المدعو يستلم بريداً. يقبل الدعوة — ينضم.'],
            ['category' => '14. الصلاحيات والأدوار', 'item_key' => 'roles.read_only', 'description' => 'صلاحية مشاهدة فقط ← لا تعديل ولا إضافة', 'details' => 'ادعُ بمشاهدة فقط. يحاول إضافة/تعديل/حذف — يُمنع.'],
            ['category' => '14. الصلاحيات والأدوار', 'item_key' => 'roles.editor', 'description' => 'صلاحية محرر ← إضافة وتعديل فقط', 'details' => 'يستطيع إضافة وتعديل. لا يستطيع حذف.'],
            ['category' => '14. الصلاحيات والأدوار', 'item_key' => 'roles.manager', 'description' => 'صلاحية مدير ← صلاحية كاملة', 'details' => 'إضافة، تعديل، حذف، دعوة، تغيير صلاحيات — كل شيء مسموح.'],
            ['category' => '14. الصلاحيات والأدوار', 'item_key' => 'roles.cross_workspace', 'description' => 'منع الوصول عبر مساحات العمل', 'details' => 'سجّل الدخول بحسابين. حسّب 1: سجّل ID أصل. حسّب 2: حاول فتح /assets/{id} — 403.'],

            // 15. Queue & Schedule (4 items)
            ['category' => '15. مهام الخلفية', 'item_key' => 'queue.work', 'description' => 'queue:work يعالج المهام دون تأخير', 'details' => 'شغّل queue:work --tries=3. أضف مهمة بريد — تُعالج فوراً. تحقق من jobs/failed_jobs.'],
            ['category' => '15. مهام الخلفية', 'item_key' => 'queue.email_delivery', 'description' => 'إرسال البريد عبر queue', 'details' => 'قم بإجراء يتطلب بريداً (إعادة تعيين كلمة مرور). يصل البريد فوراً مع HTML صحيح.'],
            ['category' => '15. مهام الخلفية', 'item_key' => 'queue.invoice_creation', 'description' => 'إنشاء الفاتورة عبر queue', 'details' => 'أكمل دفعاً ناجحاً. تُنشأ الفاتورة عبر queue. تحتوي على الرقم، المبلغ، الضريبة، التاريخ.'],
            ['category' => '15. مهام الخلفية', 'item_key' => 'schedule.run', 'description' => 'schedule:run ← تجديد + إشعارات (7d/3d/1d)', 'details' => 'شغّل schedule:run. يتجدّد الاشتراكات (auto_renew=true). تُرسل إشعارات قبل 7/3/1 يوم من الانتهاء.'],

            // 16. Security (6 items)
            ['category' => '16. الأمان', 'item_key' => 'security.csrf', 'description' => 'POST تحتاج CSRF token ← 419 بدونها', 'details' => 'أرسل POST بدون CSRF — 419 PAGE EXPIRED.'],
            ['category' => '16. الأمان', 'item_key' => 'security.xss', 'description' => 'إدخال <script> ← escaped', 'details' => 'أدخل <script> في حقل نصي. يظهر كـ escaped ولا يُنفّذ.'],
            ['category' => '16. الأمان', 'item_key' => 'security.sql_injection', 'description' => 'SQL injection محظور', 'details' => 'أدخل \' OR 1=1 في البحث. لا تسريب. Eloquent آمن.'],
            ['category' => '16. الأمان', 'item_key' => 'security.rate_limit', 'description' => 'Rate limiting (60 req/min)', 'details' => 'أرسل طلبات سريعة ← بعد 60 طلب/دقيقة، 429 Too Many Requests.'],
            ['category' => '16. الأمان', 'item_key' => 'security.404', 'description' => 'صفحة 404 مخصصة', 'details' => 'افتح رابطاً غير موجود. صفحة 404 بتصميم الموقع.'],
            ['category' => '16. الأمان', 'item_key' => 'security.403', 'description' => 'صفحة 403 للموارد غير المصرح بها', 'details' => 'مستخدم عادي يحاول /super-admin — 403 مخصصة.'],

            // 17. Email (8 items)
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.welcome_activation', 'description' => 'تأكيد الحساب ← بريد ترحيبي', 'details' => 'بعد التسجيل: يُرسل SendEmailVerificationNotification فقط. بعد النقر على رابط التفعيل: يُرسل WelcomeEmail (ترحيبي) عبر queue. رابط التفعيل ينقل إلى /onboarding/plan.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.welcome_delivered', 'description' => 'البريد الترحيبي يُسلّم بعد التفعيل عبر queue', 'details' => 'سجّل مستخدم جديد. فعّل البريد الإلكتروني (انقر رابط التفعيل). تحقق من queue: تظهر وظيفة WelcomeEmail. تأكد من تشغيل queue:work لمعالجتها.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.password_reset', 'description' => 'بريد إعادة تعيين كلمة المرور', 'details' => 'اطلب من /forgot-password. البريد يحتوي على رابط. الرابط ينقل إلى reset-password.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.invoice_receipt', 'description' => 'بريد فاتورة الدفع', 'details' => 'بعد دفع ناجح: بريد الفاتورة مع الرقم، المبلغ، تاريخ الدفع، اسم الباقة.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.expiry_warning', 'description' => 'إشعارات انتهاء الاشتراك', 'details' => 'SubscriptionExpiryWarning يُرسل 7d/3d/1d قبل انتهاء الاشتراك.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.invitation', 'description' => 'بريد دعوة مستخدم', 'details' => 'بعد دعوة مستخدم: بريد يحتوي على اسم مساحة العمل، رابط القبول (48 ساعة صلاحية).'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.locale_user', 'description' => 'جميع الإيميلات بلغة المستخدم المختارة', 'details' => 'سجّل بلغة إنجليزية → بريد الترحيب بالإنجليزية. بدّل اللغة إلى فرنسي → بريد 2FA بالفرنسي. اختبر welcome, 2FA, receipt, reminder.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.design_unified', 'description' => 'جميع القوالب البريدية تستخدم تصميم موحد حديث', 'details' => 'افتح بريد التحقق، الترحيبي، 2FA، الإيصال في Gmail/Outlook. تحقق من تناسق الألوان، الخطوط، الأزرار، التذييل. نسبة الدعم >90% في caniemail.com.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.subscription_cancelled', 'description' => 'بريد إلغاء الاشتراك ← SubscriptionCancelled', 'details' => 'ألغِ اشتراكاً (فوراً أو بنهاية الفترة). يُرسل بريد SubscriptionCancelled باسم الخطة وتاريخ الانتهاء.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.payment_failed', 'description' => 'بريد فشل الدفع ← PaymentFailed', 'details' => 'استخدم بطاقة مرفوضة أو ألغِ الدفع عبر البوابة. يُرسل بريد PaymentFailed للمستخدم.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.payment_canceled', 'description' => 'بريد إلغاء الدفع ← PaymentCanceled', 'details' => 'ألغِ الدفع في صفحة الدفع. يُرسل بريد PaymentCanceled مع المبلغ والتاريخ.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.subscription_upgraded', 'description' => 'بريد ترقية الباقة ← SubscriptionUpgraded', 'details' => 'غيّر الباقة إلى أعلى (مع دفع أو بدون). يُرسل بريد SubscriptionUpgraded باسمي الباقتين.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.subscription_downgraded', 'description' => 'بريد تخفيض الباقة ← SubscriptionDowngraded', 'details' => 'غيّر الباقة إلى أقل. يُرسل بريد SubscriptionDowngraded باسمي الباقتين وتاريخ التفعيل.'],
            ['category' => '17. البريد الإلكتروني', 'item_key' => 'email.no_duplicate_receipt', 'description' => 'عدم تكرار إيصال الدفع (إيصال واحد فقط لكل دفع ناجح)', 'details' => 'أكمل دفعاً عبر Chargily. تحقق من queue: لا تظهر وظيفتا PaymentReceipt. يُرسل إيصال واحد فقط.'],

            // 18. Backup (4 items)
            ['category' => '18. النسخ الاحتياطي', 'item_key' => 'backup.run', 'description' => 'php artisan backup:run ← ملف بدون أخطاء', 'details' => 'شغّل الأمر. يُنشأ ملف .zip في storage/app/backup.'],
            ['category' => '18. النسخ الاحتياطي', 'item_key' => 'backup.contains', 'description' => 'النسخة تحتوي على DB + الملفات', 'details' => 'افتح .zip. يحتوي على SQL dump والملفات المرفوعة. الحجم ليس 0.'],
            ['category' => '18. النسخ الاحتياطي', 'item_key' => 'backup.cloud', 'description' => 'رفع النسخة إلى التخزين السحابي', 'details' => 'تأكد من إعدادات filesystems.php. تحقق من رفع الملف إلى S3/Spaces. اختبر التحميل والاستعادة.'],
            ['category' => '18. النسخ الاحتياطي', 'item_key' => 'backup.restore_ui', 'description' => 'استعادة النسخة الاحتياطية من واجهة Super Admin', 'details' => 'اذهب إلى /super-admin/backups. اضغط زر الاستعادة (رمز السهم) بجانب أي نسخة. تأكد من ظهور تأكيد. بعد التأكيد، تظهر رسالة نجاح أو فشل. اختبر الاستعادة بقاعدة اختبار (وليس الإنتاج).'],

            // 19. Translations (5 items)
            ['category' => '19. الترجمات', 'item_key' => 'translations.onboarding', 'description' => 'ملف onboarding.php مكتمل في ar/fr', 'details' => 'تحقق من وجود: remaining, drag_drop_hint, browse_files في fr؛ timeline_pending في ar.'],
            ['category' => '19. الترجمات', 'item_key' => 'translations.settings', 'description' => 'ملف settings.php مكتمل في ar', 'details' => 'تحقق من وجود: tax, gateway_fee, proration_credit, tax_disclosed_label, view_all_invoices.'],
            ['category' => '19. الترجمات', 'item_key' => 'translations.super_admin', 'description' => 'ملف super-admin.php مكتمل في ar/fr', 'details' => 'تحقق من وجود: proration_credit, gateway_saved, gateway_status_updated, select_gateway_desc, gateway_not_recognized, search_gateway في ar؛ proration_credit في fr.'],
            ['category' => '19. الترجمات', 'item_key' => 'translations.messages', 'description' => 'ملف messages.php مكتمل في ar', 'details' => 'تحقق من وجود: income_category_restored, expense_category_restored, auth.login, auth.logout, auth.registered, auth.password_reset.'],
            ['category' => '19. الترجمات', 'item_key' => 'translations.payment_note', 'description' => 'ملاحظة العملة في صفحات الدفع مترجمة', 'details' => 'تحقق من ظهور النص "طرق الدفع المعروضة للعملة" في /onboarding/plan و /onboarding/payment باللغات الثلاث.'],

            // 19. Performance & Compatibility (6 items)
            ['category' => '19. الأداء والتوافق', 'item_key' => 'performance.load_time', 'description' => 'تحميل الصفحات < 2 ثانية (متوسط)', 'details' => 'استخدم Debugbar أو Network tab. الصفحات الرئيسية < 2 ثانية متوسط.'],
            ['category' => '19. الأداء والتوافق', 'item_key' => 'performance.pagination', 'description' => '1000+ سجل ← Pagination يعمل', 'details' => 'أنشئ 1000+ سجل. اختبر pagination. وقت تحميل < 3 ثوانٍ.'],
            ['category' => '19. الأداء والتوافق', 'item_key' => 'performance.browsers', 'description' => 'توافق مع Chrome/Firefox/Edge/Safari', 'details' => 'اختبر الوظائف الرئيسية في المتصفحات الأربعة.'],
            ['category' => '19. الأداء والتوافق', 'item_key' => 'performance.rwd', 'description' => 'توافق مع الجوال (RWD)', 'details' => 'استخدم DevTools Responsive. اختبر 360px, 414px, 768px, 1024px.'],
            ['category' => '19. الأداء والتوافق', 'item_key' => 'performance.gzip', 'description' => 'Gzip compression مفعل', 'details' => 'تحقق من Content-Encoding: gzip في الردود.'],
            ['category' => '19. الأداء والتوافق', 'item_key' => 'performance.n_plus_one', 'description' => 'لا توجد استعلامات N+1', 'details' => 'فعّل Debugbar. تصفح الصفحات. افحص Queries — لا duplicate queries.'],

            // 20. Payment & Subscriptions (6 items)
            ['category' => '21. الدفع والاشتراكات', 'item_key' => 'subscription.meta_gateway_type', 'description' => 'جدول Meta يعرض اسم البوابة + نوع الطريقة', 'details' => 'اذهب إلى /account/subscriptions. الخانة الأولى تظهر اسم البوابة (مثل Chargily) ونوعها (مثل Online) بدلاً من الاختصار فقط.'],
            ['category' => '21. الدفع والاشتراكات', 'item_key' => 'subscription.no_manual_change_method', 'description' => 'لا يوجد تغيير يدوي لطريقة الدفع في الاشتراك النشط', 'details' => 'تحت الاشتراك النشط، لم يعد هناك حقل اختيار طريقة دفع مع زر تحديث.'],
            ['category' => '21. الدفع والاشتراكات', 'item_key' => 'payment.features_collapse', 'description' => 'صفحة الدفع تعرض الميزات مع توسيع/طي (أول 5)', 'details' => 'اذهب إلى /onboarding/payment. تظهر أول 5 ميزات للباقة المختارة. زر "عرض المزيد" يوسّع القائمة. زر "عرض أقل" يطويها.'],
            ['category' => '21. الدفع والاشتراكات', 'item_key' => 'payment.features_translated', 'description' => 'مفاتيح what_included/show_more/show_less مترجمة', 'details' => 'تحقق من ملفات الترجمة en/ar/fr. الكلمات تظهر باللغة الصحيحة حسب لغة الواجهة.'],
            ['category' => '21. الدفع والاشتراكات', 'item_key' => 'plan.features_collapse', 'description' => 'صفحة الخطط تعرض أول 5 ميزات مع توسيع/طي لكل بطاقة', 'details' => 'اذهب إلى /onboarding/plan. كل بطاقة خطة تعرض أول 5 ميزات فقط. زر "عرض المزيد" مستقل لكل بطاقة.'],

            // 20. Landing Page (7 items)
            ['category' => '20. الصفحة التعريفية', 'item_key' => 'landing.plans_dynamic', 'description' => 'عرض الباقات ديناميكياً من DB مع أسعار محوّلة للعملة', 'details' => 'تظهر الباقات من جدول subscription_plans (is_active=1, is_public=1). الأسعار محوّلة للعملة المختارة.'],
            ['category' => '20. الصفحة التعريفية', 'item_key' => 'landing.features_localized', 'description' => 'أسماء المميزات مترجمة حسب اللغة', 'details' => 'تظهر أسماء المميزات من الحقل المناسب (name_ar/name_en/name_fr) حسب لغة الواجهة.'],
            ['category' => '20. الصفحة التعريفية', 'item_key' => 'landing.collapsible_features', 'description' => 'عرض المميزات بشكل قابل للطي (أول 3 + زر عرض المزيد)', 'details' => 'أول 3 مميزات فقط تظهر. زر "عرض المزيد" يوسّع القائمة. زر "عرض أقل" يطويها. تعمل بشكل مستقل لكل باقة.'],
            ['category' => '20. الصفحة التعريفية', 'item_key' => 'landing.theme_toggle_icon', 'description' => 'زر تبديل الثيم يحدّث الأيقونة فوراً', 'details' => 'اضغط زر الثيم في landing page. يجب أن تتغير الأيقونة فوراً (bi-sun-fill ↔ bi-moon-fill) ويتغير data-theme على <html>.'],
            ['category' => '20. الصفحة التعريفية', 'item_key' => 'landing.feature_icons', 'description' => 'أيقونات المميزات تظهر بألوان خلفية متناسقة', 'details' => 'تظهر أيقونات الـ 12 مميزاً مع ألوان (success, danger, warning, info, purple, accent) وأيقونات Bootstrap المناسبة.'],
            ['category' => '20. الصفحة التعريفية', 'item_key' => 'landing.faq_content', 'description' => 'قسم الأسئلة الشائعة يحتوي على محتوى فعلي عن المشروع', 'details' => 'الأسئلة والأجوبة محددة لهذا المشروع وتشمل: الأمان، العملات المتعددة، الفوترة، الزكاة، فرق العمل، تصدير البيانات.'],
            ['category' => '20. الصفحة التعريفية', 'item_key' => 'landing.features_modules', 'description' => 'قسم المميزات يسرد الوحدات الفعلية للمشروع', 'details' => '12 بطاقة مميزات: الدخل، المصروفات، الديون، الأصول، الميزانيات، الزكاة، المعاملات، الأهداف، الإشعارات، التقارير، الفواتير، فرق العمل.'],
        ];
    }
}
