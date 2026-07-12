<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateWorkspaceRoles extends Command
{
    protected $signature = 'workspace:roles-migrate';

    protected $description = 'Migrate user_workspace.role strings to workspace_role_user pivot table (completed)';

    public function handle(): int
    {
        $this->warn('The user_workspace.role column has been removed.');
        $this->warn('Role data is now stored exclusively in workspace_role_user.');
        $this->info('No migration needed — the system is already using workspace_role_user.');
        $this->info('Run "php artisan migrate:fresh --seed" to re-seed if needed.');

        return Command::SUCCESS;
    }
}
