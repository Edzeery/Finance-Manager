<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Workspaces
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type', 50)->default('personal');
            $table->text('description')->nullable();
            $table->string('currency', 3)->default('DZD');
            $table->string('timezone', 50)->default('Africa/Algiers');
            $table->boolean('is_active')->default(true);
            $table->timestamp('trial_ends_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('created_at');
        });

        // 2. current_workspace_id on users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_workspace_id')->nullable()->after('plan_confirmed_at')->constrained('workspaces')->nullOnDelete();
        });

        // 3. User-Workspace pivot
        Schema::create('user_workspace', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->primary(['user_id', 'workspace_id']);
        });

        // 4. Income Categories
        Schema::create('income_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_fr');
            $table->string('name_en');
            $table->string('icon')->nullable();
            $table->string('color', 7)->nullable();
            $table->string('type', 50);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 5. Expense Categories
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_fr');
            $table->string('name_en');
            $table->string('icon')->nullable();
            $table->string('color', 7)->nullable();
            $table->string('type', 50);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 6. Incomes
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('income_categories')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->date('date');
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency')->nullable();
            $table->date('recurring_end_date')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('date');
            $table->index('is_archived');
            $table->index('created_at');
            $table->index(['workspace_id', 'date']);
        });

        // 7. Expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->date('date');
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency')->nullable();
            $table->date('recurring_end_date')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('date');
            $table->index('is_archived');
            $table->index('created_at');
            $table->index(['workspace_id', 'date']);
        });

        // 8. Debts
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 50);
            $table->string('counterparty_name')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->date('reminder_date')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('status');
            $table->index('type');
            $table->index('due_date');
            $table->index('created_at');
        });

        // 9. Debt Payments
        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['debt_id', 'created_at']);
        });

        // 10. Assets
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 50);
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 15, 4)->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->decimal('total_value', 15, 2);
            $table->string('currency', 3)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->boolean('is_liquid')->default(false);
            $table->boolean('is_zakatable')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        // 11. Financial Goals
        Schema::create('financial_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_fr');
            $table->string('name_en');
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->date('target_date')->nullable();
            $table->string('status')->default('active');
            $table->string('icon')->nullable();
            $table->string('color', 7)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        // 12. Budgets
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_fr');
            $table->string('name_en');
            $table->string('type', 50);
            $table->decimal('total_amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        // 13. Budget Categories
        Schema::create('budget_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('allocated_amount', 15, 2);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->timestamps();
        });

        // 14. Zakat Records
        Schema::create('zakat_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->date('calculation_date');
            $table->string('hijri_year')->nullable();
            $table->decimal('nisab_gold', 15, 2)->nullable();
            $table->decimal('nisab_silver', 15, 2)->nullable();
            $table->decimal('gold_value', 15, 2)->nullable();
            $table->decimal('silver_value', 15, 2)->nullable();
            $table->decimal('cash_value', 15, 2)->nullable();
            $table->decimal('bank_value', 15, 2)->nullable();
            $table->decimal('ccp_value', 15, 2)->nullable();
            $table->decimal('business_goods_value', 15, 2)->nullable();
            $table->decimal('stocks_value', 15, 2)->nullable();
            $table->decimal('crypto_value', 15, 2)->nullable();
            $table->decimal('real_estate_value', 15, 2)->nullable();
            $table->decimal('expected_receivables', 15, 2)->nullable();
            $table->decimal('total_wealth', 15, 2);
            $table->decimal('total_zakatable', 15, 2);
            $table->boolean('exceeds_nisab')->default(false);
            $table->decimal('zakat_amount', 15, 2);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 15. Zakat Assets
        Schema::create('zakat_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zakat_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 50);
            $table->string('name');
            $table->decimal('value', 15, 2);
            $table->boolean('is_zakatable')->default(true);
            $table->decimal('zakatable_value', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 16. Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->text('description')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index('action');
            $table->index('created_at');
        });

        // 17. Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('title_ar')->nullable();
            $table->string('title_fr')->nullable();
            $table->string('title_en')->nullable();
            $table->text('message_ar')->nullable();
            $table->text('message_fr')->nullable();
            $table->text('message_en')->nullable();
            $table->text('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index('is_read');
        });

        // 18. User Settings
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('zakat_assets');
        Schema::dropIfExists('zakat_records');
        Schema::dropIfExists('budget_categories');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('financial_goals');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('debt_payments');
        Schema::dropIfExists('debts');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('income_categories');
        Schema::dropIfExists('user_workspace');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_workspace_id');
        });

        Schema::dropIfExists('workspaces');
    }
};
