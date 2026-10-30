<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depense;
use App\Models\Facture;
use App\Models\Laveur;
use App\Models\PaiementLaveur;
use App\Models\Pressing;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComptabiliteController extends Controller
{
    /**
     * Affiche le tableau de bord comptable (Revenus vs Dépenses par agence et par mois) côté Admin.
     */
    public function index(Request $request): View
    {
        $mois = (string) $request->query('mois', Carbon::today()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $mois)) {
            $mois = Carbon::today()->format('Y-m');
        }

        [$year, $month] = array_map('intval', explode('-', $mois));
        $selectedPressingId = $request->query('pressing_id');
        $selectedCategorie = $request->query('categorie');
        $search = trim((string) $request->query('q', ''));

        $pressings = Pressing::orderBy('type_activite')->orderBy('nom')->get();
        $pressingsMap = $pressings->keyBy('id');

        // Calcul du bilan financier mensuel par agence (sans relation HasMany/BelongsToMany)
        $bilanAgences = [];
        $totalRevenusMois = 0.0;
        $totalDepensesMois = 0.0;
        $totalCommissionsMois = 0.0;

        foreach ($pressings as $p) {
            $revenu = (float) Facture::where('pressing_id', $p->id)
                ->where('statut', 'paye_retire')
                ->whereYear('paye_at', $year)
                ->whereMonth('paye_at', $month)
                ->sum('montant_total');

            $depensesAgence = (float) Depense::where('pressing_id', $p->id)
                ->whereYear('date_depense', $year)
                ->whereMonth('date_depense', $month)
                ->sum('montant');

            $laveurIds = Laveur::where('pressing_id', $p->id)->pluck('id')->all();
            $commissionsLaveurs = ! empty($laveurIds)
                ? (float) PaiementLaveur::whereIn('laveur_id', $laveurIds)
                    ->where('mois', $mois)
                    ->where('statut', 'paye')
                    ->sum('montant_paye')
                : 0.0;

            $nbDepenses = Depense::where('pressing_id', $p->id)
                ->whereYear('date_depense', $year)
                ->whereMonth('date_depense', $month)
                ->count();

            $resultatNet = $revenu - $depensesAgence - $commissionsLaveurs;

            $bilanAgences[] = [
                'pressing' => $p,
                'revenu' => $revenu,
                'depenses' => $depensesAgence,
                'commissions' => $commissionsLaveurs,
                'nb_depenses' => $nbDepenses,
                'resultat_net' => $resultatNet,
            ];

            $totalRevenusMois += $revenu;
            $totalDepensesMois += $depensesAgence;
            $totalCommissionsMois += $commissionsLaveurs;
        }

        $resultatNetGlobal = $totalRevenusMois - $totalDepensesMois - $totalCommissionsMois;

        // Liste paginée des dépenses du mois
        $depenses = Depense::query()
            ->whereYear('date_depense', $year)
            ->whereMonth('date_depense', $month)
            ->when($selectedPressingId, fn ($q) => $q->where('pressing_id', $selectedPressingId))
            ->when($selectedCategorie, fn ($q) => $q->where('categorie', $selectedCategorie))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('libelle', 'like', "%{$search}%")
                        ->orWhere('justificatif', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('date_depense')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $userIds = $depenses->pluck('user_id')->filter()->unique()->values()->all();
        $usersMap = ! empty($userIds)
            ? User::whereIn('id', $userIds)->get()->keyBy('id')
            : collect();

        return view('admin.comptabilite.index', [
            'mois' => $mois,
            'pressings' => $pressings,
            'pressingsMap' => $pressingsMap,
            'usersMap' => $usersMap,
            'bilanAgences' => $bilanAgences,
            'totalRevenusMois' => $totalRevenusMois,
            'totalDepensesMois' => $totalDepensesMois,
            'totalCommissionsMois' => $totalCommissionsMois,
            'resultatNetGlobal' => $resultatNetGlobal,
            'depenses' => $depenses,
            'categories' => Depense::CATEGORIES,
            'selectedPressingId' => $selectedPressingId,
            'selectedCategorie' => $selectedCategorie,
            'search' => $search,
        ]);
    }

    /**
     * Enregistre une nouvelle dépense d'agence.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pressing_id' => ['required', 'exists:pressings,id'],
            'categorie' => ['required', 'string', Rule::in(array_keys(Depense::CATEGORIES))],
            'libelle' => ['required', 'string', 'max:255'],
            'montant' => ['required', 'numeric', 'min:1'],
            'date_depense' => ['required', 'date'],
            'justificatif' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['user_id'] = Auth::id();

        $depense = Depense::create($validated);
        $mois = $depense->date_depense->format('Y-m');

        return redirect()->route('admin.comptabilite.index', ['mois' => $mois])
            ->with('status', 'Dépense enregistrée avec succès.');
    }

    /**
     * Met à jour une dépense existante identifiée par son UUID.
     */
    public function update(Request $request, Depense $depense): RedirectResponse
    {
        $validated = $request->validate([
            'pressing_id' => ['required', 'exists:pressings,id'],
            'categorie' => ['required', 'string', Rule::in(array_keys(Depense::CATEGORIES))],
            'libelle' => ['required', 'string', 'max:255'],
            'montant' => ['required', 'numeric', 'min:1'],
            'date_depense' => ['required', 'date'],
            'justificatif' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $depense->update($validated);
        $mois = $depense->date_depense->format('Y-m');

        return redirect()->route('admin.comptabilite.index', ['mois' => $mois])
            ->with('status', 'Dépense mise à jour avec succès.');
    }

    /**
     * Supprime une dépense identifiée par son UUID.
     */
    public function destroy(Depense $depense): RedirectResponse
    {
        $mois = $depense->date_depense ? $depense->date_depense->format('Y-m') : Carbon::today()->format('Y-m');
        $depense->delete();

        return redirect()->route('admin.comptabilite.index', ['mois' => $mois])
            ->with('status', 'Dépense supprimée.');
    }
}
