<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketPrintController extends Controller
{
    /**
     * Display the thermal 80mm printable ticket view.
     */
    public function show(Facture $facture): View
    {
        $user = Auth::user();

        // Contrôle d'isolation d'agence (sauf si Super Admin)
        if (! $user->isAdmin() && $facture->pressing_id !== $user->pressing_id) {
            abort(403, 'Accès non autorisé : cette facture n\'appartient pas à votre agence.');
        }

        $facture->load(['pressing', 'user', 'ligneFactures.service']);

        return view('caisse.print', [
            'facture' => $facture,
        ]);
    }
}
