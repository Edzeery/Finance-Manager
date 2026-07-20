<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Asset\StoreAssetRequest;
use App\Http\Requests\Api\Asset\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use App\Repositories\AssetRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetController extends Controller
{
    public function __construct(private AssetRepository $assetRepo) {}

    public function index(Request $request): JsonResource
    {
        $filters = array_merge(
            $request->only(['type', 'search', 'per_page']),
            ['workspace_id' => $request->user()->current_workspace_id]
        );
        $assets = $this->assetRepo->forUser(filters: $filters);

        return AssetResource::collection($assets);
    }

    public function store(StoreAssetRequest $request): JsonResource
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['workspace_id'] = $request->user()->current_workspace_id;
        $asset = $this->assetRepo->create($data);

        return new AssetResource($asset);
    }

    public function show(Request $request, int $id): JsonResource
    {
        $asset = $this->assetRepo->findOrFail($id);
        $this->authorize('view', $asset);

        return new AssetResource($asset);
    }

    public function update(UpdateAssetRequest $request, int $id): JsonResource
    {
        $asset = $this->assetRepo->findOrFail($id);
        $this->authorize('update', $asset);

        $this->assetRepo->update($asset, $request->validated());

        return new AssetResource($asset->fresh());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $asset = $this->assetRepo->findOrFail($id);
        $this->authorize('delete', $asset);

        $asset->delete();

        return response()->json(['message' => __('messages.deleted')]);
    }
}
