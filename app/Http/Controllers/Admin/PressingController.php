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
     * Display a listing of the pressings.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $pressings = Pressing::query()
            ->when($search, function ($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                    ->orWhere('ville', 'like', "%{$search}%")
                    ->orWhere('quartier', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%");
            })
            ->withCount(['users', 'services', 'factures'])
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pressings.index', [
            'pressings' => $pressings,
            'search' => $search,
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
        ]);

        Pressing::create($validated);

        return redirect()->route('admin.pressings.index')
            ->with('status', 'Agence de pressing créée avec succès.');
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
        ]);

        $pressing->update($validated);

        return redirect()->route('admin.pressings.index')
            ->with('status', 'Agence de pressing mise à jour avec succès.');
    }

    /**
     * Remove the specified pressing from storage.
     */
    public function destroy(Pressing $pressing): RedirectResponse
    {
        if ($pressing->factures()->exists()) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer cette agence car elle possède des factures enregistrées.',
            ]);
        }

        $pressing->delete();

        return redirect()->route('admin.pressings.index')
            ->with('status', 'Agence supprimée avec succès.');
    }
}
