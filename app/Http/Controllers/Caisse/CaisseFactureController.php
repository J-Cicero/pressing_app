<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CaisseFactureController extends Controller
{
    /**
     * Display a listing of tickets/invoices for the cashier's agency.
     */
    public function index(Request $request): View
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence assignée.');
        }

        $search = $request->query('q');
        $statut = $request->query('statut');
        $date = $request->query('date');

        $query = Facture::query()
            ->where('pressing_id', $pressing->id)
            ->with(['laveur', 'vehicule', 'user', 'ligneFactures.service'])
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('num_ticket', 'like', "%{$search}%")
                        ->orWhere('client_nom', 'like', "%{$search}%")
                        ->orWhere('client_telephone', 'like', "%{$search}%")
                        ->orWhereHas('vehicule', function ($v) use ($search) {
                            $v->where('immatriculation', 'like', "%{$search}%");
                        });
                });
            })
            ->when($statut, function ($q, $statut) {
                $q->where('statut', $statut);
            })
            ->when($date, function ($q, $date) {
                $q->whereDate('created_at', $date);
            });

        $totalMontant = (float) (clone $query)->sum('montant_total');
        $totalTickets = (clone $query)->count();

        $factures = $query->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('caisse.factures.index', [
            'pressing' => $pressing,
            'factures' => $factures,
            'search' => $search,
            'selectedStatut' => $statut,
            'selectedDate' => $date,
            'totalMontant' => $totalMontant,
            'totalTickets' => $totalTickets,
        ]);
    }
}
