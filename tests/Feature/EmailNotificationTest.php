<?php

namespace Tests\Feature;

use App\Mail\TicketDeposeMail;
use App\Mail\TicketPretMail;
use App\Models\Facture;
use App\Models\Pressing;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_sent_when_deposit_created_with_client_email(): void
    {
        Mail::fake();

        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->create(['role' => 'caissier', 'pressing_id' => $pressing->id]);
        $service = Service::factory()->create(['pressing_id' => $pressing->id]);

        $response = $this->actingAs($caissier)->post(route('caisse.depot.store'), [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0102030405',
            'client_email' => 'jean.dupont@example.com',
            'date_retrait_prevue' => now()->addDays(2)->format('Y-m-d'),
            'lignes' => [
                ['service_id' => $service->id, 'quantite' => 2],
            ],
        ]);

        $response->assertRedirect();

        Mail::assertSent(TicketDeposeMail::class, function ($mail) {
            return $mail->hasTo('jean.dupont@example.com');
        });
    }

    public function test_email_sent_when_ticket_marked_as_pret(): void
    {
        Mail::fake();

        $pressing = Pressing::factory()->create();
        $caissier = User::factory()->create(['role' => 'caissier', 'pressing_id' => $pressing->id]);
        $facture = Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'client_email' => 'client@example.com',
            'statut' => 'depose',
        ]);

        $response = $this->actingAs($caissier)->patch(route('caisse.factures.pret', $facture));

        $response->assertRedirect();
        $this->assertEquals('pret', $facture->fresh()->statut);

        Mail::assertSent(TicketPretMail::class, function ($mail) {
            return $mail->hasTo('client@example.com');
        });
    }
}
