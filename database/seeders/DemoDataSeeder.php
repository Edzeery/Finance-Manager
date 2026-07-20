<?php

namespace Database\Seeders;

use App\Enums\DebtStatus;
use App\Enums\DebtType;
use App\Enums\GoalStatus;
use App\Models\Asset;
use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\Coupon;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\TaxRate;
use App\Models\User;
use App\Models\Workspace;
use App\Models\ZakatAsset;
use App\Models\ZakatRecord;
use App\Services\InvoiceNumberGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'demo@example.com')->exists()) {
            $this->command?->warn('Demo user already exists. Skipping DemoDataSeeder.');

            return;
        }

        DB::transaction(function () {
            $user = User::create([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'onboarding_completed_at' => now(),
                'plan_confirmed_at' => now(),
                'locale' => 'ar',
                'theme' => 'light',
                'currency' => 'DZD',
                'timezone' => 'Africa/Algiers',
            ]);

            $workspace = Workspace::create([
                'name' => "Demo User's Workspace",
                'slug' => 'demo-workspace-'.now()->timestamp,
                'type' => 'personal',
                'currency' => 'DZD',
                'timezone' => 'Africa/Algiers',
            ]);

            $workspace->users()->attach($user->id);

            $adminRole = Role::where('slug', 'workspace_admin')->first();
            if ($adminRole) {
                $user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $workspace->id]);
            }

            $user->update(['current_workspace_id' => $workspace->id]);
            config(['app.current_workspace' => $workspace->id]);

            $personalPlan = SubscriptionPlan::where('slug', 'personal')->first();
            $businessPlan = SubscriptionPlan::where('slug', 'business')->first();
            $demoPlan = $businessPlan ?? $personalPlan;
            $subscription = null;
            if ($demoPlan) {
                $subscription = $workspace->allSubscriptions()->create([
                    'subscription_plan_id' => $demoPlan->id,
                    'user_id' => $user->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'billing_period' => 'monthly',
                ]);
            }

            // ── Invoices ──────────────────────────────────────────────
            $generator = app(InvoiceNumberGenerator::class);
            $invoiceData = [
                ['status' => 'paid', 'subtotal' => 0, 'discount' => 0, 'tax' => 0, 'total' => 0, 'currency' => 'DZD', 'period_start' => now()->subMonths(2)->startOfMonth(), 'period_end' => now()->subMonths(2)->endOfMonth(), 'paid_at' => now()->subMonths(2)->addDays(1), 'due_at' => now()->subMonths(2)->addDays(7)],
                ['status' => 'cancelled', 'subtotal' => 0, 'discount' => 0, 'tax' => 0, 'total' => 0, 'currency' => 'DZD', 'period_start' => now()->subMonth()->startOfMonth(), 'period_end' => now()->subMonth()->endOfMonth(), 'paid_at' => null, 'due_at' => now()->subMonth()->addDays(7)],
                ['status' => 'draft', 'subtotal' => 1500, 'discount' => 0, 'tax' => 285, 'total' => 1785, 'currency' => 'DZD', 'period_start' => now()->startOfMonth(), 'period_end' => now()->endOfMonth(), 'paid_at' => null, 'due_at' => now()->addDays(7)],
                ['status' => 'overdue', 'subtotal' => 1000, 'discount' => 100, 'tax' => 171, 'total' => 1071, 'currency' => 'DZD', 'period_start' => now()->subMonths(3)->startOfMonth(), 'period_end' => now()->subMonths(3)->endOfMonth(), 'paid_at' => null, 'due_at' => now()->subMonths(3)->addDays(7)],
            ];
            foreach ($invoiceData as $data) {
                Invoice::create(array_merge($data, [
                    'workspace_id' => $workspace->id,
                    'subscription_id' => $subscription?->id,
                    'user_id' => $user->id,
                    'number' => $generator->generate(),
                    'billing_period' => 'monthly',
                ]));
            }

            $incomeCats = IncomeCategory::whereNull('user_id')->pluck('id', 'name_en');
            $expenseCats = ExpenseCategory::whereNull('user_id')->pluck('id', 'name_en');

            // ── Coupons ───────────────────────────────────────────────
            Coupon::create(['code' => 'WELCOME20', 'type' => 'percentage', 'value' => 20, 'min_amount' => 50, 'max_uses' => 100, 'used_count' => 0, 'starts_at' => now(), 'expires_at' => now()->addYears(1), 'is_active' => true]);
            Coupon::create(['code' => 'SAVE10', 'type' => 'fixed', 'value' => 10, 'max_uses' => 50, 'used_count' => 5, 'starts_at' => now(), 'expires_at' => now()->addMonths(6), 'is_active' => true]);
            Coupon::create(['code' => 'EXPIRED50', 'type' => 'percentage', 'value' => 50, 'max_uses' => 10, 'used_count' => 10, 'starts_at' => now()->subYears(1), 'expires_at' => now()->subMonth(), 'is_active' => false]);

            // ── Tax Rates ─────────────────────────────────────────────
            TaxRate::create(['name' => 'TVA 19%', 'slug' => 'tva-19', 'rate' => 19, 'type' => 'percentage', 'country' => 'DZ', 'region' => null, 'is_active' => true]);
            TaxRate::create(['name' => 'TVA 9%', 'slug' => 'tva-9', 'rate' => 9, 'type' => 'percentage', 'country' => 'DZ', 'region' => null, 'is_active' => true]);
            TaxRate::create(['name' => 'VAT 20%', 'slug' => 'vat-20', 'rate' => 20, 'type' => 'percentage', 'country' => 'FR', 'region' => null, 'is_active' => true]);

            // ── Incomes ───────────────────────────────────────────────
            foreach (range(1, 3) as $monthOffset) {
                $date = now()->subMonths($monthOffset);
                Income::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'category_id' => $incomeCats['Salary'], 'amount' => 45000, 'description' => 'الراتب الشهري', 'date' => $date->copy()->startOfMonth()->addDays(28), 'is_recurring' => true, 'recurring_frequency' => 'monthly']);
                Income::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'category_id' => $incomeCats['Freelance'], 'amount' => rand(5000, 15000), 'description' => 'مشروع فريلانس', 'date' => $date->copy()->startOfMonth()->addDays(15), 'is_recurring' => false]);
            }
            Income::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'category_id' => $incomeCats['Salary'], 'amount' => 45000, 'description' => 'الراتب الشهري', 'date' => now()->startOfMonth()->addDays(28), 'is_recurring' => true, 'recurring_frequency' => 'monthly']);
            Income::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'category_id' => $incomeCats['Rental Income'], 'amount' => 12000, 'description' => 'إيجار محل تجاري', 'date' => now()->startOfMonth()->addDays(5), 'is_recurring' => true, 'recurring_frequency' => 'monthly']);

            // ── Expenses ──────────────────────────────────────────────
            $expenseEntries = [
                ['category' => 'Housing', 'amount' => 8000, 'description' => 'إيجار الشقة', 'date' => now()->startOfMonth()->addDays(1)],
                ['category' => 'Food', 'amount' => 15000, 'description' => 'مشتريات شهرية', 'date' => now()->startOfMonth()->addDays(10)],
                ['category' => 'Transport', 'amount' => 3000, 'description' => 'بنزين', 'date' => now()->startOfMonth()->addDays(7)],
                ['category' => 'Utilities', 'amount' => 2500, 'description' => 'فاتورة كهرباء وماء', 'date' => now()->startOfMonth()->addDays(15)],
                ['category' => 'Telecom', 'amount' => 1500, 'description' => 'فاتورة الهاتف والإنترنت', 'date' => now()->startOfMonth()->addDays(5)],
                ['category' => 'Healthcare', 'amount' => 2000, 'description' => 'زيارة طبية', 'date' => now()->startOfMonth()->subDays(10)],
                ['category' => 'Education', 'amount' => 5000, 'description' => 'دورة تدريبية', 'date' => now()->startOfMonth()->subDays(20)],
                ['category' => 'Entertainment', 'amount' => 2000, 'description' => 'خروج وعائلة', 'date' => now()->startOfMonth()->subDays(15)],
                ['category' => 'Clothing', 'amount' => 3500, 'description' => 'ملابس', 'date' => now()->startOfMonth()->subDays(5)],
                ['category' => 'Charity', 'amount' => 1000, 'description' => 'صدقة شهرية', 'date' => now()->startOfMonth()->addDays(20)],
                ['category' => 'Subscriptions', 'amount' => 800, 'description' => 'اشتراك Netflix', 'date' => now()->startOfMonth()->addDays(3)],
                ['category' => 'Repairs', 'amount' => 1500, 'description' => 'إصلاح مشغل الأقراص', 'date' => now()->startOfMonth()->subDays(8)],
            ];
            foreach (range(1, 2) as $monthOffset) {
                $d = now()->subMonths($monthOffset);
                $expenseEntries[] = ['category' => 'Housing', 'amount' => 8000, 'description' => 'إيجار الشقة', 'date' => $d->copy()->startOfMonth()->addDays(1)];
                $expenseEntries[] = ['category' => 'Food', 'amount' => 14000, 'description' => 'مشتريات شهرية', 'date' => $d->copy()->startOfMonth()->addDays(10)];
                $expenseEntries[] = ['category' => 'Utilities', 'amount' => 2200, 'description' => 'فواتير', 'date' => $d->copy()->startOfMonth()->addDays(15)];
                $expenseEntries[] = ['category' => 'Telecom', 'amount' => 1500, 'description' => 'اتصالات', 'date' => $d->copy()->startOfMonth()->addDays(5)];
                $expenseEntries[] = ['category' => 'Transport', 'amount' => 3500, 'description' => 'وقود ومواصلات', 'date' => $d->copy()->startOfMonth()->addDays(8)];
                $expenseEntries[] = ['category' => 'Food', 'amount' => 3000, 'description' => 'مطاعم', 'date' => $d->copy()->startOfMonth()->addDays(20)];
                $expenseEntries[] = ['category' => 'Entertainment', 'amount' => 2500, 'description' => 'تسلية', 'date' => $d->copy()->startOfMonth()->addDays(25)];
                $expenseEntries[] = ['category' => 'Gifts', 'amount' => 2000, 'description' => 'هدايا', 'date' => $d->copy()->startOfMonth()->addDays(18)];
            }
            foreach ($expenseEntries as $e) {
                Expense::create([
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'category_id' => $expenseCats[$e['category']],
                    'amount' => $e['amount'],
                    'description' => $e['description'],
                    'date' => $e['date'],
                ]);
            }

            // ── Budgets ───────────────────────────────────────────────
            $budget1 = Budget::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'name_ar' => 'الميزانية الشهرية',
                'name_fr' => 'Budget mensuel',
                'name_en' => 'Monthly Budget',
                'type' => 'monthly',
                'total_amount' => 50000,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'is_active' => true,
            ]);
            $catBudgetMap = ['Housing' => 10000, 'Food' => 18000, 'Transport' => 5000, 'Utilities' => 5000, 'Telecom' => 2000, 'Healthcare' => 3000, 'Entertainment' => 3000, 'Clothing' => 4000];
            foreach ($catBudgetMap as $catName => $amount) {
                BudgetCategory::create(['budget_id' => $budget1->id, 'workspace_id' => $workspace->id, 'expense_category_id' => $expenseCats[$catName], 'allocated_amount' => $amount, 'spent_amount' => round($amount * rand(30, 95) / 100, 2)]);
            }

            Budget::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'name_ar' => 'ميزانية الادخار',
                'name_fr' => "Budget d'épargne",
                'name_en' => 'Savings Budget',
                'type' => 'yearly',
                'total_amount' => 240000,
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
                'is_active' => true,
            ]);

            // ── Financial Goals ───────────────────────────────────────
            FinancialGoal::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'name_ar' => 'شراء سيارة', 'name_fr' => 'Achat voiture', 'name_en' => 'Buy a Car', 'target_amount' => 2000000, 'current_amount' => 350000, 'target_date' => now()->addYear(), 'status' => GoalStatus::Active->value, 'icon' => 'bi-car-front', 'color' => '#3B82F6']);
            FinancialGoal::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'name_ar' => 'صندوق الطوارئ', 'name_fr' => "Fonds d'urgence", 'name_en' => 'Emergency Fund', 'target_amount' => 300000, 'current_amount' => 120000, 'target_date' => now()->addMonths(6), 'status' => GoalStatus::Active->value, 'icon' => 'bi-shield-check', 'color' => '#22C55E']);
            FinancialGoal::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'name_ar' => 'العمرة', 'name_fr' => 'Omra', 'name_en' => 'Umrah Trip', 'target_amount' => 500000, 'current_amount' => 500000, 'target_date' => now()->addMonth(), 'status' => GoalStatus::Completed->value, 'icon' => 'bi-building', 'color' => '#8B5CF6', 'completed_at' => now()->subDays(5)]);
            FinancialGoal::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'name_ar' => 'جهاز كمبيوتر', 'name_fr' => 'Ordinateur', 'name_en' => 'New Laptop', 'target_amount' => 150000, 'current_amount' => 150000, 'target_date' => now()->subMonth(), 'status' => GoalStatus::Completed->value, 'icon' => 'bi-laptop', 'color' => '#F59E0B', 'completed_at' => now()->subDays(30)]);

            // ── Debts ─────────────────────────────────────────────────
            $debtOwed1 = Debt::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => DebtType::Owed->value, 'counterparty_name' => 'سامي', 'total_amount' => 50000, 'paid_amount' => 15000, 'due_date' => now()->addMonths(2), 'status' => DebtStatus::Active->value, 'description' => 'قرض شخصي']);
            DebtPayment::create(['debt_id' => $debtOwed1->id, 'workspace_id' => $workspace->id, 'amount' => 5000, 'payment_date' => now()->subMonths(2), 'notes' => 'دفعة أولى']);
            DebtPayment::create(['debt_id' => $debtOwed1->id, 'workspace_id' => $workspace->id, 'amount' => 10000, 'payment_date' => now()->subMonth(), 'notes' => 'دفعة ثانية']);

            $debtOwing1 = Debt::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => DebtType::Owing->value, 'counterparty_name' => 'بنك الفلاحية', 'total_amount' => 1500000, 'paid_amount' => 300000, 'due_date' => now()->addYears(3), 'status' => DebtStatus::Active->value, 'description' => 'قرض سكني — 5 سنوات']);
            DebtPayment::create(['debt_id' => $debtOwing1->id, 'workspace_id' => $workspace->id, 'amount' => 100000, 'payment_date' => now()->subMonths(3), 'notes' => 'أول قسط']);
            DebtPayment::create(['debt_id' => $debtOwing1->id, 'workspace_id' => $workspace->id, 'amount' => 100000, 'payment_date' => now()->subMonths(2), 'notes' => 'القسط الثاني']);
            DebtPayment::create(['debt_id' => $debtOwing1->id, 'workspace_id' => $workspace->id, 'amount' => 100000, 'payment_date' => now()->subMonth(), 'notes' => 'القسط الثالث']);

            $debtOwed2 = Debt::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => DebtType::Owed->value, 'counterparty_name' => 'أحمد', 'total_amount' => 10000, 'paid_amount' => 10000, 'due_date' => now()->subMonth(), 'status' => DebtStatus::Paid->value, 'description' => 'قرض — تم التسديد بالكامل']);
            DebtPayment::create(['debt_id' => $debtOwed2->id, 'workspace_id' => $workspace->id, 'amount' => 10000, 'payment_date' => now()->subMonth(), 'notes' => 'تسديد كامل']);

            $debtOwing2 = Debt::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => DebtType::Owing->value, 'counterparty_name' => 'karim', 'total_amount' => 25000, 'paid_amount' => 10000, 'due_date' => now()->addMonths(3), 'status' => DebtStatus::Partial->value, 'description' => 'قرض شراء جهاز']);
            DebtPayment::create(['debt_id' => $debtOwing2->id, 'workspace_id' => $workspace->id, 'amount' => 10000, 'payment_date' => now()->subDays(15), 'notes' => 'دفعة جزئية']);

            // ── Assets ────────────────────────────────────────────────
            // Bank accounts
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'bank_account', 'name' => 'حساب جاري CPA', 'total_value' => 250000, 'currency' => 'DZD', 'is_liquid' => true, 'is_zakatable' => true, 'bank_name' => 'CPA', 'account_number' => '00799999001234567890']);
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'bank_account', 'name' => 'حساب توفير BNA', 'total_value' => 800000, 'currency' => 'DZD', 'is_liquid' => true, 'is_zakatable' => true, 'bank_name' => 'BNA', 'account_number' => '00799999009876543210']);

            // CCP
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'ccp', 'name' => 'حساب البريد CCP', 'total_value' => 150000, 'currency' => 'DZD', 'is_liquid' => true, 'is_zakatable' => true, 'account_number' => '1234567890123456']);

            // Cash
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'cash', 'name' => 'خزينة نقدية', 'total_value' => 50000, 'currency' => 'DZD', 'is_liquid' => true, 'is_zakatable' => true]);

            // Gold — 21K (عيار 21)
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'gold', 'name' => 'خاتم ذهب عيار 21', 'karat' => 21, 'weight_grams' => 18.5, 'unit_price' => 8500, 'total_value' => 157250, 'currency' => 'DZD', 'is_liquid' => false, 'is_zakatable' => true]);

            // Gold — 18K (ساعة ذهب)
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'gold', 'name' => 'ساعة ذهب عيار 18', 'karat' => 18, 'weight_grams' => 45, 'unit_price' => 6375, 'total_value' => 286875, 'currency' => 'DZD', 'is_liquid' => false, 'is_zakatable' => true]);

            // Gold — 22K (قلادة)
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'gold', 'name' => 'قلادة ذهب عيار 22', 'karat' => 22, 'weight_grams' => 30, 'unit_price' => 9350, 'total_value' => 280500, 'currency' => 'DZD', 'is_liquid' => false, 'is_zakatable' => true]);

            // Silver
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'silver', 'name' => 'سبائك فضة', 'weight_grams' => 200, 'unit_price' => 120, 'total_value' => 24000, 'currency' => 'DZD', 'is_liquid' => false, 'is_zakatable' => true]);

            // Stocks
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'stocks', 'name' => 'استثمارات في البورصة', 'total_value' => 350000, 'currency' => 'DZD', 'is_liquid' => false, 'is_zakatable' => true, 'notes' => 'أسهم في سوق الجزائر']);

            // Crypto
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'crypto', 'name' => 'عملات رقمية USDT', 'total_value' => 200000, 'currency' => 'DZD', 'is_liquid' => true, 'is_zakatable' => true, 'notes' => 'محفظة RedotPay']);

            // Business goods
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'business_goods', 'name' => 'بضاعة متجر إلكتروني', 'total_value' => 150000, 'currency' => 'DZD', 'is_liquid' => false, 'is_zakatable' => true, 'notes' => 'بضاعة جاهزة للبيع']);

            // Real estate (NOT zakatable)
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'real_estate', 'name' => 'شقة للإيجار', 'total_value' => 5000000, 'currency' => 'DZD', 'is_liquid' => false, 'is_zakatable' => false, 'notes' => 'شقة سكنية مسكونة — مستثناة من الزكاة']);

            // Expected receivables (NOT zakatable — ديون مرجوة السداد)
            Asset::create(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'type' => 'expected_receivables', 'name' => 'راتب متأخر — شركة X', 'total_value' => 45000, 'currency' => 'DZD', 'is_liquid' => false, 'is_zakatable' => false, 'notes' => 'راتب شهر يناير لم يُدفع بعد']);

            // ── Zakat Record ──────────────────────────────────────────
            // Gold total: 157250 + 286875 + 280500 = 724625
            // Silver: 24000
            // Cash: 50000
            // Bank: 250000 + 800000 = 1050000
            // CCP: 150000
            // Stocks: 350000
            // Crypto: 200000
            // Business goods: 150000
            // Total zakatable: 2748625
            // Total debts (owing): 1215000 (1200000 + 15000)
            // Net zakatable: 2748625 - 1215000 = 1533625
            // Zakat: 1533625 * 2.5% = 38340.63
            $goldPricePerGram = 8500;
            $silverPricePerGram = 120;
            $nisabGold = $goldPricePerGram * 85;   // 722500
            $nisabSilver = $silverPricePerGram * 595; // 71400
            $goldWeight = 18.5 + 45 + 30; // 93.5g
            $silverWeight = 200;
            $goldValue = 724625;
            $silverValue = 24000;
            $cashValue = 50000;
            $bankValue = 1050000;
            $ccpValue = 150000;
            $stocksValue = 350000;
            $cryptoValue = 200000;
            $businessGoodsValue = 150000;
            $totalWealth = $goldValue + $silverValue + $cashValue + $bankValue + $ccpValue + $stocksValue + $cryptoValue + $businessGoodsValue;
            $totalDebts = 1215000;
            $totalZakatable = $totalWealth;
            $netZakatable = $totalZakatable - $totalDebts;
            $zakatAmount = round($netZakatable * 0.025, 2);

            $record = ZakatRecord::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'calculation_date' => now(),
                'hijri_year' => '1447',
                'nisab_gold' => $nisabGold,
                'nisab_silver' => $nisabSilver,
                'gold_price_per_gram' => $goldPricePerGram,
                'silver_price_per_gram' => $silverPricePerGram,
                'gold_weight' => $goldWeight,
                'silver_weight' => $silverWeight,
                'gold_value' => $goldValue,
                'silver_value' => $silverValue,
                'cash_value' => $cashValue,
                'bank_value' => $bankValue,
                'ccp_value' => $ccpValue,
                'business_goods_value' => $businessGoodsValue,
                'stocks_value' => $stocksValue,
                'crypto_value' => $cryptoValue,
                'real_estate_value' => 0,
                'expected_receivables' => 45000,
                'total_wealth' => $totalWealth,
                'total_zakatable' => $totalZakatable,
                'net_zakatable' => $netZakatable,
                'total_debts' => $totalDebts,
                'exceeds_nisab' => true,
                'zakat_amount' => $zakatAmount,
                'cash_zakat' => round($cashValue * 0.025, 2),
                'gold_zakat' => round($goldValue * 0.025, 2),
                'silver_zakat' => round($silverValue * 0.025, 2),
                'business_zakat' => round($businessGoodsValue * 0.025, 2),
                'investments_zakat' => round(($stocksValue + $cryptoValue) * 0.025, 2),
            ]);

            // Zakat assets breakdown
            $zakatAssets = [
                ['type' => 'cash', 'name' => 'خزينة نقدية', 'value' => $cashValue, 'is_zakatable' => true, 'zakatable_value' => $cashValue],
                ['type' => 'bank_account', 'name' => 'حساب جاري CPA', 'value' => 250000, 'is_zakatable' => true, 'zakatable_value' => 250000],
                ['type' => 'bank_account', 'name' => 'حساب توفير BNA', 'value' => 800000, 'is_zakatable' => true, 'zakatable_value' => 800000],
                ['type' => 'ccp', 'name' => 'حساب البريد CCP', 'value' => $ccpValue, 'is_zakatable' => true, 'zakatable_value' => $ccpValue],
                ['type' => 'gold', 'name' => 'خاتم ذهب عيار 21 (18.5g)', 'value' => 157250, 'is_zakatable' => true, 'zakatable_value' => 157250],
                ['type' => 'gold', 'name' => 'ساعة ذهب عيار 18 (45g)', 'value' => 286875, 'is_zakatable' => true, 'zakatable_value' => 286875],
                ['type' => 'gold', 'name' => 'قلادة ذهب عيار 22 (30g)', 'value' => 280500, 'is_zakatable' => true, 'zakatable_value' => 280500],
                ['type' => 'silver', 'name' => 'سبائك فضة (200g)', 'value' => $silverValue, 'is_zakatable' => true, 'zakatable_value' => $silverValue],
                ['type' => 'stocks', 'name' => 'استثمارات في البورصة', 'value' => $stocksValue, 'is_zakatable' => true, 'zakatable_value' => $stocksValue],
                ['type' => 'crypto', 'name' => 'عملات رقمية USDT', 'value' => $cryptoValue, 'is_zakatable' => true, 'zakatable_value' => $cryptoValue],
                ['type' => 'business_goods', 'name' => 'بضاعة متجر إلكتروني', 'value' => $businessGoodsValue, 'is_zakatable' => true, 'zakatable_value' => $businessGoodsValue],
                ['type' => 'real_estate', 'name' => 'شقة للإيجار', 'value' => 5000000, 'is_zakatable' => false, 'zakatable_value' => 0, 'notes' => 'مستثناة من الزكاة'],
                ['type' => 'expected_receivables', 'name' => 'راتب متأخر — شركة X', 'value' => 45000, 'is_zakatable' => false, 'zakatable_value' => 0, 'notes' => 'ديون مرجوة السداد'],
            ];
            foreach ($zakatAssets as $za) {
                ZakatAsset::create(array_merge($za, [
                    'zakat_record_id' => $record->id,
                    'workspace_id' => $workspace->id,
                ]));
            }

            $this->command?->info('Demo data created successfully!');
            $this->command?->info('Email: demo@example.com / Password: password');
        });
    }
}
