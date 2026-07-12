<?php

namespace Tests\Feature\Security;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceIsolationChildTablesTest extends TestCase
{
    use RefreshDatabase;

    public function test_debt_payment_has_workspace_id(): void
    {
        $ws1 = Workspace::factory()->create();
        $user = User::factory()->create(['current_workspace_id' => $ws1->id]);

        $debt = Debt::factory()->create(['workspace_id' => $ws1->id, 'user_id' => $user->id]);
        $payment = DebtPayment::create([
            'debt_id' => $debt->id,
            'workspace_id' => $ws1->id,
            'amount' => 1000,
            'payment_date' => now(),
        ]);

        $this->assertEquals($ws1->id, $payment->workspace_id);
    }
}
