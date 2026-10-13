@extends('layouts.app', ['title' => 'Profil Client : ' . $client->nom])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white">{{ $client->nom }}</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-full">
                    {{ $client->telephone }}
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Client enregistré le {{ $client->created_at->format('d/m/Y') }} &bull; {{ $client->email ?? 'Sans adresse email' }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', (strlen(preg_replace('/[^0-9]/', '', $client->telephone)) === 8 ? '228' : '') . preg_replace('/[^0-9]/', '', $client->telephone)) }}" target="_blank" class="px-4 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Écrire sur WhatsApp</span>
            </a>
            <a href="{{ route('admin.clients.index') }}" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                &larr; Retour
            </a>
        </div>
    </div>

    <!-- Stats Grid (Visits & Loyalty) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Visits -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Visites Réseau</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ $totalVisites }}</span>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">toutes agences</span>
            </div>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Factures payées et retirées</p>
        </div>

        <!-- Pressing Visits -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Visites Pressing</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ $visitesPressing }}</span>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">pressing</span>
            </div>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Passages en blanchisserie</p>
        </div>

        <!-- Wash Visits -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Visites Lavage Auto</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-black font-mono text-zinc-950 dark:text-white">{{ $visitesLavage }}</span>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">stations</span>
            </div>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Lavages de véhicules</p>
        </div>

        <!-- Loyalty Level -->
        <div class="bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 p-5 rounded-2xl border border-zinc-900 dark:border-zinc-200 shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-600">Statut Fidélité</span>
            <div class="flex items-center justify-between">
                @if($fideliteAtteinte)
                    <span class="text-lg font-black">{{ $fideliteAtteinte->description }}</span>
                @else
                    <span class="text-sm font-bold">Standard</span>
                @endif
            </div>
            @if($prochaineRegle)
                <p class="text-[11px] text-zinc-400 dark:text-zinc-600">
                    Prochain palier dans {{ $prochaineRegle->seuil_visites - $totalVisites }} passage(s) ({{ $prochaineRegle->seuil_visites }} visites)
                </p>
            @else
                <p class="text-[11px] text-zinc-400 dark:text-zinc-600">Palier maximal atteint</p>
            @endif
        </div>
    </div>

    <!-- Loyalty Alert Banner if milestone reached -->
    @if($fideliteAtteinte && $whatsappFideliteUrl)
        <div class="p-5 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-300 dark:border-zinc-700 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-black dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <span class="text-sm font-bold text-zinc-950 dark:text-white">Avantage Fidélité Disponible</span>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                    Ce client a franchi le palier de {{ $fideliteAtteinte->seuil_visites }} visites. Avantage : {{ $fideliteAtteinte->description }}.
                </p>
            </div>
            <a href="{{ $whatsappFideliteUrl }}" target="_blank" class="px-4 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition flex items-center justify-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span>Féliciter via WhatsApp</span>
            </a>
        </div>
    @endif

    <!-- Vehicles Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold tracking-tight text-zinc-950 dark:text-white">Véhicules Enregistrés ({{ $client->vehicules->count() }})</h2>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <th class="py-3.5 px-6">Immatriculation</th>
                        <th class="py-3.5 px-6">Marque & Modèle</th>
                        <th class="py-3.5 px-6">Catégorie</th>
                        <th class="py-3.5 px-6">Visite Technique</th>
                        <th class="py-3.5 px-6">Assurance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                    @forelse($client->vehicules as $v)
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                            <td class="py-4 px-6 font-mono font-black text-zinc-950 dark:text-white">
                                <span class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                                    {{ $v->immatriculation }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-medium text-zinc-800 dark:text-zinc-200">
                                {{ $v->marque_modele }}
                            </td>
                            <td class="py-4 px-6 uppercase text-[10px] font-bold text-zinc-500 dark:text-zinc-400">
                                {{ $v->type_vehicule }}
                            </td>
                            <td class="py-4 px-6 font-mono">
                                @if($v->date_visite_technique)
                                    <div>{{ $v->date_visite_technique->format('d/m/Y') }}</div>
                                    @php $diff = $v->joursAvantVisiteTechnique(); @endphp
                                    @if($diff !== null)
                                        @if($diff < 0)
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-black text-white dark:bg-white dark:text-black">
                                                Expirée ({{ abs($diff) }}j de retard)
                                            </span>
                                        @elseif($diff === 0)
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-black text-white dark:bg-white dark:text-black">
                                                Expire Aujourd'hui
                                            </span>
                                        @elseif($diff <= 7)
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-white">
                                                Dans {{ $diff }} jour(s)
                                            </span>
                                        @else
                                            <span class="inline-block mt-1 text-[10px] text-zinc-400 dark:text-zinc-500">
                                                Dans {{ $diff }} jours
                                            </span>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-mono text-zinc-600 dark:text-zinc-400">
                                {{ $v->date_assurance ? $v->date_assurance->format('d/m/Y') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Aucun véhicule enregistré pour ce client.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Invoices History -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold tracking-tight text-zinc-950 dark:text-white">Historique des Tickets & Factures ({{ $client->factures->count() }})</h2>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <th class="py-3.5 px-6">N° Ticket</th>
                        <th class="py-3.5 px-6">Établissement</th>
                        <th class="py-3.5 px-6">Date de dépôt</th>
                        <th class="py-3.5 px-6">Montant Total</th>
                        <th class="py-3.5 px-6 text-center">Statut</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                    @forelse($client->factures as $f)
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                            <td class="py-4 px-6 font-mono font-bold text-zinc-950 dark:text-white">
                                {{ $f->num_ticket }}
                            </td>
                            <td class="py-4 px-6 font-medium text-zinc-800 dark:text-zinc-200">
                                {{ $f->pressing->nom ?? '—' }}
                                <span class="text-[10px] text-zinc-400 block uppercase">{{ $f->pressing->type_activite ?? '' }}</span>
                            </td>
                            <td class="py-4 px-6 text-zinc-600 dark:text-zinc-400">
                                {{ $f->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-4 px-6 font-mono font-black text-zinc-950 dark:text-white">
                                {{ number_format($f->montant_total, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($f->statut === 'paye_retire')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-900 text-white dark:bg-white dark:text-black">
                                        Payé & Retiré
                                    </span>
                                @elseif($f->statut === 'pret')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-white">
                                        Prêt
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                        Déposé
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.factures.show', $f) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition">
                                    Détails
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Aucun ticket enregistré pour ce client.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
