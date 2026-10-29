<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pressing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PressingController extends Controller
{
    /**
     * Display a listing of the pressings / wash stations.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $type = $request->query('type_activite');

        $pressings = Pressing::query()
            ->when($search, function ($query, $search) {
                $escaped = addcslashes((string) $search, '%_\\');
                $query->where(function ($sub) use ($escaped) {
                    $sub->where('nom', 'like', "%{$escaped}%")
                        ->orWhere('ville', 'like', "%{$escaped}%")
                        ->orWhere('quartier', 'like', "%{$escaped}%")
                        ->orWhere('telephone', 'like', "%{$escaped}%");
                });
            })
            ->when($type, function ($query, $type) {
                $query->where('type_activite', $type);
            })
            ->withCount(['users', 'services', 'factures', 'laveurs'])
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pressings.index', [
            'pressings' => $pressings,
            'search' => $search,
            'selectedType' => $type,
        ]);
    }

    /**
     * Show the form for creating a new pressing.
     */
    public function create(): View
    {
        return view('admin.pressings.create');
    }

    /**
     * Store a newly created pressing in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:255'],
            'quartier' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'type_activite' => ['nullable', 'string', 'in:pressing,lavage'],
        ]);

        $validated['type_activite'] = $validated['type_activite'] ?? 'pressing';

        Pressing::create($validated);

        return redirect()->route('admin.pressings.index')
            ->with('status', 'Établissement créé avec succès.');
    }

    /**
     * Show the form for editing the specified pressing.
     */
    public function edit(Pressing $pressing): View
    {
        return view('admin.pressings.edit', [
            'pressing' => $pressing,
        ]);
    }

    /**
     * Update the specified pressing in storage.
     */
    public function update(Request $request, Pressing $pressing): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:255'],
            'quartier' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'type_activite' => ['nullable', 'string', 'in:pressing,lavage'],
        ]);

        $validated['type_activite'] = $validated['type_activite'] ?? ($pressing->type_activite ?? 'pressing');

        $pressing->update($validated);

        return redirect()->route('admin.pressings.index')
            ->with('status', 'Établissement mis à jour avec succès.');
    }

    /**
     * Remove the specified pressing from storage.
     */
    public function destroy(Pressing $pressing): RedirectResponse
    {
        if ($pressing->factures()->exists()) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer cet établissement car des factures y sont rattachées.',
            ]);
        }

        $pressing->delete();

        return redirect()->route('admin.pressings.index')
            ->with('status', 'Établissement supprimé avec succès.');
    }
}
