<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignRole extends Command
{
    protected $signature = 'role:assign {user : User ID or email} {role : Role slug}';

    protected $description = 'Assign a role to a user';

    public function handle(): int
    {
        $userInput = $this->argument('user');
        $roleSlug = $this->argument('role');

        $user = User::where('id', $userInput)->orWhere('email', $userInput)->first();

        if (! $user) {
            $this->error("User not found: {$userInput}");

            return Command::FAILURE;
        }

        $role = Role::where('slug', $roleSlug)->first();

        if (! $role) {
            $this->error("Role not found: {$roleSlug}");

            return Command::FAILURE;
        }

        if ($user->hasRole($roleSlug)) {
            $this->warn("User '{$user->email}' already has the '{$roleSlug}' role.");

            return Command::SUCCESS;
        }

        $user->roles()->attach($role);
        $this->info("Role '{$roleSlug}' assigned to user '{$user->email}'.");

        return Command::SUCCESS;
    }
}
