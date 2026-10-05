@extends('layouts.app', ['title' => 'Nouveau Compte Personnel'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Créer un Compte Personnel</h1>
            <p class="text-xs text-slate-500 mt-0.5">Ajout d'un caissier ou administrateur</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition flex items-center gap-1">
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

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf

            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-semibold text-slate-700">
                    Nom complet <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-slate-400"
                    placeholder="Ex: Jean Kouassi"
                >
            </div>

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-slate-700">
                    Adresse Email <span class="text-rose-500">*</span>
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-slate-400"
                    placeholder="Ex: caissier@pressing.com"
                >
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700">
                    Mot de passe <span class="text-rose-500">*</span> <span class="text-slate-400 font-normal">(min. 8 caractères)</span>
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-slate-400"
                    placeholder="••••••••"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="role" class="block text-xs font-semibold text-slate-700">
                        Rôle <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="role"
                        id="role"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                    >
                        <option value="caissier" {{ old('role', 'caissier') === 'caissier' ? 'selected' : '' }}>Caissier</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="pressing_id" class="block text-xs font-semibold text-slate-700">
                        Agence de Pressing <span class="text-slate-400 font-normal">(Requis pour Caissier)</span>
                    </label>
                    <select
                        name="pressing_id"
                        id="pressing_id"
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition"
                    >
                        <option value="">Sélectionner une agence...</option>
                        @foreach($pressings as $p)
                            <option value="{{ $p->id }}" {{ old('pressing_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nom }} ({{ $p->ville }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    Créer le compte
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
