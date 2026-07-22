<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zakat_records', function (Blueprint $table) {
            $table->string('calendar_type', 10)->default('hijri')->after('hijri_year');
        });
    }

    public function down(): void
    {
        Schema::table('zakat_records', function (Blueprint $table) {
            $table->dropColumn('calendar_type');
        });
    }
};
