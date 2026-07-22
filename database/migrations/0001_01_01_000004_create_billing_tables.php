<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Subscription Plans
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_free')->default(false);
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('yearly_discount_percent', 5, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->integer('max_users')->nullable();
            $table->integer('max_workspaces')->nullable();
            $table->json('limits')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->timestamps();
        });

        // 2. pending_plan_id on users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('pending_plan_id')->nullable()->after('plan_confirmed_at')->constrained('subscription_plans')->nullOnDelete();
        });

        // 3. Subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('active');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('grace_ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->string('billing_period', 10)->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('status');
            $table->index('ends_at');
            $table->index(['user_id', 'status']);
        });

        // 4. Coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type')->default('percentage');
            $table->decimal('value', 15, 2);
            $table->decimal('min_amount', 15, 2)->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 11. Payment Methods (created before payments for FK reference)
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->enum('type', ['online', 'manual', 'auto_complete'])->default('online');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('supported_currencies')->nullable();
            $table->json('credentials')->nullable();
            $table->timestamps();
        });

        // 5. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method');
            $table->string('payment_method_type')->nullable()->comment('Actual payment instrument used: edahabia, cib, card, etc.');
            $table->decimal('amount', 15, 2);
            $table->decimal('original_amount', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('gateway_fee', 15, 2)->default(0);
            $table->decimal('tax_added', 15, 2)->default(0);
            $table->decimal('tax_disclosed', 15, 2)->default(0);
            $table->string('currency', 3)->default('DZD');
            $table->string('status')->default('pending');
            $table->string('reference')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('chargily_checkout_id')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->json('gateway_payload')->nullable();
            $table->json('webhook_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('webhook_processed_at')->nullable();
            $table->foreignId('method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
            $table->index('status');
            $table->index('user_id');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
            $table->index(['subscription_id', 'status']);
        });

        // 5. Payment Verifications
        Schema::create('payment_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('transaction_reference')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        // 6. Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number')->nullable();
            $table->string('status')->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('gateway_fee', 15, 2)->default(0);
            $table->decimal('tax_added', 15, 2)->default(0);
            $table->decimal('tax_disclosed', 15, 2)->default(0);
            $table->decimal('proration_credit', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('currency', 3)->default('DZD');
            $table->string('billing_period')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('status');
        });

        // 7. Invoice Sequences
        Schema::create('invoice_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('prefix')->default('INV-');
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();
        });

        // 8. Tax Rates
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->decimal('rate', 5, 2);
            $table->string('type')->default('percentage');
            $table->string('country', 2)->nullable();
            $table->string('region')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 10. Payment Webhook Logs
        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->string('event_type')->nullable();
            $table->string('checkout_id')->nullable();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->json('payload')->nullable();
            $table->string('status')->default('received');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payment_webhook_logs');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('invoice_sequences');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payment_verifications');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pending_plan_id');
        });

        Schema::dropIfExists('subscription_plans');
    }
};
