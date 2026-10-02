<?php

namespace Tests\Feature;

use App\Models\Pressing;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Chef']);
        $caissier = User::factory()->caissier()->create(['name' => 'Caissier Roger']);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Admin Chef');
        $response->assertSee('Caissier Roger');
    }

    public function test_admin_can_create_a_caissier(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Nouvel Employé',
            'email' => 'employe@pressing.local',
            'password' => 'secret1234',
            'role' => 'caissier',
            'pressing_id' => $pressing->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Nouvel Employé',
            'email' => 'employe@pressing.local',
            'role' => 'caissier',
            'pressing_id' => $pressing->id,
        ]);
    }

    public function test_admin_can_update_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create();
        $user = User::factory()->caissier()->create([
            'name' => 'Ancien Nom',
            'pressing_id' => $pressing->id,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Nom Modifié',
            'email' => $user->email,
            'role' => 'caissier',
            'pressing_id' => $pressing->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nom Modifié',
        ]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }
}
