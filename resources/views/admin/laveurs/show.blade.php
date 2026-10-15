@extends('layouts.app', ['title' => 'Fiche Laveur : ' . $laveur->nom])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white">{{ $laveur->nom }}</h1>
                @if($laveur->actif)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-950">
                        <span class="w-1.5 h-1.5 rounded-full bg-white dark:bg-zinc-950"></span>
                        <span>Actif</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                        <span>Inactif</span>
                    </span>
                @endif
                <span class="px-2.5 py-0.5 text-[11px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-full">
                    {{ $laveur->telephone ?? 'Sans téléphone' }}
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Rattaché à : <strong class="text-zinc-900 dark:text-white">{{ $laveur->pressing->nom ?? 'Aucune station' }}</strong>
                ({{ $laveur->pressing && $laveur->pressing->isLavage() ? 'Station de Lavage' : 'Agence Pressing' }})
                &bull; Mode de rémunération :
                <span class="font-semibold text-zinc-900 dark:text-white">
                    @if($laveur->type_remuneration === 'pourcentage')
                        {{ number_format($laveur->valeur_remuneration, 0) }}% du montant de chaque lavage
                    @else
                        {{ number_format($laveur->valeur_remuneration, 0, ',', ' ') }} FCFA fixe par lavage
                    @endif
                </span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($laveur->telephone)
                @php
                    $cleanTel = preg_replace('/[^0-9]/', '', $laveur->telephone);
                    if (strlen($cleanTel) === 8) {
                        $cleanTel = '228' . $cleanTel;
                    }
                @endphp
                <a href="https://wa.me/{{ $cleanTel }}" target="_blank" class="px-4 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>Contacter via WhatsApp</span>
                </a>
            @endif
            <a href="{{ route('admin.laveurs.edit', $laveur) }}" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span>Modifier</span>
            </a>
            <a href="{{ route('admin.laveurs.index') }}" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                &larr; Retour
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if(session('status'))
        <div class="p-4 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-300 dark:border-zinc-700 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-zinc-900 dark:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-xs font-semibold text-zinc-900 dark:text-white">{{ session('status') }}</span>
        </div>
    @endif

    <!-- Global Stats KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Washes -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Lavages Effectués</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ $totalLavages }}</span>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">véhicules</span>
            </div>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Depuis l'enregistrement du laveur</p>
        </div>

        <!-- Total Revenue Generated -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total CA Généré</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black font-mono text-zinc-950 dark:text-white">{{ number_format($totalCaGenere, 0, ',', ' ') }}</span>
                <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">FCFA</span>
            </div>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Chiffre d'affaires encaissé en caisse</p>
        </div>

        <!-- Total Earnings / Commissions -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Commissions & Salaires Gagnés</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black font-mono text-zinc-950 dark:text-white">{{ number_format($totalCommissions, 0, ',', ' ') }}</span>
                <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">FCFA</span>
            </div>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Cumul de tous les mois confondus</p>
        </div>

        <!-- Solde Restant Dû -->
        <div class="bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 p-5 rounded-2xl border border-zinc-900 dark:border-zinc-200 shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-600">Solde Restant Dû</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black font-mono">{{ number_format($totalResteDu, 0, ',', ' ') }}</span>
                <span class="text-xs font-mono">FCFA</span>
            </div>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-600">
                @if($totalResteDu == 0)
                    &bull; Tous les mois sont intégralement soldés
                @else
                    &bull; Reliquat total à verser physiquement
                @endif
            </p>
        </div>
    </div>

    <!-- Monthly Payroll & Settlement History Section -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-zinc-950 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-zinc-900 dark:bg-white inline-block"></span>
                    <span>Historique Mensuel des Salaires & Émargements Physiques</span>
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Le décompte s'incrémente en temps réel à chaque lavage saisi. Le versement du salaire se fait physiquement en espèces de la main à la main. Cliquez sur « Marquer payé en espèces » pour solder le mois et remettre le reste à zéro.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                            <th class="py-3.5 px-6">Mois</th>
                            <th class="py-3.5 px-6 text-center">Lavages Réalisés</th>
                            <th class="py-3.5 px-6 text-right">CA Généré</th>
                            <th class="py-3.5 px-6 text-right font-bold">Commission Totale</th>
                            <th class="py-3.5 px-6 text-center">Statut Règlement</th>
                            <th class="py-3.5 px-6 text-right">Montant Versé</th>
                            <th class="py-3.5 px-6 text-right font-black">Reste à Payer</th>
                            <th class="py-3.5 px-6 text-right">Émargement Physique</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                        @forelse($historiqueMois as $item)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                                <td class="py-4 px-6 font-bold text-zinc-950 dark:text-white capitalize">
                                    <div class="font-black text-sm">{{ $item['mois_formate'] }}</div>
                                    <div class="text-[10px] font-mono text-zinc-400">{{ $item['mois'] }}</div>
                                </td>
                                <td class="py-4 px-6 text-center font-mono">
                                    <span class="px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 text-xs font-bold">
                                        {{ $item['nb_lavages'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-mono text-zinc-800 dark:text-zinc-200">
                                    {{ number_format($item['ca_genere'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ number_format($item['commission_due'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @if($item['est_paye'])
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-950">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>Soldé en espèces</span>
                                        </div>
                                        @if($item['paiement']?->date_paiement)
                                            <div class="text-[10px] text-zinc-500 mt-1">
                                                le {{ \Carbon\Carbon::parse($item['paiement']->date_paiement)->format('d/m/Y') }}
                                                @if($item['paiement']->payePar)
                                                    par <strong class="text-zinc-800 dark:text-zinc-200">{{ $item['paiement']->payePar->name }}</strong>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        @if($item['commission_due'] > 0)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                                                <span>En attente de versement</span>
                                            </span>
                                        @else
                                            <span class="text-zinc-400 dark:text-zinc-500 text-[11px] italic">Néant (0 FCFA)</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right font-mono text-zinc-800 dark:text-zinc-200">
                                    {{ number_format($item['montant_verse'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-4 px-6 text-right font-mono font-black text-xs {{ $item['reste_a_payer'] == 0 ? 'text-zinc-400 dark:text-zinc-500' : 'text-zinc-950 dark:text-white' }}">
                                    @if($item['est_paye'])
                                        <span class="text-zinc-400 dark:text-zinc-500">0 FCFA</span>
                                    @else
                                        <span>{{ number_format($item['reste_a_payer'], 0, ',', ' ') }} FCFA</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    @if($item['est_paye'])
                                        <form method="POST" action="{{ route('admin.laveurs.annuler-paiement', $laveur) }}" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="mois" value="{{ $item['mois'] }}">
                                            <button
                                                type="button"
                                                onclick="showConfirmModal({
                                                    title: 'Annuler l\'émargement du salaire',
                                                    message: 'Confirmez-vous l\'annulation du paiement de {{ addslashes($laveur->nom) }} pour {{ $item['mois_formate'] }} ? Le solde repassera en attente.',
                                                    confirmText: 'Confirmer l\'annulation',
                                                    type: 'danger',
                                                    form: this.closest('form')
                                                })"
                                                class="px-3 py-1.5 text-[11px] font-semibold text-zinc-500 hover:text-zinc-900 dark:hover:text-white underline transition cursor-pointer"
                                            >
                                                Annuler émargement
                                            </button>
                                        </form>
                                    @else
                                        @if($item['commission_due'] > 0)
                                            <form method="POST" action="{{ route('admin.laveurs.paiement', $laveur) }}" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="mois" value="{{ $item['mois'] }}">
                                                <input type="hidden" name="montant" value="{{ $item['commission_due'] }}">
                                                <button
                                                    type="button"
                                                    onclick="showConfirmModal({
                                                        title: 'Valider le paiement physique en espèces',
                                                        message: 'Confirmez-vous le versement physique de {{ number_format($item['commission_due'], 0, ',', ' ') }} FCFA en mains propres à {{ addslashes($laveur->nom) }} pour le mois de {{ $item['mois_formate'] }} ? Le solde restant dû sera immédiatement remis à zéro.',
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
                                            <span class="text-[11px] text-zinc-400 italic">Aucune commission</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Aucun historique de salaire disponible pour ce laveur.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Washes Stream Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-zinc-950 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-zinc-900 dark:bg-white inline-block"></span>
                    <span>Derniers Véhicules Pris en Charge ({{ $derniersLavages->count() }})</span>
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Traçabilité détaillée des lavages attribués à ce laveur
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                            <th class="py-3.5 px-6">N° Ticket</th>
                            <th class="py-3.5 px-6">Date & Heure</th>
                            <th class="py-3.5 px-6">Client</th>
                            <th class="py-3.5 px-6">Véhicule</th>
                            <th class="py-3.5 px-6">Station / Agence</th>
                            <th class="py-3.5 px-6 text-right">Montant Lavage</th>
                            <th class="py-3.5 px-6 text-right font-bold">Part Laveur</th>
                            <th class="py-3.5 px-6 text-center">Statut Ticket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                        @forelse($derniersLavages as $f)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                                <td class="py-4 px-6 font-mono font-black text-zinc-950 dark:text-white">
                                    <span class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                                        {{ $f->num_ticket }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-mono text-zinc-500 dark:text-zinc-400 text-[11px]">
                                    {{ $f->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-4 px-6 font-medium text-zinc-950 dark:text-white">
                                    @if($f->client)
                                        <a href="{{ route('admin.clients.show', $f->client) }}" class="font-bold hover:underline text-zinc-900 dark:text-white">
                                            {{ $f->client->nom }}
                                        </a>
                                        <div class="text-[10px] font-mono text-zinc-500">{{ $f->client->telephone }}</div>
                                    @else
                                        <div class="font-bold">{{ $f->client_nom }}</div>
                                        <div class="text-[10px] font-mono text-zinc-500">{{ $f->client_telephone }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if($f->vehicule)
                                        <span class="px-2 py-0.5 rounded-md font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 text-[11px]">
                                            {{ $f->vehicule->immatriculation }}
                                        </span>
                                        <div class="text-[10px] text-zinc-500 mt-0.5">{{ $f->vehicule->marque_modele }} ({{ $f->vehicule->type_vehicule }})</div>
                                    @else
                                        <span class="text-zinc-400 italic">Non spécifié</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-zinc-700 dark:text-zinc-300">
                                    {{ $f->pressing->nom ?? '—' }}
                                </td>
                                <td class="py-4 px-6 text-right font-mono text-zinc-800 dark:text-zinc-200">
                                    {{ number_format($f->montant_total, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-zinc-950 dark:text-white">
                                    {{ number_format($laveur->calculerCommission($f), 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($f->statut === 'retire')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-950">
                                            Retiré
                                        </span>
                                    @elseif($f->statut === 'pret')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                                            Prêt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                            {{ ucfirst($f->statut) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Aucun lavage enregistré pour ce laveur pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
