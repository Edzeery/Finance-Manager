<?php
//resources\views\livewire\pages\onboarding\plan.blade.php
use App\Enums\PaymentStatus;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\Payments\Noest\NoestErrorHandler;
use App\Services\Payments\Noest\NoestService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public array $plans = [];
    public ?int $selectedPlanId = null;
    public string $billingPeriod = 'monthly';
    public ?string $paymentMethod = null;
    public ?string $couponCode = null;
    public bool $isProcessing = false;
    public ?string $redirectUrl = null;
    public ?string $errorMessage = null;
    public ?array $selectedPlan = null;
    public array $paymentMethods = [];

    public ?array $couponValidation = null;

    public string $noestClient = '';
    public string $noestPhone = '';
    public string $noestPhone2 = '';
    public string $noestAdresse = '';
    public string $noestWilaya = '';
    public string $noestDeskId = '';
    public array $noestWilayas = [];
    public array $noestDesks = [];

    public string $deliveryAddress = '';
    public string $deliveryPhone = '';

    #[Computed(persist: false)]
    public function pendingPayment(): ?Payment
    {
        $user = auth()->user();
        if (!$user || !$user->pending_plan_id) {
            return null;
        }

        return Payment::withoutWorkspace()->where('user_id', $user->id)->where('status', PaymentStatus::CheckoutPending->value)->latest()->first();
    }

    #[Computed(persist: false)]
    public function pendingPlanInfo(): ?array
    {
        $payment = $this->pendingPayment;
        if (!$payment) {
            return null;
        }

        $pendingPlan = \App\Models\SubscriptionPlan::find(auth()->user()->pending_plan_id);
        return $pendingPlan ? ['name' => $pendingPlan->name, 'id' => $pendingPlan->id] : null;
    }

    public function mount(OnboardingService $onboardingService): void
    {
        $this->plans = $onboardingService->getAvailablePlans();

        $planModels = SubscriptionPlan::active()
            ->public()
            ->with(['planFeatures', 'activePrices'])
            ->get()
            ->keyBy('id');
        $this->plans = collect($this->plans)
            ->map(function ($plan) use ($planModels) {
                $model = $planModels[$plan['id']] ?? null;
                if ($model) {
                    $plan['_features'] = $model->planFeatures
                        ->map(
                            fn($f) => [
                                'slug' => $f->slug,
                                'name_en' => $f->name_en,
                                'name_ar' => $f->name_ar,
                                'name_fr' => $f->name_fr,
                                'icon' => $f->icon,
                                'type' => $f->type,
                                'value' => $f->pivot->value,
                            ],
                        )
                        ->toArray();
                    $plan['_prices'] = $model->activePrices->toArray();
                    // Override array prices from plan_prices (source of truth)
                    $monthlyFromPrices = collect($plan['_prices'])->firstWhere('billing_period', 'monthly');
                    $yearlyFromPrices = collect($plan['_prices'])->firstWhere('billing_period', 'yearly');
                    if ($monthlyFromPrices) {
                        $plan['monthly_price'] = $monthlyFromPrices['price'];
                    }
                    if ($yearlyFromPrices) {
                        $plan['yearly_price'] = $yearlyFromPrices['price'];
                    }
                } else {
                    $plan['_features'] = [];
                    $plan['_prices'] = [];
                }
                return $plan;
            })
            ->toArray();

        $currency = session('currency', auth()->user()?->currency ?? config('finance.currency', 'DZD'));

        $this->paymentMethods = \App\Models\PaymentMethod::active()
            ->public()
            ->byCurrency($currency)
            ->ordered()
            ->get()
            ->map(
                fn($m) => [
                    'id' => $m->key,
                    'name' => __("onboarding.method_{$m->key}") !== "onboarding.method_{$m->key}" ? __("onboarding.method_{$m->key}") : $m->name,
                    'icon' => $m->icon ?? 'bi-credit-card',
                    'required_fields' => $m->requiredFields(),
                ],
            )
            ->toArray();

        $user = auth()->user();
        if ($user->pending_plan_id) {
            $this->selectedPlanId = $user->pending_plan_id;
            $this->findSelectedPlan();
        }

        $this->noestClient = $user->name ?? '';
        $this->noestPhone = $user->phone ?? '';
        $this->loadNoestData();
    }

    public function loadNoestData(): void
    {
        try {
            $service = app(NoestService::class);

            $wilayasRaw = $service->getWilayas();
            $wilayasList = $wilayasRaw['data'] ?? (isset($wilayasRaw[0]) ? $wilayasRaw : []);

            $this->noestWilayas = collect($wilayasList)
                ->map(
                    fn($w) => [
                        'code' => (string) ($w['code'] ?? ($w['id'] ?? ($w['wilaya_id'] ?? ''))),
                        'nom' => $w['nom'] ?? ($w['name'] ?? ($w['wilaya_name'] ?? '')),
                    ],
                )
                ->filter(fn($w) => $w['code'] !== '' && $w['nom'] !== '')
                ->values()
                ->toArray();

            $desksRaw = $service->getDesks();

            $desksList = [];
            foreach ($desksRaw['data'] ?? $desksRaw as $key => $item) {
                if (is_array($item) && !empty($item['code'])) {
                    $item['_key'] = $key;
                    $desksList[] = $item;
                }
            }

            Log::debug('Noest desks raw', [
                'count_raw' => count($desksRaw),
                'count_list' => count($desksList),
                'first_key' => array_key_first($desksRaw),
                'sample' => json_encode($desksList[0] ?? $desksRaw, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ]);

            $this->noestDesks = collect($desksList)
                ->map(
                    fn($d) => [
                        'code' => (string) ($d['code'] ?? ''),
                        'nom' => $d['name'] ?? ($d['desk_name'] ?? ($d['nom'] ?? '')),
                        'wilaya' => (function () use ($d) {
                            preg_match('/^(\d+)/', $d['_key'] ?? ($d['code'] ?? ''), $m);
                            return (string) (int) $m[1];
                        })(),
                    ],
                )
                ->filter(fn($d) => $d['code'] !== '' && $d['nom'] !== '')
                ->values()
                ->toArray();

            Log::debug('Noest data loaded', [
                'wilayas_count' => count($this->noestWilayas),
                'desks_count' => count($this->noestDesks),
                'sample_desk' => $this->noestDesks[0] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Noest loadNoestData failed', [
                'error' => $e->getMessage(),
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
        if (!$this->noestWilaya) {
            return [];
        }
        $wilaya = (int) $this->noestWilaya;

        return array_values(array_filter($this->noestDesks, fn($d) => is_array($d) && (int) ($d['wilaya'] ?? 0) === $wilaya));
    }

    public function selectPlan(int $id): void
    {
        $this->selectedPlanId = $id;
        $this->paymentMethod = null;
        $this->redirectUrl = null;
        $this->errorMessage = null;
        $this->couponCode = null;
        $this->couponValidation = null;
        $this->findSelectedPlan();
    }

    public function setPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;
        if ($method !== 'delivery') {
            $this->deliveryAddress = '';
            $this->deliveryPhone = '';
        }
        $this->validateCouponCode();
    }

    public function toggleBilling(): void
    {
        $this->billingPeriod = $this->billingPeriod === 'monthly' ? 'yearly' : 'monthly';
        $this->couponValidation = null;
        $this->validateCouponCode();
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
        if (!$code) {
            return;
        }

        $plan = $this->selectedPlan;
        if (!$plan) {
            return;
        }

        $priceUsd = $this->billingPeriod === 'yearly' ? (float) ($plan['yearly_price'] ?? 0) : (float) ($plan['monthly_price'] ?? 0);

        if ($priceUsd <= 0) {
            return;
        }

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            $this->couponValidation = [
                'valid' => false,
                'message' => __('onboarding.coupon_not_found'),
            ];
            return;
        }

        if (!$coupon->is_active) {
            $this->couponValidation = [
                'valid' => false,
                'message' => __('onboarding.coupon_inactive'),
            ];
            return;
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            $this->couponValidation = [
                'valid' => false,
                'message' => __('onboarding.coupon_expired'),
            ];
            return;
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            $this->couponValidation = [
                'valid' => false,
                'message' => __('onboarding.coupon_not_started'),
            ];
            return;
        }

        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            $this->couponValidation = [
                'valid' => false,
                'message' => __('onboarding.coupon_max_uses'),
            ];
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
            if (!$pm || !$pm->is_active) {
                $this->couponValidation = [
                    'valid' => false,
                    'message' => __('onboarding.coupon_not_for_gateway'),
                ];
                return;
            }
            if (!$coupon->paymentMethods()->where('payment_method_id', $pm->id)->exists()) {
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
            'message' => $finalUsd <= 0 ? __('onboarding.coupon_fully_covered') : __('onboarding.coupon_applied'),
            'discount_usd' => $discountUsd,
            'final_usd' => $finalUsd,
            'original_usd' => $priceUsd,
            'code' => $coupon->code,
        ];
    }

    public function proceed(): void
    {
        $this->validate(['selectedPlanId' => 'required']);

        $user = auth()->user();
        $plan = SubscriptionPlan::find($this->selectedPlanId);

        if (!$plan || !$plan->is_free) {
            return;
        }

        app(OnboardingService::class)->selectPlan($user, $plan->slug);
        app(OnboardingService::class)->processFreePlan($user);
        $this->redirect(route('onboarding.setup', absolute: false), navigate: true);
    }

    public function proceedTrial(): void
    {
        $this->validate(['selectedPlanId' => 'required']);

        $user = auth()->user();
        $plan = SubscriptionPlan::find($this->selectedPlanId);

        if (!$plan || $plan->is_free || !$plan->hasTrial()) {
            return;
        }

        app(OnboardingService::class)->selectPlan($user, $plan->slug);
        app(OnboardingService::class)->processTrialPlan($user);
        $this->redirect(route('onboarding.setup', absolute: false), navigate: true);
    }

    public function pay(): void
    {
        $rules = [
            'selectedPlanId' => 'required',
            'paymentMethod' => 'required',
        ];

        // ✅ التحقق الديناميكي حسب الحقول المطلوبة لكل بوابة
        $pm = \App\Models\PaymentMethod::where('key', $this->paymentMethod)->first();
        foreach (optional($pm)->requiredFields() ?? [] as $field) {
            $rules[$field] = match ($field) {
                'noestClient' => ['required', 'string', 'max:255'],
                'noestPhone' => ['required', 'string', 'regex:/^(05|06|07)[0-9]{8}$/'],
                'noestWilaya' => ['required', 'string'],
                'noestAdresse' => ['required', 'string', 'max:500'],
                'deliveryAddress' => ['required', 'string', 'max:500'],
                'deliveryPhone' => ['required', 'string', 'max:20'],
                default => ['required'],
            };
        }

        $this->validate($rules);

        $this->isProcessing = true;
        $this->errorMessage = null;

        try {
            $user = auth()->user();
            $plan = SubscriptionPlan::find($this->selectedPlanId);

            if (!$plan) {
                $this->errorMessage = __('onboarding.payment_init_failed');
                $this->isProcessing = false;
                return;
            }

            // If same plan as pending payment, just redirect to status page
            if ($this->pendingPayment && $this->pendingPlanInfo && $this->pendingPlanInfo['id'] === $plan->id) {
                $this->redirect(route('payment.status', $this->pendingPayment), navigate: true);
                return;
            }

            app(OnboardingService::class)->selectPlan($user, $plan->slug);

            $gatewayData = match ($this->paymentMethod) {
                'noest' => [
                    'noest_client' => trim($this->noestClient),
                    'noest_phone' => trim($this->noestPhone),
                    'noest_phone_2' => trim($this->noestPhone2),
                    'noest_adresse' => trim($this->noestAdresse),
                    'noest_wilaya' => $this->noestWilaya,
                    'noest_stop_desk' => (bool) $this->noestDeskId,
                    'noest_station_code' => $this->noestDeskId ?: null,
                    'noest_remboursement' => true,
                    'noest_can_open' => false,
                ],
                'delivery' => [
                    'address' => trim($this->deliveryAddress),
                    'phone' => trim($this->deliveryPhone),
                ],
                default => [],
            };

            $payment = app(OnboardingService::class)->initiatePaidPlanPayment($user, $this->paymentMethod, $this->billingPeriod, $this->couponCode, $gatewayData);

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
                $this->redirect(route('onboarding.manual-proof', ['payment' => $payment->id], absolute: false), navigate: true);
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
                'user_id' => auth()->id(),
                'plan_id' => $this->selectedPlanId,
                'method' => $this->paymentMethod,
                'wilaya' => $this->noestWilaya,
                'desk_id' => $this->noestDeskId,
                'error' => $e->getMessage(),
            ]);

            if ($this->paymentMethod === 'noest') {
                $this->errorMessage = NoestErrorHandler::translate($e->getMessage());
            } else {
                $this->errorMessage = __('onboarding.payment_init_failed');
            }
            $this->isProcessing = false;
        }
    }

    private function findSelectedPlan(): void
    {
        foreach ($this->plans as $plan) {
            if (($plan['id'] ?? null) === $this->selectedPlanId) {
                $this->selectedPlan = $plan;
                return;
            }
        }
        $this->selectedPlan = null;
    }

    public function yearlySavings(array $plan): ?float
    {
        $isFree = $plan['is_free'] ?? false;
        if ($isFree) {
            return null;
        }

        $monthlyPrice = $plan['monthly_price'] ?? 0;
        $yearlyPrice = $plan['yearly_price'] ?? 0;

        if (!$monthlyPrice) {
            return null;
        }
        return $monthlyPrice * 12 - $yearlyPrice;
    }

    public function yearlySavingsPercent(array $plan): ?float
    {
        $monthly = $plan['monthly_price'] ?? 0;
        $yearly = $plan['yearly_price'] ?? 0;
        if (!$monthly || !$yearly) {
            return null;
        }
        $annualized = $monthly * 12;
        if ($annualized <= 0) {
            return null;
        }
        return round((($annualized - $yearly) / $annualized) * 100);
    }

    public function getFeeBreakdownProperty(): ?array
    {
        $plan = $this->selectedPlan;
        if (!$plan || !$this->paymentMethod) {
            return null;
        }

        $priceUsd = $this->billingPeriod === 'yearly' ? (float) ($plan['yearly_price'] ?? 0) : (float) ($plan['monthly_price'] ?? 0);

        $discountUsd = $this->couponValidation['discount_usd'] ?? 0;
        $finalUsd = max($priceUsd - $discountUsd, 0);

        $pmModel = \App\Models\PaymentMethod::where('key', $this->paymentMethod)->first();
        if (!$pmModel) {
            return null;
        }

        $gatewayFeeUsd = 0.0;
        $taxAddedUsd = 0.0;
        $taxDisclosedUsd = 0.0;

        $links = $pmModel->taxRates()->withPivot('charge_type')->get();
        foreach ($links as $taxRate) {
            $calculated = $taxRate->calculateForAmount($finalUsd);
            match ($taxRate->pivot->charge_type) {
                'gateway_fee' => ($gatewayFeeUsd += $calculated),
                'tax_added' => ($taxAddedUsd += $calculated),
                'tax_disclosed' => ($taxDisclosedUsd += $calculated),
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
        $currency = session('currency', auth()->user()?->currency ?? config('finance.currency', 'DZD'));
        $converted = CurrencyHelper::fromUsd($usdAmount, $currency);
        $decimals = config('finance.currencies.' . $currency . '.decimal_places', config('finance.decimal_places', 2));
        return number_format($converted, (int) $decimals) . ' ' . CurrencyHelper::symbol($currency);
    }
}; ?>

<div class="onboarding-wrapper ">
    <div class="onboarding-header">
        <div class="auth-logo">
            <div class="logo-icon">FM</div>
            <span class="logo-text">{{ __('general.app_name') }}</span>
            <span class="logo-sub">{{ __('onboarding.choose_plan') }}</span>
        </div>
        <p class="onboarding-desc">{{ __('onboarding.plan_description') }}</p>
    </div>

    @if ($pendingPayment && $pendingPlanInfo)
        <div class="alert alert-warning d-flex align-items-start gap-3 mb-4 p-3 rounded-3 border-0 shadow-sm"
            style="background: #fff3cd; border: 1px solid #ffc107;">
            <div class="flex-shrink-0">
                <i class="bi bi-exclamation-triangle-fill fs-4" style="color: #856404;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="d-block mb-1"
                    style="color: #856404;">{{ __('onboarding.pending_payment_title') }}</strong>
                <p class="mb-2 small" style="color: #856404;">
                    {{ __('onboarding.pending_plan_banner', ['plan' => $pendingPlanInfo['name'] ?? '']) }}
                </p>
                <div class="d-flex gap-2">
                    <a href="{{ route('payment.status', $pendingPayment) }}"
                        class="btn btn-sm btn-warning text-dark fw-semibold">
                        <i class="bi bi-arrow-right-circle"></i> {{ __('onboarding.resume_payment') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="billing-toggle">
        <span
            class="billing-label {{ $billingPeriod === 'monthly' ? 'active' : '' }}">{{ __('onboarding.monthly') }}</span>
        <button type="button" class="toggle-switch {{ $billingPeriod === 'yearly' ? 'active' : '' }}"
            wire:click="toggleBilling" role="switch">
            <span class="toggle-knob {{ $billingPeriod === 'yearly' ? 'on' : '' }}"></span>
        </button>
        <span
            class="billing-label {{ $billingPeriod === 'yearly' ? 'active' : '' }}">{{ __('onboarding.yearly') }}</span>
    </div>

    <div class="plan-grid">
        @foreach ($plans as $index => $plan)
            @php
                $isPopular = $index === 1;
                $isFree = $plan['is_free'] ?? false;
                $monthlyPrice = $plan['monthly_price'] ?? 0;
                $yearlyPrice = $plan['yearly_price'] ?? 0;
                $displayPrice = $billingPeriod === 'yearly' ? $yearlyPrice : $monthlyPrice;
                $savings = $this->yearlySavings($plan);
                $savingsPercent = $this->yearlySavingsPercent($plan);
                $features = $plan['_features'] ?? [];
                $planPrices = $plan['_prices'] ?? [];
            @endphp
            <div class="plan-card {{ $selectedPlanId === $plan['id'] ? 'selected' : '' }} {{ $isPopular ? 'popular' : '' }}"
                wire:click="selectPlan({{ $plan['id'] }})" role="button" tabindex="0"
                wire:key="plan-{{ $plan['id'] }}" x-data="{ showAll: false }">

                @if ($isPopular)
                    <div class="plan-badge">{{ __('onboarding.popular') }}</div>
                @endif

                <div class="plan-card-content">
                    <div class="plan-name">{{ $plan['name'] }}</div>

                    <div class="plan-price">
                        @if ($isFree)
                            <span class="price-amount">{{ __('onboarding.free') }}</span>
                        @else
                            @php
                                $currentPrice =
                                    $billingPeriod === 'yearly'
                                        ? $yearlyPrice
                                        : collect($planPrices)->firstWhere('billing_period', 'monthly')['price'] ??
                                            $monthlyPrice;
                                $originalPrice = null;
                            @endphp
                            <span class="price-amount">{{ $this->displayPrice((float) $currentPrice) }}</span>
                            <span
                                class="price-period">{{ $billingPeriod === 'yearly' ? __('onboarding.per_year') : __('onboarding.per_month') }}</span>
                            @if ($originalPrice && $originalPrice > $currentPrice)
                                <span class="price-original">{{ $this->displayPrice((float) $originalPrice) }}</span>
                            @endif
                        @endif
                    </div>

                    @if ($billingPeriod === 'yearly' && $savingsPercent)
                        <div class="plan-savings">
                            {{ __('onboarding.save_percent', ['percent' => $savingsPercent]) }}
                            <span class="plan-savings-amount">{{ $this->displayPrice($savings) }}</span>
                        </div>
                    @endif
                    @php
                        $des_plan = $plan['description'] ?? __('subscription.' . $plan['description']);
                    @endphp
                    @if ($des_plan ?? null)
                        <p class="plan-desc"> {{ $des_plan }} </p>
                    @endif

                    <ul class="plan-features">
                        @if (count($features))
                            @foreach ($features as $i => $feature)
                                @if ($i >= 5)
                                    <li x-show="showAll" x-cloak>
                                    @else
                                    <li>
                                @endif
                                @if ($feature['icon'])
                                    <i class="{{ $feature['icon'] }}"></i>
                                @else
                                    <i class="bi bi-check-circle-fill"></i>
                                @endif
                                @php
                                    $nameKey = 'name_' . app()->getLocale();
                                    $name = $feature[$nameKey] ?? $feature['name_en'];
                                @endphp
                                @if ($feature['type'] === 'boolean')
                                    {{ $name }}
                                @elseif ($feature['value'])
                                    {{ $feature['value'] }} {{ $name }}
                                @else
                                    {{ $name }}
                                @endif
                                </li>
                            @endforeach
                            @if (count($features) > 5)
                                <li class="plan-features-toggle">
                                    <button type="button" class="plan-features-btn" @click.stop="showAll = !showAll">
                                        <span x-show="!showAll">{{ __('onboarding.show_more') }}
                                            ({{ count($features) - 5 }}) <i class="bi bi-chevron-down"></i></span>
                                        <span x-show="showAll" x-cloak>{{ __('onboarding.show_less') }} <i
                                                class="bi bi-chevron-up"></i></span>
                                    </button>
                                </li>
                            @endif
                        @else
                            @foreach (is_array($plan['features'] ?? null) ? $plan['features'] : [] as $feature)
                                <li><i class="bi bi-check-circle-fill"></i> {{ $feature }}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <div class="plan-select-indicator">
                    <div class="radio-circle {{ $selectedPlanId === $plan['id'] ? 'checked' : '' }}">
                        @if ($selectedPlanId === $plan['id'])
                            <i class="bi bi-check-lg"></i>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @error('selectedPlanId')
        <div class="onboarding-error">{{ $message }}</div>
    @enderror

    @php $isTrialPlan = $selectedPlan && !($selectedPlan['is_free'] ?? false) && ($selectedPlan['trial_days'] ?? 0) > 0; @endphp

    @if ($selectedPlan && !($selectedPlan['is_free'] ?? false) && !$isTrialPlan)
        <div class="payment-section">
            <div class="payment-section-header">
                <i class="bi bi-credit-card-2-front"></i>
                <span>{{ __('onboarding.select_payment') }}</span>
            </div>
            <div class="payment-currency-info">
                <i class="bi bi-info-circle"></i>
                {{ __('onboarding.payment_methods_for_currency', ['currency' => \App\Services\CurrencyHelper::symbol(session('currency', auth()->user()?->currency ?? config('finance.currency', 'DZD')))]) }}
            </div>

            @if ($errorMessage)
                <div class="alert alert-danger py-2 small">{{ $errorMessage }}</div>
            @endif

            <div class="payment-methods">
                @foreach ($paymentMethods as $method)
                    <div class="payment-method-card {{ $paymentMethod === $method['id'] ? 'selected' : '' }}"
                        wire:click="setPaymentMethod('{{ $method['id'] }}')" role="button" tabindex="0">
                        <div class="method-radio">
                            <div class="method-radio-circle {{ $paymentMethod === $method['id'] ? 'checked' : '' }}">
                                @if ($paymentMethod === $method['id'])
                                    <i class="bi bi-check-lg"></i>
                                @endif
                            </div>
                        </div>
                        <i class="method-icon {{ $method['icon'] }}"></i>
                        <span class="method-name">{{ $method['name'] }}</span>
                    </div>
                @endforeach
            </div>
            @error('paymentMethod')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror

            @if ($paymentMethod && App\Services\OnboardingService::isManual($paymentMethod))
                <div class="alert alert-info py-2 small mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle me-1"></i>{{ __('onboarding.manual_confirm_hint') }}
                </div>
            @endif

            @if ($paymentMethod === 'delivery')
                <div class="noest-form mb-3">
                    <div class="noest-form-header">
                        <i class="bi bi-geo-alt"></i>
                        <span>{{ __('onboarding.delivery_info') }}</span>
                    </div>
                    <div class="form-floating-group mb-3">
                        <input type="text" id="delivery_address" class="form-control" wire:model="deliveryAddress"
                            placeholder=" " @disabled($isProcessing)>
                        <label for="delivery_address">{{ __('onboarding.delivery_address') }} <span
                                class="text-danger">*</span></label>
                        @error('deliveryAddress')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-floating-group">
                        <input type="text" id="delivery_phone" class="form-control" wire:model="deliveryPhone"
                            placeholder=" " @disabled($isProcessing)>
                        <label for="delivery_phone">{{ __('onboarding.delivery_phone') }} <span
                                class="text-danger">*</span></label>
                        @error('deliveryPhone')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endif

            @if ($paymentMethod === 'noest')
                <div class="noest-form mb-3">
                    <div class="noest-form-header">
                        <i class="bi bi-truck"></i>
                        <span>{{ __('onboarding.noest_delivery_info') }}</span>
                    </div>

                    <div class="form-floating-group mb-3">
                        <input type="text" id="noest_client" class="form-control" wire:model="noestClient"
                            placeholder=" " @disabled($isProcessing)>
                        <label for="noest_client">{{ __('onboarding.noest_client') }} <span
                                class="text-danger">*</span></label>
                        @error('noestClient')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="text" id="noest_phone" class="form-control" wire:model="noestPhone"
                                    placeholder=" " @disabled($isProcessing)>
                                <label for="noest_phone">{{ __('onboarding.noest_phone') }} <span
                                        class="text-danger">*</span></label>
                                @error('noestPhone')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="text" id="noest_phone_2" class="form-control"
                                    wire:model="noestPhone2" placeholder=" " @disabled($isProcessing)>
                                <label for="noest_phone_2">{{ __('onboarding.noest_phone_2') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating-group mb-3">
                        <input type="text" id="noest_adresse" class="form-control" wire:model="noestAdresse"
                            placeholder=" " @disabled($isProcessing)>
                        <label for="noest_adresse">{{ __('onboarding.noest_adresse') }} <span
                                class="text-danger">*</span></label>
                        @error('noestAdresse')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Wilaya searchable select --}}
                    @php
                        $_wilayaItems = array_values(
                            array_map(
                                fn($w) => [
                                    'code' => (string) ($w['code'] ?? ($w['id'] ?? '')),
                                    'nom' => $w['nom'] ?? ($w['name'] ?? ''),
                                ],
                                $this->noestWilayas,
                            ),
                        );
                    @endphp
                    <div class="form-floating-group mb-3">
                        <div class="noest-search-group" x-data="{ search: '', items: [] }" x-init="items = JSON.parse($el.dataset.items)"
                            data-items="{{ json_encode($_wilayaItems) }}">
                            <div class="form-floating">
                                <input type="text" x-model="search" class="form-control" id="noest_wilaya_search"
                                    placeholder="{{ __('onboarding.noest_search_wilaya') }}" autocomplete="off"
                                    @if ($isProcessing) disabled @endif>
                                <label for="noest_wilaya_search">{{ __('onboarding.noest_wilaya') }} <span
                                        class="text-danger">*</span></label>
                            </div>
                            <select wire:model.live="noestWilaya" class="form-select"
                                @if ($isProcessing) disabled @endif>
                                <option value="">-- {{ __('onboarding.noest_select_wilaya') }} --</option>
                                <template
                                    x-for="item in items.filter(i => !search || i.nom.toLowerCase().includes(search.toLowerCase()) || i.code.includes(search))"
                                    :key="item.code">
                                    <option :value="item.code" x-text="item.code + ' - ' + item.nom"></option>
                                </template>
                            </select>
                        </div>
                        @error('noestWilaya')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Desk searchable select (filtered by wilaya) --}}
                    @php
                        $_deskItems = $this->noestDesksForWilaya();
                        $_deskItems = array_values(
                            array_map(
                                fn($d) => [
                                    'code' => (string) ($d['code'] ?? ($d['id'] ?? '')),
                                    'nom' => $d['nom'] ?? ($d['name'] ?? ($d['desk_name'] ?? '')),
                                ],
                                $_deskItems,
                            ),
                        );
                    @endphp
                    <div class="form-floating-group mb-3" wire:key="noest-desk-wrapper-{{ $noestWilaya ?: 'none' }}">
                        <div class="noest-search-group" x-data="{ search: '', items: [] }" x-init="items = JSON.parse($el.dataset.items)"
                            data-items="{{ json_encode($_deskItems) }}">
                            <div class="form-floating">
                                <input type="text" x-model="search" class="form-control" id="noest_desk_search"
                                    placeholder="{{ __('onboarding.noest_search_desk') }}" autocomplete="off"
                                    @if (!$noestWilaya || $isProcessing) disabled @endif>
                                <label for="noest_desk_search">{{ __('onboarding.noest_stop_desk') }}</label>
                            </div>
                            <select wire:model.live="noestDeskId" class="form-select"
                                @if (!$noestWilaya || $isProcessing) disabled @endif>
                                <option value="">-- {{ __('onboarding.noest_select_desk') }} --</option>
                                <template
                                    x-for="item in items.filter(i => !search || i.nom.toLowerCase().includes(search.toLowerCase()) || i.code.includes(search))"
                                    :key="item.code">
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

            <div class="coupon-section">
                <div class="coupon-input-wrapper">
                    <input type="text" id="couponCode" class="form-custom coupon-input"
                        wire:model.live.debounce.300ms="couponCode"
                        placeholder="{{ __('onboarding.coupon_placeholder') }}">
                    @if ($couponCode && !$this->couponValidation)
                        <span class="coupon-spinner"><i class="bi bi-arrow-repeat"></i></span>
                    @endif
                    @if ($this->couponValidation)
                        <span class="coupon-status {{ $this->couponValidation['valid'] ? 'valid' : 'invalid' }}">
                            <i
                                class="bi {{ $this->couponValidation['valid'] ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }}"></i>
                        </span>
                    @endif
                </div>
                @if ($this->couponValidation)
                    <div
                        class="coupon-message {{ $this->couponValidation['valid'] ? 'text-success' : 'text-danger' }}">
                        {{ $this->couponValidation['message'] }}
                    </div>
                @endif
            </div>

            @php $_feeBrk = $this->feeBreakdown; @endphp
            @if ($paymentMethod && $_feeBrk)
                <div class="price-breakdown mb-3">
                    <div class="price-row original">
                        <span>{{ __('onboarding.plan_price') }}</span>
                        <span>{{ $this->displayPrice((float) $_feeBrk['original_usd']) }}</span>
                    </div>
                    @if (($_feeBrk['discount_usd'] ?? 0) > 0)
                        <div class="price-row discount">
                            <span>{{ __('onboarding.coupon_discount') }}</span>
                            <span>-{{ $this->displayPrice((float) $_feeBrk['discount_usd']) }}</span>
                        </div>
                    @endif
                    @if (($_feeBrk['gateway_fee_usd'] ?? 0) > 0)
                        <div class="price-row fee">
                            <span>{{ __('onboarding.gateway_fee') }}</span>
                            <span>+{{ $this->displayPrice((float) $_feeBrk['gateway_fee_usd']) }}</span>
                        </div>
                    @endif
                    @if (($_feeBrk['tax_added_usd'] ?? 0) > 0)
                        <div class="price-row fee">
                            <span>{{ __('onboarding.tax_added') }}</span>
                            <span>+{{ $this->displayPrice((float) $_feeBrk['tax_added_usd']) }}</span>
                        </div>
                    @endif
                    @if (($_feeBrk['tax_disclosed_usd'] ?? 0) > 0)
                        <div class="price-row">
                            <span>{{ __('onboarding.tax_disclosed') }}</span>
                            <span>{{ $this->displayPrice((float) $_feeBrk['tax_disclosed_usd']) }}</span>
                        </div>
                    @endif
                    <div class="price-divider"></div>
                    <div class="price-row total {{ ($_feeBrk['total_usd'] ?? 0) <= 0 ? 'free' : '' }}">
                        <span>{{ __('onboarding.total') }}</span>
                        <span>
                            @if (($_feeBrk['total_usd'] ?? 0) <= 0)
                                {{ __('onboarding.free') }}
                            @else
                                {{ $this->displayPrice((float) $_feeBrk['total_usd']) }}
                            @endif
                        </span>
                    </div>
                </div>
            @endif

            <button type="button" class="btn btn-accent btn-custom w-100 pay-btn" wire:click="pay"
                wire:loading.attr="disabled" wire:target="pay" @disabled($isProcessing)>
                <span wire:loading.remove wire:target="pay">{{ __('onboarding.pay_now') }}</span>
                <span wire:loading wire:target="pay">{{ __('onboarding.processing_payment') }}</span>
            </button>

            @if ($redirectUrl)
                <div class="text-center mt-3">
                    <p class="small text-muted">{{ __('onboarding.redirecting') }}</p>
                    <a href="{{ $redirectUrl }}" class="btn btn-accent btn-sm" target="_blank" rel="noopener">
                        {{ __('onboarding.proceed_to_payment') }}
                    </a>
                </div>
            @endif
        </div>
    @elseif ($selectedPlan && ($selectedPlan['is_free'] ?? false))
        <button type="button" class="btn btn-accent btn-custom w-100 proceed-btn" wire:click="proceed"
            wire:loading.attr="disabled" wire:target="proceed">
            {{ __('onboarding.continue') }}
        </button>
    @elseif ($isTrialPlan)
        <button type="button" class="btn btn-accent btn-custom w-100 proceed-btn" wire:click="proceedTrial"
            wire:loading.attr="disabled" wire:target="proceedTrial">
            <i class="bi bi-rocket-takeoff me-1"></i>{{ __('onboarding.start_free_trial') }}
        </button>
    @endif

    @if ($selectedPlan && !($selectedPlan['is_free'] ?? false))
        <div class="trust-banner mt-4">
            <div class="trust-item">
                <i class="bi bi-shield-check"></i>
                <span>{{ __('onboarding.secure_payment') }}</span>
            </div>
            <div class="trust-item">
                <i class="bi bi-lock"></i>
                <span>{{ __('onboarding.encrypted') }}</span>
            </div>
            <div class="trust-item">
                <i class="bi bi-credit-card-2-front"></i>
                <span>{{ __('onboarding.multiple_methods') }}</span>
            </div>
        </div>
    @endif

    <div class="onboarding-footer">
        <a href="{{ route('logout') }}"
            @click="$event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right me-1"></i>{{ __('onboarding.sign_out') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</div>
