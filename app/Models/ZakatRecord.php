<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZakatRecord extends Model
{
    use BelongsToWorkspace, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    protected $fillable = [
        'user_id', 'workspace_id', 'calculation_date', 'hijri_year',
        'nisab_gold', 'nisab_silver',
        'gold_value', 'silver_value', 'cash_value', 'bank_value', 'ccp_value',
        'business_goods_value', 'stocks_value', 'crypto_value', 'real_estate_value',
        'expected_receivables', 'total_wealth', 'total_zakatable',
        'exceeds_nisab', 'zakat_amount', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'calculation_date' => 'date',
            'nisab_gold' => 'decimal:2',
            'nisab_silver' => 'decimal:2',
            'total_wealth' => 'decimal:2',
            'total_zakatable' => 'decimal:2',
            'zakat_amount' => 'decimal:2',
            'exceeds_nisab' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(ZakatAsset::class);
    }

    public static function calculateNisabGold(float $goldPricePerGram): float
    {
        return $goldPricePerGram * config('zakat.nisab.gold_grams', 85);
    }

    public static function calculateNisabSilver(float $silverPricePerGram): float
    {
        return $silverPricePerGram * config('zakat.nisab.silver_grams', 595);
    }

    public static function calculateZakat(float $zakatableWealth): float
    {
        return round($zakatableWealth * config('zakat.zakat_rate', 0.025), 2);
    }
}
