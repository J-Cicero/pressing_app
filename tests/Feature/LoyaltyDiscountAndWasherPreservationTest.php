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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyDiscountAndWasherPreservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_caisse_can_create_washer_for_its_station(): void
    {
        $station = Pressing::factory()->create([
            'nom' => 'Station Test Lavage',
            'type_activite' => 'lavage',
        ]);

        $caissier = User::factory()->create([
            'role' => 'caissier',
            'pressing_id' => $station->id,
        ]);

        $response = $this->actingAs($caissier)->post(route('caisse.laveurs.store'), [
            'nom' => 'Komi Mawuli',
            'telephone' => '90112233',
        ]);

        $response->assertRedirect(route('caisse.laveurs.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('laveurs', [
            'pressing_id' => $station->id,
            'nom' => 'Komi Mawuli',
            'telephone' => '90112233',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30.00,
            'actif' => true,
        ]);
    }

    public function test_caisse_laveurs_index_displays_daily_and_monthly_data_in_read_only_mode(): void
    {
        $station = Pressing::factory()->create([
            'nom' => 'Station Bè',
            'type_activite' => 'lavage',
        ]);

        $caissier = User::factory()->create([
            'role' => 'caissier',
            'pressing_id' => $station->id,
        ]);

        $laveur = Laveur::create([
            'pressing_id' => $station->id,
            'nom' => 'Laveur Jean',
            'telephone' => '91000000',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30.00,
            'actif' => true,
        ]);

        $response = $this->actingAs($caissier)->get(route('caisse.laveurs.index'));

        $response->assertOk();
        $response->assertSee('Laveur Jean');
        $response->assertSee('Décompte Mensuel des Rémunérations');
        $response->assertSee('Consultation Seule');
        // Verify caissier has no payment settlement action buttons
        $response->assertDontSee('laveurs.paiement');
    }

    public function test_deposit_with_loyalty_discount_reduces_client_payment_but_preserves_washer_30_percent_commission(): void
    {
        $station = Pressing::factory()->create([
            'nom' => 'Station Lavage Premium',
            'type_activite' => 'lavage',
        ]);

        $caissier = User::factory()->create([
            'role' => 'caissier',
            'pressing_id' => $station->id,
        ]);

        $service = Service::create([
            'pressing_id' => $station->id,
            'designation' => 'Lavage SUV Intérieur & Extérieur',
            'prix_unitaire' => 5000,
        ]);

        $laveur = Laveur::create([
            'pressing_id' => $station->id,
            'nom' => 'Laveur Paul',
            'telephone' => '92000000',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30.00,
            'actif' => true,
        ]);

        $client = Client::create([
            'nom' => 'Client VIP',
            'telephone' => '90123456',
            'email' => 'vip@example.com',
        ]);

        $regle = RegleFidelite::create([
            'seuil_visites' => 5,
            'type_activite' => 'lavage',
            'type_avantage' => 'pourcentage',
            'valeur_avantage' => 10, // -10%
            'nombre_utilisations' => 4, // 4 fois
            'duree_jours' => 30, // 1 mois
            'description' => '10% de réduction fidélité sur 4 lavages',
            'actif' => true,
        ]);

        $avantage = ClientFideliteAvantage::create([
            'client_id' => $client->id,
            'regle_fidelite_id' => $regle->id,
            'statut' => 'disponible',
            'utilisations_restantes' => 4,
            'date_expiration' => now()->addDays(30),
            'debloque_at' => now(),
        ]);

        $postData = [
            'client_nom' => $client->nom,
            'client_telephone' => $client->telephone,
            'client_email' => $client->email,
            'laveur_id' => $laveur->id,
            'immatriculation' => 'TG-1234-AB',
            'lignes' => [
                [
                    'service_id' => $service->id,
                    'quantite' => 1,
                ],
            ],
            'client_fidelite_avantage_id' => $avantage->id,
        ];

        $response = $this->actingAs($caissier)->post(route('caisse.depot.store'), $postData);
        $response->assertRedirect();

        $facture = Facture::where('client_id', $client->id)->first();
        $this->assertNotNull($facture);

        // 1. Client pays 10% less (5000 - 500 = 4500 FCFA)
        $this->assertEquals(5000.00, (float) $facture->montant_brut);
        $this->assertEquals(500.00, (float) $facture->remise_fidelite);
        $this->assertEquals(4500.00, (float) $facture->montant_total);

        // 2. CRITICAL: Washer commission is strictly 30% of 5000 FCFA = 1500 FCFA! (NOT 30% of 4500 FCFA)
        $commissionLaveur = $laveur->calculerCommission($facture);
        $this->assertEquals(1500.00, $commissionLaveur);

        // 3. Advantage remaining uses decremented from 4 to 3
        $avantage->refresh();
        $this->assertEquals(3, $avantage->utilisations_restantes);
        $this->assertEquals('disponible', $avantage->statut);
    }

    public function test_deposit_with_free_wash_offers_zero_cost_to_client_while_washer_is_paid_full_commission(): void
    {
        $station = Pressing::factory()->create([
            'nom' => 'Station Lavage Centrale',
            'type_activite' => 'lavage',
        ]);

        $caissier = User::factory()->create([
            'role' => 'caissier',
            'pressing_id' => $station->id,
        ]);

        $service = Service::create([
            'pressing_id' => $station->id,
            'designation' => 'Lavage Moto Complet VIP',
            'prix_unitaire' => 1500,
        ]);

        $laveur = Laveur::create([
            'pressing_id' => $station->id,
            'nom' => 'Laveur Anani',
            'telephone' => '93000000',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30.00,
            'actif' => true,
        ]);

        $client = Client::create([
            'nom' => 'Client Fidèle Moto',
            'telephone' => '90998877',
        ]);

        $regle = RegleFidelite::create([
            'seuil_visites' => 10,
            'type_activite' => 'lavage',
            'type_avantage' => 'service_offert', // 100% offert
            'valeur_avantage' => 0,
            'nombre_utilisations' => 1,
            'description' => 'Un lavage moto complet offert',
            'actif' => true,
        ]);

        $avantage = ClientFideliteAvantage::create([
            'client_id' => $client->id,
            'regle_fidelite_id' => $regle->id,
            'statut' => 'disponible',
            'utilisations_restantes' => 1,
            'debloque_at' => now(),
        ]);

        $postData = [
            'client_nom' => $client->nom,
            'client_telephone' => $client->telephone,
            'laveur_id' => $laveur->id,
            'lignes' => [
                [
                    'service_id' => $service->id,
                    'quantite' => 1,
                ],
            ],
            'client_fidelite_avantage_id' => $avantage->id,
        ];

        $response = $this->actingAs($caissier)->post(route('caisse.depot.store'), $postData);
        $response->assertRedirect();

        $facture = Facture::where('client_id', $client->id)->first();
        $this->assertNotNull($facture);

        // Client pays 0 FCFA
        $this->assertEquals(1500.00, (float) $facture->montant_brut);
        $this->assertEquals(1500.00, (float) $facture->remise_fidelite);
        $this->assertEquals(0.00, (float) $facture->montant_total);

        // Washer gets 30% of 1500 FCFA = 450 FCFA
        $commissionLaveur = $laveur->calculerCommission($facture);
        $this->assertEquals(450.00, $commissionLaveur);

        // Advantage is now fully used
        $avantage->refresh();
        $this->assertEquals(0, $avantage->utilisations_restantes);
        $this->assertEquals('utilise', $avantage->statut);
    }
}
