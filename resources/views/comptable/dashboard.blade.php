@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
    <!-- En-tête Dédié Comptable -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black dark:bg-white text-white dark:text-black">
                    Portail Comptable Dédié
                </span>
                <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">
                    Exercice mensuel : {{ $mois }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-zinc-950 dark:text-white mt-1.5">
                Suivi Mensuel des Revenus & Dépenses par Agence
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Consultez les recettes encaissées et enregistrez les charges d'exploitation pour chaque agence (Pressing & Station de Lavage).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <form method="GET" action="{{ route('comptable.dashboard') }}" class="flex items-center gap-2">
                @if($selectedPressingId)
                    <input type="hidden" name="pressing_id" value="{{ $selectedPressingId }}">
                @endif
                <label for="mois_comptable" class="text-xs font-bold text-zinc-600 dark:text-zinc-300">Mois :</label>
                <input
                    type="month"
                    id="mois_comptable"
                    name="mois"
                    value="{{ $mois }}"
                    onchange="this.form.submit()"
                    class="px-3 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono font-bold text-zinc-900 dark:text-white focus:outline-none focus:border-black dark:focus:border-white"
                >
            </form>

            <button
                type="button"
                onclick="openCreateDepenseModal()"
                class="px-4 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouvelle Dépense</span>
            </button>
        </div>
    </div>

    <!-- Alertes -->
    @if(session('status'))
        <div class="p-4 rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-zinc-950 text-xs font-semibold flex items-center justify-between shadow-xs">
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-zinc-100 dark:bg-zinc-900 border-2 border-black dark:border-white text-zinc-900 dark:text-white text-xs space-y-1">
            <div class="font-bold">Veuillez vérifier les champs suivants :</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Indicateurs Financiers du Mois -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 space-y-1 shadow-xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                Revenus Totaux ({{ $mois }})
            </div>
            <div class="text-2xl font-mono font-black text-zinc-950 dark:text-white">
                {{ number_format($totalRevenusMois, 0, ',', ' ') }} <span class="text-xs font-normal text-zinc-500">FCFA</span>
            </div>
            <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Recettes encaissées toutes agences</div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 space-y-1 shadow-xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                Total Dépenses ({{ $mois }})
            </div>
            <div class="text-2xl font-mono font-black text-zinc-950 dark:text-white">
                {{ number_format($totalDepensesMois, 0, ',', ' ') }} <span class="text-xs font-normal text-zinc-500">FCFA</span>
            </div>
            <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Charges enregistrées sur la période</div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 space-y-1 shadow-xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                Rémunérations Laveurs ({{ $mois }})
            </div>
            <div class="text-2xl font-mono font-black text-zinc-950 dark:text-white">
                {{ number_format($totalCommissionsMois, 0, ',', ' ') }} <span class="text-xs font-normal text-zinc-500">FCFA</span>
            </div>
            <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Commissions réglées aux laveurs</div>
        </div>

        <div class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-950 rounded-2xl p-5 space-y-1 shadow-sm">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                Solde Net d'Exploitation ({{ $mois }})
            </div>
            <div class="text-2xl font-mono font-black">
                {{ number_format($resultatNetGlobal, 0, ',', ' ') }} <span class="text-xs font-normal opacity-75">FCFA</span>
            </div>
            <div class="text-[11px] text-zinc-300 dark:text-zinc-600">Bénéfice net après charges</div>
        </div>
    </div>

    <!-- Cartes Détaillées par Agence -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($bilanAgences as $item)
            @php $p = $item['pressing']; @endphp
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 space-y-4 shadow-xs">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $p->isLavage() ? 'bg-zinc-200 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-950' }}">
                            {{ $p->isLavage() ? 'Station Lavage Auto/Moto' : 'Agence Pressing Vêtements' }}
                        </span>
                        <h3 class="text-base font-black text-zinc-950 dark:text-white mt-1">{{ $p->nom }}</h3>
                        <p class="text-[11px] text-zinc-500">{{ $p->ville }}{{ $p->quartier ? ' • '.$p->quartier : '' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] font-bold uppercase text-zinc-400">Résultat Net ({{ $mois }})</div>
                        <div class="text-lg font-mono font-black text-zinc-950 dark:text-white">
                            {{ number_format($item['resultat_net'], 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800">
                        <div class="text-[10px] uppercase font-bold text-zinc-500">Revenus</div>
                        <div class="text-xs font-mono font-bold text-zinc-950 dark:text-white mt-1">
                            +{{ number_format($item['revenu'], 0, ',', ' ') }}
                        </div>
                        <div class="text-[10px] text-zinc-400">{{ $item['nb_factures'] }} ticket(s)</div>
                    </div>
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800">
                        <div class="text-[10px] uppercase font-bold text-zinc-500">Dépenses</div>
                        <div class="text-xs font-mono font-bold text-zinc-950 dark:text-white mt-1">
                            -{{ number_format($item['depenses'], 0, ',', ' ') }}
                        </div>
                        <div class="text-[10px] text-zinc-400">{{ $item['nb_depenses'] }} charge(s)</div>
                    </div>
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800">
                        <div class="text-[10px] uppercase font-bold text-zinc-500">Laveurs</div>
                        <div class="text-xs font-mono font-bold text-zinc-950 dark:text-white mt-1">
                            -{{ number_format($item['commissions'], 0, ',', ' ') }}
                        </div>
                        <div class="text-[10px] text-zinc-400">Commissions</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Registre des Dépenses -->
    <div id="section-depenses" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xs overflow-hidden">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 space-y-4">
            <div>
                <h2 class="text-sm font-black uppercase tracking-wider text-zinc-950 dark:text-white">
                    Journal des Charges & Dépenses ({{ $mois }})
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Liste complète des dépenses enregistrées pour chaque agence sur le mois sélectionné.
                </p>
            </div>

            <form method="GET" action="{{ route('comptable.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <input type="hidden" name="mois" value="{{ $mois }}">
                <div>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Rechercher un motif ou reçu..."
                        class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white"
                    >
                </div>
                <div>
                    <select name="pressing_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white">
                        <option value="">Toutes les agences</option>
                        @foreach($pressings as $p)
                            <option value="{{ $p->id }}" {{ (string)$selectedPressingId === (string)$p->id ? 'selected' : '' }}>
                                {{ $p->nom }} ({{ $p->isLavage() ? 'Lavage' : 'Pressing' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="categorie" onchange="this.form.submit()" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ $selectedCategorie === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-black dark:bg-white text-white dark:text-black text-xs font-bold rounded-xl flex-1">
                        Filtrer
                    </button>
                    @if($search || $selectedPressingId || $selectedCategorie)
                        <a href="{{ route('comptable.dashboard', ['mois' => $mois]) }}" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl border border-zinc-200 dark:border-zinc-700">
                            Réinit.
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50/80 dark:bg-zinc-950/80 border-b border-zinc-100 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6">Agence</th>
                        <th class="py-3.5 px-6">Catégorie</th>
                        <th class="py-3.5 px-6">Libellé / Motif</th>
                        <th class="py-3.5 px-6">Pièce / Reçu</th>
                        <th class="py-3.5 px-6 text-right">Montant</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($depenses as $depense)
                        @php
                            $agence = $pressingsMap->get($depense->pressing_id);
                            $auteur = $usersMap->get($depense->user_id);
                        @endphp
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                            <td class="py-4 px-6 font-mono text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                {{ $depense->date_depense->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6 font-bold text-zinc-900 dark:text-white">
                                {{ $agence?->nom ?? 'Agence' }}
                                <span class="block text-[10px] font-normal text-zinc-500">
                                    {{ $agence?->isLavage() ? 'Station Lavage' : 'Pressing' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                    {{ $depense->categorie_label }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-zinc-900 dark:text-white font-semibold">
                                {{ $depense->libelle }}
                                @if($depense->notes)
                                    <div class="text-[11px] font-normal text-zinc-500 mt-0.5">{{ $depense->notes }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-mono text-zinc-500">
                                {{ $depense->justificatif ?: '—' }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-black text-zinc-950 dark:text-white whitespace-nowrap">
                                {{ number_format((float)$depense->montant, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        onclick="openEditDepenseModal({
                                            uuid: '{{ $depense->getRouteKey() }}',
                                            pressing_id: '{{ $depense->pressing_id }}',
                                            categorie: '{{ $depense->categorie }}',
                                            libelle: '{{ addslashes($depense->libelle) }}',
                                            montant: '{{ (float)$depense->montant }}',
                                            date_depense: '{{ $depense->date_depense->format('Y-m-d') }}',
                                            justificatif: '{{ addslashes((string)$depense->justificatif) }}',
                                            notes: '{{ addslashes((string)$depense->notes) }}'
                                        })"
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition cursor-pointer"
                                    >
                                        Modifier
                                    </button>

                                    <form method="POST" action="{{ route('comptable.depenses.destroy', $depense) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            onclick="return confirm('Confirmer la suppression de cette dépense ?')"
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black transition cursor-pointer"
                                        >
                                            Suppr.
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <div class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Aucune dépense enregistrée pour {{ $mois }}</div>
                                    <p class="text-[11px] text-zinc-500">Enregistrez les dépenses effectuées par les agences de pressing et de lavage.</p>
                                    <div class="pt-2">
                                        <button type="button" onclick="openCreateDepenseModal()" class="px-4 py-2 bg-black dark:bg-white text-white dark:text-black text-xs font-bold rounded-xl">
                                            + Nouvelle Dépense
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($depenses->hasPages())
            <div class="p-4 border-t border-zinc-100 dark:border-zinc-800">
                {{ $depenses->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Création Dépense (Comptable) -->
<div id="modal-create-depense" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <div>
                <h3 class="text-base font-black text-zinc-950 dark:text-white">Enregistrer une Dépense</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Saisie comptable d'une charge d'agence (Pressing ou Lavage)</p>
            </div>
            <button type="button" onclick="closeCreateDepenseModal()" class="text-zinc-400 hover:text-zinc-700 text-lg font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('comptable.depenses.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Agence concernée *</label>
                    <select name="pressing_id" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white">
                        @foreach($pressings as $p)
                            <option value="{{ $p->id }}">{{ $p->nom }} ({{ $p->isLavage() ? 'Lavage' : 'Pressing' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Catégorie *</label>
                    <select name="categorie" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Libellé / Motif *</label>
                <input type="text" name="libelle" required placeholder="Ex: Facture d'eau / Achat produits détachants" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Montant (FCFA) *</label>
                    <input type="number" name="montant" min="1" step="1" required placeholder="10000" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Date *</label>
                    <input type="date" name="date_depense" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">N° Reçu / Réf.</label>
                    <input type="text" name="justificatif" placeholder="REC-001" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" onclick="closeCreateDepenseModal()" class="px-4 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300">Annuler</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-black dark:bg-white text-white dark:text-black text-xs font-bold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Édition Dépense (Comptable) -->
<div id="modal-edit-depense" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <h3 class="text-base font-black text-zinc-950 dark:text-white">Modifier la Dépense</h3>
            <button type="button" onclick="closeEditDepenseModal()" class="text-zinc-400 hover:text-zinc-700 text-lg font-bold">&times;</button>
        </div>

        <form method="POST" id="edit_depense_form" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Agence concernée *</label>
                    <select name="pressing_id" id="edit_pressing_id" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white">
                        @foreach($pressings as $p)
                            <option value="{{ $p->id }}">{{ $p->nom }} ({{ $p->isLavage() ? 'Lavage' : 'Pressing' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Catégorie *</label>
                    <select name="categorie" id="edit_categorie" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Libellé *</label>
                <input type="text" name="libelle" id="edit_libelle" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Montant (FCFA) *</label>
                    <input type="number" name="montant" id="edit_montant" min="1" step="1" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Date *</label>
                    <input type="date" name="date_depense" id="edit_date_depense" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">N° Reçu / Réf.</label>
                    <input type="text" name="justificatif" id="edit_justificatif" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Notes</label>
                <textarea name="notes" id="edit_notes" rows="2" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" onclick="closeEditDepenseModal()" class="px-4 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300">Annuler</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-black dark:bg-white text-white dark:text-black text-xs font-bold">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateDepenseModal() {
        document.getElementById('modal-create-depense').classList.remove('hidden');
    }
    function closeCreateDepenseModal() {
        document.getElementById('modal-create-depense').classList.add('hidden');
    }
    function openEditDepenseModal(d) {
        const form = document.getElementById('edit_depense_form');
        form.action = "{{ url('comptable/depenses') }}/" + d.uuid;
        document.getElementById('edit_pressing_id').value = d.pressing_id;
        document.getElementById('edit_categorie').value = d.categorie;
        document.getElementById('edit_libelle').value = d.libelle;
        document.getElementById('edit_montant').value = d.montant;
        document.getElementById('edit_date_depense').value = d.date_depense;
        document.getElementById('edit_justificatif').value = d.justificatif || '';
        document.getElementById('edit_notes').value = d.notes || '';
        document.getElementById('modal-edit-depense').classList.remove('hidden');
    }
    function closeEditDepenseModal() {
        document.getElementById('modal-edit-depense').classList.add('hidden');
    }
</script>
@endsection
