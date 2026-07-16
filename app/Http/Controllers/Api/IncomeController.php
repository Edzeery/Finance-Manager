<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Income\StoreIncomeRequest;
use App\Http\Requests\Api\Income\UpdateIncomeRequest;
use App\Repositories\IncomeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function __construct(private IncomeRepository $incomeRepo) {}

    public function index(Request $request): JsonResponse
    {
        $filters = array_merge(
            $request->only(['category', 'date_from', 'date_to', 'search', 'per_page']),
            ['workspace_id' => $request->user()->current_workspace_id]
        );
        $incomes = $this->incomeRepo->forUser(filters: $filters);

        return response()->json($incomes);
    }

    public function store(StoreIncomeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['workspace_id'] = $request->user()->current_workspace_id;
        $income = $this->incomeRepo->create($data);

        return response()->json($income, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $income = $this->incomeRepo->findOrFail($id);
        $this->authorize('view', $income);

        return response()->json($income);
    }

    public function update(UpdateIncomeRequest $request, int $id): JsonResponse
    {
        $income = $this->incomeRepo->findOrFail($id);
        $this->authorize('update', $income);

        $this->incomeRepo->update($income, $request->validated());

        return response()->json($income->fresh());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $income = $this->incomeRepo->findOrFail($id);
        $this->authorize('delete', $income);

        $income->delete();

        return response()->json(['message' => __('messages.deleted')]);
    }
}
