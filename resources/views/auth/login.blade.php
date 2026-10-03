@extends('layouts.app', ['title' => 'Connexion'])

@section('content')
<div class="min-h-[calc(100vh-6rem)] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md bg-[#FFF] border border-[#E5E7EB] p-8 shadow-md rounded-none">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-[#000]">
                PRESSING<span class="text-[#374151] font-light">APP</span>
            </h1>
            <p class="mt-2 text-xs uppercase tracking-widest text-[#374151] font-semibold">
                Authentification & Espace de Gestion
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-[#F3F4F6] border-l-4 border-[#000] text-xs text-[#000] shadow-sm">
                <ul class="list-disc list-inside space-y-1 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#374151] mb-1.5">
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
                    class="w-full px-3.5 py-2.5 bg-[#FFF] border border-[#E5E7EB] text-[#000] text-sm focus:outline-none focus:border-[#000] focus:ring-1 focus:ring-[#000] transition"
                    placeholder="admin@pressing.com"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#374151] mb-1.5">
                    Mot de passe
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-3.5 py-2.5 bg-[#FFF] border border-[#E5E7EB] text-[#000] text-sm focus:outline-none focus:border-[#000] focus:ring-1 focus:ring-[#000] transition"
                    placeholder="••••••••"
                >
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-[#374151] cursor-pointer">
                    <input
                        type="checkbox"
                        name="remember"
                        class="rounded-none border-[#E5E7EB] text-[#000] focus:ring-0"
                    >
                    <span class="ml-2 font-medium">Se souvenir de moi</span>
                </label>
            </div>

            <div>
                <button
                    type="submit"
                    class="w-full py-3 px-4 bg-[#000] hover:bg-[#1F2937] text-[#FFF] text-xs font-bold uppercase tracking-widest transition shadow-sm"
                >
                    Se connecter &rarr;
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-[#E5E7EB] text-center text-xs text-[#374151]">
            <p class="font-bold text-[#000] uppercase text-[11px] tracking-wider mb-2">Comptes de démonstration (Lomé) :</p>
            <div class="bg-[#F3F4F6] p-3 border border-[#E5E7EB] text-left space-y-1.5 font-mono text-[11px]">
                <div>👑 <span class="font-bold text-[#000]">Admin:</span> admin@pressing.com <span class="text-[#374151]">(mdp: password)</span></div>
                <div>🧾 <span class="font-bold text-[#000]">Caissier 1:</span> caissier1@pressing.com <span class="text-[#374151]">(mdp: password)</span></div>
                <div>🧾 <span class="font-bold text-[#000]">Caissier 2:</span> caissier2@pressing.com <span class="text-[#374151]">(mdp: password)</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
