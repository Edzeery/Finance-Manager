<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\Payments\PaymentGatewayRegistry;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request, PaymentGatewayRegistry $registry)
    {
        $user = auth()->user();

        $subscriptionIds = Subscription::withoutWorkspace()
            ->where('user_id', $user->id)
            ->pluck('id');

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));

        $payments = $subscriptionIds->isNotEmpty()
            ? Payment::withoutWorkspace()
                ->whereIn('subscription_id', $subscriptionIds)
                ->latest()
                ->paginate($perPage)
            : collect();

        $userCurrency = $user->currency ?? config('finance.currency', 'USD');
        $gateways = $registry->all();

        return view('account.payments', compact('payments', 'userCurrency', 'gateways'));
    }
}
