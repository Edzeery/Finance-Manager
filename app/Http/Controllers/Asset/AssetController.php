<?php

namespace App\Http\Controllers\Asset;

use App\Contracts\Repositories\AssetRepositoryInterface;
use App\Enums\AssetType;
use App\Http\Controllers\BaseCrudController;
use App\Http\Requests\Asset\StoreAssetRequest;
use App\Http\Requests\Asset\UpdateAssetRequest;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends BaseCrudController
{
    protected string $model = Asset::class;

    public function __construct(
        private AssetRepositoryInterface $assetRepo,
    ) {}

    protected function getModelClass(): string
    {
        return Asset::class;
    }

    protected function getRoutePrefix(): string
    {
        return 'asset';
    }

    protected function getViewPrefix(): string
    {
        return 'asset';
    }

    protected function getLangPrefix(): string
    {
        return 'asset';
    }

    protected function getIcon(): string
    {
        return 'bi-pie-chart-fill';
    }

    protected function getRepositoryInterface(): string
    {
        return AssetRepositoryInterface::class;
    }

    protected function getRepository()
    {
        return $this->assetRepo;
    }

    protected function getBulkDeleteRedirect(): string
    {
        return 'asset.index';
    }

    protected function getBulkRestoreRedirect(): string
    {
        return 'asset.index';
    }

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.asset', 'asset.index', 'bi-pie-chart-fill');

        $tab = $request->input('tab', $request->boolean('trashed') ? 'trashed' : 'all');
        $filters = array_merge(
            $request->only(['search', 'type', 'per_page']),
            ['trashed' => $tab === 'trashed']
        );
        if ($tab === 'liquid') {
            $filters['liquid'] = true;
        } elseif ($tab === 'zakatable') {
            $filters['zakatable'] = true;
        }

        $assets = $this->assetRepo->forUser(filters: $filters);
        $totalValue = $this->assetRepo->totalValue();
        $liquidValue = $this->assetRepo->liquidValue();
        $zakatableValue = $this->assetRepo->zakatableValue();
        $types = AssetType::cases();

        $tabs = $this->buildTabs([
            'all' => ['label' => __('general.all'), 'scope' => null],
            'liquid' => ['label' => __('asset.liquid'), 'scope' => 'liquid'],
            'zakatable' => ['label' => __('asset.zakatable'), 'scope' => 'zakatable'],
            'trashed' => ['label' => __('general.trash'), 'scope' => fn ($q) => $q->onlyTrashed()],
        ]);

        $typeSubTabs = collect(['' => ['label' => __('general.all')]])
            ->union(collect($types)->mapWithKeys(fn ($t) => [$t->value => ['label' => $t->label()]]))
            ->toArray();

        return view('asset.index', $this->withBreadcrumbs(compact('assets', 'totalValue', 'liquidValue', 'zakatableValue', 'types', 'tab', 'tabs', 'typeSubTabs')));
    }

    public function create()
    {
        $types = AssetType::cases();

        return view('asset.create', compact('types'));
    }

    public function store(StoreAssetRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['is_liquid'] = $request->boolean('is_liquid');
        $data['is_zakatable'] = $request->boolean('is_zakatable');

        if (empty($data['total_value'])) {
            $data['total_value'] = ! empty($data['quantity']) && ! empty($data['unit_price'])
                ? $data['quantity'] * $data['unit_price']
                : 0;
        }

        $this->assetRepo->create($data);

        return redirect()->route('asset.index')
            ->with('success', __('messages.asset_created'));
    }

    public function edit(Asset $asset)
    {
        $this->authorize('update', $asset);
        $types = AssetType::cases();

        return view('asset.edit', compact('asset', 'types'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $this->authorize('update', $asset);

        $data = $request->validated();
        $data['is_liquid'] = $request->boolean('is_liquid');
        $data['is_zakatable'] = $request->boolean('is_zakatable');

        if (empty($data['total_value'])) {
            $data['total_value'] = ! empty($data['quantity']) && ! empty($data['unit_price'])
                ? $data['quantity'] * $data['unit_price']
                : $asset->total_value;
        }

        $this->assetRepo->update($asset, $data);

        return redirect()->route('asset.index')
            ->with('success', __('messages.asset_updated'));
    }
}
