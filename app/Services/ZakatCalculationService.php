<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\ZakatRecord;

class ZakatCalculationService
{
    public function calculate(array $data): array
    {
        $goldPrice = $data['gold_price'] ?? 0;
        $silverPrice = $data['silver_price'] ?? 0;

        $nisabGold = ZakatRecord::calculateNisabGold($goldPrice);
        $nisabSilver = ZakatRecord::calculateNisabSilver($silverPrice);

        $goldValue = $data['gold_value'] ?? 0;
        $silverValue = $data['silver_value'] ?? 0;
        $cashValue = $data['cash_value'] ?? 0;
        $bankValue = $data['bank_value'] ?? 0;
        $ccpValue = $data['ccp_value'] ?? 0;
        $businessGoods = $data['business_goods_value'] ?? 0;
        $stocksValue = $data['stocks_value'] ?? 0;
        $cryptoValue = $data['crypto_value'] ?? 0;
        $realEstateValue = $data['real_estate_value'] ?? 0;
        $receivables = $data['expected_receivables'] ?? 0;

        $totalWealth = $goldValue + $silverValue + $cashValue + $bankValue + $ccpValue
            + $businessGoods + $stocksValue + $cryptoValue + $realEstateValue + $receivables;

        $zakatable = $goldValue + $silverValue + $cashValue + $bankValue + $ccpValue
            + $businessGoods + $stocksValue + $cryptoValue + $receivables;

        $exceedsNisab = $zakatable >= min($nisabGold, $nisabSilver);
        $zakatAmount = $exceedsNisab ? ZakatRecord::calculateZakat($zakatable) : 0;

        return [
            'nisab_gold' => $nisabGold,
            'nisab_silver' => $nisabSilver,
            'gold_value' => $goldValue,
            'silver_value' => $silverValue,
            'cash_value' => $cashValue,
            'bank_value' => $bankValue,
            'ccp_value' => $ccpValue,
            'business_goods_value' => $businessGoods,
            'stocks_value' => $stocksValue,
            'crypto_value' => $cryptoValue,
            'real_estate_value' => $realEstateValue,
            'expected_receivables' => $receivables,
            'total_wealth' => $totalWealth,
            'total_zakatable' => $zakatable,
            'exceeds_nisab' => $exceedsNisab,
            'zakat_amount' => $zakatAmount,
        ];
    }

    public function loadUserAssets(?int $userId = null): array
    {
        $assets = Asset::zakatable()->get();
        $result = [];

        foreach ($assets as $asset) {
            $result[$asset->type->value] = ($result[$asset->type->value] ?? 0) + $asset->total_value;
        }

        return $result;
    }
}
