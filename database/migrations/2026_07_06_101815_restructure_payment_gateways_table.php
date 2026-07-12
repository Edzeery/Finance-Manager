<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn(['credentials', 'is_active', 'is_public']);
            $table->json('fields')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn('fields');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->json('credentials')->nullable();
        });
    }
};
