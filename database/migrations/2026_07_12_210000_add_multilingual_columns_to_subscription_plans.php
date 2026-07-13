<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->string('name_en')->after('name');
            $table->string('name_ar')->nullable()->after('name_en');
            $table->string('name_fr')->nullable()->after('name_ar');
            $table->text('description_en')->nullable()->after('description');
            $table->text('description_ar')->nullable()->after('description_en');
            $table->text('description_fr')->nullable()->after('description_ar');
            $table->string('button_text_en', 100)->nullable()->after('button_text');
            $table->string('button_text_ar', 100)->nullable()->after('button_text_en');
            $table->string('button_text_fr', 100)->nullable()->after('button_text_ar');
        });

        DB::table('subscription_plans')->update([
            'name_en' => DB::raw('name'),
            'description_en' => DB::raw('description'),
            'button_text_en' => DB::raw('button_text'),
        ]);
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'name_ar', 'name_fr']);
            $table->dropColumn(['description_en', 'description_ar', 'description_fr']);
            $table->dropColumn(['button_text_en', 'button_text_ar', 'button_text_fr']);
        });
    }
};
