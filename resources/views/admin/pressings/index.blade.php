@extends('layouts.app', ['title' => 'Gestion des Pressings'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-extrabold tracking-tight text-white">Gestion des Agences</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-zinc-900 text-zinc-300 border border-zinc-700/80 rounded-full">
                    {{ $pressings->total() }} agence(s)
                </span>
            </div>
            <p class="text-xs text-zinc-400 mt-1">
                Administration des points de vente et filiales du réseau
            </p>
        </div>
        <div>
            <a href="{{ route('admin.pressings.create') }}" class="px-4 py-2.5 bg-white hover:bg-zinc-200 text-zinc-950 text-xs font-bold rounded-xl shadow-md transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Ajouter une agence</span>
            </a>
        </div>
    </div>

    <!-- Search form -->
    <div class="bg-zinc-900 p-4 rounded-2xl border border-zinc-800 shadow-xs">
        <form method="GET" action="{{ route('admin.pressings.index') }}" class="flex flex-col sm:flex-row max-w-md gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Rechercher par nom, ville, quartier..."
                    class="w-full pl-9 pr-4 py-2 bg-zinc-950 border border-zinc-800 rounded-xl text-xs text-white focus:outline-none focus:border-white transition placeholder:text-zinc-600"
                >
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-white hover:bg-zinc-200 text-zinc-950 text-xs font-bold rounded-xl transition shadow-xs">
                    Filtrer
                </button>
                @if($search)
                    <a href="{{ route('admin.pressings.index') }}" class="px-3 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded-xl text-xs font-semibold transition border border-zinc-700">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-zinc-900 rounded-2xl border border-zinc-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-950/80 border-b border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-400 font-semibold">
                        <th class="py-3.5 px-6">Nom de l'agence</th>
                        <th class="py-3.5 px-6">Ville</th>
                        <th class="py-3.5 px-6">Quartier</th>
                        <th class="py-3.5 px-6">Téléphone</th>
                        <th class="py-3.5 px-6 text-center">Personnel</th>
                        <th class="py-3.5 px-6 text-center">Prestations</th>
                        <th class="py-3.5 px-6 text-center">Factures</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($pressings as $p)
                        <tr class="hover:bg-zinc-800/40 transition duration-150">
                            <td class="py-4 px-6 font-bold text-white">{{ $p->nom }}</td>
                            <td class="py-4 px-6 text-zinc-300">{{ $p->ville }}</td>
                            <td class="py-4 px-6 text-zinc-300">{{ $p->quartier }}</td>
                            <td class="py-4 px-6 font-mono text-zinc-400">{{ $p->telephone ?? '—' }}</td>
                            <td class="py-4 px-6 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-200 border border-zinc-700 text-[11px] font-bold">
                                    {{ $p->users_count }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-200 border border-zinc-700 text-[11px] font-bold">
                                    {{ $p->services_count }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center font-mono font-bold text-white">
                                {{ $p->factures_count }}
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.pressings.edit', $p) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 transition">
                                    Modifier
                                </a>

                                <form method="POST" action="{{ route('admin.pressings.destroy', $p) }}" class="inline-block" onsubmit="return confirm('Confirmer la suppression de cette agence ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-950 hover:bg-zinc-800 text-zinc-400 hover:text-white border border-zinc-800 transition cursor-pointer">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-xs text-zinc-400">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-10 h-10 rounded-full bg-zinc-800 text-zinc-400 flex items-center justify-center mx-auto text-base">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-zinc-200">Aucun pressing trouvé</p>
                                    <a href="{{ route('admin.pressings.create') }}" class="inline-block px-3.5 py-1.5 bg-white text-zinc-950 rounded-xl text-xs font-bold">
                                        + Ajouter une agence
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pressings->hasPages())
            <div class="p-4 border-t border-zinc-800 bg-zinc-900">
                {{ $pressings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
