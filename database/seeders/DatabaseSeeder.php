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
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Clean production-ready initialization seeder (compatible PostgreSQL, MySQL & SQLite).
     * Contains only foundational configurations: Admin & Cashier accounts, establishments,
     * realistic service catalogs, washers (30% commission), and loyalty rules with limited duration.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('depenses')) {
            DB::table('depenses')->truncate();
        }
        DB::table('client_fidelite_avantages')->truncate();
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
        Schema::enableForeignKeyConstraints();

        // 1. Super Administrateur Réseau (Accès global centralisé)
        User::create([
            'name' => 'Direction Générale',
            'email' => 'direction@pressing-suite.tg',
            'password' => Hash::make('Pressing@2026!'),
            'role' => 'admin',
            'pressing_id' => null,
        ]);

        // 1b. Responsable Comptabilité & Finances (Interface dédiée Comptable)
        User::create([
            'name' => 'Responsable Comptabilité',
            'email' => 'comptable@pressing-suite.tg',
            'password' => Hash::make('Comptable@2026!'),
            'role' => 'comptable',
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
            'name' => 'Responsable Guichet Pressing',
            'email' => 'guichet@pressing-suite.tg',
            'password' => Hash::make('Guichet@2026!'),
            'role' => 'caissier',
            'pressing_id' => $pressingCentral->id,
        ]);

        User::create([
            'name' => 'Responsable Caisse Lavage',
            'email' => 'lavage@pressing-suite.tg',
            'password' => Hash::make('Lavage@2026!'),
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

        // 6. Équipe Laveurs de Terrain (Règle stricte des 30% de commission sur montant brut)
        Laveur::create([
            'pressing_id' => $stationLavage->id,
            'nom' => 'Kokou Agbeko',
            'telephone' => '+228 92 11 00 22',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30,
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

        // 7. Règles du Programme de Fidélité Réseau (avec quotas d'utilisation et durées limitées)
        RegleFidelite::create([
            'seuil_visites' => 5,
            'type_activite' => 'global',
            'type_avantage' => 'pourcentage',
            'valeur_avantage' => 10,
            'nombre_utilisations' => 2,
            'duree_jours' => 15,
            'description' => '10% de remise pendant 15 jours (valable sur 2 passages)',
            'actif' => true,
        ]);

        RegleFidelite::create([
            'seuil_visites' => 10,
            'type_activite' => 'lavage',
            'type_avantage' => 'service_offert',
            'valeur_avantage' => 0,
            'nombre_utilisations' => 1,
            'duree_jours' => 30,
            'description' => 'Un lavage complet offert (valable pendant 30 jours)',
            'actif' => true,
        ]);

        RegleFidelite::create([
            'seuil_visites' => 15,
            'type_activite' => 'global',
            'type_avantage' => 'pourcentage',
            'valeur_avantage' => 20,
            'nombre_utilisations' => 4,
            'duree_jours' => 30,
            'description' => 'Privilège VIP : -20% sur 4 passages pendant 30 jours',
            'actif' => true,
        ]);
    }
}
