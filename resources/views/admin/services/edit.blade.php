@extends('layouts.app', ['title' => 'Modifier une Prestation'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Modifier la Prestation</h1>
            <p class="text-xs text-indigo-600 font-semibold mt-0.5">{{ $service->designation }}</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition flex items-center gap-1">
            <span>&larr; Retour au catalogue</span>
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

        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="pressing_id" class="block text-xs font-semibold text-slate-700">
                    Agence de Pressing <span class="text-rose-500">*</span>
                </label>
                <select
                    name="pressing_id"
                    id="pressing_id"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                >
                    @foreach($pressings as $p)
                        <option value="{{ $p->id }}" {{ old('pressing_id', $service->pressing_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->ville }} - {{ $p->quartier }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="designation" class="block text-xs font-semibold text-slate-700">
                    Désignation de la prestation <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="designation"
                    id="designation"
                    value="{{ old('designation', $service->designation) }}"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                >
            </div>

            <div class="space-y-1.5">
                <label for="prix_unitaire" class="block text-xs font-semibold text-slate-700">
                    Prix Unitaire <span class="text-rose-500">*</span> <span class="text-slate-400 font-normal">(en FCFA)</span>
                </label>
                <div class="relative rounded-xl shadow-xs">
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="prix_unitaire"
                        id="prix_unitaire"
                        value="{{ old('prix_unitaire', $service->prix_unitaire) }}"
                        required
                        class="w-full pl-3.5 pr-16 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs font-mono focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-xs font-semibold text-slate-400 font-mono">
                        FCFA
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.services.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
