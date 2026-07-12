<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\Budget;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Notification;
use App\Models\Workspace;
use App\Models\ZakatRecord;
use App\Policies\AssetPolicy;
use App\Policies\BudgetPolicy;
use App\Policies\DebtPolicy;
use App\Policies\ExpenseCategoryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\FinancialGoalPolicy;
use App\Policies\IncomeCategoryPolicy;
use App\Policies\IncomePolicy;
use App\Policies\NotificationPolicy;
use App\Policies\WorkspacePolicy;
use App\Policies\ZakatRecordPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PolicyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        Gate::policy(Budget::class, BudgetPolicy::class);
        Gate::policy(FinancialGoal::class, FinancialGoalPolicy::class);
        Gate::policy(Debt::class, DebtPolicy::class);
        Gate::policy(Asset::class, AssetPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(Income::class, IncomePolicy::class);
        Gate::policy(ExpenseCategory::class, ExpenseCategoryPolicy::class);
        Gate::policy(IncomeCategory::class, IncomeCategoryPolicy::class);
        Gate::policy(Notification::class, NotificationPolicy::class);
        Gate::policy(ZakatRecord::class, ZakatRecordPolicy::class);
        Gate::policy(Workspace::class, WorkspacePolicy::class);

        foreach (['report.view', 'report.export', 'workspace-setting.view', 'activity-log.view'] as $perm) {
            Gate::define($perm, fn($user) => $user->hasPermission($perm));
        }
    }
}
