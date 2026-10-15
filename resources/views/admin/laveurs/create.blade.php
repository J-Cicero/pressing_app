@extends('layouts.app', ['title' => 'Nouveau Laveur'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
        <div>
            <h1 class="text-xl font-black tracking-tight text-zinc-950 dark:text-white">Ajouter un Laveur</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Enregistrer un technicien de terrain pour les stations de lavage</p>
        </div>
        <a href="{{ route('admin.laveurs.index') }}" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white transition flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour à la liste</span>
        </a>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-2xl text-xs text-zinc-900 dark:text-white space-y-1">
                <div class="font-bold">Veuillez corriger les erreurs ci-dessous :</div>
                <ul class="list-disc list-inside space-y-0.5 text-zinc-600 dark:text-zinc-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.laveurs.store') }}" class="space-y-5">
            @csrf

            <div class="space-y-1.5">
                <label for="pressing_id" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    Établissement / Station de rattachement <span class="text-black dark:text-white font-bold">*</span>
                </label>
                <select
                    name="pressing_id"
                    id="pressing_id"
                    required
                    class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                >
                    <option value="">Sélectionnez une agence</option>
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ old('pressing_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->type_activite === 'lavage' ? 'Station Lavage' : 'Pressing' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="nom" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Nom complet du laveur <span class="text-black dark:text-white font-bold">*</span>
                    </label>
                    <input
                        type="text"
                        name="nom"
                        id="nom"
                        value="{{ old('nom') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                        placeholder="Ex: Koffi Mensah"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="telephone" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Téléphone
                    </label>
                    <input
                        type="text"
                        name="telephone"
                        id="telephone"
                        value="{{ old('telephone') }}"
                        class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs font-mono focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                        placeholder="Ex: 90 00 00 00"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="type_remuneration" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Mode de rémunération <span class="text-black dark:text-white font-bold">*</span>
                    </label>
                    <select
                        name="type_remuneration"
                        id="type_remuneration"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                    >
                        <option value="pourcentage" {{ old('type_remuneration') === 'pourcentage' ? 'selected' : '' }}>Pourcentage (%) sur le montant</option>
                        <option value="forfait" {{ old('type_remuneration') === 'forfait' ? 'selected' : '' }}>Forfait fixe par véhicule (FCFA)</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="valeur_remuneration" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Valeur de rémunération (% ou FCFA) <span class="text-black dark:text-white font-bold">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="valeur_remuneration"
                        id="valeur_remuneration"
                        value="{{ old('valeur_remuneration', 30) }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs font-mono focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                        placeholder="Ex: 30 pour 30% ou 1000 pour 1 000 FCFA"
                    >
                </div>
            </div>

            <div class="p-3 bg-zinc-50 dark:bg-zinc-950/80 border border-zinc-200 dark:border-zinc-800 rounded-xl text-[11px] text-zinc-600 dark:text-zinc-400">
                <span class="font-bold text-zinc-900 dark:text-white">Règle standard :</span>
                Par défaut 30% ou forfait fixe (ex: 1 000 FCFA). Cette valeur est pré-remplie automatiquement pour vous éviter de la ressaisir, mais reste personnalisable par laveur selon son niveau ou accord.
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        name="actif"
                        value="1"
                        {{ old('actif', true) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-zinc-300 dark:border-zinc-700 text-black focus:ring-black dark:focus:ring-white bg-zinc-100 dark:bg-zinc-800"
                    >
                    <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Laveur actuellement en activité</span>
                </label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-200 dark:border-zinc-800">
                <a href="{{ route('admin.laveurs.index') }}" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-xs transition duration-150">
                    Enregistrer le laveur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
