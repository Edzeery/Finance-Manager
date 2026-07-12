<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE incomes ADD FULLTEXT incomes_description_notes_fulltext(description, notes)');
        DB::statement('ALTER TABLE expenses ADD FULLTEXT expenses_description_notes_fulltext(description, notes)');
        DB::statement('ALTER TABLE debts ADD FULLTEXT debts_counterparty_notes_fulltext(counterparty_name, notes)');
        DB::statement('ALTER TABLE assets ADD FULLTEXT assets_name_description_fulltext(name, description)');
        DB::statement('ALTER TABLE budgets ADD FULLTEXT budgets_name_fulltext(name_ar, name_fr, name_en)');
        DB::statement('ALTER TABLE financial_goals ADD FULLTEXT financial_goals_name_fulltext(name_ar, name_fr, name_en)');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('incomes', fn(Blueprint $t) => $t->dropIndex('incomes_description_notes_fulltext'));
        Schema::table('expenses', fn(Blueprint $t) => $t->dropIndex('expenses_description_notes_fulltext'));
        Schema::table('debts', fn(Blueprint $t) => $t->dropIndex('debts_counterparty_notes_fulltext'));
        Schema::table('assets', fn(Blueprint $t) => $t->dropIndex('assets_name_description_fulltext'));
        Schema::table('budgets', fn(Blueprint $t) => $t->dropIndex('budgets_name_fulltext'));
        Schema::table('financial_goals', fn(Blueprint $t) => $t->dropIndex('financial_goals_name_fulltext'));
    }
};
