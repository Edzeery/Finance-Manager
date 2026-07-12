<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['max_users', 'max_workspaces']);
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->integer('max_users')->nullable()->after('is_public');
            $table->integer('max_workspaces')->nullable()->after('max_users');
        });
    }
};
