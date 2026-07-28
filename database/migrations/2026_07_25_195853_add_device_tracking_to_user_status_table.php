<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_status', function (Blueprint $table) {
            $table->text('last_user_agent')->nullable()->after('last_login_ip');
            $table->string('last_device', 20)->nullable()->after('last_user_agent');
            $table->string('last_browser', 50)->nullable()->after('last_device');
            $table->string('last_os', 50)->nullable()->after('last_browser');
        });
    }

    public function down(): void
    {
        Schema::table('user_status', function (Blueprint $table) {
            $table->dropColumn(['last_user_agent', 'last_device', 'last_browser', 'last_os']);
        });
    }
};
