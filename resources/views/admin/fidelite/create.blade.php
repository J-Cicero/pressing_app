@extends('layouts.app', ['title' => 'Nouvelle Règle de Fidélité'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
        <div>
            <h1 class="text-xl font-black tracking-tight text-zinc-950 dark:text-white">Créer une Règle de Fidélité</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Définissez un palier de passage et le privilège accordé au client</p>
        </div>
        <a href="{{ route('admin.fidelite.index') }}" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white transition flex items-center gap-1">
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

        <form method="POST" action="{{ route('admin.fidelite.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="seuil_visites" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Seuil de visites (Passages en caisse) <span class="text-black dark:text-white font-bold">*</span>
                    </label>
                    <input
                        type="number"
                        name="seuil_visites"
                        id="seuil_visites"
                        min="1"
                        value="{{ old('seuil_visites', 5) }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs font-mono focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                        placeholder="Ex: 5 ou 10"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="type_activite" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Portée de l'activité <span class="text-black dark:text-white font-bold">*</span>
                    </label>
                    <select
                        name="type_activite"
                        id="type_activite"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                    >
                        <option value="global" {{ old('type_activite') === 'global' ? 'selected' : '' }}>Global (Toutes agences confondues)</option>
                        <option value="pressing" {{ old('type_activite') === 'pressing' ? 'selected' : '' }}>Spécifique Pressing</option>
                        <option value="lavage" {{ old('type_activite') === 'lavage' ? 'selected' : '' }}>Spécifique Lavage Auto/Moto</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="type_avantage" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Type d'avantage <span class="text-black dark:text-white font-bold">*</span>
                    </label>
                    <select
                        name="type_avantage"
                        id="type_avantage"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                    >
                        <option value="pourcentage" {{ old('type_avantage') === 'pourcentage' ? 'selected' : '' }}>Pourcentage de réduction (%)</option>
                        <option value="montant_fixe" {{ old('type_avantage') === 'montant_fixe' ? 'selected' : '' }}>Remise forfaitaire (FCFA)</option>
                        <option value="service_offert" {{ old('type_avantage') === 'service_offert' ? 'selected' : '' }}>Prestation offerte</option>
                        <option value="cadeau" {{ old('type_avantage') === 'cadeau' ? 'selected' : '' }}>Cadeau / Goodie spécial</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="valeur_avantage" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Valeur (si % ou montant FCFA)
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="valeur_avantage"
                        id="valeur_avantage"
                        value="{{ old('valeur_avantage', 10) }}"
                        class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs font-mono focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                        placeholder="Ex: 10 pour 10% ou 2000 pour 2 000 FCFA"
                    >
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    Description client & Message de félicitations <span class="text-black dark:text-white font-bold">*</span>
                </label>
                <input
                    type="text"
                    name="description"
                    id="description"
                    value="{{ old('description') }}"
                    required
                    class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                    placeholder="Ex: 10% de remise immédiate sur votre prochain lavage"
                >
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
                    <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Activer immédiatement cette règle</span>
                </label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-200 dark:border-zinc-800">
                <a href="{{ route('admin.fidelite.index') }}" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-xs transition duration-150">
                    Enregistrer la règle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
