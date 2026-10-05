@extends('layouts.app', ['title' => 'Espace Caisse - ' . $pressing->nom])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold tracking-tight text-slate-900">Espace Guichet Caisse</h1>
                <span class="px-2.5 py-0.5 text-xs font-semibold bg-indigo-600 text-white rounded-full shadow-xs">
                    📍 {{ $pressing->nom }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Agence : {{ $pressing->quartier }}, {{ $pressing->ville }} &bull; Tél : <span class="font-mono text-slate-700 font-semibold">{{ $pressing->telephone ?? 'N/A' }}</span>
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex items-center gap-2.5">
            <a href="{{ route('caisse.depot') }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition duration-150 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouveau Dépôt</span>
            </a>
            <a href="{{ route('caisse.retrait') }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-xs transition duration-150 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Gestion / Encaissement Ticket</span>
            </a>
        </div>
    </div>

    <!-- Agency Metrics (Real DB Aggregates) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Dépôts du Jour -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dépôts du Jour</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                    Aujourd'hui
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-slate-900 font-mono">
                    {{ $ticketsDeposesAujourdhui }}
                </span>
                <span class="text-xs font-semibold text-slate-500">tickets</span>
            </div>
            <div class="mt-1.5 text-[11px] text-slate-500">
                Nouveaux dépôts clients enregistrés
            </div>
        </div>

        <!-- Factures Prêtes pour Retrait -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Prêts au Retrait</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                    Disponibles
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-indigo-600 font-mono">
                    {{ $ticketsPrets }}
                </span>
                <span class="text-xs font-semibold text-slate-500">prêts</span>
            </div>
            <div class="mt-1.5 text-[11px] text-slate-500">
                Vêtements lavés en attente du client
            </div>
        </div>

        <!-- Total Encaissé Aujourd'hui -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Encaissé Aujourd'hui</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                    100% au Retrait
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-slate-900 font-mono">
                    {{ number_format($totalEncaisseAujourdhui, 2, ',', ' ') }}
                </span>
                <span class="text-xs font-semibold text-slate-500">FCFA</span>
            </div>
            <div class="mt-1.5 text-[11px] text-slate-500">
                Recettes perçues sur la journée
            </div>
        </div>

        <!-- Total Tickets en Cours -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition duration-200">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">En Cours Agence</span>
                <span class="px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                    Global
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold tracking-tight text-slate-900 font-mono">
                    {{ $totalEnCours }}
                </span>
                <span class="text-xs font-semibold text-slate-500">tickets</span>
            </div>
            <div class="mt-1.5 text-[11px] text-slate-500">
                Tickets non encore réglés / retirés
            </div>
        </div>
    </div>

    <!-- Agency Operational Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Quick Action Panels -->
        <div class="lg:col-span-1 space-y-5">
            <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs space-y-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Opérations du Guichet</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Accès rapide à la saisie de dépôt ou au règlement du client lors du retrait.
                    </p>
                </div>

                <div class="space-y-2.5">
                    <a href="{{ route('caisse.depot') }}" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition duration-150 flex items-center justify-center gap-2 group">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Nouveau Dépôt Client</span>
                    </a>
                    <a href="{{ route('caisse.retrait') }}" class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200/80 text-slate-800 text-xs font-semibold rounded-xl transition duration-150 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Recherche / Retrait Client</span>
                    </a>
                </div>
            </div>

            <!-- Financial Reminder Card -->
            <div class="bg-gradient-to-br from-slate-900 to-indigo-950 rounded-xl p-5 text-white space-y-2 shadow-sm relative overflow-hidden">
                <div class="flex items-center gap-2 text-indigo-300 font-semibold text-xs uppercase tracking-wider">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Règle Financière</span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">
                    <strong class="text-white">100% du paiement est encaissé au retrait</strong> des vêtements. Aucun acompte à la création du dépôt.
                </p>
            </div>
        </div>

        <!-- Right Column: Recent Invoices of this Agency -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Derniers Tickets de l'Agence</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Activité récente du guichet {{ $pressing->nom }}</p>
                    </div>
                    <a href="{{ route('caisse.retrait') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 transition">
                        <span>Voir tout</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-semibold">
                                <th class="py-3 px-4">Ticket</th>
                                <th class="py-3 px-4">Client</th>
                                <th class="py-3 px-4">Date Dépôt</th>
                                <th class="py-3 px-4 text-center">Statut</th>
                                <th class="py-3 px-4 text-right">Montant</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dernieresFactures as $f)
                                <tr class="hover:bg-slate-50/60 transition duration-150">
                                    <td class="py-3.5 px-4 font-mono font-bold text-indigo-600">
                                        {{ $f->num_ticket }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">
                                        <div class="font-semibold text-slate-900">{{ $f->client_nom ?? 'Client de passage' }}</div>
                                        <div class="font-mono text-[11px] text-slate-400">{{ $f->client_telephone }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                        {{ $f->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($f->statut === 'paye_retire')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Retiré & Payé
                                            </span>
                                        @elseif($f->statut === 'pret')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                Prêt
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                Déposé
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                        {{ number_format((float) $f->montant_total, 2, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <a href="{{ route('caisse.factures.print', $f) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            <span>Reçu</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-xs text-slate-500">
                                        <div class="max-w-xs mx-auto space-y-2">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-base">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <p class="font-medium text-slate-700">Aucun ticket enregistré aujourd'hui</p>
                                            <a href="{{ route('caisse.depot') }}" class="inline-block px-3.5 py-1.5 bg-indigo-600 text-white rounded-xl text-xs font-semibold">
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
