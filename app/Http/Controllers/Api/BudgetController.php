<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Budget\StoreBudgetRequest;
use App\Http\Requests\Api\Budget\UpdateBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Repositories\BudgetRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BudgetController extends Controller
{
    public function __construct(private BudgetRepository $budgetRepo) {}

    public function index(Request $request): ResourceCollection
    {
        $filters = array_merge(
            $request->only(['search', 'per_page']),
            ['workspace_id' => $request->user()->current_workspace_id]
        );
        $budgets = $this->budgetRepo->forUser(filters: $filters);

        return BudgetResource::collection($budgets);
    }

    public function store(StoreBudgetRequest $request): JsonResponse|JsonResource
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['workspace_id'] = $request->user()->current_workspace_id;
        $data['name_ar'] ??= $data['name_en'];
        $data['name_fr'] ??= $data['name_en'];
        $budget = $this->budgetRepo->create($data);

        return response()->json(new BudgetResource($budget), 201);
    }

    public function show(Request $request, int $id): JsonResponse|JsonResource
    {
        $budget = $this->budgetRepo->findOrFail($id);
        $this->authorize('view', $budget);

        return response()->json(new BudgetResource($budget));
    }

    public function update(UpdateBudgetRequest $request, int $id): JsonResponse|JsonResource
    {
        $budget = $this->budgetRepo->findOrFail($id);
        $this->authorize('update', $budget);

        $data = $request->validated();
        $data['name_ar'] ??= $data['name_en'] ?? $budget->name_ar;
        $data['name_fr'] ??= $data['name_en'] ?? $budget->name_fr;
        $this->budgetRepo->update($budget, $data);

        return response()->json(new BudgetResource($budget->fresh()));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $budget = $this->budgetRepo->findOrFail($id);
        $this->authorize('delete', $budget);

        $budget->delete();

        return response()->json(['message' => __('messages.deleted')]);
    }
}
