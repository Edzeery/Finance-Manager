<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->decimal('plan_price_amount', 15, 2)->nullable()->after('billing_period')
                ->comment('Snapshot of the plan price at subscription creation/renewal time. Read-only after set.');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('plan_price_amount');
        });
    }
};
