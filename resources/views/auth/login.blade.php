@extends('layouts.app', ['title' => 'Connexion'])

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md bg-[#FFF] border border-[#374151]/20 p-8 shadow-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-[#000]">
                PRESSING<span class="text-[#374151] font-light">APP</span>
            </h1>
            <p class="mt-2 text-xs uppercase tracking-widest text-[#374151]">
                Connexion à l'espace de gestion
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-[#F3F4F6] border-l-4 border-[#000] text-xs text-[#000]">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                    Adresse Email
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-[#000] text-sm focus:outline-none focus:border-[#000]"
                    placeholder="exemple@pressing.com"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#374151] mb-1">
                    Mot de passe
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-3 py-2 bg-[#FFF] border border-[#374151]/40 text-[#000] text-sm focus:outline-none focus:border-[#000]"
                    placeholder="••••••••"
                >
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-[#374151]">
                    <input
                        type="checkbox"
                        name="remember"
                        class="rounded-none border-[#374151] text-[#000] focus:ring-0"
                    >
                    <span class="ml-2">Se souvenir de moi</span>
                </label>
            </div>

            <div>
                <button
                    type="submit"
                    class="w-full py-2.5 px-4 bg-[#000] hover:bg-[#374151] text-[#FFF] text-xs font-bold uppercase tracking-widest transition"
                >
                    Se connecter
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-[#374151]/10 text-center text-xs text-[#374151]">
            <p class="font-medium">Comptes de démonstration (Lomé) :</p>
            <p class="mt-1">Admin: <span class="font-mono text-[#000]">admin@pressing.com</span> (mdp: password)</p>
            <p>Caissier 1 (Centre-Ville): <span class="font-mono text-[#000]">caissier1@pressing.com</span> (mdp: password)</p>
            <p>Caissier 2 (GTA): <span class="font-mono text-[#000]">caissier2@pressing.com</span> (mdp: password)</p>
        </div>
    </div>
</div>
@endsection
