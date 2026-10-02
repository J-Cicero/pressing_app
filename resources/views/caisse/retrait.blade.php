@extends('layouts.app', ['title' => 'Retraits & Encaissement'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-[#374151]/20 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#000]">GESTION DES RETRAITS & ENCAISSEMENT</h1>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-1">
                Agence : {{ $pressing->nom }} &bull; Recherche par N° de ticket ou téléphone client
            </p>
        </div>
        <div>
            <a href="{{ route('caisse.depot') }}" class="px-4 py-2 bg-[#000] text-[#FFF] text-xs font-bold uppercase tracking-wider hover:bg-[#374151] transition">
                + Nouveau Dépôt
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="my-6 bg-[#FFF] p-4 border border-[#374151]/20 shadow-sm">
        <form method="GET" action="{{ route('caisse.retrait') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-[#374151] mb-1">
                    Recherche par Ticket, Téléphone ou Nom
                </label>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Ex: TCK-2026..., 97000000, Koffi..."
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/30 text-xs font-mono text-[#000] focus:outline-none focus:border-[#000]"
                >
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-[#374151] mb-1">
                    Filtrer par Statut
                </label>
                <select name="statut" class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/30 text-xs text-[#000] focus:outline-none focus:border-[#000]">
                    <option value="">Tous les statuts</option>
                    <option value="depose" {{ $selectedStatut === 'depose' ? 'selected' : '' }}>
                        Déposés (En cours de nettoyage) ({{ $countDepose }})
                    </option>
                    <option value="pret" {{ $selectedStatut === 'pret' ? 'selected' : '' }}>
                        Prêts au retrait (Non retirés) ({{ $countPret }})
                    </option>
                    <option value="paye_retire" {{ $selectedStatut === 'paye_retire' ? 'selected' : '' }}>
                        Payés & Retirés (Clôturés) ({{ $countPayeRetire }})
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition flex-1 h-[34px]">
                    Rechercher
                </button>
                @if($search || $selectedStatut)
                    <a href="{{ route('caisse.retrait') }}" class="px-3 py-2 bg-[#F3F4F6] text-[#374151] border border-[#374151]/30 text-xs font-semibold uppercase tracking-wider hover:bg-[#FFF] transition h-[34px] flex items-center">
                        Réinit.
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Status Tabs / Counters -->
    <div class="flex overflow-x-auto gap-2 mb-6 text-xs">
        <a href="{{ route('caisse.retrait') }}"
           class="px-3 py-2 border {{ empty($selectedStatut) ? 'bg-[#000] text-[#FFF] border-[#000]' : 'bg-[#FFF] text-[#374151] border-[#374151]/20 hover:bg-[#F3F4F6]' }} uppercase tracking-wider font-semibold whitespace-nowrap">
            Tous les tickets
        </a>
        <a href="{{ route('caisse.retrait', ['statut' => 'depose']) }}"
           class="px-3 py-2 border {{ $selectedStatut === 'depose' ? 'bg-[#000] text-[#FFF] border-[#000]' : 'bg-[#FFF] text-[#374151] border-[#374151]/20 hover:bg-[#F3F4F6]' }} uppercase tracking-wider font-semibold whitespace-nowrap">
            Déposés ({{ $countDepose }})
        </a>
        <a href="{{ route('caisse.retrait', ['statut' => 'pret']) }}"
           class="px-3 py-2 border {{ $selectedStatut === 'pret' ? 'bg-[#000] text-[#FFF] border-[#000]' : 'bg-[#FFF] text-[#374151] border-[#374151]/20 hover:bg-[#F3F4F6]' }} uppercase tracking-wider font-semibold whitespace-nowrap">
            Prêts au Retrait ({{ $countPret }})
        </a>
        <a href="{{ route('caisse.retrait', ['statut' => 'paye_retire']) }}"
           class="px-3 py-2 border {{ $selectedStatut === 'paye_retire' ? 'bg-[#000] text-[#FFF] border-[#000]' : 'bg-[#FFF] text-[#374151] border-[#374151]/20 hover:bg-[#F3F4F6]' }} uppercase tracking-wider font-semibold whitespace-nowrap">
            Payés & Retirés ({{ $countPayeRetire }})
        </a>
    </div>

    <!-- Table of tickets -->
    <div class="bg-[#FFF] border border-[#374151]/20 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F3F4F6] border-b border-[#374151]/20 text-[10px] uppercase tracking-wider text-[#374151]">
                        <th class="py-3 px-4 font-semibold">N° Ticket</th>
                        <th class="py-3 px-4 font-semibold">Client</th>
                        <th class="py-3 px-4 font-semibold">Dépôt & Prévu</th>
                        <th class="py-3 px-4 font-semibold">Articles</th>
                        <th class="py-3 px-4 font-semibold text-center">Statut</th>
                        <th class="py-3 px-4 font-semibold text-right">Montant (100% dû)</th>
                        <th class="py-3 px-4 font-semibold text-right">Actions Guichet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#374151]/10">
                    @forelse($factures as $f)
                        <tr class="hover:bg-[#F3F4F6]/50 transition">
                            <!-- Ticket -->
                            <td class="py-4 px-4 font-mono font-bold text-[#000]">
                                <a href="{{ route('caisse.factures.print', $f) }}" target="_blank" class="hover:underline">
                                    {{ $f->num_ticket }}
                                </a>
                            </td>

                            <!-- Client -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-[#000]">{{ $f->client_nom ?? 'Client de passage' }}</div>
                                <div class="font-mono text-[11px] text-[#374151]">{{ $f->client_telephone ?? 'Sans téléphone' }}</div>
                            </td>

                            <!-- Dates -->
                            <td class="py-4 px-4 text-[#374151] font-mono text-[11px]">
                                <div>Dépôt : {{ $f->created_at->format('d/m/Y') }}</div>
                                <div>Prévu : <span class="font-bold text-[#000]">{{ $f->date_retrait_prevue ? $f->date_retrait_prevue->format('d/m/Y') : '—' }}</span></div>
                            </td>

                            <!-- Articles count / detail -->
                            <td class="py-4 px-4 text-[#374151]">
                                <span class="font-semibold text-[#000]">{{ $f->ligneFactures->sum('quantite') }} pièce(s)</span>
                                <div class="text-[11px] truncate max-w-xs">
                                    {{ $f->ligneFactures->map(fn($l) => ($l->service ? $l->service->designation : 'Article') . ' (x' . $l->quantite . ')')->join(', ') }}
                                </div>
                            </td>

                            <!-- Statut Badge -->
                            <td class="py-4 px-4 text-center">
                                @if($f->statut === 'paye_retire')
                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-[#000] text-[#FFF]">
                                        Payé & Retiré
                                    </span>
                                @elseif($f->statut === 'pret')
                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-[#FFF] text-[#000] border border-[#000]">
                                        Prêt pour Retrait
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-[#F3F4F6] text-[#374151] border border-[#374151]/30">
                                        En Cours (Déposé)
                                    </span>
                                @endif
                            </td>

                            <!-- Montant -->
                            <td class="py-4 px-4 text-right font-mono font-bold text-sm text-[#000]">
                                {{ number_format((float) $f->montant_total, 2, ',', ' ') }} FCFA
                            </td>

                            <!-- Actions Guichet -->
                            <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                @if($f->statut === 'depose')
                                    <!-- Action 1 : Passer à "Prêt" -->
                                    <form method="POST" action="{{ route('caisse.factures.pret', $f) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1.5 text-[11px] font-semibold uppercase tracking-wider bg-[#F3F4F6] border border-[#374151]/40 text-[#000] hover:bg-[#FFF] transition"
                                            title="Indiquer que les vêtements sont repassés et disponibles"
                                        >
                                            Marquer Prêt
                                        </button>
                                    </form>

                                    <!-- Action 2 : Encaisser direct -->
                                    <form method="POST" action="{{ route('caisse.factures.encaisser', $f) }}" class="inline-block" onsubmit="return confirm('Encaisser 100% du montant ({{ number_format($f->montant_total, 0, ',', ' ') }} FCFA) et remettre les vêtements au client ?');">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider bg-[#000] text-[#FFF] hover:bg-[#374151] transition"
                                        >
                                            Encaisser & Restituer
                                        </button>
                                    </form>
                                @elseif($f->statut === 'pret')
                                    <!-- Action 2 : Encaisser & Restituer -->
                                    <form method="POST" action="{{ route('caisse.factures.encaisser', $f) }}" class="inline-block" onsubmit="return confirm('Encaisser 100% du montant ({{ number_format($f->montant_total, 0, ',', ' ') }} FCFA) et remettre les vêtements au client ?');">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider bg-[#000] text-[#FFF] hover:bg-[#374151] transition"
                                        >
                                            Encaisser & Restituer
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-[#374151] font-mono mr-2">
                                        Réglé le {{ $f->paye_at ? $f->paye_at->format('d/m H:i') : '' }}
                                    </span>
                                @endif

                                <a href="{{ route('caisse.factures.print', $f) }}" target="_blank" class="inline-block px-2 py-1.5 text-[11px] font-mono uppercase tracking-wider border border-[#374151]/30 text-[#374151] hover:text-[#000] hover:bg-[#F3F4F6] transition" title="Réimprimer le ticket 80mm">
                                    Reçu 80mm
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-[#374151]">
                                Aucun ticket ne correspond à votre recherche dans cette agence.
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
