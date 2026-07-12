<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;

class ListRoles extends Command
{
    protected $signature = 'role:list {--users : Show users assigned to each role}';

    protected $description = 'List all roles and their permissions';

    public function handle(): int
    {
        $roles = Role::with('permissions')->when($this->option('users'), fn($q) => $q->with('users'))->get();

        if ($roles->isEmpty()) {
            $this->warn('No roles found.');
            return Command::SUCCESS;
        }

        foreach ($roles as $role) {
            $this->info("Role: {$role->name} ({$role->slug})");

            $perms = $role->permissions->pluck('name')->implode(', ');
            $this->line("  Permissions: " . ($perms ?: 'none'));

            if ($this->option('users')) {
                $users = $role->users->pluck('email')->implode(', ');
                $this->line("  Users: " . ($users ?: 'none'));
            }

            $this->newLine();
        }

        return Command::SUCCESS;
    }
}
