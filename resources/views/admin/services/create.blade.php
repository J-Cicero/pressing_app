@extends('layouts.app', ['title' => 'Ajouter une Prestation'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Nouvelle Prestation</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Ajout d'un service au catalogue de l'agence</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition flex items-center gap-1">
            <span>&larr; Retour au catalogue</span>
        </a>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 p-6 sm:p-8 shadow-xs">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 rounded-2xl text-xs text-rose-800 dark:text-rose-200 space-y-1">
                <div class="font-semibold text-rose-900 dark:text-rose-100">Veuillez corriger les erreurs ci-dessous :</div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700 dark:text-rose-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-5">
            @csrf

            <div class="space-y-1.5">
                <label for="pressing_id" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    Agence de Pressing <span class="text-rose-500">*</span>
                </label>
                <select
                    name="pressing_id"
                    id="pressing_id"
                    required
                    class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition"
                >
                    <option value="">Sélectionner une agence...</option>
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ old('pressing_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->ville }} - {{ $p->quartier }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="designation" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    Désignation de la prestation <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="designation"
                    id="designation"
                    value="{{ old('designation') }}"
                    required
                    class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                    placeholder="Ex: Nettoyage Costume 3 Pièces"
                >
            </div>

            <div class="space-y-1.5">
                <label for="prix_unitaire" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    Prix Unitaire <span class="text-rose-500">*</span> <span class="text-zinc-400 dark:text-zinc-500 font-normal">(en FCFA)</span>
                </label>
                <div class="relative rounded-xl shadow-xs">
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="prix_unitaire"
                        id="prix_unitaire"
                        value="{{ old('prix_unitaire') }}"
                        required
                        class="w-full pl-3.5 pr-16 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs font-mono focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                        placeholder="Ex: 3500.00"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-xs font-semibold text-zinc-400 dark:text-zinc-500 font-mono">
                        FCFA
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-100 dark:border-zinc-800">
                <a href="{{ route('admin.services.index') }}" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-950 text-xs font-bold rounded-xl shadow-md transition cursor-pointer">
                    Enregistrer la prestation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
