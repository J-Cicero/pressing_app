@extends('layouts.app', ['title' => 'Historique des Tickets & Factures'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900 dark:text-white">
                    Tickets & Factures
                </h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>{{ $pressing->nom }}</span>
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Historique et suivi des dépôts, lavages et encaissements de votre établissement
            </p>
        </div>

        <!-- Metric summary pills -->
        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-3 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs text-xs">
            <div>
                <span class="text-zinc-400 dark:text-zinc-500 uppercase text-[10px] font-semibold block">Tickets filtrés</span>
                <span class="font-bold text-zinc-900 dark:text-white font-mono text-sm">{{ $totalTickets }} ticket(s)</span>
            </div>
            <div class="w-px h-8 bg-zinc-100 dark:bg-zinc-800"></div>
            <div>
                <span class="text-zinc-400 dark:text-zinc-500 uppercase text-[10px] font-semibold block">Montant cumulé</span>
                <span class="font-extrabold text-zinc-900 dark:text-white font-mono text-sm">{{ number_format($totalMontant, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    <!-- Filters form -->
    <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
        <form method="GET" action="{{ route('caisse.factures.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    Recherche multicritère
                </label>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="N° ticket, client, tél, plaque..."
                    class="w-full px-3.5 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                >
            </div>

            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    Statut du ticket
                </label>
                <select name="statut" class="w-full px-3.5 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition">
                    <option value="">Tous les statuts</option>
                    <option value="depose" {{ $selectedStatut === 'depose' ? 'selected' : '' }}>Déposé (En cours)</option>
                    <option value="pret" {{ $selectedStatut === 'pret' ? 'selected' : '' }}>Prêt (Non retiré)</option>
                    <option value="paye_retire" {{ $selectedStatut === 'paye_retire' ? 'selected' : '' }}>Payé & Retiré</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    Date du dépôt
                </label>
                <input
                    type="date"
                    name="date"
                    value="{{ $selectedDate }}"
                    class="w-full px-3.5 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition"
                >
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-950 text-xs font-bold rounded-xl shadow-xs transition flex-1 h-[38px] flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filtrer</span>
                </button>
                @if($search || $selectedStatut || $selectedDate)
                    <a href="{{ route('caisse.factures.index') }}" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 rounded-xl text-xs font-semibold transition border border-zinc-200 dark:border-zinc-700 h-[38px] flex items-center">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Factures -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 text-[10px] uppercase font-bold tracking-wider text-zinc-500 dark:text-zinc-400">
                        <th class="px-4 py-3.5">Ticket</th>
                        <th class="px-4 py-3.5">Date & Client</th>
                        <th class="px-4 py-3.5">Détail / Prestations</th>
                        <th class="px-4 py-3.5">Opérateur / Laveur</th>
                        <th class="px-4 py-3.5">Statut</th>
                        <th class="px-4 py-3.5 text-right">Montant</th>
                        <th class="px-4 py-3.5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-medium">
                    @forelse($factures as $facture)
                        <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-mono font-bold text-zinc-900 dark:text-white">
                                    {{ $facture->num_ticket }}
                                </span>
                                <span class="block text-[10px] text-zinc-400 font-mono">
                                    Créé le {{ $facture->created_at->format('d/m/Y H:i') }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="font-bold text-zinc-900 dark:text-white">
                                    {{ $facture->client_nom }}
                                </div>
                                <div class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400">
                                    {{ $facture->client_telephone }}
                                </div>
                                @if($facture->vehicule)
                                    <div class="mt-1 flex items-center gap-1.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700">
                                            {{ $facture->vehicule->immatriculation }}
                                        </span>
                                        <span class="text-[10px] text-zinc-500">
                                            {{ $facture->vehicule->marque_modele }} ({{ $facture->vehicule->type_vehicule }})
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5">
                                    @foreach($facture->ligneFactures->take(3) as $ligne)
                                        <div class="text-[11px] text-zinc-700 dark:text-zinc-300">
                                            <span class="font-bold font-mono">{{ $ligne->quantite }}x</span>
                                            <span>{{ $ligne->service?->designation ?? 'Prestation' }}</span>
                                        </div>
                                    @endforeach
                                    @if($facture->ligneFactures->count() > 3)
                                        <div class="text-[10px] text-zinc-400 font-semibold italic">
                                            + {{ $facture->ligneFactures->count() - 3 }} autre(s) prestation(s)
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-1 text-[10px] text-zinc-400">
                                    Retrait prévu : <span class="font-semibold text-zinc-600 dark:text-zinc-300">{{ \Carbon\Carbon::parse($facture->date_retrait_prevue)->format('d/m/Y') }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($facture->laveur)
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-6 h-6 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white flex items-center justify-center text-[10px] font-bold border border-zinc-200 dark:border-zinc-700">
                                            {{ substr($facture->laveur->nom, 0, 1) }}
                                        </span>
                                        <div>
                                            <div class="font-bold text-zinc-900 dark:text-white text-[11px]">
                                                {{ $facture->laveur->nom }}
                                            </div>
                                            <div class="text-[10px] text-zinc-400">
                                                {{ $facture->laveur->type_remuneration === 'pourcentage' ? number_format($facture->laveur->valeur_remuneration, 0).'%' : number_format($facture->laveur->valeur_remuneration, 0, ',', ' ').' F' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-zinc-400 text-[11px] italic">Non assigné</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($facture->statut === 'depose')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                        En cours
                                    </span>
                                @elseif($facture->statut === 'pret')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950">
                                        Prêt à retirer
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-zinc-900 border border-zinc-900 dark:bg-zinc-950 dark:text-white dark:border-white">
                                        Payé & Retiré
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <span class="font-mono font-bold text-zinc-900 dark:text-white text-sm">
                                    {{ number_format($facture->montant_total, 0, ',', ' ') }} FCFA
                                </span>
                                @if($facture->paye_at)
                                    <span class="block text-[10px] text-zinc-400 font-mono">
                                        Payé le {{ $facture->paye_at->format('d/m/Y') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Print receipt button -->
                                    <a
                                        href="{{ route('caisse.factures.print', $facture->num_ticket) }}"
                                        target="_blank"
                                        title="Imprimer le ticket 80mm"
                                        class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </a>

                                    @if($facture->statut === 'depose')
                                        <form method="POST" action="{{ route('caisse.factures.pret', $facture->num_ticket) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                title="Marquer comme prêt"
                                                class="px-2 py-1 text-[10px] font-bold rounded-lg bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-90 transition cursor-pointer"
                                            >
                                                Prêt
                                            </button>
                                        </form>
                                    @elseif($facture->statut === 'pret')
                                        <a
                                            href="{{ route('caisse.retrait', ['q' => $facture->num_ticket]) }}"
                                            title="Encaisser et retirer"
                                            class="px-2 py-1 text-[10px] font-bold rounded-lg bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white hover:bg-zinc-200 dark:hover:bg-zinc-700 transition"
                                        >
                                            Encaisser
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-zinc-400 dark:text-zinc-500">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <svg class="w-8 h-8 mx-auto text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Aucun ticket trouvé</p>
                                    <p class="text-[11px] text-zinc-400">Modifiez vos critères de recherche ou enregistrez un nouveau ticket.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($factures->hasPages())
            <div class="p-4 border-t border-zinc-100 dark:border-zinc-800">
                {{ $factures->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
