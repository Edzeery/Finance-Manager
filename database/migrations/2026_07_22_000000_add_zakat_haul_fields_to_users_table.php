<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('zakat_start_date')->nullable()->after('onboarding_completed_at');
            $table->string('calendar_type', 10)->default('hijri')->after('zakat_start_date');
            $table->date('last_zakat_date')->nullable()->after('calendar_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['zakat_start_date', 'calendar_type', 'last_zakat_date']);
        });
    }
};
