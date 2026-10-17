<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegleFidelite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegleFideliteController extends Controller
{
    /**
     * Display a listing of loyalty rules.
     */
    public function index(): View
    {
        $regles = RegleFidelite::orderBy('seuil_visites')->paginate(15);

        return view('admin.fidelite.index', [
            'regles' => $regles,
        ]);
    }

    /**
     * Show the form for creating a new loyalty rule.
     */
    public function create(): View
    {
        return view('admin.fidelite.create');
    }

    /**
     * Store a newly created loyalty rule in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'seuil_visites' => ['required', 'integer', 'min:1'],
            'type_avantage' => ['required', 'string', 'in:pourcentage,montant_fixe,cadeau,service_offert'],
            'valeur_avantage' => ['nullable', 'numeric', 'min:0'],
            'nombre_utilisations' => ['nullable', 'integer', 'min:1', 'max:100'],
            'duree_jours' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'description' => ['required', 'string', 'max:255'],
            'type_activite' => ['required', 'string', 'in:pressing,lavage,global'],
            'actif' => ['boolean'],
        ]);

        $validated['nombre_utilisations'] = ! empty($validated['nombre_utilisations']) ? (int) $validated['nombre_utilisations'] : 1;
        $validated['actif'] = $request->boolean('actif', true);

        RegleFidelite::create($validated);

        return redirect()->route('admin.fidelite.index')
            ->with('status', 'Règle de fidélité enregistrée avec succès.');
    }

    /**
     * Show the form for editing the specified loyalty rule.
     */
    public function edit(RegleFidelite $regle): View
    {
        return view('admin.fidelite.edit', [
            'regle' => $regle,
        ]);
    }

    /**
     * Update the specified loyalty rule in storage.
     */
    public function update(Request $request, RegleFidelite $regle): RedirectResponse
    {
        $validated = $request->validate([
            'seuil_visites' => ['required', 'integer', 'min:1'],
            'type_avantage' => ['required', 'string', 'in:pourcentage,montant_fixe,cadeau,service_offert'],
            'valeur_avantage' => ['nullable', 'numeric', 'min:0'],
            'nombre_utilisations' => ['nullable', 'integer', 'min:1', 'max:100'],
            'duree_jours' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'description' => ['required', 'string', 'max:255'],
            'type_activite' => ['required', 'string', 'in:pressing,lavage,global'],
            'actif' => ['boolean'],
        ]);

        $validated['nombre_utilisations'] = ! empty($validated['nombre_utilisations']) ? (int) $validated['nombre_utilisations'] : 1;
        $validated['actif'] = $request->boolean('actif', true);

        $regle->update($validated);

        return redirect()->route('admin.fidelite.index')
            ->with('status', 'Règle de fidélité mise à jour.');
    }

    /**
     * Remove the specified loyalty rule from storage.
     */
    public function destroy(RegleFidelite $regle): RedirectResponse
    {
        $regle->delete();

        return redirect()->route('admin.fidelite.index')
            ->with('status', 'Règle de fidélité supprimée.');
    }
}
