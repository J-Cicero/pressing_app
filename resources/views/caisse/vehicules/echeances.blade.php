@extends('layouts.app', ['title' => 'Rappels Véhicules & Échéances'])

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
                Suivi des visites techniques et assurances avec relances directes WhatsApp pour vos clients
            </p>
        </div>
        <div>
            <button
                type="button"
                onclick="openCreateModal()"
                class="px-4 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Enregistrer un Véhicule & Échéances</span>
            </button>
        </div>
    </div>

    <!-- Status Alerts -->
    @if(session('status'))
        <div class="p-4 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-300 dark:border-zinc-700 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-zinc-900 dark:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-xs font-semibold text-zinc-900 dark:text-white">{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-300 dark:border-zinc-700 rounded-2xl space-y-1">
            <div class="text-xs font-bold text-zinc-950 dark:text-white">Des erreurs sont survenues :</div>
            <ul class="list-disc list-inside text-xs text-zinc-600 dark:text-zinc-400">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Quick metric badges / filters -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'jour_j']) }}" class="p-4 rounded-2xl border transition {{ $filtre === 'jour_j' ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-xs' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/60' }}">
            <div class="text-[11px] font-bold uppercase tracking-wider opacity-75">Jour J (Aujourd'hui)</div>
            <div class="text-2xl font-black font-mono mt-1">{{ $countJourJ }}</div>
            <div class="text-[10px] opacity-75 mt-0.5">Échéance immédiate</div>
        </a>

        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'j_3']) }}" class="p-4 rounded-2xl border transition {{ $filtre === 'j_3' ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-xs' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/60' }}">
            <div class="text-[11px] font-bold uppercase tracking-wider opacity-75">Dans 3 Jours (J-3)</div>
            <div class="text-2xl font-black font-mono mt-1">{{ $countJ3 }}</div>
            <div class="text-[10px] opacity-75 mt-0.5">Offre d'assistance</div>
        </a>

        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'j_7']) }}" class="p-4 rounded-2xl border transition {{ $filtre === 'j_7' ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-xs' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/60' }}">
            <div class="text-[11px] font-bold uppercase tracking-wider opacity-75">Dans 7 Jours (J-7)</div>
            <div class="text-2xl font-black font-mono mt-1">{{ $countJ7 }}</div>
            <div class="text-[10px] opacity-75 mt-0.5">Premier rappel</div>
        </a>

        <a href="{{ route('caisse.vehicules.echeances', ['filtre' => 'retard']) }}" class="p-4 rounded-2xl border transition {{ $filtre === 'retard' ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-xs' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/60' }}">
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
                        <th class="py-3.5 px-6">Assurance</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
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
                            <td class="py-4 px-6 font-mono text-xs text-zinc-600 dark:text-zinc-400">
                                {{ $v->date_assurance ? $v->date_assurance->format('d/m/Y') : 'Non renseignée' }}
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        onclick="openEditModal({
                                            id: '{{ $v->getRouteKey() }}',
                                            immatriculation: '{{ addslashes($v->immatriculation) }}',
                                            marque_modele: '{{ addslashes($v->marque_modele) }}',
                                            type_vehicule: '{{ $v->type_vehicule }}',
                                            date_visite_technique: '{{ $v->date_visite_technique ? $v->date_visite_technique->format('Y-m-d') : '' }}',
                                            date_assurance: '{{ $v->date_assurance ? $v->date_assurance->format('Y-m-d') : '' }}'
                                        })"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        <span>Mettre à jour</span>
                                    </button>

                                    @if($v->whatsapp_url)
                                        <a href="{{ $v->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black transition shadow-xs whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span>WhatsApp</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                        <svg class="w-6 h-6 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Aucun véhicule pour ce filtre</p>
                                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500">Toutes les visites techniques sont à jour ou aucun véhicule n'est encore enregistré.</p>
                                    <div class="pt-2">
                                        <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl transition shadow-xs">
                                            + Enregistrer un véhicule
                                        </button>
                                    </div>
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

