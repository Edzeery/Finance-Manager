<?php

namespace App\Services;

use App\Models\Debt;
use Illuminate\Support\Collection;

class ZakatCalculationService
{
    public function calculate(array $data): array
    {
        $rate = config('zakat.zakat_rate', 0.025);

        $silverPrice = (float) ($data['silver_price'] ?? 0);
        $silverWeight = (float) ($data['silver_weight'] ?? 0);
        $silverValue = round($silverWeight * $silverPrice, 2);

        $goldItems = $data['gold_items'] ?? [];
        $goldTotalValue = 0;
        $goldTotalWeight = 0;
        $goldBreakdown = [];

        foreach ($goldItems as $item) {
            $karat = (int) ($item['karat'] ?? 0);
            $weight = (float) ($item['weight'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            if ($weight > 0 && $price > 0) {
                $value = round($weight * $price, 2);
                $goldTotalWeight += $weight;
                $goldTotalValue += $value;
                $goldBreakdown[] = ['karat' => $karat, 'weight' => $weight, 'price' => $price, 'value' => $value];
            }
        }

        $cashValue = (float) ($data['cash_value'] ?? 0);
        $bankValue = (float) ($data['bank_value'] ?? 0);
        $ccpValue = (float) ($data['ccp_value'] ?? 0);
        $businessGoods = (float) ($data['business_goods_value'] ?? 0);
        $stocksValue = (float) ($data['stocks_value'] ?? 0);
        $cryptoValue = (float) ($data['crypto_value'] ?? 0);
        $realEstateValue = (float) ($data['real_estate_value'] ?? 0);
        $receivables = (float) ($data['expected_receivables'] ?? 0);

        $cashTotal = $cashValue + $bankValue + $ccpValue;
        $investmentsTotal = $stocksValue + $cryptoValue;

        $totalWealth = $goldTotalValue + $silverValue + $cashTotal
            + $businessGoods + $investmentsTotal + $realEstateValue + $receivables;

        $zakatableRealEstate = config('zakat.assets.zakatable_real_estate', false);

        $totalZakatable = $cashTotal + $goldTotalValue + $silverValue
            + $businessGoods + $investmentsTotal
            + ($zakatableRealEstate ? $realEstateValue : 0);

        $owingDebts = $this->getUserOwingDebts();
        $totalDebts = $owingDebts->sum('remaining_amount');
        $netZakatable = max($totalZakatable - $totalDebts, 0);

        $goldZakat = $goldTotalValue > 0 ? round($goldTotalValue * $rate, 2) : 0;
        $silverZakat = $silverValue > 0 ? round($silverValue * $rate, 2) : 0;
        $cashZakat = $cashTotal > 0 ? round($cashTotal * $rate, 2) : 0;
        $businessZakat = $businessGoods > 0 ? round($businessGoods * $rate, 2) : 0;
        $investmentsZakat = $investmentsTotal > 0 ? round($investmentsTotal * $rate, 2) : 0;

        $totalZakatGross = $goldZakat + $silverZakat + $cashZakat + $businessZakat + $investmentsZakat;

        $nisabGoldPrice = $goldBreakdown !== []
            ? round($goldTotalValue / $goldTotalWeight, 2)
            : 0;
        $nisabGold = $nisabGoldPrice > 0
            ? round($nisabGoldPrice * config('zakat.nisab.gold_grams', 85), 2)
            : 0;
        $nisabSilver = $silverPrice > 0
            ? round($silverPrice * config('zakat.nisab.silver_grams', 595), 2)
            : 0;

        $nisabGeneral = ($nisabGold > 0 && $nisabSilver > 0)
            ? min($nisabGold, $nisabSilver)
            : max($nisabGold, $nisabSilver);

        $exceedsNisab = $nisabGeneral > 0 && $netZakatable >= $nisabGeneral;

        $totalZakat = $exceedsNisab
            ? round($netZakatable * $rate, 2)
            : 0;

        return compact(
            'nisabGold', 'nisabSilver', 'nisabGeneral',
            'goldTotalWeight', 'goldTotalValue', 'goldBreakdown',
            'silverWeight', 'silverValue',
            'cashValue', 'bankValue', 'ccpValue',
            'businessGoods', 'stocksValue', 'cryptoValue',
            'realEstateValue', 'receivables',
            'totalWealth', 'totalZakatable', 'totalDebts', 'netZakatable',
            'exceedsNisab', 'totalZakatGross', 'totalZakat',
            'cashZakat', 'goldZakat', 'silverZakat', 'businessZakat', 'investmentsZakat',
            'silverPrice',
        );
    }

    public function loadUserAssets(?int $userId = null): array
    {
        $query = \App\Models\Asset::zakatable();
        if ($userId) {
            $query->where('user_id', $userId);
        }
        $assets = $query->get();

        $result = ['gold' => [], 'silver' => ['weight' => 0, 'value' => 0]];

        foreach ($assets as $asset) {
            $type = $asset->type->value;
            if ($type === 'gold') {
                $karat = $asset->karat ?? 21;
                $found = false;
                foreach ($result['gold'] as &$g) {
                    if ($g['karat'] === $karat) {
                        $g['weight'] += (float) ($asset->weight_grams ?? 0);
                        $g['value'] += (float) $asset->total_value;
                        $found = true;
                        break;
                    }
                }
                unset($g);
                if (! $found) {
                    $result['gold'][] = [
                        'karat' => $karat,
                        'weight' => (float) ($asset->weight_grams ?? 0),
                        'value' => (float) $asset->total_value,
                    ];
                }
            } elseif ($type === 'silver') {
                $result['silver']['weight'] += (float) ($asset->weight_grams ?? 0);
                $result['silver']['value'] += (float) $asset->total_value;
            } else {
                $result[$type] = ($result[$type] ?? 0) + $asset->total_value;
            }
        }

        return $result;
    }

    public function getUserOwedDebts(): Collection
    {
        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        return Debt::where('user_id', $user->id)
            ->owed()
            ->active()
            ->get()
            ->map(fn (Debt $debt) => [
                'id' => $debt->id,
                'counterparty_name' => $debt->counterparty_name,
                'remaining_amount' => $debt->remaining_amount,
                'total_amount' => $debt->total_amount,
                'paid_amount' => $debt->paid_amount,
                'due_date' => $debt->due_date?->format('Y/m/d'),
            ]);
    }

    public function getUserOwedDebtsTotal(): float
    {
        $user = auth()->user();
        if (! $user) {
            return 0;
        }

        return (float) Debt::where('user_id', $user->id)
            ->owed()
            ->active()
            ->sum(\DB::raw('GREATEST(total_amount - paid_amount, 0)'));
    }

    public function getUserOwingDebts(): Collection
    {
        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        return Debt::where('user_id', $user->id)
            ->owing()
            ->active()
            ->get()
            ->map(fn (Debt $debt) => [
                'id' => $debt->id,
                'counterparty_name' => $debt->counterparty_name,
                'remaining_amount' => $debt->remaining_amount,
                'total_amount' => $debt->total_amount,
                'paid_amount' => $debt->paid_amount,
                'due_date' => $debt->due_date?->format('Y/m/d'),
            ]);
    }

    public function getUserOwingDebtsTotal(): float
    {
        $user = auth()->user();
        if (! $user) {
            return 0;
        }

        return (float) Debt::where('user_id', $user->id)
            ->owing()
            ->active()
            ->sum(\DB::raw('GREATEST(total_amount - paid_amount, 0)'));
    }
}
