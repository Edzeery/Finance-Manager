<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Expense\StoreExpenseRequest;
use App\Http\Requests\Api\Expense\UpdateExpenseRequest;
use App\Repositories\ExpenseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseRepository $expenseRepo) {}

    public function index(Request $request): JsonResponse
    {
        $filters = array_merge(
            $request->only(['category', 'date_from', 'date_to', 'search', 'per_page']),
            ['workspace_id' => $request->user()->current_workspace_id]
        );
        $expenses = $this->expenseRepo->forUser(filters: $filters);

        return response()->json($expenses);
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['workspace_id'] = $request->user()->current_workspace_id;
        $expense = $this->expenseRepo->create($data);

        return response()->json($expense, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $expense = $this->expenseRepo->findOrFail($id);
        $this->authorize('view', $expense);

        return response()->json($expense);
    }

    public function update(UpdateExpenseRequest $request, int $id): JsonResponse
    {
        $expense = $this->expenseRepo->findOrFail($id);
        $this->authorize('update', $expense);

        $this->expenseRepo->update($expense, $request->validated());

        return response()->json($expense->fresh());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $expense = $this->expenseRepo->findOrFail($id);
        $this->authorize('delete', $expense);

        $expense->delete();

        return response()->json(['message' => __('messages.deleted')]);
    }
}
