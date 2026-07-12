<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use HasBreadcrumbs;

    public function __construct(
        private SubscriptionService $subscriptionService,
    ) {}

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.subscriptions'));

        $query = Subscription::withoutWorkspace()->with('workspace', 'plan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('workspace', function ($wq) use ($search) {
                    $wq->where('name', 'like', "%{$search}%");
                })->orWhereHas('workspace.users', function ($uq) use ($search) {
                    $uq->where('email', 'like', "%{$search}%")
                       ->orWhere('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('subscription_plan_id', $request->plan_id);
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $subscriptions = $query->latest('starts_at')->paginate($perPage);

        $plans = SubscriptionPlan::all();

        return view('super-admin.subscriptions', $this->withBreadcrumbs(compact('subscriptions', 'plans')));
    }

    public function show(int $id)
    {
        $subscription = Subscription::withoutWorkspace()->with('workspace', 'plan', 'invoices', 'payments.user')->findOrFail($id);

        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.subscriptions'), route('super.admin.subscriptions.index'), 'bi-credit-card')
            ->addBreadcrumb('#' . $subscription->id);

        $plans = SubscriptionPlan::all();

        return view('super-admin.subscription-show', $this->withBreadcrumbs(compact('subscription', 'plans')));
    }

    public function cancel(int $id): RedirectResponse
    {
        $subscription = Subscription::withoutWorkspace()->findOrFail($id);
        $this->subscriptionService->cancelSubscription($subscription);

        return redirect()->route('super.admin.subscriptions.show', $subscription->id)
            ->with('success', __('messages.subscription_canceled'));
    }

    public function toggleRenew(int $id): RedirectResponse
    {
        $subscription = Subscription::withoutWorkspace()->findOrFail($id);
        $subscription->update(['auto_renew' => !$subscription->auto_renew]);

        return redirect()->route('super.admin.subscriptions.show', $subscription->id)
            ->with('success', __('messages.subscription_updated'));
    }

    public function changePlan(Request $request, int $id): RedirectResponse
    {
        $subscription = Subscription::withoutWorkspace()->findOrFail($id);

        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $targetPlan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);
        $workspace = $subscription->workspace;

        if ($targetPlan->sort_order < $subscription->plan->sort_order) {
            $check = $this->subscriptionService->canDowngrade($workspace, $targetPlan);
            if (!$check['can_downgrade']) {
                return back()->with('error', implode(' ', $check['errors']));
            }
        }

        $result = $this->subscriptionService->changePlan(
            $workspace,
            $targetPlan->slug,
            $subscription->billing_period ?? 'monthly',
            null,
            $subscription->payment_method,
        );

        if (!$result['subscription']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('super.admin.subscriptions.show', $result['subscription']->id)
            ->with('success', $result['message']);
    }
}
