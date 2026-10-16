<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Mail\TicketPretMail;
use App\Models\Facture;
use App\Models\Laveur;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RetraitController extends Controller
{
    /**
     * Display the search and checkout / pickup interface with WhatsApp shortcuts.
     */
    public function index(Request $request, WhatsAppNotificationService $whatsApp): View
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence assignée.');
        }

        $search = $request->query('q');
        $statut = $request->query('statut');

        $query = Facture::where('pressing_id', $pressing->id)
            ->with(['ligneFactures.service', 'user', 'client', 'vehicule', 'laveur'])
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('num_ticket', 'like', "%{$search}%")
                        ->orWhere('client_telephone', 'like', "%{$search}%")
                        ->orWhere('client_nom', 'like', "%{$search}%")
                        ->orWhereHas('vehicule', fn ($v) => $v->where('immatriculation', 'like', "%{$search}%"));
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

        // Attach WhatsApp URL to each invoice for quick communication
        $factures->getCollection()->transform(function ($f) use ($whatsApp) {
            if ($f->statut === 'pret') {
                $f->whatsapp_url = $whatsApp->urlPret($f);
            } else {
                $f->whatsapp_url = $whatsApp->urlDepot($f);
            }

            return $f;
        });

        $laveurs = Laveur::where('pressing_id', $pressing->id)->where('actif', true)->orderBy('nom')->get();

        $viewName = $pressing->isLavage() ? 'caisse.lavage.retrait' : 'caisse.pressing.retrait';

        return view($viewName, [
            'pressing' => $pressing,
            'factures' => $factures,
            'laveurs' => $laveurs,
            'search' => $search,
            'selectedStatut' => $statut,
            'countDepose' => $countDepose,
            'countPret' => $countPret,
            'countPayeRetire' => $countPayeRetire,
        ]);
    }

    /**
     * Update ticket status from 'depose' to 'pret' (items/car washed & ready).
     */
    public function marquerPret(Facture $facture, WhatsAppNotificationService $whatsApp): RedirectResponse
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
        $facture->load(['pressing', 'vehicule', 'laveur', 'ligneFactures.service', 'client']);

        if (! empty($facture->client_email)) {
            try {
                Mail::to($facture->client_email)->send(new TicketPretMail($facture));
            } catch (\Throwable $e) {
                Log::error('Erreur lors de l\'envoi de l\'e-mail de prêt : '.$e->getMessage());
            }
        }

        $whatsappUrl = $whatsApp->urlPret($facture);
        $msgLaveur = $facture->laveur ? " (Opérateur/Laveur : {$facture->laveur->nom})" : '';

        return back()
            ->with('status', 'Le ticket '.$facture->num_ticket.$msgLaveur.' est désormais PRÊT pour le retrait.')
            ->with('whatsapp_pret_url', $whatsappUrl);
    }

    /**
     * Process 100% payment and collection ('pret' or 'depose' -> 'paye_retire').
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
        $facture->load(['pressing', 'vehicule', 'laveur', 'ligneFactures.service', 'client']);

        $msgLaveur = $facture->laveur
            ? " (Laveur : {$facture->laveur->nom} — Commission : ".number_format($facture->laveur->calculerCommission($facture), 0, ',', ' ').' FCFA)'
            : '';

        return back()->with('status', 'Paiement de '.number_format((float) $facture->montant_total, 2, ',', ' ').' FCFA encaissé avec succès pour le ticket '.$facture->num_ticket.'. Restitution effectuée'.$msgLaveur.'.');
    }

    /**
     * Assign or change the washer / operator for a ticket.
     */
    public function assignerLaveur(Request $request, Facture $facture): RedirectResponse
    {
        $user = Auth::user();

        if ($facture->pressing_id !== $user->pressing_id) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'laveur_id' => ['nullable', 'exists:laveurs,id'],
        ]);

        $facture->update([
            'laveur_id' => $validated['laveur_id'] ?? null,
        ]);
        $facture->load('laveur');

        $nom = $facture->laveur ? $facture->laveur->nom : 'Aucun';

        return back()->with('status', "Personnel / Laveur assigné au ticket {$facture->num_ticket} : {$nom}.");
    }
}
