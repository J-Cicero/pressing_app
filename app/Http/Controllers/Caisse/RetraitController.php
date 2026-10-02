<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RetraitController extends Controller
{
    /**
     * Display the search and checkout / pickup interface.
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

        $query = Facture::where('pressing_id', $pressing->id)
            ->with(['ligneFactures.service', 'user'])
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('num_ticket', 'like', "%{$search}%")
                        ->orWhere('client_telephone', 'like', "%{$search}%")
                        ->orWhere('client_nom', 'like', "%{$search}%");
                });
            })
            ->when($statut, function ($q, $statut) {
                $q->where('statut', $statut);
            });

        // Totaux pour les indicateurs rapides
        $countDepose = Facture::where('pressing_id', $pressing->id)->where('statut', 'depose')->count();
        $countPret = Facture::where('pressing_id', $pressing->id)->where('statut', 'pret')->count();
        $countPayeRetire = Facture::where('pressing_id', $pressing->id)->where('statut', 'paye_retire')->count();

        $factures = $query->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('caisse.retrait', [
            'pressing' => $pressing,
            'factures' => $factures,
            'search' => $search,
            'selectedStatut' => $statut,
            'countDepose' => $countDepose,
            'countPret' => $countPret,
            'countPayeRetire' => $countPayeRetire,
        ]);
    }

    /**
     * Update ticket status from 'depose' to 'pret' (garments washed & ready).
     */
    public function marquerPret(Facture $facture): RedirectResponse
    {
        $user = Auth::user();

        if ($facture->pressing_id !== $user->pressing_id) {
            abort(403, 'Accès non autorisé.');
        }

        if ($facture->statut !== 'depose') {
            return back()->withErrors([
                'error' => 'Seul un ticket à l\'état déposé peut être marqué comme prêt.',
            ]);
        }

        $facture->update([
            'statut' => 'pret',
        ]);

        return back()->with('status', 'Le ticket '.$facture->num_ticket.' est désormais marqué comme PRÊT pour le retrait client.');
    }

    /**
     * Process 100% payment and garment collection ('pret' or 'depose' -> 'paye_retire').
     */
    public function encaisser(Facture $facture): RedirectResponse
    {
        $user = Auth::user();

        if ($facture->pressing_id !== $user->pressing_id) {
            abort(403, 'Accès non autorisé.');
        }

        if ($facture->statut === 'paye_retire') {
            return back()->withErrors([
                'error' => 'Cette facture a déjà été encaissée et retirée.',
            ]);
        }

        $facture->update([
            'statut' => 'paye_retire',
            'paye_at' => now(),
        ]);

        return back()->with('status', 'Paiement de '.number_format((float) $facture->montant_total, 2, ',', ' ').' FCFA encaissé avec succès pour le ticket '.$facture->num_ticket.'. Vêtements restitués au client.');
    }
}
