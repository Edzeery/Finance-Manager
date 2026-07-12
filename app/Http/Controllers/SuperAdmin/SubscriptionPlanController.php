<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\PlanFeature;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.plans'));

        $tab = $request->query('tab', 'plans');

        $query = SubscriptionPlan::withCount('planFeatures', 'activePrices')->orderBy('sort_order');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = min((int) $request->input('per_page', 20), config('finance.per_page_max', 100));
        $plans = $query->paginate($perPage);

        $featuresQuery = PlanFeature::orderBy('sort_order');
        if ($request->filled('features_search')) {
            $fs = $request->features_search;
            $featuresQuery->where(function ($q) use ($fs) {
                $q->where('name_en', 'like', "%{$fs}%")
                  ->orWhere('slug', 'like', "%{$fs}%")
                  ->orWhere('name_ar', 'like', "%{$fs}%")
                  ->orWhere('name_fr', 'like', "%{$fs}%");
            });
        }
        if ($request->filled('features_type')) {
            $featuresQuery->where('type', $request->features_type);
        }
        if ($request->filled('features_core')) {
            $featuresQuery->where('is_core', $request->features_core === 'true');
        }
        $featuresPerPage = min((int) $request->input('features_per_page', 20), config('finance.per_page_max', 100));
        $features = $featuresQuery->paginate($featuresPerPage, ['*'], 'features_page');

        $selectedPlanId = $request->input('price_plan_id');
        $selectedPlan = null;
        $prices = collect();
        if ($selectedPlanId) {
            $selectedPlan = SubscriptionPlan::find($selectedPlanId);
            if ($selectedPlan) {
                $prices = $selectedPlan->planPrices()->orderBy('currency')->orderBy('billing_period')->get();
            }
        }
        $allPlansForPrices = SubscriptionPlan::orderBy('sort_order')->get();

        return view('super-admin.plans', $this->withBreadcrumbs(compact(
            'tab', 'plans', 'features', 'selectedPlan', 'prices', 'allPlansForPrices'
        )));
    }

    public function create()
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.plans'), route('super.admin.plans.index'), 'bi-box')
            ->addBreadcrumb(__('super-admin.create_plan'));

        $allFeatures = PlanFeature::orderBy('sort_order')->get();
        $assignedFeatures = collect();
        $prices = collect();

        return view('super-admin.plans-form', $this->withBreadcrumbs([
            'plan' => null,
            'allFeatures' => $allFeatures,
            'assignedFeatures' => $assignedFeatures,
            'prices' => $prices,
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'unique:subscription_plans,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_free' => ['boolean'],
            'is_active' => ['boolean'],
            'is_public' => ['boolean'],
            'yearly_discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:500'],
            'plan_features' => ['nullable', 'array'],
            'plan_features.*.feature_id' => ['required_with:plan_features', 'exists:plan_features,id'],
            'plan_features.*.value' => ['nullable', 'string', 'max:255'],
            'plan_features.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $planFeatures = $validated['plan_features'] ?? [];
        unset($validated['plan_features']);

        $plan = SubscriptionPlan::create($validated);

        $this->syncPlanFeatures($plan, $planFeatures);

        return redirect()->route('super.admin.plans.index')
            ->with('success', __('super-admin.plan_created'));
    }

    public function edit(SubscriptionPlan $plan)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.plans'), route('super.admin.plans.index'), 'bi-box')
            ->addBreadcrumb(__('super-admin.edit_plan'));

        $allFeatures = PlanFeature::orderBy('sort_order')->get();
        $assignedFeatures = $plan->planFeatures()
            ->get()
            ->keyBy('id')
            ->map(fn($f) => [
                'feature_id' => $f->id,
                'value' => $f->pivot->value,
                'sort_order' => $f->pivot->sort_order,
            ]);

        $prices = $plan->planPrices()->orderBy('currency')->orderBy('billing_period')->get();

        return view('super-admin.plans-form', $this->withBreadcrumbs(compact(
            'plan', 'allFeatures', 'assignedFeatures', 'prices'
        )));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'unique:subscription_plans,slug,' . $plan->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_free' => ['boolean'],
            'is_active' => ['boolean'],
            'is_public' => ['boolean'],
            'yearly_discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:500'],
            'plan_features' => ['nullable', 'array'],
            'plan_features.*.feature_id' => ['required_with:plan_features', 'exists:plan_features,id'],
            'plan_features.*.value' => ['nullable', 'string', 'max:255'],
            'plan_features.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $planFeatures = $validated['plan_features'] ?? [];
        unset($validated['plan_features']);

        $plan->update($validated);

        $this->syncPlanFeatures($plan, $planFeatures);

        return redirect()->route('super.admin.plans.index')
            ->with('success', __('super-admin.plan_updated'));
    }

    public function destroy(SubscriptionPlan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            return redirect()->route('super.admin.plans.index')
                ->with('error', __('super-admin.plan_has_subscriptions'));
        }

        $plan->delete();

        return redirect()->route('super.admin.plans.index')
            ->with('success', __('super-admin.plan_deleted'));
    }

    private function syncPlanFeatures(SubscriptionPlan $plan, array $features): void
    {
        $syncData = [];
        foreach ($features as $item) {
            if (isset($item['feature_id'])) {
                $syncData[(int) $item['feature_id']] = [
                    'value' => $item['value'] ?? null,
                    'sort_order' => $item['sort_order'] ?? 0,
                ];
            }
        }

        $coreFeatureIds = PlanFeature::where('is_core', true)->pluck('id');
        foreach ($coreFeatureIds as $coreId) {
            if (!isset($syncData[$coreId])) {
                $syncData[$coreId] = ['value' => null, 'sort_order' => 0];
            }
        }

        $plan->planFeatures()->sync($syncData);
    }
}
