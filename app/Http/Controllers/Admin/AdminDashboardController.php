<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\Pressing;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Super Dashboard with real-time MySQL database aggregates.
     */
    public function index(Request $request): View
    {
        $periode = $request->query('periode', 'mois');

        $now = Carbon::now();
        $startDate = null;
        $endDate = null;

        if ($periode === 'jour') {
            $startDate = $now->copy()->startOfDay();
            $endDate = $now->copy()->endOfDay();
        } elseif ($periode === 'mois') {
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
        }

        // 1. CA Global Consolidé
        $caJour = (float) Facture::where('statut', 'paye_retire')
            ->whereDate('paye_at', $now->toDateString())
            ->sum('montant_total');

        $caMois = (float) Facture::where('statut', 'paye_retire')
            ->whereYear('paye_at', $now->year)
            ->whereMonth('paye_at', $now->month)
            ->sum('montant_total');

        $totalImpayes = (float) Facture::where('statut', '!=', 'paye_retire')
            ->sum('montant_total');

        $caPeriodeQuery = Facture::where('statut', 'paye_retire');
        if ($startDate && $endDate) {
            $caPeriodeQuery->whereBetween('paye_at', [$startDate, $endDate]);
        }
        $caPeriode = (float) $caPeriodeQuery->sum('montant_total');

        // 2. Ségrégation : Pôle Blanchisserie & Pressing
        $caPressingJour = (float) Facture::where('statut', 'paye_retire')
            ->whereDate('paye_at', $now->toDateString())
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'pressing'))
            ->sum('montant_total');

        $caPressingPeriodeQuery = Facture::where('statut', 'paye_retire')
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'pressing'));
        if ($startDate && $endDate) {
            $caPressingPeriodeQuery->whereBetween('paye_at', [$startDate, $endDate]);
        }
        $caPressingPeriode = (float) $caPressingPeriodeQuery->sum('montant_total');

        $ticketsPressingPayes = Facture::where('statut', 'paye_retire')
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'pressing'))
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('paye_at', [$startDate, $endDate]))
            ->count();

        // 3. Ségrégation : Pôle Station de Lavage Auto/Moto
        $caLavageJour = (float) Facture::where('statut', 'paye_retire')
            ->whereDate('paye_at', $now->toDateString())
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'lavage'))
            ->sum('montant_total');

        $caLavagePeriodeQuery = Facture::where('statut', 'paye_retire')
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'lavage'));
        if ($startDate && $endDate) {
            $caLavagePeriodeQuery->whereBetween('paye_at', [$startDate, $endDate]);
        }
        $caLavagePeriode = (float) $caLavagePeriodeQuery->sum('montant_total');

        $lavagesPayes = Facture::where('statut', 'paye_retire')
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'lavage'))
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('paye_at', [$startDate, $endDate]))
            ->count();

        // Métriques de volume globales
        $totalTickets = Facture::count();
        $ticketsPayes = Facture::where('statut', 'paye_retire')->count();
        $ticketsEnAttente = Facture::where('statut', '!=', 'paye_retire')->count();

        // Tableau comparatif par agence (Eloquent queries avec withCount et withSum)
        $pressings = Pressing::query()
            ->withCount('factures as total_tickets')
            ->withSum(['factures as ca_mois' => function ($q) use ($now) {
                $q->where('statut', 'paye_retire')
                    ->whereYear('paye_at', $now->year)
                    ->whereMonth('paye_at', $now->month);
            }], 'montant_total')
            ->withSum(['factures as ca_filtre' => function ($q) use ($startDate, $endDate) {
                $q->where('statut', 'paye_retire');
                if ($startDate && $endDate) {
                    $q->whereBetween('paye_at', [$startDate, $endDate]);
                }
            }], 'montant_total')
            ->withSum(['factures as total_impayes' => function ($q) {
                $q->where('statut', '!=', 'paye_retire');
            }], 'montant_total')
            ->withCount('users as total_personnel')
            ->orderBy('type_activite')
            ->orderBy('nom')
            ->get();

        return view('admin.dashboard', [
            'periode' => $periode,
            'caJour' => $caJour,
            'caMois' => $caMois,
            'totalImpayes' => $totalImpayes,
            'caPeriode' => $caPeriode,
            'caPressingJour' => $caPressingJour,
            'caPressingPeriode' => $caPressingPeriode,
            'ticketsPressingPayes' => $ticketsPressingPayes,
            'caLavageJour' => $caLavageJour,
            'caLavagePeriode' => $caLavagePeriode,
            'lavagesPayes' => $lavagesPayes,
            'totalTickets' => $totalTickets,
            'ticketsPayes' => $ticketsPayes,
            'ticketsEnAttente' => $ticketsEnAttente,
            'pressings' => $pressings,
        ]);
    }
}
