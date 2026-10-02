<?php

namespace Tests\Feature;

use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Pressing;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CaisseParcoursTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_from_caisse(): void
    {
        $response = $this->get(route('caisse.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_caissier_dashboard_directly(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('caisse.dashboard'));

        $response->assertStatus(403);
    }

    public function test_caissier_can_access_caisse_dashboard_and_sees_own_agency_metrics(): void
    {
        $pressing = Pressing::factory()->create(['nom' => 'Pressing Cotonou Étoile']);
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        // Facture 1 : déposée aujourd'hui
        Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $caissier->id,
            'statut' => 'depose',
            'created_at' => now(),
        ]);

        // Facture 2 : prête
        Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $caissier->id,
            'statut' => 'pret',
        ]);

        // Facture 3 : payée aujourd'hui
        Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $caissier->id,
            'statut' => 'paye_retire',
            'montant_total' => 7000.00,
            'paye_at' => now(),
        ]);

        $response = $this->actingAs($caissier)->get(route('caisse.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pressing Cotonou Étoile');
        $response->assertSee('7 000,00');
        $response->assertSee('Nouveau Dépôt');
        $response->assertSee('Gestion / Encaissement Ticket');
    }

    public function test_caissier_can_view_depot_form_with_own_services(): void
    {
        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        $service1 = Service::factory()->create([
            'pressing_id' => $pressing->id,
            'designation' => 'Nettoyage Veste Smoking',
            'prix_unitaire' => 3000.00,
        ]);

        $otherPressing = Pressing::factory()->create();
        $serviceOther = Service::factory()->create([
            'pressing_id' => $otherPressing->id,
            'designation' => 'Prestation Autre Agence',
        ]);

        $response = $this->actingAs($caissier)->get(route('caisse.depot'));

        $response->assertStatus(200);
        $response->assertSee('Nettoyage Veste Smoking');
        $response->assertDontSee('Prestation Autre Agence');
        $response->assertSee('Reste à Payer au Retrait');
    }

    public function test_caissier_can_create_depot_and_gets_redirected_to_thermal_print(): void
    {
        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        $serviceA = Service::factory()->create([
            'pressing_id' => $pressing->id,
            'prix_unitaire' => 2500.00,
            'designation' => 'Nettoyage Costume',
        ]);

        $serviceB = Service::factory()->create([
            'pressing_id' => $pressing->id,
            'prix_unitaire' => 1000.00,
            'designation' => 'Lavage Chemise',
        ]);

        $response = $this->actingAs($caissier)->post(route('caisse.depot.store'), [
            'client_nom' => 'Amadou Diallo',
            'client_telephone' => '+229 97 12 34 56',
            'date_retrait_prevue' => now()->addDays(2)->format('Y-m-d'),
            'lignes' => [
                ['service_id' => $serviceA->id, 'quantite' => 2], // 2 * 2500 = 5000
                ['service_id' => $serviceB->id, 'quantite' => 3], // 3 * 1000 = 3000 => Total 8000
            ],
        ]);

        $facture = Facture::where('client_nom', 'Amadou Diallo')->first();

        $this->assertNotNull($facture);
        $this->assertEquals(8000.00, (float) $facture->montant_total);
        $this->assertEquals('depose', $facture->statut);
        $this->assertNull($facture->paye_at); // Strict: 0 FCFA advance, unpaid
        $this->assertStringStartsWith('TCK-', $facture->num_ticket);
        $this->assertCount(2, $facture->ligneFactures);

        // Redirects directly to the thermal print view
        $response->assertRedirect(route('caisse.factures.print', $facture));
    }

    public function test_cannot_create_depot_with_service_from_another_agency(): void
    {
        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        $otherPressing = Pressing::factory()->create();
        $otherService = Service::factory()->create(['pressing_id' => $otherPressing->id]);

        $response = $this->actingAs($caissier)->post(route('caisse.depot.store'), [
            'client_nom' => 'Tentative Invalide',
            'client_telephone' => '97000000',
            'date_retrait_prevue' => now()->addDay()->format('Y-m-d'),
            'lignes' => [
                ['service_id' => $otherService->id, 'quantite' => 1],
            ],
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseMissing('factures', [
            'client_nom' => 'Tentative Invalide',
        ]);
    }

    public function test_caissier_can_view_thermal_print_ticket_via_route_key_binding(): void
    {
        $pressing = Pressing::factory()->create([
            'nom' => 'Pressing Rapide 80mm',
            'ville' => 'Cotonou',
            'quartier' => 'Cadjehoun',
            'telephone' => '+229 01 22 33 44',
        ]);
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);
        $service = Service::factory()->create(['pressing_id' => $pressing->id, 'designation' => 'Robe Soirée']);

        $facture = Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $caissier->id,
            'num_ticket' => 'TCK-20261002-TEST',
            'client_nom' => 'Béatrice Mensah',
            'client_telephone' => '95001122',
            'montant_total' => 3500.00,
            'statut' => 'depose',
        ]);

        LigneFacture::factory()->create([
            'facture_id' => $facture->id,
            'service_id' => $service->id,
            'quantite' => 1,
            'prix_applique' => 3500.00,
        ]);

        // URL uses route key binding: /caisse/factures/TCK-20261002-TEST/print
        $response = $this->actingAs($caissier)->get(route('caisse.factures.print', $facture));

        $response->assertStatus(200);
        $response->assertSee(strtoupper($pressing->nom));
        $response->assertSee('TCK-20261002-TEST');
        $response->assertSee('Béatrice Mensah');
        $response->assertSee('Robe Soirée');
        $response->assertSee('3 500 FCFA');
        $response->assertSee('PAIEMENT À 100% LORS DU RETRAIT DE VOS ARTICLES.');
        $response->assertSee('window.print()');
    }

    public function test_caissier_cannot_view_print_ticket_from_another_agency(): void
    {
        $pressingA = Pressing::factory()->create();
        $pressingB = Pressing::factory()->create();

        $caissierA = User::factory()->caissier()->create(['pressing_id' => $pressingA->id]);

        $factureB = Facture::factory()->create([
            'pressing_id' => $pressingB->id,
        ]);

        $response = $this->actingAs($caissierA)->get(route('caisse.factures.print', $factureB));

        $response->assertStatus(403);
    }

    public function test_caissier_can_mark_ticket_as_pret(): void
    {
        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        $facture = Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $caissier->id,
            'statut' => 'depose',
        ]);

        $response = $this->actingAs($caissier)->patch(route('caisse.factures.pret', $facture));

        $response->assertRedirect();
        $facture->refresh();
        $this->assertEquals('pret', $facture->statut);
    }

    public function test_caissier_can_encaisser_et_restituer_with_100_percent_payment_and_timestamp(): void
    {
        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        $facture = Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $caissier->id,
            'statut' => 'pret',
            'montant_total' => 6000.00,
            'paye_at' => null,
        ]);

        $response = $this->actingAs($caissier)->patch(route('caisse.factures.encaisser', $facture));

        $response->assertRedirect();
        $facture->refresh();
        $this->assertEquals('paye_retire', $facture->statut);
        $this->assertNotNull($facture->paye_at);
    }

    public function test_caissier_can_search_tickets_by_phone_and_num_ticket(): void
    {
        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'num_ticket' => 'TCK-20261002-SEARCH1',
            'client_telephone' => '97998877',
        ]);

        Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'num_ticket' => 'TCK-20261002-OTHER',
            'client_telephone' => '95000000',
        ]);

        // Search by phone
        $responsePhone = $this->actingAs($caissier)->get(route('caisse.retrait', ['q' => '97998877']));
        $responsePhone->assertStatus(200);
        $responsePhone->assertSee('TCK-20261002-SEARCH1');
        $responsePhone->assertDontSee('TCK-20261002-OTHER');

        // Search by num_ticket
        $responseTicket = $this->actingAs($caissier)->get(route('caisse.retrait', ['q' => 'SEARCH1']));
        $responseTicket->assertStatus(200);
        $responseTicket->assertSee('TCK-20261002-SEARCH1');
        $responseTicket->assertDontSee('TCK-20261002-OTHER');
    }
}
