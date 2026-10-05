@extends('layouts.app', ['title' => 'Catalogue des Prestations'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Catalogue des Prestations</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200/80 rounded-full">
                    {{ $services->total() }} service(s)
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Grille tarifaire des services de blanchisserie et nettoyage par agence
            </p>
        </div>
        <div>
            <a href="{{ route('admin.services.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Ajouter une prestation</span>
            </a>
        </div>
    </div>

    <!-- Filters form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.services.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-2xl">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Désignation du service..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                >
            </div>
            <div>
                <select name="pressing_id" class="w-full px-3 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition">
                    <option value="">Toutes les agences</option>
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ (string)$selectedPressingId === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->ville }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition flex-1 shadow-xs">
                    Filtrer
                </button>
                @if($search || $selectedPressingId)
                    <a href="{{ route('admin.services.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">
                        Réinit.
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="py-3.5 px-6">Désignation</th>
                        <th class="py-3.5 px-6">Agence Associée</th>
                        <th class="py-3.5 px-6 text-right">Prix Unitaire</th>
                        <th class="py-3.5 px-6 text-center">Utilisations</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($services as $s)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <td class="py-4 px-6 font-bold text-slate-900">{{ $s->designation }}</td>
                            <td class="py-4 px-6 text-slate-600">
                                <span class="font-semibold text-slate-900">{{ $s->pressing->nom }}</span>
                                <span class="text-slate-400 text-[11px]">({{ $s->pressing->ville }})</span>
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-slate-900">
                                {{ number_format((float) $s->prix_unitaire, 2, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-semibold">
                                    {{ $s->ligne_factures_count }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.services.edit', $s) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 transition">
                                    Modifier
                                </a>

                                <form method="POST" action="{{ route('admin.services.destroy', $s) }}" class="inline-block" onsubmit="return confirm('Confirmer la suppression de cette prestation ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 transition cursor-pointer">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-xs text-slate-500">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-lg">
                                        🏷️
                                    </div>
                                    <p class="font-medium text-slate-700">Aucune prestation trouvée</p>
                                    <a href="{{ route('admin.services.create') }}" class="inline-block px-3.5 py-1.5 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
                                        + Ajouter une prestation
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
            <div class="p-4 border-t border-slate-100 bg-white">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
