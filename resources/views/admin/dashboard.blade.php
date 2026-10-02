@extends('layouts.app', ['title' => 'Super Admin - Tableau de bord'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header & Temporal Filter -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-[#374151]/20 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#000]">ESPACE SUPER ADMIN</h1>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-1">
                Indicateurs financiers réels & supervision multi-agences
            </p>
        </div>

        <!-- Temporal Filter Tabs -->
        <div class="flex items-center space-x-1 bg-[#FFF] p-1 border border-[#374151]/20">
            <span class="text-xs uppercase font-semibold text-[#374151] px-2">Filtre temporel :</span>
            <a href="{{ route('admin.dashboard', ['periode' => 'jour']) }}"
               class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ $periode === 'jour' ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                Aujourd'hui
            </a>
            <a href="{{ route('admin.dashboard', ['periode' => 'mois']) }}"
               class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ $periode === 'mois' ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                Ce mois
            </a>
            <a href="{{ route('admin.dashboard', ['periode' => 'tout']) }}"
               class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition {{ $periode === 'tout' ? 'bg-[#000] text-[#FFF]' : 'text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6]' }}">
                Historique global
            </a>
        </div>
    </div>

    <!-- Financial KPI Cards (Real DB Aggregates) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 my-8">
        <!-- CA du Jour -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold uppercase tracking-wider text-[#374151]">CA du Jour</span>
                <span class="text-[10px] uppercase font-mono px-1.5 py-0.5 bg-[#F3F4F6] text-[#374151] border border-[#374151]/20">Payé / Retiré</span>
            </div>
            <div class="mt-4 text-3xl font-extrabold tracking-tight text-[#000] font-mono">
                {{ number_format($caJour, 2, ',', ' ') }} <span class="text-sm font-normal text-[#374151]">FCFA</span>
            </div>
            <div class="mt-2 text-xs text-[#374151]">
                Encaissé aujourd'hui (100% au retrait)
            </div>
        </div>

        <!-- CA du Mois -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold uppercase tracking-wider text-[#374151]">CA du Mois</span>
                <span class="text-[10px] uppercase font-mono px-1.5 py-0.5 bg-[#000] text-[#FFF]">{{ date('M Y') }}</span>
            </div>
            <div class="mt-4 text-3xl font-extrabold tracking-tight text-[#000] font-mono">
                {{ number_format($caMois, 2, ',', ' ') }} <span class="text-sm font-normal text-[#374151]">FCFA</span>
            </div>
            <div class="mt-2 text-xs text-[#374151]">
                Encaissé sur le mois civil en cours
            </div>
        </div>

        <!-- Total Impayés / En Attente -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold uppercase tracking-wider text-[#374151]">Total Impayés</span>
                <span class="text-[10px] uppercase font-mono px-1.5 py-0.5 bg-[#F3F4F6] text-[#000] border border-[#374151]">En attente</span>
            </div>
            <div class="mt-4 text-3xl font-extrabold tracking-tight text-[#000] font-mono">
                {{ number_format($totalImpayes, 2, ',', ' ') }} <span class="text-sm font-normal text-[#374151]">FCFA</span>
            </div>
            <div class="mt-2 text-xs text-[#374151]">
                {{ $ticketsEnAttente }} ticket(s) déposé(s) ou prêt(s) non retiré(s)
            </div>
        </div>

        <!-- Total Volume Tickets -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold uppercase tracking-wider text-[#374151]">Volume Tickets</span>
                <span class="text-[10px] uppercase font-mono px-1.5 py-0.5 bg-[#F3F4F6] text-[#374151] border border-[#374151]/20">Global</span>
            </div>
            <div class="mt-4 text-3xl font-extrabold tracking-tight text-[#000] font-mono">
                {{ $totalTickets }}
            </div>
            <div class="mt-2 text-xs text-[#374151]">
                {{ $ticketsPayes }} retiré(s) • {{ $ticketsEnAttente }} en cours
            </div>
        </div>
    </div>

    <!-- Comparative Table by Agency (Deliverable 1 requirement) -->
    <div class="bg-[#FFF] border border-[#374151]/20 shadow-sm mt-8">
        <div class="px-6 py-4 border-b border-[#374151]/20 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#000]">
                    Tableau comparatif dynamique par agence
                </h2>
                <p class="text-xs text-[#374151] mt-0.5">
                    Données consolidées en temps réel par agence de pressing
                </p>
            </div>
            <a href="{{ route('admin.pressings.create') }}" class="inline-flex items-center px-3 py-1.5 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition">
                + Nouvelle agence
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F3F4F6] border-b border-[#374151]/20 text-[11px] uppercase tracking-wider text-[#374151]">
                        <th class="py-3 px-6 font-semibold">Nom du Pressing</th>
                        <th class="py-3 px-6 font-semibold">Ville</th>
                        <th class="py-3 px-6 font-semibold">Quartier</th>
                        <th class="py-3 px-6 font-semibold text-center">Personnel</th>
                        <th class="py-3 px-6 font-semibold text-center">Tickets Émis</th>
                        <th class="py-3 px-6 font-semibold text-right">Impayés / En cours</th>
                        <th class="py-3 px-6 font-semibold text-right">CA Encaissé Ce Mois</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#374151]/10 text-xs">
                    @forelse($pressings as $p)
                        <tr class="hover:bg-[#F3F4F6]/50 transition">
                            <td class="py-4 px-6 font-bold text-[#000]">
                                <a href="{{ route('admin.pressings.edit', $p) }}" class="hover:underline">
                                    {{ $p->nom }}
                                </a>
                                @if($p->telephone)
                                    <div class="text-[11px] text-[#374151] font-normal font-mono">{{ $p->telephone }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-[#374151]">{{ $p->ville }}</td>
                            <td class="py-4 px-6 text-[#374151]">{{ $p->quartier }}</td>
                            <td class="py-4 px-6 text-center font-mono">{{ $p->total_personnel }}</td>
                            <td class="py-4 px-6 text-center font-mono font-semibold text-[#000]">
                                {{ $p->total_tickets }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono text-[#374151]">
                                {{ number_format((float) ($p->total_impayes ?? 0), 2, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-[#000]">
                                {{ number_format((float) ($p->ca_mois ?? 0), 2, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-[#374151]">
                                Aucune agence de pressing enregistrée en base de données.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Navigation / Shortcuts -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
        <a href="{{ route('admin.pressings.index') }}" class="p-4 bg-[#FFF] border border-[#374151]/20 hover:border-[#000] transition block group">
            <div class="text-xs font-bold uppercase tracking-wider text-[#000] group-hover:underline">Gérer les Agences &rarr;</div>
            <div class="text-xs text-[#374151] mt-1">{{ $pressings->count() }} agence(s) enregistrée(s)</div>
        </a>
        <a href="{{ route('admin.users.index') }}" class="p-4 bg-[#FFF] border border-[#374151]/20 hover:border-[#000] transition block group">
            <div class="text-xs font-bold uppercase tracking-wider text-[#000] group-hover:underline">Gérer le Personnel &rarr;</div>
            <div class="text-xs text-[#374151] mt-1">Caissiers et comptes d'accès</div>
        </a>
        <a href="{{ route('admin.services.index') }}" class="p-4 bg-[#FFF] border border-[#374151]/20 hover:border-[#000] transition block group">
            <div class="text-xs font-bold uppercase tracking-wider text-[#000] group-hover:underline">Grille des Prestations &rarr;</div>
            <div class="text-xs text-[#374151] mt-1">Catalogue et tarifs par agence</div>
        </a>
        <a href="{{ route('admin.factures.index') }}" class="p-4 bg-[#FFF] border border-[#374151]/20 hover:border-[#000] transition block group">
            <div class="text-xs font-bold uppercase tracking-wider text-[#000] group-hover:underline">Consulter les Factures &rarr;</div>
            <div class="text-xs text-[#374151] mt-1">Recherche par numéro de ticket</div>
        </a>
    </div>
</div>
@endsection
