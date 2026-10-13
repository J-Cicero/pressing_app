<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaisseClientLookupController extends Controller
{
    /**
     * Search client by phone number and return profile, vehicles and loyalty tier.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $phone = $request->query('telephone');

        if (! $phone) {
            return response()->json(['found' => false]);
        }

        $client = Client::where('telephone', $phone)
            ->orWhere('telephone', 'like', "%{$phone}%")
            ->with('vehicules')
            ->first();

        if (! $client) {
            return response()->json(['found' => false]);
        }

        $fidelite = $client->fideliteAtteinte();

        return response()->json([
            'found' => true,
            'client' => [
                'id' => $client->id,
                'nom' => $client->nom,
                'telephone' => $client->telephone,
                'email' => $client->email,
                'total_visites' => $client->totalVisites(),
                'visites_pressing' => $client->visitesPressing(),
                'visites_lavage' => $client->visitesLavage(),
            ],
            'vehicules' => $client->vehicules->map(fn ($v) => [
                'id' => $v->id,
                'immatriculation' => $v->immatriculation,
                'marque_modele' => $v->marque_modele,
                'type_vehicule' => $v->type_vehicule,
                'date_visite_technique' => $v->date_visite_technique?->format('Y-m-d'),
                'date_assurance' => $v->date_assurance?->format('Y-m-d'),
            ]),
            'fidelite' => $fidelite ? [
                'id' => $fidelite->id,
                'seuil_visites' => $fidelite->seuil_visites,
                'type_avantage' => $fidelite->type_avantage,
                'valeur_avantage' => $fidelite->valeur_avantage,
                'description' => $fidelite->description,
            ] : null,
        ]);
    }
}
