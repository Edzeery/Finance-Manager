<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerAbilitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_normal_user_requesting_wildcard_receives_only_allowed_abilities(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
            'abilities' => ['*'],
        ]);

        $response->assertOk();
        $response->assertJsonPath('abilities', []);
    }
}
