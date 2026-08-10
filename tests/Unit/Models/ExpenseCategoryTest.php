<?php

namespace Tests\Unit\Models;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_budget_percentage_is_stored_and_cast_to_decimal(): void
    {
        $user = User::factory()->create();
        $category = ExpenseCategory::create([
            'user_id' => $user->id,
            'name_ar' => 'غذاء',
            'name_fr' => 'Nourriture',
            'name_en' => 'Food',
            'icon' => 'bi-cart',
            'color' => '#EF4444',
            'type' => 'variable',
            'default_budget_percentage' => 15,
        ]);

        $this->assertEquals('15.00', $category->fresh()->default_budget_percentage);
    }

    public function test_default_budget_percentage_is_nullable(): void
    {
        $user = User::factory()->create();
        $category = ExpenseCategory::factory()->create(['user_id' => $user->id]);

        $this->assertNull($category->fresh()->default_budget_percentage);
    }
}
