<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $status = $request->input('status', 'all');
        $query = Subscription::withoutWorkspace()->with('workspace', 'plan');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

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

        if ($request->filled('plan_id')) {
            $query->where('subscription_plan_id', $request->plan_id);
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $subscriptions = $query->latest('starts_at')->paginate($perPage);

        $plans = SubscriptionPlan::all();
        $countAll = Subscription::withoutWorkspace()->count();
        $countActive = Subscription::withoutWorkspace()->where('status', 'active')->count();
        $countTrialing = Subscription::withoutWorkspace()->where('status', 'trialing')->count();
        $countPastDue = Subscription::withoutWorkspace()->where('status', 'past_due')->count();
        $countCanceled = Subscription::withoutWorkspace()->where('status', 'canceled')->count();
        $countExpired = Subscription::withoutWorkspace()->where('status', 'expired')->count();

        $planSubTabs = collect(['' => ['label' => __('general.all')]])
            ->union($plans->mapWithKeys(fn ($p) => [$p->id => ['label' => $p->name]]))
            ->toArray();

        return view('super-admin.subscriptions', $this->withBreadcrumbs(compact(
            'subscriptions', 'plans', 'countAll', 'countActive', 'countTrialing',
            'countPastDue', 'countCanceled', 'countExpired', 'planSubTabs'
        )));
    }

    public function show(int $id)
    {
        $subscription = Subscription::withoutWorkspace()->with('workspace', 'plan', 'invoices', 'payments.user')->findOrFail($id);

        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.subscriptions'), route('super.admin.subscriptions.index'), 'bi-credit-card')
            ->addBreadcrumb('#'.$subscription->id);

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
        $subscription->update(['auto_renew' => ! $subscription->auto_renew]);

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
            if (! $check['can_downgrade']) {
                return back()->with('error', implode(' ', $check['errors']));
            }
        }

        $result = $this->subscriptionService->changePlan(
            $workspace,
            $targetPlan->slug,
            $subscription->billing_period ?? 'monthly',
            null,
            $subscription->paymentMethod?->key ?? $subscription->payment_method,
        );

        if (! $result['subscription']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('super.admin.subscriptions.show', $result['subscription']->id)
            ->with('success', $result['message']);
    }

    public function updateStatus(Request $request, int $id)
    {
        $subscription = Subscription::withoutWorkspace()->findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,past_due,expired,canceled,trialing'],
        ]);

        $newStatus = SubscriptionStatus::from($validated['status']);

        DB::transaction(function () use ($subscription, $newStatus) {
            $updates = ['status' => $newStatus];

            if ($newStatus->isTerminal()) {
                $updates['canceled_at'] = $subscription->canceled_at ?? now();
                $updates['ends_at'] = $subscription->ends_at ?? now();
            }

            if ($newStatus === SubscriptionStatus::Active && ! $subscription->starts_at) {
                $updates['starts_at'] = now();
            }

            $subscription->update($updates);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $subscription->fresh()->status->value,
                'badge' => (string) view('vendor.status-kit.components.status-badge', [
                    'domain' => 'subscription',
                    'status' => $subscription->fresh()->status->value,
                    'set' => 'bi',
                ]),
            ]);
        }

        return back()->with('success', __('messages.subscription_updated'));
    }

    public function updateAutoRenew(Request $request, int $id)
    {
        $subscription = Subscription::withoutWorkspace()->findOrFail($id);
        $subscription->update(['auto_renew' => ! $subscription->auto_renew]);

        if ($request->expectsJson()) {
            $subscription->refresh();

            return response()->json([
                'success' => true,
                'auto_renew' => $subscription->auto_renew,
                'badge' => (string) view('vendor.status-kit.components.status-badge', [
                    'domain' => 'general',
                    'status' => $subscription->auto_renew ? 'yes' : 'no',
                    'set' => 'bi',
                ]),
            ]);
        }

        return back()->with('success', __('messages.subscription_updated'));
    }

    public function updatePlan(Request $request, int $id)
    {
        $subscription = Subscription::withoutWorkspace()->findOrFail($id);

        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $targetPlan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);
        $workspace = $subscription->workspace;

        if ($targetPlan->sort_order < $subscription->plan->sort_order) {
            $check = $this->subscriptionService->canDowngrade($workspace, $targetPlan);
            if (! $check['can_downgrade']) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => implode(' ', $check['errors'])], 422);
                }
                return back()->with('error', implode(' ', $check['errors']));
            }
        }

        $result = $this->subscriptionService->adminForceChangePlan(
            $workspace,
            $targetPlan->slug,
            $subscription->billing_period ?? 'monthly',
        );

        if (! $result['subscription']) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $result['message']], 422);
            }
            return back()->with('error', $result['message']);
        }

        if ($request->expectsJson()) {
            $sub = $result['subscription']->fresh('plan');

            return response()->json([
                'success' => true,
                'plan_name' => $sub->plan?->name ?? '—',
                'subscription_id' => $sub->id,
                'invoice_number' => $result['invoice']?->number,
                'payment_amount' => $result['payment']?->amount,
            ]);
        }

        return redirect()->route('super.admin.subscriptions.show', $result['subscription']->id)
            ->with('success', $result['message']);
    }
}
