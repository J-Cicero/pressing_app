<?php

namespace Database\Seeders;

use App\Models\Laveur;
use App\Models\Pressing;
use App\Models\RegleFidelite;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Clean production-ready initialization seeder.
     * Contains only foundational configurations: Admin accounts, establishments,
     * realistic service catalogs, washers (30% commission), and loyalty rules.
     *
     * Zero mock clients, zero mock vehicles, and zero mock invoices.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('paiements_laveurs')->truncate();
        DB::table('visites_techniques')->truncate();
        DB::table('ligne_factures')->truncate();
        DB::table('factures')->truncate();
        DB::table('vehicules')->truncate();
        DB::table('laveurs')->truncate();
        DB::table('regles_fidelite')->truncate();
        DB::table('clients')->truncate();
        DB::table('services')->truncate();
        DB::table('users')->truncate();
        DB::table('pressings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Super Administrateur Réseau (Accès global centralisé)
        User::create([
            'name' => 'Super Administrateur',
            'email' => 'admin@pressing.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'pressing_id' => null,
        ]);

        // 2. Établissements Fondateurs du Réseau
        $pressingCentral = Pressing::create([
            'nom' => 'Pressing Central Déckon',
            'ville' => 'Lomé',
            'quartier' => 'Déckon',
            'telephone' => '+228 90 01 02 03',
            'type_activite' => 'pressing',
        ]);

        $stationLavage = Pressing::create([
            'nom' => 'Station Lavage Express Bè-Kpota',
            'ville' => 'Lomé',
            'quartier' => 'Bè-Kpota',
            'telephone' => '+228 90 04 05 06',
            'type_activite' => 'lavage',
        ]);

        // 3. Utilisateurs Guichet / Caissiers assignés à leurs pôles respectifs
        User::create([
            'name' => 'Caissier Pressing',
            'email' => 'caissier.pressing@pressing.com',
            'password' => Hash::make('password'),
            'role' => 'caissier',
            'pressing_id' => $pressingCentral->id,
        ]);

        // Alias conventionnel pour accès rapide
        User::create([
            'name' => 'Guichet Blanchisserie',
            'email' => 'caissier@pressing.com',
            'password' => Hash::make('password'),
            'role' => 'caissier',
            'pressing_id' => $pressingCentral->id,
        ]);

        User::create([
            'name' => 'Caissier Station Lavage',
            'email' => 'caissier.lavage@pressing.com',
            'password' => Hash::make('password'),
            'role' => 'caissier',
            'pressing_id' => $stationLavage->id,
        ]);

        // 4. Catalogue des Prestations Réelles — Pôle Pressing & Blanchisserie
        $prestationsPressing = [
            ['designation' => 'Nettoyage Costume 2 Pièces (Veste + Pantalon)', 'prix_unitaire' => 2500],
            ['designation' => 'Nettoyage Costume 3 Pièces (Veste + Gilet + Pantalon)', 'prix_unitaire' => 3500],
            ['designation' => 'Chemise / Chemisier sur cintre', 'prix_unitaire' => 500],
            ['designation' => 'Pantalon / Jean / Jupe', 'prix_unitaire' => 700],
            ['designation' => 'Robe Simple / Chemise de nuit', 'prix_unitaire' => 1200],
            ['designation' => 'Robe de Cérémonie / Soirée', 'prix_unitaire' => 3000],
            ['designation' => 'Draps, Couette & Couverture (Grande Taille)', 'prix_unitaire' => 3500],
            ['designation' => 'Rideaux & Voilages (au mètre)', 'prix_unitaire' => 1500],
            ['designation' => 'Veste / Manteau / Blazer', 'prix_unitaire' => 1500],
            ['designation' => 'Repassage Seul (Chemise / Pantalon)', 'prix_unitaire' => 300],
        ];

        foreach ($prestationsPressing as $item) {
            Service::create([
                'pressing_id' => $pressingCentral->id,
                'designation' => $item['designation'],
                'prix_unitaire' => $item['prix_unitaire'],
            ]);
        }

        // 5. Catalogue des Prestations Réelles — Pôle Station Lavage Auto & Moto
        $prestationsLavage = [
            ['designation' => 'Lavage Moto Express (Châssis + Carrosserie)', 'prix_unitaire' => 1000],
            ['designation' => 'Lavage Moto Complet VIP (Moteur + Pneus + Brillance)', 'prix_unitaire' => 1500],
            ['designation' => 'Lavage Tricycle / Moto Taxi', 'prix_unitaire' => 1200],
            ['designation' => 'Lavage Complet Berline / Citadine (Intérieur + Extérieur)', 'prix_unitaire' => 3000],
            ['designation' => 'Lavage Complet SUV / 4x4 / Pick-up', 'prix_unitaire' => 5000],
            ['designation' => 'Lavage Extérieur Seul (Berline / SUV)', 'prix_unitaire' => 2000],
            ['designation' => 'Nettoyage Moteur & Dégraissage Châssis', 'prix_unitaire' => 4000],
            ['designation' => 'Shampoing Sièges & Rénovation Habitacle', 'prix_unitaire' => 7000],
        ];

        foreach ($prestationsLavage as $item) {
            Service::create([
                'pressing_id' => $stationLavage->id,
                'designation' => $item['designation'],
                'prix_unitaire' => $item['prix_unitaire'],
            ]);
        }

        // 6. Équipe Laveurs de Terrain (Règle stricte des 30% de commission)
        Laveur::create([
            'pressing_id' => $stationLavage->id,
            'nom' => 'Kokou Agbeko',
            'telephone' => '+228 92 11 00 22',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30, // 30% du montant de la prestation
            'actif' => true,
        ]);

        Laveur::create([
            'pressing_id' => $stationLavage->id,
            'nom' => 'Ablam Mensah',
            'telephone' => '+228 93 33 44 55',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30,
            'actif' => true,
        ]);

        Laveur::create([
            'pressing_id' => $stationLavage->id,
            'nom' => 'Koffi Anani',
            'telephone' => '+228 90 55 66 77',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30,
            'actif' => true,
        ]);

        // 7. Règles du Programme de Fidélité Réseau
        RegleFidelite::create([
            'seuil_visites' => 5,
            'type_activite' => 'global',
            'type_avantage' => 'pourcentage',
            'valeur_avantage' => 10,
            'description' => '10% de remise sur le prochain passage',
            'actif' => true,
        ]);

        RegleFidelite::create([
            'seuil_visites' => 10,
            'type_activite' => 'lavage',
            'type_avantage' => 'service_offert',
            'valeur_avantage' => 0,
            'description' => 'Un lavage moto complet ou extérieur offert',
            'actif' => true,
        ]);

        RegleFidelite::create([
            'seuil_visites' => 15,
            'type_activite' => 'global',
            'type_avantage' => 'cadeau',
            'valeur_avantage' => 0,
            'description' => 'Cadeau privilège VIP offert au guichet',
            'actif' => true,
        ]);

        // NOTE : Les tables clients, vehicules, factures, ligne_factures,
        // visites_techniques et paiements_laveurs restent 100% vierges de
        // données mock. Le système est prêt pour les clients réels.
    }
}
