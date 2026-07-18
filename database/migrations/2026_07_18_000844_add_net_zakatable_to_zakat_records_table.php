<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zakat_records', function (Blueprint $table) {
            $table->decimal('net_zakatable', 15, 2)->nullable()->after('total_debts');
        });
    }

    public function down(): void
    {
        Schema::table('zakat_records', function (Blueprint $table) {
            $table->dropColumn('net_zakatable');
        });
    }
};
