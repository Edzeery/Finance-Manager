<?php
//resources\views\livewire\pages\onboarding\payment.blade.php
use App\Enums\PaymentStatus;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\SubscriptionPlan;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\Payments\Noest\NoestErrorHandler;
use App\Services\Payments\Noest\NoestService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public ?string $paymentMethod = null;
    public string $billingPeriod = 'monthly';
    public ?string $couponCode = null;
    public ?array $plan = null;
    public bool $isProcessing = false;
    public ?string $redirectUrl = null;
    public ?string $errorMessage = null;
    public ?array $couponValidation = null;

    public string $noestClient = '';
    public string $noestPhone = '';
    public string $noestPhone2 = '';
    public string $noestAdresse = '';
    public string $noestWilaya = '';
    public string $noestDeskId = '';
    public array $noestWilayas = [];
    public array $noestDesks = [];

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user->pending_plan_id) {
            $this->redirect(route('onboarding.plan', absolute: false), navigate: true);
            return;
        }

        $plan = SubscriptionPlan::with('planFeatures')->find($user->pending_plan_id);
        if (!$plan || $plan->is_free) {
            $this->redirect(route('onboarding.setup', absolute: false), navigate: true);
            return;
        }

        $this->cancelStalePendingPayments($user);

        $plan->load('activePrices');
        $planArr = $plan->toArray();
        $planArr['_features'] = $plan->planFeatures->map(fn($f) => [
            'slug' => $f->slug,
            'name_en' => $f->name_en,
            'name_ar' => $f->name_ar,
            'name_fr' => $f->name_fr,
            'icon' => $f->icon,
            'type' => $f->type,
            'value' => $f->pivot->value,
        ])->toArray();
        $planArr['_prices'] = $plan->activePrices->toArray();
        $monthlyFromPrices = collect($planArr['_prices'])->firstWhere('billing_period', 'monthly');
        $yearlyFromPrices = collect($planArr['_prices'])->firstWhere('billing_period', 'yearly');
        if ($monthlyFromPrices) $planArr['monthly_price'] = (float) $monthlyFromPrices['price'];
        if ($yearlyFromPrices) $planArr['yearly_price'] = (float) $yearlyFromPrices['price'];
        $this->plan = $planArr;
        $this->noestClient = $user->name ?? '';
        $this->noestPhone = $user->phone ?? '';
        $this->loadNoestData();
    }

    private function cancelStalePendingPayments($user): void
    {
        $stale = Payment::where('user_id', $user->id)
            ->where('status', PaymentStatus::CheckoutPending->value)
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        foreach ($stale as $payment) {
            $payment->update(['status' => PaymentStatus::CheckoutCanceled, 'canceled_at' => now()]);
        }
    }

    public function loadNoestData(): void
    {
        try {
            $service = app(NoestService::class);

            $wilayasRaw = $service->getWilayas();
            $wilayasList = $wilayasRaw['data'] ?? (isset($wilayasRaw[0]) ? $wilayasRaw : []);

            $this->noestWilayas = collect($wilayasList)
                ->map(fn($w) => [
                    'code' => (string)($w['code'] ?? $w['id'] ?? $w['wilaya_id'] ?? ''),
                    'nom'  => $w['nom'] ?? $w['name'] ?? $w['wilaya_name'] ?? '',
                ])
                ->filter(fn($w) => $w['code'] !== '' && $w['nom'] !== '')
                ->values()
                ->toArray();

            $desksRaw = $service->getDesks();

            $desksList = [];
            foreach (($desksRaw['data'] ?? $desksRaw) as $key => $item) {
                if (is_array($item) && !empty($item['code'])) {
                    $item['_key'] = $key;
                    $desksList[] = $item;
                }
            }

            Log::debug('Noest desks raw (payment)', [
                'count_raw' => count($desksRaw),
                'count_list' => count($desksList),
                'first_key' => array_key_first($desksRaw),
                'sample' => json_encode($desksList[0] ?? $desksRaw, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ]);

            $this->noestDesks = collect($desksList)
                ->map(fn($d) => [
                    'code'    => (string)($d['code'] ?? ''),
                    'nom'     => $d['name'] ?? $d['desk_name'] ?? $d['nom'] ?? '',
                    'wilaya'  => (function() use ($d) {
                        preg_match('/^(\d+)/', $d['_key'] ?? $d['code'] ?? '', $m);
                        return (string)(int)$m[1];
                    })(),
                ])
                ->filter(fn($d) => $d['code'] !== '' && $d['nom'] !== '')
                ->values()
                ->toArray();

            Log::debug('Noest data loaded (payment)', [
                'wilayas_count' => count($this->noestWilayas),
                'desks_count'   => count($this->noestDesks),
                'sample_desk'   => $this->noestDesks[0] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Noest loadNoestData failed', [
                'error'    => $e->getMessage(),
                'token_ok' => !empty(config('payment.gateways.noest.api_token')),
                'base_url' => config('payment.gateways.noest.base_url'),
            ]);
        }
    }

    public function updatedNoestWilaya(): void
    {
        $this->noestDeskId = '';
    }

    public function noestDesksForWilaya(): array
    {
        if (!$this->noestWilaya) return [];
        $wilaya = (int)$this->noestWilaya;

        return array_values(array_filter(
            $this->noestDesks,
            fn($d) => is_array($d) && (int)($d['wilaya'] ?? 0) === $wilaya
        ));
    }

    public function updatedCouponCode(): void
    {
        $this->validateCouponCode();
    }

    public function updatedPaymentMethod(): void
    {
        $this->validateCouponCode();
    }

    public function validateCouponCode(): void
    {
        $this->couponValidation = null;

        $code = trim($this->couponCode ?? '');
        if (!$code || !$this->plan) return;

        $priceUsd = $this->billingPeriod === 'yearly'
            ? (float) ($this->plan['yearly_price'] ?? 0)
            : (float) ($this->plan['monthly_price'] ?? 0);

        if ($priceUsd <= 0) return;

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            $this->couponValidation = ['valid' => false, 'message' => __('onboarding.coupon_not_found')];
            return;
        }

        if (!$coupon->is_active) {
            $this->couponValidation = ['valid' => false, 'message' => __('onboarding.coupon_inactive')];
            return;
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            $this->couponValidation = ['valid' => false, 'message' => __('onboarding.coupon_expired')];
            return;
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            $this->couponValidation = ['valid' => false, 'message' => __('onboarding.coupon_not_started')];
            return;
        }

        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            $this->couponValidation = ['valid' => false, 'message' => __('onboarding.coupon_max_uses')];
            return;
        }

        if ($coupon->min_amount > 0 && $priceUsd < $coupon->min_amount) {
            $this->couponValidation = [
                'valid' => false,
                'message' => __('onboarding.coupon_min_amount', ['amount' => $this->displayPrice($coupon->min_amount)]),
            ];
            return;
        }

        if ($coupon->paymentMethods()->exists()) {
            if (!$this->paymentMethod) {
                $this->couponValidation = [
                    'valid' => false,
                    'message' => __('onboarding.coupon_requires_gateway'),
                ];
                return;
            }
            $pm = \App\Models\PaymentMethod::where('key', $this->paymentMethod)->first();
            if ($pm && !$coupon->paymentMethods()->where('payment_method_id', $pm->id)->exists()) {
                $this->couponValidation = [
                    'valid' => false,
                    'message' => __('onboarding.coupon_not_for_gateway'),
                ];
                return;
            }
        }

        $discountUsd = $coupon->applyDiscount($priceUsd);
        $finalUsd = max($priceUsd - $discountUsd, 0);

        $this->couponValidation = [
            'valid' => true,
            'message' => $finalUsd <= 0
                ? __('onboarding.coupon_fully_covered')
                : __('onboarding.coupon_applied'),
            'discount_usd' => $discountUsd,
            'final_usd' => $finalUsd,
            'original_usd' => $priceUsd,
            'code' => $coupon->code,
        ];
    }

    public function pay(): void
    {
        $rules = ['paymentMethod' => 'required'];

        if ($this->paymentMethod === 'noest') {
            $rules['noestClient'] = ['required', 'string', 'max:255'];
            $rules['noestPhone'] = ['required', 'string', 'regex:/^(05|06|07)[0-9]{8}$/'];
            $rules['noestWilaya'] = ['required', 'string'];
            $rules['noestAdresse'] = ['required', 'string', 'max:500'];
        }

        $this->validate($rules);

        $this->isProcessing = true;
        $this->errorMessage = null;

        try {
            $gatewayData = $this->paymentMethod === 'noest'
                ? [
                    'noest_client'        => trim($this->noestClient),
                    'noest_phone'         => trim($this->noestPhone),
                    'noest_phone_2'       => trim($this->noestPhone2),
                    'noest_adresse'       => trim($this->noestAdresse),
                    'noest_wilaya'        => $this->noestWilaya,
                    'noest_stop_desk'     => (bool)$this->noestDeskId,
                    'noest_station_code'  => $this->noestDeskId ?: null,
                    'noest_remboursement' => true,
                    'noest_can_open'      => false,
                ]
                : [];

            $payment = app(OnboardingService::class)->initiatePaidPlanPayment(
                auth()->user(),
                $this->paymentMethod,
                $this->billingPeriod,
                $this->couponCode,
                $gatewayData,
            );

            if (!$payment) {
                $this->errorMessage = __('onboarding.payment_init_failed');
                $this->isProcessing = false;
                return;
            }

            if ($payment->isCompleted()) {
                $this->redirect(route('onboarding.setup', absolute: false), navigate: true);
                return;
            }

            $isManual = OnboardingService::isManual($this->paymentMethod);

            if ($isManual) {
                $this->redirect(
                    route('onboarding.manual-proof', ['payment' => $payment->id], absolute: false),
                    navigate: true,
                );
                return;
            }

            $redirectUrl = $payment->metadata['redirect_url'] ?? null;

            if ($redirectUrl) {
                $this->js("window.location.href = '" . addslashes($redirectUrl) . "'");
                return;
            }

            $this->errorMessage = __('onboarding.payment_init_failed');
            $this->isProcessing = false;
        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'user_id'   => auth()->id(),
                'plan_id'   => $this->plan['id'] ?? null,
                'method'    => $this->paymentMethod,
                'wilaya'    => $this->noestWilaya,
                'desk_id'   => $this->noestDeskId,
                'error'     => $e->getMessage(),
            ]);

            if ($this->paymentMethod === 'noest') {
                $this->errorMessage = NoestErrorHandler::translate($e->getMessage());
            } else {
                $this->errorMessage = __('onboarding.payment_init_failed');
            }
            $this->isProcessing = false;
        }
    }

    public function getPaymentMethodsProperty(): array
    {
        $currency = session('currency', auth()->user()?->currency ?? config('finance.currency', 'DZD'));

        return PaymentMethod::active()->public()->byCurrency($currency)->ordered()->get()
            ->map(fn($m) => [
            'id' => $m->key,
            'name' => __("onboarding.method_{$m->key}") !== "onboarding.method_{$m->key}"
                ? __("onboarding.method_{$m->key}")
                : $m->name,
            'icon' => $m->icon ?? 'bi-credit-card',
        ])->toArray();
    }

    public function getFeeBreakdownProperty(): ?array
    {
        if (!$this->paymentMethod || !$this->plan) return null;

        $priceUsd = $this->billingPeriod === 'yearly'
            ? (float) ($this->plan['yearly_price'] ?? 0)
            : (float) ($this->plan['monthly_price'] ?? 0);

        $discountUsd = $this->couponValidation['discount_usd'] ?? 0;
        $finalUsd = max($priceUsd - $discountUsd, 0);

        $pmModel = \App\Models\PaymentMethod::where('key', $this->paymentMethod)->first();
        if (!$pmModel) return null;

        $gatewayFeeUsd = 0.0;
        $taxAddedUsd = 0.0;
        $taxDisclosedUsd = 0.0;

        $links = $pmModel->taxRates()->withPivot('charge_type')->get();
        foreach ($links as $taxRate) {
            $calculated = $taxRate->calculateForAmount($finalUsd);
            match ($taxRate->pivot->charge_type) {
                'gateway_fee' => $gatewayFeeUsd += $calculated,
                'tax_added' => $taxAddedUsd += $calculated,
                'tax_disclosed' => $taxDisclosedUsd += $calculated,
            };
        }

        return [
            'gateway_fee_usd' => $gatewayFeeUsd,
            'tax_added_usd' => $taxAddedUsd,
            'tax_disclosed_usd' => $taxDisclosedUsd,
            'total_usd' => $finalUsd + $gatewayFeeUsd + $taxAddedUsd,
            'original_usd' => $priceUsd,
            'discount_usd' => $discountUsd,
            'final_after_discount_usd' => $finalUsd,
        ];
    }

    public function displayPrice(float $usdAmount): string
    {
        $currency = auth()->user()?->currency ?? config('finance.currency', 'DZD');
        $converted = CurrencyHelper::fromUsd($usdAmount, $currency);
        return number_format($converted, 2) . ' ' . CurrencyHelper::symbol($currency);
    }
}; ?>

