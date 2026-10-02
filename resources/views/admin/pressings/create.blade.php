@extends('layouts.app', ['title' => 'Ajouter une Agence'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-[#000]">NOUVELLE AGENCE DE PRESSING</h1>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-0.5">Enregistrer une succursale dans le réseau</p>
        </div>
        <a href="{{ route('admin.pressings.index') }}" class="text-xs font-semibold uppercase tracking-wider text-[#374151] hover:underline">
            &larr; Retour à la liste
        </a>
    </div>

    <div class="bg-[#FFF] border border-[#374151]/20 p-6 shadow-sm">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-[#F3F4F6] border-l-4 border-[#000] text-xs text-[#000]">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pressings.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="nom" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                    Nom de l'agence *
                </label>
                <input
                    type="text"
                    name="nom"
                    id="nom"
                    value="{{ old('nom') }}"
                    required
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                    placeholder="Ex: Pressing Central"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="ville" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                        Ville *
                    </label>
                    <input
                        type="text"
                        name="ville"
                        id="ville"
                        value="{{ old('ville') }}"
                        required
                        class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                        placeholder="Ex: Cotonou"
                    >
                </div>

                <div>
                    <label for="quartier" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                        Quartier *
                    </label>
                    <input
                        type="text"
                        name="quartier"
                        id="quartier"
                        value="{{ old('quartier') }}"
                        required
                        class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                        placeholder="Ex: Haie Vive"
                    >
                </div>
            </div>

            <div>
                <label for="telephone" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                    Téléphone
                </label>
                <input
                    type="text"
                    name="telephone"
                    id="telephone"
                    value="{{ old('telephone') }}"
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                    placeholder="Ex: +229 01 23 45 67"
                >
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-[#374151]/10">
                <a href="{{ route('admin.pressings.index') }}" class="px-4 py-2 border border-[#374151]/30 text-xs font-semibold uppercase tracking-wider text-[#374151] hover:bg-[#F3F4F6] transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition">
                    Enregistrer l'agence
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
