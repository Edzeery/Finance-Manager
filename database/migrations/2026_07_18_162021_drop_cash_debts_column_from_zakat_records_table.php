<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zakat_records', function (Blueprint $table) {
            $table->dropColumn('cash_debts');
        });
    }

    public function down(): void
    {
        Schema::table('zakat_records', function (Blueprint $table) {
            $table->decimal('cash_debts', 15, 2)->nullable()->after('expected_receivables');
        });
    }
};
