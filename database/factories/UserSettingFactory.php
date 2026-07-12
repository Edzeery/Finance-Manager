<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserSetting;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSettingFactory extends Factory
{
    protected $model = UserSetting::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'key' => fake()->word(),
            'value' => fake()->word(),
        ];
    }
}
