<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Vehicule;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CaisseVehiculeController extends Controller
{
    /**
     * Display vehicle technical deadlines for cashier with WhatsApp actions.
     */
    public function echeances(Request $request, WhatsAppNotificationService $whatsApp): View
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence assignée.');
        }

        $filtre = $request->query('filtre', 'tous');
        $today = Carbon::today();

        $vehiculesQuery = Vehicule::with('client')
            ->whereNotNull('date_visite_technique')
            ->orderBy('date_visite_technique');

        if ($filtre === 'jour_j') {
            $vehiculesQuery->whereDate('date_visite_technique', $today);
        } elseif ($filtre === 'j_3') {
            $vehiculesQuery->whereDate('date_visite_technique', $today->copy()->addDays(3));
        } elseif ($filtre === 'j_7') {
            $vehiculesQuery->whereDate('date_visite_technique', $today->copy()->addDays(7));
        } elseif ($filtre === 'retard') {
            $vehiculesQuery->whereDate('date_visite_technique', '<', $today);
        } elseif ($filtre === 'prochains') {
            $vehiculesQuery->whereBetween('date_visite_technique', [$today, $today->copy()->addDays(30)]);
        }

        $vehicules = $vehiculesQuery->paginate(20)->withQueryString();

        $countJourJ = Vehicule::whereDate('date_visite_technique', $today)->count();
        $countJ3 = Vehicule::whereDate('date_visite_technique', $today->copy()->addDays(3))->count();
        $countJ7 = Vehicule::whereDate('date_visite_technique', $today->copy()->addDays(7))->count();
        $countRetard = Vehicule::whereDate('date_visite_technique', '<', $today)->count();

        $vehicules->getCollection()->transform(function ($v) use ($whatsApp) {
            $diff = $v->joursAvantVisiteTechnique();

            if ($diff < 0) {
                $v->whatsapp_url = $whatsApp->urlRappelVisiteJourJ($v);
                $v->echeance_label = 'Expirée (Retard)';
                $v->echeance_badge = 'urgent';
            } elseif ($diff === 0) {
                $v->whatsapp_url = $whatsApp->urlRappelVisiteJourJ($v);
                $v->echeance_label = 'Expire Aujourd\'hui (Jour J)';
                $v->echeance_badge = 'jour_j';
            } elseif ($diff <= 3) {
                $v->whatsapp_url = $whatsApp->urlRappelVisiteJMoins3($v);
                $v->echeance_label = "Dans {$diff} jour(s) (J-3)";
                $v->echeance_badge = 'j_3';
            } elseif ($diff <= 7) {
                $v->whatsapp_url = $whatsApp->urlRappelVisiteJMoins7($v);
                $v->echeance_label = "Dans {$diff} jours (J-7)";
                $v->echeance_badge = 'j_7';
            } else {
                $v->whatsapp_url = $whatsApp->urlRappelVisiteJMoins7($v);
                $v->echeance_label = "Dans {$diff} jours";
                $v->echeance_badge = 'futur';
            }

            return $v;
        });

        return view('caisse.vehicules.echeances', [
            'pressing' => $pressing,
            'vehicules' => $vehicules,
            'filtre' => $filtre,
            'countJourJ' => $countJourJ,
            'countJ3' => $countJ3,
            'countJ7' => $countJ7,
            'countRetard' => $countRetard,
        ]);
    }

    /**
     * Store a new vehicle and its deadlines.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_telephone' => ['required', 'string', 'max:50'],
            'client_nom' => ['required', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'immatriculation' => ['required', 'string', 'max:50'],
            'marque_modele' => ['required', 'string', 'max:100'],
            'type_vehicule' => ['required', 'string', 'in:moto,voiture,4x4,utilitaire,autre'],
            'date_visite_technique' => ['nullable', 'date'],
            'date_assurance' => ['nullable', 'date'],
        ]);

        $client = Client::firstOrCreate(
            ['telephone' => trim($validated['client_telephone'])],
            [
                'nom' => trim($validated['client_nom']),
                'email' => $validated['client_email'] ?? null,
            ]
        );

        if (! empty($validated['client_nom']) && $client->nom !== $validated['client_nom']) {
            $client->update([
                'nom' => trim($validated['client_nom']),
                'email' => $validated['client_email'] ?? $client->email,
            ]);
        }

        $immat = strtoupper(trim($validated['immatriculation']));

        $vehicule = Vehicule::updateOrCreate(
            ['immatriculation' => $immat],
            [
                'client_id' => $client->id,
                'marque_modele' => trim($validated['marque_modele']),
                'type_vehicule' => $validated['type_vehicule'],
                'date_visite_technique' => $validated['date_visite_technique'] ?? null,
                'date_assurance' => $validated['date_assurance'] ?? null,
            ]
        );

        return back()->with('status', "Véhicule {$vehicule->immatriculation} et ses échéances enregistrés avec succès.");
    }

    /**
     * Update an existing vehicle's deadlines.
     */
    public function update(Request $request, Vehicule $vehicule): RedirectResponse
    {
        $validated = $request->validate([
            'marque_modele' => ['nullable', 'string', 'max:100'],
            'type_vehicule' => ['nullable', 'string', 'in:moto,voiture,4x4,utilitaire,autre'],
            'date_visite_technique' => ['nullable', 'date'],
            'date_assurance' => ['nullable', 'date'],
        ]);

        $vehicule->update([
            'marque_modele' => $validated['marque_modele'] ?? $vehicule->marque_modele,
            'type_vehicule' => $validated['type_vehicule'] ?? $vehicule->type_vehicule,
            'date_visite_technique' => $validated['date_visite_technique'] ?? null,
            'date_assurance' => $validated['date_assurance'] ?? null,
        ]);

        return back()->with('status', "Échéances du véhicule {$vehicule->immatriculation} mises à jour avec succès.");
    }
}
