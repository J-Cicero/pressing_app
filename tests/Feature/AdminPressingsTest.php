<?php

namespace Tests\Feature;

use App\Models\Pressing;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminPressingsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_list_pressings(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create(['nom' => 'Pressing Étoile']);

        $response = $this->actingAs($admin)->get(route('admin.pressings.index'));

        $response->assertStatus(200);
        $response->assertSee('Pressing Étoile');
    }

    public function test_admin_can_create_a_pressing(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.pressings.store'), [
            'nom' => 'Pressing Marina',
            'ville' => 'Cotonou',
            'quartier' => 'Marina',
            'telephone' => '+229 01 00 11 22',
        ]);

        $response->assertRedirect(route('admin.pressings.index'));
        $this->assertDatabaseHas('pressings', [
            'nom' => 'Pressing Marina',
            'ville' => 'Cotonou',
        ]);
    }

    public function test_admin_can_update_a_pressing(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create(['nom' => 'Ancien Nom']);

        $response = $this->actingAs($admin)->put(route('admin.pressings.update', $pressing), [
            'nom' => 'Nouveau Nom Pressing',
            'ville' => $pressing->ville,
            'quartier' => $pressing->quartier,
            'telephone' => $pressing->telephone,
        ]);

        $response->assertRedirect(route('admin.pressings.index'));
        $this->assertDatabaseHas('pressings', [
            'id' => $pressing->id,
            'nom' => 'Nouveau Nom Pressing',
        ]);
    }

    public function test_admin_can_delete_a_pressing_without_factures(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.pressings.destroy', $pressing));

        $response->assertRedirect(route('admin.pressings.index'));
        $this->assertDatabaseMissing('pressings', [
            'id' => $pressing->id,
        ]);
    }
}
