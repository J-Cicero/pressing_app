@extends('layouts.app', ['title' => 'Vue Consolidée des Factures'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-[#374151]/20 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#000]">VUE CONSOLIDÉE DES FACTURES</h1>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-1">
                Suivi centralisé des dépôts, préparations et retraits d'agences
            </p>
        </div>

        <!-- Metric summary pills -->
        <div class="flex items-center space-x-3 bg-[#FFF] p-3 border border-[#374151]/20 text-xs">
            <div>
                <span class="text-[#374151] uppercase text-[10px]">Total filtré :</span>
                <span class="font-bold text-[#000] font-mono ml-1">{{ $totalTickets }} ticket(s)</span>
            </div>
            <span class="text-[#374151]/30">•</span>
            <div>
                <span class="text-[#374151] uppercase text-[10px]">Montant cumulé :</span>
                <span class="font-bold text-[#000] font-mono ml-1">{{ number_format($totalMontant, 2, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    <!-- Filters form -->
    <div class="my-6 bg-[#FFF] p-4 border border-[#374151]/20 shadow-sm">
        <form method="GET" action="{{ route('admin.factures.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-[#374151] mb-1">
                    Numéro de ticket
                </label>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Ex: TK-2026-..."
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/30 text-xs font-mono text-[#000] focus:outline-none focus:border-[#000]"
                >
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-[#374151] mb-1">
                    Agence de Pressing
                </label>
                <select name="pressing_id" class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/30 text-xs text-[#000] focus:outline-none focus:border-[#000]">
                    <option value="">Toutes les agences</option>
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ (string)$selectedPressingId === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->ville }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-[#374151] mb-1">
                    Statut du traitement
                </label>
                <select name="statut" class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/30 text-xs text-[#000] focus:outline-none focus:border-[#000]">
                    <option value="">Tous les statuts</option>
                    <option value="depose" {{ $selectedStatut === 'depose' ? 'selected' : '' }}>Déposé (En attente)</option>
                    <option value="pret" {{ $selectedStatut === 'pret' ? 'selected' : '' }}>Prêt (Non retiré)</option>
                    <option value="paye_retire" {{ $selectedStatut === 'paye_retire' ? 'selected' : '' }}>Payé & Retiré</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition flex-1 h-[34px]">
                    Appliquer
                </button>
                @if($search || $selectedPressingId || $selectedStatut)
                    <a href="{{ route('admin.factures.index') }}" class="px-3 py-2 bg-[#F3F4F6] text-[#374151] border border-[#374151]/30 text-xs font-semibold uppercase tracking-wider hover:bg-[#FFF] transition h-[34px] flex items-center">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Factures Table -->
    <div class="bg-[#FFF] border border-[#374151]/20 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F3F4F6] border-b border-[#374151]/20 text-[11px] uppercase tracking-wider text-[#374151]">
                        <th class="py-3 px-6 font-semibold">Numéro Ticket</th>
                        <th class="py-3 px-6 font-semibold">Agence</th>
                        <th class="py-3 px-6 font-semibold">Agent / Caissier</th>
                        <th class="py-3 px-6 font-semibold">Date Dépôt</th>
                        <th class="py-3 px-6 font-semibold text-center">Statut</th>
                        <th class="py-3 px-6 font-semibold text-right">Montant Total</th>
                        <th class="py-3 px-6 font-semibold text-right">Date Retrait</th>
                        <th class="py-3 px-6 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#374151]/10 text-xs">
                    @forelse($factures as $f)
                        <tr class="hover:bg-[#F3F4F6]/50 transition">
                            <td class="py-4 px-6 font-mono font-bold text-[#000]">
                                <a href="{{ route('admin.factures.show', $f) }}" class="hover:underline">
                                    {{ $f->num_ticket }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-[#374151]">
                                <span class="font-medium text-[#000]">{{ $f->pressing->nom }}</span>
                                <span class="text-[11px]">({{ $f->pressing->ville }})</span>
                            </td>
                            <td class="py-4 px-6 text-[#374151]">{{ $f->user->name }}</td>
                            <td class="py-4 px-6 text-[#374151] font-mono text-[11px]">
                                {{ $f->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($f->statut === 'paye_retire')
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-[#000] text-[#FFF]">
                                        Payé & Retiré
                                    </span>
                                @elseif($f->statut === 'pret')
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-[#FFF] text-[#000] border border-[#374151]">
                                        Prêt
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-[#F3F4F6] text-[#374151] border border-[#374151]/30">
                                        Déposé
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-[#000]">
                                {{ number_format((float) $f->montant_total, 2, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6 text-right font-mono text-[11px] text-[#374151]">
                                {{ $f->paye_at ? $f->paye_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.factures.show', $f) }}" class="inline-block px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider border border-[#374151] text-[#000] hover:bg-[#000] hover:text-[#FFF] transition">
                                    Détails &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-xs text-[#374151]">
                                Aucune facture ne correspond aux filtres appliqués.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($factures->hasPages())
            <div class="p-4 border-t border-[#374151]/10 bg-[#FFF]">
                {{ $factures->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
