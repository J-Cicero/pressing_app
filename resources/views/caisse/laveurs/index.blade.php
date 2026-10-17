@extends('layouts.app', ['title' => 'Suivi des Laveurs & Commissions'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white uppercase">
                    Commissions & Suivi Laveurs
                </h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-full">
                    {{ $pressing->nom }}
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Enregistrement des laveurs de la station, décompte journalier et consultation des rémunérations mensuelles dues.
            </p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <!-- Action to register a new washer for this station -->
            <button
                type="button"
                onclick="document.getElementById('modalNouveauLaveur').classList.remove('hidden')"
                class="px-4 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouveau Laveur</span>
            </button>

            <!-- Date selector form -->
            <form method="GET" action="{{ route('caisse.laveurs.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="mois" value="{{ $mois }}">
                <input
                    type="date"
                    name="date"
                    value="{{ $date }}"
                    class="px-3.5 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                >
                <button type="submit" class="px-3.5 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-bold rounded-xl transition border border-zinc-200 dark:border-zinc-700">
                    Filtrer Jour
                </button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-2xl text-xs font-semibold text-zinc-900 dark:text-white flex items-center justify-between">
            <span>{{ session('status') }}</span>
            <button onclick="this.parentElement.remove()" class="text-zinc-400 hover:text-black dark:hover:text-white text-base leading-none">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-2xl text-xs text-zinc-900 dark:text-white space-y-1">
            <div class="font-bold">Veuillez corriger les erreurs ci-dessous :</div>
            <ul class="list-disc list-inside space-y-0.5 text-zinc-600 dark:text-zinc-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- SECTION 1 : VUE JOURNALIÈRE -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-black tracking-tight text-zinc-950 dark:text-white uppercase flex items-center gap-2">
                    <span>Activité Journalière</span>
                    <span class="text-xs font-mono font-normal text-zinc-500 dark:text-zinc-400">({{ \Carbon\Carbon::parse($date)->format('d/m/Y') }})</span>
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Commissions terrain générées lors de cette journée</p>
            </div>
        </div>

        <!-- Daily Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Véhicules Lavés</span>
                <div class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ $totalJourLavages }}</div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Date du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Chiffre d'Affaires</span>
                <div class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ number_format($totalJourCa, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Base prestations ordinaires</p>
            </div>

            <div class="bg-black text-white dark:bg-white dark:text-black p-5 rounded-2xl border border-zinc-900 dark:border-zinc-200 shadow-xs space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider opacity-75">Commissions Laveurs (30%)</span>
                <div class="text-3xl font-black font-mono">{{ number_format($totalJourCommission, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
                <p class="text-[11px] opacity-75">Rémunération totale terrain du jour</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Part Station / Marge</span>
                <div class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ number_format($totalJourCa - $totalJourCommission, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Reste pour l'établissement</p>
            </div>
        </div>

        <!-- Washers Breakdown Table (Jour) -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">Décompte Individuel du Jour</h3>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ count($recap) }} laveur(s)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                            <th class="py-3.5 px-6">Laveur</th>
                            <th class="py-3.5 px-6">Téléphone</th>
                            <th class="py-3.5 px-6">Taux de Rétribution</th>
                            <th class="py-3.5 px-6 text-center">Véhicules Traités</th>
                            <th class="py-3.5 px-6 text-right">CA Brut</th>
                            <th class="py-3.5 px-6 text-right font-black">Commission Due</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                        @forelse($recap as $row)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition duration-150">
                                <td class="py-4 px-6 font-bold text-zinc-950 dark:text-white">
                                    {{ $row['laveur']->nom }}
                                </td>
                                <td class="py-4 px-6 font-mono text-zinc-600 dark:text-zinc-400">
                                    {{ $row['laveur']->telephone ?? '—' }}
                                </td>
                                <td class="py-4 px-6 text-zinc-700 dark:text-zinc-300">
                                    <span class="font-mono font-bold">{{ number_format($row['laveur']->valeur_remuneration, 0) }}%</span> du tarif ordinaire
                                </td>
                                <td class="py-4 px-6 text-center font-mono">
                                    <span class="px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 text-[11px] font-black">
                                        {{ $row['nb_lavages'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-mono text-zinc-800 dark:text-zinc-200">
                                    {{ number_format($row['total_ca'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-4 px-6 text-right font-mono font-black text-sm text-zinc-950 dark:text-white">
                                    <span class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                                        {{ number_format($row['total_commission'], 0, ',', ' ') }} FCFA
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Aucun laveur actif configuré pour cet établissement.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 2 : VUE MENSUELLE CONSOLIDÉE (LECTURE SEULE POUR LE CAISSIER) -->
    <div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-black tracking-tight text-zinc-950 dark:text-white uppercase">
                        Décompte Mensuel des Rémunérations
                    </h2>
                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 rounded-md">
                        Consultation Seule
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Montants cumulés dus à chaque laveur pour le mois sélectionné. Le versement effectif est assuré par la direction.
                </p>
            </div>

            <!-- Month Selector Form -->
            <form method="GET" action="{{ route('caisse.laveurs.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="date" value="{{ $date }}">
                <label for="selectMois" class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Mois :</label>
                <input
                    type="month"
                    id="selectMois"
                    name="mois"
                    value="{{ $mois }}"
                    onchange="this.form.submit()"
                    class="px-3.5 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                >
            </form>
        </div>

        <!-- Monthly Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Lavages Réalisés</span>
                <div class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ $totalMoisLavages }}</div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Mois de {{ \Carbon\Carbon::parse($mois . '-01')->translatedFormat('F Y') }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">CA Mensuel Brut</span>
                <div class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ number_format($totalMoisCa, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Valeur totale des prestations</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Rémunération Dûe</span>
                <div class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ number_format($totalMoisCommission, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">30% sur prestations lavages</p>
            </div>

            <div class="bg-black text-white dark:bg-white dark:text-black p-5 rounded-2xl border border-zinc-900 dark:border-zinc-200 shadow-xs space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider opacity-75">Reliquat à Régler</span>
                <div class="text-3xl font-black font-mono">{{ number_format($totalMoisResteAPayer, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
                <p class="text-[11px] opacity-75">Reste à payer par la Direction</p>
            </div>
        </div>

        <!-- Monthly Table (Read-Only) -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-xs font-bold text-zinc-950 dark:text-white uppercase tracking-wider">
                    Récapitulatif Mensuel par Laveur &mdash; {{ \Carbon\Carbon::parse($mois . '-01')->translatedFormat('F Y') }}
                </h3>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Règlements gérés par l'Administration</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                            <th class="py-3.5 px-6">Laveur</th>
                            <th class="py-3.5 px-6">Téléphone</th>
                            <th class="py-3.5 px-6 text-center">Lavages Effectués</th>
                            <th class="py-3.5 px-6 text-right">CA Brut Traité</th>
                            <th class="py-3.5 px-6 text-right font-black">Rémunération Mensuelle</th>
                            <th class="py-3.5 px-6 text-center">Statut du Règlement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                        @forelse($recapMois as $row)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition duration-150">
                                <td class="py-4 px-6 font-bold text-zinc-950 dark:text-white">
                                    {{ $row['laveur']->nom }}
                                </td>
                                <td class="py-4 px-6 font-mono text-zinc-600 dark:text-zinc-400">
                                    {{ $row['laveur']->telephone ?? '—' }}
                                </td>
                                <td class="py-4 px-6 text-center font-mono">
                                    <span class="px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 text-[11px] font-black">
                                        {{ $row['nb_lavages'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-mono text-zinc-800 dark:text-zinc-200">
                                    {{ number_format($row['total_ca'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-4 px-6 text-right font-mono font-black text-sm text-zinc-950 dark:text-white">
                                    {{ number_format($row['total_commission'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($row['est_paye'])
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-black">
                                            <span>Régularisé par la Direction</span>
                                        </span>
                                    @elseif($row['total_commission'] == 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                            Aucun reliquat
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-500"></span>
                                            <span>En attente de paiement ({{ number_format($row['reste_a_payer'], 0, ',', ' ') }} FCFA)</span>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Aucun laveur actif configuré.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal : Enregistrement d'un Nouveau Laveur pour la Station -->
<div id="modalNouveauLaveur" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden" role="dialog" aria-modal="true">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="flex items-start justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800">
            <div>
                <h3 class="text-base font-black text-zinc-950 dark:text-white uppercase tracking-tight">
                    Nouveau Laveur de Station
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Enregistrement d'un laveur pour l'agence {{ $pressing->nom }}
                </p>
            </div>
            <button
                type="button"
                onclick="document.getElementById('modalNouveauLaveur').classList.add('hidden')"
                class="text-zinc-400 hover:text-black dark:hover:text-white text-lg leading-none p-1 cursor-pointer"
            >
                &times;
            </button>
        </div>

        <form method="POST" action="{{ route('caisse.laveurs.store') }}" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="laveur_nom" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    Nom et Prénom du Laveur <span class="text-black dark:text-white font-bold">*</span>
                </label>
                <input
                    type="text"
                    name="nom"
                    id="laveur_nom"
                    required
                    class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                    placeholder="Ex: Kodjo Amouzou"
                >
            </div>

            <div class="space-y-1.5">
                <label for="laveur_telephone" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    Numéro de Téléphone <span class="text-black dark:text-white font-bold">*</span>
                </label>
                <input
                    type="tel"
                    name="telephone"
                    id="laveur_telephone"
                    required
                    class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs font-mono focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                    placeholder="Ex: 90 12 34 56"
                >
            </div>

            <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-[11px] text-zinc-600 dark:text-zinc-400 space-y-1">
                <div class="font-bold text-zinc-900 dark:text-white">Règle de Rémunération Automatique :</div>
                <div>Commission fixe de <strong class="text-black dark:text-white font-mono">30%</strong> sur chaque véhicule lavé.</div>
                <div class="text-[10px] text-zinc-500">Le calcul s'applique sur le tarif ordinaire du lavage, sans déduction des éventuelles remises fidélité client.</div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-100 dark:border-zinc-800">
                <button
                    type="button"
                    onclick="document.getElementById('modalNouveauLaveur').classList.add('hidden')"
                    class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    class="px-5 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-xs transition duration-150 cursor-pointer"
                >
                    Enregistrer le Laveur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
