@extends('layouts.app', ['title' => 'Connexion'])

@section('content')
<div class="min-h-[calc(100vh-6rem)] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8 bg-white dark:bg-zinc-950">
    <div class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl shadow-xl shadow-slate-900/5 dark:shadow-black/80 border border-slate-200/80 dark:border-zinc-800 p-8 sm:p-10 space-y-6">
        
        <!-- Top Header & Brand -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-zinc-800">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-900 dark:bg-white inline-block"></span>
                    <span class="text-base font-extrabold tracking-tight text-slate-900 dark:text-white">
                        PRESSING<span class="text-slate-400 dark:text-zinc-500 font-light">APP</span>
                    </span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">Portail de Gestion Multi-Agences</p>
            </div>

            <!-- Theme Toggle Button -->
            <button type="button" onclick="toggleTheme()" class="p-2 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-950 text-slate-600 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 transition cursor-pointer" title="Changer de thème">
                <svg class="w-4 h-4 text-amber-500 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg class="w-4 h-4 text-slate-700 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>
        </div>

        <!-- Title -->
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Connexion à votre compte</h1>
            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">Saisissez votre e-mail et votre mot de passe pour continuer.</p>
        </div>

        <!-- Error Banner -->
        @if ($errors->any())
            <div class="p-3.5 bg-rose-50 dark:bg-zinc-950 border border-rose-200 dark:border-zinc-800 rounded-xl text-xs text-rose-800 dark:text-zinc-200 space-y-1">
                <div class="flex items-center gap-2 font-bold text-rose-900 dark:text-white">
                    <svg class="w-4 h-4 text-rose-600 dark:text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Erreur d'identifiants</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700 dark:text-zinc-400 pl-1 text-[11px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Login Form -->
        <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-4" onsubmit="handleLoginSubmit(event)">
            @csrf

            <!-- Email Field -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-zinc-300">
                    Adresse Email <span class="text-slate-900 dark:text-white">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-zinc-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </div>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', 'admin@pressing.com') }}"
                        required
                        autocomplete="email"
                        autofocus
                        class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 dark:bg-zinc-950 border border-slate-200 dark:border-zinc-800 rounded-xl text-slate-900 dark:text-white text-xs focus:bg-white focus:outline-none focus:border-slate-900 dark:focus:border-white focus:ring-1 focus:ring-slate-900 dark:focus:ring-white transition placeholder:text-slate-400 dark:placeholder:text-zinc-600"
                        placeholder="admin@pressing.com"
                    >
                </div>
            </div>

            <!-- Password Field -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-zinc-300">
                    Mot de passe <span class="text-slate-900 dark:text-white">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-zinc-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        value="password"
                        required
                        autocomplete="current-password"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-zinc-950 border border-slate-200 dark:border-zinc-800 rounded-xl text-slate-900 dark:text-white text-xs focus:bg-white focus:outline-none focus:border-slate-900 dark:focus:border-white focus:ring-1 focus:ring-slate-900 dark:focus:ring-white transition placeholder:text-slate-400 dark:placeholder:text-zinc-600"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        onclick="togglePasswordVisibility()"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 dark:text-zinc-500 hover:text-slate-600 dark:hover:text-zinc-300 transition cursor-pointer"
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
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input
                        type="checkbox"
                        name="remember"
                        checked
                        class="w-3.5 h-3.5 rounded border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-slate-900 dark:text-white focus:ring-slate-900 dark:focus:ring-white focus:ring-offset-0 transition"
                    >
                    <span class="text-xs text-slate-600 dark:text-zinc-400 group-hover:text-slate-900 dark:group-hover:text-white transition">Se souvenir de moi</span>
                </label>
            </div>

            <!-- Primary Submit Button -->
            <div class="pt-2">
                <button
                    id="submitBtn"
                    type="submit"
                    class="w-full py-3 px-4 bg-slate-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-zinc-950 text-xs font-extrabold uppercase tracking-wider rounded-xl transition duration-150 shadow-md flex items-center justify-center gap-2 group cursor-pointer"
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

        <!-- Quick Fill Account Card -->
        <div class="pt-4 border-t border-slate-100 dark:border-zinc-800">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">Compte Initial Administrateur</span>
                <span class="text-[10px] text-slate-500 dark:text-zinc-400 font-semibold">Clic rapide</span>
            </div>
            <button
                type="button"
                onclick="fillCredentials('admin@pressing.com', 'password')"
                class="w-full p-3 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-950 hover:bg-slate-100 dark:hover:bg-zinc-800 transition text-left group cursor-pointer flex items-center justify-between"
            >
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 dark:text-white text-xs">Super Administrateur</span>
                        <span class="text-[9px] uppercase font-mono px-2 py-0.5 rounded bg-slate-200 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 font-semibold">Super Admin</span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5 font-mono">admin@pressing.com &bull; password</p>
                </div>
                <svg class="w-4 h-4 text-slate-400 dark:text-zinc-400 group-hover:translate-x-1 group-hover:text-slate-900 dark:group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
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
        
        emailInput.classList.add('ring-1', 'ring-slate-900');
        passwordInput.classList.add('ring-1', 'ring-slate-900');
        setTimeout(() => {
            emailInput.classList.remove('ring-1', 'ring-slate-900');
            passwordInput.classList.remove('ring-1', 'ring-slate-900');
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
