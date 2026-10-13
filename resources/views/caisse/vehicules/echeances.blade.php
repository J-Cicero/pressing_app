@extends('layouts.app', ['title' => 'Rappels Véhicules'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black tracking-tight text-zinc-950 dark:text-white">Rappels Techniques & Contrôles</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-full">
                    {{ $pressing->nom }}
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Relancez directement vos clients sur WhatsApp pour leurs échéances de visite technique
            </p>
        </div>
    </div>

    <!-- Quick metric badges / filters -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'jour_j']) }}" class="p-4 rounded-2xl border transition {{ $filtre === 'jour_j' ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-sm' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/60' }}">
            <div class="text-[11px] font-bold uppercase tracking-wider opacity-75">Jour J (Aujourd'hui)</div>
            <div class="text-2xl font-black font-mono mt-1">{{ $countJourJ }}</div>
            <div class="text-[10px] opacity-75 mt-0.5">Échéance immédiate</div>
        </a>

        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'j_3']) }}" class="p-4 rounded-2xl border transition {{ $filtre === 'j_3' ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-sm' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/60' }}">
            <div class="text-[11px] font-bold uppercase tracking-wider opacity-75">Dans 3 Jours (J-3)</div>
            <div class="text-2xl font-black font-mono mt-1">{{ $countJ3 }}</div>
            <div class="text-[10px] opacity-75 mt-0.5">Offre d'assistance</div>
        </a>

        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'j_7']) }}" class="p-4 rounded-2xl border transition {{ $filtre === 'j_7' ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-sm' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/60' }}">
            <div class="text-[11px] font-bold uppercase tracking-wider opacity-75">Dans 7 Jours (J-7)</div>
            <div class="text-2xl font-black font-mono mt-1">{{ $countJ7 }}</div>
            <div class="text-[10px] opacity-75 mt-0.5">Premier rappel</div>
        </a>

        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'retard']) }}" class="p-4 rounded-2xl border transition {{ $filtre === 'retard' ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-sm' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/60' }}">
            <div class="text-[11px] font-bold uppercase tracking-wider opacity-75">En retard (Expirées)</div>
            <div class="text-2xl font-black font-mono mt-1">{{ $countRetard }}</div>
            <div class="text-[10px] opacity-75 mt-0.5">Relance urgente</div>
        </a>
    </div>

    <!-- Filter Tabs Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'tous']) }}" class="px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap {{ $filtre === 'tous' ? 'bg-black text-white dark:bg-white dark:text-black' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
            Tous les véhicules
        </a>
        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'prochains']) }}" class="px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap {{ $filtre === 'prochains' ? 'bg-black text-white dark:bg-white dark:text-black' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
            Prochains 30 jours
        </a>
        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'jour_j']) }}" class="px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap {{ $filtre === 'jour_j' ? 'bg-black text-white dark:bg-white dark:text-black' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
            Jour J ({{ $countJourJ }})
        </a>
        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'j_3']) }}" class="px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap {{ $filtre === 'j_3' ? 'bg-black text-white dark:bg-white dark:text-black' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
            J-3 ({{ $countJ3 }})
        </a>
        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'j_7']) }}" class="px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap {{ $filtre === 'j_7' ? 'bg-black text-white dark:bg-white dark:text-black' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
            J-7 ({{ $countJ7 }})
        </a>
        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'retard']) }}" class="px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap {{ $filtre === 'retard' ? 'bg-black text-white dark:bg-white dark:text-black' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
            En retard ({{ $countRetard }})
        </a>
    </div>

    <!-- Vehicles Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <th class="py-3.5 px-6">Immatriculation</th>
                        <th class="py-3.5 px-6">Client</th>
                        <th class="py-3.5 px-6">Véhicule</th>
                        <th class="py-3.5 px-6">Visite Technique</th>
                        <th class="py-3.5 px-6">Statut Échéance</th>
                        <th class="py-3.5 px-6 text-right">Relance WhatsApp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                    @forelse($vehicules as $v)
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition duration-150">
                            <td class="py-4 px-6 font-mono font-black text-sm text-zinc-950 dark:text-white">
                                <span class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                                    {{ $v->immatriculation }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-medium text-zinc-800 dark:text-zinc-200">
                                @if($v->client)
                                    <div class="font-bold text-zinc-950 dark:text-white">{{ $v->client->nom }}</div>
                                    <span class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400">
                                        {{ $v->client->telephone }}
                                    </span>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-zinc-700 dark:text-zinc-300">
                                <div class="font-semibold">{{ $v->marque_modele }}</div>
                                <span class="text-[10px] uppercase font-bold text-zinc-500 dark:text-zinc-400">{{ $v->type_vehicule }}</span>
                            </td>
                            <td class="py-4 px-6 font-mono font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $v->date_visite_technique ? $v->date_visite_technique->format('d/m/Y') : '—' }}
                            </td>
                            <td class="py-4 px-6">
                                @if($v->echeance_badge === 'urgent')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-black text-white dark:bg-white dark:text-black">
                                        {{ $v->echeance_label }}
                                    </span>
                                @elseif($v->echeance_badge === 'jour_j')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-black text-white dark:bg-white dark:text-black">
                                        {{ $v->echeance_label }}
                                    </span>
                                @elseif($v->echeance_badge === 'j_3' || $v->echeance_badge === 'j_7')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-white">
                                        {{ $v->echeance_label }}
                                    </span>
                                @else
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $v->echeance_label }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($v->whatsapp_url)
                                    <a href="{{ $v->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black transition shadow-xs whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span>WhatsApp</span>
                                    </a>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                        <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Aucun véhicule pour ce filtre</p>
                                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500">Toutes les visites techniques sont à jour.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicules->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $vehicules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
