<?php

namespace App\Enums;

enum DebtType: string
{
    case Owed = 'owed';
    case Owing = 'owing';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Owed => __('debt.owed'),
            self::Owing => __('debt.owing'),
        };
    }
}
