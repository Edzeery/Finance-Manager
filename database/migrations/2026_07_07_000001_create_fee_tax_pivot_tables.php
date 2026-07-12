<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_method_tax_rate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tax_rate_id')->constrained()->cascadeOnDelete();
            $table->enum('charge_type', ['gateway_fee', 'tax_added', 'tax_disclosed']);
            $table->timestamps();
            $table->unique(['payment_method_id', 'tax_rate_id', 'charge_type'], 'pmtr_unique');
        });

        Schema::create('coupon_payment_method', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['coupon_id', 'payment_method_id'], 'cpm_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_payment_method');
        Schema::dropIfExists('payment_method_tax_rate');
    }
};
