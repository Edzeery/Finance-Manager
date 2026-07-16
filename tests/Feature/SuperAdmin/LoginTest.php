<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private string $genericError;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EnterpriseRolePermissionSeeder::class);

        $this->superAdmin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->superAdmin->roles()->attach(Role::where('slug', 'super_admin')->first());

        $this->genericError = __('auth.failed');
    }

    public function test_super_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/super-admin/login');

        $response
            ->assertOk()
            ->assertSee(__('super-admin.login_title'));
    }

    public function test_super_admin_can_authenticate(): void
    {
        $component = Volt::test('pages.super-admin.login')
            ->set('form.email', 'admin@example.com')
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('super.admin.dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_normal_user_gets_generic_error_not_enumeration(): void
    {
        $normalUser = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $component = Volt::test('pages.super-admin.login')
            ->set('form.email', 'user@example.com')
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasErrors(['form.email'])
            ->assertNoRedirect();

        $this->assertGuest();

        $errorMessage = $component->errors()->first('form.email');
        $this->assertEquals($this->genericError, $errorMessage);
    }

    public function test_nonexistent_email_gets_same_error(): void
    {
        $component = Volt::test('pages.super-admin.login')
            ->set('form.email', 'nonexistent@example.com')
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasErrors(['form.email'])
            ->assertNoRedirect();

        $this->assertGuest();

        $errorMessage = $component->errors()->first('form.email');
        $this->assertEquals($this->genericError, $errorMessage);
    }

    public function test_wrong_password_for_super_admin_gets_same_generic_error(): void
    {
        $component = Volt::test('pages.super-admin.login')
            ->set('form.email', 'admin@example.com')
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors(['form.email'])
            ->assertNoRedirect();

        $this->assertGuest();

        $errorMessage = $component->errors()->first('form.email');
        $this->assertEquals($this->genericError, $errorMessage);
    }

    public function test_super_admin_login_rate_limited_after_3_attempts(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $component = Volt::test('pages.super-admin.login')
                ->set('form.email', 'admin@example.com')
                ->set('form.password', 'wrong-password');

            $component->call('login');
        }

        $component = Volt::test('pages.super-admin.login')
            ->set('form.email', 'admin@example.com')
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasErrors(['form.email'])
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_normal_user_login_has_separate_rate_limiter(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $component = Volt::test('pages.super-admin.login')
                ->set('form.email', 'nonexistent@example.com')
                ->set('form.password', 'wrong');

            $component->call('login');
        }

        $this->assertGuest();
    }

    public function test_authenticated_super_admin_redirected_away_from_login(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/super-admin/login');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_authenticated_normal_user_redirected_away_from_login(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/super-admin/login');

        $response->assertRedirect(route('dashboard'));
    }
}
