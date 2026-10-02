<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\Pressing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FactureController extends Controller
{
    /**
     * Display a consolidated listing of invoices.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $pressingId = $request->query('pressing_id');
        $statut = $request->query('statut');

        $query = Facture::query()
            ->with(['pressing', 'user', 'ligneFactures.service'])
            ->when($search, function ($q, $search) {
                $q->where('num_ticket', 'like', "%{$search}%");
            })
            ->when($pressingId, function ($q, $pressingId) {
                $q->where('pressing_id', $pressingId);
            })
            ->when($statut, function ($q, $statut) {
                $q->where('statut', $statut);
            });

        // Totaux calculés directement depuis la requête filtrée en BDD
        $totalMontant = (float) (clone $query)->sum('montant_total');
        $totalTickets = (clone $query)->count();

        $factures = $query->latest('id')
            ->paginate(20)
            ->withQueryString();

        $pressings = Pressing::orderBy('nom')->get();

        return view('admin.factures.index', [
            'factures' => $factures,
            'pressings' => $pressings,
            'search' => $search,
            'selectedPressingId' => $pressingId,
            'selectedStatut' => $statut,
            'totalMontant' => $totalMontant,
            'totalTickets' => $totalTickets,
        ]);
    }

    /**
     * Display the specified invoice details using Route Key Binding (num_ticket).
     */
    public function show(Facture $facture): View
    {
        $facture->load(['pressing', 'user', 'ligneFactures.service']);

        return view('admin.factures.show', [
            'facture' => $facture,
        ]);
    }
}
