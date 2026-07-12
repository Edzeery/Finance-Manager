<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillWorkspaceId extends Command
{
    protected $signature = 'workspace:backfill-ids {--dry-run : Show what would be updated without making changes}';

    protected $description = 'Backfill NULL workspace_id on child tables where possible';

    private array $tables = [
        'debt_payments'       => ['parent_table' => 'debts', 'parent_fk' => 'debt_id'],
        'budget_categories'   => ['parent_table' => 'budgets', 'parent_fk' => 'budget_id'],
        'zakat_assets'        => ['parent_table' => 'zakat_records', 'parent_fk' => 'zakat_record_id'],
        'payment_verifications' => ['parent_table' => 'payments', 'parent_fk' => 'payment_id'],
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $totalUpdated = 0;
        $totalSkipped = 0;

        foreach ($this->tables as $childTable => $config) {
            if (!DB::getSchemaBuilder()->hasColumn($childTable, 'workspace_id')) {
                $this->warn("Table '{$childTable}' has no workspace_id column — skipping.");
                continue;
            }

            $parentTable = $config['parent_table'];
            $parentFk = $config['parent_fk'];

            $nullRows = DB::table($childTable)
                ->whereNull('workspace_id')
                ->get();

            if ($nullRows->isEmpty()) {
                $this->info("{$childTable}: No NULL workspace_id rows found.");
                continue;
            }

            $updated = 0;
            $skipped = 0;

            foreach ($nullRows as $row) {
                $parentRow = DB::table($parentTable)->find($row->{$parentFk});

                if (!$parentRow || !isset($parentRow->workspace_id) || !$parentRow->workspace_id) {
                    $skipped++;
                    continue;
                }

                if (!$dryRun) {
                    DB::table($childTable)
                        ->where('id', $row->id)
                        ->update(['workspace_id' => $parentRow->workspace_id]);
                }

                $updated++;
            }

            $this->line("{$childTable}: {$updated} updated, {$skipped} skipped (parent missing/no workspace_id)");
            $totalUpdated += $updated;
            $totalSkipped += $skipped;
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total updated', $totalUpdated],
                ['Total skipped', $totalSkipped],
            ]
        );

        if ($dryRun) {
            $this->warn('Dry run — no changes were made.');
        }

        return Command::SUCCESS;
    }
}
