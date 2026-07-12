<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Coupon;

class DashboardController extends Controller
{
    use HasBreadcrumbs;

    public function index()
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded');

        $kpis = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'super_admins' => User::whereHas('roles', fn($q) => $q->where('slug', 'super_admin'))->count(),
            'total_workspaces' => Workspace::count(),
            'active_workspaces' => Workspace::where('is_active', true)->count(),
        ];

        $subscriptionCounts = SubscriptionPlan::withCount('subscriptions')->get()->pluck('subscriptions_count', 'slug');
        $kpis['subscriptions_by_plan'] = $subscriptionCounts;

        $kpis['active_subscriptions'] = Subscription::whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Trialing->value])->count();
        $kpis['canceled_subscriptions'] = Subscription::where('status', SubscriptionStatus::Canceled->value)->count();

        $kpis['total_revenue'] = Payment::where('status', PaymentStatus::CheckoutPaid->value)->sum('amount');
        $kpis['pending_payments'] = Payment::where('status', PaymentStatus::CheckoutPending->value)->count();
        $kpis['completed_payments'] = Payment::where('status', PaymentStatus::CheckoutPaid->value)->count();
        $kpis['pending_amount'] = Payment::where('status', PaymentStatus::CheckoutPending->value)->sum('amount');

        $kpis['recent_payments'] = Payment::where('status', PaymentStatus::CheckoutPaid->value)
            ->latest('paid_at')->take(5)->with('workspace', 'subscription.plan')->get();

        $thisMonth = now()->startOfMonth();
        $kpis['revenue_this_month'] = Payment::where('status', PaymentStatus::CheckoutPaid->value)
            ->where('paid_at', '>=', $thisMonth)->sum('amount');

        $kpis['total_coupons'] = Coupon::count();
        $kpis['active_coupons'] = Coupon::active()->count();
        $kpis['expired_coupons'] = Coupon::where('is_active', true)
            ->where('expires_at', '<', now())->count();
        $kpis['total_coupon_uses'] = Coupon::sum('used_count');

        $kpis['revenue_by_gateway'] = Payment::where('status', PaymentStatus::CheckoutPaid->value)
            ->selectRaw('method, SUM(amount) as total')
            ->groupBy('method')
            ->pluck('total', 'method');

        return view('super-admin.dashboard', $this->withBreadcrumbs(compact('kpis')));
    }
}
