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
        $email = trim((string) $request->query('email', ''));
        $phone = trim((string) $request->query('telephone', ''));
        $q = trim((string) $request->query('q', ''));

        if ($email === '' && $phone === '' && $q === '') {
            return response()->json(['found' => false]);
        }

        $clientQuery = Client::query()->with('vehicules');

        if ($email !== '') {
            $clientQuery->where('email', $email);
        } elseif ($phone !== '') {
            $clientQuery->where(function ($sub) use ($phone) {
                $sub->where('telephone', $phone)
                    ->orWhere('telephone', 'like', "%{$phone}%");
            });
        } elseif ($q !== '') {
            $clientQuery->where(function ($sub) use ($q) {
                $sub->where('email', $q)
                    ->orWhere('telephone', $q)
                    ->orWhere('telephone', 'like', "%{$q}%");
            });
        }

        $client = $clientQuery->first();

        if (! $client) {
            return response()->json(['found' => false]);
        }

        $pressing = $request->user()?->pressing;
        $activite = $pressing?->type_activite;

        $fidelite = $client->fideliteAtteinte($activite);
        $avantages = $client->avantagesDisponibles($activite);

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
            'avantages' => $avantages->map(fn ($av) => [
                'id' => $av->id,
                'regle_id' => $av->regle_fidelite_id,
                'description' => $av->regleFidelite?->description,
                'type_avantage' => $av->regleFidelite?->type_avantage,
                'valeur_avantage' => (float) ($av->regleFidelite?->valeur_avantage ?? 0),
                'utilisations_restantes' => $av->utilisations_restantes,
                'date_expiration' => $av->date_expiration?->format('d/m/Y'),
            ])->values(),
        ]);
    }
}
