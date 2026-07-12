<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Helpers\CurrencyHelper;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use App\Services\Payments\PaymentGatewayRegistry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentGatewayController extends Controller
{
    use HasBreadcrumbs;

    public function create(PaymentGatewayRegistry $registry)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.payment_methods'), route('super.admin.payment-methods.index'), 'bi-credit-card-2-front')
            ->addBreadcrumb(__('super-admin.create_gateway_structure'));

        return view('super-admin.payment-gateway-form', $this->withBreadcrumbs([
            'gateway' => null,
            'registry' => $registry,
        ]));
    }

    public function store(Request $request)
    {
        $validCodes = CurrencyHelper::availableCurrencyCodes() ?: ['DZD', 'USD', 'EUR'];

        $validated = $request->validate([
            'key' => ['required', 'string', 'max:50', 'regex:/^[a-z_]+$/', 'unique:payment_gateways,key'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'sandbox' => ['boolean'],
            'webhook' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'supported_currencies' => ['nullable', 'array'],
            'supported_currencies.*' => ['string', Rule::in($validCodes)],
            'fields' => ['nullable', 'array'],
        ]);

        $fields = $this->buildFields($request->input('fields', []));

        PaymentGateway::create([
            'key' => $validated['key'],
            'name' => $validated['name'],
            'category' => $validated['category'],
            'icon' => $validated['icon'] ?? null,
            'description' => $validated['description'] ?? null,
            'sandbox' => $request->boolean('sandbox'),
            'webhook' => $request->boolean('webhook'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'supported_currencies' => $validated['supported_currencies'] ?? ['DZD'],
            'fields' => $fields,
        ]);

        return redirect()->route('super.admin.payment-methods.index')
            ->with('success', __('super-admin.payment_method_created'));
    }

    public function edit(PaymentGateway $gateway, PaymentGatewayRegistry $registry)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.payment_methods'), route('super.admin.payment-methods.index'), 'bi-credit-card-2-front')
            ->addBreadcrumb(__('super-admin.edit_gateway_structure'));

        return view('super-admin.payment-gateway-form', $this->withBreadcrumbs([
            'gateway' => $gateway,
            'registry' => $registry,
        ]));
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        $validCodes = CurrencyHelper::availableCurrencyCodes() ?: ['DZD', 'USD', 'EUR'];

        $validated = $request->validate([
            'key' => ['required', 'string', 'max:50', 'regex:/^[a-z_]+$/', 'unique:payment_gateways,key,' . $gateway->id],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'sandbox' => ['boolean'],
            'webhook' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'supported_currencies' => ['nullable', 'array'],
            'supported_currencies.*' => ['string', Rule::in($validCodes)],
            'fields' => ['nullable', 'array'],
        ]);

        $requestFields = $request->input('fields', []);
        $existingFields = $gateway->fields ?? [];
        $fields = $this->buildFields($requestFields, $existingFields);

        $gateway->update([
            'key' => $validated['key'],
            'name' => $validated['name'],
            'category' => $validated['category'],
            'icon' => $validated['icon'] ?? null,
            'description' => $validated['description'] ?? null,
            'sandbox' => $request->boolean('sandbox'),
            'webhook' => $request->boolean('webhook'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'supported_currencies' => $validated['supported_currencies'] ?? ['DZD'],
            'fields' => $fields,
        ]);

        return redirect()->route('super.admin.payment-methods.index')
            ->with('success', __('super-admin.payment_method_updated'));
    }

    protected function buildFields(array $submitted, array $existing = []): array
    {
        $fields = [];
        foreach ($submitted as $i => $f) {
            if (empty($f['key'])) continue;
            $f['key'] = is_string($f['key']) ? $f['key'] : '';
            $f['type'] = in_array($f['type'] ?? 'text', ['text','password','textarea','email','url','number','select','boolean']) ? ($f['type'] ?? 'text') : 'text';
            $f['label'] = is_string($f['label'] ?? '') ? ($f['label'] ?? $f['key']) : $f['key'];
            $fields[] = [
                'key' => $f['key'],
                'type' => $f['type'],
                'label' => $f['label'],
                'required' => filter_var($f['required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'encrypted' => filter_var($f['encrypted'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'maxLength' => (int) ($f['maxLength'] ?? 255),
                'placeholder' => $f['placeholder'] ?? '',
                'help' => $f['help'] ?? '',
                'options' => $this->parseOptions($f['options'] ?? ($existing[$i]['options'] ?? [])),
                'default' => array_key_exists('default', $f) ? $f['default'] : ($existing[$i]['default'] ?? null),
                'rules' => $f['rules'] ?? ($existing[$i]['rules'] ?? []),
                'sensitive' => array_key_exists('sensitive', $f) ? filter_var($f['sensitive'], FILTER_VALIDATE_BOOLEAN) : ($existing[$i]['sensitive'] ?? false),
            ];
        }
        return $fields;
    }

    protected function parseOptions(array|string|null $options): array
    {
        if (is_array($options)) {
            return array_values(array_filter($options, function ($o) {
                return isset($o['value']) && trim((string) $o['value']) !== '';
            }));
        }
        if (is_string($options) && trim($options) !== '') {
            $result = [];
            foreach (explode("\n", $options) as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $parts = explode('|', $line, 2);
                $value = trim($parts[0]);
                $label = isset($parts[1]) ? trim($parts[1]) : $value;
                if ($value !== '') {
                    $result[] = ['value' => $value, 'label' => $label];
                }
            }
            return $result;
        }
        return [];
    }

    public function destroy(PaymentGateway $gateway)
    {
        $inUse = PaymentMethod::where('key', $gateway->key)->exists();

        if ($inUse) {
            return redirect()->back()->withErrors([
                'message' => __('super-admin.gateway_delete_protected'),
            ]);
        }

        $gateway->delete();

        return redirect()->route('super.admin.payment-methods.index')
            ->with('success', __('super-admin.payment_method_deleted'));
    }
}
