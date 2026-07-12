<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\PlanFeature;
use Illuminate\Http\Request;

class PlanFeatureController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.features'));

        $query = PlanFeature::orderBy('sort_order');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_fr', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_core')) {
            $query->where('is_core', $request->is_core === 'true');
        }

        $perPage = min((int) $request->input('per_page', 20), config('finance.per_page_max', 100));
        $features = $query->paginate($perPage);

        return view('super-admin.features', $this->withBreadcrumbs(compact('features')));
    }

    public function create()
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.features'), route('super.admin.features.index'), 'bi-list-check')
            ->addBreadcrumb(__('super-admin.create_feature'));

        return view('super-admin.features-form', $this->withBreadcrumbs(['feature' => null]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:100', 'unique:plan_features,slug'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_core' => ['boolean'],
        ]);

        PlanFeature::create($validated);

        return redirect()->route('super.admin.features.index')
            ->with('success', __('super-admin.feature_created'));
    }

    public function edit(PlanFeature $feature)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.features'), route('super.admin.features.index'), 'bi-list-check')
            ->addBreadcrumb(__('super-admin.edit_feature'));

        return view('super-admin.features-form', $this->withBreadcrumbs(compact('feature')));
    }

    public function update(Request $request, PlanFeature $feature)
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:100', 'unique:plan_features,slug,' . $feature->id],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_core' => ['boolean'],
        ]);

        $feature->update($validated);

        return redirect()->route('super.admin.features.index')
            ->with('success', __('super-admin.feature_updated'));
    }

    public function destroy(PlanFeature $feature)
    {
        if ($feature->plans()->exists()) {
            return redirect()->route('super.admin.features.index')
                ->with('error', __('super-admin.feature_has_plans'));
        }

        $feature->delete();

        return redirect()->route('super.admin.features.index')
            ->with('success', __('super-admin.feature_deleted'));
    }
}
