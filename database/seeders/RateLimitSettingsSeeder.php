<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class RateLimitSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = config('finance.rate_limits', []);

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(
                ['key' => "rate_limit.{$key}"],
                ['value' => (string) $value]
            );
        }
    }
}
