@extends('layouts.app', ['title' => 'Ajouter un Établissement'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Nouvel Établissement</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Enregistrer une agence de pressing ou station de lavage</p>
        </div>
        <a href="{{ route('admin.pressings.index') }}" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition flex items-center gap-1">
            <span>&larr; Retour à la liste</span>
        </a>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-2xl text-xs text-zinc-900 dark:text-zinc-100 space-y-1">
                <div class="font-semibold">Veuillez corriger les erreurs ci-dessous :</div>
                <ul class="list-disc list-inside space-y-0.5 text-zinc-600 dark:text-zinc-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pressings.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="nom" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Nom de l'établissement <span class="text-zinc-900 dark:text-white font-bold">*</span>
                    </label>
                    <input
                        type="text"
                        name="nom"
                        id="nom"
                        value="{{ old('nom') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                        placeholder="Ex: AutoWash Express ou Pressing Central"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="type_activite" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Type d'activité <span class="text-zinc-900 dark:text-white font-bold">*</span>
                    </label>
                    <select
                        name="type_activite"
                        id="type_activite"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition"
                    >
                        <option value="pressing" {{ old('type_activite') === 'pressing' ? 'selected' : '' }}>Pressing & Blanchisserie</option>
                        <option value="lavage" {{ old('type_activite') === 'lavage' ? 'selected' : '' }}>Station de Lavage Auto / Moto</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="ville" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Ville <span class="text-zinc-900 dark:text-white font-bold">*</span>
                    </label>
                    <input
                        type="text"
                        name="ville"
                        id="ville"
                        value="{{ old('ville') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                        placeholder="Ex: Lomé"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="quartier" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Quartier <span class="text-zinc-900 dark:text-white font-bold">*</span>
                    </label>
                    <input
                        type="text"
                        name="quartier"
                        id="quartier"
                        value="{{ old('quartier') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                        placeholder="Ex: Haie Vive"
                    >
                </div>
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
                    class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs font-mono focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-black dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                    placeholder="Ex: +228 90 00 11 22"
                >
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-100 dark:border-zinc-800">
                <a href="{{ route('admin.pressings.index') }}" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 bg-black hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black text-xs font-bold rounded-xl shadow-md transition cursor-pointer">
                    Enregistrer l'établissement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
