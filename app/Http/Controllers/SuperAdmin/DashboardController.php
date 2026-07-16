<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SuperAdminDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request, SuperAdminDashboardService $dashboard)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded');

        $tab = $request->input('tab', 'overview');
        $period = $request->input('period', 'all_time');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $data = [];

        switch ($tab) {
            case 'overview':
                $data = $dashboard->getOverviewKpis($period, $startDate, $endDate);
                $data['recent_payments'] = Payment::withoutWorkspace()
                    ->where('status', 'checkout.paid')->whereNull('refunded_at')
                    ->latest('paid_at')->take(5)->with('workspace', 'subscription.plan')->get();
                break;

            case 'revenue':
                $gateway = $request->input('gateway');
                $planId = $request->input('plan_id');
                $data = $dashboard->getRevenueStats($period, $startDate, $endDate, $gateway, $planId);
                $data['gateway_keys'] = Payment::withoutWorkspace()
                    ->where('status', 'checkout.paid')->whereNotNull('method')
                    ->distinct()->pluck('method')->toArray();
                $data['plan_options'] = SubscriptionPlan::pluck('name', 'id')->toArray();
                break;

            case 'subscriptions':
                $planId = $request->input('plan_id');
                $data = $dashboard->getSubscriptionStats($period, $startDate, $endDate, $planId);
                $data['plan_options'] = SubscriptionPlan::pluck('name', 'id')->toArray();
                break;

            case 'team':
                $memberId = $request->input('member_id');
                $data = $dashboard->getTeamPerformance($period, $startDate, $endDate, $memberId);
                $data['member_options'] = User::whereHas('roles', fn ($q) => $q->whereIn('slug', [
                    'super_admin', 'deputy_super_admin', 'platform_manager', 'billing_manager',
                    'support_team', 'technical_team', 'qa_team',
                ]))->pluck('name', 'id')->toArray();
                break;
        }

        $data['current_tab'] = $tab;
        $data['period'] = $period;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;

        return view('super-admin.dashboard', $this->withBreadcrumbs(['data' => $data]));
    }
}
