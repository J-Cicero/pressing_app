@extends('layouts.app', ['title' => 'Facture ' . $facture->num_ticket])

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900 dark:text-white font-mono">{{ $facture->num_ticket }}</h1>
                @if($facture->statut === 'paye_retire')
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        Payé & Retiré
                    </span>
                @elseif($facture->statut === 'pret')
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        Prêt (En attente retrait)
                    </span>
                @else
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                        Déposé (En cours de lavage)
                    </span>
                @endif
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Fiche détaillée et historique du ticket de pressing
            </p>
        </div>
        <a href="{{ route('admin.factures.index') }}" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition flex items-center gap-1">
            <span>&larr; Retour aux factures</span>
        </a>
    </div>

    <!-- Ticket Summary Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 pb-6 border-b border-zinc-100 dark:border-zinc-800 text-xs">
            <div class="space-y-1">
                <span class="text-zinc-400 dark:text-zinc-500 uppercase text-[10px] font-semibold block">Agence de Dépôt</span>
                <span class="font-bold text-zinc-900 dark:text-white text-sm block">{{ $facture->pressing->nom }}</span>
                <span class="text-zinc-500 dark:text-zinc-400 text-[11px] block">{{ $facture->pressing->quartier }}, {{ $facture->pressing->ville }}</span>
            </div>

            <div class="space-y-1">
                <span class="text-zinc-400 dark:text-zinc-500 uppercase text-[10px] font-semibold block">Agent Enregistreur</span>
                <span class="font-bold text-zinc-900 dark:text-white text-sm block">{{ $facture->user->name }}</span>
                <span class="text-zinc-500 dark:text-zinc-400 text-[11px] block">{{ $facture->user->email }}</span>
            </div>

            <div class="space-y-1">
                <span class="text-zinc-400 dark:text-zinc-500 uppercase text-[10px] font-semibold block">Date de Dépôt</span>
                <span class="font-bold text-zinc-900 dark:text-white font-mono text-sm block">{{ $facture->created_at->format('d/m/Y') }}</span>
                <span class="text-zinc-500 dark:text-zinc-400 font-mono text-[11px] block">{{ $facture->created_at->format('H:i') }}</span>
            </div>

            <div class="space-y-1">
                <span class="text-zinc-400 dark:text-zinc-500 uppercase text-[10px] font-semibold block">Date Retrait & Paiement</span>
                @if($facture->paye_at)
                    <span class="font-bold text-emerald-700 dark:text-emerald-400 font-mono text-sm block">{{ $facture->paye_at->format('d/m/Y') }}</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-mono text-[11px] block">{{ $facture->paye_at->format('H:i') }}</span>
                @else
                    <span class="text-zinc-400 dark:text-zinc-500 italic block mt-1">Non encore retiré</span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500">Règle : 100% au retrait</span>
                @endif
            </div>
        </div>

        <!-- Line items table -->
        <div class="space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">Articles & Prestations incluses</h2>
            <div class="border border-zinc-200/80 dark:border-zinc-800 rounded-xl overflow-hidden">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50/80 dark:bg-zinc-950/80 border-b border-zinc-100 dark:border-zinc-800 text-[10px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                            <th class="py-3 px-4">Prestation</th>
                            <th class="py-3 px-4 text-right">Prix Unitaire</th>
                            <th class="py-3 px-4 text-center">Quantité</th>
                            <th class="py-3 px-4 text-right">Sous-Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @forelse($facture->ligneFactures as $ligne)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition duration-150">
                                <td class="py-3.5 px-4 font-semibold text-zinc-900 dark:text-white">
                                    {{ $ligne->service ? $ligne->service->designation : 'Service supprimé' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-zinc-600 dark:text-zinc-400">
                                    {{ number_format((float) $ligne->prix_applique, 2, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono">
                                    <span class="px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 font-semibold">
                                        {{ $ligne->quantite }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-zinc-900 dark:text-white">
                                    {{ number_format((float) ($ligne->quantite * $ligne->prix_applique), 2, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-zinc-500 dark:text-zinc-400">
                                    Aucune ligne d'article enregistrée sur cette facture.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-zinc-900 dark:bg-zinc-950 text-white border-t border-zinc-800 dark:border-zinc-800">
                            <td colspan="3" class="py-3.5 px-4 font-bold text-xs uppercase text-right">
                                Montant Total de la Facture :
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-extrabold text-sm text-white">
                                {{ number_format((float) $facture->montant_total, 2, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Financial Rule Reminder -->
        <div class="p-4 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-200/80 dark:border-zinc-800 text-xs text-zinc-600 dark:text-zinc-400 space-y-1">
            <p class="font-bold text-zinc-900 dark:text-white uppercase text-[10px] tracking-wider">Règle Financière du Pressing :</p>
            <p class="leading-relaxed">Le client effectue 100% de son règlement lors du <strong>RETRAIT</strong> de ses vêtements (aucun acompte n'est perçu au dépôt). L'encaissement est formalisé dès que le statut passe à <strong>paye_retire</strong> avec horodatage strict.</p>
        </div>
    </div>
</div>
@endsection
