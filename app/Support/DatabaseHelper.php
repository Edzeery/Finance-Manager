<?php

namespace App\Support;

use App\Enums\GoalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DatabaseHelper
{
    public static function monthExpression(string $column = 'date'): string
    {
        $driver = DB::connection()->getDriverName();

        return $driver === 'mysql'
            ? "DATE_FORMAT($column, '%Y-%m')"
            : "strftime('%Y-%m', $column)";
    }

    public static function goalStatusOrderExpression(): string
    {
        $driver = DB::connection()->getDriverName();

        return $driver === 'mysql'
            ? "FIELD(status, '" . GoalStatus::InProgress->value . "', '" . GoalStatus::Completed->value . "', '" . GoalStatus::Cancelled->value . "')"
            : "CASE status WHEN '" . GoalStatus::InProgress->value . "' THEN 0 WHEN '" . GoalStatus::Completed->value . "' THEN 1 WHEN '" . GoalStatus::Cancelled->value . "' THEN 2 ELSE 3 END";
    }

    public static function applyFulltextToQuery(Builder $query, array $columns, string $search): void
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            $query->whereFulltext($columns, $search);
        } else {
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }
    }
}
