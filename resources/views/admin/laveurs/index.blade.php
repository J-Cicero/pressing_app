@extends('layouts.app', ['title' => 'Gestion des Laveurs'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white">Personnel de Terrain (Laveurs)</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-full">
                    {{ $laveurs->total() }} laveur(s)
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Suivi des techniciens de lavage, configuration des commissions et calculs journaliers
            </p>
        </div>
        <div>
            <a href="{{ route('admin.laveurs.create') }}" class="px-4 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-xs transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Ajouter un laveur</span>
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl text-xs font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs">
        <form method="GET" action="{{ route('admin.laveurs.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1 max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Rechercher par nom ou téléphone..."
                    class="w-full pl-9 pr-4 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                >
            </div>

            <div class="w-full sm:w-64">
                <select
                    name="pressing_id"
                    class="w-full px-3.5 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                >
                    <option value="">Toutes les agences</option>
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ $selectedPressingId == $p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->type_activite }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition shadow-xs">
                    Filtrer
                </button>
                @if($search || $selectedPressingId)
                    <a href="{{ route('admin.laveurs.index') }}" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-semibold transition border border-zinc-200 dark:border-zinc-700">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <th class="py-3.5 px-6">Nom</th>
                        <th class="py-3.5 px-6">Établissement</th>
                        <th class="py-3.5 px-6">Téléphone</th>
                        <th class="py-3.5 px-6">Mode de rémunération</th>
                        <th class="py-3.5 px-6">Taux / Montant</th>
                        <th class="py-3.5 px-6 text-center">Lavages traités</th>
                        <th class="py-3.5 px-6 text-center">Statut</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                    @forelse($laveurs as $laveur)
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition duration-150">
                            <td class="py-4 px-6 font-bold text-zinc-950 dark:text-white">
                                {{ $laveur->nom }}
                            </td>
                            <td class="py-4 px-6 font-medium text-zinc-800 dark:text-zinc-300">
                                {{ $laveur->pressing->nom ?? '—' }}
                            </td>
                            <td class="py-4 px-6 font-mono text-zinc-600 dark:text-zinc-400">
                                {{ $laveur->telephone ?? '—' }}
                            </td>
                            <td class="py-4 px-6 font-medium text-zinc-800 dark:text-zinc-200">
                                @if($laveur->type_remuneration === 'pourcentage')
                                    Pourcentage par lavage
                                @else
                                    Forfait fixe par véhicule
                                @endif
                            </td>
                            <td class="py-4 px-6 font-mono font-bold text-zinc-900 dark:text-zinc-100">
                                @if($laveur->type_remuneration === 'pourcentage')
                                    {{ number_format($laveur->valeur_remuneration, 0) }}%
                                @else
                                    {{ number_format($laveur->valeur_remuneration, 0, ',', ' ') }} FCFA
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 text-[11px] font-bold">
                                    {{ $laveur->factures_count }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($laveur->actif)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-black">
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                        Inactif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.laveurs.edit', $laveur) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition">
                                    Modifier
                                </a>

                                <form method="POST" action="{{ route('admin.laveurs.destroy', $laveur) }}" class="inline-block" onsubmit="return confirm('Confirmer la suppression de ce laveur ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 transition">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                        <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Aucun laveur enregistré</p>
                                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500">Ajoutez le personnel de terrain de vos stations de lavage pour suivre leurs commissions.</p>
                                    <a href="{{ route('admin.laveurs.create') }}" class="inline-flex items-center px-3.5 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition">
                                        Ajouter un laveur
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laveurs->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $laveurs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
