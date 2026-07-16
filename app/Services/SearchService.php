<?php

namespace App\Services;

use App\Contracts\Services\SearchServiceInterface;
use App\DTOs\SearchResult;
use App\Helpers\CurrencyFormatter;
use App\Models\Asset;
use App\Models\Budget;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Support\DatabaseHelper;
use Illuminate\Support\Collection;

class SearchService implements SearchServiceInterface
{
    public function search(string $query, ?int $workspaceId = null, ?int $userId = null): Collection
    {
        $limit = 5;
        $wid = $workspaceId ?? config('app.current_workspace')?->id;

        $incomes = $this->searchIncomes($query, $limit, $wid);
        $expenses = $this->searchExpenses($query, $limit, $wid);
        $debts = $this->searchDebts($query, $limit, $wid);
        $assets = $this->searchAssets($query, $limit, $wid);
        $budgets = $this->searchBudgets($query, $limit, $wid);
        $goals = $this->searchGoals($query, $limit, $wid);

        return collect()
            ->merge($incomes)
            ->merge($expenses)
            ->merge($debts)
            ->merge($assets)
            ->merge($budgets)
            ->merge($goals)
            ->sortByDesc('date')
            ->take(30)
            ->values();
    }

    private function searchIncomes(string $query, int $limit, ?int $workspaceId = null): Collection
    {
        $base = Income::query();
        if ($workspaceId) {
            $base->where('workspace_id', $workspaceId);
        }
        DatabaseHelper::applyFulltextToQuery($base, ['description', 'notes'], $query);

        return $base->with('category')
            ->latest('date')
            ->take($limit)
            ->get()
            ->map(fn ($i) => new SearchResult(
                id: $i->id,
                type: 'income',
                description: $i->description,
                amount: (float) $i->amount,
                date: $i->date,
                category: CurrencyFormatter::localeName($i->category ?? new \stdClass, 'name') ?: '—',
                url: route('income.edit', $i),
            ));
    }

    private function searchExpenses(string $query, int $limit, ?int $workspaceId = null): Collection
    {
        $base = Expense::query();
        if ($workspaceId) {
            $base->where('workspace_id', $workspaceId);
        }
        DatabaseHelper::applyFulltextToQuery($base, ['description', 'notes'], $query);

        return $base->with('category')
            ->latest('date')
            ->take($limit)
            ->get()
            ->map(fn ($e) => new SearchResult(
                id: $e->id,
                type: 'expense',
                description: $e->description,
                amount: (float) $e->amount,
                date: $e->date,
                category: CurrencyFormatter::localeName($e->category ?? new \stdClass, 'name') ?: '—',
                url: route('expense.edit', $e),
            ));
    }

    private function searchDebts(string $query, int $limit, ?int $workspaceId = null): Collection
    {
        $base = Debt::query();
        if ($workspaceId) {
            $base->where('workspace_id', $workspaceId);
        }
        DatabaseHelper::applyFulltextToQuery($base, ['counterparty_name', 'notes'], $query);

        return $base->latest('due_date')
            ->take($limit)
            ->get()
            ->map(fn ($d) => new SearchResult(
                id: $d->id,
                type: 'debt',
                description: $d->counterparty_name,
                amount: (float) $d->remaining_amount,
                date: $d->due_date,
                category: '',
                url: route('debt.show', $d),
            ));
    }

    private function searchAssets(string $query, int $limit, ?int $workspaceId = null): Collection
    {
        $base = Asset::query();
        if ($workspaceId) {
            $base->where('workspace_id', $workspaceId);
        }
        DatabaseHelper::applyFulltextToQuery($base, ['name', 'description'], $query);

        return $base->latest()
            ->take($limit)
            ->get()
            ->map(fn ($a) => new SearchResult(
                id: $a->id,
                type: 'asset',
                description: $a->name,
                amount: (float) ($a->total_value ?? 0),
                date: $a->created_at,
                category: $a->type->label(),
                url: route('asset.edit', $a),
            ));
    }

    private function searchBudgets(string $query, int $limit, ?int $workspaceId = null): Collection
    {
        $base = Budget::query();
        if ($workspaceId) {
            $base->where('workspace_id', $workspaceId);
        }
        DatabaseHelper::applyFulltextToQuery($base, ['name_ar', 'name_fr', 'name_en'], $query);

        return $base->latest()
            ->take($limit)
            ->get()
            ->map(fn ($b) => new SearchResult(
                id: $b->id,
                type: 'budget',
                description: CurrencyFormatter::localeName($b),
                amount: (float) ($b->total_amount ?? 0),
                date: $b->start_date,
                category: __('general.budget'),
                url: route('budget.show', $b),
            ));
    }

    private function searchGoals(string $query, int $limit, ?int $workspaceId = null): Collection
    {
        $base = FinancialGoal::query();
        if ($workspaceId) {
            $base->where('workspace_id', $workspaceId);
        }
        DatabaseHelper::applyFulltextToQuery($base, ['name_ar', 'name_fr', 'name_en'], $query);

        return $base->latest()
            ->take($limit)
            ->get()
            ->map(fn ($g) => new SearchResult(
                id: $g->id,
                type: 'goal',
                description: CurrencyFormatter::localeName($g),
                amount: (float) ($g->target_amount ?? 0),
                date: $g->target_date,
                category: __('general.goal'),
                url: route('goal.edit', $g),
            ));
    }
}
