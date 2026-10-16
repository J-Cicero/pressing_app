@extends('layouts.app', ['title' => 'Retraits Vêtements - ' . $pressing->nom])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- Header Pure Pressing -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-black tracking-tight text-zinc-950 dark:text-white uppercase">
                    Gestion des Retraits & Encaissement
                </h1>
                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-black dark:bg-white text-white dark:text-black flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>{{ $pressing->nom }}</span>
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Recherche par N° de ticket, téléphone ou nom & encaissement lors du retrait des vêtements
            </p>
        </div>
        <div>
            <a href="{{ route('caisse.depot') }}" class="px-3.5 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-xs transition duration-150 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouveau Dépôt Vêtements</span>
            </a>
        </div>
    </div>

    <!-- WhatsApp Alert Banner if just marked ready -->
    @if(session('whatsapp_pret_url'))
        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-black text-white dark:bg-white dark:text-black flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-zinc-950 dark:text-white">{{ session('status') }}</div>
                    <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Prévenez immédiatement le client par message WhatsApp prêt à envoyer.</div>
                </div>
            </div>
            <a href="{{ session('whatsapp_pret_url') }}" target="_blank" class="px-4 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition flex items-center justify-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Notifier le client sur WhatsApp</span>
            </a>
        </div>
    @elseif(session('status'))
        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl text-xs font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-xs">
        <form method="GET" action="{{ route('caisse.retrait') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    Recherche par Ticket, Nom ou Téléphone
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Ex: TCK-2026..., 90000000, Koffi..."
                        class="w-full pl-9 pr-3.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                    >
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    Filtrer par Statut
                </label>
                <select name="statut" class="w-full px-3.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition">
                    <option value="">Tous les statuts</option>
                    <option value="depose" {{ $selectedStatut === 'depose' ? 'selected' : '' }}>
                        Déposés (En cours) ({{ $countDepose }})
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
                <button type="submit" class="px-4 py-1.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-xs transition flex-1 h-[34px] flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Rechercher</span>
                </button>
                @if($search || $selectedStatut)
                    <a href="{{ route('caisse.retrait') }}" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-semibold transition h-[34px] flex items-center border border-zinc-200 dark:border-zinc-700">
                        Réinit.
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Status Tabs / Counters -->
    <div class="flex overflow-x-auto gap-2 text-xs">
        <a href="{{ route('caisse.retrait') }}"
           class="px-3.5 py-1.5 rounded-xl border transition font-bold whitespace-nowrap {{ empty($selectedStatut) ? 'bg-black text-white border-black dark:bg-white dark:text-black dark:border-white shadow-xs' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-black dark:hover:text-white' }}">
            Tous les dépôts
        </a>
        <a href="{{ route('caisse.retrait', ['statut' => 'depose']) }}"
           class="px-3.5 py-1.5 rounded-xl border transition font-bold whitespace-nowrap flex items-center gap-2 {{ $selectedStatut === 'depose' ? 'bg-black text-white border-black dark:bg-white dark:text-black dark:border-white shadow-xs' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-black dark:hover:text-white' }}">
            <span>Déposés (En cours)</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $selectedStatut === 'depose' ? 'bg-white text-black dark:bg-black dark:text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700' }} font-mono font-bold">{{ $countDepose }}</span>
        </a>
        <a href="{{ route('caisse.retrait', ['statut' => 'pret']) }}"
           class="px-3.5 py-1.5 rounded-xl border transition font-bold whitespace-nowrap flex items-center gap-2 {{ $selectedStatut === 'pret' ? 'bg-black text-white border-black dark:bg-white dark:text-black dark:border-white shadow-xs' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-black dark:hover:text-white' }}">
            <span>Prêts au Retrait</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $selectedStatut === 'pret' ? 'bg-white text-black dark:bg-black dark:text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700' }} font-mono font-bold">{{ $countPret }}</span>
        </a>
        <a href="{{ route('caisse.retrait', ['statut' => 'paye_retire']) }}"
           class="px-3.5 py-1.5 rounded-xl border transition font-bold whitespace-nowrap flex items-center gap-2 {{ $selectedStatut === 'paye_retire' ? 'bg-black text-white border-black dark:bg-white dark:text-black dark:border-white shadow-xs' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-black dark:hover:text-white' }}">
            <span>Payés & Retirés</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $selectedStatut === 'paye_retire' ? 'bg-white text-black dark:bg-black dark:text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700' }} font-mono font-bold">{{ $countPayeRetire }}</span>
        </a>
    </div>

    <!-- Table of tickets -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[10px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <th class="py-3 px-4">N° Ticket</th>
                        <th class="py-3 px-4">Client & Contact</th>
                        <th class="py-3 px-4">Articles & Prestations</th>
                        <th class="py-3 px-4">Dépôt & Prévu</th>
                        <th class="py-3 px-4 text-center">Statut</th>
                        <th class="py-3 px-4 text-right">Montant (100% dû)</th>
                        <th class="py-3 px-4 text-right">Actions Guichet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                    @forelse($factures as $f)
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition duration-150">
                            <!-- Ticket -->
                            <td class="py-3.5 px-4 font-mono font-bold text-zinc-950 dark:text-white">
                                <a href="{{ route('caisse.factures.print', $f) }}" target="_blank" class="hover:underline">
                                    {{ $f->num_ticket }}
                                </a>
                            </td>

                            <!-- Client -->
                            <td class="py-3.5 px-4 text-zinc-600 dark:text-zinc-300">
                                <div class="font-bold text-zinc-950 dark:text-white">{{ $f->client_nom ?? 'Client de passage' }}</div>
                                <div class="font-mono text-[11px] text-zinc-500 dark:text-zinc-400">{{ $f->client_telephone ?? 'Sans téléphone' }}</div>
                                @if($f->client_email)
                                    <div class="text-[10px] text-zinc-400 truncate">{{ $f->client_email }}</div>
                                @endif
                            </td>

                            <!-- Articles -->
                            <td class="py-3.5 px-4 text-zinc-600 dark:text-zinc-300">
                                <div class="text-[11px] text-zinc-700 dark:text-zinc-300 font-medium max-w-xs">
                                    {{ $f->ligneFactures->map(fn($l) => ($l->service ? $l->service->designation : 'Article') . ' (x' . $l->quantite . ')')->join(', ') }}
                                </div>
                            </td>

                            <!-- Dates -->
                            <td class="py-3.5 px-4 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">
                                <div>Dépôt : {{ $f->created_at->format('d/m/Y') }}</div>
                                <div>Prévu : <span class="font-bold text-zinc-950 dark:text-white">{{ $f->date_retrait_prevue ? $f->date_retrait_prevue->format('d/m/Y') : '—' }}</span></div>
                            </td>

                            <!-- Statut Badge -->
                            <td class="py-3.5 px-4 text-center">
                                @if($f->statut === 'paye_retire')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-black text-white dark:bg-white dark:text-black">
                                        Payé & Retiré
                                    </span>
                                @elseif($f->statut === 'pret')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-white">
                                        Prêt pour Retrait
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                        En Cours (Déposé)
                                    </span>
                                @endif
                            </td>

                            <!-- Montant -->
                            <td class="py-3.5 px-4 text-right font-mono font-black text-zinc-950 dark:text-white">
                                {{ number_format((float) $f->montant_total, 0, ',', ' ') }} FCFA
                            </td>

                            <!-- Actions Guichet -->
                            <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                @if($f->statut === 'depose')
                                    <!-- Passer à Prêt -->
                                    <form method="POST" action="{{ route('caisse.factures.pret', $f) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition cursor-pointer"
                                            title="Indiquer que les vêtements sont repassés et prêts"
                                        >
                                            Marquer Prêt
                                        </button>
                                    </form>

                                    <!-- Encaisser direct -->
                                    <form method="POST" action="{{ route('caisse.factures.encaisser', $f) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="button"
                                            onclick="showConfirmModal({
                                                title: 'Validation de l\'encaissement',
                                                message: 'Confirmez-vous l\'encaissement intégral de {{ number_format($f->montant_total, 0, ',', ' ') }} FCFA et le retrait des vêtements pour le ticket {{ $f->num_ticket }} ?',
                                                confirmText: 'Encaisser & Valider',
                                                type: 'info',
                                                form: this.closest('form')
                                            })"
                                            class="px-3 py-1 text-[11px] font-bold rounded-lg bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black transition shadow-xs cursor-pointer"
                                        >
                                            Encaisser
                                        </button>
                                    </form>
                                @elseif($f->statut === 'pret')
                                    <!-- Encaisser & Retirer -->
                                    <form method="POST" action="{{ route('caisse.factures.encaisser', $f) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="button"
                                            onclick="showConfirmModal({
                                                title: 'Retrait & Encaissement',
                                                message: 'Confirmez-vous la perception de {{ number_format($f->montant_total, 0, ',', ' ') }} FCFA pour la remise des vêtements du ticket {{ $f->num_ticket }} au client ?',
                                                confirmText: 'Encaisser & Remettre',
                                                type: 'info',
                                                form: this.closest('form')
                                            })"
                                            class="px-3 py-1 text-[11px] font-bold rounded-lg bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black transition shadow-xs cursor-pointer"
                                        >
                                            Encaisser & Remettre
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono font-semibold mr-1.5">
                                        Réglé {{ $f->paye_at ? $f->paye_at->format('d/m H:i') : '' }}
                                    </span>
                                @endif

                                <!-- WhatsApp Quick Action -->
                                @if(!empty($f->whatsapp_url))
                                    <a href="{{ $f->whatsapp_url }}" target="_blank" class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition" title="Contacter sur WhatsApp">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </a>
                                @endif

                                <a href="{{ route('caisse.factures.print', $f) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition" title="Imprimer le ticket 80mm">
                                    <svg class="w-3.5 h-3.5 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    <span>Ticket</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-xs text-zinc-500 dark:text-zinc-400">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 flex items-center justify-center mx-auto text-base">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-zinc-700 dark:text-zinc-300">Aucun dépôt de linge correspondant</p>
                                    <a href="{{ route('caisse.retrait') }}" class="inline-block text-black dark:text-white font-bold text-xs underline">
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
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                {{ $factures->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
