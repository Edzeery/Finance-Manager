<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('name', 255);
            $table->string('category', 50)->default('custom');
            $table->string('icon', 100)->nullable();
            $table->text('description')->nullable();
            $table->json('supported_currencies')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->boolean('sandbox')->default(true);
            $table->boolean('webhook')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('credentials')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
