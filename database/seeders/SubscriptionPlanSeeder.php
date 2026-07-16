<?php

namespace Database\Seeders;

use App\Models\PlanFeature;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            'free' => [
                'name_en' => 'Free',
                'name_ar' => 'مجاني',
                'name_fr' => 'Gratuit',
                'description' => 'Free forever — basic finance management for individuals.',
                'description_ar' => 'مجاني للأبد — إدارة مالية أساسية للأفراد.',
                'description_fr' => 'Gratuit à vie — gestion financière de base pour les particuliers.',
                'sort_order' => 0,
                'is_free' => true,
                'trial_days' => null,
                'is_public' => true,
                'button_text' => 'Get Started Free',
                'button_text_ar' => 'ابدأ مجاناً',
                'button_text_fr' => 'Commencez gratuitement',
                'plan_features' => [
                    ['slug' => 'workspaces', 'value' => '1'],
                    ['slug' => 'users', 'value' => '1'],
                    ['slug' => 'reports', 'value' => 'basic'],
                    ['slug' => 'support', 'value' => 'community'],
                    ['slug' => 'transactions_per_month', 'value' => '100'],
                ],
                'prices' => [
                    ['billing_period' => 'monthly', 'currency' => 'USD', 'price' => 0],
                ],
            ],

            'personal' => [
                'name_en' => 'Personal',
                'name_ar' => 'شخصي',
                'name_fr' => 'Personnel',
                'description' => 'Start with a free 30-day trial — then unlock premium finance management for individuals.',
                'description_ar' => 'ابدأ بفترة تجريبية مجانية لمدة 30 يوماً — ثم احصل على إدارة مالية متميزة للأفراد.',
                'description_fr' => 'Commencez par un essai gratuit de 30 jours — puis débloquez une gestion financière premium pour les particuliers.',
                'sort_order' => 1,
                'is_free' => false,
                'trial_days' => 30,
                'is_public' => true,
                'yearly_discount_percent' => 17,
                'button_text' => 'Start Free Trial',
                'button_text_ar' => 'ابدأ التجربة المجانية',
                'button_text_fr' => 'Commencez l\'essai gratuit',
                'plan_features' => [
                    ['slug' => 'workspaces', 'value' => '1'],
                    ['slug' => 'users', 'value' => '1'],
                    ['slug' => 'reports', 'value' => 'basic'],
                    ['slug' => 'support', 'value' => 'community'],
                    ['slug' => 'transactions_per_month', 'value' => '500'],
                ],
                'prices' => [
                    ['billing_period' => 'monthly', 'currency' => 'USD', 'price' => 3.99],
                    ['billing_period' => 'yearly', 'currency' => 'USD', 'price' => 39.99],
                ],
            ],

            'business' => [
                'name_en' => 'Business',
                'name_ar' => 'أعمال',
                'name_fr' => 'Business',
                'description' => 'For small businesses and teams needing multiple workspaces.',
                'description_ar' => 'للشركات الصغيرة والفرق التي تحتاج إلى مساحات عمل متعددة.',
                'description_fr' => 'Pour les petites entreprises et les équipes ayant besoin de plusieurs espaces de travail.',
                'sort_order' => 2,
                'is_free' => false,
                'trial_days' => null,
                'is_public' => true,
                'yearly_discount_percent' => 17,
                'button_text' => 'Subscribe',
                'button_text_ar' => 'اشترك',
                'button_text_fr' => 'S\'abonner',
                'plan_features' => [
                    ['slug' => 'workspaces', 'value' => '2'],
                    ['slug' => 'users', 'value' => '10'],
                    ['slug' => 'reports', 'value' => 'advanced'],
                    ['slug' => 'support', 'value' => 'email'],
                    ['slug' => 'collaboration', 'value' => null],
                    ['slug' => 'team_management', 'value' => null],
                    ['slug' => 'roles_permissions', 'value' => null],
                    ['slug' => 'export', 'value' => null],
                    ['slug' => 'api_access', 'value' => null],
                    ['slug' => 'budget', 'value' => null],
                    ['slug' => 'goals', 'value' => null],
                    ['slug' => 'debt', 'value' => null],
                    ['slug' => 'zakat', 'value' => null],
                    ['slug' => 'transactions_per_month', 'value' => '10000'],
                    ['slug' => 'api_requests_per_minute', 'value' => '30'],
                    ['slug' => 'api_requests_per_hour', 'value' => '500'],
                    ['slug' => 'api_requests_per_day', 'value' => '5000'],
                ],
                'prices' => [
                    ['billing_period' => 'monthly', 'currency' => 'USD', 'price' => 9],
                    ['billing_period' => 'yearly', 'currency' => 'USD', 'price' => 89.64],
                ],
            ],

            'professional' => [
                'name_en' => 'Professional',
                'name_ar' => 'احترافي',
                'name_fr' => 'Professionnel',
                'description' => 'For growing organizations with advanced management needs.',
                'description_ar' => 'للمؤسسات المتنامية ذات احتياجات الإدارة المتقدمة.',
                'description_fr' => 'Pour les organisations en croissance ayant des besoins de gestion avancés.',
                'sort_order' => 3,
                'is_free' => false,
                'trial_days' => null,
                'is_public' => true,
                'yearly_discount_percent' => 17,
                'button_text' => 'Subscribe',
                'button_text_ar' => 'اشترك',
                'button_text_fr' => 'S\'abonner',
                'plan_features' => [
                    ['slug' => 'workspaces', 'value' => '3'],
                    ['slug' => 'users', 'value' => '50'],
                    ['slug' => 'reports', 'value' => 'advanced'],
                    ['slug' => 'support', 'value' => 'priority'],
                    ['slug' => 'collaboration', 'value' => null],
                    ['slug' => 'team_management', 'value' => null],
                    ['slug' => 'roles_permissions', 'value' => null],
                    ['slug' => 'export', 'value' => null],
                    ['slug' => 'api_access', 'value' => null],
                    ['slug' => 'audit_logs', 'value' => null],
                    ['slug' => 'activity_logs', 'value' => null],
                    ['slug' => 'deputy_admin', 'value' => null],
                    ['slug' => 'budget', 'value' => null],
                    ['slug' => 'goals', 'value' => null],
                    ['slug' => 'debt', 'value' => null],
                    ['slug' => 'zakat', 'value' => null],
                    ['slug' => 'transactions_per_month', 'value' => '50000'],
                    ['slug' => 'api_requests_per_minute', 'value' => '60'],
                    ['slug' => 'api_requests_per_hour', 'value' => '1000'],
                    ['slug' => 'api_requests_per_day', 'value' => '10000'],
                ],
                'prices' => [
                    ['billing_period' => 'monthly', 'currency' => 'USD', 'price' => 19],
                    ['billing_period' => 'yearly', 'currency' => 'USD', 'price' => 189.24],
                ],
            ],

            'enterprise' => [
                'name_en' => 'Enterprise',
                'name_ar' => 'مؤسسات',
                'name_fr' => 'Enterprise',
                'description' => 'Custom workspaces, custom users, custom pricing — tailored for your organization.',
                'description_ar' => 'مساحات عمل مخصصة، مستخدمون مخصصون، تسعير مخصص — مصمم خصيصاً لمؤسستك.',
                'description_fr' => 'Espaces de travail personnalisés, utilisateurs personnalisés, tarification personnalisée — adapté à votre organisation.',
                'sort_order' => 4,
                'is_free' => false,
                'trial_days' => null,
                'is_public' => false,
                'button_text' => 'Contact Us',
                'button_text_ar' => 'اتصل بنا',
                'button_text_fr' => 'Contactez-nous',
                'button_link' => '/contact',
                'plan_features' => [
                    ['slug' => 'workspaces', 'value' => 'custom'],
                    ['slug' => 'users', 'value' => 'custom'],
                    ['slug' => 'reports', 'value' => 'advanced'],
                    ['slug' => 'support', 'value' => 'dedicated'],
                    ['slug' => 'collaboration', 'value' => null],
                    ['slug' => 'team_management', 'value' => null],
                    ['slug' => 'roles_permissions', 'value' => null],
                    ['slug' => 'export', 'value' => null],
                    ['slug' => 'api_access', 'value' => null],
                    ['slug' => 'audit_logs', 'value' => null],
                    ['slug' => 'activity_logs', 'value' => null],
                    ['slug' => 'deputy_admin', 'value' => null],
                    ['slug' => 'budget', 'value' => null],
                    ['slug' => 'goals', 'value' => null],
                    ['slug' => 'debt', 'value' => null],
                    ['slug' => 'zakat', 'value' => null],
                    ['slug' => 'white_label', 'value' => null],
                    ['slug' => 'custom_integrations', 'value' => null],
                    ['slug' => 'sla', 'value' => null],
                    ['slug' => 'transactions_per_month', 'value' => '999999'],
                    ['slug' => 'api_requests_per_minute', 'value' => '200'],
                    ['slug' => 'api_requests_per_hour', 'value' => '5000'],
                    ['slug' => 'api_requests_per_day', 'value' => '50000'],
                ],
                'prices' => [],
            ],
        ];

        $features = $this->getAllFeatures();

        foreach ($plans as $slug => $data) {
            $planFeaturesSlugs = $data['plan_features'];
            $prices = $data['prices'];

            unset($data['plan_features'], $data['prices']);

            $plan = SubscriptionPlan::updateOrCreate(
                ['slug' => $slug],
                $data
            );

            $this->syncPlanFeatures($plan, $features, $planFeaturesSlugs);

            $this->syncPlanPrices($plan, $prices);
        }
    }

    private function getAllFeatures(): array
    {
        return [
            [
                'slug' => 'workspaces',
                'name_en' => 'Workspaces',
                'name_ar' => 'مساحات العمل',
                'name_fr' => 'Espaces de travail',
                'type' => 'number',
                'icon' => 'fa-solid fa-layer-group',
                'sort_order' => 0,
                'is_core' => false,
            ],
            [
                'slug' => 'users',
                'name_en' => 'Users',
                'name_ar' => 'المستخدمون',
                'name_fr' => 'Utilisateurs',
                'type' => 'number',
                'icon' => 'fa-solid fa-users',
                'sort_order' => 1,
                'is_core' => false,
            ],
            [
                'slug' => 'income_expense',
                'name_en' => 'Income & Expense Tracking',
                'name_ar' => 'تتبع الإيرادات والمصروفات',
                'name_fr' => 'Suivi des revenus et dépenses',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-chart-line',
                'sort_order' => 2,
                'is_core' => true,
            ],
            [
                'slug' => 'budget',
                'name_en' => 'Budget Management',
                'name_ar' => 'إدارة الميزانية',
                'name_fr' => 'Gestion budgétaire',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-coins',
                'sort_order' => 3,
                'is_core' => false,
            ],
            [
                'slug' => 'goals',
                'name_en' => 'Savings Goals',
                'name_ar' => 'أهداف الادخار',
                'name_fr' => 'Objectifs d\'épargne',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-bullseye',
                'sort_order' => 4,
                'is_core' => false,
            ],
            [
                'slug' => 'debt',
                'name_en' => 'Debt Tracking',
                'name_ar' => 'تتبع الديون',
                'name_fr' => 'Suivi des dettes',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-hand-holding-usd',
                'sort_order' => 5,
                'is_core' => false,
            ],
            [
                'slug' => 'zakat',
                'name_en' => 'Zakat Calculator',
                'name_ar' => 'حاسبة الزكاة',
                'name_fr' => 'Calculateur de Zakat',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-hand-holding-heart',
                'sort_order' => 6,
                'is_core' => false,
            ],
            [
                'slug' => 'reports',
                'name_en' => 'Reports & Analytics',
                'name_ar' => 'التقارير والتحليلات',
                'name_fr' => 'Rapports et analyses',
                'type' => 'text',
                'icon' => 'fa-solid fa-chart-pie',
                'sort_order' => 7,
                'is_core' => false,
            ],
            [
                'slug' => 'support',
                'name_en' => 'Support',
                'name_ar' => 'الدعم الفني',
                'name_fr' => 'Assistance',
                'type' => 'text',
                'icon' => 'fa-solid fa-headset',
                'sort_order' => 8,
                'is_core' => false,
            ],
            [
                'slug' => 'collaboration',
                'name_en' => 'Multi-user Collaboration',
                'name_ar' => 'التعاون بين المستخدمين',
                'name_fr' => 'Collaboration multi-utilisateurs',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-users-gear',
                'sort_order' => 9,
                'is_core' => false,
            ],
            [
                'slug' => 'team_management',
                'name_en' => 'Team Management',
                'name_ar' => 'إدارة الفريق',
                'name_fr' => 'Gestion d\'équipe',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-user-group',
                'sort_order' => 10,
                'is_core' => false,
            ],
            [
                'slug' => 'roles_permissions',
                'name_en' => 'Roles & Permissions',
                'name_ar' => 'الأدوار والصلاحيات',
                'name_fr' => 'Rôles et permissions',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-shield-halved',
                'sort_order' => 11,
                'is_core' => false,
            ],
            [
                'slug' => 'export',
                'name_en' => 'Export PDF & Excel',
                'name_ar' => 'تصدير PDF و Excel',
                'name_fr' => 'Export PDF & Excel',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-file-export',
                'sort_order' => 12,
                'is_core' => false,
            ],
            [
                'slug' => 'api_access',
                'name_en' => 'API Access',
                'name_ar' => 'الوصول إلى API',
                'name_fr' => 'Accès API',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-code',
                'sort_order' => 13,
                'is_core' => false,
            ],
            [
                'slug' => 'audit_logs',
                'name_en' => 'Audit Logs',
                'name_ar' => 'سجلات التدقيق',
                'name_fr' => 'Journaux d\'audit',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-clock-rotate-left',
                'sort_order' => 14,
                'is_core' => false,
            ],
            [
                'slug' => 'activity_logs',
                'name_en' => 'Activity Logs',
                'name_ar' => 'سجلات النشاط',
                'name_fr' => 'Journaux d\'activité',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-list-check',
                'sort_order' => 15,
                'is_core' => false,
            ],
            [
                'slug' => 'deputy_admin',
                'name_en' => 'Deputy Admin Role',
                'name_ar' => 'دور نائب المدير',
                'name_fr' => 'Rôle d\'administrateur adjoint',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-user-tie',
                'sort_order' => 16,
                'is_core' => false,
            ],
            [
                'slug' => 'white_label',
                'name_en' => 'White Label',
                'name_ar' => 'العلامة البيضاء',
                'name_fr' => 'Marque blanche',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-tag',
                'sort_order' => 17,
                'is_core' => false,
            ],
            [
                'slug' => 'custom_integrations',
                'name_en' => 'Custom Integrations',
                'name_ar' => 'تكاملات مخصصة',
                'name_fr' => 'Intégrations personnalisées',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-puzzle-piece',
                'sort_order' => 18,
                'is_core' => false,
            ],
            [
                'slug' => 'sla',
                'name_en' => 'SLA Support',
                'name_ar' => 'دعم باتفاقية مستوى الخدمة',
                'name_fr' => 'Support SLA',
                'type' => 'boolean',
                'icon' => 'fa-solid fa-handshake',
                'sort_order' => 19,
                'is_core' => false,
            ],
            [
                'slug' => 'transactions_per_month',
                'name_en' => 'Transactions per Month',
                'name_ar' => 'عدد المعاملات شهرياً',
                'name_fr' => 'Transactions par mois',
                'type' => 'value',
                'icon' => 'fa-solid fa-arrow-right-arrow-left',
                'sort_order' => 20,
                'is_core' => false,
            ],
            [
                'slug' => 'api_requests_per_minute',
                'name_en' => 'API Requests per Minute',
                'name_ar' => 'حد طلبات API في الدقيقة',
                'name_fr' => 'Requêtes API par minute',
                'type' => 'value',
                'icon' => 'fa-solid fa-gauge-high',
                'sort_order' => 21,
                'is_core' => false,
            ],
            [
                'slug' => 'api_requests_per_hour',
                'name_en' => 'API Requests per Hour',
                'name_ar' => 'حد طلبات API في الساعة',
                'name_fr' => 'Requêtes API par heure',
                'type' => 'value',
                'icon' => 'fa-solid fa-gauge',
                'sort_order' => 22,
                'is_core' => false,
            ],
            [
                'slug' => 'api_requests_per_day',
                'name_en' => 'API Requests per Day',
                'name_ar' => 'حد طلبات API اليومي',
                'name_fr' => 'Requêtes API par jour',
                'type' => 'value',
                'icon' => 'fa-solid fa-gauge-low',
                'sort_order' => 23,
                'is_core' => false,
            ],
            [
                'slug' => 'max_api_tokens',
                'name_en' => 'Max API Tokens',
                'name_ar' => 'الحد الأقصى لمفاتيح API',
                'name_fr' => 'Max jetons API',
                'type' => 'value',
                'icon' => 'fa-solid fa-key',
                'sort_order' => 24,
                'is_core' => false,
            ],
        ];
    }

    private function syncPlanFeatures(SubscriptionPlan $plan, array $allFeatures, array $planFeatureSlugs): void
    {
        $coreSlugs = collect($allFeatures)->where('is_core', true)->pluck('slug')->toArray();
        $assignedSlugs = array_column($planFeatureSlugs, 'slug');
        $slugs = array_unique(array_merge($coreSlugs, $assignedSlugs));

        $syncData = [];

        foreach ($slugs as $slug) {
            $feature = collect($allFeatures)->firstWhere('slug', $slug);
            if (! $feature) {
                continue;
            }

            $dbFeature = PlanFeature::firstOrCreate(
                ['slug' => $slug],
                $feature
            );

            $pivot = collect($planFeatureSlugs)->firstWhere('slug', $slug);

            $syncData[$dbFeature->id] = [
                'value' => $pivot['value'] ?? null,
                'sort_order' => $feature['sort_order'],
            ];
        }

        $plan->planFeatures()->sync($syncData);
    }

    private function syncPlanPrices(SubscriptionPlan $plan, array $prices): void
    {
        $plan->planPrices()->delete();

        foreach ($prices as $price) {
            $plan->planPrices()->create($price);
        }
    }
}
