<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('payments', 'method_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('method_id')->nullable()->after('method')->constrained('payment_methods')->nullOnDelete();
            });
        }

        // Backfill from the method string column
        $methods = DB::table('payment_methods')->pluck('id', 'key');

        DB::table('payments')
            ->whereNull('method_id')
            ->whereNotNull('method')
            ->orderBy('id')
            ->chunkById(500, function ($payments) use ($methods) {
                foreach ($payments as $payment) {
                    $methodId = $methods[$payment->method] ?? null;
                    if ($methodId) {
                        DB::table('payments')
                            ->where('id', $payment->id)
                            ->update(['method_id' => $methodId]);
                    }
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'method_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['method_id']);
                $table->dropColumn('method_id');
            });
        }
    }
};
