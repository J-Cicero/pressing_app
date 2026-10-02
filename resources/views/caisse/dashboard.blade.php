@extends('layouts.app', ['title' => 'Espace Caisse - ' . $pressing->nom])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-[#374151]/20 gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold tracking-tight text-[#000]">ESPACE CAISSE & GUICHET</h1>
                <span class="px-2 py-0.5 text-xs font-bold uppercase tracking-wider bg-[#000] text-[#FFF]">
                    {{ $pressing->nom }}
                </span>
            </div>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-1">
                Agence : {{ $pressing->quartier }}, {{ $pressing->ville }} &bull; Tél : {{ $pressing->telephone ?? 'N/A' }}
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('caisse.depot') }}" class="px-4 py-2.5 bg-[#000] text-[#FFF] text-xs font-bold uppercase tracking-wider hover:bg-[#374151] transition shadow-sm">
                + Nouveau Dépôt
            </a>
            <a href="{{ route('caisse.retrait') }}" class="px-4 py-2.5 bg-[#FFF] text-[#000] border border-[#000] text-xs font-bold uppercase tracking-wider hover:bg-[#F3F4F6] transition shadow-sm">
                Gestion / Encaissement Ticket
            </a>
        </div>
    </div>

    <!-- Agency Metrics (Real DB Aggregates) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 my-8">
        <!-- Tickets Déposés Aujourd'hui -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold uppercase tracking-wider text-[#374151]">Dépôts du Jour</span>
                <span class="text-[10px] font-mono px-1.5 py-0.5 bg-[#F3F4F6] text-[#374151] border border-[#374151]/20">Aujourd'hui</span>
            </div>
            <div class="mt-4 text-3xl font-extrabold tracking-tight text-[#000] font-mono">
                {{ $ticketsDeposesAujourdhui }}
            </div>
            <div class="mt-2 text-xs text-[#374151]">
                Nouveaux tickets enregistrés ce jour
            </div>
        </div>

        <!-- Factures Prêtes pour Retrait -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold uppercase tracking-wider text-[#374151]">Prêts au Retrait</span>
                <span class="text-[10px] font-mono px-1.5 py-0.5 bg-[#FFF] text-[#000] border border-[#374151]">Disponibles</span>
            </div>
            <div class="mt-4 text-3xl font-extrabold tracking-tight text-[#000] font-mono">
                {{ $ticketsPrets }}
            </div>
            <div class="mt-2 text-xs text-[#374151]">
                Vêtements lavés en attente du client
            </div>
        </div>

        <!-- Total Encaissé Aujourd'hui -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold uppercase tracking-wider text-[#374151]">Encaissé Aujourd'hui</span>
                <span class="text-[10px] font-mono px-1.5 py-0.5 bg-[#000] text-[#FFF]">100% au Retrait</span>
            </div>
            <div class="mt-4 text-3xl font-extrabold tracking-tight text-[#000] font-mono">
                {{ number_format($totalEncaisseAujourdhui, 2, ',', ' ') }} <span class="text-xs font-normal text-[#374151]">FCFA</span>
            </div>
            <div class="mt-2 text-xs text-[#374151]">
                Total perçu lors des retraits du jour
            </div>
        </div>

        <!-- Total Tickets en Cours -->
        <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold uppercase tracking-wider text-[#374151]">En Cours Agence</span>
                <span class="text-[10px] font-mono px-1.5 py-0.5 bg-[#F3F4F6] text-[#374151] border border-[#374151]/20">Global</span>
            </div>
            <div class="mt-4 text-3xl font-extrabold tracking-tight text-[#000] font-mono">
                {{ $totalEnCours }}
            </div>
            <div class="mt-2 text-xs text-[#374151]">
                Tickets non encore réglés / retirés
            </div>
        </div>
    </div>

    <!-- Agency Operational Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Quick Action Panels -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#000] mb-2">Opérations du Guichet</h2>
                <p class="text-xs text-[#374151] mb-6">
                    Enregistrez les dépôts de linge des clients et encaissez au moment de la restitution.
                </p>

                <div class="space-y-3">
                    <a href="{{ route('caisse.depot') }}" class="block w-full text-center py-3 bg-[#000] text-[#FFF] text-xs font-bold uppercase tracking-widest hover:bg-[#374151] transition">
                        Nouveau Dépôt Client
                    </a>
                    <a href="{{ route('caisse.retrait') }}" class="block w-full text-center py-3 bg-[#F3F4F6] border border-[#374151]/40 text-[#000] text-xs font-bold uppercase tracking-widest hover:bg-[#FFF] transition">
                        Recherche / Retrait Client
                    </a>
                </div>
            </div>

            <!-- Financial Reminder Card -->
            <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#000] mb-2">Règle Financière Stricte</h3>
                <p class="text-xs text-[#374151] leading-relaxed">
                    <strong>100% du paiement est perçu au retrait</strong> des vêtements. Aucun acompte n'est exigé ni accepté à la création du ticket. Le montant reste dû jusqu'à la remise des articles.
                </p>
            </div>
        </div>

        <!-- Recent Invoices of this Agency -->
        <div class="lg:col-span-2 bg-[#FFF] border border-[#374151]/20 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-[#374151]/20 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-[#000]">Derniers Tickets de l'Agence</h2>
                    <p class="text-xs text-[#374151]">Activité récente sur le guichet de {{ $pressing->nom }}</p>
                </div>
                <a href="{{ route('caisse.retrait') }}" class="text-xs font-semibold uppercase tracking-wider text-[#000] hover:underline">
                    Tout voir &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F3F4F6] border-b border-[#374151]/20 text-[10px] uppercase tracking-wider text-[#374151]">
                            <th class="py-3 px-4 font-semibold">Ticket</th>
                            <th class="py-3 px-4 font-semibold">Client</th>
                            <th class="py-3 px-4 font-semibold">Date Dépôt</th>
                            <th class="py-3 px-4 font-semibold text-center">Statut</th>
                            <th class="py-3 px-4 font-semibold text-right">Montant</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#374151]/10">
                        @forelse($dernieresFactures as $f)
                            <tr class="hover:bg-[#F3F4F6]/50 transition">
                                <td class="py-3 px-4 font-mono font-bold text-[#000]">
                                    {{ $f->num_ticket }}
                                </td>
                                <td class="py-3 px-4 text-[#374151]">
                                    <div class="font-medium text-[#000]">{{ $f->client_nom ?? 'Client de passage' }}</div>
                                    <div class="font-mono text-[11px]">{{ $f->client_telephone }}</div>
                                </td>
                                <td class="py-3 px-4 text-[#374151] font-mono text-[11px]">
                                    {{ $f->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($f->statut === 'paye_retire')
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-[#000] text-[#FFF]">
                                            Retiré
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
                                <td class="py-3 px-4 text-right font-mono font-bold text-[#000]">
                                    {{ number_format((float) $f->montant_total, 2, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3 px-4 text-right space-x-1">
                                    <a href="{{ route('caisse.factures.print', $f) }}" target="_blank" class="inline-block px-2 py-1 text-[11px] font-semibold uppercase tracking-wider bg-[#000] text-[#FFF] hover:bg-[#374151] transition">
                                        Imprimer
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-xs text-[#374151]">
                                    Aucun ticket enregistré pour le moment.
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
