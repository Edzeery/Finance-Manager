<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->boolean('count_at_incurrence')->default(false)->after('type');
            $table->foreignId('expense_category_id')->nullable()->after('count_at_incurrence')
                ->constrained('expense_categories')->nullOnDelete();
            $table->foreignId('income_category_id')->nullable()->after('expense_category_id')
                ->constrained('income_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expense_category_id');
            $table->dropConstrainedForeignId('income_category_id');
            $table->dropColumn('count_at_incurrence');
        });
    }
};
