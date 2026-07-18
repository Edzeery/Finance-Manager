<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $incomeCategories = [
            ['name_ar' => 'الراتب', 'name_fr' => 'Salaire', 'name_en' => 'Salary', 'icon' => 'bi-briefcase', 'color' => '#22C55E', 'type' => 'fixed', 'sort_order' => 1],
            ['name_ar' => 'عمل حر', 'name_fr' => 'Freelance', 'name_en' => 'Freelance', 'icon' => 'bi-laptop', 'color' => '#3B82F6', 'type' => 'variable', 'sort_order' => 2],
            ['name_ar' => 'استثمارات', 'name_fr' => 'Investissements', 'name_en' => 'Investments', 'icon' => 'bi-graph-up', 'color' => '#8B5CF6', 'type' => 'variable', 'sort_order' => 3],
            ['name_ar' => 'هدايا', 'name_fr' => 'Cadeaux', 'name_en' => 'Gifts', 'icon' => 'bi-gift', 'color' => '#F59E0B', 'type' => 'variable', 'sort_order' => 4],
            ['name_ar' => 'إيجار عقار', 'name_fr' => 'Revenus locatifs', 'name_en' => 'Rental Income', 'icon' => 'bi-house', 'color' => '#06B6D4', 'type' => 'recurring', 'sort_order' => 5],
            ['name_ar' => 'أخرى', 'name_fr' => 'Autres', 'name_en' => 'Other', 'icon' => 'bi-three-dots', 'color' => '#64748B', 'type' => 'variable', 'sort_order' => 6],
        ];

        $expenseCategories = [
            ['name_ar' => 'السكن', 'name_fr' => 'Logement', 'name_en' => 'Housing', 'icon' => 'bi-house-door', 'color' => '#EF4444', 'type' => 'fixed', 'sort_order' => 1],
            ['name_ar' => 'الطعام', 'name_fr' => 'Alimentation', 'name_en' => 'Food', 'icon' => 'bi-basket', 'color' => '#F59E0B', 'type' => 'variable', 'sort_order' => 2],
            ['name_ar' => 'المواصلات', 'name_fr' => 'Transport', 'name_en' => 'Transport', 'icon' => 'bi-car-front-fill', 'color' => '#3B82F6', 'type' => 'variable', 'sort_order' => 3],
            ['name_ar' => 'الصحة', 'name_fr' => 'Santé', 'name_en' => 'Healthcare', 'icon' => 'bi-heart-pulse', 'color' => '#EC4899', 'type' => 'variable', 'sort_order' => 4],
            ['name_ar' => 'التعليم', 'name_fr' => 'Éducation', 'name_en' => 'Education', 'icon' => 'bi-book', 'color' => '#8B5CF6', 'type' => 'variable', 'sort_order' => 5],
            ['name_ar' => 'الترفيه', 'name_fr' => 'Divertissement', 'name_en' => 'Entertainment', 'icon' => 'bi-controller', 'color' => '#06B6D4', 'type' => 'variable', 'sort_order' => 6],
            ['name_ar' => 'الفواتير', 'name_fr' => 'Factures', 'name_en' => 'Utilities', 'icon' => 'bi-lightning', 'color' => '#F97316', 'type' => 'fixed', 'sort_order' => 7],
            ['name_ar' => 'الاتصالات', 'name_fr' => 'Télécom', 'name_en' => 'Telecom', 'icon' => 'bi-phone', 'color' => '#14B8A6', 'type' => 'fixed', 'sort_order' => 8],
            ['name_ar' => 'ملابس', 'name_fr' => 'Vêtements', 'name_en' => 'Clothing', 'icon' => 'bi-handbag', 'color' => '#E11D48', 'type' => 'variable', 'sort_order' => 9],
            ['name_ar' => 'الاشتراكات', 'name_fr' => 'Abonnements', 'name_en' => 'Subscriptions', 'icon' => 'bi-arrow-repeat', 'color' => '#7C3AED', 'type' => 'recurring', 'sort_order' => 10],
            ['name_ar' => 'الصدقات والعبادات', 'name_fr' => 'Charité', 'name_en' => 'Charity', 'icon' => 'bi-heart', 'color' => '#16A34A', 'type' => 'variable', 'sort_order' => 11],
            ['name_ar' => 'الهدايا', 'name_fr' => 'Cadeaux', 'name_en' => 'Gifts', 'icon' => 'bi-gift', 'color' => '#CA8A04', 'type' => 'variable', 'sort_order' => 12],
            ['name_ar' => 'الصيانة والإصلاحات', 'name_fr' => 'Réparations', 'name_en' => 'Repairs', 'icon' => 'bi-wrench', 'color' => '#9333EA', 'type' => 'variable', 'sort_order' => 13],
            ['name_ar' => 'التأمين', 'name_fr' => 'Assurance', 'name_en' => 'Insurance', 'icon' => 'bi-shield-check', 'color' => '#0891B2', 'type' => 'fixed', 'sort_order' => 14],
            ['name_ar' => 'الضرائب والرسوم', 'name_fr' => 'Taxes', 'name_en' => 'Taxes', 'icon' => 'bi-file-earmark-text', 'color' => '#DC2626', 'type' => 'fixed', 'sort_order' => 15],
            ['name_ar' => 'سداد القروض', 'name_fr' => 'Prêts', 'name_en' => 'Loan Repayment', 'icon' => 'bi-bank', 'color' => '#B91C1C', 'type' => 'fixed', 'sort_order' => 16],
            ['name_ar' => 'أخرى', 'name_fr' => 'Autres', 'name_en' => 'Other', 'icon' => 'bi-three-dots', 'color' => '#64748B', 'type' => 'variable', 'sort_order' => 17],
        ];

        foreach ($incomeCategories as $cat) {
            DB::table('income_categories')->insert(array_merge($cat, [
                'user_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        foreach ($expenseCategories as $cat) {
            DB::table('expense_categories')->insert(array_merge($cat, [
                'user_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
