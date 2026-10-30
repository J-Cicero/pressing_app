<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Depense;
use App\Models\Facture;
use App\Models\Laveur;
use App\Models\Pressing;
use App\Models\RegleFidelite;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ComptabiliteAndUuidRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_models_generate_uuid_and_use_uuid_in_route_urls_instead_of_numeric_id(): void
    {
        $pressing = Pressing::factory()->create(['nom' => 'Pressing Test']);
        $user = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);
        $service = Service::factory()->create(['pressing_id' => $pressing->id]);
        $client = Client::create([
            'nom' => 'Koffi Mensah',
            'telephone' => '+22890112233',
            'email' => 'koffi@example.com',
        ]);
        $laveur = Laveur::create([
            'pressing_id' => $pressing->id,
            'nom' => 'Kodjo Laveur',
            'telephone' => '+22891000000',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30,
            'actif' => true,
        ]);
        $regle = RegleFidelite::create([
            'seuil_visites' => 5,
            'type_activite' => 'global',
            'type_avantage' => 'pourcentage',
            'valeur_avantage' => 10,
            'nombre_utilisations' => 1,
            'description' => 'Remise 10%',
            'actif' => true,
        ]);

        $this->assertTrue(Str::isUuid($pressing->uuid));
        $this->assertTrue(Str::isUuid($user->uuid));
        $this->assertTrue(Str::isUuid($service->uuid));
        $this->assertTrue(Str::isUuid($client->uuid));
        $this->assertTrue(Str::isUuid($laveur->uuid));
        $this->assertTrue(Str::isUuid($regle->uuid));

        // Verify URLs contain UUIDs and NOT sequential integer IDs
        $this->assertStringContainsString($pressing->uuid, route('admin.pressings.edit', $pressing));
        $this->assertStringContainsString($user->uuid, route('admin.users.edit', $user));
        $this->assertStringContainsString($service->uuid, route('admin.services.edit', $service));
        $this->assertStringContainsString($client->uuid, route('admin.clients.show', $client));
        $this->assertStringContainsString($laveur->uuid, route('admin.laveurs.show', $laveur));
        $this->assertStringContainsString($regle->uuid, route('admin.fidelite.edit', $regle));
    }

    public function test_admin_can_manage_agency_expenses_and_view_monthly_revenues_on_admin_interface(): void
    {
        $admin = User::factory()->admin()->create();
        $agencePressing = Pressing::factory()->create([
            'nom' => 'Agence Pressing Centrale',
            'type_activite' => 'pressing',
        ]);
        $agenceLavage = Pressing::factory()->create([
            'nom' => 'Station Lavage Express',
            'type_activite' => 'lavage',
        ]);

        $moisActuel = now()->format('Y-m');

        // Create paid invoices for both agencies
        Facture::factory()->create([
            'pressing_id' => $agencePressing->id,
            'user_id' => $admin->id,
            'statut' => 'paye_retire',
            'montant_total' => 25000,
            'paye_at' => now(),
        ]);

        Facture::factory()->create([
            'pressing_id' => $agenceLavage->id,
            'user_id' => $admin->id,
            'statut' => 'paye_retire',
            'montant_total' => 40000,
            'paye_at' => now(),
        ]);

        // Store expense for Pressing via Admin interface
        $storeResponse = $this->actingAs($admin)->post(route('admin.comptabilite.depenses.store'), [
            'pressing_id' => $agencePressing->id,
            'categorie' => 'electricite',
            'libelle' => 'Facture CEET Pressing',
            'montant' => 10000,
            'date_depense' => now()->toDateString(),
            'justificatif' => 'CEET-001',
        ]);

        $storeResponse->assertRedirect(route('admin.comptabilite.index', ['mois' => $moisActuel]));

        $depense = Depense::first();
        $this->assertNotNull($depense);
        $this->assertTrue(Str::isUuid($depense->uuid));
        $this->assertEquals(10000, (float) $depense->montant);

        // View Admin Comptabilite index
        $indexResponse = $this->actingAs($admin)->get(route('admin.comptabilite.index', ['mois' => $moisActuel]));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Facture CEET Pressing');
        $indexResponse->assertSee('65 000'); // Total Revenus (25k + 40k)
        $indexResponse->assertSee('10 000'); // Total Depenses
        $indexResponse->assertSee('55 000'); // Resultat Net (65k - 10k)

        // Update expense via UUID route
        $updateResponse = $this->actingAs($admin)->put(route('admin.comptabilite.depenses.update', $depense), [
            'pressing_id' => $agenceLavage->id,
            'categorie' => 'produits_lavage',
            'libelle' => 'Achat Shampoing Auto',
            'montant' => 15000,
            'date_depense' => now()->toDateString(),
            'justificatif' => 'SHAMP-02',
        ]);

        $updateResponse->assertRedirect(route('admin.comptabilite.index', ['mois' => $moisActuel]));
        $this->assertEquals('Achat Shampoing Auto', $depense->fresh()->libelle);
        $this->assertEquals(15000, (float) $depense->fresh()->montant);

        // Delete expense via UUID route
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.comptabilite.depenses.destroy', $depense));
        $deleteResponse->assertRedirect(route('admin.comptabilite.index', ['mois' => $moisActuel]));
        $this->assertDatabaseMissing('depenses', ['id' => $depense->id]);
    }

    public function test_comptable_has_dedicated_interface_and_cannot_access_admin_users_or_caisse(): void
    {
        $comptable = User::factory()->comptable()->create();
        $agenceLavage = Pressing::factory()->create([
            'nom' => 'Lavage Moto Pro',
            'type_activite' => 'lavage',
        ]);

        // Redirects to comptable.dashboard from /dashboard
        $dashRedirect = $this->actingAs($comptable)->get(route('dashboard'));
        $dashRedirect->assertRedirect(route('comptable.dashboard'));

        // Can view dedicated comptable dashboard
        $response = $this->actingAs($comptable)->get(route('comptable.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Portail Comptable Dédié');
        $response->assertSee('Lavage Moto Pro');

        // Can record an expense on dedicated interface
        $storeResp = $this->actingAs($comptable)->post(route('comptable.depenses.store'), [
            'pressing_id' => $agenceLavage->id,
            'categorie' => 'eau',
            'libelle' => 'Facture TdE Station',
            'montant' => 8500,
            'date_depense' => now()->toDateString(),
            'justificatif' => 'TDE-99',
        ]);

        $storeResp->assertRedirect(route('comptable.dashboard', ['mois' => now()->format('Y-m')]));
        $this->assertDatabaseHas('depenses', [
            'libelle' => 'Facture TdE Station',
            'user_id' => $comptable->id,
        ]);

        // Blocked from Admin Users management and Caisse Depot
        $this->actingAs($comptable)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($comptable)->get(route('caisse.depot'))->assertStatus(403);
    }
}
