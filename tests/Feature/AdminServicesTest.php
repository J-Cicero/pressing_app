<?php

namespace Tests\Feature;

use App\Models\Pressing;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminServicesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_list_services(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create();
        $service = Service::factory()->create([
            'pressing_id' => $pressing->id,
            'designation' => 'Nettoyage Blouson Cuir',
            'prix_unitaire' => 4500.00,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.services.index'));

        $response->assertStatus(200);
        $response->assertSee('Nettoyage Blouson Cuir');
    }

    public function test_admin_can_create_a_service(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.services.store'), [
            'pressing_id' => $pressing->id,
            'designation' => 'Lavage Rideau',
            'prix_unitaire' => 3000.00,
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseHas('services', [
            'pressing_id' => $pressing->id,
            'designation' => 'Lavage Rideau',
            'prix_unitaire' => 3000.00,
        ]);
    }

    public function test_admin_can_update_a_service(): void
    {
        $admin = User::factory()->admin()->create();
        $service = Service::factory()->create([
            'designation' => 'Ancien Service',
            'prix_unitaire' => 1500.00,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.services.update', $service), [
            'pressing_id' => $service->pressing_id,
            'designation' => 'Service Mis à Jour',
            'prix_unitaire' => 2000.00,
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'designation' => 'Service Mis à Jour',
            'prix_unitaire' => 2000.00,
        ]);
    }

    public function test_admin_can_delete_a_service_without_ligne_factures(): void
    {
        $admin = User::factory()->admin()->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.services.destroy', $service));

        $response->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    }
}
