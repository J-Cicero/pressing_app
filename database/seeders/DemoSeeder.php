<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Laveur;
use App\Models\Pressing;
use App\Models\User;
use App\Models\Vehicule;
use App\Models\VisiteTechnique;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Optional demo seeder containing simulated mock clients and transactions.
     * Can be invoked manually via: php artisan db:seed --class=DemoSeeder
     */
    public function run(): void
    {
        $station = Pressing::where('type_activite', 'lavage')->first();
        $pressing = Pressing::where('type_activite', 'pressing')->first();

        if (! $station || ! $pressing) {
            $this->call(DatabaseSeeder::class);
            $station = Pressing::where('type_activite', 'lavage')->first();
            $pressing = Pressing::where('type_activite', 'pressing')->first();
        }

        $caissierLavage = User::where('pressing_id', $station->id)->first();
        $caissierPressing = User::where('pressing_id', $pressing->id)->first();
        $laveur = Laveur::where('pressing_id', $station->id)->first();

        $today = Carbon::today();

        // 1. Client avec véhicule et échéance Jour J
        $client1 = Client::firstOrCreate(
            ['email' => 'yao.demo@example.com'],
            ['nom' => 'Yao Demo', 'telephone' => '90112233']
        );

        $vehicule1 = Vehicule::firstOrCreate(
            ['immatriculation' => 'TG-1234-AX'],
            [
                'client_id' => $client1->id,
                'marque_modele' => 'Toyota RAV4',
                'type_vehicule' => 'SUV / 4x4',
                'date_visite_technique' => $today->copy(),
                'date_assurance' => $today->copy()->addMonths(6),
            ]
        );

        VisiteTechnique::firstOrCreate(
            ['vehicule_id' => $vehicule1->id, 'date_echeance' => $today->copy()->toDateString()],
            [
                'client_id' => $client1->id,
                'date_visite' => $today->copy()->subYear()->toDateString(),
                'statut' => 'valide',
                'centre_controle' => 'Station '.$station->nom,
            ]
        );

        // 2. Client régulier Pressing
        $client2 = Client::firstOrCreate(
            ['email' => 'afiwa.demo@example.com'],
            ['nom' => 'Afiwa Demo', 'telephone' => '91223344']
        );
    }
}
