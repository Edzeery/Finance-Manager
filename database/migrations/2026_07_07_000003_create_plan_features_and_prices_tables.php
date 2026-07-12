<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Plan Features (dictionary)
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('name_fr');
            $table->string('type')->default('boolean')->comment('boolean, value, text');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_core')->default(false)->comment('تظهر تلقائياً في كل الخطط');
            $table->timestamps();
        });

        // 2. Plan-PlanFeature pivot
        Schema::create('plan_plan_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->foreignId('plan_feature_id')->constrained('plan_features')->cascadeOnDelete();
            $table->string('value')->nullable()->comment('قيمة الميزة للعرض: 10, غير محدود, 1GB');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['plan_id', 'plan_feature_id']);
        });

        // 3. Plan Prices (multi-currency)
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->string('billing_period', 10);
            $table->string('currency', 10)->default('USD');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['plan_id', 'billing_period', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
        Schema::dropIfExists('plan_plan_feature');
        Schema::dropIfExists('plan_features');
    }
};
