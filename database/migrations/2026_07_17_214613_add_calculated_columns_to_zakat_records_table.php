<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zakat_records', function (Blueprint $table) {
            $table->decimal('total_debts', 15, 2)->nullable()->after('total_zakatable');
            $table->decimal('cash_zakat', 15, 2)->nullable()->after('zakat_amount');
            $table->decimal('gold_zakat', 15, 2)->nullable()->after('cash_zakat');
            $table->decimal('silver_zakat', 15, 2)->nullable()->after('gold_zakat');
            $table->decimal('business_zakat', 15, 2)->nullable()->after('silver_zakat');
            $table->decimal('investments_zakat', 15, 2)->nullable()->after('business_zakat');
        });
    }

    public function down(): void
    {
        Schema::table('zakat_records', function (Blueprint $table) {
            $table->dropColumn([
                'total_debts', 'cash_zakat', 'gold_zakat',
                'silver_zakat', 'business_zakat', 'investments_zakat',
            ]);
        });
    }
};
