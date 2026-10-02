@extends('layouts.app', ['title' => 'Facture ' . $facture->num_ticket])

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold tracking-tight text-[#000] font-mono">{{ $facture->num_ticket }}</h1>
                @if($facture->statut === 'paye_retire')
                    <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider bg-[#000] text-[#FFF]">
                        Payé & Retiré
                    </span>
                @elseif($facture->statut === 'pret')
                    <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider bg-[#FFF] text-[#000] border border-[#374151]">
                        Prêt (En attente retrait)
                    </span>
                @else
                    <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider bg-[#F3F4F6] text-[#374151] border border-[#374151]/30">
                        Déposé (En cours de lavage)
                    </span>
                @endif
            </div>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-1">
                Fiche détaillée du ticket de pressing
            </p>
        </div>
        <a href="{{ route('admin.factures.index') }}" class="text-xs font-semibold uppercase tracking-wider text-[#374151] hover:underline">
            &larr; Retour aux factures
        </a>
    </div>

    <!-- Ticket Summary Card -->
    <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pb-6 border-b border-[#374151]/10 text-xs">
            <div>
                <span class="text-[#374151] uppercase text-[10px] block">Agence de Dépôt</span>
                <span class="font-bold text-[#000] text-sm">{{ $facture->pressing->nom }}</span>
                <span class="text-[#374151] block">{{ $facture->pressing->quartier }}, {{ $facture->pressing->ville }}</span>
            </div>

            <div>
                <span class="text-[#374151] uppercase text-[10px] block">Agent Enregistreur</span>
                <span class="font-bold text-[#000] text-sm">{{ $facture->user->name }}</span>
                <span class="text-[#374151] block">{{ $facture->user->email }}</span>
            </div>

            <div>
                <span class="text-[#374151] uppercase text-[10px] block">Date de Dépôt</span>
                <span class="font-bold text-[#000] font-mono text-sm">{{ $facture->created_at->format('d/m/Y') }}</span>
                <span class="text-[#374151] block font-mono">{{ $facture->created_at->format('H:i') }}</span>
            </div>

            <div>
                <span class="text-[#374151] uppercase text-[10px] block">Date de Retrait & Paiement</span>
                @if($facture->paye_at)
                    <span class="font-bold text-[#000] font-mono text-sm">{{ $facture->paye_at->format('d/m/Y') }}</span>
                    <span class="text-[#374151] block font-mono">{{ $facture->paye_at->format('H:i') }}</span>
                @else
                    <span class="text-[#374151] italic block mt-1">Non encore retiré</span>
                    <span class="text-[10px] text-[#374151]">Règle : 100% au retrait</span>
                @endif
            </div>
        </div>

        <!-- Line items table -->
        <div class="mt-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-[#000] mb-3">Articles & Prestations incluses</h2>
            <div class="border border-[#374151]/20 overflow-hidden">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F3F4F6] border-b border-[#374151]/20 text-[10px] uppercase tracking-wider text-[#374151]">
                            <th class="py-2.5 px-4 font-semibold">Prestation</th>
                            <th class="py-2.5 px-4 font-semibold text-right">Prix Unitaire</th>
                            <th class="py-2.5 px-4 font-semibold text-center">Quantité</th>
                            <th class="py-2.5 px-4 font-semibold text-right">Sous-Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#374151]/10">
                        @forelse($facture->ligneFactures as $ligne)
                            <tr>
                                <td class="py-3 px-4 font-medium text-[#000]">
                                    {{ $ligne->service ? $ligne->service->designation : 'Service supprimé' }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-[#374151]">
                                    {{ number_format((float) $ligne->prix_applique, 2, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3 px-4 text-center font-mono">
                                    {{ $ligne->quantite }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-[#000]">
                                    {{ number_format((float) ($ligne->quantite * $ligne->prix_applique), 2, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-[#374151]">
                                    Aucune ligne d'article enregistrée sur cette facture.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-[#F3F4F6] border-t-2 border-[#000]">
                            <td colspan="3" class="py-3 px-4 font-bold text-xs uppercase text-right text-[#000]">
                                Montant Total de la Facture :
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-extrabold text-sm text-[#000]">
                                {{ number_format((float) $facture->montant_total, 2, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Financial Rule Reminder -->
        <div class="mt-6 p-4 bg-[#F3F4F6] border border-[#374151]/20 text-xs text-[#374151]">
            <p class="font-bold text-[#000] uppercase text-[10px] tracking-wider mb-1">Règle Financière du Pressing :</p>
            <p>Le client effectue 100% de son règlement lors du <strong>RETRAIT</strong> de ses vêtements (aucun acompte n'est perçu au dépôt). L'encaissement est formalisé dès que le statut passe à <strong>paye_retire</strong> avec horodatage strict.</p>
        </div>
    </div>
</div>
@endsection