<div class="auth-card animate-fade-in" x-data="noestForm()">
    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text">{{ __('general.app_name') }}</span>
        <span class="logo-sub">{{ __('onboarding.payment') }}</span>
    </div>

    @if ($plan)
        @php
            $usdPrice = $billingPeriod === 'yearly' ? $plan['yearly_price'] : $plan['monthly_price'];
            $userCurrency = auth()->user()?->currency ?? config('finance.currency', 'DZD');
            $converted = CurrencyHelper::fromUsd($usdPrice, $userCurrency);
        @endphp
        <div class="text-center mb-3">
            <h5>{{ $plan['name'] }}</h5>
            <p class="h3 mb-2">
                {{ number_format($converted, 2) }}
                <small>{{ CurrencyHelper::symbol($userCurrency) }}</small>
            </p>
        </div>
        @php $_features = $plan['_features'] ?? []; @endphp
        @if (count($_features))
            <div class="mb-4" x-data="{ showAll: false }">
                <div class="plan-features" style="border:1px solid var(--border);border-radius:8px;padding:12px 16px;">
                    <div style="font-weight:600;font-size:13px;margin-bottom:8px;color:var(--text-muted)">{{ __('onboarding.what_included') }}</div>
                    @foreach ($_features as $i => $feat)
                        @if ($i >= 5)
                            <div x-show="showAll" x-cloak style="border-bottom:1px solid var(--border);padding:6px 0">
                                <span style="font-size:13px">
                                    @if ($feat['icon'])
                                        <i class="{{ $feat['icon'] }}" style="margin-inline-end:6px;color:var(--accent)"></i>
                                    @else
                                        <i class="bi bi-check-circle-fill" style="margin-inline-end:6px;color:var(--accent);font-size:12px"></i>
                                    @endif
                                    @php
                                        $_nameKey = 'name_' . app()->getLocale();
                                        $_name = $feat[$_nameKey] ?? $feat['name_en'];
                                    @endphp
                                    @if ($feat['type'] === 'boolean')
                                        {{ $_name }}
                                    @elseif ($feat['value'])
                                        {{ $feat['value'] }} {{ $_name }}
                                    @else
                                        {{ $_name }}
                                    @endif
                                </span>
                            </div>
                        @else
                            <div style="border-bottom:1px solid var(--border);padding:6px 0">
                                <span style="font-size:13px">
                                    @if ($feat['icon'])
                                        <i class="{{ $feat['icon'] }}" style="margin-inline-end:6px;color:var(--accent)"></i>
                                    @else
                                        <i class="bi bi-check-circle-fill" style="margin-inline-end:6px;color:var(--accent);font-size:12px"></i>
                                    @endif
                                    @php
                                        $_nameKey = 'name_' . app()->getLocale();
                                        $_name = $feat[$_nameKey] ?? $feat['name_en'];
                                    @endphp
                                    @if ($feat['type'] === 'boolean')
                                        {{ $_name }}
                                    @elseif ($feat['value'])
                                        {{ $feat['value'] }} {{ $_name }}
                                    @else
                                        {{ $_name }}
                                    @endif
                                </span>
                            </div>
                        @endif
                    @endforeach
                    @if (count($_features) > 5)
                        <button type="button" @click="showAll = !showAll" style="background:none;border:none;color:var(--accent);font-size:12px;padding:8px 0 0;cursor:pointer;width:100%;text-align:center">
                            <span x-show="!showAll">{{ __('onboarding.show_more') }} ({{ count($_features) - 5 }}) <i class="bi bi-chevron-down"></i></span>
                            <span x-show="showAll" x-cloak>{{ __('onboarding.show_less') }} <i class="bi bi-chevron-up"></i></span>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    @endif

    @if ($errorMessage)
        <div class="alert alert-danger py-2 small" style="white-space:pre-wrap;">{{ $errorMessage }}</div>
    @endif

    <div class="mb-4">
        <label class="form-label-custom">{{ __('onboarding.payment_method') }}</label>
        <div style="font-size:12px;color:var(--text-muted,#888);margin-bottom:8px;display:flex;align-items:center;gap:6px">
            <i class="bi bi-info-circle"></i>
            {{ __('onboarding.payment_methods_for_currency', ['currency' => \App\Services\CurrencyHelper::symbol(session('currency', auth()->user()?->currency ?? config('finance.currency', 'DZD')))]) }}
        </div>
        @foreach ($this->paymentMethods as $method)
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="paymentMethod"
                    id="method-{{ $method['id'] }}" value="{{ $method['id'] }}"
                    wire:model="paymentMethod">
                <label class="form-check-label d-flex align-items-center gap-2" for="method-{{ $method['id'] }}">
                    <i class="{{ $method['icon'] }}"></i>
                    {{ $method['name'] }}
                </label>
            </div>
        @endforeach
        @error('paymentMethod') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    @if ($paymentMethod === 'noest')
        <div class="noest-form mb-4">
            <div class="noest-form-header">
                <i class="bi bi-truck"></i>
                <span>{{ __('onboarding.noest_delivery_info') }}</span>
            </div>

            <div class="form-floating-group mb-3">
                <input type="text" id="noest_client" class="form-control" wire:model="noestClient" placeholder=" " @disabled($isProcessing)>
                <label for="noest_client">{{ __('onboarding.noest_client') }} <span class="text-danger">*</span></label>
                @error('noestClient') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <div class="form-floating-group">
                        <input type="text" id="noest_phone" class="form-control" wire:model="noestPhone" placeholder=" " @disabled($isProcessing)>
                        <label for="noest_phone">{{ __('onboarding.noest_phone') }} <span class="text-danger">*</span></label>
                        @error('noestPhone') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating-group">
                        <input type="text" id="noest_phone_2" class="form-control" wire:model="noestPhone2" placeholder=" " @disabled($isProcessing)>
                        <label for="noest_phone_2">{{ __('onboarding.noest_phone_2') }}</label>
                    </div>
                </div>
            </div>

            <div class="form-floating-group mb-3">
                <input type="text" id="noest_adresse" class="form-control" wire:model="noestAdresse" placeholder=" " @disabled($isProcessing)>
                <label for="noest_adresse">{{ __('onboarding.noest_adresse') }} <span class="text-danger">*</span></label>
                @error('noestAdresse') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- Wilaya searchable select --}}
            @php
                $_wilayaItems = array_values(array_map(fn($w) => [
                    'code' => (string) ($w['code'] ?? $w['id'] ?? ''),
                    'nom'  => $w['nom'] ?? $w['name'] ?? '',
                ], $this->noestWilayas));
            @endphp
            <div class="form-floating-group mb-3">
                <div class="noest-search-group"
                     x-data="{ search: '', items: [] }"
                     x-init="items = JSON.parse($el.dataset.items)"
                     data-items="{{ json_encode($_wilayaItems) }}">
                    <div class="form-floating">
                        <input type="text" x-model="search" class="form-control" id="noest_wilaya_search" placeholder="{{ __('onboarding.noest_search_wilaya') }}" autocomplete="off" @if($isProcessing) disabled @endif>
                        <label for="noest_wilaya_search">{{ __('onboarding.noest_wilaya') }} <span class="text-danger">*</span></label>
                    </div>
                    <select wire:model.live="noestWilaya" class="form-select" @if($isProcessing) disabled @endif>
                        <option value="">-- {{ __('onboarding.noest_select_wilaya') }} --</option>
                        <template x-for="item in items.filter(i => !search || i.nom.toLowerCase().includes(search.toLowerCase()) || i.code.includes(search))" :key="item.code">
                            <option :value="item.code" x-text="item.code + ' - ' + item.nom"></option>
                        </template>
                    </select>
                </div>
                @error('noestWilaya') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- Desk searchable select (filtered by wilaya) --}}
            @php
                $_deskItems = $this->noestDesksForWilaya();
                $_deskItems = array_values(array_map(fn($d) => [
                    'code' => (string) ($d['code'] ?? $d['id'] ?? ''),
                    'nom'  => $d['nom'] ?? $d['name'] ?? $d['desk_name'] ?? '',
                ], $_deskItems));
            @endphp
            <div class="form-floating-group mb-3" wire:key="noest-desk-wrapper-{{ $noestWilaya ?: 'none' }}">
                <div class="noest-search-group"
                     x-data="{ search: '', items: [] }"
                     x-init="items = JSON.parse($el.dataset.items)"
                     data-items="{{ json_encode($_deskItems) }}">
                    <div class="form-floating">
                        <input type="text" x-model="search" class="form-control" id="noest_desk_search" placeholder="{{ __('onboarding.noest_search_desk') }}" autocomplete="off" @if(!$noestWilaya || $isProcessing) disabled @endif>
                        <label for="noest_desk_search">{{ __('onboarding.noest_stop_desk') }}</label>
                    </div>
                    <select wire:model.live="noestDeskId" class="form-select" @if(!$noestWilaya || $isProcessing) disabled @endif>
                        <option value="">-- {{ __('onboarding.noest_select_desk') }} --</option>
                        <template x-for="item in items.filter(i => !search || i.nom.toLowerCase().includes(search.toLowerCase()) || i.code.includes(search))" :key="item.code">
                            <option :value="item.code" x-text="item.code + ' - ' + item.nom"></option>
                        </template>
                    </select>
                </div>
                @if ($noestWilaya && !count($_deskItems))
                    <div class="noest-no-desks">{{ __('onboarding.noest_no_desks_for_wilaya') }}</div>
                @endif
            </div>
        </div>
    @endif

    @if ($paymentMethod === 'chargily')
        <div class="alert alert-info py-2 mb-0 d-flex align-items-center gap-2 small">
            <i class="bi bi-info-circle me-1"></i>{{ __('onboarding.chargily_auto_hint') }}
        </div>
    @endif

    <div class="mb-4">
        <label for="couponCode" class="form-label-custom">{{ __('onboarding.coupon') }}</label>
        <div class="coupon-input-wrapper">
            <input type="text" id="couponCode" class="form-custom coupon-input"
                wire:model.live.debounce.300ms="couponCode"
                placeholder="{{ __('onboarding.coupon_placeholder') }}">
            @if ($couponCode && !$this->couponValidation)
                <span class="coupon-spinner"><i class="bi bi-arrow-repeat"></i></span>
            @endif
            @if ($this->couponValidation)
                <span class="coupon-status {{ $this->couponValidation['valid'] ? 'valid' : 'invalid' }}">
                    <i class="bi {{ $this->couponValidation['valid'] ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }}"></i>
                </span>
            @endif
        </div>
        @if ($this->couponValidation)
            <div class="coupon-message {{ $this->couponValidation['valid'] ? 'text-success' : 'text-danger' }}">
                {{ $this->couponValidation['message'] }}
            </div>
        @endif
    </div>

    @php $fees = $this->feeBreakdown; @endphp
    @if ($paymentMethod && $fees)
        <div class="price-breakdown mb-3">
            <div class="price-row original">
                <span>{{ __('onboarding.plan_price') }}</span>
                <span>{{ $this->displayPrice((float) $fees['original_usd']) }}</span>
            </div>
            @if (($fees['discount_usd'] ?? 0) > 0)
            <div class="price-row discount">
                <span>{{ __('onboarding.coupon_discount') }}</span>
                <span>-{{ $this->displayPrice((float) $fees['discount_usd']) }}</span>
            </div>
            @endif
            @if (($fees['gateway_fee_usd'] ?? 0) > 0)
            <div class="price-row fee">
                <span>{{ __('onboarding.gateway_fee') }}</span>
                <span>+{{ $this->displayPrice((float) $fees['gateway_fee_usd']) }}</span>
            </div>
            @endif
            @if (($fees['tax_added_usd'] ?? 0) > 0)
            <div class="price-row fee">
                <span>{{ __('onboarding.tax_added') }}</span>
                <span>+{{ $this->displayPrice((float) $fees['tax_added_usd']) }}</span>
            </div>
            @endif
            @if (($fees['tax_disclosed_usd'] ?? 0) > 0)
            <div class="price-row">
                <span>{{ __('onboarding.tax_disclosed') }}</span>
                <span>{{ $this->displayPrice((float) $fees['tax_disclosed_usd']) }}</span>
            </div>
            @endif
            <div class="price-divider"></div>
            <div class="price-row total {{ ($fees['total_usd'] ?? 0) <= 0 ? 'free' : '' }}">
                <span>{{ __('onboarding.total') }}</span>
                <span>
                    @if (($fees['total_usd'] ?? 0) <= 0)
                        {{ __('onboarding.free') }}
                    @else
                        {{ $this->displayPrice((float) $fees['total_usd']) }}
                    @endif
                </span>
            </div>
        </div>
    @endif

    <button type="button" class="btn btn-accent btn-custom w-100" wire:click="pay"
        wire:loading.attr="disabled" wire:target="pay" @disabled($isProcessing)>
        <div wire:loading wire:target="pay" class="spinner-border spinner-border-sm me-2" role="status"></div>
        <span wire:loading.remove wire:target="pay">{{ __('onboarding.pay_now') }}</span>
        <span wire:loading wire:target="pay">{{ __('onboarding.processing_payment') }}</span>
    </button>

    <div class="auth-footer mt-3">
        <a href="{{ route('onboarding.plan') }}" wire:navigate>{{ __('onboarding.back_to_plans') }}</a>
    </div>

    @if ($redirectUrl)
        <div class="text-center mt-3">
            <p class="small text-muted">{{ __('onboarding.redirecting') }}</p>
            <a href="{{ $redirectUrl }}" class="btn btn-accent btn-sm">
                {{ __('onboarding.proceed_to_payment') }}
            </a>
        </div>
    @endif
</div>

