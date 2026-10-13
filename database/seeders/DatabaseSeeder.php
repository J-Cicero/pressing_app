<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Laveur;
use App\Models\Pressing;
use App\Models\RegleFidelite;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
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

        // 1. Super Administrateur (aucun pressing assigné)
        User::create([
            'name' => 'Super Administrateur',
            'email' => 'admin@pressing.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'pressing_id' => null,
        ]);

        // 2. Établissements : Station de Lavage & Pressing
        $stationLavage = Pressing::create([
            'nom' => 'AutoWash Express Lomé',
            'ville' => 'Lomé',
            'quartier' => 'Bè-Kpota',
            'telephone' => '+228 90 11 22 33',
            'type_activite' => 'lavage',
        ]);

        $pressingRoyal = Pressing::create([
            'nom' => 'Pressing Royal Déckon',
            'ville' => 'Lomé',
            'quartier' => 'Déckon',
            'telephone' => '+228 91 44 55 66',
            'type_activite' => 'pressing',
        ]);

        // 3. Caissiers de démonstration
        User::create([
            'name' => 'Caissier Lavage',
            'email' => 'caissier.lavage@pressing.com',
            'password' => Hash::make('password'),
            'role' => 'caissier',
            'pressing_id' => $stationLavage->id,
        ]);

        User::create([
            'name' => 'Caissier Pressing',
            'email' => 'caissier@pressing.com',
            'password' => Hash::make('password'),
            'role' => 'caissier',
            'pressing_id' => $pressingRoyal->id,
        ]);

        // 4. Catalogue des Prestations Lavage
        Service::create([
            'pressing_id' => $stationLavage->id,
            'designation' => 'Lavage Complet Berline (Extérieur + Intérieur)',
            'prix_unitaire' => 3000,
        ]);

        Service::create([
            'pressing_id' => $stationLavage->id,
            'designation' => 'Lavage Complet 4x4 / SUV / Pick-up',
            'prix_unitaire' => 5000,
        ]);

        Service::create([
            'pressing_id' => $stationLavage->id,
            'designation' => 'Lavage Moto / Scooter Express',
            'prix_unitaire' => 1000,
        ]);

        Service::create([
            'pressing_id' => $stationLavage->id,
            'designation' => 'Nettoyage Moteur & Graissage',
            'prix_unitaire' => 4000,
        ]);

        // 5. Catalogue des Prestations Pressing
        Service::create([
            'pressing_id' => $pressingRoyal->id,
            'designation' => 'Nettoyage & Repassage Costume 2 pièces',
            'prix_unitaire' => 2500,
        ]);

        Service::create([
            'pressing_id' => $pressingRoyal->id,
            'designation' => 'Repassage Chemise / Polo',
            'prix_unitaire' => 500,
        ]);

        Service::create([
            'pressing_id' => $pressingRoyal->id,
            'designation' => 'Lavage Draps & Couette (Grande Taille)',
            'prix_unitaire' => 3500,
        ]);

        // 6. Laveurs de terrain (Personnel de Station)
        $laveur1 = Laveur::create([
            'pressing_id' => $stationLavage->id,
            'nom' => 'Kokou Agbeko',
            'telephone' => '+228 92 11 00 22',
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30, // 30% du CA réalisé
            'actif' => true,
        ]);

        $laveur2 = Laveur::create([
            'pressing_id' => $stationLavage->id,
            'nom' => 'Ablam Mensah',
            'telephone' => '+228 93 33 44 55',
            'type_remuneration' => 'forfait',
            'valeur_remuneration' => 1000, // 1000 FCFA par lavage
            'actif' => true,
        ]);

        // 7. Règles de Fidélité
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
            'description' => 'Un lavage complet offert pour votre fidélité',
            'actif' => true,
        ]);

        RegleFidelite::create([
            'seuil_visites' => 15,
            'type_activite' => 'global',
            'type_avantage' => 'cadeau',
            'valeur_avantage' => 0,
            'description' => 'Parfum auto VIP ou cadeau privilège offert',
            'actif' => true,
        ]);

        // 8. Clients Centralisés & Véhicules avec échéances
        $today = Carbon::today();

        $client1 = Client::create([
            'nom' => 'Yao Amouzou',
            'telephone' => '90123456',
            'email' => 'yao.amouzou@example.com',
        ]);

        $client2 = Client::create([
            'nom' => 'Afiwa Lawson',
            'telephone' => '91234567',
            'email' => 'afiwa.lawson@example.com',
        ]);

        $client3 = Client::create([
            'nom' => 'Kodjo Tossou',
            'telephone' => '92345678',
            'email' => 'kodjo.tossou@example.com',
        ]);

        $client4 = Client::create([
            'nom' => 'Koffi Adanbounou',
            'telephone' => '93456789',
            'email' => null,
        ]);

        // Véhicule 1 : Visite technique aujourd'hui (Jour J)
        Vehicule::create([
            'client_id' => $client1->id,
            'immatriculation' => 'TG-4521-BN',
            'marque_modele' => 'Toyota RAV4',
            'type_vehicule' => '4x4',
            'date_visite_technique' => $today->copy(),
            'date_assurance' => $today->copy()->addMonths(6),
        ]);

        // Véhicule 2 : Visite technique dans 3 jours (J-3)
        Vehicule::create([
            'client_id' => $client2->id,
            'immatriculation' => 'TG-8890-AH',
            'marque_modele' => 'Hyundai Tucson',
            'type_vehicule' => 'voiture',
            'date_visite_technique' => $today->copy()->addDays(3),
            'date_assurance' => $today->copy()->addMonths(4),
        ]);

        // Véhicule 3 : Visite technique dans 7 jours (J-7)
        Vehicule::create([
            'client_id' => $client3->id,
            'immatriculation' => 'TG-1123-CZ',
            'marque_modele' => 'Toyota Corolla',
            'type_vehicule' => 'voiture',
            'date_visite_technique' => $today->copy()->addDays(7),
            'date_assurance' => $today->copy()->addMonths(8),
        ]);

        // Véhicule 4 : Visite technique expirée en retard (-5 jours)
        Vehicule::create([
            'client_id' => $client4->id,
            'immatriculation' => 'TG-9912-AY',
            'marque_modele' => 'Yamaha TMAX 530',
            'type_vehicule' => 'moto',
            'date_visite_technique' => $today->copy()->subDays(5),
            'date_assurance' => $today->copy()->addMonths(1),
        ]);
    }
}
