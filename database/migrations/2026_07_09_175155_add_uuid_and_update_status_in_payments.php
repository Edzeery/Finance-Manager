<?php

use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('uuid', 20)->nullable()->after('id');
        });

        $used = [];
        DB::table('payments')->orderBy('id')->each(function ($payment) use (&$used) {
            do {
                $uuid = 'pay-'.Str::lower(Str::random(12));
            } while (isset($used[$uuid]));
            $used[$uuid] = true;
            DB::table('payments')->where('id', $payment->id)->update(['uuid' => $uuid]);
        });

        $mapping = [
            'pending' => PaymentStatus::CheckoutPending->value,
            'completed' => PaymentStatus::CheckoutPaid->value,
            'paid' => PaymentStatus::CheckoutPaid->value,
            'failed' => PaymentStatus::CheckoutFailed->value,
            'canceled' => PaymentStatus::CheckoutCanceled->value,
            'cancelled' => PaymentStatus::CheckoutCanceled->value,
            'expired' => PaymentStatus::CheckoutExpired->value,
        ];

        foreach ($mapping as $old => $new) {
            DB::table('payments')->where('status', $old)->update(['status' => $new]);
        }

        $duplicates = DB::table('payments')
            ->select(DB::raw('MIN(id) as keep_id'), 'uuid', DB::raw('COUNT(*) as cnt'))
            ->groupBy('uuid')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('payments')
                ->where('uuid', $dup->uuid)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->unique('uuid');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });

        $reverseMapping = [
            PaymentStatus::CheckoutPending->value => 'pending',
            PaymentStatus::CheckoutPaid->value => 'completed',
            PaymentStatus::CheckoutFailed->value => 'failed',
            PaymentStatus::CheckoutCanceled->value => 'canceled',
            PaymentStatus::CheckoutExpired->value => 'expired',
        ];

        foreach ($reverseMapping as $new => $old) {
            DB::table('payments')->where('status', $new)->update(['status' => $old]);
        }
    }
};
