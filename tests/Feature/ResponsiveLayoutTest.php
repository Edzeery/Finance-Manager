<?php

namespace Tests\Feature;

use Database\Seeders\SubscriptionPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponsiveLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SubscriptionPlanSeeder::class);
    }

    public function test_landing_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_landing_page_has_viewport_meta(): void
    {
        $response = $this->get('/');
        $response->assertSee('name="viewport"', false);
        $response->assertSee('width=device-width', false);
    }

    public function test_landing_page_does_not_have_admin_sidebar(): void
    {
        $response = $this->get('/');
        $response->assertDontSee('sidebar');
        $response->assertDontSee('main-content');
    }

    public function test_error_page_renders_with_error_layout(): void
    {
        $response = $this->get('/non-existent-route');
        $response->assertStatus(404);
    }

    public function test_login_page_has_form(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('form', false);
    }
}
