<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientFideliteAvantage;
use App\Models\Facture;
use App\Models\Laveur;
use App\Models\Pressing;
use App\Models\RegleFidelite;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_endpoint_is_rate_limited_after_five_failed_attempts(): void
    {
        $email = 'bruteforce.'.uniqid().'@pressing.local';

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'wrong-password',
            ]);
            $response->assertStatus(302);
        }

        $throttledResponse = $this->post('/login', [
            'email' => $email,
            'password' => 'wrong-password',
        ]);

        $throttledResponse->assertStatus(429);
    }

    public function test_caissier_cannot_assign_washer_from_another_agency_on_depot(): void
    {
        $stationA = Pressing::factory()->create(['type_activite' => 'lavage']);
        $stationB = Pressing::factory()->create(['type_activite' => 'lavage']);

        $caissierA = User::factory()->caissier()->create(['pressing_id' => $stationA->id]);
        $serviceA = Service::factory()->create(['pressing_id' => $stationA->id, 'prix_unitaire' => 3000]);

        $laveurB = Laveur::create([
            'pressing_id' => $stationB->id,
            'nom' => 'Laveur Station Concurrente',
            'telephone' => '90000099',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30,
            'actif' => true,
        ]);

        $response = $this->actingAs($caissierA)->post(route('caisse.depot.store'), [
            'client_nom' => 'Client Isolation Test',
            'client_telephone' => '91112233',
            'date_retrait_prevue' => now()->toDateString(),
            'laveur_id' => $laveurB->id,
            'immatriculation' => 'TG-1111-AA',
            'lignes' => [
                ['service_id' => $serviceA->id, 'quantite' => 1],
            ],
        ]);

        $response->assertSessionHasErrors('laveur_id');
    }

    public function test_caissier_cannot_assign_washer_from_another_agency_on_retrait(): void
    {
        $stationA = Pressing::factory()->create(['type_activite' => 'lavage']);
        $stationB = Pressing::factory()->create(['type_activite' => 'lavage']);

        $caissierA = User::factory()->caissier()->create(['pressing_id' => $stationA->id]);

        $factureA = Facture::factory()->create([
            'pressing_id' => $stationA->id,
            'user_id' => $caissierA->id,
            'statut' => 'depose',
        ]);

        $laveurB = Laveur::create([
            'pressing_id' => $stationB->id,
            'nom' => 'Laveur Externe',
            'telephone' => '90000088',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30,
            'actif' => true,
        ]);

        $response = $this->actingAs($caissierA)->patch(route('caisse.factures.assigner-laveur', $factureA), [
            'laveur_id' => $laveurB->id,
        ]);

        $response->assertSessionHasErrors('laveur_id');
        $this->assertNull($factureA->fresh()->laveur_id);
    }

    public function test_client_lookup_does_not_leak_clients_via_sql_like_wildcards(): void
    {
        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        Client::create([
            'nom' => 'Client Confidentiel',
            'telephone' => '90123456',
            'email' => 'confidentiel@example.com',
        ]);

        $responsePercent = $this->actingAs($caissier)->getJson(route('caisse.clients.lookup', ['telephone' => '%%%']));
        $responsePercent->assertOk()->assertJson(['found' => false]);

        $responseUnderscore = $this->actingAs($caissier)->getJson(route('caisse.clients.lookup', ['q' => '___']));
        $responseUnderscore->assertOk()->assertJson(['found' => false]);
    }

    public function test_admin_cannot_downgrade_own_role(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'caissier',
            'pressing_id' => $pressing->id,
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertEquals('admin', $admin->fresh()->role);
    }

    public function test_security_headers_are_attached_to_web_responses(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_loyalty_advantage_for_lavage_cannot_be_applied_on_pressing_deposit(): void
    {
        $pressing = Pressing::factory()->create(['type_activite' => 'pressing']);
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);
        $service = Service::factory()->create(['pressing_id' => $pressing->id, 'prix_unitaire' => 5000]);

        $client = Client::create([
            'nom' => 'Client Fidelite',
            'telephone' => '99887766',
        ]);

        $regleLavage = RegleFidelite::create([
            'seuil_visites' => 5,
            'type_activite' => 'lavage',
            'type_avantage' => 'pourcentage',
            'valeur_avantage' => 100,
            'nombre_utilisations' => 1,
            'description' => '1 Lavage Gratuit',
            'actif' => true,
        ]);

        $avantage = ClientFideliteAvantage::create([
            'client_id' => $client->id,
            'regle_fidelite_id' => $regleLavage->id,
            'statut' => 'disponible',
            'utilisations_restantes' => 1,
            'debloque_at' => now(),
        ]);

        $response = $this->actingAs($caissier)->post(route('caisse.depot.store'), [
            'client_id' => $client->id,
            'client_nom' => $client->nom,
            'client_telephone' => $client->telephone,
            'date_retrait_prevue' => now()->addDay()->toDateString(),
            'client_fidelite_avantage_id' => $avantage->id,
            'lignes' => [
                ['service_id' => $service->id, 'quantite' => 1],
            ],
        ]);

        $response->assertRedirect();
        $facture = Facture::latest('id')->first();
        $this->assertEquals(0.0, (float) $facture->remise_fidelite);
        $this->assertEquals(5000.0, (float) $facture->montant_total);
        $this->assertEquals('disponible', $avantage->fresh()->statut);
    }
}
