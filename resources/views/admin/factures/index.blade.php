@extends('layouts.app', ['title' => 'Vue Consolidée des Factures'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Vue Consolidée des Factures</h1>
            <p class="text-xs text-slate-500 mt-1">
                Suivi centralisé des dépôts, préparations et retraits d'agences
            </p>
        </div>

        <!-- Metric summary pills -->
        <div class="flex items-center gap-3 bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs text-xs">
            <div>
                <span class="text-slate-400 uppercase text-[10px] font-semibold block">Total filtré</span>
                <span class="font-bold text-slate-900 font-mono text-sm">{{ $totalTickets }} ticket(s)</span>
            </div>
            <div class="w-px h-8 bg-slate-100"></div>
            <div>
                <span class="text-slate-400 uppercase text-[10px] font-semibold block">Montant cumulé</span>
                <span class="font-extrabold text-indigo-600 font-mono text-sm">{{ number_format($totalMontant, 2, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    <!-- Filters form -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.factures.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Numéro de ticket
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Ex: TK-2026-..."
                        class="w-full px-3.5 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                    >
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Agence de Pressing
                </label>
                <select name="pressing_id" class="w-full px-3.5 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition">
                    <option value="">Toutes les agences</option>
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ (string)$selectedPressingId === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->ville }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Statut du traitement
                </label>
                <select name="statut" class="w-full px-3.5 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition">
                    <option value="">Tous les statuts</option>
                    <option value="depose" {{ $selectedStatut === 'depose' ? 'selected' : '' }}>Déposé (En attente)</option>
                    <option value="pret" {{ $selectedStatut === 'pret' ? 'selected' : '' }}>Prêt (Non retiré)</option>
                    <option value="paye_retire" {{ $selectedStatut === 'paye_retire' ? 'selected' : '' }}>Payé & Retiré</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex-1 h-[38px] flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filtrer</span>
                </button>
                @if($search || $selectedPressingId || $selectedStatut)
                    <a href="{{ route('admin.factures.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition h-[38px] flex items-center">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Factures Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="py-3.5 px-6">Numéro Ticket</th>
                        <th class="py-3.5 px-6">Agence</th>
                        <th class="py-3.5 px-6">Agent / Caissier</th>
                        <th class="py-3.5 px-6">Date Dépôt</th>
                        <th class="py-3.5 px-6 text-center">Statut</th>
                        <th class="py-3.5 px-6 text-right">Montant Total</th>
                        <th class="py-3.5 px-6 text-right">Date Retrait</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($factures as $f)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <td class="py-4 px-6 font-mono font-bold text-indigo-600">
                                <a href="{{ route('admin.factures.show', $f) }}" class="hover:underline">
                                    {{ $f->num_ticket }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                <span class="font-semibold text-slate-900">{{ $f->pressing->nom }}</span>
                                <span class="text-slate-400 text-[11px]">({{ $f->pressing->ville }})</span>
                            </td>
                            <td class="py-4 px-6 text-slate-700 font-medium">{{ $f->user->name }}</td>
                            <td class="py-4 px-6 text-slate-500 font-mono text-[11px]">
                                {{ $f->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($f->statut === 'paye_retire')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Payé & Retiré
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
                            <td class="py-4 px-6 text-right font-mono font-bold text-slate-900">
                                {{ number_format((float) $f->montant_total, 2, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6 text-right font-mono text-[11px] text-slate-500">
                                {{ $f->paye_at ? $f->paye_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.factures.show', $f) }}" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                    <span>Détails</span>
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-xs text-slate-500">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-lg">
                                        🧾
                                    </div>
                                    <p class="font-medium text-slate-700">Aucune facture ne correspond aux filtres</p>
                                    <a href="{{ route('admin.factures.index') }}" class="inline-block text-indigo-600 font-semibold text-xs">
                                        Réinitialiser les filtres
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($factures->hasPages())
            <div class="p-4 border-t border-slate-100 bg-white">
                {{ $factures->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
