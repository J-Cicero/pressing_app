@extends('layouts.app', ['title' => 'Modifier un Compte'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Modifier le Compte</h1>
            <p class="text-xs text-zinc-600 dark:text-zinc-400 font-semibold mt-0.5">{{ $user->name }} &bull; {{ $user->email }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition flex items-center gap-1">
            <span>&larr; Retour à la liste</span>
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

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    Nom complet <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition"
                >
            </div>

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    Adresse Email <span class="text-rose-500">*</span>
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition"
                >
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    Nouveau mot de passe <span class="text-zinc-400 dark:text-zinc-500 font-normal">(laisser vide pour ne pas modifier)</span>
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
                    placeholder="••••••••"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="role" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Rôle <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="role"
                        id="role"
                        required
                        class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition"
                    >
                        <option value="caissier" {{ old('role', $user->role) === 'caissier' ? 'selected' : '' }}>Caissier</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="pressing_id" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                        Agence de Pressing <span class="text-zinc-400 dark:text-zinc-500 font-normal">(Requis pour Caissier)</span>
                    </label>
                    <select
                        name="pressing_id"
                        id="pressing_id"
                        class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-white text-xs focus:bg-white dark:focus:bg-zinc-900 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition"
                    >
                        <option value="">Sélectionner une agence...</option>
                        @foreach($pressings as $p)
                            <option value="{{ $p->id }}" {{ old('pressing_id', $user->pressing_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nom }} ({{ $p->ville }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-100 dark:border-zinc-800">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-950 text-xs font-bold rounded-xl shadow-md transition cursor-pointer">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
