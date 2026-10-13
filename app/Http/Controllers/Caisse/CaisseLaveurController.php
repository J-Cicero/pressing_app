<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\Laveur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CaisseLaveurController extends Controller
{
    /**
     * Display washers daily activity and commissions summary.
     */
    public function index(Request $request): View
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence assignée.');
        }

        $date = $request->query('date', Carbon::today()->toDateString());
        $carbonDate = Carbon::parse($date);

        $laveurs = Laveur::where('pressing_id', $pressing->id)
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        // Calculate stats for each washer on that date
        $recap = $laveurs->map(function (Laveur $laveur) use ($carbonDate, $pressing) {
            $factures = Facture::where('pressing_id', $pressing->id)
                ->where('laveur_id', $laveur->id)
                ->whereDate('created_at', $carbonDate)
                ->with(['ligneFactures.service', 'vehicule'])
                ->get();

            $totalCa = $factures->sum('montant_total');
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

        return view('caisse.laveurs.index', [
            'pressing' => $pressing,
            'date' => $date,
            'recap' => $recap,
            'totalJourCa' => $totalJourCa,
            'totalJourCommission' => $totalJourCommission,
            'totalJourLavages' => $totalJourLavages,
        ]);
    }
}
