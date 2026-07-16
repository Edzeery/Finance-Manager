<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', [
                'active',
                'inactive',
                'pending',
                'suspended',
                'banned',
            ])->default('active')->after('is_active');
            $table->enum('online_status', ['online', 'offline'])->default('offline')->after('status');
        });

        DB::statement("
            UPDATE users SET status = CASE
                WHEN is_active = 1 THEN 'active'
                WHEN is_active = 0 THEN 'inactive'
                WHEN is_active = 2 THEN 'suspended'
                WHEN is_active >= 3 THEN 'banned'
                ELSE 'active'
            END
        ");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('email_verified_at');
        });

        DB::statement("
            UPDATE users SET is_active = CASE status
                WHEN 'active' THEN 1
                WHEN 'inactive' THEN 0
                WHEN 'pending' THEN 2
                WHEN 'suspended' THEN 2
                WHEN 'banned' THEN 3
                ELSE 1
            END
        ");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'online_status']);
        });
    }
};
