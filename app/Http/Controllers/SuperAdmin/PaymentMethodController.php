<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use App\Models\TaxRate;
use App\Services\Payments\PaymentGatewayRegistry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.payment_methods'));

        $query = PaymentMethod::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('key', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $paymentMethods = $query->ordered()->paginate($perPage);

        $gateways = PaymentGateway::ordered()->get();

        return view('super-admin.payment-methods', $this->withBreadcrumbs(compact('paymentMethods', 'gateways')));
    }

    public function create(PaymentGatewayRegistry $registry)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.payment_methods'), route('super.admin.payment-methods.index'), 'bi-credit-card-2-front')
            ->addBreadcrumb(__('super-admin.create_payment_method'));

        $taxRates = TaxRate::active()->get();
        $linkedTaxRates = collect();

        return view('super-admin.payment-methods-form', $this->withBreadcrumbs([
            'paymentMethod' => null,
            'registry' => $registry,
            'currentCredentials' => [],
            'taxRates' => $taxRates,
            'linkedTaxRates' => $linkedTaxRates,
        ]));
    }

    public function store(Request $request, PaymentGatewayRegistry $registry)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:50', Rule::exists('payment_gateways', 'key'), 'unique:payment_methods,key'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:online,manual,auto_complete'],
            'is_active' => ['boolean'],
            'is_public' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $definition = $registry->find($validated['key']);

        if (!empty($request->input('credentials'))) {
            $credRules = ['credentials' => ['nullable', 'array']];
            foreach ($definition?->fields ?? [] as $field) {
                $credRules['credentials.' . $field->key] = $field->validationRules();
            }
            $request->validate($credRules);
        }

        $data = $validated;
        $data['name'] = $data['name'] ?: ($definition?->name ?? $data['key']);
        $data['description'] = $data['description'] ?: ($definition?->description ?? '');
        $data['icon'] = $data['icon'] ?: ($definition?->icon ?? '');
        if ($definition) {
            $data['credentials'] = $this->prepareCredentials($request->input('credentials', []), $definition);
        } else {
            $data['credentials'] = [];
        }

        $data['credentials'] = array_filter($data['credentials'] ?? [], fn($v) => $v !== null && $v !== '');

        $pm = PaymentMethod::create($data);

        // حفظ علاقات الضرائب والرسوم
        if ($request->has('tax_rate_links')) {
            $syncData = [];
            foreach ($request->input('tax_rate_links', []) as $taxRateId => $linkData) {
                if (!empty($linkData['charge_type'])) {
                    $syncData[(int) $taxRateId] = ['charge_type' => $linkData['charge_type']];
                }
            }
            $pm->taxRates()->sync($syncData);
        }

        return redirect()->route('super.admin.payment-methods.index')
            ->with('success', __('super-admin.payment_method_created'));
    }

    public function edit(PaymentMethod $paymentMethod, PaymentGatewayRegistry $registry)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.payment_methods'), route('super.admin.payment-methods.index'), 'bi-credit-card-2-front')
            ->addBreadcrumb(__('super-admin.edit_payment_method'));

        $taxRates = TaxRate::active()->get();
        $linkedTaxRates = $paymentMethod->taxRates()->withPivot('charge_type')->get()->keyBy('id');

        return view('super-admin.payment-methods-form', $this->withBreadcrumbs([
            'paymentMethod' => $paymentMethod,
            'registry' => $registry,
            'currentCredentials' => $paymentMethod->credentials ?? [],
            'taxRates' => $taxRates,
            'linkedTaxRates' => $linkedTaxRates,
        ]));
    }

    public function update(Request $request, PaymentMethod $paymentMethod, PaymentGatewayRegistry $registry)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:50', Rule::exists('payment_gateways', 'key'), 'unique:payment_methods,key,' . $paymentMethod->id],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:online,manual,auto_complete'],
            'is_active' => ['boolean'],
            'is_public' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $definition = $registry->find($validated['key']);

        if (!empty($request->input('credentials'))) {
            $credRules = ['credentials' => ['nullable', 'array']];
            foreach ($definition?->fields ?? [] as $field) {
                $credRules['credentials.' . $field->key] = ['nullable'];
            }
            $request->validate($credRules);
        }

        $data = $validated;
        $data['name'] = $data['name'] ?: ($definition?->name ?? $data['key']);
        $data['description'] = $data['description'] ?: ($definition?->description ?? '');
        $data['icon'] = $data['icon'] ?: ($definition?->icon ?? '');
        $existing = $paymentMethod->credentials ?? [];
        $raw = $request->input('credentials', []);
        $merged = [];

        foreach ($definition?->fields ?? [] as $field) {
            $key = $field->key;
            if (array_key_exists($key, $raw)) {
                if ($raw[$key] !== null && $raw[$key] !== '') {
                    $merged[$key] = ($field->encrypted) ? encrypt($raw[$key]) : $raw[$key];
                } elseif (array_key_exists($key, $existing)) {
                    $merged[$key] = $existing[$key];
                }
            }
        }

        $data['credentials'] = $merged;

        $paymentMethod->update($data);

        // حفظ علاقات الضرائب والرسوم (tax_rates)
        if ($request->has('tax_rate_links')) {
            $syncData = [];
            foreach ($request->input('tax_rate_links', []) as $taxRateId => $linkData) {
                if (!empty($linkData['charge_type'])) {
                    $syncData[(int) $taxRateId] = ['charge_type' => $linkData['charge_type']];
                }
            }
            $paymentMethod->taxRates()->sync($syncData);
        } else {
            $paymentMethod->taxRates()->sync([]);
        }

        return redirect()->route('super.admin.payment-methods.index')
            ->with('success', __('super-admin.payment_method_updated'));
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->route('super.admin.payment-methods.index')
            ->with('success', __('super-admin.payment_method_deleted'));
    }

    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);

        return redirect()->back()->with('success', $paymentMethod->is_active
            ? __('super-admin.payment_method_updated')
            : __('super-admin.payment_method_updated'));
    }

    public function togglePublic(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_public' => !$paymentMethod->is_public]);

        return redirect()->back()->with('success', $paymentMethod->is_public
            ? __('super-admin.payment_method_updated')
            : __('super-admin.payment_method_updated'));
    }

    private function prepareCredentials(array $rawCredentials, $definition): array
    {
        $prepared = [];

        foreach ($rawCredentials as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $field = $definition->field($key);
            $prepared[$key] = ($field && $field->encrypted) ? encrypt($value) : $value;
        }

        return $prepared;
    }
}
