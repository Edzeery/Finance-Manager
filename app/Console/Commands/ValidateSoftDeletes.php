<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class ValidateSoftDeletes extends Command
{
    protected $signature = 'finance:validate-soft-deletes';
    protected $description = 'Assert every model using SoftDeletes has a deleted_at column on its table';

    public function handle(): int
    {
        $modelsPath = app_path('Models');
        $files = File::glob("{$modelsPath}/*.php");

        $failures = [];

        foreach ($files as $file) {
            $className = 'App\\Models\\' . pathinfo($file, PATHINFO_FILENAME);

            if (!class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            if (!$reflection->isSubclassOf(Model::class)) {
                continue;
            }

            $traits = $reflection->getTraitNames();
            $reflectTraits = $reflection->getTraits();
            $hasSoftDeletes = false;
            foreach ($reflectTraits as $trait) {
                if ($trait->getShortName() === 'SoftDeletes') {
                    $hasSoftDeletes = true;
                    break;
                }
            }

            if (!$hasSoftDeletes) {
                continue;
            }

            /** @var Model $instance */
            $instance = new $className;
            $table = $instance->getTable();

            try {
                $columns = DB::getSchemaBuilder()->getColumnListing($table);
                if (!in_array('deleted_at', $columns, true)) {
                    $failures[] = "{$className} (table: {$table})";
                }
            } catch (\Exception $e) {
                $failures[] = "{$className} (table: {$table}) — error: {$e->getMessage()}";
            }
        }

        if (empty($failures)) {
            $this->info('All SoftDeletes models have deleted_at columns.');
            return Command::SUCCESS;
        }

        $this->error('Models using SoftDeletes without deleted_at column:');
        foreach ($failures as $failure) {
            $this->warn("  - {$failure}");
        }

        return Command::FAILURE;
    }
}
