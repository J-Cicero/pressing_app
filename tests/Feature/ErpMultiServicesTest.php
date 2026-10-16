<?php

namespace Tests\Feature;

use App\Mail\TicketDeposeMail;
use App\Mail\TicketPretMail;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Laveur;
use App\Models\Pressing;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicule;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ErpMultiServicesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_lavage_station(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.pressings.store'), [
            'nom' => 'Station Clean Express',
            'ville' => 'Lomé',
            'quartier' => 'Adidogomé',
            'telephone' => '+228 90 00 11 22',
            'type_activite' => 'lavage',
        ]);

        $response->assertRedirect(route('admin.pressings.index'));
        $this->assertDatabaseHas('pressings', [
            'nom' => 'Station Clean Express',
            'type_activite' => 'lavage',
        ]);
    }

    public function test_admin_can_manage_loyalty_rules(): void
    {
        $admin = User::factory()->admin()->create();

        // Create
        $response = $this->actingAs($admin)->post(route('admin.fidelite.store'), [
            'seuil_visites' => 8,
            'type_activite' => 'lavage',
            'type_avantage' => 'pourcentage',
            'valeur_avantage' => 15,
            'description' => '15% de remise au 8ème lavage',
            'actif' => true,
        ]);

        $response->assertRedirect(route('admin.fidelite.index'));
        $this->assertDatabaseHas('regles_fidelite', [
            'seuil_visites' => 8,
            'description' => '15% de remise au 8ème lavage',
        ]);
    }

    public function test_admin_can_manage_washers(): void
    {
        $admin = User::factory()->admin()->create();
        $station = Pressing::factory()->create(['type_activite' => 'lavage']);

        $response = $this->actingAs($admin)->post(route('admin.laveurs.store'), [
            'pressing_id' => $station->id,
            'nom' => 'Komi Mawuli',
            'telephone' => '+228 91 22 33 44',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 25,
            'actif' => true,
        ]);

        $response->assertRedirect(route('admin.laveurs.index'));
        $this->assertDatabaseHas('laveurs', [
            'nom' => 'Komi Mawuli',
            'pressing_id' => $station->id,
            'valeur_remuneration' => 25,
        ]);
    }

    public function test_caisse_client_lookup_returns_client_data_vehicles_and_loyalty(): void
    {
        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        $client = Client::create([
            'nom' => 'Messan Agbodjan',
            'telephone' => '99887766',
            'email' => 'messan@test.com',
        ]);

        Vehicule::create([
            'client_id' => $client->id,
            'immatriculation' => 'TG-5544-ZZ',
            'marque_modele' => 'Toyota Yaris',
            'type_vehicule' => 'voiture',
            'date_visite_technique' => Carbon::today()->addDays(5),
        ]);

        $response = $this->actingAs($caissier)->getJson(route('caisse.clients.lookup', ['telephone' => '99887766']));

        $response->assertStatus(200);
        $response->assertJson([
            'found' => true,
            'client' => [
                'nom' => 'Messan Agbodjan',
                'telephone' => '99887766',
            ],
            'vehicules' => [
                [
                    'immatriculation' => 'TG-5544-ZZ',
                    'marque_modele' => 'Toyota Yaris',
                ],
            ],
        ]);
    }

    public function test_depot_lavage_links_client_creates_vehicle_and_assigns_washer(): void
    {
        $station = Pressing::factory()->create(['type_activite' => 'lavage']);
        $caissier = User::factory()->caissier()->create(['pressing_id' => $station->id]);

        $service = Service::factory()->create([
            'pressing_id' => $station->id,
            'designation' => 'Lavage Complet SUV',
            'prix_unitaire' => 5000,
        ]);

        $laveur = Laveur::create([
            'pressing_id' => $station->id,
            'nom' => 'Kokou Laveur',
            'telephone' => '+228 90 99 88 77',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30,
            'actif' => true,
        ]);

        $response = $this->actingAs($caissier)->post(route('caisse.depot.store'), [
            'client_nom' => 'Pauline Dossou',
            'client_telephone' => '90445566',
            'client_email' => 'pauline@test.com',
            'date_retrait_prevue' => Carbon::today()->toDateString(),
            'laveur_id' => $laveur->id,
            'immatriculation' => 'TG-7788-BA',
            'marque_modele' => 'Kia Sportage',
            'type_vehicule' => '4x4',
            'date_visite_technique' => Carbon::today()->addDays(7)->toDateString(),
            'date_assurance' => Carbon::today()->addMonths(6)->toDateString(),
            'lignes' => [
                [
                    'service_id' => $service->id,
                    'quantite' => 1,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        // Verify client creation
        $this->assertDatabaseHas('clients', [
            'telephone' => '90445566',
            'nom' => 'Pauline Dossou',
        ]);

        // Verify vehicle creation
        $this->assertDatabaseHas('vehicules', [
            'immatriculation' => 'TG-7788-BA',
            'marque_modele' => 'Kia Sportage',
        ]);

        // Verify invoice links
        $this->assertDatabaseHas('factures', [
            'pressing_id' => $station->id,
            'client_nom' => 'Pauline Dossou',
            'laveur_id' => $laveur->id,
            'montant_total' => 5000,
        ]);

        // Commission calculation verification
        $facture = Facture::where('client_nom', 'Pauline Dossou')->first();
        $this->assertNotNull($facture);
        $this->assertEquals(1500, $laveur->calculerCommission($facture)); // 30% of 5000
    }

    public function test_whatsapp_service_generates_valid_wa_me_urls(): void
    {
        $service = app(WhatsAppNotificationService::class);

        $client = new Client([
            'nom' => 'Akouvi Mensah',
            'telephone' => '90 12 34 56',
        ]);

        $pressing = new Pressing([
            'nom' => 'Pressing Elite',
        ]);

        $facture = new Facture([
            'num_ticket' => 'TCK-20261013-ABCD',
            'client_nom' => 'Akouvi Mensah',
            'client_telephone' => '90 12 34 56',
            'montant_total' => 4000,
            'date_retrait_prevue' => Carbon::parse('2026-10-15'),
            'statut' => 'depose',
        ]);
        $facture->setRelation('pressing', $pressing);
        $facture->setRelation('ligneFactures', collect());

        $urlDepot = $service->urlDepot($facture);
        $this->assertStringStartsWith('https://wa.me/22890123456', $urlDepot);
        $this->assertStringContainsString('TCK-20261013-ABCD', $urlDepot);

        $vehicule = new Vehicule([
            'immatriculation' => 'TG-2026-OK',
            'marque_modele' => 'Toyota RAV4',
            'date_visite_technique' => Carbon::today()->addDays(3),
        ]);
        $vehicule->setRelation('client', $client);

        $urlRappel = $service->urlRappelVisiteJMoins3($vehicule);
        $this->assertStringStartsWith('https://wa.me/22890123456', $urlRappel);
        $this->assertStringContainsString('TG-2026-OK', $urlRappel);
    }

    public function test_washer_monthly_payout_and_volume_calculation(): void
    {
        $station = Pressing::create([
            'nom' => 'Lavage Mensuel Test',
            'ville' => 'Lomé',
            'quartier' => 'Nyékonakpoé',
            'type_activite' => 'lavage',
        ]);

        $laveur = Laveur::create([
            'pressing_id' => $station->id,
            'nom' => 'Komi Tech',
            'telephone' => '90998877',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 25, // 25%
            'actif' => true,
        ]);

        $user = User::factory()->caissier()->create(['pressing_id' => $station->id]);

        // Create 2 invoices in October 2026
        $facture1 = Facture::create([
            'pressing_id' => $station->id,
            'user_id' => $user->id,
            'laveur_id' => $laveur->id,
            'num_ticket' => 'TCK-OCT-01',
            'client_nom' => 'Client 1',
            'montant_total' => 6000,
            'statut' => 'paye_retire',
        ]);
        $facture1->created_at = Carbon::parse('2026-10-05 10:00:00');
        $facture1->save();

        $facture2 = Facture::create([
            'pressing_id' => $station->id,
            'user_id' => $user->id,
            'laveur_id' => $laveur->id,
            'num_ticket' => 'TCK-OCT-02',
            'client_nom' => 'Client 2',
            'montant_total' => 4000,
            'statut' => 'paye_retire',
        ]);
        $facture2->created_at = Carbon::parse('2026-10-12 14:00:00');
        $facture2->save();

        // Create 1 invoice in November 2026 (different month)
        $facture3 = Facture::create([
            'pressing_id' => $station->id,
            'user_id' => $user->id,
            'laveur_id' => $laveur->id,
            'num_ticket' => 'TCK-NOV-01',
            'client_nom' => 'Client 3',
            'montant_total' => 8000,
            'statut' => 'paye_retire',
        ]);
        $facture3->created_at = Carbon::parse('2026-11-02 09:00:00');
        $facture3->save();

        // Monthly assertions for 2026-10
        $this->assertEquals(2, $laveur->nombreLavagesMois('2026-10'));
        $this->assertEquals(10000, $laveur->totalCaMois('2026-10'));
        // 25% of 10000 = 2500 FCFA
        $this->assertEquals(2500, $laveur->totalCommissionMois('2026-10'));

        // Monthly assertions for 2026-11
        $this->assertEquals(1, $laveur->nombreLavagesMois('2026-11'));
        $this->assertEquals(8000, $laveur->totalCaMois('2026-11'));
        // 25% of 8000 = 2000 FCFA
        $this->assertEquals(2000, $laveur->totalCommissionMois('2026-11'));
    }

    public function test_admin_can_filter_washer_payroll_and_services_by_agency(): void
    {
        $admin = User::factory()->admin()->create();

        $station = Pressing::create([
            'nom' => 'Station Filter Test',
            'ville' => 'Lomé',
            'quartier' => 'Bè',
            'type_activite' => 'lavage',
        ]);

        // Test Laveurs index with month and pressing filter
        $responseLaveurs = $this->actingAs($admin)->get(route('admin.laveurs.index', [
            'pressing_id' => $station->id,
            'mois' => '2026-10',
        ]));
        $responseLaveurs->assertOk();
        $responseLaveurs->assertViewHas('selectedMois', '2026-10');
        $responseLaveurs->assertViewHas('selectedPressingId', (string) $station->id);

        // Test Services index with pressing filter
        $responseServices = $this->actingAs($admin)->get(route('admin.services.index', [
            'pressing_id' => $station->id,
        ]));
        $responseServices->assertOk();
        $responseServices->assertViewHas('selectedPressingId', (string) $station->id);

        // Test Users index with pressing filter
        $responseUsers = $this->actingAs($admin)->get(route('admin.users.index', [
            'pressing_id' => $station->id,
        ]));
        $responseUsers->assertOk();
        $responseUsers->assertViewHas('selectedPressingId', (string) $station->id);
    }

    public function test_admin_can_record_physical_cash_payment_and_reset_washer_balance_to_zero(): void
    {
        $admin = User::factory()->admin()->create();

        $station = Pressing::create([
            'nom' => 'Station Settlement Test',
            'ville' => 'Lomé',
            'quartier' => 'Tokoin',
            'type_activite' => 'lavage',
        ]);

        $laveur = Laveur::create([
            'pressing_id' => $station->id,
            'nom' => 'Yao Paye',
            'telephone' => '91223344',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30, // 30% / lavage
            'actif' => true,
        ]);

        $caissier = User::factory()->caissier()->create(['pressing_id' => $station->id]);

        // Create 3 washes in October 2026 => 3 * (4 000 * 30%) = 3 600 FCFA
        for ($i = 1; $i <= 3; $i++) {
            $f = Facture::create([
                'pressing_id' => $station->id,
                'user_id' => $caissier->id,
                'laveur_id' => $laveur->id,
                'num_ticket' => 'TCK-PAY-0'.$i,
                'client_nom' => 'Client Pay '.$i,
                'montant_total' => 4000,
                'statut' => 'paye_retire',
            ]);
            $f->created_at = Carbon::parse('2026-10-10 10:00:00');
            $f->save();
        }

        // Before payment: due = 3600, reste = 3600, estPaye = false
        $this->assertEquals(3600, $laveur->totalCommissionMois('2026-10'));
        $this->assertEquals(3600, $laveur->resteAPayerMois('2026-10'));
        $this->assertFalse($laveur->estPayeMois('2026-10'));

        // Admin records physical cash payment
        $response = $this->actingAs($admin)->post(route('admin.laveurs.paiement', $laveur), [
            'mois' => '2026-10',
            'montant' => 3600,
            'notes' => 'Remis en mains propres en espèces',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('paiements_laveurs', [
            'laveur_id' => $laveur->id,
            'mois' => '2026-10',
            'montant_paye' => 3600,
            'statut' => 'paye',
            'mode_paiement' => 'especes',
        ]);

        // After payment: reste is reset to 0 FCFA!
        $laveur->refresh();
        $this->assertTrue($laveur->estPayeMois('2026-10'));
        $this->assertEquals(0, $laveur->resteAPayerMois('2026-10'));

        // Admin can cancel settlement if needed
        $cancelResponse = $this->actingAs($admin)->post(route('admin.laveurs.annuler-paiement', $laveur), [
            'mois' => '2026-10',
        ]);

        $cancelResponse->assertSessionHas('status');
        $this->assertDatabaseMissing('paiements_laveurs', [
            'laveur_id' => $laveur->id,
            'mois' => '2026-10',
        ]);

        $laveur->refresh();
        $this->assertFalse($laveur->estPayeMois('2026-10'));
        $this->assertEquals(3600, $laveur->resteAPayerMois('2026-10'));
    }

    public function test_admin_can_view_washer_show_profile_and_salary_history(): void
    {
        $admin = User::factory()->admin()->create();
        $station = Pressing::factory()->create(['type_activite' => 'lavage']);
        $laveur = Laveur::create([
            'pressing_id' => $station->id,
            'nom' => 'Koffi Anani',
            'telephone' => '+228 90 12 34 56',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30,
            'actif' => true,
        ]);

        $uniqueTel = '9'.rand(1000000, 9999999);
        $uniquePlate = 'TG-'.rand(1000, 9999).'-AX';
        $uniqueTicket = 'TCK-SHOW-'.rand(1000, 9999);

        $client = Client::create([
            'nom' => 'Ablanvi Mensah',
            'telephone' => $uniqueTel,
        ]);

        $vehicule = Vehicule::create([
            'client_id' => $client->id,
            'immatriculation' => $uniquePlate,
            'marque_modele' => 'Toyota RAV4',
            'type_vehicule' => '4x4',
        ]);

        $caissier = User::factory()->caissier()->create(['pressing_id' => $station->id]);

        $facture = Facture::create([
            'pressing_id' => $station->id,
            'user_id' => $caissier->id,
            'client_id' => $client->id,
            'vehicule_id' => $vehicule->id,
            'laveur_id' => $laveur->id,
            'num_ticket' => $uniqueTicket,
            'client_nom' => $client->nom,
            'client_telephone' => $client->telephone,
            'montant_total' => 5000,
            'statut' => 'paye_retire',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.laveurs.show', $laveur));

        $response->assertOk();
        $response->assertSee('Koffi Anani');
        $response->assertSee('+228 90 12 34 56');
        $response->assertSee('Historique Mensuel des Salaires');
        $response->assertSee('Derniers Véhicules Pris en Charge');
        $response->assertSee($uniqueTicket);
        $response->assertSee($uniquePlate);
    }

    public function test_cashier_can_store_and_update_vehicle_and_deadlines(): void
    {
        $station = Pressing::factory()->create(['type_activite' => 'lavage']);
        $caissier = User::factory()->caissier()->create(['pressing_id' => $station->id]);

        $tel = '9'.rand(1000000, 9999999);
        $plate = 'TG-'.rand(1000, 9999).'-VE';

        // 1. Store
        $response = $this->actingAs($caissier)->post(route('caisse.vehicules.store'), [
            'client_telephone' => $tel,
            'client_nom' => 'Kojo Agbeko',
            'client_email' => 'kojo@test.com',
            'immatriculation' => $plate,
            'marque_modele' => 'Toyota RAV4',
            'type_vehicule' => '4x4',
            'date_visite_technique' => '2026-11-20',
            'date_assurance' => '2026-12-15',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('vehicules', [
            'immatriculation' => $plate,
            'marque_modele' => 'Toyota RAV4',
            'type_vehicule' => '4x4',
            'date_visite_technique' => '2026-11-20',
        ]);
        $this->assertDatabaseHas('clients', [
            'telephone' => $tel,
            'nom' => 'Kojo Agbeko',
        ]);

        $vehicule = Vehicule::where('immatriculation', $plate)->firstOrFail();

        // 2. Update
        $updateResponse = $this->actingAs($caissier)->put(route('caisse.vehicules.update', $vehicule), [
            'marque_modele' => 'Toyota RAV4 Hybrid',
            'type_vehicule' => '4x4',
            'date_visite_technique' => '2027-05-10',
            'date_assurance' => '2027-06-01',
        ]);

        $updateResponse->assertSessionHas('status');
        $this->assertDatabaseHas('vehicules', [
            'id' => $vehicule->id,
            'marque_modele' => 'Toyota RAV4 Hybrid',
            'date_visite_technique' => '2027-05-10',
        ]);
    }

    public function test_admin_can_store_and_update_vehicle_and_deadlines(): void
    {
        $admin = User::factory()->admin()->create();

        $tel = '9'.rand(1000000, 9999999);
        $plate = 'TG-'.rand(1000, 9999).'-AD';

        $response = $this->actingAs($admin)->post(route('admin.vehicules.store'), [
            'client_telephone' => $tel,
            'client_nom' => 'Adjoa Mensah',
            'client_email' => 'adjoa@test.com',
            'immatriculation' => $plate,
            'marque_modele' => 'Yamaha MT-07',
            'type_vehicule' => 'moto',
            'date_visite_technique' => '2026-10-30',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('vehicules', [
            'immatriculation' => $plate,
            'type_vehicule' => 'moto',
        ]);
    }

    public function test_email_content_adapts_to_lavage_vs_pressing(): void
    {
        $station = Pressing::factory()->create(['type_activite' => 'lavage']);
        $pressing = Pressing::factory()->create(['type_activite' => 'pressing']);

        $client = Client::create([
            'nom' => 'Edoh Kodjo',
            'telephone' => '9'.rand(1000000, 9999999),
            'email' => 'edoh@test.com',
        ]);

        $vehicule = Vehicule::create([
            'client_id' => $client->id,
            'immatriculation' => 'TG-9999-LW',
            'marque_modele' => 'Honda CR-V',
            'type_vehicule' => '4x4',
        ]);

        $admin = User::factory()->admin()->create();

        // 1. Facture Lavage
        $factureLavage = Facture::create([
            'pressing_id' => $station->id,
            'user_id' => $admin->id,
            'client_id' => $client->id,
            'vehicule_id' => $vehicule->id,
            'num_ticket' => 'TCK-LAV-01',
            'client_nom' => $client->nom,
            'client_telephone' => $client->telephone,
            'client_email' => $client->email,
            'montant_total' => 4500,
            'statut' => 'depose',
        ]);

        $mailDeposeLavage = new TicketDeposeMail($factureLavage);
        $this->assertStringContainsString('Prise en charge de votre véhicule', $mailDeposeLavage->envelope()->subject);
        $renderedLavage = $mailDeposeLavage->render();
        $this->assertStringContainsString('prise en charge de votre véhicule', $renderedLavage);
        $this->assertStringContainsString('TG-9999-LW', $renderedLavage);
        $this->assertStringNotContainsString('dépôt de vos vêtements', $renderedLavage);

        $mailPretLavage = new TicketPretMail($factureLavage);
        $this->assertStringContainsString('Votre véhicule est PRÊT', $mailPretLavage->envelope()->subject);
        $renderedPretLavage = $mailPretLavage->render();
        $this->assertStringContainsString('Votre véhicule est prêt', $renderedPretLavage);
        $this->assertStringNotContainsString('Vos vêtements sont prêts', $renderedPretLavage);

        // 2. Facture Pressing
        $facturePressing = Facture::create([
            'pressing_id' => $pressing->id,
            'user_id' => $admin->id,
            'client_id' => $client->id,
            'num_ticket' => 'TCK-PRS-01',
            'client_nom' => $client->nom,
            'client_telephone' => $client->telephone,
            'client_email' => $client->email,
            'montant_total' => 3000,
            'statut' => 'depose',
        ]);

        $mailDeposePressing = new TicketDeposeMail($facturePressing);
        $this->assertStringContainsString('Confirmation de votre dépôt', $mailDeposePressing->envelope()->subject);
        $renderedPressing = $mailDeposePressing->render();
        $this->assertStringContainsString('dépôt de vos vêtements', $renderedPressing);
        $this->assertStringNotContainsString('prise en charge de votre véhicule', $renderedPressing);

        $mailPretPressing = new TicketPretMail($facturePressing);
        $this->assertStringContainsString('Vos vêtements sont PRÊTS', $mailPretPressing->envelope()->subject);
        $renderedPretPressing = $mailPretPressing->render();
        $this->assertStringContainsString('Vos vêtements sont prêts', $renderedPretPressing);
    }
}
