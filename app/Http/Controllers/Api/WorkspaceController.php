<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Api\Workspace\UpdateWorkspaceRequest;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function __construct(
        private readonly WorkspaceService $workspaceService,
    ) {}

    public function index(Request $request): \Illuminate\Http\Resources\Json\ResourceCollection
    {
        if ($request->user()->hasRole('super_admin')) {
            $workspaces = Workspace::all();
        } else {
            $workspaces = $request->user()->workspaces()->get();
        }

        return WorkspaceResource::collection($workspaces);
    }

    public function store(StoreWorkspaceRequest $request): JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        try {
            $workspace = $this->workspaceService->createForUser($request->user(), $request->validated());

            return response()->json(new WorkspaceResource($workspace), 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function show(Request $request, Workspace $workspace): JsonResponse
    {
        if (! $request->user()->hasRole('super_admin') && ! $workspace->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => __('messages.unauthorized')], 403);
        }

        return response()->json(new WorkspaceResource($workspace));
    }

    public function update(UpdateWorkspaceRequest $request, Workspace $workspace): JsonResponse
    {
        if (! $request->user()->hasRole('super_admin') && ! $workspace->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => __('messages.unauthorized')], 403);
        }

        $workspace->update($request->validated());

        return response()->json(new WorkspaceResource($workspace));
    }

    public function destroy(Request $request, Workspace $workspace): JsonResponse
    {
        if (! $request->user()->hasRole('super_admin') && ! $workspace->isOwner($request->user())) {
            return response()->json(['message' => __('messages.unauthorized')], 403);
        }

        $workspace->delete();

        return response()->json(['message' => __('messages.deleted')]);
    }

    public function switch(Request $request, Workspace $workspace): JsonResponse
    {
        if (! $request->user()->hasRole('super_admin') && ! $workspace->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => __('messages.unauthorized')], 403);
        }

        $request->user()->switchWorkspace($workspace);

        return response()->json(['workspace_id' => $workspace->id]);
    }
}
