@extends('layouts.app', ['title' => 'Catalogue des Prestations'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900 dark:text-white">Catalogue des Prestations</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-full">
                    {{ $services->total() }} service(s)
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Grille tarifaire des prestations de blanchisserie et de lavage auto/moto par agence (Devise : FCFA)
            </p>
        </div>
        <div>
            <a href="{{ route('admin.services.create') }}" class="px-4 py-2.5 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-950 text-xs font-bold rounded-xl shadow-md transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4 text-white dark:text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Ajouter une prestation</span>
            </a>
        </div>
    </div>

    <!-- Filters & Activity Tabs (Balanced Full Width) -->
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Quick Activity Tabs -->
        <div class="flex items-center gap-1.5 p-1 bg-zinc-100 dark:bg-zinc-950 rounded-xl text-xs border border-zinc-200 dark:border-zinc-800 shrink-0">
            <a href="{{ route('admin.services.index', array_filter(['q' => $search, 'pressing_id' => $selectedPressingId])) }}"
               class="px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1.5 {{ empty($selectedType) ? 'bg-white dark:bg-zinc-800 text-black dark:text-white shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white' }}">
                <span>Toutes</span>
                <span class="text-[10px] font-mono opacity-70">({{ $countTotal }})</span>
            </a>
            <a href="{{ route('admin.services.index', array_filter(['type_activite' => 'pressing', 'q' => $search, 'pressing_id' => $selectedPressingId])) }}"
               class="px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1.5 {{ $selectedType === 'pressing' ? 'bg-white dark:bg-zinc-800 text-black dark:text-white shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white' }}">
                <span>Pressing</span>
                <span class="text-[10px] font-mono opacity-70">({{ $countPressing }})</span>
            </a>
            <a href="{{ route('admin.services.index', array_filter(['type_activite' => 'lavage', 'q' => $search, 'pressing_id' => $selectedPressingId])) }}"
               class="px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1.5 {{ $selectedType === 'lavage' ? 'bg-white dark:bg-zinc-800 text-black dark:text-white shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white' }}">
                <span>Lavage Auto/Moto</span>
                <span class="text-[10px] font-mono opacity-70">({{ $countLavage }})</span>
            </a>
        </div>

        <!-- Search & Agency Filters on Right -->
        <form method="GET" action="{{ route('admin.services.index') }}" class="flex flex-col sm:flex-row flex-1 lg:justify-end gap-2.5">
            @if($selectedType && !$selectedPressingId)
                <input type="hidden" name="type_activite" value="{{ $selectedType }}">
            @endif

            <div class="relative flex-1 sm:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Recherche par prestation (ex: Chemise, Moto)..."
                    class="w-full pl-9 pr-4 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                >
            </div>

            <div class="sm:max-w-xs">
                <select
                    name="pressing_id"
                    onchange="this.form.submit()"
                    class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition cursor-pointer"
                >
                    <option value="">Toutes les agences</option>
                    @if($pressings->where('type_activite', '!=', 'lavage')->isNotEmpty())
                        <optgroup label="Agences Pressing">
                            @foreach($pressings->where('type_activite', '!=', 'lavage') as $p)
                                <option value="{{ $p->id }}" {{ (string)$selectedPressingId === (string)$p->id ? 'selected' : '' }}>
                                    {{ $p->nom }} ({{ $p->ville }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                    @if($pressings->where('type_activite', 'lavage')->isNotEmpty())
                        <optgroup label="Stations de Lavage Auto/Moto">
                            @foreach($pressings->where('type_activite', 'lavage') as $p)
                                <option value="{{ $p->id }}" {{ (string)$selectedPressingId === (string)$p->id ? 'selected' : '' }}>
                                    {{ $p->nom }} ({{ $p->ville }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition shadow-xs">
                    Filtrer
                </button>
                @if($search || $selectedPressingId || $selectedType)
                    <a href="{{ route('admin.services.index') }}" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-semibold transition border border-zinc-200 dark:border-zinc-700">
                        Réinit.
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50/80 dark:bg-zinc-950/80 border-b border-zinc-100 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <th class="py-3.5 px-6">Désignation</th>
                        <th class="py-3.5 px-6">Établissement</th>
                        <th class="py-3.5 px-6 text-right">Prix Unitaire</th>
                        <th class="py-3.5 px-6 text-center">Commandes</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($services as $s)
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition duration-150">
                            <td class="py-4 px-6 font-bold text-zinc-900 dark:text-white">{{ $s->designation }}</td>
                            <td class="py-4 px-6 text-zinc-600 dark:text-zinc-300">
                                <span class="font-bold text-zinc-900 dark:text-white">{{ $s->pressing->nom }}</span>
                                <span class="text-zinc-400 dark:text-zinc-500 text-[11px]">({{ $s->pressing->isLavage() ? 'Lavage' : 'Pressing' }})</span>
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-zinc-900 dark:text-white">
                                {{ number_format((float) $s->prix_unitaire, 2, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 text-[11px] font-bold">
                                    {{ $s->ligne_factures_count }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.services.edit', $s) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition">
                                        Modifier
                                    </a>

                                    <form method="POST" action="{{ route('admin.services.destroy', $s) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            onclick="showConfirmModal({
                                                title: 'Supprimer la prestation',
                                                message: 'Confirmez-vous la suppression de la prestation « {{ addslashes($s->designation) }} » ?',
                                                confirmText: 'Supprimer',
                                                type: 'danger',
                                                form: this.closest('form')
                                            })"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-950 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-800 transition cursor-pointer"
                                        >
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-xs text-zinc-500 dark:text-zinc-400">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-400 flex items-center justify-center mx-auto text-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-zinc-700 dark:text-zinc-200">Aucune prestation trouvée</p>
                                    <a href="{{ route('admin.services.create') }}" class="inline-block px-3.5 py-1.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-950 rounded-xl text-xs font-bold">
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
            <div class="p-4 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
