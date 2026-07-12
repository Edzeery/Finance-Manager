<?php

namespace App\Enums;

enum GoalStatus: string
{
    case Active = 'active';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Active, self::InProgress => __('goal.in_progress'),
            self::Completed => __('goal.completed'),
            self::Cancelled => __('goal.cancelled'),
        };
    }
}
