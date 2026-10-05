@extends('layouts.app', ['title' => 'Modifier une Agence'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Modifier l'Agence</h1>
            <p class="text-xs text-indigo-600 font-semibold mt-0.5">{{ $pressing->nom }}</p>
        </div>
        <a href="{{ route('admin.pressings.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition flex items-center gap-1">
            <span>&larr; Retour à la liste</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
                <div class="font-semibold text-rose-900">Veuillez corriger les erreurs ci-dessous :</div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pressings.update', $pressing) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="nom" class="block text-xs font-semibold text-slate-700">
                    Nom de l'agence <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="nom"
                    id="nom"
                    value="{{ old('nom', $pressing->nom) }}"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="ville" class="block text-xs font-semibold text-slate-700">
                        Ville <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="ville"
                        id="ville"
                        value="{{ old('ville', $pressing->ville) }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="quartier" class="block text-xs font-semibold text-slate-700">
                        Quartier <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="quartier"
                        id="quartier"
                        value="{{ old('quartier', $pressing->quartier) }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                    >
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="telephone" class="block text-xs font-semibold text-slate-700">
                    Téléphone
                </label>
                <input
                    type="text"
                    name="telephone"
                    id="telephone"
                    value="{{ old('telephone', $pressing->telephone) }}"
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs font-mono focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                >
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.pressings.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    Mettre à jour l'agence
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
