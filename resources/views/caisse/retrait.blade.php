@extends('layouts.app', ['title' => 'Retraits & Encaissement'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">GESTION DES RETRAITS & ENCAISSEMENT</h1>
                <span class="px-3 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                    📍 {{ $pressing->nom }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Recherche par N° de ticket ou téléphone client & encaissement lors de la restitution
            </p>
        </div>
        <div>
            <a href="{{ route('caisse.depot') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition duration-150 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouveau Dépôt</span>
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('caisse.retrait') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Recherche par Ticket, Téléphone ou Nom
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Ex: TCK-2026..., 90000000, Koffi..."
                        class="w-full pl-9 pr-4 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                    >
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Filtrer par Statut
                </label>
                <select name="statut" class="w-full px-3.5 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition">
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
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex-1 h-[38px] flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Rechercher</span>
                </button>
                @if($search || $selectedStatut)
                    <a href="{{ route('caisse.retrait') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition h-[38px] flex items-center">
                        Réinit.
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Status Tabs / Counters -->
    <div class="flex overflow-x-auto gap-2 text-xs">
        <a href="{{ route('caisse.retrait') }}"
           class="px-4 py-2 rounded-xl border transition font-semibold whitespace-nowrap {{ empty($selectedStatut) ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Tous les tickets
        </a>
        <a href="{{ route('caisse.retrait', ['statut' => 'depose']) }}"
           class="px-4 py-2 rounded-xl border transition font-semibold whitespace-nowrap flex items-center gap-2 {{ $selectedStatut === 'depose' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            <span>Déposés</span>
            <span class="px-2 py-0.2 rounded-full text-[10px] {{ $selectedStatut === 'depose' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }} font-bold">{{ $countDepose }}</span>
        </a>
        <a href="{{ route('caisse.retrait', ['statut' => 'pret']) }}"
           class="px-4 py-2 rounded-xl border transition font-semibold whitespace-nowrap flex items-center gap-2 {{ $selectedStatut === 'pret' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            <span>Prêts au Retrait</span>
            <span class="px-2 py-0.2 rounded-full text-[10px] {{ $selectedStatut === 'pret' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800' }} font-bold">{{ $countPret }}</span>
        </a>
        <a href="{{ route('caisse.retrait', ['statut' => 'paye_retire']) }}"
           class="px-4 py-2 rounded-xl border transition font-semibold whitespace-nowrap flex items-center gap-2 {{ $selectedStatut === 'paye_retire' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            <span>Payés & Retirés</span>
            <span class="px-2 py-0.2 rounded-full text-[10px] {{ $selectedStatut === 'paye_retire' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }} font-bold">{{ $countPayeRetire }}</span>
        </a>
    </div>

    <!-- Table of tickets -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="py-3.5 px-4">N° Ticket</th>
                        <th class="py-3.5 px-4">Client</th>
                        <th class="py-3.5 px-4">Dépôt & Prévu</th>
                        <th class="py-3.5 px-4">Articles</th>
                        <th class="py-3.5 px-4 text-center">Statut</th>
                        <th class="py-3.5 px-4 text-right">Montant (100% dû)</th>
                        <th class="py-3.5 px-4 text-right">Actions Guichet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($factures as $f)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <!-- Ticket -->
                            <td class="py-4 px-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('caisse.factures.print', $f) }}" target="_blank" class="hover:underline">
                                    {{ $f->num_ticket }}
                                </a>
                            </td>

                            <!-- Client -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-900">{{ $f->client_nom ?? 'Client de passage' }}</div>
                                <div class="font-mono text-[11px] text-slate-400">📞 {{ $f->client_telephone ?? 'Sans téléphone' }}</div>
                            </td>

                            <!-- Dates -->
                            <td class="py-4 px-4 text-slate-500 font-mono text-[11px]">
                                <div>Dépôt : {{ $f->created_at->format('d/m/Y') }}</div>
                                <div>Prévu : <span class="font-bold text-slate-900">{{ $f->date_retrait_prevue ? $f->date_retrait_prevue->format('d/m/Y') : '—' }}</span></div>
                            </td>

                            <!-- Articles count / detail -->
                            <td class="py-4 px-4 text-slate-600">
                                <span class="font-semibold text-slate-900">{{ $f->ligneFactures->sum('quantite') }} pièce(s)</span>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs">
                                    {{ $f->ligneFactures->map(fn($l) => ($l->service ? $l->service->designation : 'Article') . ' (x' . $l->quantite . ')')->join(', ') }}
                                </div>
                            </td>

                            <!-- Statut Badge -->
                            <td class="py-4 px-4 text-center">
                                @if($f->statut === 'paye_retire')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Payé & Retiré
                                    </span>
                                @elseif($f->statut === 'pret')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Prêt pour Retrait
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        En Cours (Déposé)
                                    </span>
                                @endif
                            </td>

                            <!-- Montant -->
                            <td class="py-4 px-4 text-right font-mono font-bold text-sm text-slate-900">
                                {{ number_format((float) $f->montant_total, 2, ',', ' ') }} FCFA
                            </td>

                            <!-- Actions Guichet -->
                            <td class="py-4 px-4 text-right space-x-1.5 whitespace-nowrap">
                                @if($f->statut === 'depose')
                                    <!-- Action 1 : Passer à "Prêt" -->
                                    <form method="POST" action="{{ route('caisse.factures.pret', $f) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 text-[11px] font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer"
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
                                            class="px-3 py-1.5 text-[11px] font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-sm cursor-pointer"
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
                                            class="px-3 py-1.5 text-[11px] font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-sm cursor-pointer"
                                        >
                                            Encaisser & Restituer
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-emerald-700 font-mono font-semibold mr-2">
                                        ✓ Réglé {{ $f->paye_at ? $f->paye_at->format('d/m H:i') : '' }}
                                    </span>
                                @endif

                                <a href="{{ route('caisse.factures.print', $f) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Réimprimer le ticket 80mm">
                                    <span>🖨️ Reçu</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-slate-500">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-lg">
                                        🔍
                                    </div>
                                    <p class="font-medium text-slate-700">Aucun ticket correspondant trouvé</p>
                                    <a href="{{ route('caisse.retrait') }}" class="inline-block text-indigo-600 font-semibold text-xs">
                                        Réinitialiser la recherche
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
