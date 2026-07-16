<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended', 'banned'])->default('active');
            $table->enum('online_status', ['online', 'offline'])->default('offline');
            $table->text('status_reason')->nullable();
            $table->foreignId('status_changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index('status');
        });

        DB::table('user_status')->insertUsing(
            ['user_id', 'status', 'online_status', 'status_changed_at', 'created_at', 'updated_at'],
            DB::table('users')->select(
                'id',
                DB::raw("'active' as status"),
                DB::raw("'offline' as online_status"),
                'created_at as status_changed_at',
                'created_at',
                'updated_at'
            )
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('user_status');
    }
};
