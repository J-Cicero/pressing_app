<?php

namespace Tests\Feature;

use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Pressing;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminFacturesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_list_factures_with_eager_loading(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create(['nom' => 'Pressing Alpha']);
        $facture = Facture::factory()->create([
            'pressing_id' => $pressing->id,
            'user_id' => $admin->id,
            'num_ticket' => 'TK-2026-9999',
            'montant_total' => 7500.00,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.factures.index'));

        $response->assertStatus(200);
        $response->assertSee('TK-2026-9999');
        $response->assertSee('Pressing Alpha');
    }

    public function test_admin_can_filter_factures_by_pressing(): void
    {
        $admin = User::factory()->admin()->create();
        $p1 = Pressing::factory()->create(['nom' => 'Pressing Nord']);
        $p2 = Pressing::factory()->create(['nom' => 'Pressing Sud']);

        Facture::factory()->create([
            'pressing_id' => $p1->id,
            'user_id' => $admin->id,
            'num_ticket' => 'TK-2026-1111',
        ]);

        Facture::factory()->create([
            'pressing_id' => $p2->id,
            'user_id' => $admin->id,
            'num_ticket' => 'TK-2026-2222',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.factures.index', ['pressing_id' => $p1->id]));

        $response->assertStatus(200);
        $response->assertSee('TK-2026-1111');
        $response->assertDontSee('TK-2026-2222');
    }

    public function test_admin_can_filter_factures_by_statut(): void
    {
        $admin = User::factory()->admin()->create();

        Facture::factory()->create([
            'user_id' => $admin->id,
            'num_ticket' => 'TK-2026-3333',
            'statut' => 'depose',
        ]);

        Facture::factory()->payeRetire()->create([
            'user_id' => $admin->id,
            'num_ticket' => 'TK-2026-4444',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.factures.index', ['statut' => 'paye_retire']));

        $response->assertStatus(200);
        $response->assertSee('TK-2026-4444');
        $response->assertDontSee('TK-2026-3333');
    }

    public function test_admin_can_search_facture_by_num_ticket(): void
    {
        $admin = User::factory()->admin()->create();

        Facture::factory()->create([
            'user_id' => $admin->id,
            'num_ticket' => 'TK-2026-SPECIAL',
        ]);

        Facture::factory()->create([
            'user_id' => $admin->id,
            'num_ticket' => 'TK-2026-OTHER',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.factures.index', ['q' => 'SPECIAL']));

        $response->assertStatus(200);
        $response->assertSee('TK-2026-SPECIAL');
        $response->assertDontSee('TK-2026-OTHER');
    }

    public function test_admin_can_view_facture_details_via_route_key_binding(): void
    {
        $admin = User::factory()->admin()->create();
        $pressing = Pressing::factory()->create();
        $service = Service::factory()->create([
            'pressing_id' => $pressing->id,
            'designation' => 'Costume Soie',
        ]);

        $facture = Facture::factory()->create([
            'num_ticket' => 'TK-2026-ROUTEKEY',
            'pressing_id' => $pressing->id,
            'user_id' => $admin->id,
            'montant_total' => 6000.00,
        ]);

        LigneFacture::factory()->create([
            'facture_id' => $facture->id,
            'service_id' => $service->id,
            'quantite' => 2,
            'prix_applique' => 3000.00,
        ]);

        // Access via Route Key Binding URL: /admin/factures/TK-2026-ROUTEKEY (no numeric ID in URL)
        $response = $this->actingAs($admin)->get('/admin/factures/'.$facture->num_ticket);

        $response->assertStatus(200);
        $response->assertSee('TK-2026-ROUTEKEY');
        $response->assertSee('Costume Soie');
        $response->assertSee('6 000,00 FCFA');
    }
}
