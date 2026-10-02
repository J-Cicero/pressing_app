<?php

namespace Tests\Feature;

use App\Models\Pressing;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('PRESSING');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_caissier_cannot_access_admin_routes(): void
    {
        $caissier = User::factory()->caissier()->create();

        $response = $this->actingAs($caissier)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_caissier_can_access_caisse_routes(): void
    {
        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        $response = $this->actingAs($caissier)->get('/caisse/dashboard');

        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_caisse_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/caisse/dashboard');

        $response->assertStatus(403);
    }
}
