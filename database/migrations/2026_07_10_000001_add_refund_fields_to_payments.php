<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable()->after('canceled_at');
            $table->decimal('refund_amount', 15, 2)->nullable()->after('refunded_at');
            $table->text('refund_reason')->nullable()->after('refund_amount');
            $table->foreignId('refunded_by')->nullable()->after('refund_reason')->constrained('users')->nullOnDelete();
            $table->index('refunded_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['workspace_id', 'status', 'created_at'], 'payments_workspace_status_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_workspace_status_created_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['refunded_by']);
            $table->dropColumn(['refunded_at', 'refund_amount', 'refund_reason', 'refunded_by']);
        });
    }
};
