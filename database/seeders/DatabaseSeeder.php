<?php

namespace Database\Seeders;

use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Pressing;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with real records.
     */
    public function run(): void
    {
        // 1. Agences
        $pressing1 = Pressing::firstOrCreate(
            ['nom' => 'Pressing Centre-Ville'],
            [
                'ville' => 'Lomé',
                'quartier' => 'Centre-Ville',
                'telephone' => '+228 90 00 00 01',
            ]
        );

        $pressing2 = Pressing::firstOrCreate(
            ['nom' => 'Pressing GTA'],
            [
                'ville' => 'Lomé',
                'quartier' => 'GTA',
                'telephone' => '+228 90 00 00 02',
            ]
        );

        // 2. Personnel
        $admin = User::firstOrCreate(
            ['email' => 'admin@pressing.com'],
            [
                'pressing_id' => $pressing1->id,
                'name' => 'Administrateur Principal',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $caissier1 = User::firstOrCreate(
            ['email' => 'caissier1@pressing.com'],
            [
                'pressing_id' => $pressing1->id,
                'name' => 'Caissier Centre-Ville',
                'password' => Hash::make('password'),
                'role' => 'caissier',
            ]
        );

        $caissier2 = User::firstOrCreate(
            ['email' => 'caissier2@pressing.com'],
            [
                'pressing_id' => $pressing2->id,
                'name' => 'Caissier GTA',
                'password' => Hash::make('password'),
                'role' => 'caissier',
            ]
        );

        // 3. Catalogue Prestations
        $servicesData = [
            ['designation' => 'Chemise', 'prix_unitaire' => 1000.00],
            ['designation' => 'Costume 2 Pièces', 'prix_unitaire' => 3500.00],
            ['designation' => 'Robe de soirée', 'prix_unitaire' => 2500.00],
            ['designation' => 'Pantalon', 'prix_unitaire' => 1200.00],
            ['designation' => 'Draps / Couette', 'prix_unitaire' => 4000.00],
        ];

        $servicesP1 = [];
        foreach ($servicesData as $s) {
            $servicesP1[] = Service::firstOrCreate(
                ['pressing_id' => $pressing1->id, 'designation' => $s['designation']],
                ['prix_unitaire' => $s['prix_unitaire']]
            );
        }

        $servicesP2 = [];
        foreach ($servicesData as $s) {
            $servicesP2[] = Service::firstOrCreate(
                ['pressing_id' => $pressing2->id, 'designation' => $s['designation']],
                ['prix_unitaire' => $s['prix_unitaire']]
            );
        }

        // 4. Factures et Lignes réelles si aucune facture n'existe
        if (Facture::count() === 0) {
            $now = Carbon::now();

            // Facture 1 : Payée et retirée aujourd'hui (Agence 1 - Centre-Ville)
            $f1 = Facture::create([
                'num_ticket' => 'TCK-'.date('Ymd').'-0001',
                'pressing_id' => $pressing1->id,
                'user_id' => $caissier1->id,
                'client_nom' => 'Koffi Mensah',
                'client_telephone' => '+228 91 23 45 67',
                'date_retrait_prevue' => $now->toDateString(),
                'montant_total' => 8000.00,
                'statut' => 'paye_retire',
                'paye_at' => $now->copy()->subHours(2),
                'created_at' => $now->copy()->subDays(2),
            ]);
            LigneFacture::create(['facture_id' => $f1->id, 'service_id' => $servicesP1[1]->id, 'quantite' => 2, 'prix_applique' => 3500.00]); // 2 Costumes = 7000
            LigneFacture::create(['facture_id' => $f1->id, 'service_id' => $servicesP1[0]->id, 'quantite' => 1, 'prix_applique' => 1000.00]); // 1 Chemise = 1000

            // Facture 2 : Payée et retirée ce mois (Agence 1 - Centre-Ville)
            $f2 = Facture::create([
                'num_ticket' => 'TCK-'.date('Ymd').'-0002',
                'pressing_id' => $pressing1->id,
                'user_id' => $caissier1->id,
                'client_nom' => 'Afiwa Lawson',
                'client_telephone' => '+228 92 34 56 78',
                'date_retrait_prevue' => $now->copy()->subDays(2)->toDateString(),
                'montant_total' => 6500.00,
                'statut' => 'paye_retire',
                'paye_at' => $now->copy()->subDays(3),
                'created_at' => $now->copy()->subDays(5),
            ]);
            LigneFacture::create(['facture_id' => $f2->id, 'service_id' => $servicesP1[2]->id, 'quantite' => 1, 'prix_applique' => 2500.00]); // 1 Robe = 2500
            LigneFacture::create(['facture_id' => $f2->id, 'service_id' => $servicesP1[4]->id, 'quantite' => 1, 'prix_applique' => 4000.00]); // 1 Draps = 4000

            // Facture 3 : Prêt non retiré / impayé (Agence 1 - Centre-Ville)
            $f3 = Facture::create([
                'num_ticket' => 'TCK-'.date('Ymd').'-0003',
                'pressing_id' => $pressing1->id,
                'user_id' => $caissier1->id,
                'client_nom' => 'Kodjo Amegan',
                'client_telephone' => '+228 93 45 67 89',
                'date_retrait_prevue' => $now->toDateString(),
                'montant_total' => 3400.00,
                'statut' => 'pret',
                'paye_at' => null,
                'created_at' => $now->copy()->subDays(1),
            ]);
            LigneFacture::create(['facture_id' => $f3->id, 'service_id' => $servicesP1[0]->id, 'quantite' => 1, 'prix_applique' => 1000.00]); // 1 Chemise = 1000
            LigneFacture::create(['facture_id' => $f3->id, 'service_id' => $servicesP1[3]->id, 'quantite' => 2, 'prix_applique' => 1200.00]); // 2 Pantalons = 2400

            // Facture 4 : Déposé en cours / impayé (Agence 2 - GTA)
            $f4 = Facture::create([
                'num_ticket' => 'TCK-'.date('Ymd').'-0004',
                'pressing_id' => $pressing2->id,
                'user_id' => $caissier2->id,
                'client_nom' => 'Ewomazino Adanledji',
                'client_telephone' => '+228 94 56 78 90',
                'date_retrait_prevue' => $now->copy()->addDays(2)->toDateString(),
                'montant_total' => 4000.00,
                'statut' => 'depose',
                'paye_at' => null,
                'created_at' => $now->copy()->subHours(4),
            ]);
            LigneFacture::create(['facture_id' => $f4->id, 'service_id' => $servicesP2[4]->id, 'quantite' => 1, 'prix_applique' => 4000.00]); // 1 Draps = 4000

            // Facture 5 : Payée et retirée aujourd'hui (Agence 2 - GTA)
            $f5 = Facture::create([
                'num_ticket' => 'TCK-'.date('Ymd').'-0005',
                'pressing_id' => $pressing2->id,
                'user_id' => $caissier2->id,
                'client_nom' => 'Akossiwa Dossou',
                'client_telephone' => '+228 95 67 89 01',
                'date_retrait_prevue' => $now->toDateString(),
                'montant_total' => 4700.00,
                'statut' => 'paye_retire',
                'paye_at' => $now->copy()->subHour(),
                'created_at' => $now->copy()->subDays(3),
            ]);
            LigneFacture::create(['facture_id' => $f5->id, 'service_id' => $servicesP2[1]->id, 'quantite' => 1, 'prix_applique' => 3500.00]); // 1 Costume = 3500
            LigneFacture::create(['facture_id' => $f5->id, 'service_id' => $servicesP2[3]->id, 'quantite' => 1, 'prix_applique' => 1200.00]); // 1 Pantalon = 1200
        }
    }
}
