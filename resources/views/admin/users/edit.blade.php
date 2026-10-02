@extends('layouts.app', ['title' => 'Modifier un Compte'])

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-[#000]">MODIFIER LE COMPTE</h1>
            <p class="text-xs uppercase tracking-wider text-[#374151] mt-0.5">{{ $user->name }} ({{ $user->email }})</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold uppercase tracking-wider text-[#374151] hover:underline">
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

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                    Nom complet *
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                >
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                    Adresse Email *
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                    Nouveau mot de passe (laisser vide pour ne pas modifier)
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                    placeholder="••••••••"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                        Rôle *
                    </label>
                    <select
                        name="role"
                        id="role"
                        required
                        class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
                    >
                        <option value="caissier" {{ old('role', $user->role) === 'caissier' ? 'selected' : '' }}>Caissier</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div>
                    <label for="pressing_id" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                        Agence de Pressing (Obligatoire pour Caissier)
                    </label>
                    <select
                        name="pressing_id"
                        id="pressing_id"
                        class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-xs text-[#000] focus:outline-none focus:border-[#000]"
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

            <div class="pt-4 flex justify-end space-x-3 border-t border-[#374151]/10">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-[#374151]/30 text-xs font-semibold uppercase tracking-wider text-[#374151] hover:bg-[#F3F4F6] transition">
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
