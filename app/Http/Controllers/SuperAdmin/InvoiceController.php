<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.invoices'));

        $base = Invoice::withoutWorkspace();

        $countAll = (clone $base)->count();
        $countPaid = (clone $base)->where('status', InvoiceStatus::Paid)->count();
        $countOverdue = (clone $base)->where('status', InvoiceStatus::Overdue)->count();
        $countDraft = (clone $base)->where('status', InvoiceStatus::Draft)->count();
        $countCancelled = (clone $base)->where('status', InvoiceStatus::Cancelled)->count();

        $query = Invoice::withoutWorkspace()->with(['workspace', 'subscription' => function ($q) {
            $q->withoutWorkspace()->with('plan');
        }]);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('number', 'like', "%{$request->search}%");
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $invoices = $query->latest()->paginate($perPage);

        return view('super-admin.invoices', $this->withBreadcrumbs(compact(
            'invoices', 'countAll', 'countPaid', 'countOverdue', 'countDraft', 'countCancelled'
        )));
    }

    public function show(int $id)
    {
        $invoice = Invoice::withoutWorkspace()->with(['workspace', 'subscription' => function ($q) {
            $q->withoutWorkspace()->with('plan');
        }, 'user'])->findOrFail($id);

        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.invoices'), route('super.admin.invoices.index'), 'bi-receipt')
            ->addBreadcrumb($invoice->number);

        return view('super-admin.invoice-show', $this->withBreadcrumbs(compact('invoice')));
    }
}
