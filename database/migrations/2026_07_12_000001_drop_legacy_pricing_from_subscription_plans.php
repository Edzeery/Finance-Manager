<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['monthly_price', 'yearly_discount_percent', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->decimal('monthly_price', 10, 2)->nullable()->after('is_free');
            $table->decimal('yearly_discount_percent', 5, 2)->nullable()->after('monthly_price');
            $table->string('currency', 3)->default('USD')->after('yearly_discount_percent');
        });
    }
};
