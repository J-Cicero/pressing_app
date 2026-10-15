<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Facture;
use App\Models\Pressing;
use App\Models\RegleFidelite;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClientLoyaltyEngineTest extends TestCase
{
    use DatabaseTransactions;

    public function test_loyalty_rule_calculates_visits_strictly_from_rule_creation_date(): void
    {
        $pressing = Pressing::factory()->create(['type_activite' => 'pressing']);
        $client = Client::create([
            'nom' => 'Koffi Anani',
            'telephone' => '90112233',
        ]);

        // 1. Client had 5 past visits last week
        for ($i = 0; $i < 5; $i++) {
            Facture::factory()->create([
                'pressing_id' => $pressing->id,
                'client_id' => $client->id,
                'statut' => 'paye_retire',
                'created_at' => Carbon::now()->subDays(10),
                'paye_at' => Carbon::now()->subDays(10),
            ]);
        }

        // Total all-time visits is 5
        $this->assertEquals(5, $client->countTotalVisites());

        // Deactivate pre-existing seeded rules for this test scope
        RegleFidelite::query()->update(['actif' => false]);

        // 2. Admin creates a new loyalty rule TODAY with threshold of 5 visits
        $regle = RegleFidelite::create([
            'seuil_visites' => 5,
            'type_avantage' => 'cadeau',
            'description' => 'Cadeau de bienvenue après 5 passages',
            'type_activite' => 'global',
            'actif' => true,
            'created_at' => Carbon::now()->startOfDay(),
        ]);

        // Client has 0 visits since the rule was created today
        $this->assertEquals(0, $client->countVisitesPourRegle($regle));
        $this->assertFalse($client->estEligibleRegle($regle));
        $this->assertEquals(5, $client->visitesRestantesPourRegle($regle));
        $this->assertNull($client->regleFideliteAtteinte());

        // 3. Client does 5 NEW visits AFTER rule creation
        for ($i = 0; $i < 5; $i++) {
            Facture::factory()->create([
                'pressing_id' => $pressing->id,
                'client_id' => $client->id,
                'statut' => 'paye_retire',
                'created_at' => Carbon::now(),
                'paye_at' => Carbon::now(),
            ]);
        }

        // Now client has 5 visits since rule inception
        $this->assertEquals(5, $client->countVisitesPourRegle($regle));
        $this->assertTrue($client->estEligibleRegle($regle));
        $this->assertEquals(0, $client->visitesRestantesPourRegle($regle));
        $this->assertEquals($regle->id, $client->regleFideliteAtteinte()->id);
    }

    public function test_loyalty_rule_edit_and_update_work_without_parameter_binding_error(): void
    {
        $admin = User::factory()->admin()->create();

        $regle = RegleFidelite::create([
            'seuil_visites' => 10,
            'type_avantage' => 'pourcentage',
            'valeur_avantage' => 10,
            'description' => '10% de réduction au 10ème passage',
            'type_activite' => 'pressing',
            'actif' => true,
        ]);

        // Test GET edit
        $response = $this->actingAs($admin)->get(route('admin.fidelite.edit', $regle));
        $response->assertStatus(200);
        $response->assertSee('Palier de 10 visites');

        // Test PUT update
        $updateResponse = $this->actingAs($admin)->put(route('admin.fidelite.update', $regle), [
            'seuil_visites' => 12,
            'type_avantage' => 'pourcentage',
            'valeur_avantage' => 12,
            'description' => '12% de réduction au 12ème passage',
            'type_activite' => 'pressing',
            'actif' => true,
        ]);

        $updateResponse->assertRedirect(route('admin.fidelite.index'));
        $this->assertDatabaseHas('regles_fidelite', [
            'id' => $regle->id,
            'seuil_visites' => 12,
            'description' => '12% de réduction au 12ème passage',
        ]);
    }

    public function test_service_controller_filters_by_activity_type(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create(['type_activite' => 'pressing']);
        $station = Pressing::factory()->create(['type_activite' => 'lavage']);

        $servicePressing = Service::factory()->create([
            'pressing_id' => $pressing->id,
            'designation' => 'Repassage Costume 3 Pièces',
        ]);

        $serviceLavage = Service::factory()->create([
            'pressing_id' => $station->id,
            'designation' => 'Lavage Moto Cross',
        ]);

        // Filter pressing only
        $responsePressing = $this->actingAs($admin)->get(route('admin.services.index', ['type_activite' => 'pressing']));
        $responsePressing->assertStatus(200);
        $responsePressing->assertSee($servicePressing->designation);
        $responsePressing->assertDontSee($serviceLavage->designation);

        // Filter lavage only
        $responseLavage = $this->actingAs($admin)->get(route('admin.services.index', ['type_activite' => 'lavage']));
        $responseLavage->assertStatus(200);
        $responseLavage->assertSee($serviceLavage->designation);
        $responseLavage->assertDontSee($servicePressing->designation);
    }

    public function test_admin_dashboard_segregates_pressing_and_lavage_revenue(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create(['type_activite' => 'pressing']);
        $station = Pressing::factory()->create(['type_activite' => 'lavage']);

        // Pressing invoice: 15 000 FCFA
        Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'statut' => 'paye_retire',
            'montant_total' => 15000,
            'created_at' => Carbon::now(),
            'paye_at' => Carbon::now(),
        ]);

        // Lavage invoice: 5 000 FCFA
        Facture::factory()->create([
            'pressing_id' => $station->id,
            'statut' => 'paye_retire',
            'montant_total' => 5000,
            'created_at' => Carbon::now(),
            'paye_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard', ['periode' => 'jour']));
        $response->assertStatus(200);

        // Total 20 000 FCFA
        $response->assertSee('20 000');
        // Pressing 15 000 FCFA
        $response->assertSee('15 000');
        // Lavage 5 000 FCFA
        $response->assertSee('5 000');
    }
}
