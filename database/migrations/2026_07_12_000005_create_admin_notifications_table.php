<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g. 'new_user', 'new_payment', 'new_subscription', 'backup_completed', 'system_alert'
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('title_fr')->nullable();
            $table->text('message_en')->nullable();
            $table->text('message_ar')->nullable();
            $table->text('message_fr')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('is_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
