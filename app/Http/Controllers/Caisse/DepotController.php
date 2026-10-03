<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepotController extends Controller
{
    /**
     * Show the deposit form for recording incoming laundry.
     */
    public function create(): View
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence de pressing n\'est assignée à ce compte caissier.');
        }

        $services = Service::where('pressing_id', $pressing->id)
            ->orderBy('designation')
            ->get();

        return view('caisse.depot', [
            'pressing' => $pressing,
            'services' => $services,
        ]);
    }

    /**
     * Store a newly created deposit and its line items within a transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence assignée.');
        }

        $validated = $request->validate([
            'client_nom' => ['required', 'string', 'max:255'],
            'client_telephone' => ['required', 'string', 'max:50'],
            'client_email' => ['nullable', 'string', 'email', 'max:255'],
            'date_retrait_prevue' => ['required', 'date', 'after_or_equal:today'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.service_id' => ['required', 'exists:services,id'],
            'lignes.*.quantite' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        // Vérifier l'isolation : tous les services doivent appartenir au pressing du caissier
        $serviceIds = collect($validated['lignes'])->pluck('service_id')->unique();
        $services = Service::where('pressing_id', $pressing->id)
            ->whereIn('id', $serviceIds)
            ->get()
            ->keyBy('id');

        if ($services->count() !== $serviceIds->count()) {
            return back()->withErrors([
                'error' => 'Une ou plusieurs prestations sélectionnées n\'appartiennent pas à votre agence.',
            ])->withInput();
        }

        $facture = DB::transaction(function () use ($validated, $pressing, $user, $services) {
            // Génération d'un numéro de ticket unique TCK-YYYYMMDD-XXXX
            $datePrefix = 'TCK-'.date('Ymd').'-';
            do {
                $numTicket = $datePrefix.strtoupper(Str::random(4));
            } while (Facture::where('num_ticket', $numTicket)->exists());

            // Calcul fiable côté serveur du montant total
            $montantTotal = 0;
            $itemsData = [];

            foreach ($validated['lignes'] as $ligne) {
                $service = $services->get($ligne['service_id']);
                $quantite = (int) $ligne['quantite'];
                $prixApplique = (float) $service->prix_unitaire;
                $montantTotal += ($prixApplique * $quantite);

                $itemsData[] = [
                    'service_id' => $service->id,
                    'quantite' => $quantite,
                    'prix_applique' => $prixApplique,
                ];
            }

            // Création de la facture (Règle : 100% au retrait, aucun acompte, statut initial 'depose')
            $facture = Facture::create([
                'num_ticket' => $numTicket,
                'pressing_id' => $pressing->id,
                'user_id' => $user->id,
                'client_nom' => $validated['client_nom'],
                'client_telephone' => $validated['client_telephone'],
                'client_email' => $validated['client_email'] ?? null,
                'date_retrait_prevue' => $validated['date_retrait_prevue'],
                'montant_total' => $montantTotal,
                'statut' => 'depose',
                'paye_at' => null,
            ]);

            // Insertion des lignes de facturation
            foreach ($itemsData as $item) {
                LigneFacture::create([
                    'facture_id' => $facture->id,
                    'service_id' => $item['service_id'],
                    'quantite' => $item['quantite'],
                    'prix_applique' => $item['prix_applique'],
                ]);
            }

            return $facture;
        });

        return redirect()->route('caisse.factures.print', $facture)
            ->with('status', 'Dépôt validé avec succès. Ticket généré : '.$facture->num_ticket);
    }
}
