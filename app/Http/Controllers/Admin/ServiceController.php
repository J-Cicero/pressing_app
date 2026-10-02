<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pressing;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $pressingId = $request->query('pressing_id');

        $services = Service::query()
            ->with('pressing')
            ->withCount('ligneFactures')
            ->when($search, function ($query, $search) {
                $query->where('designation', 'like', "%{$search}%");
            })
            ->when($pressingId, function ($query, $pressingId) {
                $query->where('pressing_id', $pressingId);
            })
            ->orderBy('designation')
            ->paginate(15)
            ->withQueryString();

        $pressings = Pressing::orderBy('nom')->get();

        return view('admin.services.index', [
            'services' => $services,
            'pressings' => $pressings,
            'search' => $search,
            'selectedPressingId' => $pressingId,
        ]);
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View
    {
        $pressings = Pressing::orderBy('nom')->get();

        return view('admin.services.create', [
            'pressings' => $pressings,
        ]);
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pressing_id' => ['required', 'exists:pressings,id'],
            'designation' => ['required', 'string', 'max:255'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
        ]);

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('status', 'Prestation ajoutée au catalogue avec succès.');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service): View
    {
        $pressings = Pressing::orderBy('nom')->get();

        return view('admin.services.edit', [
            'service' => $service,
            'pressings' => $pressings,
        ]);
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'pressing_id' => ['required', 'exists:pressings,id'],
            'designation' => ['required', 'string', 'max:255'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
        ]);

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('status', 'Prestation mise à jour avec succès.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        if ($service->ligneFactures()->exists()) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer cette prestation car elle est référencée dans des factures.',
            ]);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('status', 'Prestation supprimée avec succès.');
    }
}
