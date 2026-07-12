<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);

        $incomes = Income::with('category')->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'type' => 'income',
                'amount' => $i->amount,
                'date' => $i->date,
                'description' => $i->description,
                'category' => $i->category?->name,
                'created_at' => $i->created_at,
            ]);

        $expenses = Expense::with('category')->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'type' => 'expense',
                'amount' => $e->amount,
                'date' => $e->date,
                'description' => $e->description,
                'category' => $e->category?->name,
                'created_at' => $e->created_at,
            ]);

        $transactions = $incomes->concat($expenses)
            ->sortByDesc('date')
            ->values();

        return response()->json($transactions);
    }
}
