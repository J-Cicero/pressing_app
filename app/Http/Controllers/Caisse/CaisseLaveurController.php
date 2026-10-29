<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\Laveur;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CaisseLaveurController extends Controller
{
    /**
     * Display washers daily activity and read-only monthly commission recap.
     */
    public function index(Request $request): View
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence assignée.');
        }

        $date = (string) $request->query('date', Carbon::today()->toDateString());
        try {
            $carbonDate = Carbon::parse($date);
            $date = $carbonDate->toDateString();
        } catch (\Throwable) {
            $carbonDate = Carbon::today();
            $date = $carbonDate->toDateString();
        }

        $mois = (string) $request->query('mois', Carbon::today()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $mois)) {
            $mois = Carbon::today()->format('Y-m');
        }

        $laveurs = Laveur::where('pressing_id', $pressing->id)
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        // 1. Calculate stats for each washer on the selected date
        $recap = $laveurs->map(function (Laveur $laveur) use ($carbonDate, $pressing) {
            $factures = Facture::where('pressing_id', $pressing->id)
                ->where('laveur_id', $laveur->id)
                ->whereDate('created_at', $carbonDate)
                ->with(['ligneFactures.service', 'vehicule'])
                ->get();

            $totalCa = $factures->sum(fn ($f) => $f->montant_brut ?: $f->montant_total);
            $totalCommission = $factures->sum(fn ($f) => $laveur->calculerCommission($f));

            return [
                'laveur' => $laveur,
                'nb_lavages' => $factures->count(),
                'total_ca' => $totalCa,
                'total_commission' => $totalCommission,
                'factures' => $factures,
            ];
        });

        $totalJourCa = $recap->sum('total_ca');
        $totalJourCommission = $recap->sum('total_commission');
        $totalJourLavages = $recap->sum('nb_lavages');

        // 2. Calculate read-only monthly salaries/commissions due to washers
        $recapMois = $laveurs->map(function (Laveur $laveur) use ($mois) {
            $nbLavages = $laveur->nombreLavagesMois($mois);
            $totalCa = $laveur->totalCaMois($mois);
            $totalCommission = $laveur->totalCommissionMois($mois);
            $estPaye = $laveur->estPayeMois($mois);
            $resteAPayer = $laveur->resteAPayerMois($mois);
            $paiement = $laveur->paiementMois($mois);

            return [
                'laveur' => $laveur,
                'nb_lavages' => $nbLavages,
                'total_ca' => $totalCa,
                'total_commission' => $totalCommission,
                'est_paye' => $estPaye,
                'reste_a_payer' => $resteAPayer,
                'paiement' => $paiement,
            ];
        });

        $totalMoisLavages = $recapMois->sum('nb_lavages');
        $totalMoisCa = $recapMois->sum('total_ca');
        $totalMoisCommission = $recapMois->sum('total_commission');
        $totalMoisResteAPayer = $recapMois->sum('reste_a_payer');

        return view('caisse.laveurs.index', [
            'pressing' => $pressing,
            'date' => $date,
            'mois' => $mois,
            'recap' => $recap,
            'totalJourCa' => $totalJourCa,
            'totalJourCommission' => $totalJourCommission,
            'totalJourLavages' => $totalJourLavages,
            'recapMois' => $recapMois,
            'totalMoisLavages' => $totalMoisLavages,
            'totalMoisCa' => $totalMoisCa,
            'totalMoisCommission' => $totalMoisCommission,
            'totalMoisResteAPayer' => $totalMoisResteAPayer,
        ]);
    }

    /**
     * Store a newly registered washer for this station.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence assignée.');
        }

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:50'],
        ]);

        $laveur = Laveur::create([
            'pressing_id' => $pressing->id,
            'nom' => trim($validated['nom']),
            'telephone' => trim($validated['telephone']),
            'type_remuneration' => 'pourcentage',
            'valeur_remuneration' => 30.00, // Règle d'entreprise : 30% commission
            'actif' => true,
        ]);

        return redirect()->route('caisse.laveurs.index')
            ->with('status', "Le laveur {$laveur->nom} a été enregistré avec succès pour votre station (30% de commission).");
    }
}
