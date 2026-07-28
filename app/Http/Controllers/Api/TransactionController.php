<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $page = $request->integer('page', 1);
        $locale = app()->getLocale();

        $incomeQuery = Income::active()
            ->select(
                'incomes.id',
                DB::raw("'income' as type"),
                'incomes.amount',
                'incomes.date',
                'incomes.description',
                'incomes.created_at',
                DB::raw("COALESCE(ic.name_{$locale}, '—') as category")
            )
            ->leftJoin('income_categories as ic', 'incomes.category_id', '=', 'ic.id');

        $expenseQuery = Expense::active()
            ->select(
                'expenses.id',
                DB::raw("'expense' as type"),
                'expenses.amount',
                'expenses.date',
                'expenses.description',
                'expenses.created_at',
                DB::raw("COALESCE(ec.name_{$locale}, '—') as category")
            )
            ->leftJoin('expense_categories as ec', 'expenses.category_id', '=', 'ec.id');

        $incomeCount = (clone $incomeQuery)->count();
        $expenseCount = (clone $expenseQuery)->count();
        $total = $incomeCount + $expenseCount;

        $query = $incomeQuery->unionAll($expenseQuery);
        $query->orderBy('date', 'desc');

        $items = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ],
        ]);
    }
}
