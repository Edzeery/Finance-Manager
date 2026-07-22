<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('subscriptions', 'payment_method_id')) {
                $table->foreignId('payment_method_id')->nullable()->after('subscription_plan_id')->constrained('payment_methods')->nullOnDelete();
            }
        });

        $subscriptions = DB::table('subscriptions')
            ->whereNull('payment_method_id')
            ->whereNotNull('payment_method')
            ->where('payment_method', '!=', '')
            ->where('payment_method', '!=', 'free')
            ->where('payment_method', '!=', 'trial')
            ->get();

        if ($subscriptions->isNotEmpty()) {
            foreach ($subscriptions as $sub) {
                $pmId = DB::table('payment_methods')
                    ->where('key', $sub->payment_method)
                    ->value('id');

                if ($pmId) {
                    DB::table('subscriptions')
                        ->where('id', $sub->id)
                        ->update(['payment_method_id' => $pmId]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'payment_method_id')) {
                $table->dropForeign(['payment_method_id']);
                $table->dropColumn('payment_method_id');
            }
        });
    }
};
