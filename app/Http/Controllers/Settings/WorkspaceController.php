<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Payments\GatewayManager;
use App\Services\SubscriptionService;
use App\Services\WorkspaceInvitationService;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    use HasBreadcrumbs;

    public function __construct(
        private SubscriptionService $subscriptionService,
        private WorkspaceService $workspaceService,
        private WorkspaceInvitationService $invitationService,
        private GatewayManager $gatewayManager,
    ) {}

    public function changePlan(Request $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        if (! $workspace || ! auth()->user()->isWorkspaceOwner($workspace)) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.unauthorized'));
        }

        $targetPlan = $this->subscriptionService->getPlan($request->input('plan_slug'));

        if (! $targetPlan) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.plan_not_found'));
        }

        $validated = $request->validate([
            'plan_slug' => ['required', 'string', 'exists:subscription_plans,slug'],
            'billing' => ['required', 'in:monthly,yearly'],
            'coupon' => ['nullable', 'string', 'max:50'],
            'payment_method' => [$targetPlan->isFree() ? 'nullable' : 'required', 'string', 'in:chargily,baridimob,cash,delivery,paypal,redotpay,wise,wise_manual,stripe,payoneer,noest'],
        ]);

        $currentPlan = $workspace->activePlan();
        $currentSub = $workspace->owner()?->first()?->activeSubscription();

        if ($currentPlan && $targetPlan->sort_order < $currentPlan->sort_order) {
            $check = $this->subscriptionService->canDowngrade($workspace, $targetPlan, $currentSub);
            if (! $check['can_downgrade']) {
                return redirect()->route('account.subscriptions')
                    ->with('error', implode(' ', $check['errors']));
            }
        }

        $result = $this->subscriptionService->changePlan(
            $workspace,
            $validated['plan_slug'],
            $validated['billing'],
            $validated['coupon'] ?? null,
            $validated['payment_method'] ?? null,
        );

        if (! $result['subscription']) {
            return redirect()->route('account.subscriptions')
                ->with('error', $result['message']);
        }

        if ($result['redirect_url']) {
            return redirect()->away($result['redirect_url']);
        }

        if ($result['payment']) {
            $payment = $result['payment'];

            $onlineMethods = ['chargily', 'paypal', 'stripe', 'wise', 'payoneer'];
            if (in_array($payment->method, $onlineMethods)) {
                return redirect()->route('payment.checkout', $payment);
            }

            return redirect()->route('onboarding.manual-proof', $payment)
                ->with('warning', $result['message']);
        }

        return redirect()->route('account.subscriptions')
            ->with('success', $result['message']);
    }

    public function cancel()
    {
        $workspace = auth()->user()->currentWorkspace;

        if (! $workspace || ! auth()->user()->isWorkspaceOwner($workspace)) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.unauthorized'));
        }

        $subscription = $workspace->owner()?->first()?->activeSubscription();
        if (! $subscription || $subscription->isExpired()) {
            return redirect()->route('account.subscriptions')
                ->with('error', __('messages.no_active_subscription'));
        }

        $this->subscriptionService->cancelSubscription($subscription, 'immediate');

        return redirect()->route('account.subscriptions')
            ->with('success', __('messages.subscription_canceled'));
    }

    public function invite(Request $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        if (! $workspace || ! auth()->user()->isWorkspaceOwner($workspace)) {
            return redirect()->route('settings.index')
                ->with('error', __('messages.unauthorized'));
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'in:workspace_deputy_admin,workspace_finance_manager,workspace_accountant,workspace_editor,workspace_viewer'],
        ]);

        try {
            $this->invitationService->invite(
                $workspace,
                auth()->user(),
                $validated['email'],
                $validated['role']
            );

            return redirect()->route('settings.index')
                ->with('success', __('messages.invite_sent'));
        } catch (\RuntimeException $e) {
            return redirect()->route('settings.index')
                ->with('error', $e->getMessage());
        }
    }

    public function changeRole(Request $request, User $user)
    {
        $workspace = auth()->user()->currentWorkspace;

        if (! $workspace || ! auth()->user()->isWorkspaceOwner($workspace)) {
            return redirect()->route('settings.index')
                ->with('error', __('messages.unauthorized'));
        }

        $validated = $request->validate([
            'role' => ['required', 'in:workspace_deputy_admin,workspace_finance_manager,workspace_accountant,workspace_editor,workspace_viewer'],
        ]);

        $changed = $this->workspaceService->changeRole($workspace, $user, $validated['role']);

        if (! $changed) {
            return redirect()->route('settings.index')
                ->with('error', __('messages.role_change_failed'));
        }

        return redirect()->route('settings.index')
            ->with('success', __('messages.role_changed'));
    }

    public function remove(User $user)
    {
        $workspace = auth()->user()->currentWorkspace;

        if (! $workspace || ! auth()->user()->isWorkspaceOwner($workspace)) {
            return redirect()->route('settings.index')
                ->with('error', __('messages.unauthorized'));
        }

        $removed = $this->workspaceService->removeUser($workspace, $user);

        if (! $removed) {
            return redirect()->route('settings.index')
                ->with('error', __('messages.remove_failed'));
        }

        return redirect()->route('settings.index')
            ->with('success', __('messages.removed'));
    }

    public function transferOwnership(Request $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        if (! $workspace || ! auth()->user()->isWorkspaceOwner($workspace)) {
            return redirect()->route('settings.index')
                ->with('error', __('messages.unauthorized'));
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $newOwner = User::findOrFail($validated['user_id']);

        $transferred = $this->workspaceService->transferOwnership($workspace, $newOwner);

        if (! $transferred) {
            return redirect()->route('settings.index')
                ->with('error', __('messages.transfer_failed'));
        }

        return redirect()->route('settings.index')
            ->with('success', __('messages.ownership_transferred'));
    }

    public function create()
    {
        $this->authorize('create', Workspace::class);

        return view('settings.create-workspace');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Workspace::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        try {
            $workspace = $this->workspaceService->createForUser(auth()->user(), [
                'name' => $validated['name'],
            ]);

            auth()->user()->switchWorkspace($workspace);

            return redirect()->route('dashboard')
                ->with('success', __('workspace.create_success'));
        } catch (\RuntimeException $e) {
            return redirect()->route('settings.workspace.create')
                ->with('error', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        if (! $workspace || ! auth()->user()->isWorkspaceOwner($workspace)) {
            return redirect()->route('settings.index')
                ->with('error', __('messages.unauthorized'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $workspace->update(['name' => $validated['name']]);

        return redirect()->route('settings.index')
            ->with('success', __('messages.settings_saved'));
    }
}
