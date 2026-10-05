@extends('layouts.app', ['title' => 'Connexion'])

@section('content')
<div class="min-h-[calc(100vh-6rem)] flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8 bg-slate-50/80">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl shadow-slate-900/5 border border-slate-200/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[520px]">
        
        <!-- Left Banner (Sober, Neutral Branding for All Users) -->
        <div class="lg:col-span-5 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 p-8 flex flex-col justify-between text-white relative overflow-hidden">
            <!-- Background Subtle Glow & Pattern -->
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <!-- Brand Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[11px] font-medium text-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                    Espace de Gestion PressingApp
                </div>

                <!-- Brand Title & Sober Tagline -->
                <div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight text-white font-sans">
                        PRESSING<span class="text-indigo-400 font-light">APP</span>
                    </h1>
                    <p class="mt-2 text-xs text-slate-300 leading-relaxed">
                        Portail d'authentification pour le personnel et les administrateurs des agences de pressing.
                    </p>
                </div>

                <!-- Neutral Highlights -->
                <div class="space-y-3 pt-4 border-t border-white/10 text-xs text-slate-300">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Accès sécurisé par rôle & agence</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9"/>
                        </svg>
                        <span>Gestion centralisée des guichets</span>
                    </div>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="relative z-10 pt-6 mt-4 border-t border-white/10 text-[11px] text-slate-400 flex items-center justify-between">
                <span>PressingApp Suite</span>
                <span>v2.0</span>
            </div>
        </div>

        <!-- Right Side: Clean Login Form & Demo Selector -->
        <div class="lg:col-span-7 p-7 sm:p-8 flex flex-col justify-between bg-white">
            <div class="space-y-5">
                <!-- Header -->
                <div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Connexion à votre espace</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Saisissez vos identifiants pour ouvrir votre session.</p>
                </div>

                <!-- Error State Banner -->
                @if ($errors->any())
                    <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 space-y-1">
                        <div class="flex items-center gap-2 font-semibold text-rose-900">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Erreur d'identifiants</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1 text-[11px]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-4" onsubmit="handleLoginSubmit(event)">
                    @csrf

                    <!-- Email Field -->
                    <div class="space-y-1">
                        <label for="email" class="block text-xs font-semibold text-slate-700">
                            Adresse Email <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                autofocus
                                class="w-full pl-9 pr-3.5 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-slate-400"
                                placeholder="votre.email@pressing.com"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-1">
                        <label for="password" class="block text-xs font-semibold text-slate-700">
                            Mot de passe <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                class="w-full pl-9 pr-9 py-2 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition placeholder:text-slate-400"
                                placeholder="••••••••"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer"
                                title="Afficher/masquer le mot de passe"
                            >
                                <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input
                                type="checkbox"
                                name="remember"
                                class="w-3.5 h-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 focus:ring-offset-0 transition"
                            >
                            <span class="text-xs text-slate-600 group-hover:text-slate-900 transition">Se souvenir de moi</span>
                        </label>
                    </div>

                    <!-- Submit Button with Loading State -->
                    <div class="pt-1">
                        <button
                            id="submitBtn"
                            type="submit"
                            class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-semibold uppercase tracking-wider rounded-xl transition duration-150 shadow-sm shadow-indigo-600/20 flex items-center justify-center gap-2 group cursor-pointer"
                        >
                            <span id="btnText">Se connecter</span>
                            <svg id="btnIcon" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                            <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Demo Accounts Selector (Interactive Quick Fill preserved for testing) -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Comptes de Test Rapides</span>
                    <span class="text-[10px] text-indigo-600 font-medium">Cliquez pour remplir</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                    <button
                        type="button"
                        onclick="fillCredentials('admin@pressing.com', 'password')"
                        class="p-2 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-indigo-50/60 hover:border-indigo-200 transition text-left group cursor-pointer"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 group-hover:text-indigo-700 text-xs">Super Admin</span>
                            <span class="text-[9px] text-slate-400 font-mono">Admin</span>
                        </div>
                        <p class="text-[10px] text-slate-500 truncate mt-0.5">admin@pressing.com</p>
                    </button>

                    <button
                        type="button"
                        onclick="fillCredentials('caissier1@pressing.com', 'password')"
                        class="p-2 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-indigo-50/60 hover:border-indigo-200 transition text-left group cursor-pointer"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 group-hover:text-indigo-700 text-xs">Caissier 1</span>
                            <span class="text-[9px] text-slate-400 font-mono">Guichet</span>
                        </div>
                        <p class="text-[10px] text-slate-500 truncate mt-0.5">caissier1@pressing.com</p>
                    </button>

                    <button
                        type="button"
                        onclick="fillCredentials('caissier2@pressing.com', 'password')"
                        class="p-2 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-indigo-50/60 hover:border-indigo-200 transition text-left group cursor-pointer"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 group-hover:text-indigo-700 text-xs">Caissier 2</span>
                            <span class="text-[9px] text-slate-400 font-mono">Guichet</span>
                        </div>
                        <p class="text-[10px] text-slate-500 truncate mt-0.5">caissier2@pressing.com</p>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 013.98.937c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>`;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }
    }

    function fillCredentials(email, password) {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        emailInput.value = email;
        passwordInput.value = password;
        
        emailInput.classList.add('ring-2', 'ring-indigo-500');
        passwordInput.classList.add('ring-2', 'ring-indigo-500');
        setTimeout(() => {
            emailInput.classList.remove('ring-2', 'ring-indigo-500');
            passwordInput.classList.remove('ring-2', 'ring-indigo-500');
        }, 400);
    }

    function handleLoginSubmit(event) {
        const btn = document.getElementById('submitBtn');
        const text = document.getElementById('btnText');
        const icon = document.getElementById('btnIcon');
        const spinner = document.getElementById('btnSpinner');

        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        text.textContent = 'Connexion en cours...';
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');
    }
</script>
@endsection
