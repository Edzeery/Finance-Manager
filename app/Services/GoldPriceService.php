<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoldPriceService
{
    private const BASE_URL = 'https://api.gold-api.com/price';

    private const CACHE_TTL = 3600;

    private const OZ_TO_GRAM = 31.1035;

    private const KARAT_PURITY = [
        24 => 1.0,
        22 => 0.9167,
        21 => 0.875,
        18 => 0.75,
        14 => 0.5833,
        10 => 0.4167,
    ];

    public function getGold24kGramUsd(): ?float
    {
        if (Setting::get('zakat.manual_override', '0') === '1') {
            $manual = (float) Setting::get('zakat.gold_per_gram', '0');
            if ($manual > 0) {
                $karat = (int) Setting::get('zakat.default_karat', '24');
                $purity = config("zakat.karat_purity.{$karat}", 1.0);

                return round($manual / $purity, 4);
            }
        }

        $cacheKey = 'gold_24k_gram_usd';
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return (float) $cached;
        }

        $pricePerOz = $this->getMetalPricePerOz('XAU');
        if ($pricePerOz === null) {
            return null;
        }

        $gramPrice = round($pricePerOz / self::OZ_TO_GRAM, 4);
        Cache::put($cacheKey, $gramPrice, self::CACHE_TTL);

        return $gramPrice;
    }

    public function getGoldKaratGramUsd(int $karat): ?float
    {
        $gold24k = $this->getGold24kGramUsd();

        if ($gold24k === null) {
            return null;
        }

        $purities = config('zakat.karat_purity', self::KARAT_PURITY);
        $purity = $purities[$karat] ?? self::KARAT_PURITY[$karat] ?? null;

        if ($purity === null) {
            return null;
        }

        return round($gold24k * $purity, 4);
    }

    public function getSilverGramUsd(): ?float
    {
        if (Setting::get('zakat.manual_override', '0') === '1') {
            $manual = (float) Setting::get('zakat.silver_per_gram', '0');
            if ($manual > 0) {
                return $manual;
            }
        }

        $cacheKey = 'silver_gram_usd';
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return (float) $cached;
        }

        $pricePerOz = $this->getMetalPricePerOz('XAG');
        if ($pricePerOz === null) {
            return null;
        }

        $gramPrice = round($pricePerOz / self::OZ_TO_GRAM, 4);
        Cache::put($cacheKey, $gramPrice, self::CACHE_TTL);

        return $gramPrice;
    }

    public function convertCurrency(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        return CurrencyHelper::convert($amount, $from, $to);
    }

    public function getMetalPricePerOz(string $symbol): ?float
    {
        $cacheKey = "metal_price_{$symbol}";

        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return (float) $cached;
        }

        try {
            $response = Http::timeout(10)->get(self::BASE_URL . '/' . $symbol);

            if ($response->successful()) {
                $data = $response->json();
                $price = $data['price'] ?? null;

                if ($price !== null) {
                    Cache::put($cacheKey, (float) $price, self::CACHE_TTL);

                    return (float) $price;
                }
            }
        } catch (\Exception $e) {
            Log::warning('GoldPriceService: Failed to fetch metal price', [
                'symbol' => $symbol,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
