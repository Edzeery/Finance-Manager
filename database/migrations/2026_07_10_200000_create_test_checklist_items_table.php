<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('item_key');
            $table->text('description');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->foreignId('tested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tested_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_checklist_items');
    }
};
