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
            ['nom' => 'Pressing Central'],
            [
                'ville' => 'Cotonou',
                'quartier' => 'Haie Vive',
                'telephone' => '+229 01 23 45 67',
            ]
        );

        $pressing2 = Pressing::firstOrCreate(
            ['nom' => 'Pressing Littoral'],
            [
                'ville' => 'Porto-Novo',
                'quartier' => 'Avakpa',
                'telephone' => '+229 01 98 76 54',
            ]
        );

        // 2. Personnel
        $admin = User::firstOrCreate(
            ['email' => 'admin@pressing.local'],
            [
                'pressing_id' => $pressing1->id,
                'name' => 'Administrateur Principal',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $caissier1 = User::firstOrCreate(
            ['email' => 'caissier@pressing.local'],
            [
                'pressing_id' => $pressing1->id,
                'name' => 'Caissier Cotonou',
                'password' => Hash::make('password'),
                'role' => 'caissier',
            ]
        );

        $caissier2 = User::firstOrCreate(
            ['email' => 'caissier2@pressing.local'],
            [
                'pressing_id' => $pressing2->id,
                'name' => 'Caissier Porto-Novo',
                'password' => Hash::make('password'),
                'role' => 'caissier',
            ]
        );

        // 3. Catalogue Prestations
        $servicesData = [
            ['designation' => 'Nettoyage Costume', 'prix_unitaire' => 2500.00],
            ['designation' => 'Lavage Chemise', 'prix_unitaire' => 1000.00],
            ['designation' => 'Repassage Pantalon', 'prix_unitaire' => 1000.00],
            ['designation' => 'Nettoyage Robe de Soirée', 'prix_unitaire' => 3500.00],
            ['designation' => 'Lavage Couette 2 Places', 'prix_unitaire' => 5000.00],
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

            // Facture 1 : Payée et retirée aujourd'hui (Agence 1)
            $f1 = Facture::create([
                'num_ticket' => 'TK-'.date('Y').'-0001',
                'pressing_id' => $pressing1->id,
                'user_id' => $caissier1->id,
                'montant_total' => 6000.00,
                'statut' => 'paye_retire',
                'paye_at' => $now->copy()->subHours(2),
                'created_at' => $now->copy()->subDays(2),
            ]);
            LigneFacture::create(['facture_id' => $f1->id, 'service_id' => $servicesP1[0]->id, 'quantite' => 2, 'prix_applique' => 2500.00]);
            LigneFacture::create(['facture_id' => $f1->id, 'service_id' => $servicesP1[1]->id, 'quantite' => 1, 'prix_applique' => 1000.00]);

            // Facture 2 : Payée et retirée ce mois (Agence 1)
            $f2 = Facture::create([
                'num_ticket' => 'TK-'.date('Y').'-0002',
                'pressing_id' => $pressing1->id,
                'user_id' => $caissier1->id,
                'montant_total' => 8500.00,
                'statut' => 'paye_retire',
                'paye_at' => $now->copy()->subDays(3),
                'created_at' => $now->copy()->subDays(5),
            ]);
            LigneFacture::create(['facture_id' => $f2->id, 'service_id' => $servicesP1[3]->id, 'quantite' => 1, 'prix_applique' => 3500.00]);
            LigneFacture::create(['facture_id' => $f2->id, 'service_id' => $servicesP1[4]->id, 'quantite' => 1, 'prix_applique' => 5000.00]);

            // Facture 3 : Prêt non retiré / impayé (Agence 1)
            $f3 = Facture::create([
                'num_ticket' => 'TK-'.date('Y').'-0003',
                'pressing_id' => $pressing1->id,
                'user_id' => $caissier1->id,
                'montant_total' => 3000.00,
                'statut' => 'pret',
                'paye_at' => null,
                'created_at' => $now->copy()->subDays(1),
            ]);
            LigneFacture::create(['facture_id' => $f3->id, 'service_id' => $servicesP1[1]->id, 'quantite' => 3, 'prix_applique' => 1000.00]);

            // Facture 4 : Déposé en cours / impayé (Agence 2)
            $f4 = Facture::create([
                'num_ticket' => 'TK-'.date('Y').'-0004',
                'pressing_id' => $pressing2->id,
                'user_id' => $caissier2->id,
                'montant_total' => 5000.00,
                'statut' => 'depose',
                'paye_at' => null,
                'created_at' => $now->copy()->subHours(4),
            ]);
            LigneFacture::create(['facture_id' => $f4->id, 'service_id' => $servicesP2[4]->id, 'quantite' => 1, 'prix_applique' => 5000.00]);

            // Facture 5 : Payée et retirée aujourd'hui (Agence 2)
            $f5 = Facture::create([
                'num_ticket' => 'TK-'.date('Y').'-0005',
                'pressing_id' => $pressing2->id,
                'user_id' => $caissier2->id,
                'montant_total' => 4500.00,
                'statut' => 'paye_retire',
                'paye_at' => $now->copy()->subHour(),
                'created_at' => $now->copy()->subDays(3),
            ]);
            LigneFacture::create(['facture_id' => $f5->id, 'service_id' => $servicesP2[0]->id, 'quantite' => 1, 'prix_applique' => 2500.00]);
            LigneFacture::create(['facture_id' => $f5->id, 'service_id' => $servicesP2[2]->id, 'quantite' => 2, 'prix_applique' => 1000.00]);
        }
    }
}
