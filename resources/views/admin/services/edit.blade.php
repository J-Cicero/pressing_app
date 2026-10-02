@extends('layouts.app', ['title' => 'Modifier une Prestation'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-[#000]">MODIFIER LA PRESTATION</h1>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-0.5">{{ $service->designation }}</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="text-xs font-semibold uppercase tracking-wider text-[#374151] hover:underline">
            &larr; Retour au catalogue
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

        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="pressing_id" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                    Agence de Pressing *
                </label>
                <select
                    name="pressing_id"
                    id="pressing_id"
                    required
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                >
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ old('pressing_id', $service->pressing_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->ville }} - {{ $p->quartier }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="designation" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                    Désignation de la prestation *
                </label>
                <input
                    type="text"
                    name="designation"
                    id="designation"
                    value="{{ old('designation', $service->designation) }}"
                    required
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                >
            </div>

            <div>
                <label for="prix_unitaire" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                    Prix Unitaire (en FCFA) *
                </label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="prix_unitaire"
                    id="prix_unitaire"
                    value="{{ old('prix_unitaire', $service->prix_unitaire) }}"
                    required
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                >
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-[#374151]/10">
                <a href="{{ route('admin.services.index') }}" class="px-4 py-2 border border-[#374151]/30 text-xs font-semibold uppercase tracking-wider text-[#374151] hover:bg-[#F3F4F6] transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2 bg-[#000] text-[#FFF] text-xs font-semibold uppercase tracking-wider hover:bg-[#374151] transition">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
