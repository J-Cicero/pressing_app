<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Mail\TicketDeposeMail;
use App\Models\Client;
use App\Models\ClientFideliteAvantage;
use App\Models\Facture;
use App\Models\Laveur;
use App\Models\LigneFacture;
use App\Models\RegleFidelite;
use App\Models\Service;
use App\Models\Vehicule;
use App\Models\VisiteTechnique;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepotController extends Controller
{
    /**
     * Show the deposit / wash registration form.
     */
    public function create(): View
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence n\'est assignée à ce compte caissier.');
        }

        $services = Service::where('pressing_id', $pressing->id)
            ->orderBy('designation')
            ->get();

        $laveurs = Laveur::where('pressing_id', $pressing->id)
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        $viewName = $pressing->isLavage() ? 'caisse.lavage.depot' : 'caisse.pressing.depot';

        return view($viewName, [
            'pressing' => $pressing,
            'services' => $services,
            'laveurs' => $laveurs,
        ]);
    }

    /**
     * Store a newly created deposit and its line items within a transaction.
     */
    public function store(Request $request, WhatsAppNotificationService $whatsApp): RedirectResponse
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence assignée.');
        }

        $rules = [
            'client_nom' => ['required', 'string', 'max:255'],
            'client_telephone' => ['required', 'string', 'max:50'],
            'client_email' => ['nullable', 'string', 'email', 'max:255'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.service_id' => ['required', 'exists:services,id'],
            'lignes.*.quantite' => ['required', 'integer', 'min:1', 'max:1000'],
            // Wash specific fields
            'laveur_id' => ['nullable', 'exists:laveurs,id'],
            'immatriculation' => ['nullable', 'string', 'max:50'],
            'marque_modele' => ['nullable', 'string', 'max:100'],
            'type_vehicule' => ['nullable', 'string', 'max:100'],
            'date_visite_technique' => ['nullable', 'date'],
            'date_assurance' => ['nullable', 'date'],
            // Loyalty reward application
            'client_fidelite_avantage_id' => ['nullable', 'exists:client_fidelite_avantages,id'],
            'regle_fidelite_id' => ['nullable', 'exists:regles_fidelite,id'],
        ];

        if ($pressing->isLavage()) {
            $rules['date_retrait_prevue'] = ['nullable', 'date', 'after_or_equal:today'];
        } else {
            $rules['date_retrait_prevue'] = ['required', 'date', 'after_or_equal:today'];
        }

        $validated = $request->validate($rules);

        if (empty($validated['date_retrait_prevue'])) {
            $validated['date_retrait_prevue'] = now()->toDateString();
        }

        // Vérifier l'isolation des services : doivent appartenir à l'établissement du caissier
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
            // 1. Centraliser ou mettre à jour le client (priorité email, fallback téléphone)
            $email = ! empty($validated['client_email']) ? trim($validated['client_email']) : null;
            $phone = trim($validated['client_telephone']);

            $client = null;
            if ($email) {
                $client = Client::where('email', $email)->first();
            }
            if (! $client && $phone) {
                $client = Client::where('telephone', $phone)->first();
            }

            if (! $client) {
                $client = Client::create([
                    'nom' => trim($validated['client_nom']),
                    'telephone' => $phone,
                    'email' => $email,
                ]);
            } else {
                $client->update([
                    'nom' => trim($validated['client_nom']),
                    'telephone' => $phone,
                    'email' => $email ?? $client->email,
                ]);
            }

            // 2. Gestion du véhicule si station de lavage et enregistrement détaché des visites techniques
            $vehicule = null;
            if ($pressing->isLavage()) {
                $immat = ! empty($validated['immatriculation'])
                    ? strtoupper(trim($validated['immatriculation']))
                    : 'VEH-'.date('ymd').'-'.strtoupper(Str::random(3));

                $marqueModele = ! empty($validated['marque_modele']) ? trim($validated['marque_modele']) : 'Véhicule';
                $typeVehicule = ! empty($validated['type_vehicule']) ? trim($validated['type_vehicule']) : 'Moto/Auto';

                $vehicule = Vehicule::updateOrCreate(
                    ['immatriculation' => $immat],
                    [
                        'client_id' => $client->id,
                        'marque_modele' => $marqueModele,
                        'type_vehicule' => $typeVehicule,
                        'date_visite_technique' => $validated['date_visite_technique'] ?? null,
                        'date_assurance' => $validated['date_assurance'] ?? null,
                    ]
                );

                // Enregistrement dédié et détaché de la visite technique dans la table spécifique
                if (! empty($validated['date_visite_technique'])) {
                    VisiteTechnique::create([
                        'vehicule_id' => $vehicule->id,
                        'client_id' => $client->id,
                        'date_visite' => now()->toDateString(),
                        'date_echeance' => $validated['date_visite_technique'],
                        'date_assurance' => $validated['date_assurance'] ?? null,
                        'statut' => Carbon::parse($validated['date_visite_technique'])->isPast() ? 'expire' : 'valide',
                        'centre_controle' => 'Station '.$pressing->nom,
                    ]);
                }
            }

            // 3. Génération du numéro de ticket unique TCK-YYYYMMDD-XXXX
            $datePrefix = 'TCK-'.date('Ymd').'-';
            do {
                $numTicket = $datePrefix.strtoupper(Str::random(4));
            } while (Facture::where('num_ticket', $numTicket)->exists());

            // 4. Calcul du montant brut des prestations
            $montantBrut = 0;
            $itemsData = [];

            foreach ($validated['lignes'] as $ligne) {
                $service = $services->get($ligne['service_id']);
                $quantite = (int) $ligne['quantite'];
                $prixApplique = (float) $service->prix_unitaire;
                $montantBrut += ($prixApplique * $quantite);

                $itemsData[] = [
                    'service_id' => $service->id,
                    'quantite' => $quantite,
                    'prix_applique' => $prixApplique,
                ];
            }

            // 5. Calcul de la remise de fidélité accordée au client
            $remiseFidelite = 0.0;
            $regleFideliteId = null;
            $avantageId = null;
            $detailsRemise = null;
            $avantageAUtiliser = null;

            if (! empty($validated['client_fidelite_avantage_id'])) {
                $avantage = ClientFideliteAvantage::where('id', $validated['client_fidelite_avantage_id'])
                    ->where('client_id', $client->id)
                    ->with('regleFidelite')
                    ->first();

                if ($avantage && $avantage->estValide()) {
                    $regle = $avantage->regleFidelite;
                    if ($regle && $regle->actif) {
                        $remiseFidelite = min($montantBrut, $regle->calculerRemise($montantBrut));
                        $regleFideliteId = $regle->id;
                        $avantageId = $avantage->id;
                        $detailsRemise = "Privilège Fidélité : {$regle->description}";
                        $avantageAUtiliser = $avantage;
                    }
                }
            } elseif (! empty($validated['regle_fidelite_id'])) {
                $regle = RegleFidelite::find($validated['regle_fidelite_id']);
                if ($regle && $regle->actif && $client->estEligibleRegle($regle)) {
                    $remiseFidelite = min($montantBrut, $regle->calculerRemise($montantBrut));
                    $regleFideliteId = $regle->id;
                    $detailsRemise = "Privilège Fidélité : {$regle->description}";
                }
            }

            $montantNet = max(0.0, $montantBrut - $remiseFidelite);

            // 6. Création de la facture
            // Règle fondamentale : laveur_id est associé au lavage avec rémunération calculée sur le montant_brut
            $facture = Facture::create([
                'num_ticket' => $numTicket,
                'pressing_id' => $pressing->id,
                'user_id' => $user->id,
                'client_id' => $client->id,
                'vehicule_id' => $vehicule?->id,
                'laveur_id' => $pressing->isLavage() ? ($validated['laveur_id'] ?? null) : null,
                'client_nom' => $client->nom,
                'client_telephone' => $client->telephone,
                'client_email' => $client->email,
                'montant_brut' => $montantBrut,
                'remise_fidelite' => $remiseFidelite,
                'regle_fidelite_id' => $regleFideliteId,
                'client_fidelite_avantage_id' => $avantageId,
                'details_remise' => $detailsRemise,
                'date_retrait_prevue' => $validated['date_retrait_prevue'],
                'montant_total' => $montantNet,
                'statut' => 'depose',
                'paye_at' => null,
            ]);

            // Décompte de l'avantage fidélité utilisé
            if ($avantageAUtiliser) {
                $avantageAUtiliser->consommer($facture, $remiseFidelite);
            }

            // 6. Lignes de facturation
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

        // Charger toutes les relations indispensables pour les emails, tickets et notifications
        $facture->load(['pressing', 'vehicule', 'laveur', 'ligneFactures.service', 'client']);

        // Email notification (optionnel)
        if (! empty($facture->client_email)) {
            try {
                Mail::to($facture->client_email)->send(new TicketDeposeMail($facture));
            } catch (\Throwable $e) {
                Log::error('Erreur lors de l\'envoi de l\'e-mail de dépôt : '.$e->getMessage());
            }
        }

        // Génération du lien WhatsApp
        $whatsappUrl = $whatsApp->urlDepot($facture);

        // Vérification de fidélité pour le client
        $fideliteMessage = null;
        $fideliteUrl = null;
        if ($facture->client) {
            $regle = $facture->client->fideliteAtteinte($pressing->type_activite);
            if ($regle) {
                $fideliteMessage = "Palier de fidélité atteint : {$regle->seuil_visites} visites ({$regle->description}) !";
                $fideliteUrl = $whatsApp->urlFidelite($facture->client, $regle);
            }
        }

        return redirect()->route('caisse.factures.print', $facture)
            ->with('status', 'Ticket généré avec succès : '.$facture->num_ticket)
            ->with('whatsapp_url', $whatsappUrl)
            ->with('fidelite_message', $fideliteMessage)
            ->with('fidelite_url', $fideliteUrl);
    }
}
