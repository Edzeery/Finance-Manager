<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Goal\StoreGoalRequest;
use App\Http\Requests\Api\Goal\UpdateGoalRequest;
use App\Repositories\GoalRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function __construct(private GoalRepository $goalRepo) {}

    public function index(Request $request): JsonResponse
    {
        $filters = array_merge(
            $request->only(['search', 'per_page']),
            ['workspace_id' => $request->user()->current_workspace_id]
        );
        $goals = $this->goalRepo->forUser(filters: $filters);

        return response()->json($goals);
    }

    public function store(StoreGoalRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['workspace_id'] = $request->user()->current_workspace_id;
        $data['name_ar'] ??= $data['name_en'];
        $data['name_fr'] ??= $data['name_en'];
        $goal = $this->goalRepo->create($data);

        return response()->json($goal, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $goal = $this->goalRepo->findOrFail($id);
        $this->authorize('view', $goal);

        return response()->json($goal);
    }

    public function update(UpdateGoalRequest $request, int $id): JsonResponse
    {
        $goal = $this->goalRepo->findOrFail($id);
        $this->authorize('update', $goal);

        $data = $request->validated();
        $data['name_ar'] ??= $data['name_en'] ?? $goal->name_ar;
        $data['name_fr'] ??= $data['name_en'] ?? $goal->name_fr;
        $this->goalRepo->update($goal, $data);

        return response()->json($goal->fresh());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $goal = $this->goalRepo->findOrFail($id);
        $this->authorize('delete', $goal);

        $goal->delete();

        return response()->json(['message' => __('messages.deleted')]);
    }
}
