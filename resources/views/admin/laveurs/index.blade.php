@extends('layouts.app', ['title' => 'Gestion des Laveurs'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white">Personnel de Terrain (Laveurs)</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-full">
                    {{ $laveurs->total() }} laveur(s)
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Suivi des techniciens de lavage, configuration des commissions et calculs journaliers
            </p>
        </div>
        <div>
            <a href="{{ route('admin.laveurs.create') }}" class="px-4 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-xs transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Ajouter un laveur</span>
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl text-xs font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs">
        <form method="GET" action="{{ route('admin.laveurs.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1 max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Rechercher par nom ou téléphone..."
                    class="w-full pl-9 pr-4 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                >
            </div>

            <div class="w-full sm:w-48">
                <input
                    type="month"
                    name="mois"
                    value="{{ $selectedMois }}"
                    onchange="this.form.submit()"
                    title="Mois de décompte de paie"
                    class="w-full px-3.5 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition cursor-pointer"
                >
            </div>

            <div class="w-full sm:w-64">
                <select
                    name="pressing_id"
                    onchange="this.form.submit()"
                    class="w-full px-3.5 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition cursor-pointer"
                >
                    <option value="">Toutes les agences</option>
                    @if($pressings->where('type_activite', 'lavage')->isNotEmpty())
                        <optgroup label="Stations de Lavage Auto/Moto">
                            @foreach($pressings->where('type_activite', 'lavage') as $p)
                                <option value="{{ $p->id }}" {{ (string)$selectedPressingId === (string)$p->id ? 'selected' : '' }}>
                                    {{ $p->nom }} ({{ $p->ville }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                    @if($pressings->where('type_activite', '!=', 'lavage')->isNotEmpty())
                        <optgroup label="Agences Pressing">
                            @foreach($pressings->where('type_activite', '!=', 'lavage') as $p)
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
                @if($search || $selectedPressingId || $selectedMois !== date('Y-m'))
                    <a href="{{ route('admin.laveurs.index') }}" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-semibold transition border border-zinc-200 dark:border-zinc-700">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Monthly Payroll Overview Section -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-zinc-200/80 dark:border-zinc-800">
            <div>
                <h2 class="text-base font-extrabold text-zinc-950 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-zinc-900 dark:bg-white inline-block"></span>
                    <span>État des Rémunérations & Paies de Fin de Mois</span>
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Mois : <strong class="text-zinc-900 dark:text-white capitalize">{{ \Carbon\Carbon::parse($selectedMois . '-01')->translatedFormat('F Y') }}</strong> &bull; Décompte automatique des montants à payer à chaque laveur
                </p>
            </div>
        </div>

        <!-- Monthly KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Lavages Réalisés</span>
                <div class="text-2xl font-black font-mono text-zinc-950 dark:text-white">{{ $totalMoisLavages }}</div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Véhicules traités ce mois</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">CA Lavage du Mois</span>
                <div class="text-2xl font-black font-mono text-zinc-950 dark:text-white">{{ number_format($totalMoisCa, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Chiffre d'affaires généré</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Commissions Totales Calculées</span>
                <div class="text-2xl font-black font-mono text-zinc-950 dark:text-white">{{ number_format($totalMoisCommissions, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Montant théorique du mois</p>
            </div>

            <div class="bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 p-4 rounded-2xl border border-zinc-900 dark:border-zinc-200 shadow-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider opacity-75">Reste à Verser aux Laveurs</span>
                <div class="text-2xl font-black font-mono">{{ number_format($totalMoisResteDu, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
                <p class="text-[11px] opacity-75">
                    @if($totalMoisResteDu == 0 && $totalMoisCommissions > 0)
                        &bull; Tous les salaires du mois sont soldés
                    @else
                        Total restant à régler en espèces
                    @endif
                </p>
            </div>
        </div>

        <!-- Monthly Breakdown Table -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-950 dark:text-white">Décompte Individuel & Émargement des Salaires de Fin de Mois</h3>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Cliquez sur « Valider le paiement » une fois le salaire remis physiquement en espèces pour remettre le solde à zéro.</p>
                </div>
                <span class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono">{{ count($recapLaveurs) }} laveur(s) actif(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                            <th class="py-3 px-6">Laveur</th>
                            <th class="py-3 px-6">Station de rattachement</th>
                            <th class="py-3 px-6">Règle</th>
                            <th class="py-3 px-6 text-center">Lavages</th>
                            <th class="py-3 px-6 text-right">CA Réalisé</th>
                            <th class="py-3 px-6 text-right font-bold">Total Dû (FCFA)</th>
                            <th class="py-3 px-6 text-center">État Règlement</th>
                            <th class="py-3 px-6 text-right font-black">Reste à Payer</th>
                            <th class="py-3 px-6 text-right">Action (Paiement physique)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                        @forelse($recapLaveurs as $row)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                                <td class="py-3.5 px-6 font-bold text-zinc-950 dark:text-white">
                                    <div>{{ $row['laveur']->nom }}</div>
                                    <div class="text-[10px] font-mono text-zinc-500">{{ $row['laveur']->telephone ?? 'Sans tél.' }}</div>
                                </td>
                                <td class="py-3.5 px-6 text-zinc-700 dark:text-zinc-300">
                                    <span class="font-semibold">{{ $row['laveur']->pressing->nom ?? '—' }}</span>
                                    <span class="text-[10px] text-zinc-400">({{ $row['laveur']->pressing->isLavage() ? 'Lavage' : 'Pressing' }})</span>
                                </td>
                                <td class="py-3.5 px-6 text-zinc-700 dark:text-zinc-300">
                                    @if($row['laveur']->type_remuneration === 'pourcentage')
                                        <span class="font-mono font-bold">{{ number_format($row['laveur']->valeur_remuneration, 0) }}%</span>
                                    @else
                                        <span class="font-mono font-bold">{{ number_format($row['laveur']->valeur_remuneration, 0, ',', ' ') }} F</span> / vh
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-center font-mono">
                                    <span class="px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 text-[11px] font-bold">
                                        {{ $row['nb_lavages'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-right font-mono text-zinc-800 dark:text-zinc-200">
                                    {{ number_format($row['ca_genere'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3.5 px-6 text-right font-mono font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ number_format($row['commission_due'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                    @if($row['est_paye'])
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-950">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white dark:bg-zinc-950"></span>
                                            <span>Soldé en espèces</span>
                                        </span>
                                        @if($row['paiement']?->date_paiement)
                                            <div class="text-[9px] font-mono text-zinc-400 mt-0.5">le {{ \Carbon\Carbon::parse($row['paiement']->date_paiement)->format('d/m/Y') }}</div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                                            <span>En attente</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-right font-mono font-black text-xs {{ $row['reste_a_payer'] == 0 ? 'text-zinc-400 dark:text-zinc-500' : 'text-zinc-950 dark:text-white' }}">
                                    @if($row['est_paye'])
                                        <span class="text-zinc-400 dark:text-zinc-500">0 FCFA</span>
                                    @else
                                        <span>{{ number_format($row['reste_a_payer'], 0, ',', ' ') }} FCFA</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                    @if($row['est_paye'])
                                        <form method="POST" action="{{ route('admin.laveurs.annuler-paiement', $row['laveur']) }}" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="mois" value="{{ $selectedMois }}">
                                            <button
                                                type="button"
                                                onclick="showConfirmModal({
                                                    title: 'Annuler le règlement de salaire',
                                                    message: 'Confirmez-vous l\'annulation du paiement de {{ $row['laveur']->nom }} pour le mois de {{ \Carbon\Carbon::parse($selectedMois . '-01')->translatedFormat('F Y') }} ? Le salaire repassera en attente.',
                                                    confirmText: 'Confirmer l\'annulation',
                                                    type: 'danger',
                                                    form: this.closest('form')
                                                })"
                                                class="px-2.5 py-1 text-[10px] font-semibold text-zinc-500 hover:text-zinc-900 dark:hover:text-white underline transition cursor-pointer"
                                            >
                                                Annuler émargement
                                            </button>
                                        </form>
                                    @else
                                        @if($row['commission_due'] > 0)
                                            <form method="POST" action="{{ route('admin.laveurs.paiement', $row['laveur']) }}" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="mois" value="{{ $selectedMois }}">
                                                <input type="hidden" name="montant" value="{{ $row['commission_due'] }}">
                                                <button
                                                    type="button"
                                                    onclick="showConfirmModal({
                                                        title: 'Valider le paiement physique en espèces',
                                                        message: 'Confirmez-vous le versement physique de {{ number_format($row['commission_due'], 0, ',', ' ') }} FCFA en espèces à {{ addslashes($row['laveur']->nom) }} pour le mois de {{ \Carbon\Carbon::parse($selectedMois . '-01')->translatedFormat('F Y') }} ? Le montant dû sera remis à zéro.',
                                                        confirmText: 'Valider et solder le mois',
                                                        type: 'info',
                                                        form: this.closest('form')
                                                    })"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black font-bold text-[11px] rounded-lg transition shadow-xs cursor-pointer"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Marquer payé en espèces</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] text-zinc-400 italic">Aucune commission</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-xs text-zinc-500">
                                    Aucun laveur enregistré ou aucun lavage ce mois.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Personnel Roster Section -->
    <div class="space-y-3 pt-4 border-t border-zinc-200/80 dark:border-zinc-800">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-extrabold uppercase tracking-wider text-zinc-900 dark:text-white">
                Effectif & Paramétrage du Personnel Laveurs
            </h2>
            <span class="text-xs text-zinc-500 font-mono">{{ $laveurs->total() }} membre(s)</span>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                            <th class="py-3.5 px-6">Nom</th>
                            <th class="py-3.5 px-6">Établissement</th>
                            <th class="py-3.5 px-6">Téléphone</th>
                            <th class="py-3.5 px-6">Mode de rémunération</th>
                            <th class="py-3.5 px-6">Taux / Montant</th>
                            <th class="py-3.5 px-6 text-center">Lavages traités (Total)</th>
                            <th class="py-3.5 px-6 text-center">Statut</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                        @forelse($laveurs as $laveur)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition duration-150">
                                <td class="py-4 px-6 font-bold text-zinc-950 dark:text-white">
                                    {{ $laveur->nom }}
                                </td>
                                <td class="py-4 px-6 font-medium text-zinc-800 dark:text-zinc-300">
                                    {{ $laveur->pressing->nom ?? '—' }}
                                </td>
                                <td class="py-4 px-6 font-mono text-zinc-600 dark:text-zinc-400">
                                    {{ $laveur->telephone ?? '—' }}
                                </td>
                                <td class="py-4 px-6 font-medium text-zinc-800 dark:text-zinc-200">
                                    @if($laveur->type_remuneration === 'pourcentage')
                                        Pourcentage par lavage
                                    @else
                                        Forfait fixe par véhicule
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-mono font-bold text-zinc-900 dark:text-zinc-100">
                                    @if($laveur->type_remuneration === 'pourcentage')
                                        {{ number_format($laveur->valeur_remuneration, 0) }}%
                                    @else
                                        {{ number_format($laveur->valeur_remuneration, 0, ',', ' ') }} FCFA
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center font-mono">
                                    <span class="px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 text-[11px] font-bold">
                                        {{ $laveur->factures_count }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($laveur->actif)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-black">
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                            Inactif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.laveurs.edit', $laveur) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition">
                                            Modifier
                                        </a>

                                        <form method="POST" action="{{ route('admin.laveurs.destroy', $laveur) }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                onclick="showConfirmModal({
                                                    title: 'Supprimer le profil laveur',
                                                    message: 'Confirmez-vous la suppression du laveur « {{ addslashes($laveur->nom) }} » ?',
                                                    confirmText: 'Supprimer',
                                                    type: 'danger',
                                                    form: this.closest('form')
                                                })"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 transition cursor-pointer"
                                            >
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                        <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Aucun laveur enregistré</p>
                                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500">Ajoutez le personnel de terrain de vos stations de lavage pour suivre leurs commissions.</p>
                                    <a href="{{ route('admin.laveurs.create') }}" class="inline-flex items-center px-3.5 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition">
                                        Ajouter un laveur
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laveurs->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $laveurs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
