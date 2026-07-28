<?php

namespace App\Http\Controllers\Zakat;

use App\Contracts\Repositories\ZakatRepositoryInterface;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Http\Requests\Zakat\StoreZakatRequest;
use App\Http\Requests\Zakat\UpdateHaulSettingsRequest;
use App\Models\ZakatRecord;
use App\Services\CurrencyHelper;
use App\Services\GoldPriceService;
use App\Services\ZakatCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ZakatController extends Controller
{
    use HasBreadcrumbs;

    public function __construct(
        private ZakatRepositoryInterface $zakatRepo,
        private GoldPriceService $goldPriceService,
    ) {}

    public function calculator(ZakatCalculationService $service)
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.zakat', 'zakat.calculator', 'bi-heart-fill');

        $assets = $service->loadUserAssets();
        $karatPurity = config('zakat.karat_purity', []);

        $goldItems = $assets['gold'] ?? [];
        if ($goldItems === []) {
            $goldItems = [['karat' => 21, 'weight' => 0, 'value' => 0]];
        }

        $owedDebts = $service->getUserOwedDebts();
        $owedDebtsTotal = $owedDebts->sum('remaining_amount');
        $owingDebts = $service->getUserOwingDebts();
        $owingDebtsTotal = $owingDebts->sum('remaining_amount');

        $recentRecords = $this->zakatRepo->history(filters: ['per_page' => 5]);
        $defaultSilverPrice = $this->goldPriceService->convertCurrency(
            $this->goldPriceService->getSilverGramUsd() ?? 0,
            'USD',
            config('finance.currency', 'USD')
        );

        $haulStatus = $service->getHaulStatus();

        return view('zakat.calculator', $this->withBreadcrumbs([
            'assets' => $assets,
            'goldItems' => $goldItems,
            'karatPurity' => $karatPurity,
            'owedDebts' => $owedDebts,
            'owedDebtsTotal' => $owedDebtsTotal,
            'owingDebts' => $owingDebts,
            'owingDebtsTotal' => $owingDebtsTotal,
            'recentRecords' => $recentRecords,
            'defaultSilverPrice' => $defaultSilverPrice,
            'haulStatus' => $haulStatus,
        ]));
    }

    public function calculate(StoreZakatRequest $request, ZakatCalculationService $service)
    {
        $user = auth()->user();

        if (! $user->hasZakatHaulStarted()) {
            return redirect()->route('zakat.calculator')
                ->withErrors(['zakat_start_date' => __('zakat.set_start_date')]);
        }

        if (! $user->isZakatDue()) {
            $daysLeft = $user->daysUntilNextZakat();
            $nextDate = $user->nextZakatDate()?->format('Y/m/d');

            return redirect()->route('zakat.calculator')
                ->withErrors(['haul' => __('zakat.haul_not_complete').' — '.__('zakat.days_left', ['days' => $daysLeft]).' ('.$nextDate.')']);
        }

        $data = $request->validated();
        $userCurrency = config('finance.currency', 'USD');
        $karatPurity = config('zakat.karat_purity', []);

        if (! empty($data['gold_items'])) {
            foreach ($data['gold_items'] as &$item) {
                if (empty($item['price']) || $item['price'] == 0) {
                    $karat = (int) ($item['karat'] ?? 21);
                    $fetched = $this->goldPriceService->getGoldKaratGramUsd($karat);
                    if ($fetched !== null) {
                        $item['price'] = $this->goldPriceService->convertCurrency($fetched, 'USD', $userCurrency);
                    }
                }
            }
            unset($item);
        }

        if (empty($data['silver_price']) || $data['silver_price'] == 0) {
            $fetched = $this->goldPriceService->getSilverGramUsd();
            if ($fetched !== null) {
                $data['silver_price'] = $this->goldPriceService->convertCurrency($fetched, 'USD', $userCurrency);
            }
        }

        $result = $service->calculate($data);
        $owedDebts = $service->getUserOwedDebts();
        $owedDebtsTotal = $owedDebts->sum('remaining_amount');
        $owingDebts = $service->getUserOwingDebts();
        $owingDebtsTotal = $owingDebts->sum('remaining_amount');

        $goldItems = $data['gold_items'] ?? [['karat' => 21, 'weight' => 0, 'price' => 0]];
        $haulStatus = $service->getHaulStatus();

        if (! empty($data['save'])) {
            $dbData = $this->mapResultToDb($result);
            $record = $this->zakatRepo->create(array_merge($dbData, [
                'user_id' => auth()->id(),
                'calculation_date' => now(),
                'hijri_year' => $this->getHijriYear(),
                'calendar_type' => auth()->user()->calendar_type,
                'notes' => $data['notes'] ?? null,
            ]));

            $this->createZakatAssets($record, $result);

            auth()->user()->update(['last_zakat_date' => now()]);

            return redirect()->route('zakat.report', $record)
                ->with('success', __('messages.zakat_saved'));
        }

        return view('zakat.calculator', [
            'result' => $result,
            'assets' => $service->loadUserAssets(),
            'goldItems' => $goldItems,
            'karatPurity' => $karatPurity,
            'input' => $data,
            'owedDebts' => $owedDebts,
            'owedDebtsTotal' => $owedDebtsTotal,
            'owingDebts' => $owingDebts,
            'owingDebtsTotal' => $owingDebtsTotal,
            'recentRecords' => $this->zakatRepo->history(filters: ['per_page' => 5]),
            'defaultSilverPrice' => $this->goldPriceService->convertCurrency(
                $this->goldPriceService->getSilverGramUsd() ?? 0,
                'USD',
                $userCurrency
            ),
            'haulStatus' => $haulStatus,
        ]);
    }

    public function fetchPrices(Request $request): JsonResponse
    {
        try {
            $karatsParam = $request->input('karats', '21');
            $karats = array_map('intval', explode(',', $karatsParam));
            $karats = array_filter($karats, fn ($k) => in_array($k, [24, 22, 21, 18, 14, 10]));
            if (empty($karats)) {
                $karats = [21];
            }

            $gold24kUsd = $this->goldPriceService->getGold24kGramUsd();
            $silverUsd = $this->goldPriceService->getSilverGramUsd();
            $userCurrency = config('finance.currency', 'USD');

            $goldPrices = [];
            foreach ($karats as $karat) {
                $usdPrice = $this->goldPriceService->getGoldKaratGramUsd($karat);
                if ($usdPrice === null && $gold24kUsd !== null) {
                    $karatPurityMap = config('zakat.karat_purity', []);
                    $purity = $karatPurityMap[$karat] ?? null;
                    if ($purity !== null) {
                        $usdPrice = round($gold24kUsd * $purity, 4);
                    }
                }
                $goldPrices[$karat] = $usdPrice
                    ? $this->goldPriceService->convertCurrency($usdPrice, 'USD', $userCurrency)
                    : null;
            }

            $allNull = empty(array_filter($goldPrices)) && $silverUsd === null;
            if ($allNull) {
                return response()->json([
                    'success' => false,
                    'message' => __('zakat.fetch_prices_error'),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'gold_prices' => $goldPrices,
                'silver' => $silverUsd
                    ? $this->goldPriceService->convertCurrency($silverUsd, 'USD', $userCurrency)
                    : null,
                'currency' => $userCurrency,
                'symbol' => CurrencyHelper::symbol($userCurrency),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('zakat.fetch_prices_error'),
            ], 500);
        }
    }

    public function updateHaulSettings(UpdateHaulSettingsRequest $request)
    {
        $data = $request->validated();

        auth()->user()->update([
            'zakat_start_date' => $request->getResolvedStartDate(),
            'calendar_type' => $data['calendar_type'],
        ]);

        return redirect()->route('zakat.calculator')
            ->with('success', __('zakat.haul_settings_updated'));
    }

    public function history(Request $request)
    {
        $filters = $request->only(['search', 'date_from', 'date_to', 'exceeds_nisab', 'per_page']);

        $records = $this->zakatRepo->history(filters: $filters);

        $tabs = [
            'all' => ['label' => __('general.all')],
            'yes' => ['label' => __('general.yes')],
            'no' => ['label' => __('general.no')],
        ];

        $exceedsNisab = $request->input('exceeds_nisab', 'all');

        return view('zakat.history', compact('records', 'tabs', 'exceedsNisab'));
    }

    public function report(ZakatRecord $zakatRecord)
    {
        $this->authorize('view', $zakatRecord);

        return view('zakat.report', compact('zakatRecord'));
    }

    private function mapResultToDb(array $result): array
    {
        $goldWeight = $result['goldTotalWeight'] ?? 0;
        $goldValue = $result['goldTotalValue'] ?? 0;
        $goldAvgPrice = $goldWeight > 0 ? round($goldValue / $goldWeight, 2) : 0;

        return [
            'nisab_gold' => $result['nisabGold'],
            'nisab_silver' => $result['nisabSilver'],
            'gold_price_per_gram' => $goldAvgPrice,
            'silver_price_per_gram' => $result['silverPrice'],
            'gold_weight' => $goldWeight,
            'silver_weight' => $result['silverWeight'],
            'gold_value' => $goldValue,
            'silver_value' => $result['silverValue'],
            'cash_value' => $result['cashValue'],
            'bank_value' => $result['bankValue'],
            'ccp_value' => $result['ccpValue'],
            'business_goods_value' => $result['businessGoods'],
            'stocks_value' => $result['stocksValue'],
            'crypto_value' => $result['cryptoValue'],
            'real_estate_value' => $result['realEstateValue'],
            'expected_receivables' => $result['receivables'],
            'total_wealth' => $result['totalWealth'],
            'total_zakatable' => $result['totalZakatable'],
            'total_debts' => $result['totalDebts'],
            'net_zakatable' => $result['netZakatable'],
            'exceeds_nisab' => $result['exceedsNisab'],
            'zakat_amount' => $result['totalZakat'],
            'cash_zakat' => $result['cashZakat'],
            'gold_zakat' => $result['goldZakat'],
            'silver_zakat' => $result['silverZakat'],
            'business_zakat' => $result['businessZakat'],
            'investments_zakat' => $result['investmentsZakat'],
        ];
    }

    private function createZakatAssets(ZakatRecord $record, array $result): void
    {
        foreach ($result['goldBreakdown'] as $item) {
            $record->assets()->create([
                'type' => 'gold',
                'name' => __('zakat.gold_value').' '.$item['karat'].'K',
                'value' => $item['value'],
                'is_zakatable' => true,
                'zakatable_value' => $item['value'],
                'notes' => "{$item['weight']}g × {$item['price']}",
            ]);
        }

        if ($result['silverValue'] > 0) {
            $record->assets()->create([
                'type' => 'silver',
                'name' => __('zakat.silver_value'),
                'value' => $result['silverValue'],
                'is_zakatable' => true,
                'zakatable_value' => $result['silverValue'],
                'notes' => "{$result['silverWeight']}g × {$result['silverPrice']}",
            ]);
        }

        $otherFields = [
            ['dbField' => 'cash_value', 'resultKey' => 'cashValue', 'type' => 'cash', 'name' => 'zakat.cash_value'],
            ['dbField' => 'bank_value', 'resultKey' => 'bankValue', 'type' => 'bank_account', 'name' => 'zakat.bank_value'],
            ['dbField' => 'ccp_value', 'resultKey' => 'ccpValue', 'type' => 'ccp', 'name' => 'zakat.ccp_value'],
            ['dbField' => 'business_goods_value', 'resultKey' => 'businessGoods', 'type' => 'business_goods', 'name' => 'zakat.business_goods'],
            ['dbField' => 'stocks_value', 'resultKey' => 'stocksValue', 'type' => 'stocks', 'name' => 'zakat.stocks_value'],
            ['dbField' => 'crypto_value', 'resultKey' => 'cryptoValue', 'type' => 'crypto', 'name' => 'zakat.crypto_value'],
            ['dbField' => 'real_estate_value', 'resultKey' => 'realEstateValue', 'type' => 'real_estate', 'name' => 'zakat.real_estate_value', 'zakatable' => false],
            ['dbField' => 'expected_receivables', 'resultKey' => 'receivables', 'type' => 'receivables', 'name' => 'zakat.expected_receivables', 'zakatable' => false],
        ];

        foreach ($otherFields as $config) {
            $value = $result[$config['resultKey']] ?? 0;
            if ($value > 0) {
                $isZakatable = $config['zakatable'] ?? true;
                $record->assets()->create([
                    'type' => $config['type'],
                    'name' => __($config['name']),
                    'value' => $value,
                    'is_zakatable' => $isZakatable,
                    'zakatable_value' => $isZakatable ? $value : 0,
                ]);
            }
        }
    }

    private function getHijriYear(): string
    {
        try {
            $carbon = now();
            $year = $carbon->year;
            $month = $carbon->month;
            $day = $carbon->day;

            $jd = (int) ((1461 * ($year + 4800 + (int) (($month - 14) / 12))) / 4)
                + (int) ((367 * ($month - 2 - 12 * ((int) (($month - 14) / 12)))) / 12)
                - (int) ((3 * (int) (($year + 4900 + (int) (($month - 14) / 12)) / 100)) / 4)
                + $day - 32075;

            $l = $jd - 1948440 + 10632;
            $n = (int) (($l - 1) / 10631);
            $l = $l - 10631 * $n + 354;
            $j = (int) ((10985 - $l) / 5316) * (int) ((50 * $l) / 17719)
                + (int) ($l / 5670) * (int) ((43 * $l) / 15238);
            $l = $l - (int) ((30 - $j) / 15) * (int) ((17719 * $j) / 50)
                - (int) ($j / 16) * (int) ((15238 * $j) / 43) + 29;
            $month = (int) ((24 * $l) / 709);
            $day = $l - (int) ((709 * $month) / 24);
            $year = 30 * $n + $j - 30;

            return (string) $year;
        } catch (\Throwable) {
            return (string) now()->year;
        }
    }
}
