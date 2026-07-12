<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Debt\StoreDebtRequest;
use App\Http\Requests\Api\Debt\UpdateDebtRequest;
use App\Repositories\DebtRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function __construct(private DebtRepository $debtRepo) {}

    public function index(Request $request): JsonResponse
    {
        $filters = array_merge(
            $request->only(['status', 'type', 'search', 'per_page']),
            ['workspace_id' => $request->user()->current_workspace_id]
        );
        $debts = $this->debtRepo->forUser(filters: $filters);

        return response()->json($debts);
    }

    public function store(StoreDebtRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['workspace_id'] = $request->user()->current_workspace_id;
        $debt = $this->debtRepo->create($data);

        return response()->json($debt, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $debt = $this->debtRepo->findOrFail($id);
        $this->authorize('view', $debt);

        return response()->json($debt);
    }

    public function update(UpdateDebtRequest $request, int $id): JsonResponse
    {
        $debt = $this->debtRepo->findOrFail($id);
        $this->authorize('update', $debt);

        $this->debtRepo->update($debt, $request->validated());

        return response()->json($debt->fresh());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $debt = $this->debtRepo->findOrFail($id);
        $this->authorize('delete', $debt);

        $debt->delete();

        return response()->json(['message' => __('messages.deleted')]);
    }
}
