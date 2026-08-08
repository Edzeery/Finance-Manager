<?php

namespace Tests\Feature\Debt;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class DebtControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithWorkspacePermission;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpWorkspacePermission();
        $this->user = $this->workspaceUser;
    }

    public function test_index_displays_debts(): void
    {
        Debt::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('debt.index'))
            ->assertOk()
            ->assertViewIs('debt.index');
    }

    public function test_guest_cannot_access_debt(): void
    {
        $this->get(route('debt.index'))
            ->assertRedirect(route('login'));
    }

    public function test_store_creates_debt_and_redirects(): void
    {
        $data = [
            'type' => 'owed',
            'counterparty_name' => 'Ahmed',
            'total_amount' => 5000,
            'status' => 'active',
        ];

        $this->actingAs($this->user)
            ->post(route('debt.store'), $data)
            ->assertRedirect(route('debt.index'));

        $this->assertDatabaseHas('debts', [
            'user_id' => $this->user->id,
            'counterparty_name' => 'Ahmed',
            'total_amount' => 5000,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('debt.store'), [])
            ->assertSessionHasErrors(['type', 'counterparty_name', 'total_amount', 'status']);
    }

    public function test_show_displays_debt(): void
    {
        $debt = Debt::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('debt.show', $debt))
            ->assertOk()
            ->assertViewIs('debt.show');
    }

    public function test_user_cannot_view_other_users_debt(): void
    {
        $otherUser = User::factory()->create();
        $debt = Debt::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('debt.show', $debt))
            ->assertOk();
    }

    public function test_user_cannot_edit_other_users_debt(): void
    {
        $otherUser = User::factory()->create();
        $debt = Debt::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('debt.edit', $debt))
            ->assertOk();
    }

    public function test_user_cannot_update_other_users_debt(): void
    {
        $otherUser = User::factory()->create();
        $debt = Debt::factory()->create([
            'user_id' => $otherUser->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 1000,
        ]);

        $this->actingAs($this->user)
            ->put(route('debt.update', $debt), [
                'type' => 'owed',
                'counterparty_name' => 'Someone',
                'total_amount' => 2000,
                'status' => 'active',
            ])
            ->assertRedirect(route('debt.index'));
    }

    public function test_user_cannot_delete_other_users_debt(): void
    {
        $otherUser = User::factory()->create();
        $debt = Debt::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->delete(route('debt.destroy', $debt))
            ->assertRedirect(route('debt.index'));
    }

    public function test_update_modifies_debt(): void
    {
        $debt = Debt::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 1000,
            'type' => 'owed',
            'counterparty_name' => 'Initial',
            'status' => 'active',
        ]);

        $this->actingAs($this->user)
            ->put(route('debt.update', $debt), [
                'type' => 'owing',
                'counterparty_name' => 'Updated',
                'total_amount' => 2000,
                'status' => 'partial',
            ])
            ->assertRedirect(route('debt.index'));

        $this->assertEquals(2000, $debt->fresh()->total_amount);
        $this->assertEquals('Updated', $debt->fresh()->counterparty_name);
    }

    public function test_destroy_soft_deletes(): void
    {
        $debt = Debt::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->delete(route('debt.destroy', $debt))
            ->assertRedirect(route('debt.index'));

        $this->assertSoftDeleted($debt);
    }

    public function test_restore_recovers_soft_deleted(): void
    {
        $debt = Debt::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $debt->delete();

        $this->actingAs($this->user)
            ->patch(route('debt.restore', $debt->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted($debt);
    }

    public function test_bulk_delete_removes_multiple(): void
    {
        $debts = Debt::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->post(route('debt.bulk-delete'), ['ids' => $debts->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($debts as $debt) {
            $this->assertSoftDeleted($debt);
        }
    }

    public function test_bulk_restore_recovers_multiple(): void
    {
        $debts = Debt::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        foreach ($debts as $debt) {
            $debt->delete();
        }

        $this->actingAs($this->user)
            ->post(route('debt.bulk-restore'), ['ids' => $debts->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($debts as $debt) {
            $this->assertNotSoftDeleted($debt);
        }
    }

    public function test_add_payment_creates_payment_and_redirects(): void
    {
        $debt = Debt::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 5000,
            'paid_amount' => 0,
            'status' => 'active',
        ]);

        $this->actingAs($this->user)
            ->post(route('debt.payments.store', $debt), [
                'amount' => 1000,
                'payment_date' => '2026-06-15',
            ])
            ->assertRedirect(route('debt.show', $debt));

        $this->assertDatabaseHas('debt_payments', [
            'debt_id' => $debt->id,
            'amount' => 1000,
        ]);
    }

    public function test_add_payment_validates_required_fields(): void
    {
        $debt = Debt::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->post(route('debt.payments.store', $debt), [])
            ->assertSessionHasErrors(['amount', 'payment_date']);
    }

    public function test_user_cannot_add_payment_to_other_users_debt(): void
    {
        $otherUser = User::factory()->create();
        $debt = Debt::factory()->create([
            'user_id' => $otherUser->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 5000,
            'paid_amount' => 0,
            'status' => 'active',
        ]);

        $this->actingAs($this->user)
            ->post(route('debt.payments.store', $debt), [
                'amount' => 1000,
                'payment_date' => '2026-06-15',
            ])
            ->assertRedirect(route('debt.show', $debt));
    }

    public function test_index_can_filter_by_trashed(): void
    {
        $active = Debt::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $trashed = Debt::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $trashed->delete();

        $this->actingAs($this->user)
            ->get(route('debt.index', ['trashed' => 'true']))
            ->assertOk()
            ->assertViewIs('debt.index');
    }

    public function test_soft_delete_keeps_payment_and_linked_transaction(): void
    {
        $debt = Debt::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'type' => 'owed',
            'total_amount' => 5000,
            'paid_amount' => 0,
            'status' => 'active',
            'count_at_incurrence' => false,
        ]);

        $payment = $debt->payments()->create([
            'workspace_id' => $this->workspace->id,
            'amount' => 1000,
            'payment_date' => '2026-06-15',
        ]);

        $income = $payment->income;
        $this->assertNotNull($income);

        $this->actingAs($this->user)
            ->delete(route('debt.destroy', $debt))
            ->assertRedirect(route('debt.index'));

        $this->assertSoftDeleted($debt);
        $this->assertDatabaseHas('debt_payments', ['id' => $payment->id]);
        $this->assertDatabaseHas('incomes', ['id' => $income->id]);
    }

    public function test_force_delete_removes_payment_and_linked_transaction(): void
    {
        $debt = Debt::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'type' => 'owed',
            'total_amount' => 5000,
            'paid_amount' => 0,
            'status' => 'active',
            'count_at_incurrence' => false,
        ]);

        $payment = $debt->payments()->create([
            'workspace_id' => $this->workspace->id,
            'amount' => 1000,
            'payment_date' => '2026-06-15',
        ]);

        $income = $payment->income;
        $this->assertNotNull($income);

        $debt->delete();

        $this->actingAs($this->user)
            ->delete(route('debt.force-delete', $debt->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('debt_payments', ['id' => $payment->id]);
        $this->assertSoftDeleted($income);
    }
}
