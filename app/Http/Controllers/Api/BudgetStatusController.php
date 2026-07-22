<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\JsonResponse;

class BudgetStatusController extends Controller
{
    public function getBudgetStatus(ExpenseCategory $expenseCategory): JsonResponse
    {
        $info = $expenseCategory->getActiveBudgetInfo();

        return response()->json([
            'has_budget' => $info !== null,
            'budget_name' => $info['budget_name'] ?? null,
            'allocated' => $info['allocated'] ?? 0,
            'spent' => $info['spent'] ?? 0,
            'remaining' => $info['remaining'] ?? 0,
        ]);
    }
}
