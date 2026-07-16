<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxRateController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request)
    {
        return redirect()->route('super.admin.coupons-tax-rates.index');
    }

    public function create()
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.tax_rates'), route('super.admin.coupons-tax-rates.index'), 'bi-percent')
            ->addBreadcrumb(__('super-admin.create_tax_rate'));

        $paymentMethods = PaymentMethod::active()->get();
        $linkedPaymentMethods = collect();
        $taxRate = null;

        return view('super-admin.tax-rates-form', $this->withBreadcrumbs(compact('taxRate', 'paymentMethods', 'linkedPaymentMethods')));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:tax_rates,slug'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'type' => ['required', 'in:percentage,fixed'],
            'country' => ['nullable', 'string', 'max:2'],
            'region' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'links' => ['nullable', 'array'],
            'links.*' => ['array'],
            'links.*.*' => ['in:gateway_fee,tax_added,tax_disclosed'],
        ]);

        $taxRate = TaxRate::create($validated);

        $this->syncPaymentMethodLinks($taxRate, $request->input('links', []));

        return redirect()->route('super.admin.coupons-tax-rates.index')
            ->with('success', __('super-admin.tax_rate_created'));
    }

    public function edit(TaxRate $taxRate)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.tax_rates'), route('super.admin.coupons-tax-rates.index'), 'bi-percent')
            ->addBreadcrumb(__('super-admin.edit_tax_rate'));

        $paymentMethods = PaymentMethod::active()->get();

        $linkedPaymentMethods = DB::table('payment_method_tax_rate')
            ->where('tax_rate_id', $taxRate->id)
            ->select('payment_method_id', 'charge_type')
            ->get()
            ->groupBy('payment_method_id')
            ->map(fn ($items) => $items->pluck('charge_type')->values());

        return view('super-admin.tax-rates-form', $this->withBreadcrumbs(compact('taxRate', 'paymentMethods', 'linkedPaymentMethods')));
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:tax_rates,slug,'.$taxRate->id],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'type' => ['required', 'in:percentage,fixed'],
            'country' => ['nullable', 'string', 'max:2'],
            'region' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'links' => ['nullable', 'array'],
            'links.*' => ['array'],
            'links.*.*' => ['in:gateway_fee,tax_added,tax_disclosed'],
        ]);

        $taxRate->update($validated);

        $this->syncPaymentMethodLinks($taxRate, $request->input('links', []));

        return redirect()->route('super.admin.coupons-tax-rates.index')
            ->with('success', __('super-admin.tax_rate_updated'));
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();

        return redirect()->route('super.admin.coupons-tax-rates.index')
            ->with('success', __('super-admin.tax_rate_deleted'));
    }

    private function syncPaymentMethodLinks(TaxRate $taxRate, array $links): void
    {
        DB::table('payment_method_tax_rate')
            ->where('tax_rate_id', $taxRate->id)
            ->delete();

        foreach ($links as $pmId => $chargeTypes) {
            $pmId = (int) $pmId;
            if (! PaymentMethod::where('id', $pmId)->exists()) {
                continue;
            }
            foreach ($chargeTypes as $chargeType) {
                DB::table('payment_method_tax_rate')->insert([
                    'payment_method_id' => $pmId,
                    'tax_rate_id' => $taxRate->id,
                    'charge_type' => $chargeType,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
