<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debt_payments', function (Blueprint $table) {
            $table->foreignId('expense_id')->nullable()->after('notes')
                ->constrained('expenses')->nullOnDelete();
            $table->foreignId('income_id')->nullable()->after('expense_id')
                ->constrained('incomes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('debt_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expense_id');
            $table->dropConstrainedForeignId('income_id');
        });
    }
};
