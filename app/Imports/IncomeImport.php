<?php

namespace App\Imports;

use App\Models\Income;
use App\Models\IncomeCategory;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class IncomeImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private int $imported = 0;

    private array $failures = [];

    public function __construct(
        private int $userId,
        private int $workspaceId,
    ) {}

    public function model(array $row): ?Income
    {
        $category = null;
        if (! empty($row['category'])) {
            $category = IncomeCategory::where(function ($q) use ($row) {
                $q->where('name_ar', $row['category'])
                    ->orWhere('name_en', $row['category'])
                    ->orWhere('name_fr', $row['category']);
            })->first();
        }

        $this->imported++;

        return new Income([
            'user_id' => $this->userId,
            'workspace_id' => $this->workspaceId,
            'category_id' => $category?->id,
            'amount' => static::normalizeAmount($row['amount']),
            'description' => $row['description'] ?? null,
            'date' => $row['date'] ?? now()->format('Y-m-d'),
            'is_recurring' => ! empty($row['recurring']) && in_array(strtolower($row['recurring']), ['yes', '1', 'true', 'نعم']),
            'notes' => $row['notes'] ?? null,
        ]);
    }

    public static function normalizeAmount(string|int|float|null $value): float
    {
        $value = trim((string) $value);
        $value = str_replace([' ', "\xc2\xa0"], '', $value);
        if (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $value)) {
            $value = str_replace(',', '', $value);
        } else {
            $value = str_replace(',', '.', $value);
        }

        return (float) $value;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required',
            'date' => 'nullable|date',
        ];
    }

    public function onFailure(Failure ...$failures): void
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }
}
