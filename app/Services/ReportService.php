<?php

namespace App\Services;

use App\Contracts\Services\ReportServiceInterface;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Debt;
use App\Models\ZakatRecord;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;

class ReportService implements ReportServiceInterface
{
    public function monthlyReport(int $year, int $month, ?int $userId = null): array
    {
        $start = now()->createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $income = Income::whereBetween('date', [$start, $end])
            ->get();

        $expense = Expense::whereBetween('date', [$start, $end])
            ->get();

        $debts = Debt::active()->get();

        $incomeByCategory = $income->groupBy('category_id');
        $incomeCatIds = $incomeByCategory->keys();
        $incomeCategories = IncomeCategory::whereIn('id', $incomeCatIds)->get()->keyBy('id');

        $expenseByCategory = $expense->groupBy('category_id');
        $expenseCatIds = $expenseByCategory->keys();
        $expenseCategories = ExpenseCategory::whereIn('id', $expenseCatIds)->get()->keyBy('id');

        return [
            'period' => ['start' => $start, 'end' => $end],
            'totalIncome' => $income->sum('amount'),
            'totalExpense' => $expense->sum('amount'),
            'netSavings' => $income->sum('amount') - $expense->sum('amount'),
            'incomeByCategory' => $incomeByCategory->map(fn($i) => $i->sum('amount')),
            'expenseByCategory' => $expenseByCategory->map(fn($e) => $e->sum('amount')),
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'activeDebts' => $debts,
            'incomeCount' => $income->count(),
            'expenseCount' => $expense->count(),
        ];
    }

    public function yearlyReport(int $year, ?int $userId = null): array
    {
        $start = now()->createFromDate($year, 1, 1)->startOfYear();
        $end = $start->copy()->endOfYear();

        $income = Income::whereBetween('date', [$start, $end])
            ->get();

        $expense = Expense::whereBetween('date', [$start, $end])
            ->get();

        $monthlyIncome = $income->groupBy(fn($i) => $i->date->format('m'))->map(fn($group) => $group->sum('amount'));
        $monthlyExpense = $expense->groupBy(fn($e) => $e->date->format('m'))->map(fn($group) => $group->sum('amount'));

        $incomeByCategory = $income->groupBy('category_id');
        $incomeCatIds = $incomeByCategory->keys();
        $incomeCategories = IncomeCategory::whereIn('id', $incomeCatIds)->get()->keyBy('id');

        $expenseByCategory = $expense->groupBy('category_id');
        $expenseCatIds = $expenseByCategory->keys();
        $expenseCategories = ExpenseCategory::whereIn('id', $expenseCatIds)->get()->keyBy('id');

        return [
            'period' => ['start' => $start, 'end' => $end],
            'year' => $year,
            'totalIncome' => $income->sum('amount'),
            'totalExpense' => $expense->sum('amount'),
            'netSavings' => $income->sum('amount') - $expense->sum('amount'),
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpense' => $monthlyExpense,
            'incomeByCategory' => $incomeByCategory->map(fn($i) => $i->sum('amount')),
            'expenseByCategory' => $expenseByCategory->map(fn($e) => $e->sum('amount')),
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'incomeCount' => $income->count(),
            'expenseCount' => $expense->count(),
        ];
    }
}
