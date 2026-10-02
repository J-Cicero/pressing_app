<?php

namespace Tests\Feature;

use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Pressing;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_pressing_has_users_services_and_factures(): void
    {
        $pressing = Pressing::factory()->create();

        $user = User::factory()->create(['pressing_id' => $pressing->id]);
        $service = Service::factory()->create(['pressing_id' => $pressing->id]);
        $facture = Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($pressing->users->contains($user));
        $this->assertTrue($pressing->services->contains($service));
        $this->assertTrue($pressing->factures->contains($facture));
    }

    public function test_user_belongs_to_pressing_and_has_factures(): void
    {
        $pressing = Pressing::factory()->create();
        $user = User::factory()->create(['pressing_id' => $pressing->id]);
        $facture = Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals($pressing->id, $user->pressing->id);
        $this->assertTrue($user->factures->contains($facture));
    }

    public function test_service_belongs_to_pressing_and_has_ligne_factures(): void
    {
        $pressing = Pressing::factory()->create();
        $user = User::factory()->create(['pressing_id' => $pressing->id]);
        $service = Service::factory()->create(['pressing_id' => $pressing->id]);
        $facture = Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $user->id,
        ]);

        $ligneFacture = LigneFacture::factory()->create([
            'facture_id' => $facture->id,
            'service_id' => $service->id,
        ]);

        $this->assertEquals($pressing->id, $service->pressing->id);
        $this->assertTrue($service->ligneFactures->contains($ligneFacture));
    }

    public function test_facture_belongs_to_pressing_user_and_has_ligne_factures(): void
    {
        $pressing = Pressing::factory()->create();
        $user = User::factory()->create(['pressing_id' => $pressing->id]);
        $service = Service::factory()->create(['pressing_id' => $pressing->id]);
        $facture = Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $user->id,
            'num_ticket' => 'TK-TEST-RELATION',
        ]);

        $ligneFacture = LigneFacture::factory()->create([
            'facture_id' => $facture->id,
            'service_id' => $service->id,
        ]);

        $this->assertEquals($pressing->id, $facture->pressing->id);
        $this->assertEquals($user->id, $facture->user->id);
        $this->assertTrue($facture->ligneFactures->contains($ligneFacture));
        $this->assertEquals('num_ticket', $facture->getRouteKeyName());
    }

    public function test_ligne_facture_belongs_to_facture_and_service(): void
    {
        $pressing = Pressing::factory()->create();
        $user = User::factory()->create(['pressing_id' => $pressing->id]);
        $service = Service::factory()->create(['pressing_id' => $pressing->id]);
        $facture = Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $user->id,
        ]);

        $ligneFacture = LigneFacture::factory()->create([
            'facture_id' => $facture->id,
            'service_id' => $service->id,
            'quantite' => 3,
            'prix_applique' => 1500.00,
        ]);

        $this->assertEquals($facture->id, $ligneFacture->facture->id);
        $this->assertEquals($service->id, $ligneFacture->service->id);
    }
}
