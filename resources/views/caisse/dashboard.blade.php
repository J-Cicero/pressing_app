@extends('layouts.app', ['title' => 'Espace Caisse - ' . $pressing->nom])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-extrabold tracking-tight text-white">Espace Guichet Caisse</h1>
                <span class="px-2.5 py-0.5 text-xs font-bold bg-white text-zinc-950 rounded-full shadow-xs">
                    📍 {{ $pressing->nom }}
                </span>
            </div>
            <p class="text-xs text-zinc-400 mt-0.5">
                Agence : {{ $pressing->quartier }}, {{ $pressing->ville }} &bull; Tél : <span class="font-mono text-zinc-200 font-bold">{{ $pressing->telephone ?? 'N/A' }}</span>
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex items-center gap-2.5">
            <a href="{{ route('caisse.depot') }}" class="px-3.5 py-2 bg-white hover:bg-zinc-200 text-zinc-950 text-xs font-bold rounded-xl shadow-xs transition duration-150 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouveau Dépôt</span>
            </a>
            <a href="{{ route('caisse.retrait') }}" class="px-3.5 py-2 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 text-xs font-bold rounded-xl shadow-xs transition duration-150 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Gestion / Encaissement Ticket</span>
            </a>
        </div>
    </div>

    <!-- Agency Metrics (Real DB Aggregates) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Dépôts du Jour -->
        <div class="bg-zinc-900 rounded-xl p-5 border border-zinc-800 shadow-xs hover:border-zinc-700 transition duration-200">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Dépôts du Jour</span>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-zinc-800 text-zinc-200 border border-zinc-700">
                    Aujourd'hui
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-white font-mono">
                    {{ $ticketsDeposesAujourdhui }}
                </span>
                <span class="text-xs font-semibold text-zinc-500">tickets</span>
            </div>
            <div class="mt-1.5 text-[11px] text-zinc-400">
                Nouveaux dépôts clients enregistrés
            </div>
        </div>

        <!-- Factures Prêtes pour Retrait -->
        <div class="bg-zinc-900 rounded-xl p-5 border border-zinc-800 shadow-xs hover:border-zinc-700 transition duration-200">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Prêts au Retrait</span>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-white text-zinc-950">
                    Disponibles
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-white font-mono">
                    {{ $ticketsPrets }}
                </span>
                <span class="text-xs font-semibold text-zinc-500">prêts</span>
            </div>
            <div class="mt-1.5 text-[11px] text-zinc-400">
                Vêtements lavés en attente du client
            </div>
        </div>

        <!-- Total Encaissé Aujourd'hui -->
        <div class="bg-zinc-900 rounded-xl p-5 border border-zinc-800 shadow-xs hover:border-zinc-700 transition duration-200">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Encaissé Aujourd'hui</span>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-zinc-800 text-zinc-200 border border-zinc-700">
                    100% au Retrait
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-white font-mono">
                    {{ number_format($totalEncaisseAujourdhui, 2, ',', ' ') }}
                </span>
                <span class="text-xs font-semibold text-zinc-500">FCFA</span>
            </div>
            <div class="mt-1.5 text-[11px] text-zinc-400">
                Recettes perçues sur la journée
            </div>
        </div>

        <!-- Total Tickets en Cours -->
        <div class="bg-zinc-900 rounded-xl p-5 border border-zinc-800 shadow-xs hover:border-zinc-700 transition duration-200">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">En Cours Agence</span>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-zinc-800 text-zinc-300 border border-zinc-700">
                    Global
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-white font-mono">
                    {{ $totalEnCours }}
                </span>
                <span class="text-xs font-semibold text-zinc-500">tickets</span>
            </div>
            <div class="mt-1.5 text-[11px] text-zinc-400">
                Tickets non encore réglés / retirés
            </div>
        </div>
    </div>

    <!-- Agency Operational Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Quick Action Panels -->
        <div class="lg:col-span-1 space-y-5">
            <div class="bg-zinc-900 rounded-xl border border-zinc-800 p-5 shadow-xs space-y-4">
                <div>
                    <h2 class="text-sm font-bold text-white">Opérations du Guichet</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">
                        Accès rapide à la saisie de dépôt ou au règlement du client lors du retrait.
                    </p>
                </div>

                <div class="space-y-2.5">
                    <a href="{{ route('caisse.depot') }}" class="w-full py-2.5 px-4 bg-white hover:bg-zinc-200 text-zinc-950 text-xs font-bold rounded-xl shadow-xs transition duration-150 flex items-center justify-center gap-2 group">
                        <svg class="w-4 h-4 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Nouveau Dépôt Client</span>
                    </a>
                    <a href="{{ route('caisse.retrait') }}" class="w-full py-2.5 px-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl transition duration-150 flex items-center justify-center gap-2 border border-zinc-700">
                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Recherche / Retrait Client</span>
                    </a>
                </div>
            </div>

            <!-- Financial Reminder Card -->
            <div class="bg-zinc-900 rounded-xl border border-zinc-800 p-5 text-white space-y-2 shadow-sm relative overflow-hidden">
                <div class="flex items-center gap-2 text-zinc-300 font-bold text-xs uppercase tracking-wider">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Règle Financière</span>
                </div>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    <strong class="text-white">100% du paiement est encaissé au retrait</strong> des vêtements. Aucun acompte à la création du dépôt.
                </p>
            </div>
        </div>

        <!-- Right Column: Recent Invoices of this Agency -->
        <div class="lg:col-span-2 bg-zinc-900 rounded-xl border border-zinc-800 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-zinc-800/80 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-white">Derniers Tickets de l'Agence</h2>
                        <p class="text-xs text-zinc-400 mt-0.5">Activité récente du guichet {{ $pressing->nom }}</p>
                    </div>
                    <a href="{{ route('caisse.retrait') }}" class="text-xs font-bold text-white hover:text-zinc-300 flex items-center gap-1 transition">
                        <span>Voir tout</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-zinc-950/80 border-b border-zinc-800 text-[10px] uppercase tracking-wider text-zinc-400 font-semibold">
                                <th class="py-3 px-4">Ticket</th>
                                <th class="py-3 px-4">Client</th>
                                <th class="py-3 px-4">Date Dépôt</th>
                                <th class="py-3 px-4 text-center">Statut</th>
                                <th class="py-3 px-4 text-right">Montant</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/60">
                            @forelse($dernieresFactures as $f)
                                <tr class="hover:bg-zinc-800/40 transition duration-150">
                                    <td class="py-3.5 px-4 font-mono font-bold text-white">
                                        {{ $f->num_ticket }}
                                    </td>
                                    <td class="py-3.5 px-4 text-zinc-300">
                                        <div class="font-bold text-white">{{ $f->client_nom ?? 'Client de passage' }}</div>
                                        <div class="font-mono text-[11px] text-zinc-400">{{ $f->client_telephone }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-zinc-400 font-mono text-[11px]">
                                        {{ $f->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($f->statut === 'paye_retire')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white text-zinc-950">
                                                Retiré & Payé
                                            </span>
                                        @elseif($f->statut === 'pret')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-800 text-zinc-200 border border-zinc-700">
                                                Prêt
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-800 text-zinc-400 border border-zinc-700">
                                                Déposé
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-white">
                                        {{ number_format((float) $f->montant_total, 2, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <a href="{{ route('caisse.factures.print', $f) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 transition">
                                            <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            <span>Reçu</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-xs text-zinc-400">
                                        <div class="max-w-xs mx-auto space-y-2">
                                            <div class="w-10 h-10 rounded-full bg-zinc-800 text-zinc-400 flex items-center justify-center mx-auto text-base">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <p class="font-medium text-zinc-200">Aucun ticket enregistré aujourd'hui</p>
                                            <a href="{{ route('caisse.depot') }}" class="inline-block px-3.5 py-1.5 bg-white text-zinc-950 rounded-xl text-xs font-bold">
                                                Créer le premier dépôt
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
