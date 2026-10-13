@extends('layouts.app', ['title' => 'Suivi des Laveurs & Commissions'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white">Commissions & Suivi Laveurs</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-full">
                    {{ $pressing->nom }}
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Calcul journalier du reliquat et de la rémunération due à chaque laveur de terrain
            </p>
        </div>

        <!-- Date selector form -->
        <form method="GET" action="{{ route('caisse.laveurs.index') }}" class="flex items-center gap-2">
            <input
                type="date"
                name="date"
                value="{{ $date }}"
                class="px-3.5 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
            >
            <button type="submit" class="px-4 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition shadow-xs">
                Actualiser
            </button>
        </form>
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
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Total encaissé / engagé</p>
        </div>

        <div class="bg-black text-white dark:bg-white dark:text-black p-5 rounded-2xl border border-zinc-900 dark:border-zinc-200 shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider opacity-75">Reliquat Dû aux Laveurs</span>
            <div class="text-3xl font-black font-mono">{{ number_format($totalJourCommission, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
            <p class="text-[11px] opacity-75">Commissions à verser aujourd'hui</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Part Station / Caisse</span>
            <div class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ number_format($totalJourCa - $totalJourCommission, 0, ',', ' ') }} <span class="text-xs">FCFA</span></div>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Marge brute agence</p>
        </div>
    </div>

    <!-- Washers Breakdown Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
            <h2 class="text-sm font-bold text-zinc-950 dark:text-white">Décompte Individuel par Laveur</h2>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ count($recap) }} laveur(s) configuré(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <th class="py-3.5 px-6">Laveur</th>
                        <th class="py-3.5 px-6">Téléphone</th>
                        <th class="py-3.5 px-6">Règle de Rémunération</th>
                        <th class="py-3.5 px-6 text-center">Véhicules Traités</th>
                        <th class="py-3.5 px-6 text-right">CA Généré</th>
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
                                @if($row['laveur']->type_remuneration === 'pourcentage')
                                    <span class="font-mono font-bold">{{ number_format($row['laveur']->valeur_remuneration, 0) }}%</span> du montant
                                @else
                                    <span class="font-mono font-bold">{{ number_format($row['laveur']->valeur_remuneration, 0, ',', ' ') }} FCFA</span> / véhicule
                                @endif
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
@endsection
