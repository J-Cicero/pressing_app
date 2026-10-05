@extends('layouts.app', ['title' => 'Super Admin - Tableau de bord'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header & Temporal Filter -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">ESPACE SUPER ADMIN</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200/80 rounded-full">
                    Supervision Global
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Consolidation financière en temps réel & contrôle des agences de pressing
            </p>
        </div>

        <!-- Temporal Filter Tabs -->
        <div class="flex items-center bg-slate-200/70 p-1 rounded-2xl text-xs shadow-xs">
            <span class="text-xs font-medium text-slate-600 px-3 hidden sm:inline">Période :</span>
            <a href="{{ route('admin.dashboard', ['periode' => 'jour']) }}"
               class="px-3.5 py-1.5 font-semibold rounded-xl transition duration-150 {{ $periode === 'jour' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                Aujourd'hui
            </a>
            <a href="{{ route('admin.dashboard', ['periode' => 'mois']) }}"
               class="px-3.5 py-1.5 font-semibold rounded-xl transition duration-150 {{ $periode === 'mois' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                Ce mois
            </a>
            <a href="{{ route('admin.dashboard', ['periode' => 'tout']) }}"
               class="px-3.5 py-1.5 font-semibold rounded-xl transition duration-150 {{ $periode === 'tout' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                Historique global
            </a>
        </div>
    </div>

    <!-- Financial KPI Cards (Real DB Aggregates) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- CA du Jour -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-bl-full pointer-events-none"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">CA du Jour</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Encaissé
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-3xl font-extrabold tracking-tight text-slate-900 font-mono">
                    {{ number_format($caJour, 2, ',', ' ') }}
                </span>
                <span class="text-xs font-semibold text-slate-500">FCFA</span>
            </div>
            <div class="mt-2 text-[11px] text-slate-500 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Paiement 100% perçu au retrait
            </div>
        </div>

        <!-- CA du Mois -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-bl-full pointer-events-none"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">CA du Mois</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ date('M Y') }}
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-3xl font-extrabold tracking-tight text-slate-900 font-mono">
                    {{ number_format($caMois, 2, ',', ' ') }}
                </span>
                <span class="text-xs font-semibold text-slate-500">FCFA</span>
            </div>
            <div class="mt-2 text-[11px] text-slate-500 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                Recette mensuelle en cours
            </div>
        </div>

        <!-- Total Impayés / En Attente -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/5 rounded-bl-full pointer-events-none"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Impayés</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                    En attente
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-3xl font-extrabold tracking-tight text-slate-900 font-mono">
                    {{ number_format($totalImpayes, 2, ',', ' ') }}
                </span>
                <span class="text-xs font-semibold text-slate-500">FCFA</span>
            </div>
            <div class="mt-2 text-[11px] text-slate-500">
                <span class="font-semibold text-slate-700 font-mono">{{ $ticketsEnAttente }}</span> ticket(s) déposé(s) non retiré(s)
            </div>
        </div>

        <!-- Total Volume Tickets -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-slate-500/5 rounded-bl-full pointer-events-none"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Volume Tickets</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                    Global
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-3xl font-extrabold tracking-tight text-slate-900 font-mono">
                    {{ $totalTickets }}
                </span>
                <span class="text-xs font-semibold text-slate-500">tickets</span>
            </div>
            <div class="mt-2 text-[11px] text-slate-500">
                <span class="text-emerald-600 font-semibold">{{ $ticketsPayes }} payés</span> &bull; <span class="text-amber-600 font-semibold">{{ $ticketsEnAttente }} en cours</span>
            </div>
        </div>
    </div>

    <!-- Comparative Table by Agency -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Tableau comparatif dynamique par agence</h2>
                <p class="text-xs text-slate-500 mt-0.5">Données d'activité et chiffre d'affaires consolidés par point de vente</p>
            </div>
            <a href="{{ route('admin.pressings.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/20 transition gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouvelle agence</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="py-3.5 px-6">Pressing</th>
                        <th class="py-3.5 px-6">Localisation</th>
                        <th class="py-3.5 px-6 text-center">Personnel</th>
                        <th class="py-3.5 px-6 text-center">Tickets Émis</th>
                        <th class="py-3.5 px-6 text-right">Impayés / En cours</th>
                        <th class="py-3.5 px-6 text-right">CA Encaissé Ce Mois</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pressings as $p)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <td class="py-4 px-6">
                                <a href="{{ route('admin.pressings.edit', $p) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                    {{ $p->nom }}
                                </a>
                                @if($p->telephone)
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">📞 {{ $p->telephone }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                <span class="font-medium text-slate-800">{{ $p->ville }}</span>
                                <span class="text-slate-400">&bull; {{ $p->quartier }}</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-mono font-semibold text-[11px]">
                                    {{ $p->total_personnel }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center font-mono font-bold text-slate-900">
                                {{ $p->total_tickets }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono text-slate-600">
                                {{ number_format((float) ($p->total_impayes ?? 0), 2, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-extrabold text-slate-900">
                                {{ number_format((float) ($p->ca_mois ?? 0), 2, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-slate-500">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                                        🏢
                                    </div>
                                    <p class="font-medium text-slate-700">Aucune agence de pressing enregistrée</p>
                                    <p class="text-[11px] text-slate-400">Commencez par ajouter votre premier établissement.</p>
                                    <a href="{{ route('admin.pressings.create') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
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
        <a href="{{ route('admin.pressings.index') }}" class="p-5 bg-white rounded-2xl border border-slate-200/80 hover:border-indigo-500 hover:shadow-lg hover:shadow-indigo-500/5 transition duration-200 group block">
            <div class="flex items-center justify-between text-indigo-600 mb-2">
                <span class="text-xl">🏢</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </div>
            <div class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">Gérer les Agences</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $pressings->count() }} agence(s) active(s)</div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="p-5 bg-white rounded-2xl border border-slate-200/80 hover:border-indigo-500 hover:shadow-lg hover:shadow-indigo-500/5 transition duration-200 group block">
            <div class="flex items-center justify-between text-indigo-600 mb-2">
                <span class="text-xl">👥</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </div>
            <div class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">Personnel & Caissiers</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Comptes et droits d'accès</div>
        </a>

        <a href="{{ route('admin.services.index') }}" class="p-5 bg-white rounded-2xl border border-slate-200/80 hover:border-indigo-500 hover:shadow-lg hover:shadow-indigo-500/5 transition duration-200 group block">
            <div class="flex items-center justify-between text-indigo-600 mb-2">
                <span class="text-xl">🏷️</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </div>
            <div class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">Grille Tarifaire</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Services de nettoyage et tarifs</div>
        </a>

        <a href="{{ route('admin.factures.index') }}" class="p-5 bg-white rounded-2xl border border-slate-200/80 hover:border-indigo-500 hover:shadow-lg hover:shadow-indigo-500/5 transition duration-200 group block">
            <div class="flex items-center justify-between text-indigo-600 mb-2">
                <span class="text-xl">🧾</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </div>
            <div class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">Recherche Factures</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Consultation par N° de ticket</div>
        </a>
    </div>
</div>
@endsection
