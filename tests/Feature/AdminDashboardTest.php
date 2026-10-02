<?php

namespace Tests\Feature;

use App\Models\Facture;
use App\Models\Pressing;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_caissier_cannot_access_admin_dashboard(): void
    {
        $caissier = User::factory()->caissier()->create();

        $response = $this->actingAs($caissier)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_with_real_database_aggregates(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create(['nom' => 'Agence Test Nord']);

        // Facture 1 : Payée aujourd'hui
        Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $admin->id,
            'montant_total' => 5000.00,
            'statut' => 'paye_retire',
            'paye_at' => now(),
        ]);

        // Facture 2 : Impayée (déposée)
        Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $admin->id,
            'montant_total' => 2000.00,
            'statut' => 'depose',
            'paye_at' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('ESPACE SUPER ADMIN');
        $response->assertSee('Tableau comparatif dynamique par agence');
        $response->assertSee('Agence Test Nord');
    }

    public function test_temporal_filters_return_successful_response(): void
    {
        $admin = User::factory()->admin()->create();

        $responseJour = $this->actingAs($admin)->get(route('admin.dashboard', ['periode' => 'jour']));
        $responseJour->assertStatus(200);

        $responseMois = $this->actingAs($admin)->get(route('admin.dashboard', ['periode' => 'mois']));
        $responseMois->assertStatus(200);

        $responseTout = $this->actingAs($admin)->get(route('admin.dashboard', ['periode' => 'tout']));
        $responseTout->assertStatus(200);
    }
}
