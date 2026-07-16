<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $limitKeys = [
        'transactions_per_month',
        'api_requests_per_minute',
        'api_requests_per_hour',
        'api_requests_per_day',
        'max_api_tokens',
    ];

    public function up(): void
    {
        $features = DB::table('plan_features')
            ->whereIn('slug', $this->limitKeys)
            ->pluck('id', 'slug');

        DB::table('subscription_plans')
            ->orderBy('id')
            ->chunkById(100, function ($plans) use ($features) {
                foreach ($plans as $plan) {
                    if (! $plan->limits) {
                        continue;
                    }

                    $limits = json_decode($plan->limits, true);
                    if (! $limits || ! is_array($limits)) {
                        continue;
                    }

                    foreach ($this->limitKeys as $key) {
                        if (! isset($limits[$key])) {
                            continue;
                        }

                        $featureId = $features[$key] ?? null;
                        if (! $featureId) {
                            continue;
                        }

                        $exists = DB::table('plan_plan_feature')
                            ->where('plan_id', $plan->id)
                            ->where('plan_feature_id', $featureId)
                            ->exists();

                        if (! $exists) {
                            DB::table('plan_plan_feature')->insert([
                                'plan_id' => $plan->id,
                                'plan_feature_id' => $featureId,
                                'value' => (string) $limits[$key],
                                'sort_order' => 999,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            });

        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('limits');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->json('limits')->nullable()->after('yearly_discount_percent');
        });

        $features = DB::table('plan_features')
            ->whereIn('slug', $this->limitKeys)
            ->pluck('id', 'slug');

        $featureIds = $features->values()->toArray();

        DB::table('subscription_plans')
            ->orderBy('id')
            ->chunkById(100, function ($plans) use ($features, $featureIds) {
                foreach ($plans as $plan) {
                    $limits = [];

                    $pivots = DB::table('plan_plan_feature')
                        ->where('plan_id', $plan->id)
                        ->whereIn('plan_feature_id', $featureIds)
                        ->get();

                    $slugById = $features->flip();

                    foreach ($pivots as $pivot) {
                        $slug = $slugById[$pivot->plan_feature_id] ?? null;
                        if ($slug) {
                            $limits[$slug] = is_numeric($pivot->value) ? (float) $pivot->value : $pivot->value;
                        }
                    }

                    DB::table('subscription_plans')
                        ->where('id', $plan->id)
                        ->update(['limits' => empty($limits) ? null : json_encode($limits)]);
                }
            });

        DB::table('plan_plan_feature')
            ->whereIn('plan_feature_id', $featureIds)
            ->delete();
    }
};
