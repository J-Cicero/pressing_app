<?php

namespace Tests\Feature;

use App\Mail\TicketDeposeMail;
use App\Mail\TicketEcheanceRetraitMail;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Laveur;
use App\Models\Pressing;
use App\Models\Service;
use App\Models\User;
use App\Models\VisiteTechnique;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PressingLavageModularSeparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pressing_caissier_cannot_submit_deposit_without_mandatory_withdrawal_date(): void
    {
        $pressing = Pressing::factory()->create(['type_activite' => 'pressing']);
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);
        $service = Service::factory()->create(['pressing_id' => $pressing->id]);

        $response = $this->actingAs($caissier)->post(route('caisse.depot.store'), [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '90112233',
            'client_email' => 'jean@dupont.tg',
            'date_retrait_prevue' => '', // Empty: should fail validation
            'lignes' => [
                ['service_id' => $service->id, 'quantite' => 1],
            ],
        ]);

        $response->assertSessionHasErrors(['date_retrait_prevue']);
        $this->assertEquals(0, Facture::count());
    }

    public function test_pressing_deposit_enforces_null_laveur_and_prioritizes_email_lookup(): void
    {
        Mail::fake();

        $pressing = Pressing::factory()->create(['type_activite' => 'pressing']);
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);
        $service = Service::factory()->create(['pressing_id' => $pressing->id]);

        // Existing client with email
        $client = Client::create([
            'nom' => 'Ancien Nom',
            'telephone' => '90112233',
            'email' => 'client.fidele@example.com',
        ]);

        $response = $this->actingAs($caissier)->post(route('caisse.depot.store'), [
            'client_nom' => 'Nouveau Nom Client',
            'client_telephone' => '99887766',
            'client_email' => 'client.fidele@example.com',
            'date_retrait_prevue' => now()->addDays(2)->toDateString(),
            'lignes' => [
                ['service_id' => $service->id, 'quantite' => 2],
            ],
        ]);

        $facture = Facture::first();
        $this->assertNotNull($facture);
        $response->assertRedirect(route('caisse.factures.print', $facture));
        // Client ID must match the existing client found by email
        $this->assertEquals($client->id, $facture->client_id);
        // Pressing tickets must never have a laveur assigned
        $this->assertNull($facture->laveur_id);
        $this->assertNull($facture->vehicule_id);

        Mail::assertSent(TicketDeposeMail::class, function ($mail) use ($facture) {
            return $mail->facture->id === $facture->id;
        });
    }

    public function test_automated_command_notifies_clients_on_scheduled_withdrawal_date(): void
    {
        Mail::fake();

        $pressing = Pressing::factory()->create(['type_activite' => 'pressing']);
        $caissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        $factureDueToday = Facture::create([
            'pressing_id' => $pressing->id,
            'user_id' => $caissier->id,
            'num_ticket' => 'TCK-DUE-01',
            'client_nom' => 'Client Retrait Aujourdhui',
            'client_telephone' => '90123456',
            'client_email' => 'retrait.today@example.com',
            'montant_total' => 5000,
            'statut' => 'depose',
            'date_retrait_prevue' => now()->toDateString(),
        ]);

        $factureDueTomorrow = Facture::create([
            'pressing_id' => $pressing->id,
            'user_id' => $caissier->id,
            'num_ticket' => 'TCK-DUE-02',
            'client_nom' => 'Client Retrait Demain',
            'client_telephone' => '90998877',
            'client_email' => 'retrait.tomorrow@example.com',
            'montant_total' => 3000,
            'statut' => 'depose',
            'date_retrait_prevue' => now()->addDay()->toDateString(),
        ]);

        Artisan::call('pressing:notifier-echeances-retrait');

        Mail::assertSent(TicketEcheanceRetraitMail::class, function ($mail) use ($factureDueToday) {
            return $mail->facture->id === $factureDueToday->id;
        });

        Mail::assertNotSent(TicketEcheanceRetraitMail::class, function ($mail) use ($factureDueTomorrow) {
            return $mail->facture->id === $factureDueTomorrow->id;
        });
    }

    public function test_lavage_station_creates_visite_technique_and_calculates_thirty_percent_commission(): void
    {
        $station = Pressing::factory()->create(['type_activite' => 'lavage']);
        $caissier = User::factory()->caissier()->create(['pressing_id' => $station->id]);
        $service = Service::factory()->create([
            'pressing_id' => $station->id,
            'prix_unitaire' => 10000,
        ]);

        $laveur = Laveur::create([
            'pressing_id' => $station->id,
            'nom' => 'Mawuli Laveur',
            'telephone' => '90554433',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30,
            'actif' => true,
        ]);

        $response = $this->actingAs($caissier)->post(route('caisse.depot.store'), [
            'client_nom' => 'Proprietaire Moto',
            'client_telephone' => '91112233',
            'client_email' => 'moto@example.com',
            'immatriculation' => 'TG-999-AB',
            'marque_modele' => 'Yamaha 125',
            'type_vehicule' => 'Moto 2 Roues',
            'laveur_id' => $laveur->id,
            'date_visite_technique' => now()->addMonths(6)->toDateString(),
            'lignes' => [
                ['service_id' => $service->id, 'quantite' => 1],
            ],
        ]);

        $this->assertEquals(1, Facture::count());
        $facture = Facture::first();
        $response->assertRedirect(route('caisse.factures.print', $facture));

        // 30% of 10000 FCFA must strictly equal 3000 FCFA
        $this->assertEquals(3000, $laveur->calculerCommission($facture));

        // VisiteTechnique must be created
        $this->assertEquals(1, VisiteTechnique::count());
        $visite = VisiteTechnique::first();
        $this->assertEquals('TG-999-AB', $visite->vehicule->immatriculation);
        $this->assertEquals(now()->addMonths(6)->toDateString(), $visite->date_echeance->toDateString());
    }

    public function test_retrait_view_specialization_and_sidebar_washers_isolation(): void
    {
        $pressing = Pressing::factory()->create(['type_activite' => 'pressing']);
        $pressingCaissier = User::factory()->caissier()->create(['pressing_id' => $pressing->id]);

        $station = Pressing::factory()->create(['type_activite' => 'lavage']);
        $stationCaissier = User::factory()->caissier()->create(['pressing_id' => $station->id]);

        // Pressing caissier gets pressing retrait view and does NOT see laveurs in sidebar
        $respPressing = $this->actingAs($pressingCaissier)->get(route('caisse.retrait'));
        $respPressing->assertOk();
        $respPressing->assertViewIs('caisse.pressing.retrait');
        $respPressing->assertDontSee(route('caisse.laveurs.index'));

        // Lavage caissier gets lavage retrait view and DOES see laveurs in sidebar
        $respStation = $this->actingAs($stationCaissier)->get(route('caisse.retrait'));
        $respStation->assertOk();
        $respStation->assertViewIs('caisse.lavage.retrait');
        $respStation->assertSee(route('caisse.laveurs.index'));
    }
}
