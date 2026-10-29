@extends('layouts.app', ['title' => 'Règles de Fidélité'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white">Règles de Fidélité</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-full">
                    {{ $regles->total() }} règle(s)
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Paliers de passage en caisse et avantages clients automatiques (Pressing & Station de Lavage)
            </p>
        </div>
        <div>
            <a href="{{ route('admin.fidelite.create') }}" class="px-4 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-xs transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouvelle règle</span>
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

    <!-- Cards Grid / Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <th class="py-3.5 px-6">Palier (Visites)</th>
                        <th class="py-3.5 px-6">Portée</th>
                        <th class="py-3.5 px-6">Type d'avantage</th>
                        <th class="py-3.5 px-6">Valeur</th>
                        <th class="py-3.5 px-6">Quota & Durée</th>
                        <th class="py-3.5 px-6">Description client</th>
                        <th class="py-3.5 px-6 text-center">Statut</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                    @forelse($regles as $regle)
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition duration-150">
                            <td class="py-4 px-6 font-mono font-black text-sm text-zinc-950 dark:text-white">
                                <span class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                                    {{ $regle->seuil_visites }} passage(s)
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($regle->type_activite === 'lavage')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-900 text-white dark:bg-white dark:text-zinc-950">
                                        Lavage
                                    </span>
                                @elseif($regle->type_activite === 'pressing')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700">
                                        Pressing
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                        Global
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-medium text-zinc-800 dark:text-zinc-200">
                                @if($regle->type_avantage === 'pourcentage')
                                    Remise en %
                                @elseif($regle->type_avantage === 'montant_fixe')
                                    Remise Fixe
                                @elseif($regle->type_avantage === 'service_offert')
                                    Prestation Offerte
                                @else
                                    Cadeau / Privilège
                                @endif
                            </td>
                            <td class="py-4 px-6 font-mono font-bold text-zinc-900 dark:text-zinc-100">
                                @if($regle->type_avantage === 'pourcentage')
                                    -{{ number_format($regle->valeur_avantage, 0) }}%
                                @elseif($regle->type_avantage === 'montant_fixe')
                                    -{{ number_format($regle->valeur_avantage, 0, ',', ' ') }} FCFA
                                @else
                                    Offert
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-zinc-900 dark:text-white">{{ $regle->nombre_utilisations ?? 1 }} utilisation(s)</div>
                                <div class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                    @if($regle->duree_jours)
                                        Durée limitée : {{ $regle->duree_jours }} jour(s)
                                    @else
                                        Sans limite de durée
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-zinc-600 dark:text-zinc-400 max-w-xs truncate">
                                {{ $regle->description }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($regle->actif)
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
                                    <a href="{{ route('admin.fidelite.edit', $regle) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition">
                                        Modifier
                                    </a>

                                    <form method="POST" action="{{ route('admin.fidelite.destroy', $regle) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            onclick="showConfirmModal({
                                                title: 'Supprimer la règle de fidélité',
                                                message: 'Confirmez-vous la suppression du palier de fidélité à {{ $regle->seuil_visites }} visites ({{ addslashes($regle->description) }}) ?',
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
                            <td colspan="7" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                        <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V4a2 2 0 10-2 2h2m0 13a4 4 0 01-4-4V7a4 4 0 014-4h4a4 4 0 014 4v10a4 4 0 01-4 4z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Aucune règle de fidélité paramétrée</p>
                                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500">Configurez des avantages automatiques pour récompenser les clients réguliers.</p>
                                    <a href="{{ route('admin.fidelite.create') }}" class="inline-flex items-center px-3.5 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition">
                                        Créer une règle
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($regles->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $regles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
