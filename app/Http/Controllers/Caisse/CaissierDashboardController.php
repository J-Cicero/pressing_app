<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CaissierDashboardController extends Controller
{
    /**
     * Display the cashier dashboard with real-time statistics for their agency.
     */
    public function index(): View
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence de pressing n\'est assignée à ce compte caissier.');
        }

        $today = Carbon::today();

        // 1. Nombre de tickets déposés aujourd'hui dans l'agence
        $ticketsDeposesAujourdhui = Facture::where('pressing_id', $pressing->id)
            ->whereDate('created_at', $today)
            ->count();

        // 2. Nombre de factures prêtes à être retirées (vêtements prêts)
        $ticketsPrets = Facture::where('pressing_id', $pressing->id)
            ->where('statut', 'pret')
            ->count();

        // 3. Total encaissé aujourd'hui (statut = 'paye_retire' et paye_at aujourd'hui)
        $totalEncaisseAujourdhui = (float) Facture::where('pressing_id', $pressing->id)
            ->where('statut', 'paye_retire')
            ->whereDate('paye_at', $today)
            ->sum('montant_total');

        // 4. Total tickets en cours (déposés + prêts)
        $totalEnCours = Facture::where('pressing_id', $pressing->id)
            ->whereIn('statut', ['depose', 'pret'])
            ->count();

        // Dernières factures de l'agence
        $dernieresFactures = Facture::where('pressing_id', $pressing->id)
            ->latest('id')
            ->take(8)
            ->get();

        return view('caisse.dashboard', [
            'user' => $user,
            'pressing' => $pressing,
            'ticketsDeposesAujourdhui' => $ticketsDeposesAujourdhui,
            'ticketsPrets' => $ticketsPrets,
            'totalEncaisseAujourdhui' => $totalEncaisseAujourdhui,
            'totalEnCours' => $totalEnCours,
            'dernieresFactures' => $dernieresFactures,
        ]);
    }
}
