<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use BelongsToWorkspace;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    protected $fillable = ['user_id', 'workspace_id', 'action', 'subject_type', 'subject_id', 'description', 'properties', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return ['properties' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabel(): string
    {
        return match ($this->action) {
            'created' => __('general.created'),
            'updated' => __('general.updated'),
            'deleted' => __('general.deleted'),
            'restored' => __('general.restore'),
            'login' => __('general.login'),
            'logout' => __('general.logout'),
            'two_factor_enabled' => __('general.two_factor_enabled'),
            'two_factor_disabled' => __('general.two_factor_disabled'),
            default => $this->action,
        };
    }

    public function getSubjectNameAttribute(): string
    {
        if (! $this->subject_type) {
            return __('general.unknown');
        }

        $name = class_basename($this->subject_type);
        $map = [
            'Income' => 'income.title',
            'Expense' => 'expense.title',
            'Debt' => 'debt.title',
            'Asset' => 'asset.title',
            'Budget' => 'budget.title',
            'FinancialGoal' => 'goal.title',
            'ZakatRecord' => 'zakat.title',
            'ExpenseCategory' => 'expense.categories',
            'IncomeCategory' => 'income.categories',
            'User' => 'general.profile',
            'UserSetting' => 'general.settings',
        ];

        return $map[$name] ?? $name;
    }

    public function getChangesSummaryAttribute(): array
    {
        $props = $this->properties ?? [];
        $ignored = ['updated_at', 'created_at', 'remember_token', 'deleted_at'];

        if (empty($props['old']) || empty($props['new'])) {
            return [];
        }

        $changes = [];
        foreach ($props['new'] as $key => $newValue) {
            if (in_array($key, $ignored)) {
                continue;
            }

            $oldValue = $props['old'][$key] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                    'label' => $this->getFieldLabel($key),
                ];
            }
        }

        return $changes;
    }

    private function getFieldLabel(string $key): string
    {
        $labels = [
            'amount' => 'amount',
            'description' => 'description',
            'category_id' => 'category',
            'date' => 'date',
            'total_amount' => 'amount',
            'paid_amount' => 'amount',
            'counterparty_name' => 'description',
            'due_date' => 'date',
            'status' => 'status',
            'total_value' => 'amount',
            'name' => 'name',
            'target_amount' => 'amount',
            'current_amount' => 'amount',
            'target_date' => 'date',
            'icon' => 'icon',
            'color' => 'color',
            'type' => 'type',
            'status' => 'status',
            'is_recurring' => 'general.recurring',
            'recurring_frequency' => 'general.frequency',
            'recurring_end_date' => 'general.date',
            'is_liquid' => 'asset.liquid',
            'bank_name' => 'asset.bank',
            'account_number' => 'asset.account',
            'notes' => 'general.notes',
        ];

        return $labels[$key] ?? $key;
    }

    public function getActionIcon(): string
    {
        return match ($this->action) {
            'created' => 'bi-plus-circle-fill',
            'updated' => 'bi-pencil-fill',
            'deleted' => 'bi-trash-fill',
            'restored' => 'bi-arrow-counterclockwise',
            'login' => 'bi-box-arrow-in-right',
            'logout' => 'bi-box-arrow-left',
            'two_factor_enabled' => 'bi-shield-check',
            'two_factor_disabled' => 'bi-shield-slash',
            default => 'bi-activity',
        };
    }

    public function getActionColor(): string
    {
        return match ($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'restored' => 'warning',
            'login' => 'purple',
            'logout' => 'secondary',
            'two_factor_enabled' => 'success',
            'two_factor_disabled' => 'warning',
            default => 'primary',
        };
    }
}
