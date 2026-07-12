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
        // =====================================================================
        // 🟦  لوحة الإدارة (Super Admin)
        // =====================================================================
        return array_merge(
            $this->adminItems(),
            // =================================================================
            // 🟩  لوحة المستخدم (User Panel)
            // =================================================================
            $this->userItems(),
        );
    }

    // =========================================================================
    // 🟦  ADMIN TAB
    // =========================================================================
    private function adminItems(): array
    {
        return [
            // ----------------------------------------------------------------
            // SA.1  البيئة والإعدادات  (8 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 1. البيئة والإعدادات', 'item_key' => 'environment.app_env', 'description' => 'APP_ENV=production -- APP_DEBUG=false', 'details' => 'تأكد من APP_ENV=production و APP_DEBUG=false في .env. شغّل php artisan config:cache. تحقق عبر route /api/health أو tinker.'],
            ['category' => 'لوحة الإدارة | 1. البيئة والإعدادات', 'item_key' => 'environment.session_config', 'description' => 'SESSION_DRIVER=database|redis + SESSION_SECURE_COOKIE=true', 'details' => 'تأكد من SESSION_DRIVER=database|redis و SESSION_SECURE_COOKIE=true في .env. تحقق من جدول sessions إن كنت تستخدم database driver.'],
            ['category' => 'لوحة الإدارة | 1. البيئة والإعدادات', 'item_key' => 'environment.queue_connection', 'description' => 'QUEUE_CONNECTION=database', 'details' => 'تأكد من QUEUE_CONNECTION=database في .env. افحص config/queue.php. شغّل php artisan queue:work --once لاختبار المعالجة.'],
            ['category' => 'لوحة الإدارة | 1. البيئة والإعدادات', 'item_key' => 'environment.ssl_https', 'description' => 'SSL/HTTPS مفعل', 'details' => 'افتح الموقع عبر https://. تحقق من القفل الأخضر. استخدم ssllabs.com لفحص الشهادة.'],
            ['category' => 'لوحة الإدارة | 1. البيئة والإعدادات', 'item_key' => 'environment.optimize', 'description' => 'php artisan optimize ← دون أخطاء', 'details' => 'شغّل php artisan optimize. يجب ألا يظهر خطأ. تأكد من أن cache مجلداتها قابلة للكتابة.'],
            ['category' => 'لوحة الإدارة | 1. البيئة والإعدادات', 'item_key' => 'environment.storage_writable', 'description' => 'مجلدات storage/ و bootstrap/cache/ قابلة للكتابة', 'details' => 'شغّل php artisan storage:link إن لم يكن بعد. تأكد من صلاحيات الكتابة.'],
            ['category' => 'لوحة الإدارة | 1. البيئة والإعدادات', 'item_key' => 'environment.cron_schedule', 'description' => 'CRON الجدول الزمني مفعل: * * * * * php artisan schedule:run', 'details' => 'أضف السطر إلى crontab. تأكد بتشغيل crontab -e كمستخدم www-data.'],
            ['category' => 'لوحة الإدارة | 1. البيئة والإعدادات', 'item_key' => 'environment.api_keys', 'description' => 'مفاتيح API حقيقية في .env (Chargily, Mail, Sentry...)', 'details' => 'تأكد من وجود مفاتيح حقيقية لـ: CHARGILY_API_KEY, MAIL_MAILER, MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD, SENTRY_LARAVEL_DSN.'],

            // ----------------------------------------------------------------
            // SA.2  إدارة المستخدمين  (8 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 2. إدارة المستخدمين', 'item_key' => 'admin.users.list', 'description' => 'عرض قائمة المستخدمين مع البحث والتصفية', 'details' => 'اذهب إلى super-admin → المستخدمين. تظهر القائمة مع: الاسم، البريد، الحالة، تاريخ التسجيل. استخدم البحث والتصفية.'],
            ['category' => 'لوحة الإدارة | 2. إدارة المستخدمين', 'item_key' => 'admin.users.create', 'description' => 'إنشاء مستخدم جديد مباشرة من لوحة الإدارة', 'details' => 'اضغط "إضافة مستخدم". أدخل البيانات (الاسم، البريد، كلمة المرور، الدور). حفظ ← يُنشأ المستخدم دون الحاجة لتأكيد البريد.'],
            ['category' => 'لوحة الإدارة | 2. إدارة المستخدمين', 'item_key' => 'admin.users.edit', 'description' => 'تعديل بيانات المستخدم (الاسم، البريد، الدور)', 'details' => 'اختر مستخدم من القائمة. عدّل الاسم، البريد، الدور. حفظ ← تتحدّث البيانات.'],
            ['category' => 'لوحة الإدارة | 2. إدارة المستخدمين', 'item_key' => 'admin.users.suspend', 'description' => 'تعليق/تفعيل حساب المستخدم', 'details' => 'اختر مستخدم نشط ← علّق. لا يستطيع تسجيل الدخول. فعّل مرة أخرى ← يعود طبيعياً.'],
            ['category' => 'لوحة الإدارة | 2. إدارة المستخدمين', 'item_key' => 'admin.users.delete', 'description' => 'حذف مستخدم (ناعم + نهائي)', 'details' => 'احذف مستخدم ← soft delete. تحقق من سلة المهملات. حذف نهائي ← يُزال بالكامل.'],
            ['category' => 'لوحة الإدارة | 2. إدارة المستخدمين', 'item_key' => 'admin.users.filter_search', 'description' => 'تصفية المستخدمين (الحالة، الدور) + بحث بالاسم/البريد', 'details' => 'فلتر حسب الحالة (نشط/موقوف). بحث بالبريد الإلكتروني. يمكن الجمع.'],
            ['category' => 'لوحة الإدارة | 2. إدارة المستخدمين', 'item_key' => 'admin.users.view_detail', 'description' => 'عرض تفاصيل المستخدم (الاشتراكات، مساحات العمل، النشاط)', 'details' => 'اختر مستخدم. تظهر تفاصيل: البريد، تاريخ التسجيل، الأدوار، مساحات العمل، الاشتراكات، آخر نشاط.'],
            ['category' => 'لوحة الإدارة | 2. إدارة المستخدمين', 'item_key' => 'admin.users.impersonate', 'description' => 'تسجيل الدخول كمستخدم (Impersonate)', 'details' => 'اختر مستخدم ← "تسجيل الدخول كـ". يُنقل إلى واجهة المستخدم. شريط علوي يظهر "أنت تستخدم حساب ...". خروج ← العودة للإدارة.'],

            // ----------------------------------------------------------------
            // SA.3  إدارة مساحات العمل  (7 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 3. إدارة مساحات العمل', 'item_key' => 'admin.workspaces.list', 'description' => 'عرض قائمة مساحات العمل مع البحث', 'details' => 'اذهب إلى super-admin → مساحات العمل. تظهر القائمة مع: الاسم، المالك، عدد الأعضاء، الحالة، الاشتراك.'],
            ['category' => 'لوحة الإدارة | 3. إدارة مساحات العمل', 'item_key' => 'admin.workspaces.view', 'description' => 'عرض تفاصيل مساحة العمل (الأعضاء، الاشتراك، المعاملات)', 'details' => 'اختر مساحة عمل. تظهر: الأعضاء، الاشتراك الحالي، عدد المعاملات، تاريخ الإنشاء.'],
            ['category' => 'لوحة الإدارة | 3. إدارة مساحات العمل', 'item_key' => 'admin.workspaces.suspend', 'description' => 'تعليق مساحة عمل ← منع الوصول', 'details' => 'علّق مساحة عمل. يحاول أعضاؤها الدخول — يُمنعون مع رسالة "مساحة العمل معلقة".'],
            ['category' => 'لوحة الإدارة | 3. إدارة مساحات العمل', 'item_key' => 'admin.workspaces.activate', 'description' => 'إعادة تفعيل مساحة عمل موقوفة', 'details' => 'فعّل المساحة المعلقة. يعود الأعضاء للوصول الطبيعي.'],
            ['category' => 'لوحة الإدارة | 3. إدارة مساحات العمل', 'item_key' => 'admin.workspaces.filter_search', 'description' => 'تصفية مساحات العمل (الحالة، الخطة) + بحث', 'details' => 'فلتر حسب الحالة (نشط/موقوف) أو الخطة. بحث بالاسم.'],
            ['category' => 'لوحة الإدارة | 3. إدارة مساحات العمل', 'item_key' => 'admin.workspaces.statistics', 'description' => 'عرض إحصائيات مساحة العمل (عدد المعاملات، الإيرادات، المصروفات)', 'details' => 'في صفحة التفاصيل، تظهر إحصائيات: عدد المعاملات هذا الشهر، إجمالي الإيرادات/المصروفات.'],
            ['category' => 'لوحة الإدارة | 3. إدارة مساحات العمل', 'item_key' => 'admin.workspaces.delete', 'description' => 'حذف مساحة عمل + جميع بياناتها', 'details' => 'احذف مساحة عمل. تُحذف جميع المعاملات المرتبطة (الأصول، الإيرادات، المصروفات...). soft delete مع إمكانية الاستعادة.'],

            // ----------------------------------------------------------------
            // SA.4  إدارة الباقات والخطط  (10 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 4. إدارة الباقات والخطط', 'item_key' => 'admin.plans.list', 'description' => 'عرض قائمة الباقات مع المميزات والأسعار', 'details' => 'اذهب إلى super-admin → الباقات. تظهر الباقات مع ترتيب العرض، الحالة (نشط/غير نشط)، الأسعار لكل عملة.'],
            ['category' => 'لوحة الإدارة | 4. إدارة الباقات والخطط', 'item_key' => 'admin.plans.create', 'description' => 'إنشاء باقة جديدة (الاسم، الوصف، السعر، المميزات)', 'details' => 'اضغط "إضافة باقة". أدخل الاسم، الوصف، slug. أضف الأسعار (شهري/سنوي لكل عملة). اختر المميزات وقيمها. حفظ.'],
            ['category' => 'لوحة الإدارة | 4. إدارة الباقات والخطط', 'item_key' => 'admin.plans.edit', 'description' => 'تعديل الباقة (الاسم، السعر، المميزات، الترتيب)', 'details' => 'عدّل اسم الباقة، الوصف، الترتيب. عدّل الأسعار في plan_prices. أضف/أزل مميزات في plan_plan_feature.'],
            ['category' => 'لوحة الإدارة | 4. إدارة الباقات والخطط', 'item_key' => 'admin.plans.toggle_active', 'description' => 'تفعيل/إلغاء تفعيل باقة', 'details' => 'ألغِ تفعيل باقة ← لا تظهر للمستخدمين في onboarding. فعّلها ← تظهر.'],
            ['category' => 'لوحة الإدارة | 4. إدارة الباقات والخطط', 'item_key' => 'admin.plans.toggle_public', 'description' => 'إخفاء/إظهار الباقة للجمهور', 'details' => 'اجعل الباقة غير عامة ← لا تظهر في landing page لكن تظهر في لوحة الإدارة.'],
            ['category' => 'لوحة الإدارة | 4. إدارة الباقات والخطط', 'item_key' => 'admin.plans.manage_features', 'description' => 'إدارة مميزات الباقة عبر الجدول الوسيط (قيمة، ترتيب)', 'details' => 'عدّل قيمة ميزة (مثل users=10). غيّر ترتيب العرض. أضف ميزة جديدة باقة. احذف ميزة.'],
            ['category' => 'لوحة الإدارة | 4. إدارة الباقات والخطط', 'item_key' => 'admin.plans.manage_prices', 'description' => 'إدارة أسعار الباقة لكل عملة وفترة', 'details' => 'أضف سعراً لـ EUR (شهري). عدّل سعر DZD (سنوي). احذف سعراً.'],
            ['category' => 'لوحة الإدارة | 4. إدارة الباقات والخطط', 'item_key' => 'admin.plans.sort_order', 'description' => 'تغيير ترتيب عرض الباقات', 'details' => 'غيّر sort_order لباقة. يظهر الترتيب الجديد في onboarding والـ landing page.'],
            ['category' => 'لوحة الإدارة | 4. إدارة الباقات والخطط', 'item_key' => 'admin.plans.feature_definitions', 'description' => 'إدارة تعريفات المميزات (PlanFeature CRUD)', 'details' => 'اذهب إلى المميزات. أضف ميزة جديدة (slug, name). عدّل ميزة. احذف ميزة (تأثيرها على الباقات المرتبطة).'],
            ['category' => 'لوحة الإدارة | 4. إدارة الباقات والخطط', 'item_key' => 'admin.plans.delete', 'description' => 'حذف باقة', 'details' => 'احذف باقة. الاشتراكات المرتبطة تبقى لكن بعلاقة مقطوعة. يجب تأكيد الحذف.'],

            // ----------------------------------------------------------------
            // SA.5  إدارة الاشتراكات  (7 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 5. إدارة الاشتراكات', 'item_key' => 'admin.subscriptions.list', 'description' => 'عرض جميع الاشتراكات مع التصفية (الحالة، الخطة، الفترة)', 'details' => 'اذهب إلى super-admin → الاشتراكات. تظهر كل الاشتراكات. فلتر حسب: نشط، منتهي، ملغي، past_due. فلتر حسب الباقة. بحث باسم مساحة العمل.'],
            ['category' => 'لوحة الإدارة | 5. إدارة الاشتراكات', 'item_key' => 'admin.subscriptions.view', 'description' => 'عرض تفاصيل الاشتراك (الفاتورة، المدفوعات، التواريخ)', 'details' => 'اختر اشتراكاً. تظهر: الباقة، المدة، تاريخ البدء/الانتهاء، المدفوعات المرتبطة، الفواتير، حالة التجديد التلقائي.'],
            ['category' => 'لوحة الإدارة | 5. إدارة الاشتراكات', 'item_key' => 'admin.subscriptions.manual_activate', 'description' => 'تفعيل اشتراك يدوياً من لوحة الإدارة', 'details' => 'اختر اشتراك past_due. اضغط "تفعيل". يصبح Active دون دفع. تُنشأ فاتورة.'],
            ['category' => 'لوحة الإدارة | 5. إدارة الاشتراكات', 'item_key' => 'admin.subscriptions.manual_cancel', 'description' => 'إلغاء اشتراك يدوياً', 'details' => 'اختر اشتراك Active. اضغط "إلغاء". يصبح Canceled مع canceled_at.'],
            ['category' => 'لوحة الإدارة | 5. إدارة الاشتراكات', 'item_key' => 'admin.subscriptions.change_plan', 'description' => 'تغيير باقة الاشتراك من الإدارة', 'details' => 'اختر اشتراك. غيّر الباقة. يُحسب proration إن لزم. تُنشأ فاتورة.'],
            ['category' => 'لوحة الإدارة | 5. إدارة الاشتراكات', 'item_key' => 'admin.subscriptions.stats', 'description' => 'عرض إحصائيات الاشتراكات (النشط، المنتهي، الإيرادات)', 'details' => 'في صفحة الاشتراكات: عدد النشط، المنتهي، الملغي. إجمالي الإيرادات الشهرية/السنوية.'],
            ['category' => 'لوحة الإدارة | 5. إدارة الاشتراكات', 'item_key' => 'admin.subscriptions.export', 'description' => 'تصدير الاشتراكات إلى Excel/CSV', 'details' => 'زر التصدير. الملف يحتوي على: مساحة العمل، الباقة، الحالة، تاريخ البدء، تاريخ الانتهاء، المبلغ.'],

            // ----------------------------------------------------------------
            // SA.6  إدارة المدفوعات  (8 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 6. إدارة المدفوعات', 'item_key' => 'admin.payments.list', 'description' => 'عرض جميع المدفوعات مع التصفية (الحالة، الطريقة، التاريخ)', 'details' => 'اذهب إلى super-admin → المدفوعات. فلتر حسب: الحالة (pending, paid, failed, canceled)، طريقة الدفع، نطاق تاريخي. بحث بالمرجع/المعرف.'],
            ['category' => 'لوحة الإدارة | 6. إدارة المدفوعات', 'item_key' => 'admin.payments.view', 'description' => 'عرض تفاصيل الدفعة (المبلغ، الرسوم، الاشتراك، المستخدم)', 'details' => 'اختر دفعة. تظهر: المبلغ، العملة، طريقة الدفع، الحالة، الرسوم، الضريبة، الاشتراك المرتبط، المستخدم، البوابة.'],
            ['category' => 'لوحة الإدارة | 6. إدارة المدفوعات', 'item_key' => 'admin.payments.approve_manual', 'description' => 'اعتماد دفع يدوي (قبول إثبات الدفع)', 'details' => 'اختر دفعة يدوية (BaridiMob/RedotPay) بحالة pending. افحص الإثبات. اضغط "قبول". تصبح paid. يُفعّل الاشتراك. تُرسل الفاتورة.'],
            ['category' => 'لوحة الإدارة | 6. إدارة المدفوعات', 'item_key' => 'admin.payments.reject_manual', 'description' => 'رفض دفع يدوي مع سبب', 'details' => 'اختر دفعة يدوية pending. اضغط "رفض". أدخل سبب الرفض. تصبح failed. يُشعر المستخدم بالبريد.'],
            ['category' => 'لوحة الإدارة | 6. إدارة المدفوعات', 'item_key' => 'admin.payments.refund', 'description' => 'رد مبلغ (Refund) لدفعة مدفوعة', 'details' => 'اختر دفعة paid. اضغط "رد المبلغ". يُحاول رد المبلغ عبر البوابة (إن كانت online). تسجل كـ refunded.'],
            ['category' => 'لوحة الإدارة | 6. إدارة المدفوعات', 'item_key' => 'admin.payments.invoice_view', 'description' => 'عرض فاتورة الدفعة وتحميل PDF', 'details' => 'اختر دفعة paid. اضغط "عرض الفاتورة". تظهر الفاتورة بالمبلغ، الضريبة، الرسوم. حمّلها PDF.'],
            ['category' => 'لوحة الإدارة | 6. إدارة المدفوعات', 'item_key' => 'admin.payments.stats', 'description' => 'عرض إحصائيات المدفوعات (الإيرادات، عدد المعاملات)', 'details' => 'في صفحة المدفوعات: إجمالي الإيرادات (شهر/سنة/كل)، عدد المدفوعات الناجحة/الفاشلة/المعلقة.'],
            ['category' => 'لوحة الإدارة | 6. إدارة المدفوعات', 'item_key' => 'admin.payments.export', 'description' => 'تصدير المدفوعات إلى Excel/CSV', 'details' => 'زر التصدير. الملف يحتوي على: المعرف، المرجع، المبلغ، العملة، الطريقة، الحالة، التاريخ.'],

            // ----------------------------------------------------------------
            // SA.7  إدارة بوابات الدفع  (6 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 7. إدارة بوابات الدفع', 'item_key' => 'admin.gateways.list', 'description' => 'عرض قائمة بوابات الدفع (النشطة/غير النشطة)', 'details' => 'اذهب إلى super-admin → بوابات الدفع. تظهر البوابات مع: الاسم، المفتاح، الحالة، العملات المدعومة.'],
            ['category' => 'لوحة الإدارة | 7. إدارة بوابات الدفع', 'item_key' => 'admin.gateways.toggle', 'description' => 'تفعيل/إلغاء تفعيل بوابة دفع', 'details' => 'عطّل بوابة Chargily ← تختفي من خيارات الدفع للمستخدم. فعّلها ← تظهر.'],
            ['category' => 'لوحة الإدارة | 7. إدارة بوابات الدفع', 'item_key' => 'admin.gateways.edit_credentials', 'description' => 'تعديل بيانات اعتماد البوابة (API keys, Mode)', 'details' => 'عدّل CHARGILY_MODE من test إلى live. غيّر المفاتيح. حفظ ← تُستخدم في الدفعات الجديدة.'],
            ['category' => 'لوحة الإدارة | 7. إدارة بوابات الدفع', 'item_key' => 'admin.gateways.manage_currencies', 'description' => 'إدارة العملات المدعومة للبوابة', 'details' => 'أضف/احذف العملات المدعومة لبوابة معينة. تظهر العملات المتاحة للمستخدم حسب البوابة المختارة.'],
            ['category' => 'لوحة الإدارة | 7. إدارة بوابات الدفع', 'item_key' => 'admin.gateways.fee_tax_links', 'description' => 'إدارة رسوم البوابة والضرائب المرتبطة', 'details' => 'اربط ضريبة ببوابة. تظهر في تحليل السعر: gateway_fee و tax_added و tax_disclosed.'],
            ['category' => 'لوحة الإدارة | 7. إدارة بوابات الدفع', 'item_key' => 'admin.gateways.test_connection', 'description' => 'اختبار اتصال البوابة', 'details' => 'اضغط "اختبار الاتصال" لبوابة. تظهر رسالة نجاح/فشل.'],

            // ----------------------------------------------------------------
            // SA.8  إدارة الكوبونات  (7 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 8. إدارة الكوبونات', 'item_key' => 'admin.coupons.list', 'description' => 'عرض قائمة الكوبونات مع البحث', 'details' => 'اذهب إلى super-admin → الكوبونات. تظهر القائمة مع: الكود، النوع، القيمة، الاستخدام، الصلاحية.'],
            ['category' => 'لوحة الإدارة | 8. إدارة الكوبونات', 'item_key' => 'admin.coupons.create', 'description' => 'إنشاء كوبون خصم (نسبة/قيمة ثابتة)', 'details' => 'أضف كوبون: الكود، النوع (percentage/fixed)، القيمة، الحد الأدنى للمبلغ، الحد الأقصى للاستخدام، تاريخ الصلاحية. حفظ.'],
            ['category' => 'لوحة الإدارة | 8. إدارة الكوبونات', 'item_key' => 'admin.coupons.edit', 'description' => 'تعديل كوبون', 'details' => 'عدّل قيمة الخصم، تاريخ الصلاحية، الحد الأقصى. حفظ ← التغييرات تسري على الاستخدامات الجديدة.'],
            ['category' => 'لوحة الإدارة | 8. إدارة الكوبونات', 'item_key' => 'admin.coupons.toggle', 'description' => 'تفعيل/إلغاء تفعيل كوبون', 'details' => 'عطّل كوبون ← لا يمكن استخدامه في الدفع. فعّله ← يعود للعمل.'],
            ['category' => 'لوحة الإدارة | 8. إدارة الكوبونات', 'item_key' => 'admin.coupons.restrict_gateway', 'description' => 'تقييد كوبون ببوابة دفع محددة', 'details' => 'اربط الكوبون بـ Chargily فقط. اختبره مع BaridiMob ← يُرفض.'],
            ['category' => 'لوحة الإدارة | 8. إدارة الكوبونات', 'item_key' => 'admin.coupons.restrict_plan', 'description' => 'تقييد كوبون بباقة محددة', 'details' => 'اربط الكوبون بباقة Professional. اختبره مع Business ← يُرفض.'],
            ['category' => 'لوحة الإدارة | 8. إدارة الكوبونات', 'item_key' => 'admin.coupons.usage_stats', 'description' => 'عرض إحصائيات استخدام الكوبون', 'details' => 'اختر كوبون. يظهر: عدد مرات الاستخدام، إجمالي الخصم الممنوح، آخر استخدام.'],

            // ----------------------------------------------------------------
            // SA.9  إدارة الضرائب والرسوم  (5 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 9. إدارة الضرائب والرسوم', 'item_key' => 'admin.taxes.list', 'description' => 'عرض قائمة الضرائب والرسوم', 'details' => 'اذهب إلى super-admin → الضرائب. تظهر: الاسم، النسبة، النوع (gateway_fee/tax_added/tax_disclosed).'],
            ['category' => 'لوحة الإدارة | 9. إدارة الضرائب والرسوم', 'item_key' => 'admin.taxes.create', 'description' => 'إنشاء ضريبة/رسوم جديدة', 'details' => 'أضف: الاسم، النسبة المئوية، النوع (رسوم بوابة/ضريبة مضافة/ضريبة مفصح عنها). حفظ.'],
            ['category' => 'لوحة الإدارة | 9. إدارة الضرائب والرسوم', 'item_key' => 'admin.taxes.link_gateway', 'description' => 'ربط ضريبة ببوابة دفع', 'details' => 'اربط ضريبة 2.5% بـ Chargily كـ gateway_fee. تظهر في تحليل السعر عند اختيار Chargily.'],
            ['category' => 'لوحة الإدارة | 9. إدارة الضرائب والرسوم', 'item_key' => 'admin.taxes.edit_delete', 'description' => 'تعديل وحذف ضريبة', 'details' => 'عدّل النسبة. احذف الضريبة — تُزال من البوابات المرتبطة.'],
            ['category' => 'لوحة الإدارة | 9. إدارة الضرائب والرسوم', 'item_key' => 'admin.taxes.calculation_test', 'description' => 'اختبار حساب الضريبة بدقة', 'details' => 'أنشئ ضريبة 19%. اختر خطة بسعر 1000 DZD. تحقق من تحليل السعر: الضريبة = 190 DZD.'],

            // ----------------------------------------------------------------
            // SA.10  إدارة الأدوار والصلاحيات (منصة)  (6 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 10. الأدوار والصلاحيات', 'item_key' => 'admin.roles.list', 'description' => 'عرض قائمة الأدوار (منصة)', 'details' => 'اذهب إلى super-admin → الأدوار. تظهر الأدوار: super_admin, admin, user مع صلاحيات كل دور.'],
            ['category' => 'لوحة الإدارة | 10. الأدوار والصلاحيات', 'item_key' => 'admin.roles.create', 'description' => 'إنشاء دور جديد مع صلاحيات محددة', 'details' => 'أضف دوراً جديداً. اختر الصلاحيات من القائمة (users.manage, workspaces.view, payments.refund...). حفظ.'],
            ['category' => 'لوحة الإدارة | 10. الأدوار والصلاحيات', 'item_key' => 'admin.roles.edit', 'description' => 'تعديل صلاحيات دور', 'details' => 'عدّل صلاحيات دور admin — أضف/أزل صلاحية. المستخدمون بهذا الدور يتأثرون فوراً.'],
            ['category' => 'لوحة الإدارة | 10. الأدوار والصلاحيات', 'item_key' => 'admin.roles.assign_user', 'description' => 'تخصيص دور لمستخدم', 'details' => 'اذهب للمستخدم → عدّل الدور. يكتسب صلاحيات الدور الجديد فوراً.'],
            ['category' => 'لوحة الإدارة | 10. الأدوار والصلاحيات', 'item_key' => 'admin.roles.permission_check', 'description' => 'التحقق من صلاحية المستخدم', 'details' => 'اجعل دوراً بدون صلاحية payments.approve. مستخدم بهذا الدور لا يرى زر القبول في المدفوعات.'],
            ['category' => 'لوحة الإدارة | 10. الأدوار والصلاحيات', 'item_key' => 'admin.roles.delete', 'description' => 'حذف دور', 'details' => 'احذف دوراً. المستخدمون بهذا الدور يُنقلون إلى الدور الافتراضي.'],

            // ----------------------------------------------------------------
            // SA.11  إدارة الإعدادات العامة  (6 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 11. الإعدادات العامة', 'item_key' => 'admin.settings.general', 'description' => 'تعديل الإعدادات العامة (اسم التطبيق، اللغة، التسجيل)', 'details' => 'اذهب إلى super-admin → الإعدادات. غيّر اسم التطبيق. عدّل اللغة الافتراضية. فعّل/عطّل التسجيل. حفظ ← التغييرات تظهر فوراً.'],
            ['category' => 'لوحة الإدارة | 11. الإعدادات العامة', 'item_key' => 'admin.settings.localization', 'description' => 'إعدادات الترجمة (اللغة الافتراضية، العملة، التنسيق)', 'details' => 'عدّل العملة الافتراضية إلى USD. غيّر تنسيق التاريخ. حفظ.'],
            ['category' => 'لوحة الإدارة | 11. الإعدادات العامة', 'item_key' => 'admin.settings.rate_limits', 'description' => 'تعديل حدود السرعة (Rate Limits)', 'details' => 'غيّر RATE_LIMIT_API من 120 إلى 60. أرسل طلبات سريعة — يُمنع بعد 60 طلباً.'],
            ['category' => 'لوحة الإدارة | 11. الإعدادات العامة', 'item_key' => 'admin.settings.maintenance', 'description' => 'وضع الصيانة (Down for Maintenance)', 'details' => 'فعّل وضع الصيانة. المستخدمون العاديون يرون صفحة الصيانة. المسؤول يرى الموقع طبيعياً.'],
            ['category' => 'لوحة الإدارة | 11. الإعدادات العامة', 'item_key' => 'admin.settings.email_config', 'description' => 'إعدادات البريد الإلكتروني', 'details' => 'عدّل MAIL_MAILER. اختبر إرسال بريد تجريبي. يصل البريد.'],
            ['category' => 'لوحة الإدارة | 11. الإعدادات العامة', 'item_key' => 'admin.settings.invoice_config', 'description' => 'إعدادات الفواتير (ترقيم، شروط)', 'details' => 'عدّل تنسيق رقم الفاتورة. عدّل شروط الدفع. حفظ ← الفواتير الجديدة تستخدم الإعدادات الجديدة.'],

            // ----------------------------------------------------------------
            // SA.12  الإعلانات  (5 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 12. الإعلانات', 'item_key' => 'admin.announcements.list', 'description' => 'عرض قائمة الإعلانات', 'details' => 'اذهب إلى super-admin → الإعلانات. تظهر القائمة مع: العنوان، الحالة (منشور/مسودة)، التاريخ.'],
            ['category' => 'لوحة الإدارة | 12. الإعلانات', 'item_key' => 'admin.announcements.create', 'description' => 'إنشاء إعلان جديد', 'details' => 'أضف إعلاناً: العنوان، المحتوى، الحالة (منشور/مسودة)، تاريخ النشر. حفظ.'],
            ['category' => 'لوحة الإدارة | 12. الإعلانات', 'item_key' => 'admin.announcements.publish', 'description' => 'نشر إعلان ← يظهر للمستخدمين', 'details' => 'غيّر حالة الإعلان إلى "منشور". يظهر الإعلان في لوحة المستخدم (بanner أو popup).'],
            ['category' => 'لوحة الإدارة | 12. الإعلانات', 'item_key' => 'admin.announcements.edit_delete', 'description' => 'تعديل وحذف إعلان', 'details' => 'عدّل محتوى الإعلان. احذفه — يختفي من واجهات المستخدم.'],
            ['category' => 'لوحة الإدارة | 12. الإعلانات', 'item_key' => 'admin.announcements.user_view', 'description' => 'المستخدم يرى الإعلانات المنشورة', 'details' => 'سجّل كمستخدم عادي. تظهر الإعلانات المنشورة في dashboard أو صفحة الإشعارات.'],

            // ----------------------------------------------------------------
            // SA.13  النسخ الاحتياطي  (4 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 13. النسخ الاحتياطي', 'item_key' => 'backup.run', 'description' => 'php artisan backup:run ← ملف بدون أخطاء', 'details' => 'شغّل الأمر. يُنشأ ملف .zip في storage/app/backup.'],
            ['category' => 'لوحة الإدارة | 13. النسخ الاحتياطي', 'item_key' => 'backup.contains', 'description' => 'النسخة تحتوي على DB + الملفات', 'details' => 'افتح .zip. يحتوي على SQL dump والملفات المرفوعة. الحجم ليس 0.'],
            ['category' => 'لوحة الإدارة | 13. النسخ الاحتياطي', 'item_key' => 'backup.cloud', 'description' => 'رفع النسخة إلى التخزين السحابي', 'details' => 'تأكد من إعدادات filesystems.php. تحقق من رفع الملف إلى S3/Spaces. اختبر التحميل والاستعادة.'],
            ['category' => 'لوحة الإدارة | 13. النسخ الاحتياطي', 'item_key' => 'backup.restore_ui', 'description' => 'استعادة النسخة الاحتياطية من واجهة Super Admin', 'details' => 'اذهب إلى /super-admin/backups. اضغط زر الاستعادة بجانب أي نسخة. تأكيد. رسالة نجاح/فشل. اختبر الاستعادة بقاعدة اختبار.'],

            // ----------------------------------------------------------------
            // SA.14  الصفحة التعريفية (Landing)  (7 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة الإدارة | 14. الصفحة التعريفية', 'item_key' => 'landing.plans_dynamic', 'description' => 'عرض الباقات ديناميكياً من DB مع أسعار محوّلة للعملة', 'details' => 'تظهر الباقات من جدول subscription_plans (is_active=1, is_public=1). الأسعار محوّلة للعملة المختارة.'],
            ['category' => 'لوحة الإدارة | 14. الصفحة التعريفية', 'item_key' => 'landing.features_localized', 'description' => 'أسماء المميزات مترجمة حسب اللغة', 'details' => 'تظهر أسماء المميزات من الحقل المناسب (name_ar/name_en/name_fr) حسب لغة الواجهة.'],
            ['category' => 'لوحة الإدارة | 14. الصفحة التعريفية', 'item_key' => 'landing.collapsible_features', 'description' => 'عرض المميزات بشكل قابل للطي (أول 3 + زر عرض المزيد)', 'details' => 'أول 3 مميزات فقط تظهر. زر "عرض المزيد" يوسّع القائمة. زر "عرض أقل" يطويها. تعمل بشكل مستقل لكل باقة.'],
            ['category' => 'لوحة الإدارة | 14. الصفحة التعريفية', 'item_key' => 'landing.theme_toggle_icon', 'description' => 'زر تبديل الثيم يحدّث الأيقونة فوراً', 'details' => 'اضغط زر الثيم في landing page. يجب أن تتغير الأيقونة فوراً (bi-sun-fill ↔ bi-moon-fill) ويتغير data-theme على <html>.'],
            ['category' => 'لوحة الإدارة | 14. الصفحة التعريفية', 'item_key' => 'landing.feature_icons', 'description' => 'أيقونات المميزات تظهر بألوان خلفية متناسقة', 'details' => 'تظهر أيقونات الـ 12 مميزاً مع ألوان (success, danger, warning, info, purple, accent) وأيقونات Bootstrap المناسبة.'],
            ['category' => 'لوحة الإدارة | 14. الصفحة التعريفية', 'item_key' => 'landing.faq_content', 'description' => 'قسم الأسئلة الشائعة يحتوي على محتوى فعلي عن المشروع', 'details' => 'الأسئلة والأجوبة محددة لهذا المشروع وتشمل: الأمان، العملات المتعددة، الفوترة، الزكاة، فرق العمل، تصدير البيانات.'],
            ['category' => 'لوحة الإدارة | 14. الصفحة التعريفية', 'item_key' => 'landing.features_modules', 'description' => 'قسم المميزات يسرد الوحدات الفعلية للمشروع', 'details' => '12 بطاقة مميزات: الدخل، المصروفات، الديون، الأصول، الميزانيات، الزكاة، المعاملات، الأهداف، الإشعارات، التقارير، الفواتير، فرق العمل.'],
        ];
    }

    // =========================================================================
    // 🟩  USER TAB
    // =========================================================================
    private function userItems(): array
    {
        return [
            // ----------------------------------------------------------------
            // US.1  التسجيل والمصادقة  (7 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 1. التسجيل والمصادقة', 'item_key' => 'auth.register', 'description' => 'تسجيل ← إشعار تأكيد ← verification.notice ← poll تلقائي ← تأكيد ← onboarding.plan + بريد ترحيبي', 'details' => 'سجّل في /register. يتم تسجيل الدخول تلقائياً. يُرسل إشعار التحقق فقط. يُوجّه إلى /verify-email الذي يستعلم كل 3 ثوانٍ عن حالة التحقق. بعد النقر على رابط التفعيل، يُكتشف تلقائياً، يُرسل WelcomeEmail، ويُوجّه إلى /onboarding/plan. لا تُنشأ workspace بعد.'],
            ['category' => 'لوحة المستخدم | 1. التسجيل والمصادقة', 'item_key' => 'auth.email_unverified', 'description' => 'الحساب غير مفعل ← cannot access dashboard', 'details' => 'تحقق من users.email_verified_at = null. محاولة /dashboard تعيد التوجيه إلى /verify-email. بعد التفعيل ← onboarding.plan.'],
            ['category' => 'لوحة المستخدم | 1. التسجيل والمصادقة', 'item_key' => 'auth.login_remember', 'description' => 'تسجيل الدخول + تذكرني', 'details' => 'سجّل الدخول باختيار "تذكرني". تحقق من cookie remember_web_*. أعد فتح المتصفح — يجب البقاء مسجلاً.'],
            ['category' => 'لوحة المستخدم | 1. التسجيل والمصادقة', 'item_key' => 'auth.password_reset', 'description' => 'إعادة تعيين كلمة المرور', 'details' => 'اذهب إلى /forgot-password. أدخل بريداً. استلم رابط إعادة التعيين. غيّر كلمة المرور. سجّل الدخول بالجديدة.'],
            ['category' => 'لوحة المستخدم | 1. التسجيل والمصادقة', 'item_key' => 'auth.two_factor_enable', 'description' => 'تفعيل 2FA (Google Authenticator)', 'details' => 'الإعدادات → الأمان → تفعيل 2FA. امسح QR code. أدخل الرمز. سجّل الخروج والدخول — يُطلب رمز 2FA.'],
            ['category' => 'لوحة المستخدم | 1. التسجيل والمصادقة', 'item_key' => 'auth.two_factor_wrong', 'description' => 'رمز 2FA خاطئ ← رفض', 'details' => 'بعد تفعيل 2FA، أدخل رمزاً خاطئاً — يجب رفض الدخول مع رسالة خطأ.'],
            ['category' => 'لوحة المستخدم | 1. التسجيل والمصادقة', 'item_key' => 'auth.auto_poll_verify', 'description' => 'صفحة verification.notice تكتشف التفعيل تلقائياً', 'details' => 'بعد التسجيل، تبقى صفحة /verify-email تستعلم كل 3 ثوانٍ (wire:poll.3s). عند النقر على رابط التفعيل، تكتشف الصفحة التفعيل وتُوجّه تلقائياً إلى onboarding.plan دون تحديث يدوي.'],

            // ----------------------------------------------------------------
            // US.2  الإعداد الأولي (Onboarding)  (21 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.view_plans', 'description' => 'عرض الباقات (شهرية/سنوية) مع المميزات والأسعار', 'details' => 'بعد التفعيل، يُوجّه إلى /onboarding/plan. تظهر الباقات من SubscriptionPlan: النشطة والعامة. يوجد toggle شهري/سنوي. تظهر المميزات والأسعار.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.free_plan', 'description' => 'اختيار باقة مجانية ← إنشاء workspace + اشتراك Active فوراً', 'details' => 'اختر الباقة المجانية. تُنشأ workspace. يُنشأ اشتراك Active لمدة 10 سنوات. يُستدعى markPlanConfirmed(). يُوجّه إلى /onboarding/setup.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.paid_plan', 'description' => 'اختيار باقة مدفوعة ← اختيار طريقة الدفع ← توجيه حسب الطريقة', 'details' => 'اختر باقة مدفوعة. اختر طريقة دفع (Chargily, BaridiMob, RedotPay). للتوجيه: online ← بوابة الدفع، manual ← صفحة رفع الإثبات، auto_complete ← انتظار التأكيد.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.coupon', 'description' => 'استخدام كود خصم ← تطبيق الخصم على السعر', 'details' => 'اختر باقة مدفوعة. أدخل كود خصم. يُتحقق من validity. يظهر تحليل السعر مع الخصم.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.coupon_gateway_restrict', 'description' => 'كود خصم مربوط ببوابة غير المختارة ← رسالة خطأ', 'details' => 'أنشئ كود خصم مربوطاً بـ Chargily فقط. اختر BaridiMob كبوابة. أدخل الكود — يظهر خطأ. اختر Chargily — يُقبل الكود.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.payment_failure', 'description' => 'دفع فاشل ← رسالة خطأ + إعادة المحاولة', 'details' => 'استخدم بطاقة مرفوضة أو ألغِ الدفع. تظهر رسالة خطأ. يمكن إعادة اختيار طريقة الدفع. لا يُفعّل الاشتراك.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.pending_block', 'description' => 'وجود دفع معلق ← يُسمح بالدخول إلى /onboarding/plan مع Banner تحذيري', 'details' => 'أنشئ دفعاً معلقاً (checkout.pending). اذهب إلى /onboarding/plan. يظهر Banner: "لديك دفع معلق لخطة X. اختيار خطة جديدة سيلغي الدفع الحالي."'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.change_plan', 'description' => 'تغيير الخطة أثناء وجود دفع معلق ← إلغاء القديم + إنشاء الجديد', 'details' => 'في /onboarding/plan، اختر خطة مختلفة عن الخطة المعلقة. اضغط "اشتراك". يُلغى الدفع القديم تلقائياً. يُنشأ دفع جديد للخطة الجديدة.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.status_page', 'description' => '/payment/status/{payment} ← صفحة حالة الدفع الموحّدة', 'details' => 'تظهر الصفحة حسب حالة الدفع: pending (أزرار: متابعة، تغيير طريقة، تغيير خطة)، completed (توجيه إلى setup)، failed (أزرار: إعادة محاولة، تغيير طريقة)، canceled (أزرار: إعادة محاولة، تغيير طريقة، تغيير خطة).'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.status_continue', 'description' => '/payment/status ← زر "متابعة الدفع" ← يعيد التوجيه إلى بوابة الدفع', 'details' => 'في صفحة status (حالة pending)، اضغط "متابعة الدفع". يُعاد التوجيه إلى بوابة الدفع عبر getContinueUrl() أو gateway->charge().'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.status_cancel_change_plan', 'description' => '/payment/status ← زر "إلغاء الدفع وتغيير الخطة" ← إلغاء + توجيه إلى /onboarding/plan', 'details' => 'في صفحة status، اضغط "إلغاء الدفع وتغيير الخطة". يُلغى الدفع (CheckoutCanceled). يُوجّه إلى /onboarding/plan لاختيار خطة جديدة.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.status_switch_method', 'description' => '/payment/status ← زر "تغيير طريقة الدفع" ← توجيه إلى /onboarding/plan', 'details' => 'في صفحة status، اضغط "تغيير طريقة الدفع". يُوجّه إلى /onboarding/plan مع تحديد الخطة الحالية، حيث يمكن اختيار طريقة جديدة.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.status_retry', 'description' => '/payment/status ← زر "إعادة المحاولة" ← يُلغي الدفع الحالي ويعيد الشحن', 'details' => 'في صفحة status (حالة failed/canceled)، اضغط "إعادة المحاولة". يُلغى الدفع الحالي. يُعاد شحنه عبر نفس البوابة. إذا Online + redirectUrl ← JS redirect إلى البوابة.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.fee_breakdown_plan', 'description' => 'عرض تفصيل الرسوم في /onboarding/plan عند اختيار طريقة دفع', 'details' => 'اختر باقة مدفوعة ثم طريقة دفع. يظهر تحليل السعر: سعر الخطة، الخصم، رسوم البوابة، الضريبة المضافة، المجموع.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.fee_breakdown_status', 'description' => 'عرض تفصيل الرسوم في /payment/status/{id} من سجل الدفع', 'details' => 'اذهب إلى /payment/status/{id} لدفع معلق/فاشل/ملغي. يظهر تفصيل الرسوم من سجل الدفع المخزّن.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.autocomplete_fix', 'description' => 'إصلاح autocomplete في البحث: إزالة name في Livewire، استخدام name="{{ $name }}" في GET forms', 'details' => 'اختبر مربع البحث في super-admin (GET form). يجب إرسال ?search=value. اختبر البحث في Livewire — لا يظهر suggest.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.complete', 'description' => 'إكمال الإعداد ← تسمية مساحة العمل ← completeOnboarding ← Dashboard', 'details' => 'بعد الدفع الناجح/الخطة المجانية، يُوجّه إلى /onboarding/setup. أدخل اسم workspace. يُستدعى completeOnboarding(). يُوجّه إلى /dashboard.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.free_plan_new', 'description' => 'اختيار باقة Free (الجديدة) ← اشتراك Active 10 سنوات بدون Trial', 'details' => 'بعد التسجيل، اختر باقة Free. اضغط Continue. يُنشأ اشتراك Active لمدة 10 سنوات. يُوجّه إلى /onboarding/setup.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.trial_plan', 'description' => 'اختيار باقة Personal (Trial) ← اشتراك Trialing 30 يوماً', 'details' => 'بعد التسجيل، اختر باقة Personal. يظهر زر "Start Free Trial". اضغط عليه. يُنشأ اشتراك Trialing مع trial_ends_at = now+30 يوماً. لا يُطلب دفع. يُوجّه إلى /onboarding/setup.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.trial_plan_not_paid', 'description' => 'باقة Trial لا تظهر خيارات الدفع (Business/Professional تظهرها)', 'details' => 'اختر Personal (Trial). يجب ألا يظهر قسم الدفع أو طرق الدفع. يظهر فقط زر "Start Free Trial". اختر Business — تظهر طرق الدفع.'],
            ['category' => 'لوحة المستخدم | 2. الإعداد الأولي', 'item_key' => 'onboarding.free_new_only', 'description' => 'باقة Free متاحة فقط للحسابات الجديدة', 'details' => 'أنشئ حساباً واختر Free. اذهب إلى /account/subscriptions. يجب ألا تظهر Free في قائمة الباقات المتاحة (لأن لديك سجل اشتراك). Personal + Business + Professional + Enterprise تظهر.'],

            // ----------------------------------------------------------------
            // US.3  لوحة التحكم والتقارير  (4 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 3. لوحة التحكم والتقارير', 'item_key' => 'dashboard.summary', 'description' => 'Dashboard يعرض ملخص الأصول + الإيرادات + المصروفات + الميزانية + آخر المعاملات + الديون القادمة', 'details' => 'اذهب إلى /dashboard. يظهر: ملخص الأصول (الإجمالي)، الإيرادات (الشهر)، المصروفات (الشهر)، حالة الميزانية، آخر 5 معاملات، الديون القادمة (30 يوماً).'],
            ['category' => 'لوحة المستخدم | 3. لوحة التحكم والتقارير', 'item_key' => 'dashboard.livewire', 'description' => 'تحديث فوري عبر Livewire polling/events', 'details' => 'أضف معاملة في نافذة. dashboard في نافذة أخرى — تتغير الأرقام دون تحديث يدوي.'],
            ['category' => 'لوحة المستخدم | 3. لوحة التحكم والتقارير', 'item_key' => 'reports.accuracy', 'description' => 'التقارير (الأصول/الإيرادات/المصروفات/التدفق النقدي) بأرقام صحيحة', 'details' => 'اذهب إلى التقارير. قارن الأرقام مع قاعدة البيانات. (الإيرادات - المصروفات) = صافي التدفق.'],
            ['category' => 'لوحة المستخدم | 3. لوحة التحكم والتقارير', 'item_key' => 'reports.export', 'description' => 'تصدير التقارير (PDF/Excel/CSV)', 'details' => 'اذهب إلى أي تقرير. اضغط تصدير. اختر PDF/Excel/CSV. الملف يحتوي على البيانات الصحيحة.'],

            // ----------------------------------------------------------------
            // US.4  الأصول  (7 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 4. الأصول', 'item_key' => 'assets.create_types', 'description' => 'إضافة أصل بجميع الأنواع', 'details' => 'اذهب إلى الأصول → إضافة أصل. الأنواع: نقدي، بنكي، CCP، ذهب، فضة، عقار، أسهم، عملات رقمية، أخرى.'],
            ['category' => 'لوحة المستخدم | 4. الأصول', 'item_key' => 'assets.crud', 'description' => 'تعديل وحذف (ناعم + نهائي)', 'details' => 'تعديل اسم/قيمة ← حفظ. حذف ناعم ← يظهر في سلة المهملات. حذف نهائي ← يُزال بالكامل.'],
            ['category' => 'لوحة المستخدم | 4. الأصول', 'item_key' => 'assets.restore', 'description' => 'استعادة من سلة المهملات', 'details' => 'احذف ناعم. اذهب إلى سلة المهملات. استعادة ← deleted_at = null.'],
            ['category' => 'لوحة المستخدم | 4. الأصول', 'item_key' => 'assets.filter_search', 'description' => 'تصفية حسب النوع + بحث بالاسم', 'details' => 'فلتر النوع يظهر أصول نوع واحد. بحث بالاسم يصفّي النتائج. يمكن الجمع بينهما.'],
            ['category' => 'لوحة المستخدم | 4. الأصول', 'item_key' => 'assets.bulk_actions', 'description' => 'عمليات جماعية (حذف/استعادة)', 'details' => 'اختر أصولاً باستخدام checkbox. حذف جماعي ← ناعم. استعادة جماعية من سلة المهملات.'],
            ['category' => 'لوحة المستخدم | 4. الأصول', 'item_key' => 'assets.indicators', 'description' => 'مؤشرات الأداء (الإجمالي، السائل، القابل للزكاة، العدد)', 'details' => 'في صفحة الأصول: إجمالي القيمة، الأصول السائلة (نقدي+بنكي+CCP)، الأصول القابلة للزكاة، عدد الأصول.'],
            ['category' => 'لوحة المستخدم | 4. الأصول', 'item_key' => 'assets.export', 'description' => 'تصدير إلى Excel/CSV/PDF', 'details' => 'زر التصدير. جرب Excel (.xlsx), CSV (.csv), PDF (.pdf). الملف يحتوي على الأصول المعروضة.'],

            // ----------------------------------------------------------------
            // US.5  الإيرادات  (4 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 5. الإيرادات', 'item_key' => 'income.crud', 'description' => 'إضافة/تعديل/حذف إيراد مع الفئة', 'details' => 'اذهب إلى الإيرادات. أضف: المبلغ، التاريخ، الفئة (IncomeCategory)، الوصف. تعديل ← حفظ. حذف ناعم ونهائي.'],
            ['category' => 'لوحة المستخدم | 5. الإيرادات', 'item_key' => 'income.filter', 'description' => 'تصفية الإيرادات حسب الفئة والتاريخ والنطاق السعري', 'details' => 'استخدم الفلتر: فئة محددة، نطاق تاريخي (من-إلى)، نطاق سعري. تظهر النتائج المصفّاة.'],
            ['category' => 'لوحة المستخدم | 5. الإيرادات', 'item_key' => 'income.export', 'description' => 'تصدير الإيرادات إلى Excel/CSV/PDF', 'details' => 'زر التصدير. الملف يحتوي على التاريخ، المبلغ، الفئة، الوصف.'],
            ['category' => 'لوحة المستخدم | 5. الإيرادات', 'item_key' => 'income.bulk_delete', 'description' => 'حذف جماعي للإيرادات', 'details' => 'اختر إيرادات متعددة. حذف جماعي ← soft delete. استعادة جماعية من سلة المهملات.'],

            // ----------------------------------------------------------------
            // US.6  المصروفات  (4 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 6. المصروفات', 'item_key' => 'expense.crud', 'description' => 'إضافة/تعديل/حذف مصروف مع الفئة', 'details' => 'اذهب إلى المصروفات. أضف: المبلغ، التاريخ، الفئة (ExpenseCategory)، الوصف. يظهر في التقارير.'],
            ['category' => 'لوحة المستخدم | 6. المصروفات', 'item_key' => 'expense.filter', 'description' => 'تصفية المصروفات حسب الفئة والتاريخ والنطاق السعري', 'details' => 'استخدم الفلتر: فئة محددة، نطاق تاريخي، نطاق سعري. تظهر النتائج المصفّاة.'],
            ['category' => 'لوحة المستخدم | 6. المصروفات', 'item_key' => 'expense.export', 'description' => 'تصدير المصروفات إلى Excel/CSV/PDF', 'details' => 'زر التصدير. الملف يحتوي على التاريخ، المبلغ، الفئة، الوصف.'],
            ['category' => 'لوحة المستخدم | 6. المصروفات', 'item_key' => 'expense.bulk_delete', 'description' => 'حذف جماعي للمصروفات', 'details' => 'اختر مصروفات متعددة. حذف جماعي ← soft delete. استعادة جماعية من سلة المهملات.'],

            // ----------------------------------------------------------------
            // US.7  فئات الإيرادات والمصروفات  (3 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 7. الفئات', 'item_key' => 'categories.manage', 'description' => 'إدارة فئات الإيرادات والمصروفات', 'details' => 'الإعدادات → الفئات. أضف/عدّل/احذف فئة إيراد أو مصروف. اختر عامة/خاصة.'],
            ['category' => 'لوحة المستخدم | 7. الفئات', 'item_key' => 'categories.income_specific', 'description' => 'إضافة فئة إيراد جديدة (الاسم، النوع، اللون)', 'details' => 'أضف فئة إيراد باسم "freelance". اختر اللون. اختر إن كانت عامة. تظهر في الإيرادات.'],
            ['category' => 'لوحة المستخدم | 7. الفئات', 'item_key' => 'categories.expense_specific', 'description' => 'إضافة فئة مصروف جديدة (الاسم، النوع، اللون)', 'details' => 'أضف فئة مصروف باسم "فواتير". اختر اللون. تظهر في المصروفات والميزانيات.'],

            // ----------------------------------------------------------------
            // US.8  الميزانيات  (4 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 8. الميزانيات', 'item_key' => 'budgets.create', 'description' => 'إنشاء ميزانية (شهر/سنة + فئة + حد)', 'details' => 'اذهب إلى الميزانيات. أنشئ: الشهر، فئة مصروف، حد الإنفاق.'],
            ['category' => 'لوحة المستخدم | 8. الميزانيات', 'item_key' => 'budgets.consumption', 'description' => 'تسجيل مصروف ← تحديث نسبة الاستهلاك', 'details' => 'أضف مصروفاً ضمن فئة الميزانية. يتحدّث spent_amount ونسبة (spent/limit*100).'],
            ['category' => 'لوحة المستخدم | 8. الميزانيات', 'item_key' => 'budgets.warning_80', 'description' => 'تحذير عند 80%', 'details' => 'اجعل الاستهلاك 80%. يظهر تحذير. يُرسل إشعار.'],
            ['category' => 'لوحة المستخدم | 8. الميزانيات', 'item_key' => 'budgets.alert_exceed', 'description' => 'إنذار عند تجاوز الحد', 'details' => 'تجاوز 100%. يظهر إنذار. يُرسل إشعار. قد يُمنع إضافة مصروفات إضافية.'],

            // ----------------------------------------------------------------
            // US.9  الديون  (6 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 9. الديون', 'item_key' => 'debts.create', 'description' => 'إضافة دين مع النوع والطرف والاستحقاق', 'details' => 'اذهب إلى الديون. الأنواع: دين عليّ / دين لي. أدخل المبلغ، الطرف المقابل، تاريخ الاستحقاق.'],
            ['category' => 'لوحة المستخدم | 9. الديون', 'item_key' => 'debts.partial', 'description' => 'تسديد جزئي + تحديث المبلغ المتبقي', 'details' => 'أضف دفعة جزئية. يتحدّث المبلغ المتبقي. الحالة ← Partial.'],
            ['category' => 'لوحة المستخدم | 9. الديون', 'item_key' => 'debts.full_payment', 'description' => 'تسديد كامل ← Paid', 'details' => 'أضف دفعة بكامل المبلغ. المبلغ المتبقي = 0. الحالة ← Paid.'],
            ['category' => 'لوحة المستخدم | 9. الديون', 'item_key' => 'debts.overdue', 'description' => 'تصنيف الديون المتأخرة تلقائياً', 'details' => 'إذا مرّ تاريخ الاستحقاق ولم يُسدّد، الحالة ← Overdue. يظهر في dashboard.'],
            ['category' => 'لوحة المستخدم | 9. الديون', 'item_key' => 'debts.filter', 'description' => 'تصفية الديون حسب الحالة والنوع', 'details' => 'فلتر حسب: نشط، مدفوع، متأخر. فلتر حسب: دين عليّ، دين لي.'],
            ['category' => 'لوحة المستخدم | 9. الديون', 'item_key' => 'debts.export', 'description' => 'تصدير الديون إلى Excel/CSV', 'details' => 'زر التصدير. الملف يحتوي على: الطرف، المبلغ، المتبقي، الحالة، تاريخ الاستحقاق.'],

            // ----------------------------------------------------------------
            // US.10  الأهداف المالية  (7 items)  ← NEW
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 10. الأهداف المالية', 'item_key' => 'goals.create', 'description' => 'إنشاء هدف مالي (ادخار/إنفاق/كسب)', 'details' => 'اذهب إلى الأهداف. الأنواع: ادخار (Save)، إنفاق (Spend)، كسب (Earn). أدخل الاسم، المبلغ المستهدف، تاريخ الانتهاء.'],
            ['category' => 'لوحة المستخدم | 10. الأهداف المالية', 'item_key' => 'goals.progress', 'description' => 'تتبع التقدم (current_amount يتحدّث تلقائياً)', 'details' => 'سجّل إيراداً ← هدف الكسب يتقدّم. سجّل مصروفاً ← هدف الادخار يتقدّم. النسبة المئوية تتحدّث.'],
            ['category' => 'لوحة المستخدم | 10. الأهداف المالية', 'item_key' => 'goals.complete', 'description' => 'تحديد هدف كمكتمل', 'details' => 'عند بلوغ المبلغ المستهدف، اختر "تحديد كمكتمل". الحالة ← Completed.'],
            ['category' => 'لوحة المستخدم | 10. الأهداف المالية', 'item_key' => 'goals.abandon', 'description' => 'إلغاء هدف (Abandoned)', 'details' => 'اختر هدفاً نشطاً. اضغط "إلغاء". الحالة ← Abandoned. يختفي من الأهداف النشطة.'],
            ['category' => 'لوحة المستخدم | 10. الأهداف المالية', 'item_key' => 'goals.filter', 'description' => 'تصفية الأهداف حسب الحالة والنوع', 'details' => 'فلتر حسب: نشط، مكتمل، ملغي. فلتر حسب نوع الهدف.'],
            ['category' => 'لوحة المستخدم | 10. الأهداف المالية', 'item_key' => 'goals.edit_delete', 'description' => 'تعديل وحذف هدف', 'details' => 'عدّل الاسم، المبلغ المستهدف، تاريخ الانتهاء. حذف ناعم ثم نهائي.'],
            ['category' => 'لوحة المستخدم | 10. الأهداف المالية', 'item_key' => 'goals.dashboard', 'description' => 'عرض الأهداف في Dashboard', 'details' => 'في /dashboard: يظهر ملخص الأهداف — قيد التقدم، المكتملة، القادمة.'],

            // ----------------------------------------------------------------
            // US.11  الزكاة  (6 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 11. الزكاة', 'item_key' => 'zakat.calculator_load', 'description' => 'تحميل الأصول القابلة للزكاة تلقائياً', 'details' => 'اذهب إلى الزكاة → حاسبة الزكاة. الأنواع القابلة: نقدي، بنكي، CCP، ذهب، فضة، أسهم، عملات رقمية. عقار لا يظهر.'],
            ['category' => 'لوحة المستخدم | 11. الزكاة', 'item_key' => 'zakat.nisab', 'description' => 'حساب النصاب (85g ذهب / 595g فضة)', 'details' => 'أدخل سعر الذهب (عيار 24) والفضة. يُحسب النصاب. يظهر إشارة بلوغ النصاب.'],
            ['category' => 'لوحة المستخدم | 11. الزكاة', 'item_key' => 'zakat.calculate', 'description' => 'حساب الزكاة (2.5%)', 'details' => 'المجموع القابل للزكاة × 2.5%. إضافة/إزالة أصول يحدّث النتيجة.'],
            ['category' => 'لوحة المستخدم | 11. الزكاة', 'item_key' => 'zakat.save', 'description' => 'حفظ النتيجة في سجل الزكاة', 'details' => 'اضغط حفظ. يظهر السجل في سجل الزكاة.'],
            ['category' => 'لوحة المستخدم | 11. الزكاة', 'item_key' => 'zakat.report', 'description' => 'عرض تقرير زكاة محفوظ', 'details' => 'اختر عملية من السجل. تظهر التفاصيل: الأصول، الأسعار، النصاب، المبلغ.'],
            ['category' => 'لوحة المستخدم | 11. الزكاة', 'item_key' => 'zakat.print', 'description' => 'طباعة التقرير', 'details' => 'اضغط طباعة. تنسيق مناسب للطباعة. اختبر الطباعة إلى PDF.'],

            // ----------------------------------------------------------------
            // US.12  المعاملات  (7 items)  ← NEW (Recurring + Import)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 12. المعاملات', 'item_key' => 'transactions.list', 'description' => 'عرض جميع المعاملات (إيرادات + مصروفات) في صفحة واحدة', 'details' => 'اذهب إلى المعاملات. تظهر الإيرادات والمصروفات في قائمة موحّدة مرتبة زمنياً مع أيقونة تميز النوع.'],
            ['category' => 'لوحة المستخدم | 12. المعاملات', 'item_key' => 'transactions.filter', 'description' => 'تصفية المعاملات حسب النوع والفئة والتاريخ', 'details' => 'فلتر: إيرادات فقط / مصروفات فقط / الكل. فلتر حسب الفئة. نطاق تاريخي.'],
            ['category' => 'لوحة المستخدم | 12. المعاملات', 'item_key' => 'transactions.export', 'description' => 'تصدير المعاملات إلى Excel/CSV/PDF', 'details' => 'اذهب إلى المعاملات. صدّر. الملف يحتوي على: التاريخ، المبلغ، الفئة، النوع، الوصف.'],
            ['category' => 'لوحة المستخدم | 12. المعاملات', 'item_key' => 'transactions.recurring_create', 'description' => 'إنشاء معاملة متكررة (يومي/أسبوعي/شهري/سنوي)', 'details' => 'أضف معاملة متكررة: المبلغ، الفئة، الدورة (يومي/أسبوعي/شهري/سنوي)، تاريخ البدء، عدد التكرارات.'],
            ['category' => 'لوحة المستخدم | 12. المعاملات', 'item_key' => 'transactions.recurring_process', 'description' => 'معالجة المعاملات المتكررة عبر schedule', 'details' => 'شغّل schedule:run. تُنشأ معاملات جديدة حسب الجدول. تظهر في القائمة.'],
            ['category' => 'لوحة المستخدم | 12. المعاملات', 'item_key' => 'transactions.recurring_pause_resume', 'description' => 'إيقاف/استئناف معاملة متكررة', 'details' => 'أوقف معاملة متكررة ← لا تُنشأ معاملات جديدة. استأنفها ← تعود للعمل.'],
            ['category' => 'لوحة المستخدم | 12. المعاملات', 'item_key' => 'transactions.import_csv', 'description' => 'استيراد معاملات من CSV', 'details' => 'حمّل ملف CSV. اختر تطابق الأعمدة. معاينة. استيراد. تظهر المعاملات المستوردة. أخطاء الصيغ تُبلغ عنها.'],

            // ----------------------------------------------------------------
            // US.13  البحث العام  (3 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 13. البحث العام', 'item_key' => 'search.results', 'description' => 'البحث عبر الأصول/الإيرادات/المصروفات/الديون', 'details' => 'استخدم مربع البحث العام. اكتب اسم/وصف — تظهر النتائج من جميع الفئات.'],
            ['category' => 'لوحة المستخدم | 13. البحث العام', 'item_key' => 'search.navigation', 'description' => 'النقر على نتيجة ← التوجيه للصفحة المعنية', 'details' => 'انقر على نتيجة أصل ← يُوجّه لصفحة الأصل. اختبر جميع الأنواع.'],
            ['category' => 'لوحة المستخدم | 13. البحث العام', 'item_key' => 'search.no_results', 'description' => 'بحث بدون نتائج ← رسالة', 'details' => 'اكتب نصاً عشوائياً ← "لا توجد نتائج". لا أخطاء.'],

            // ----------------------------------------------------------------
            // US.14  الاشتراكات والفواتير  (18 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'subscriptions.activation', 'description' => 'بعد دفع ناجح ← اشتراك Active + فاتورة Paid', 'details' => 'بعد أي دفع ناجح: تحقق من subscriptions — سجل Active مع starts_at/ends_at. تحقق من invoices — فاتورة Paid.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'subscriptions.cancel', 'description' => 'إلغاء الاشتراك ← Canceled + بريد تأكيد الإلغاء', 'details' => 'الإعدادات → الاشتراك → إلغاء. حالة الاشتراك ← Canceled. فترة السماح سارية. يُرسل بريد.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'subscriptions.upgrade_email', 'description' => 'ترقية الباقة ← بريد ترقية (SubscriptionUpgraded)', 'details' => 'غيّر الباقة إلى أعلى سعراً. يُرسل بريد SubscriptionUpgraded مع اسمي الباقتين.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'subscriptions.downgrade_email', 'description' => 'تخفيض الباقة ← بريد تخفيض (SubscriptionDowngraded)', 'details' => 'غيّر الباقة إلى أقل سعراً. يُرسل بريد SubscriptionDowngraded مع اسمي الباقتين وفترة التفعيل.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'subscriptions.auto_renew', 'description' => 'تجديد تلقائي مع فاتورة جديدة', 'details' => 'تأكد من auto_renew=true. شغّل schedule:run. تُنشأ فاتورة جديدة. تتحدّث starts_at/ends_at.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'subscriptions.past_due', 'description' => 'فشل تجديد ← Past Due + إشعار', 'details' => 'عطّل auto_renew. شغّل التجديد. الاشتراك ← Past Due. يُرسل إشعار للمستخدم.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'subscriptions.change_plan', 'description' => 'تغيير الباقة مع proration', 'details' => 'الإعدادات → الاشتراك → تغيير الباقة. يُحسب proration. تُنشأ فاتورة بالمبلغ الصحيح.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'subscriptions.expired_block', 'description' => 'منع استخدام الميزات بعد انتهاء الاشتراك', 'details' => 'اجعل الاشتراك منتهياً. حاول إنشاء معاملة — يجب المنع عبر middleware.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'subscriptions.fee_breakdown', 'description' => 'عرض تفصيل الرسوم قبل تغيير الباقة', 'details' => 'اذهب إلى /account/subscriptions. اختر باقة مدفوعة. اختر طريقة دفع. يظهر تفصيل: السعر، الخصم، رسوم البوابة، الضريبة، الإجمالي.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'upgrade.free_to_business', 'description' => 'ترقية من مجاني إلى Business (شهري) — دفع كامل السعر', 'details' => 'أنشئ حساباً بخطة Personal. اذهب إلى /account/subscriptions. اختر Business (شهري). أتمم الدفع. تحقق: اشتراك Business Active.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'upgrade.midcycle_proration', 'description' => 'ترقية في منتصف الدورة — proration تناسبي', 'details' => 'أنشئ اشتراك Business شهري بدأ قبل 10 أيام. اختر Professional شهري. تحقق: proration. ends_at محمول.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'upgrade.with_coupon', 'description' => 'ترقية مع كوبون خصم — تطبيق الخصم بعد proration', 'details' => 'Business → Professional مع كوبون خصم 20%. تحقق: الخصم يُطبق على المبلغ الإجمالي.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'downgrade.full_credit', 'description' => 'تخفيض مع رصيد (السعر <= 0) — تفعيل فوري بدون دفع', 'details' => 'إذا كانت القيمة المتبقية > سعر الفترة الجديدة، يُفعّل الاشتراك فوراً بدون دفع.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'downgrade.partial_payment', 'description' => 'تخفيض مع دفعة مخفضة (السعر > 0 بعد proration)', 'details' => 'proration يعطي مبلغاً موجباً لكن أقل من السعر الكامل. ادفع المبلغ المخفض.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'downgrade.blocked_too_many_users', 'description' => 'تخفيض محظور — مستخدمون أكثر من max_users', 'details' => 'أضف مستخدمين يتجاوزون حد الباقة الجديدة. يجب ظهور خطأ.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'resume.during_grace', 'description' => 'استئناف اشتراك ملغي ضمن فترة السماح ← Active', 'details' => 'ألغِ اشتراك. اذهب إلى /account/subscriptions. اضغط "استئناف". الاشتراك عاد Active.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'resume.after_grace', 'description' => 'استئناف بعد انتهاء فترة السماح ← ممنوع', 'details' => 'عدّل grace_ends_at يدوياً إلى تاريخ ماضٍ. زر الاستئناف لا يظهر.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'subscriptions.meta_gateway_type', 'description' => 'جدول الاشتراكات يعرض اسم البوابة + نوع الطريقة', 'details' => 'اذهب إلى /account/subscriptions. الخانة الأولى تظهر اسم البوابة (مثل Chargily) ونوعها (مثل Online).'],

            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'trial.plan_starts_trialing', 'description' => 'اشتراك Trial جديد ـ Trialing + trial_ends_at بعد 30 يوماً', 'details' => 'اختر Personal. يُنشأ اشتراك: status=Trialing, trial_ends_at=now+30, ends_at=trial_ends_at, plan_price_amount=0.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'trial.active_during_trial', 'description' => 'خلال فترة Trial ـ جميع الميزات متاحة', 'details' => 'سجّل الدخول. dashboard, المعاملات, التقارير — كلها متاحة. لا يظهر تحذير اشتراك.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'trial.expired_block', 'description' => 'بعد انتهاء Trial ـ منع الوصول للميزات + توجيه إلى الاشتراكات', 'details' => 'عدّل trial_ends_at إلى الماضي. حاول فتح dashboard — يُمنع ويُوجّه إلى /account/subscriptions.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'trial.pay_after_expiry', 'description' => 'بعد انتهاء Trial ـ اشتراك جديد (Business/Professional) يفعّل الاشتراك', 'details' => 'بعد انتهاء trial، اختر Business وادفع. يصبح اشتراك Business Active.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'trial.downgrade_to_free_blocked', 'description' => 'بعد Trial ـ لا يمكن الرجوع إلى Free (لوجود سجل)', 'details' => 'أنشئ حساباً واختر Personal (trial). اذهب إلى /account/subscriptions. Free لا يظهر في القائمة.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'upgrade.free_to_personal_trial', 'description' => 'ترقية من Free إلى Personal Trial', 'details' => 'أنشئ حساباً بخطة Free. اذهب إلى /account/subscriptions. اختر Personal. يُنشأ اشتراك Trialing.'],
            ['category' => 'لوحة المستخدم | 14. الاشتراكات والفواتير', 'item_key' => 'plans.landing_show_new_hierarchy', 'description' => 'صفحة landing تعرض Free + Personal (بسعر) + Business + Professional + Enterprise', 'details' => 'اذهب إلى /. تظهر Free بسعر 0. Personal بسعر $3.99/شهر مع شعار "Start Free Trial". Business كـ Popular.'],

            // ----------------------------------------------------------------
            // US.15  بوابات الدفع (وجهة مستخدم)  (19 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'chargily.checkout', 'description' => 'Chargily: إنشاء Checkout ← التوجيه إلى صفحة Chargily', 'details' => 'اختر Chargily كطريقة دفع. يُنشأ checkout. يُوجّه إلى صفحة Chargily.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'chargily.payment_success', 'description' => 'Chargily: دفع ناجح ← اشتراك Active + فاتورة Paid', 'details' => 'استخدم CIB أو Edahabia. بعد الدفع، يُوجّه إلى /payment/return. تتحدّث حالة payment.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'chargily.payment_cancel', 'description' => 'Chargily: إلغاء الدفع ← رسالة الإلغاء', 'details' => 'في صفحة Chargily، اضغط إلغاء. يُعاد مع رسالة. حالة payment ← checkout.canceled.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'baridimob.show_instructions', 'description' => 'BaridiMob: عرض تعليمات التحويل (RIP/IBAN)', 'details' => 'اختر BaridiMob. تظهر تعليمات التحويل: رقم الحساب (RIP/IBAN)، اسم الحساب، المبلغ.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'baridimob.upload_proof', 'description' => 'BaridiMob: رفع إثبات الدفع ← قيد المراجعة', 'details' => 'ارفع صورة/PDF (حد 4MB). أدخل رقم مرجعي. يُنشأ verification pending.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'redotpay.show_instructions', 'description' => 'RedotPay: عرض تعليمات التحويل (Account ID)', 'details' => 'اختر RedotPay. تظهر تعليمات التحويل إلى حساب RedotPay.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'redotpay.upload_proof', 'description' => 'RedotPay: رفع إثبات الدفع ← قيد المراجعة', 'details' => 'ارفع إثبات الدفع. أدخل المرجع. يُنشأ verification pending.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'chargily.webhook_valid', 'description' => 'Chargily: Webhook بتوقيع صحيح ← 200', 'details' => 'أرسل POST /payment/webhook/chargily بتوقيع صحيح. يُعيد 200.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'chargily.webhook_invalid', 'description' => 'Chargily: Webhook بتوقيع خاطئ ← 401', 'details' => 'أرسل webhook بتوقيع خاطئ ← 401. بدون توقيع ← 403.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'plan.features_collapse', 'description' => 'بطاقات الخطط + صفحة الدفع تعرض الميزات مع توسيع/طي (أول 5)', 'details' => 'اذهب إلى /onboarding/plan واختر خطة مدفوعة. تظهر أول 5 ميزات مع زر "عرض المزيد".'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'cancel_payment.from_subscriptions', 'description' => 'إلغاء دفع معلق من صفحة الاشتراكات', 'details' => 'أنشئ دفعاً معلقاً. اذهب إلى /account/subscriptions. يظهر تنبيه مع زر "إلغاء الدفع".'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'cancel_payment.unblocks_change', 'description' => 'إلغاء دفع معلق ← إتاحة تغيير الباقة مرة أخرى', 'details' => 'بعد إلغاء الدفع المعلق، حاول تغيير الباقة — يجب أن يسمح الآن.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'gateways.inactive_list', 'description' => 'البوابات غير النشطة (Stripe, PayPal, Wise, Payoneer, Noest, Cash, Delivery)', 'details' => 'هذه البوابات منفذة كـ Gateways لكنها غير نشطة في DB. لتفعيلها: استخدم PaymentGatewaySeeder.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'trust_banner.plan', 'description' => 'trust-banner — شعارات الأمان تظهر في /onboarding/plan', 'details' => 'اذهب إلى /onboarding/plan مع اختيار خطة مدفوعة. يظهر شريط الثقة: أيقونة قفل + "مدفوعاتك مشفرة" + "بوابات دفع آمنة" + "طرق دفع متعددة".'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'trust_banner.status_manual', 'description' => 'trust-banner — يظهر في /payment/status و /onboarding/manual-proof', 'details' => 'في صفحة حالة الدفع وصفحة الإثبات اليدوي: يظهر شعار الأمان لطمأنة المستخدم.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'manual_proof.change_method', 'description' => 'تغيير طريقة الدفع من manual-proof ← يذهب إلى /onboarding/plan (مع إلغاء الدفع الحالي)', 'details' => 'في /onboarding/manual-proof، اضغط "تغيير طريقة الدفع". يُلغى الدفع الحالي (بعد تأكيد). يُوجّه إلى /onboarding/plan.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'manual_proof.change_plan', 'description' => 'من manual-proof ← تغيير الخطة', 'details' => 'في /onboarding/manual-proof، يوجد زر "تغيير الخطة". يُلغى الدفع الحالي. يُوجّه إلى /onboarding/plan لاختيار خطة مختلفة.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'cancel_verify_manual', 'description' => '?cancel في رابط العودة ← تحقق مع gateway حتى للمethods اليدوية', 'details' => 'أضف ?cancel إلى رابط العودة لدفعة يدوية. يجب أن يستدعي النظام $gateway->verify() أولاً. إذا الدفع لم يُدفع فعلاً، يُلغى. إذا مدفوع، لا يُلغى.'],
            ['category' => 'لوحة المستخدم | 15. بوابات الدفع', 'item_key' => 'session_resolution_safe', 'description' => 'pending_payment_id في Session لا يُستخدم لحل الدفعة — الأولوية للـ URL parameter', 'details' => 'اختبر العودة من بوابة الدفع بدون {payment} parameter (فقط session). يجب أن يتحقق النظام من أن session(\'pending_payment_id\') ينتمي للمستخدم الحالي قبل استخدامه.'],

            // ----------------------------------------------------------------
            // US.16  الأعضاء والصلاحيات  (6 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 16. الأعضاء والصلاحيات', 'item_key' => 'roles.invite', 'description' => 'دعوة مستخدم ← بريد ← قبول ← انضمام', 'details' => 'إعدادات مساحة العمل → الأعضاء. أدخل بريد مستخدم، اختر صلاحية، أرسل. المدعو يستلم بريداً. يقبل الدعوة — ينضم.'],
            ['category' => 'لوحة المستخدم | 16. الأعضاء والصلاحيات', 'item_key' => 'roles.read_only', 'description' => 'صلاحية مشاهدة فقط ← لا تعديل ولا إضافة', 'details' => 'ادعُ بمشاهدة فقط. يحاول إضافة/تعديل/حذف — يُمنع.'],
            ['category' => 'لوحة المستخدم | 16. الأعضاء والصلاحيات', 'item_key' => 'roles.editor', 'description' => 'صلاحية محرر ← إضافة وتعديل فقط', 'details' => 'يستطيع إضافة وتعديل. لا يستطيع حذف.'],
            ['category' => 'لوحة المستخدم | 16. الأعضاء والصلاحيات', 'item_key' => 'roles.manager', 'description' => 'صلاحية مدير ← صلاحية كاملة', 'details' => 'إضافة، تعديل، حذف، دعوة، تغيير صلاحيات — كل شيء مسموح.'],
            ['category' => 'لوحة المستخدم | 16. الأعضاء والصلاحيات', 'item_key' => 'roles.cross_workspace', 'description' => 'منع الوصول عبر مساحات العمل', 'details' => 'سجّل بحسابين. حسّب 1 لديه أصل ID 5. حسّب 2 يحاول /assets/5 — 403.'],
            ['category' => 'لوحة المستخدم | 16. الأعضاء والصلاحيات', 'item_key' => 'roles.change_member_role', 'description' => 'تغيير صلاحية عضو (مالك فقط)', 'details' => 'المالك يغيّر صلاحية عضو من "مشاهدة" إلى "محرر". العضو يكتسب الصلاحية فوراً.'],

            // ----------------------------------------------------------------
            // US.17  إعدادات المستخدم  (8 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 17. إعدادات المستخدم', 'item_key' => 'profile.edit', 'description' => 'تعديل الملف الشخصي (الاسم، البريد، كلمة المرور)', 'details' => 'الإعدادات → الملف الشخصي. غير الاسم ← حفظ. غير البريد ← إعادة تحقق. غير كلمة المرور ← إعادة تسجيل دخول.'],
            ['category' => 'لوحة المستخدم | 17. إعدادات المستخدم', 'item_key' => 'profile.language', 'description' => 'تبديل اللغة (عربي/فرنسي/إنجليزي)', 'details' => 'الإعدادات → اللغة. اختر لغة — تتغير الواجهة فوراً.'],
            ['category' => 'لوحة المستخدم | 17. إعدادات المستخدم', 'item_key' => 'profile.theme', 'description' => 'تبديل الثيم (فاتح/داكن)', 'details' => 'الإعدادات → المظهر. بدّل الثيم — تطبيق فوري.'],
            ['category' => 'لوحة المستخدم | 17. إعدادات المستخدم', 'item_key' => 'profile.2fa', 'description' => 'تفعيل 2FA (Google Authenticator)', 'details' => 'الإعدادات → الأمان. امسح QR code. أدخل الرمز للتحقق.'],
            ['category' => 'لوحة المستخدم | 17. إعدادات المستخدم', 'item_key' => 'profile.sessions', 'description' => 'عرض وقطع الجلسات النشطة', 'details' => 'الإعدادات → الأمان → الجلسات. اقطع جلسة عن بعد — تُنهى فوراً.'],
            ['category' => 'لوحة المستخدم | 17. إعدادات المستخدم', 'item_key' => 'notifications.markread_page', 'description' => 'زر "تحديد كمقروء" في صفحة الإشعارات', 'details' => 'اذهب إلى /notifications. اضغط "تحديد كمقروء" على إشعار غير مقروء. رسالة نجاح.'],
            ['category' => 'لوحة المستخدم | 17. إعدادات المستخدم', 'item_key' => 'notifications.locale_icons', 'description' => 'أيقونات صحيحة حسب نوع الإشعار', 'details' => 'تحقق من أيقونات مناسبة: budget_exceeded, debt_reminder, goal_achieved, zakat_reminder, role_changed.'],
            ['category' => 'لوحة المستخدم | 17. إعدادات المستخدم', 'item_key' => 'profile.currency_timezone', 'description' => 'تغيير العملة المفضلة والمنطقة الزمنية', 'details' => 'الإعدادات → العملة. اختر عملة العرض. غيّر المنطقة الزمنية. تظهر الأسعار والتواريخ بالاختيار الجديد.'],

            // ----------------------------------------------------------------
            // US.18  إعدادات مساحة العمل  (5 items)  ← NEW
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 18. إعدادات مساحة العمل', 'item_key' => 'workspace.edit', 'description' => 'تعديل اسم ووصف مساحة العمل', 'details' => 'الإعدادات → مساحة العمل. غيّر الاسم والوصف. حفظ ← يظهر في أعلى الصفحة.'],
            ['category' => 'لوحة المستخدم | 18. إعدادات مساحة العمل', 'item_key' => 'workspace.currency', 'description' => 'تغيير العملة الافتراضية لمساحة العمل', 'details' => 'غيّر العملة الافتراضية. المعاملات الجديدة تستخدم العملة الجديدة.'],
            ['category' => 'لوحة المستخدم | 18. إعدادات مساحة العمل', 'item_key' => 'workspace.timezone', 'description' => 'تغيير المنطقة الزمنية لمساحة العمل', 'details' => 'غيّر المنطقة الزمنية. التواريخ والتقارير تظهر بالتوقيت الجديد.'],
            ['category' => 'لوحة المستخدم | 18. إعدادات مساحة العمل', 'item_key' => 'workspace.members', 'description' => 'عرض قائمة أعضاء مساحة العمل', 'details' => 'اذهب إلى الإعدادات → الأعضاء. تظهر القائمة مع: الاسم، البريد، الصلاحية، تاريخ الانضمام.'],
            ['category' => 'لوحة المستخدم | 18. إعدادات مساحة العمل', 'item_key' => 'workspace.transfer_ownership', 'description' => 'نقل ملكية مساحة العمل', 'details' => 'المالك ينقل الملكية إلى عضو آخر. يصبح العضو هو المالك الجديد.'],

            // ----------------------------------------------------------------
            // US.19  سجل النشاط  (4 items)  ← NEW
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 19. سجل النشاط', 'item_key' => 'activity.view', 'description' => 'عرض سجل النشاط (جميع العمليات)', 'details' => 'اذهب إلى سجل النشاط. تظهر جميع العمليات: إضافة، تعديل، حذف، استعادة. مرتبة زمنياً.'],
            ['category' => 'لوحة المستخدم | 19. سجل النشاط', 'item_key' => 'activity.filter_type', 'description' => 'تصفية حسب نوع العملية (إضافة/تعديل/حذف/استعادة)', 'details' => 'فلتر: created فقط. فلتر: deleted فقط. يمكن الجمع.'],
            ['category' => 'لوحة المستخدم | 19. سجل النشاط', 'item_key' => 'activity.filter_date', 'description' => 'تصفية حسب نطاق تاريخي', 'details' => 'اختر من تاريخ - إلى تاريخ. تظهر العمليات في هذا النطاق.'],
            ['category' => 'لوحة المستخدم | 19. سجل النشاط', 'item_key' => 'activity.view_detail', 'description' => 'عرض تفاصيل العملية (البيانات القديمة والجديدة)', 'details' => 'انقر على عملية. تظهر التفاصيل: نوع الكائن، المعرف، التغييرات (old/new)، IP، الـ User-Agent.'],

            // ----------------------------------------------------------------
            // US.20  مهام الخلفية  (4 items)
            // ----------------------------------------------------------------
            ['category' => 'لوحة المستخدم | 20. مهام الخلفية', 'item_key' => 'queue.work', 'description' => 'queue:work يعالج المهام دون تأخير', 'details' => 'شغّل queue:work --tries=3. أضف مهمة بريد — تُعالج فوراً. تحقق من jobs/failed_jobs.'],
            ['category' => 'لوحة المستخدم | 20. مهام الخلفية', 'item_key' => 'queue.email_delivery', 'description' => 'إرسال البريد عبر queue', 'details' => 'قم بإجراء يتطلب بريداً (إعادة تعيين كلمة مرور). يصل البريد فوراً مع HTML صحيح.'],
            ['category' => 'لوحة المستخدم | 20. مهام الخلفية', 'item_key' => 'queue.invoice_creation', 'description' => 'إنشاء الفاتورة عبر queue', 'details' => 'أكمل دفعاً ناجحاً. تُنشأ الفاتورة عبر queue. تحتوي على الرقم، المبلغ، الضريبة، التاريخ.'],
            ['category' => 'لوحة المستخدم | 20. مهام الخلفية', 'item_key' => 'schedule.run', 'description' => 'schedule:run ← تجديد + إشعارات (7d/3d/1d)', 'details' => 'شغّل schedule:run. يتجدّد الاشتراكات. تُرسل إشعارات قبل 7/3/1 يوم من الانتهاء.'],
        ];
    }
}
