<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Debt\StoreDebtRequest;
use App\Http\Requests\Api\Debt\UpdateDebtRequest;
use App\Http\Resources\DebtResource;
use App\Repositories\DebtRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function __construct(private DebtRepository $debtRepo) {}

    public function index(Request $request): \Illuminate\Http\Resources\Json\ResourceCollection
    {
        $filters = array_merge(
            $request->only(['status', 'type', 'search', 'per_page']),
            ['workspace_id' => $request->user()->current_workspace_id]
        );
        $debts = $this->debtRepo->forUser(filters: $filters);

        return DebtResource::collection($debts);
    }

    public function store(StoreDebtRequest $request): JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['workspace_id'] = $request->user()->current_workspace_id;
        $debt = $this->debtRepo->create($data);

        return response()->json(new DebtResource($debt), 201);
    }

    public function show(Request $request, int $id): JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $debt = $this->debtRepo->findOrFail($id);
        $this->authorize('view', $debt);

        return response()->json(new DebtResource($debt));
    }

    public function update(UpdateDebtRequest $request, int $id): JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $debt = $this->debtRepo->findOrFail($id);
        $this->authorize('update', $debt);

        $this->debtRepo->update($debt, $request->validated());

        return response()->json(new DebtResource($debt->fresh()));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $debt = $this->debtRepo->findOrFail($id);
        $this->authorize('delete', $debt);

        $debt->delete();

        return response()->json(['message' => __('messages.deleted')]);
    }
}
