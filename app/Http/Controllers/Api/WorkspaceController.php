<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Api\Workspace\UpdateWorkspaceRequest;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function __construct(
        private readonly WorkspaceService $workspaceService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        if ($request->user()->hasRole('super_admin')) {
            $workspaces = Workspace::all();
        } else {
            $workspaces = $request->user()->workspaces()->get();
        }

        return response()->json($workspaces->map(fn($ws) => [
            'id' => $ws->id,
            'name' => $ws->name,
            'slug' => $ws->slug,
            'type' => $ws->type,
            'currency' => $ws->currency,
            'is_active' => $ws->is_active,
            'plan' => $ws->activePlan()?->name,
            'role' => $request->user()->workspaceRole($ws),
        ]));
    }

    public function store(StoreWorkspaceRequest $request): JsonResponse
    {
        try {
            $workspace = $this->workspaceService->createForUser($request->user(), $request->validated());
            return response()->json($workspace, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function show(Request $request, Workspace $workspace): JsonResponse
    {
        if (!$request->user()->hasRole('super_admin') && !$workspace->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => __('messages.unauthorized')], 403);
        }

        return response()->json([
            'id' => $workspace->id,
            'name' => $workspace->name,
            'slug' => $workspace->slug,
            'type' => $workspace->type,
            'currency' => $workspace->currency,
            'is_active' => $workspace->is_active,
            'plan' => $workspace->activePlan()?->name,
            'role' => $request->user()->workspaceRole($workspace),
        ]);
    }

    public function update(UpdateWorkspaceRequest $request, Workspace $workspace): JsonResponse
    {
        if (!$request->user()->hasRole('super_admin') && !$workspace->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => __('messages.unauthorized')], 403);
        }

        $workspace->update($request->validated());

        return response()->json($workspace);
    }

    public function destroy(Request $request, Workspace $workspace): JsonResponse
    {
        if (!$request->user()->hasRole('super_admin') && !$workspace->isOwner($request->user())) {
            return response()->json(['message' => __('messages.unauthorized')], 403);
        }

        $workspace->delete();

        return response()->json(['message' => __('messages.deleted')]);
    }

    public function switch(Request $request, Workspace $workspace): JsonResponse
    {
        if (!$request->user()->hasRole('super_admin') && !$workspace->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => __('messages.unauthorized')], 403);
        }

        $request->user()->switchWorkspace($workspace);

        return response()->json(['workspace_id' => $workspace->id]);
    }
}
