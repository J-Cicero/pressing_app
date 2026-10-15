<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\RegleFidelite;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a listing of clients with their visit counters.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $clients = Client::query()
            ->when($search, function ($q, $search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->withCount(['factures as total_factures', 'vehicules'])
            ->withCount(['factures as factures_payees' => function ($q) {
                $q->where('statut', 'paye_retire');
            }])
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.clients.index', [
            'clients' => $clients,
            'search' => $search,
        ]);
    }

    /**
     * Display client profile, vehicles, visits history and loyalty benefits.
     */
    public function show(Client $client, WhatsAppNotificationService $whatsApp): View
    {
        $client->load(['vehicules', 'factures' => fn ($q) => $q->latest('id')->with('pressing')]);

        $totalVisites = $client->totalVisites();
        $visitesPressing = $client->visitesPressing();
        $visitesLavage = $client->visitesLavage();

        $fideliteAtteinte = $client->fideliteAtteinte();
        $whatsappFideliteUrl = $fideliteAtteinte ? $whatsApp->urlFidelite($client, $fideliteAtteinte) : null;
        $prochaineRegle = $client->prochaineRegleFidelite();

        $toutesLesRegles = RegleFidelite::where('actif', true)
            ->orderBy('seuil_visites')
            ->get();

        return view('admin.clients.show', [
            'client' => $client,
            'totalVisites' => $totalVisites,
            'visitesPressing' => $visitesPressing,
            'visitesLavage' => $visitesLavage,
            'fideliteAtteinte' => $fideliteAtteinte,
            'whatsappFideliteUrl' => $whatsappFideliteUrl,
            'prochaineRegle' => $prochaineRegle,
            'toutesLesRegles' => $toutesLesRegles,
        ]);
    }
}