<!-- Modal 1 : Enregistrer un Véhicule & Échéances -->
<div id="modal-create-vehicule" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 dark:bg-black/80 backdrop-blur-xs transition-opacity duration-200 hidden opacity-0">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <div>
                <h3 class="text-base font-black text-zinc-950 dark:text-white tracking-tight">
                    Enregistrer un Véhicule & Échéances
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Ajoutez le véhicule d'un client et ses dates de contrôle technique et assurance
                </p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 text-lg font-bold">
                &times;
            </button>
        </div>

        <form method="POST" action="{{ route('caisse.vehicules.store') }}" class="space-y-4">
            @csrf

            <!-- Client Info Group -->
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200 dark:border-zinc-800 space-y-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">1. Propriétaire / Client</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Téléphone <span class="text-black dark:text-white">*</span>
                        </label>
                        <input
                            type="text"
                            name="client_telephone"
                            id="create_client_telephone"
                            required
                            placeholder="ex: 90123456"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Nom Complet <span class="text-black dark:text-white">*</span>
                        </label>
                        <input
                            type="text"
                            name="client_nom"
                            id="create_client_nom"
                            required
                            placeholder="ex: Koffi Mensah"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                        >
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                        Adresse E-mail <span class="text-zinc-400 font-normal">(facultatif)</span>
                    </label>
                    <input
                        type="email"
                        name="client_email"
                        id="create_client_email"
                        placeholder="client@domaine.com"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                    >
                </div>
            </div>

            <!-- Vehicle Info Group -->
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200 dark:border-zinc-800 space-y-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">2. Caractéristiques du Véhicule</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Immatriculation <span class="text-black dark:text-white">*</span>
                        </label>
                        <input
                            type="text"
                            name="immatriculation"
                            required
                            placeholder="ex: TG-4820-AZ"
                            class="w-full px-3 py-2 text-xs font-mono font-bold uppercase rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Type / Catégorie <span class="text-black dark:text-white">*</span>
                        </label>
                        <input
                            type="text"
                            name="type_vehicule"
                            required
                            list="types_vehicules_modal_suggestions"
                            value="Moto"
                            placeholder="ex: Moto, Berline, SUV..."
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                        >
                        <datalist id="types_vehicules_modal_suggestions">
                            <option value="Moto">
                            <option value="Scooter">
                            <option value="Berline">
                            <option value="SUV / 4x4">
                            <option value="Pick-up">
                            <option value="Tricycle">
                            <option value="Camionnette">
                            <option value="Camion">
                        </datalist>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                        Marque & Modèle <span class="text-black dark:text-white">*</span>
                    </label>
                    <input
                        type="text"
                        name="marque_modele"
                        required
                        placeholder="ex: Toyota Corolla, Hyundai Tucson..."
                        class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                    >
                </div>
            </div>

            <!-- Deadlines Group -->
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200 dark:border-zinc-800 space-y-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">3. Échéances & Contrôles</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Date Visite Technique
                        </label>
                        <input
                            type="date"
                            name="date_visite_technique"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Date Assurance
                        </label>
                        <input
                            type="date"
                            name="date_assurance"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                        >
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                <button
                    type="button"
                    onclick="closeCreateModal()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 transition"
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-black dark:bg-white text-white dark:text-black hover:opacity-90 transition shadow-xs"
                >
                    Enregistrer le véhicule
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2 : Mettre à jour les Échéances -->
<div id="modal-edit-vehicule" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 dark:bg-black/80 backdrop-blur-xs transition-opacity duration-200 hidden opacity-0">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <div>
                <h3 class="text-base font-black text-zinc-950 dark:text-white tracking-tight flex items-center gap-2">
                    <span>Mettre à jour les Échéances</span>
                    <span id="edit_immat_badge" class="font-mono text-xs px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700"></span>
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Modifiez les dates d'échéance de contrôle technique et d'assurance
                </p>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 text-lg font-bold">
                &times;
            </button>
        </div>

        <form id="edit_vehicule_form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-3">
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                        Marque & Modèle
                    </label>
                    <input
                        type="text"
                        name="marque_modele"
                        id="edit_marque_modele"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                    >
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                        Type / Catégorie de véhicule
                    </label>
                    <input
                        type="text"
                        name="type_vehicule"
                        id="edit_type_vehicule"
                        list="types_vehicules_modal_suggestions"
                        placeholder="ex: Moto, Berline, SUV..."
                        class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Date Visite Technique
                        </label>
                        <input
                            type="date"
                            name="date_visite_technique"
                            id="edit_date_visite_technique"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Date Assurance
                        </label>
                        <input
                            type="date"
                            name="date_assurance"
                            id="edit_date_assurance"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-black dark:focus:ring-white"
                        >
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 transition"
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-black dark:bg-white text-white dark:text-black hover:opacity-90 transition shadow-xs"
                >
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        const modal = document.getElementById('modal-create-vehicule');
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
        });
    }

    function closeCreateModal() {
        const modal = document.getElementById('modal-create-vehicule');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 180);
    }

    function openEditModal(v) {
        const modal = document.getElementById('modal-edit-vehicule');
        const form = document.getElementById('edit_vehicule_form');
        form.action = "{{ url('caisse/vehicules') }}/" + v.id;

        document.getElementById('edit_immat_badge').textContent = v.immatriculation;
        document.getElementById('edit_marque_modele').value = v.marque_modele || '';
        document.getElementById('edit_type_vehicule').value = v.type_vehicule || 'voiture';
        document.getElementById('edit_date_visite_technique').value = v.date_visite_technique || '';
        document.getElementById('edit_date_assurance').value = v.date_assurance || '';

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
        });
    }

    function closeEditModal() {
        const modal = document.getElementById('modal-edit-vehicule');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 180);
    }

    // Auto lookup client when phone is typed in create modal
    document.addEventListener('DOMContentLoaded', () => {
        const telInput = document.getElementById('create_client_telephone');
        const nomInput = document.getElementById('create_client_nom');
        const emailInput = document.getElementById('create_client_email');

        if (telInput) {
            telInput.addEventListener('blur', () => {
                const tel = telInput.value.trim();
                if (tel.length >= 8) {
                    fetch(`{{ route('caisse.clients.lookup') }}?telephone=${encodeURIComponent(tel)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.found && data.client) {
                                if (!nomInput.value) nomInput.value = data.client.nom || '';
                                if (!emailInput.value) emailInput.value = data.client.email || '';
                            }
                        })
                        .catch(() => {});
                }
            });
        }
    });
</script>
@endsection
