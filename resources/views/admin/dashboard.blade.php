@extends('layouts.app', ['title' => 'Super Admin - Tableau de bord'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- Header & Temporal Filter -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-5 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold tracking-tight text-slate-900">ESPACE SUPER ADMIN</h1>
                <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200/80 rounded-full">
                    @if($periode === 'jour')
                        Filtre : Aujourd'hui
                    @elseif($periode === 'tout')
                        Filtre : Historique Global
                    @else
                        Filtre : Ce Mois
                    @endif
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Consolidation financière en temps réel & contrôle des agences de pressing
            </p>
        </div>

        <!-- Temporal Filter Tabs -->
        <div class="flex items-center bg-slate-200/70 p-1 rounded-xl text-xs shadow-xs">
            <span class="text-xs font-medium text-slate-600 px-2.5 hidden sm:inline">Période :</span>
            <a href="{{ route('admin.dashboard', ['periode' => 'jour']) }}"
               class="px-3 py-1 font-semibold rounded-lg transition duration-150 {{ $periode === 'jour' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                Aujourd'hui
            </a>
            <a href="{{ route('admin.dashboard', ['periode' => 'mois']) }}"
               class="px-3 py-1 font-semibold rounded-lg transition duration-150 {{ $periode === 'mois' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                Ce mois
            </a>
            <a href="{{ route('admin.dashboard', ['periode' => 'tout']) }}"
               class="px-3 py-1 font-semibold rounded-lg transition duration-150 {{ $periode === 'tout' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                Historique global
            </a>
        </div>
    </div>

    <!-- Financial KPI Cards (Real DB Aggregates) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- CA du Jour -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">CA du Jour</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Aujourd'hui
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-slate-900 font-mono">
                    {{ number_format($caJour, 2, ',', ' ') }}
                </span>
                <span class="text-xs font-semibold text-slate-500">FCFA</span>
            </div>
            <div class="mt-1.5 text-[11px] text-slate-500 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Paiement 100% perçu au retrait
            </div>
        </div>

        <!-- CA du Mois / Filtré -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    @if($periode === 'jour')
                        CA Sélectionné
                    @elseif($periode === 'tout')
                        CA Cumulé Global
                    @else
                        CA du Mois
                    @endif
                </span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                    @if($periode === 'jour')
                        Jour
                    @elseif($periode === 'tout')
                        Global
                    @else
                        {{ date('M Y') }}
                    @endif
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-indigo-600 font-mono">
                    {{ number_format($caPeriode, 2, ',', ' ') }}
                </span>
                <span class="text-xs font-semibold text-slate-500">FCFA</span>
            </div>
            <div class="mt-1.5 text-[11px] text-slate-500 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                Recette selon filtre actif
            </div>
        </div>

        <!-- Total Impayés / En Attente -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Impayés</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                    En attente
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-slate-900 font-mono">
                    {{ number_format($totalImpayes, 2, ',', ' ') }}
                </span>
                <span class="text-xs font-semibold text-slate-500">FCFA</span>
            </div>
            <div class="mt-1.5 text-[11px] text-slate-500">
                <span class="font-semibold text-slate-700 font-mono">{{ $ticketsEnAttente }}</span> ticket(s) déposé(s) non retiré(s)
            </div>
        </div>

        <!-- Total Volume Tickets -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Volume Tickets</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                    Global
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-slate-900 font-mono">
                    {{ $totalTickets }}
                </span>
                <span class="text-xs font-semibold text-slate-500">tickets</span>
            </div>
            <div class="mt-1.5 text-[11px] text-slate-500">
                <span class="text-emerald-600 font-semibold">{{ $ticketsPayes }} payés</span> &bull; <span class="text-amber-600 font-semibold">{{ $ticketsEnAttente }} en cours</span>
            </div>
        </div>
    </div>

    <!-- Comparative Table by Agency -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Tableau comparatif dynamique par agence</h2>
                <p class="text-xs text-slate-500 mt-0.5">Données d'activité et chiffre d'affaires consolidés par point de vente</p>
            </div>
            <a href="{{ route('admin.pressings.create') }}" class="inline-flex items-center justify-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouvelle agence</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="py-3 px-5">Pressing</th>
                        <th class="py-3 px-5">Localisation</th>
                        <th class="py-3 px-5 text-center">Personnel</th>
                        <th class="py-3 px-5 text-center">Tickets Émis</th>
                        <th class="py-3 px-5 text-right">Impayés / En cours</th>
                        <th class="py-3 px-5 text-right">
                            @if($periode === 'jour')
                                CA Encaissé Aujourd'hui
                            @elseif($periode === 'tout')
                                CA Encaissé Global
                            @else
                                CA Encaissé Ce Mois
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pressings as $p)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <td class="py-3.5 px-5">
                                <a href="{{ route('admin.pressings.edit', $p) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                    {{ $p->nom }}
                                </a>
                                @if($p->telephone)
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">Tél: {{ $p->telephone }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-slate-600">
                                <span class="font-semibold text-slate-800">{{ $p->ville }}</span>
                                <span class="text-slate-400">&bull; {{ $p->quartier }}</span>
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-mono font-semibold text-[11px]">
                                    {{ $p->total_personnel }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-center font-mono font-bold text-slate-900">
                                {{ $p->total_tickets }}
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono text-slate-600">
                                {{ number_format((float) ($p->total_impayes ?? 0), 2, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-slate-900">
                                {{ number_format((float) ($p->ca_filtre ?? 0), 2, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-xs text-slate-500">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-base">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-slate-700">Aucune agence de pressing enregistrée</p>
                                    <a href="{{ route('admin.pressings.create') }}" class="inline-block px-3.5 py-1.5 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
                                        + Ajouter une agence
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Navigation Shortcuts Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.pressings.index') }}" class="p-4 bg-white rounded-xl border border-slate-200/80 hover:border-indigo-500 hover:shadow-md transition duration-200 group block">
            <div class="flex items-center justify-between text-indigo-600 mb-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9"/>
                </svg>
                <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </div>
            <div class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">Gérer les Agences</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $pressings->count() }} agence(s) active(s)</div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="p-4 bg-white rounded-xl border border-slate-200/80 hover:border-indigo-500 hover:shadow-md transition duration-200 group block">
            <div class="flex items-center justify-between text-indigo-600 mb-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </div>
            <div class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">Personnel & Caissiers</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Comptes et droits d'accès</div>
        </a>

        <a href="{{ route('admin.services.index') }}" class="p-4 bg-white rounded-xl border border-slate-200/80 hover:border-indigo-500 hover:shadow-md transition duration-200 group block">
            <div class="flex items-center justify-between text-indigo-600 mb-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </div>
            <div class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">Grille Tarifaire</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Services de nettoyage et tarifs</div>
        </a>

        <a href="{{ route('admin.factures.index') }}" class="p-4 bg-white rounded-xl border border-slate-200/80 hover:border-indigo-500 hover:shadow-md transition duration-200 group block">
            <div class="flex items-center justify-between text-indigo-600 mb-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </div>
            <div class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">Recherche Factures</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Consultation par N° de ticket</div>
        </a>
    </div>
</div>
@endsection
