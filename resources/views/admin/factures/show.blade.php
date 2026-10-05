@extends('layouts.app', ['title' => 'Facture ' . $facture->num_ticket])

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 font-mono">{{ $facture->num_ticket }}</h1>
                @if($facture->statut === 'paye_retire')
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Payé & Retiré
                    </span>
                @elseif($facture->statut === 'pret')
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Prêt (En attente retrait)
                    </span>
                @else
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                        Déposé (En cours de lavage)
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Fiche détaillée et historique du ticket de pressing
            </p>
        </div>
        <a href="{{ route('admin.factures.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition flex items-center gap-1">
            <span>&larr; Retour aux factures</span>
        </a>
    </div>

    <!-- Ticket Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 pb-6 border-b border-slate-100 text-xs">
            <div class="space-y-1">
                <span class="text-slate-400 uppercase text-[10px] font-semibold block">Agence de Dépôt</span>
                <span class="font-bold text-slate-900 text-sm block">{{ $facture->pressing->nom }}</span>
                <span class="text-slate-500 text-[11px] block">{{ $facture->pressing->quartier }}, {{ $facture->pressing->ville }}</span>
            </div>

            <div class="space-y-1">
                <span class="text-slate-400 uppercase text-[10px] font-semibold block">Agent Enregistreur</span>
                <span class="font-bold text-slate-900 text-sm block">{{ $facture->user->name }}</span>
                <span class="text-slate-500 text-[11px] block">{{ $facture->user->email }}</span>
            </div>

            <div class="space-y-1">
                <span class="text-slate-400 uppercase text-[10px] font-semibold block">Date de Dépôt</span>
                <span class="font-bold text-slate-900 font-mono text-sm block">{{ $facture->created_at->format('d/m/Y') }}</span>
                <span class="text-slate-500 font-mono text-[11px] block">{{ $facture->created_at->format('H:i') }}</span>
            </div>

            <div class="space-y-1">
                <span class="text-slate-400 uppercase text-[10px] font-semibold block">Date Retrait & Paiement</span>
                @if($facture->paye_at)
                    <span class="font-bold text-emerald-700 font-mono text-sm block">{{ $facture->paye_at->format('d/m/Y') }}</span>
                    <span class="text-emerald-600 font-mono text-[11px] block">{{ $facture->paye_at->format('H:i') }}</span>
                @else
                    <span class="text-slate-400 italic block mt-1">Non encore retiré</span>
                    <span class="text-[10px] text-slate-400">Règle : 100% au retrait</span>
                @endif
            </div>
        </div>

        <!-- Line items table -->
        <div class="space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900">Articles & Prestations incluses</h2>
            <div class="border border-slate-200/80 rounded-xl overflow-hidden">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-semibold">
                            <th class="py-3 px-4">Prestation</th>
                            <th class="py-3 px-4 text-right">Prix Unitaire</th>
                            <th class="py-3 px-4 text-center">Quantité</th>
                            <th class="py-3 px-4 text-right">Sous-Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($facture->ligneFactures as $ligne)
                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $ligne->service ? $ligne->service->designation : 'Service supprimé' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-slate-600">
                                    {{ number_format((float) $ligne->prix_applique, 2, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 font-semibold">
                                        {{ $ligne->quantite }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                    {{ number_format((float) ($ligne->quantite * $ligne->prix_applique), 2, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-500">
                                    Aucune ligne d'article enregistrée sur cette facture.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-900 text-white">
                            <td colspan="3" class="py-3.5 px-4 font-bold text-xs uppercase text-right">
                                Montant Total de la Facture :
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-extrabold text-sm text-indigo-300">
                                {{ number_format((float) $facture->montant_total, 2, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Financial Rule Reminder -->
        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/80 text-xs text-slate-600 space-y-1">
            <p class="font-bold text-slate-900 uppercase text-[10px] tracking-wider">Règle Financière du Pressing :</p>
            <p class="leading-relaxed">Le client effectue 100% de son règlement lors du <strong>RETRAIT</strong> de ses vêtements (aucun acompte n'est perçu au dépôt). L'encaissement est formalisé dès que le statut passe à <strong>paye_retire</strong> avec horodatage strict.</p>
        </div>
    </div>
</div>
@endsection
