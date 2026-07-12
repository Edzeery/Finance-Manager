<?php

namespace App\Enums;

enum AssetType: string
{
    case Cash = 'cash';
    case BankAccount = 'bank_account';
    case CCP = 'ccp';
    case Gold = 'gold';
    case Silver = 'silver';
    case RealEstate = 'real_estate';
    case Stocks = 'stocks';
    case Crypto = 'crypto';
    case Other = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Cash => __('asset.cash'),
            self::BankAccount => __('asset.bank_account'),
            self::CCP => __('asset.ccp'),
            self::Gold => __('asset.gold'),
            self::Silver => __('asset.silver'),
            self::RealEstate => __('asset.real_estate'),
            self::Stocks => __('asset.stocks'),
            self::Crypto => __('asset.crypto'),
            self::Other => __('asset.other'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cash => 'bi-cash',
            self::BankAccount => 'bi-bank',
            self::CCP => 'bi-envelope',
            self::Gold => 'bi-gem',
            self::Silver => 'bi-gem',
            self::RealEstate => 'bi-house',
            self::Stocks => 'bi-bar-chart',
            self::Crypto => 'bi-currency-bitcoin',
            self::Other => 'bi-box',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Cash => '#22C55E',
            self::BankAccount => '#3B82F6',
            self::CCP => '#8B5CF6',
            self::Gold => '#FFC107',
            self::Silver => '#94A3B8',
            self::RealEstate => '#F59E0B',
            self::Stocks => '#06B6D4',
            self::Crypto => '#EF4444',
            self::Other => '#64748B',
        };
    }

    public function isZakatable(): bool
    {
        return match ($this) {
            self::Cash, self::BankAccount, self::CCP,
            self::Gold, self::Silver, self::Stocks,
            self::Crypto => true,
            self::RealEstate, self::Other => false,
        };
    }

    public static function zakatable(): array
    {
        return [
            self::Cash,
            self::BankAccount,
            self::CCP,
            self::Gold,
            self::Silver,
            self::Stocks,
            self::Crypto,
        ];
    }

    public static function zakatableValues(): array
    {
        return array_map(fn($case) => $case->value, self::zakatable());
    }
}
