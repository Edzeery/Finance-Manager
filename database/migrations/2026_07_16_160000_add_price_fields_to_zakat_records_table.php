<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zakat_records', function (Blueprint $table) {
            $table->decimal('gold_price_per_gram', 15, 2)->nullable()->after('nisab_gold');
            $table->decimal('silver_price_per_gram', 15, 2)->nullable()->after('nisab_silver');
            $table->decimal('gold_weight', 10, 4)->nullable()->after('gold_price_per_gram');
            $table->decimal('silver_weight', 10, 4)->nullable()->after('silver_price_per_gram');
            $table->decimal('cash_debts', 15, 2)->nullable()->after('expected_receivables');
        });
    }

    public function down(): void
    {
        Schema::table('zakat_records', function (Blueprint $table) {
            $table->dropColumn([
                'gold_price_per_gram', 'silver_price_per_gram',
                'gold_weight', 'silver_weight', 'cash_debts',
            ]);
        });
    }
};
