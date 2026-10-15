<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laveur;
use App\Models\Pressing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaveurController extends Controller
{
    /**
     * Display a listing of washers.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $pressingId = $request->query('pressing_id');
        $selectedMois = $request->query('mois', date('Y-m'));

        if (! preg_match('/^\d{4}-\d{2}$/', (string) $selectedMois)) {
            $selectedMois = date('Y-m');
        }

        [$year, $month] = explode('-', $selectedMois);

        $laveurs = Laveur::query()
            ->with('pressing')
            ->when($search, function ($q, $search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%");
            })
            ->when($pressingId, function ($q, $pressingId) {
                $q->where('pressing_id', $pressingId);
            })
            ->withCount('factures')
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        $recapLaveurs = Laveur::query()
            ->with(['pressing', 'factures' => function ($q) use ($year, $month) {
                $q->whereYear('created_at', (int) $year)
                    ->whereMonth('created_at', (int) $month);
            }])
            ->when($pressingId, fn ($q) => $q->where('pressing_id', $pressingId))
            ->where('actif', true)
            ->orderBy('nom')
            ->get()
            ->map(function (Laveur $laveur) {
                $nbLavages = $laveur->factures->count();
                $caGenere = (float) $laveur->factures->sum('montant_total');
                $commissionDue = (float) $laveur->factures->sum(fn ($f) => $laveur->calculerCommission($f));

                return [
                    'laveur' => $laveur,
                    'nb_lavages' => $nbLavages,
                    'ca_genere' => $caGenere,
                    'commission_due' => $commissionDue,
                    'part_station' => $caGenere - $commissionDue,
                ];
            });

        $totalMoisLavages = $recapLaveurs->sum('nb_lavages');
        $totalMoisCa = $recapLaveurs->sum('ca_genere');
        $totalMoisCommissions = $recapLaveurs->sum('commission_due');
        $totalMoisPartStation = $totalMoisCa - $totalMoisCommissions;

        $pressings = Pressing::orderBy('type_activite')->orderBy('nom')->get();

        return view('admin.laveurs.index', [
            'laveurs' => $laveurs,
            'pressings' => $pressings,
            'search' => $search,
            'selectedPressingId' => $pressingId,
            'selectedMois' => $selectedMois,
            'recapLaveurs' => $recapLaveurs,
            'totalMoisLavages' => $totalMoisLavages,
            'totalMoisCa' => $totalMoisCa,
            'totalMoisCommissions' => $totalMoisCommissions,
            'totalMoisPartStation' => $totalMoisPartStation,
        ]);
    }

    /**
     * Show the form for creating a new washer.
     */
    public function create(): View
    {
        $pressings = Pressing::orderByRaw("CASE WHEN type_activite = 'lavage' THEN 0 ELSE 1 END")->orderBy('nom')->get();

        return view('admin.laveurs.create', [
            'pressings' => $pressings,
        ]);
    }

    /**
     * Store a newly created washer.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pressing_id' => ['required', 'exists:pressings,id'],
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'type_remuneration' => ['required', 'string', 'in:pourcentage,forfait'],
            'valeur_remuneration' => ['required', 'numeric', 'min:0'],
            'actif' => ['boolean'],
        ]);

        $validated['actif'] = $request->boolean('actif', true);

        Laveur::create($validated);

        return redirect()->route('admin.laveurs.index')
            ->with('status', 'Laveur enregistré avec succès.');
    }

    /**
     * Show the form for editing the washer.
     */
    public function edit(Laveur $laveur): View
    {
        $pressings = Pressing::orderBy('nom')->get();

        return view('admin.laveurs.edit', [
            'laveur' => $laveur,
            'pressings' => $pressings,
        ]);
    }

    /**
     * Update the washer.
     */
    public function update(Request $request, Laveur $laveur): RedirectResponse
    {
        $validated = $request->validate([
            'pressing_id' => ['required', 'exists:pressings,id'],
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'type_remuneration' => ['required', 'string', 'in:pourcentage,forfait'],
            'valeur_remuneration' => ['required', 'numeric', 'min:0'],
            'actif' => ['boolean'],
        ]);

        $validated['actif'] = $request->boolean('actif', true);

        $laveur->update($validated);

        return redirect()->route('admin.laveurs.index')
            ->with('status', 'Laveur mis à jour avec succès.');
    }

    /**
     * Delete the washer.
     */
    public function destroy(Laveur $laveur): RedirectResponse
    {
        if ($laveur->factures()->exists()) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer ce laveur car des lavages lui sont rattachés. Vous pouvez plutôt désactiver son compte.',
            ]);
        }

        $laveur->delete();

        return redirect()->route('admin.laveurs.index')
            ->with('status', 'Laveur supprimé.');
    }
}
