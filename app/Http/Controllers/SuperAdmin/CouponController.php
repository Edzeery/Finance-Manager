<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class CouponController extends Controller
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
            ->addBreadcrumb(__('super-admin.coupons'), route('super.admin.coupons-tax-rates.index'), 'bi-tags')
            ->addBreadcrumb(__('super-admin.create_coupon'));

        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('sort_order')->get();

        return view('super-admin.coupons-form', $this->withBreadcrumbs(['coupon' => null, 'paymentMethods' => $paymentMethods, 'linkedPaymentMethods' => collect()]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'is_active' => ['boolean'],
            'payment_methods' => ['nullable', 'array'],
            'payment_methods.*' => ['exists:payment_methods,id'],
        ]);

        $coupon = Coupon::create($validated);

        if ($request->has('payment_methods')) {
            $coupon->paymentMethods()->sync(array_map('intval', $request->input('payment_methods', [])));
        }

        return redirect()->route('super.admin.coupons-tax-rates.index')
            ->with('success', __('super-admin.coupon_created'));
    }

    public function edit(Coupon $coupon)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.coupons'), route('super.admin.coupons-tax-rates.index'), 'bi-tags')
            ->addBreadcrumb(__('super-admin.edit_coupon'));

        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('sort_order')->get();
        $linkedPaymentMethods = $coupon->paymentMethods()->pluck('payment_methods.id');

        return view('super-admin.coupons-form', $this->withBreadcrumbs(compact('coupon', 'paymentMethods', 'linkedPaymentMethods')));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code,'.$coupon->id],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'is_active' => ['boolean'],
            'payment_methods' => ['nullable', 'array'],
            'payment_methods.*' => ['exists:payment_methods,id'],
        ]);

        $coupon->update($validated);

        if ($request->has('payment_methods')) {
            $coupon->paymentMethods()->sync(array_map('intval', $request->input('payment_methods', [])));
        } else {
            $coupon->paymentMethods()->sync([]);
        }

        return redirect()->route('super.admin.coupons-tax-rates.index')
            ->with('success', __('super-admin.coupon_updated'));
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('super.admin.coupons-tax-rates.index')
            ->with('success', __('super-admin.coupon_deleted'));
    }
}
