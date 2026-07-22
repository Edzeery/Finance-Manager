<?php

namespace App\Http\Controllers\Account;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Services\CurrencyHelper;
use Barryvdh\DomPDF\Facades\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $subscriptionIds = Subscription::withoutWorkspace()
            ->where('user_id', $user->id)
            ->pluck('id');

        if ($subscriptionIds->isEmpty()) {
            $hasSubscriptions = false;
            $invoices = collect();
            $countAll = $countPaid = $countOverdue = $countDraft = $countCancelled = 0;
        } else {
            $hasSubscriptions = true;
            $query = Invoice::withoutWorkspace()
                ->whereIn('subscription_id', $subscriptionIds)
                ->with('subscription.plan');

            $status = $request->input('status');
            if ($status && in_array($status, array_map(fn ($case) => $case->value, InvoiceStatus::cases()))) {
                $query->where('status', $status);
            }

            $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
            $invoices = $query->latest()->paginate($perPage);

            $countAll = Invoice::withoutWorkspace()->whereIn('subscription_id', $subscriptionIds)->count();
            $countPaid = Invoice::withoutWorkspace()->whereIn('subscription_id', $subscriptionIds)->paid()->count();
            $countOverdue = Invoice::withoutWorkspace()->whereIn('subscription_id', $subscriptionIds)->overdue()->count();
            $countDraft = Invoice::withoutWorkspace()->whereIn('subscription_id', $subscriptionIds)->draft()->count();
            $countCancelled = Invoice::withoutWorkspace()->whereIn('subscription_id', $subscriptionIds)->cancelled()->count();
        }

        $userCurrency = $user->currency ?? config('finance.currency', 'USD');

        return view('account.invoices-index', compact(
            'invoices', 'hasSubscriptions',
            'countAll', 'countPaid', 'countOverdue', 'countDraft', 'countCancelled',
            'userCurrency'
        ));
    }

    public function show(Invoice $invoice)
    {
        $user = auth()->user();

        $subscriptionIds = Subscription::withoutWorkspace()
            ->where('user_id', $user->id)
            ->pluck('id');

        abort_if($subscriptionIds->isEmpty() || ! in_array($invoice->subscription_id, $subscriptionIds->toArray()), 403);

        $invoice->load('subscription.plan', 'user');
        $userCurrency = $user->currency ?? config('finance.currency', 'USD');

        return view('account.invoices-show', compact('invoice', 'userCurrency'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        $user = auth()->user();

        $subscriptionIds = Subscription::withoutWorkspace()
            ->where('user_id', $user->id)
            ->pluck('id');

        abort_if($subscriptionIds->isEmpty() || ! in_array($invoice->subscription_id, $subscriptionIds->toArray()), 403);

        $invoice->load('subscription.plan', 'user');
        $userCurrency = $user->currency ?? config('finance.currency', 'USD');

        $displayPrice = function (float $amount, ?string $currency = null) use ($userCurrency) {
            $cur = $currency ?: $userCurrency;

            return number_format($amount, 2).' '.CurrencyHelper::symbol($cur);
        };

        $pdf = Pdf::loadView('account.invoices-pdf', compact('invoice', 'userCurrency', 'displayPrice'));

        return $pdf->download("{$invoice->number}.pdf");
    }
}
