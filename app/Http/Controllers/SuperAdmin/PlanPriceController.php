<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\PlanPrice;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PlanPriceController extends Controller
{
    use HasBreadcrumbs;

    private function redirectBack(Request $request, SubscriptionPlan $plan, ?string $defaultRoute = null, array $params = [])
    {
        if ($request->query('_tab') === 'prices') {
            return redirect()->route('super.admin.plans.edit', [$plan, 'tab' => 'prices']);
        }

        return redirect()->route($defaultRoute ?? 'super.admin.plans.prices.index', array_merge([$plan], $params));
    }

    public function index(Request $request, SubscriptionPlan $plan)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.plans'), route('super.admin.plans.index'), 'bi-box')
            ->addBreadcrumb($plan->name, route('super.admin.plans.edit', $plan))
            ->addBreadcrumb(__('super-admin.prices'));

        $query = $plan->planPrices()->orderBy('currency')->orderBy('billing_period');

        $perPage = min((int) $request->input('per_page', 50), config('finance.per_page_max', 100));
        $prices = $query->paginate($perPage);

        return view('super-admin.prices', $this->withBreadcrumbs(compact('plan', 'prices')));
    }

    public function create(Request $request, SubscriptionPlan $plan)
    {
        if ($request->query('_tab') === 'prices') {
            return redirect()->route('super.admin.plans.edit', [$plan, 'tab' => 'prices']);
        }

        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.plans'), route('super.admin.plans.index'), 'bi-box')
            ->addBreadcrumb($plan->name, route('super.admin.plans.edit', $plan))
            ->addBreadcrumb(__('super-admin.prices'), route('super.admin.plans.prices.index', $plan), 'bi-currency-dollar')
            ->addBreadcrumb(__('super-admin.create_price'));

        return view('super-admin.prices-form', $this->withBreadcrumbs(['plan' => $plan, 'price' => null]));
    }

    public function store(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'billing_period' => ['required', 'in:monthly,yearly'],
            'currency' => ['required', 'string', 'max:10'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $plan->planPrices()->create($validated);

        return $this->redirectBack($request, $plan, 'super.admin.plans.prices.index')
            ->with('success', __('super-admin.price_created'));
    }

    public function edit(SubscriptionPlan $plan, PlanPrice $price)
    {
        if ($price->plan_id !== $plan->id) {
            abort(404);
        }

        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.plans'), route('super.admin.plans.index'), 'bi-box')
            ->addBreadcrumb($plan->name, route('super.admin.plans.edit', $plan))
            ->addBreadcrumb(__('super-admin.prices'), route('super.admin.plans.prices.index', $plan), 'bi-currency-dollar')
            ->addBreadcrumb(__('super-admin.edit_price'));

        return view('super-admin.prices-form', $this->withBreadcrumbs(compact('plan', 'price')));
    }

    public function update(Request $request, SubscriptionPlan $plan, PlanPrice $price)
    {
        if ($price->plan_id !== $plan->id) {
            abort(404);
        }

        $validated = $request->validate([
            'billing_period' => ['required', 'in:monthly,yearly'],
            'currency' => ['required', 'string', 'max:10'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $price->update($validated);

        return $this->redirectBack($request, $plan, 'super.admin.plans.prices.index')
            ->with('success', __('super-admin.price_updated'));
    }

    public function destroy(SubscriptionPlan $plan, PlanPrice $price)
    {
        if ($price->plan_id !== $plan->id) {
            abort(404);
        }

        $price->delete();

        return $this->redirectBack(request(), $plan, 'super.admin.plans.prices.index')
            ->with('success', __('super-admin.price_deleted'));
    }
}
